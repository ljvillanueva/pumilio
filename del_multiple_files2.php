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
$force_admin = TRUE;
require("include/check_admin.php");

$SoundIDs=$_POST['SoundIDs'];
$where_to=filter_var($_POST["where_to"], FILTER_SANITIZE_URL);

$howmany = count($SoundIDs);
if ($howmany>0){
	for ($i=0;$i<$howmany;$i++) {
		$SoundID = $SoundIDs[$i];
	
		#$filename=query_one("SELECT OriginalFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		#$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		#$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		#$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		
		$filename = DB::column('SELECT `OriginalFilename` FROM `Sounds` WHERE `SoundID` = ' . $SoundID);
		$ColID = DB::column('SELECT `ColID` FROM `Sounds` WHERE `SoundID` = ' . $SoundID);
		$DirID = DB::column('SELECT `DirID` FROM `Sounds` WHERE `SoundID` = ' . $SoundID);
		$AudioPreviewFilename = DB::column('SELECT `AudioPreviewFilename` FROM `Sounds` WHERE `SoundID` = ' . $SoundID);

		$source_dir="sounds/sounds/$ColID/$DirID";	
		
		if (unlink($source_dir . "/" . $filename)) {
			$query_file = "UPDATE Sounds SET SoundStatus='9' WHERE SoundID='$SoundID' LIMIT 1";
			$result_file = mysqli_query($connection, $query_file)
				or die (mysqli_error($connection));
			save_log($connection, $SoundID, "90", "The file sounds/sounds/$ColID/$DirID/$filename was deleted.");

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
			}
		}
	}

header("Location: $where_to&md=$howmany");
die();
	
?>
