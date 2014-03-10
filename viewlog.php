<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require("config.php");
	} 
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");

$force_loggedin = TRUE;
require("include/check_login.php");

$QueueID=filter_var($_GET["QueueID"], FILTER_SANITIZE_NUMBER_INT);
$c=filter_var($_GET["c"], FILTER_SANITIZE_NUMBER_INT);
$NewStatus=filter_var($_GET["Status"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Manage Jobs</title>\n";

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
<body onblur="window.focus();">

<div style="padding: 10px;">

<?php

if ($c==1) {
	$result_newstatus = mysqli_query($connection, "UPDATE Queue SET Status='$NewStatus' WHERE QueueID='$QueueID' LIMIT 1")
		or die (mysqli_error($connection));
	}
	

$result=mysqli_query($connection, "SELECT * FROM Queue, QueueJobs WHERE Queue.QueueID='$QueueID' AND Queue.JobID=QueueJobs.JobID LIMIT 1")
	or die (mysqli_error($connection));;
$nrows = mysqli_num_rows($result);
$row = mysqli_fetch_array($result);
extract($row);

echo "<p style=\"float: right;\">[<a href=\"viewlog.php?QueueID=$QueueID\">Refresh information</a>]</p>
	<p>SoundID - Priority - Status
	<hr noshade>";

echo "<p><a href=\"#\" onClick=\"window.opener.location.href='db_filedetails.php?SoundID=$SoundID';\" title=\"Open file in main window\">$SoundID</a> - $Priority - ";
	if ($Status==0){
		echo "<strong style=\"color: black;\">Waiting";
		}
	elseif ($Status==1){
		echo "<strong style=\"color: gray;\">Running";
		}
	elseif ($Status==2){
		echo "<strong style=\"color: green;\">Completed";
		}
	elseif ($Status==3){
		echo "<strong style=\"color: red;\">There was an error";
		}
	elseif ($Status==4){
		echo "<strong style=\"color: red;\">On hold";
		}
						
	echo "</strong>
	<p>Log:";

$result=mysqli_query($connection, "SELECT FileLog, Computer FROM ProcessLog WHERE QueueID='$QueueID' LIMIT 1")
	or die (mysqli_error($connection));;
$nrows = mysqli_num_rows($result);
if ($nrows>0) {
	$row = mysqli_fetch_array($result);
	extract($row);

	echo "<p>Computer: $Computer</p>
	<pre>$FileLog</pre>";
	}
else {
	echo "<p>This file does not have anything in the log.";
	}
		
echo "<hr noshade>";

echo "<p><strong>Change the status for this item</strong>
	<form method=\"GET\" action=\"viewlog.php\">
	<input type=\"hidden\" name=\"c\" value=\"1\">
	<input type=\"hidden\" name=\"QueueID\" value=\"$QueueID\">
	New status: <select name=\"Status\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
		<option value=\"0\">Waiting</option>
		<option value=\"4\">On Hold</option>
	</select> 
	<p><input type=submit value=\" Change Status \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";

echo "<br><p><a href=\"managejobs.php?JobID=$JobID\">Go back</a>";
	
?>

<br><br><p><a href="#" onClick="window.close();">Close window</a>

</div>
</body>
</html>
