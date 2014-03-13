<?php
require("functions.php");
require("../config.php");
require("apply_config_include.php");
require("check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>Pumilio</title>";

?>

<link rel="stylesheet" href="../css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print">	
<!--[if IE]><link rel="stylesheet" href="../css/ie.css" type="text/css" media="screen, projection"><![endif]-->

<!-- Scripts for JQuery -->
	<script src="../js/jquery-1.3.2.min.js"></script>
	<link type="text/css" href="../js/jquery/start/jquery-ui-1.7.3.custom.css" rel="stylesheet" />	
	<script type="text/javascript" src="../js/jquery/jquery-ui-1.7.3.custom.min.js"></script>
	<script src="../js/jquery.fg-button.js"></script>

</head>
<body>

<div style="padding: 10px;">

<?php

$allowuse = FALSE;

if ($no_login == TRUE) {
	$allowuse = FALSE;
	}
else {
	if ($login_wordpress == TRUE){
		if (is_user_logged_in() == TRUE){
			$allowuse = TRUE;
			}
		else {
			$allowuse = FALSE;
			}
		}
	else{
		if (sessionAuthenticate($connection)) {
			$allowuse = TRUE;
			}
		}
	}	
		

if ($allowuse == FALSE){
	echo "<p>You have to be logged in to use this tool.";
	die();
	}

$username = $_COOKIE["username"];

$UserID=query_one("SELECT UserID FROM Users WHERE UserName='$username' LIMIT 1", $connection);

#Sanitize inputs:
$t_min=filter_var($_POST["t_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$t_max=filter_var($_POST["t_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_min=filter_var($_POST["f_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_max=filter_var($_POST["f_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$SoundID=filter_var($_POST["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$from_db=filter_var($_POST["from_db"], FILTER_SANITIZE_STRING_INT);

#Escape to prevent SQL injection
$mark_tag=mysqli_real_escape_string($connection, $_POST["mark_tag"]);

if (isset($_COOKIE["fft"])) {
	$fft_size=$_COOKIE["fft"];
	}
else {
	$fft_size=2048;
	}


#Build query
$query = "INSERT INTO SoundsMarks (SoundID, time_min, time_max, freq_min, freq_max, mark_tag, fft_size, UserID)
		VALUES ('$SoundID', '$t_min', '$t_max', '$f_min', '$f_max', '$mark_tag', '$fft_size', '$UserID')";

#Execute query or die and display error message
$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));

#Make the new mark into a tag
#remove spaces
$mark_tag=str_replace(" ","", $mark_tag);

#Check that it does not exist already for this sound
$result=query_several("SELECT Tag FROM Tags WHERE SoundID='$SoundID' AND Tag='$mark_tag'", $connection);
$nrows = mysqli_num_rows($result);
if ($nrows==0) {			
	$query_tags = "INSERT INTO Tags (SoundID, Tag) VALUES ('$SoundID', '$mark_tag')";
	$result_tags = mysqli_query($connection, $query_tags)
		or die (mysqli_error($connection));
	}

echo "<p class=\"success\">Record inserted in database.";

?>

<p><a href="#" onClick="opener.location.reload();window.close();">Close window</a>

</div>

</body>
</html>
