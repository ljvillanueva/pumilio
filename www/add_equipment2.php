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

require("include/check_login.php");

#Sanitize
if ($_POST["Recorder"]!="")
	$Recorder=filter_var($_POST["Recorder"], FILTER_SANITIZE_STRING);
if ($_POST["Microphone"]!="")
	$Microphone=filter_var($_POST["Microphone"], FILTER_SANITIZE_STRING);
if ($_POST["Notes"]!="")
	$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);

	$query = ("INSERT INTO Sensors 
		(Recorder,Microphone,Notes) 
		VALUES ('$Recorder', '$Microphone', '$Notes')");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

header("Location: admin.php?t=4");
	die();
?>
