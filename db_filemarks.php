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

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$username = $_COOKIE["username"];
$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);

$force_loggedin = TRUE;
require("include/check_login.php");

$d = filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);
$mark_todel = filter_var($_GET["markID"], FILTER_SANITIZE_NUMBER_INT);

if ($d == 1) {
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

if ($use_googleanalytics) {
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

$result=mysqli_query($connection, "SELECT SoundName, SamplingRate FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
	or die (mysqli_error($connection));;
$row = mysqli_fetch_array($result);
extract($row);

echo "<h4>Marks in the database for the file $SoundName (ID: $SoundID):</h4>";

if ($d){
	echo "<div class=\"success\">The mark was deleted</div>";
	}
			
$resultm=mysqli_query($connection, "SELECT marks_ID FROM SoundsMarks WHERE SoundID='$SoundID' ORDER BY marks_ID")
	or die (mysqli_error($connection));
$nrowsm = mysqli_num_rows($resultm);
if ($nrowsm>0) {
	echo "<table>";

	for ($w=0;$w<$nrowsm;$w++){
		$rowm = mysqli_fetch_array($resultm);
		extract($rowm);

		//Query for the last mark edit
		$res=mysqli_query($connection, "SELECT marks_ID, SoundID AS mark_fileID, time_min AS mark_time_min, time_max AS mark_time_max, freq_min AS mark_freq_min, freq_max AS mark_freq_max, mark_tag FROM SoundsMarks WHERE marks_ID='$marks_ID' LIMIT 1");
		$row = mysqli_fetch_array($res);
		extract($row);
		unset($row);

		//Mark
		echo "<tr><td>$mark_tag_name: $mark_tag (ID:$marks_ID) | Time: $mark_time_min - $mark_time_max sec | Frequency: $mark_freq_min - $mark_freq_max Hz <a href=\"db_filemarks.php?d=1&SoundID=$SoundID&markID=$marks_ID\"><img src=\"images/database_delete.png\" title=\" Delete \"></a></td></tr>\n";

		}
	echo "</table>";
	}
else {
	echo "<p>This file has no marks.";
	}
		
?>

<hr noshade>
<p><a href="#" onClick="opener.location.reload();window.close();">Close window</a>

</div>
</body>
</html>
