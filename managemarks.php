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

if (!isset($_GET["Token"])){
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
	<html>
	<head>
	<title>$app_custom_name - Manage Marks</title>";

	#Get CSS
	require("include/get_css.php");
	require("include/get_jqueryui.php");

	if ($use_googleanalytics){
		echo $googleanalytics_code;
		}


	#Execute custom code for head, if set
	if (is_file("$absolute_dir/customhead.php")) {
			include("customhead.php");
		}
		
	echo "
	</head>
	<body>

	<div style = \"padding: 10px;\">";

	echo "<div class=\"error\">The ID is not valid.</div>
	<hr noshade>
	<p><a href=\"#\" onClick=\"opener.location.reload();window.close();\">Close window</a>

	</div>
	</body>
	</html>";
	die();
	}


$Token = filter_var($_GET["Token"], FILTER_SANITIZE_STRING);

$username = $_COOKIE["username"];
$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);

$valid_token = query_one("SELECT COUNT(*) FROM Tokens WHERE TokenID='$Token' AND UserID='$UserID'", $connection);

if ($valid_token==1) {
	$soundfile_format = query_one("SELECT soundfile_format FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_duration = query_one("SELECT soundfile_duration FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_name = query_one("SELECT soundfile_name FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_wav = query_one("SELECT soundfile_wav FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_id = query_one("SELECT soundfile_id FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$no_channels = query_one("SELECT no_channels FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_samplingrate = query_one("SELECT soundfile_samplingrate FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_samplingrateoriginal = query_one("SELECT soundfile_samplingrateoriginal FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$random_cookie = query_one("SELECT random_cookie FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$from_db = query_one("SELECT from_db FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	}

	#Check if fft size is set
	if (isset($_COOKIE["fft"])){
		$fft_size = $_COOKIE["fft"];
		}
	else{
		$fft_size = 2048;
		}

#Check if user can edit files
$force_loggedin = TRUE;
require("include/check_login.php");

$SoundID = $soundfile_id;

if (isset($_GET["d"])){
	$d = filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);
	$mark_todel = filter_var($_GET["marks_id"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$d = FALSE;
	}

if ($d) {
	$result = mysqli_query($connection, "DELETE FROM SoundsMarks WHERE marks_ID='$mark_todel' LIMIT 1")
		or die (mysqli_error($connection));
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Manage Marks</title>";

#Get CSS
require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	

?>

</head>
<body>

<div style="padding: 10px;">

<?php

$result = mysqli_query($connection, "SELECT SoundName FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
	or die (mysqli_error($connection));
$row = mysqli_fetch_array($result);
extract($row);

echo "<h4>Marks in the database for the file $SoundName (ID: $SoundID):</h4>";
			
			
$resultm = mysqli_query($connection, "SELECT marks_ID FROM SoundsMarks WHERE SoundID='$SoundID' ORDER BY marks_ID")
	or die (mysqli_error($connection));;
$nrowsm = mysqli_num_rows($resultm);
if ($nrowsm>0){

	echo "<table>";

	$nyquist = $soundfile_samplingrateoriginal/2;

	exec('include/svt.py -s tmp/' . $random_cookie . '/marks-spectrogram.png -w 400 -h 180 -m ' . $nyquist . ' -f ' . $fft_size . ' -p ' . $spectrogram_palette . ' tmp/' . $random_cookie . '/' . $soundfile_wav, $lastline, $retval);

	for ($w = 0; $w < $nrowsm; $w++){
		$rowm = mysqli_fetch_array($resultm);
		extract($rowm);

		//Query for the last mark edit
		$res = mysqli_query($connection, "SELECT marks_ID, SoundID AS mark_fileID, time_min AS mark_time_min, time_max AS mark_time_max, freq_min AS mark_freq_min, freq_max AS mark_freq_max, mark_tag FROM SoundsMarks WHERE marks_ID='$marks_ID' LIMIT 1");
		$row = mysqli_fetch_array($res);
		extract($row);
		unset($row);

		$viewport_box_low = round(180 - ((($mark_freq_min-10) / $nyquist) * 180));
		$viewport_box_high = round((($nyquist-$mark_freq_max) / $nyquist) * 180);
		$viewport_box_left = round(($mark_time_min/$soundfile_duration) * 400);
		$viewport_box_right = round(($mark_time_max/$soundfile_duration) * 400);

		$viewport_box_width = round($viewport_box_right-$viewport_box_left);
		$viewport_box_height = round($viewport_box_low-$viewport_box_high);

		//Mark
		echo "<tr><td><a href=\"editmarks.php?Token=$Token&markID=$marks_ID\"><img src=\"images/database_edit.png\" title=\" Edit \"></a> $mark_tag_name: $mark_tag (ID:$marks_ID) | Time: $mark_time_min - $mark_time_max sec | Frequency: $mark_freq_min - $mark_freq_max Hz <a href=\"managemarks.php?Token=$Token&marks_id=$marks_ID&d=1\"><img src=\"images/database_delete.png\" title=\" Delete \"></a><br><br>\n";


		exec("convert -stroke red -fill none -draw \"rectangle " . $viewport_box_left . "," . $viewport_box_high. " " . $viewport_box_right . "," . $viewport_box_low . "\" tmp/" . $random_cookie . "/marks-spectrogram.png tmp/" . $random_cookie . "/marks-spectrogram" . $w . ".png", $lastline, $retval);
		if ($retval != 0) {
			echo "<div class=\"error\">There was a problem with ImageMagick...</div>";
			die();
			}
		echo "<img src=\"tmp/" . $random_cookie . "/marks-spectrogram" . $w . ".png\" style=\"margin-left: 20px;\"><br><br></td></tr>";

		}
	echo "</table>";
	}
else{
	echo "<p>This file has no marks.";
	}
		
?>

<hr noshade>
<p><a href="#" onClick="opener.location.reload();window.close();">Close window</a>

</div>
</body>
</html>