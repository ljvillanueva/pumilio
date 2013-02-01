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

	if (!sessionAuthenticate($connection))
		{die();}
		
	if (!is_user_admin($username, $connection))
		{die();}

$SoundIDs=$_POST['SoundIDs'];
$where_to=filter_var($_POST["where_to"], FILTER_SANITIZE_URL);

echo "<html>
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
			$howmany = count($SoundIDs);
			if ($howmany>0){
				echo "<div class=\"notice\">
					<p>Are you sure you want to delete these files?
					<form action=\"del_multiple_files2.php\" method=\"POST\">
					<ul>";
				for ($i=0;$i<$howmany;$i++) {
					$OriginalFilename=query_one("SELECT OriginalFilename FROM Sounds WHERE SoundID='$SoundIDs[$i]' LIMIT 1", $connection);
					$small_spectrogram = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundIDs[$i]' and ImageType='spectrogram-small'", $connection);
					$ColID = query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundIDs[$i]' LIMIT 1", $connection);
					$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundIDs[$i]' LIMIT 1", $connection);

					echo "<li><img src=\"$app_url/sounds/images/$ColID/$DirID/$small_spectrogram\" width=\"300\" height=\"150\"><br>
					<input type=\"hidden\" name=SoundIDs[] value=\"$SoundIDs[$i]\">$SoundIDs[$i] $OriginalFilename";
				
					}
				echo "</ul>
					<input name=\"where_to\" type=\"hidden\" value=\"$where_to\">
					<input type=submit value=\" Yes, permanently delete these files \" class=\"fg-button ui-state-default ui-corner-all\"></form>
					</div>";
				}
			else {
				echo "<div class=\"error\">You did not select any files to delete. Please go back and try again.</div><br>";
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
