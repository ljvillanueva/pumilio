<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");
	
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>\n";

require("get_css_include.php");
require("get_jqueryui_include.php");
?>

</head>
<body>
<div style="padding: 10px;">

<?php
$SampleID=filter_var($_POST["SampleID"], FILTER_SANITIZE_NUMBER_INT);
$ScriptID=filter_var($_POST["ScriptID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$jobname=filter_var($_POST["jobname"], FILTER_SANITIZE_STRING);
$type=filter_var($_POST["type"], FILTER_SANITIZE_STRING);

$userid=query_one("SELECT UserID FROM Users WHERE UserName='$username' LIMIT 1", $connection);

#Make a new job
$query_newjob = "INSERT INTO QueueJobs (JobName, UserID) VALUES ('$jobname', '$userid')";
$result_newjob = mysqli_query($connection, $query_newjob)
	or die (mysqli_error($connection));
$JobID=mysqli_insert_id($connection);

if ($type=="site"){
	#Get all the SoundID from the Sample Set
	$query = "SELECT SoundID from Sounds WHERE SiteID='$SiteID' AND Sounds.SoundStatus!='9'";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	for ($i=0;$i<$nrows;$i++){
		$row = mysqli_fetch_array($result);
		extract($row);
		#Add each one to the Queue
		$query_newitem = "INSERT INTO Queue (JobID, SoundID, ScriptID) VALUES ('$JobID', '$SoundID', '$ScriptID')";
		$result_newitem = mysqli_query($connection, $query_newitem)
			or die (mysqli_error($connection));
		}
	}
else {
	#Get all the SoundID from the Sample Set
	$query = "SELECT SoundID from SampleMembers WHERE SampleID='$SampleID'";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		#Add each one to the Queue
		$query_newitem = "INSERT INTO Queue (JobID, SoundID, ScriptID) VALUES ('$JobID', '$SoundID', '$ScriptID')";
		$result_newitem = mysqli_query($connection, $query_newitem)
			or die (mysqli_error($connection));
		}
	}

echo "<div class=\"success\">The job was created.</div>";

?>
<br><br><p><a href="#" onClick="opener.location.reload();window.close();">Close window.</a>
</div>
</body>
</html>
