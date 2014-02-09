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

	echo "<a href=\"$db_filedetails_link&SoundID=$SoundID\">
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
