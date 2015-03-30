<?php

use \DByte\DB;
DB::$c = $pdo;

ob_flush(); flush();

$row_break_counter = 0;

for ($i=0;$i<$nrows;$i++) {
	$row = mysqli_fetch_array($result);
	extract($row);
	
	if ($row_break_counter == 0) {
		echo "<div class=\"row\">";
	}

	$row_break_counter = $row_break_counter + 1;

	echo "<div class=\"col-lg-4\">";

	$ColID = DB::column('SELECT ColID FROM `Sounds` WHERE SoundID = ' . $SoundID);
	$SiteID = DB::column('SELECT SiteID FROM `Sounds` WHERE SoundID = ' . $SoundID);
	$DirID = DB::column('SELECT DirID FROM `Sounds` WHERE SoundID = ' . $SoundID);

	$small_spectrogram = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);
	$small_spectrogram_path = "sounds/images/$ColID/$DirID/$small_spectrogram";
	
	if (!is_file("$absolute_dir/$small_spectrogram_path"))	{
		$small_spectrogram_path = "images/notready-small.png";
		}

	echo "<a href=\"db_filedetails.php?SoundID=$SoundID\" title=\"Click for file details and more options\">
		<img src=\"$small_spectrogram_path\" width=\"300\" height=\"150\" style=\"margin-top: 20px;\"><br>
		$SoundName</a>";

	if (isset($Date_h) && $Date_h!="") {
		echo "<br>$Date_h | $Time";
		}
	
if (!isset($show_tags)){
	$show_tags = 0;
	}

	
	flush(); @ob_flush();
		
	
	#Check if there are images
		$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
		$sound_images=query_one($query_img, $connection);
		$check_auxfiles = FALSE;
		if ($sound_images!=6) {
			$check_auxfiles = TRUE;
			}
			
		#check if spectrogram exists
		$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
			$check_auxfiles = TRUE;
			}

		$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
			$check_auxfiles = TRUE;
			}

		$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
			$check_auxfiles = TRUE;
			}

		$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
			$check_auxfiles = TRUE;
			}

		$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
			$check_auxfiles = TRUE;
			}

		$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
			$check_auxfiles = TRUE;
			}

		

	#MP3
	$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	if (($AudioPreviewFilename=="") || (is_null($AudioPreviewFilename))) {
		$check_auxfiles = TRUE;
		}
	if (!is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
		$check_auxfiles = TRUE;
		}
	
	if ($check_auxfiles) {
		#check files in background
		if ($special_noprocess == FALSE){
			check_in_background($absolute_dir, $connection);
			}
		}


	if ($row_break_counter == 3) {
		echo "</div>";
		$row_break_counter = 0;
		}
	echo "</div>";

}	

if ($row_break_counter != 0){
	$remdivs = ($nrows % 3 ) - 1;
	print str_repeat("<div class=\"col-lg-4\"><br>&nbsp;</div>", $remdivs);
	echo "</div>";
	}

echo "<br><br>";

?>