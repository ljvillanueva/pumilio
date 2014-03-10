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
$force_loggedin = TRUE;
require("include/check_login.php");

#Sanitize
$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$SampleID=filter_var($_GET["SampleID"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name</title>";

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
$query_q = "SELECT * from SampleMembers WHERE SampleID='$SampleID' AND SoundID='$SoundID'";
$result_q = mysqli_query($connection, $query_q)
	or die (mysqli_error($connection));
$nrows_q = mysqli_num_rows($result_q);

if ($nrows_q==0){
	$query = ("INSERT INTO SampleMembers (SampleID,SoundID) VALUES ('$SampleID', '$SoundID')");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	echo "<div class=\"success\">Sound added to the sample set.</div>";
	}
else {
	echo "<div class=\"notice\">The sound is already in the sample set.</div>";
	}
?>

<br><p><a href="#" onClick="window.close();">Close window.</a>

</div>

</body>
</html>
