<?php

#Check if tablet or iOS
require_once("include/Mobile_Detect.php");
$detect = new Mobile_Detect();

if ($detect->isMobile() || $detect->isTablet()) {
	$mobile = TRUE;
	}
else {
	$mobile = FALSE;
	}

#Special wrapper
if ($special_wrapper==TRUE){
	$db_filedetails_link = "$wrapper?page=db_filedetails";
	}
else {
	$db_filedetails_link = "db_filedetails.php?";
	}

if ($pumilio_admin==TRUE && $mobile==FALSE){
	$mult_delete=TRUE;
	}
else {
	$mult_delete=FALSE;
	}

if ($mult_delete==TRUE){
	echo "<form action=\"del_multiple_files.php\" method=\"POST\" id=\"delmult\">";
	}


ob_flush(); flush();



$row_break_counter=0;
for ($i=0;$i<$nrows;$i++) {
	$row = mysqli_fetch_array($result);
	extract($row);

	$row_break_counter=$row_break_counter+1;

	if ($row_break_counter==3) {
		echo "<div class=\"span-8 last\">&nbsp;<br>";
		$row_break_counter=0;
		}
	else {
		echo "<div class=\"span-8\">&nbsp;<br>";
		}

	$ColID = DB::column('SELECT ColID FROM `Sounds` WHERE SoundID = ' . $SoundID);
	$SiteID = DB::column('SELECT SiteID FROM `Sounds` WHERE SoundID = ' . $SoundID);
	$DirID = DB::column('SELECT DirID FROM `Sounds` WHERE SoundID = ' . $SoundID);

	$small_spectrogram = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);
	$small_spectrogram_path = "sounds/images/$ColID/$DirID/$small_spectrogram";
	
	if (!is_file("$absolute_dir/$small_spectrogram_path"))	{
		$small_spectrogram_path = "images/notready-small.png";
		}

	echo "<a href=\"$db_filedetails_link&SoundID=$SoundID\" title=\"Click for file details and more options\">
		<img src=\"$small_spectrogram_path\" width=\"300\" height=\"150\"><br>
		$SoundName</a>";

	if ($mult_delete==TRUE){
		echo "<input type=\"checkbox\" name=SoundIDs[] value=\"$SoundID\" class=\"case\">";
		}
		
	if (isset($Date_h) && $Date_h!="") {
		echo "<br>$Date_h | $Time";
		}
	
if (!isset($show_tags)){
	$show_tags = 0;
	}

#if ($show_tags=="1") {
#	#Tags
#	if ($pumilio_loggedin == TRUE) {
#		echo "<div id=\"tagspace$i\">
#		<form method=\"get\" action=\"include/addtag_ajax.php\" id=\"addtags$i\">";
#			$where_to = $_SERVER['PHP_SELF'];
#			$where_toq = $_SERVER['QUERY_STRING'];

#			require("include/managetags.php");

#			echo "<p>Add tags:
#			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
#			<input type=\"hidden\" name=\"this_i\" value=\"$i\">
#			<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\">
#			<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\">
#		</form><br>
#		</div>\n\n";
#		}
#	else {
#		require("include/gettags.php");
#		}
#	}

	echo "</div>";				
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

}	

if ($mult_delete==TRUE){
	$self=$_SERVER['PHP_SELF'];
	$q=$_SERVER['QUERY_STRING'];
	echo "<div class=\"span-24 last\"><hr noshade></div>
		<div class=\"span-8\">&nbsp;</div>
		<div class=\"span-8\">
			<input type=\"checkbox\" id=\"selectall\"/>Select all 
			<input name=\"where_to\" type=\"hidden\" value=\"$self?$q\">
			<input type=submit value=\" Delete selected files \" class=\"fg-button ui-state-default ui-corner-all\">
		</form></div>
	<div class=\"span-8 last\">&nbsp;</div>";
	}

?>
