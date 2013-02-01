<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");

#Check if user can edit files (i.e. has admin privileges)
	$username = $_COOKIE["username"];
	if (!is_user_admin($username, $connection)) {
		die();
		}

$SoundID=filter_var($_POST["SoundID"], FILTER_SANITIZE_NUMBER_INT);

echo "	<html>
	<head>
<title>$app_custom_name - Delete file</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">

			<?php
			$filename=query_one("SELECT OriginalFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			$source_dir="sounds/sounds/$ColID/$DirID";
			if (unlink($source_dir . "/" . $filename)) {
				$query_file = "UPDATE Sounds SET SoundStatus='9' WHERE SoundID='$SoundID' LIMIT 1";
				$result_file = mysqli_query($connection, $query_file)
					or die (mysqli_error($connection));
					
				save_log($connection, $SoundID, "90", "The file sounds/sounds/$ColID/$DirID/$OriginalFilename was deleted.");

				#Check if there are images
				$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
				$sound_images=query_one($query_img, $connection);
				if ($sound_images!=0) {
					$ImageFile=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram'", $connection);
					if (is_file("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile)){
						unlink("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile);}

					$ImageFile=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='waveform'", $connection);
					if (is_file("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile)){
						unlink("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile);}

					$ImageFile=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-small'", $connection);
					if (is_file("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile)){
						unlink("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile);}

					$ImageFile=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='waveform-small'", $connection);
					if (is_file("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile)){
						unlink("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile);}

					$ImageFile=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-large'", $connection);
					if (is_file("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile)){
						unlink("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile);}

					$ImageFile=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='waveform-large'", $connection);
					if (is_file("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile)){
						unlink("sounds/images/$ColID" . "/" . $DirID . "/" . $ImageFile);}


					$query_file = "DELETE FROM SoundsImages WHERE SoundID='$SoundID'";
					$result_file = mysqli_query($connection, $query_file)
						or die (mysqli_error($connection));

					}
				#Check if there are mp3
				if (is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
					unlink("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename");
					}
			
				echo "<p><div class=\"success\">The file was deleted.</div>
				<p><a href=\"db_browse.php?ColID=$ColID\">Browse the archive</a>";
			
				}
			else {
				echo "<div class=\"error\">There was an error deleting the file, please check the server logs.</div><br>";
				}

			?>

		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>

	</div>


</body>
</html>
