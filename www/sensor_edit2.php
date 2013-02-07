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

#Check if user can edit files (i.e. has admin privileges)
	$username = $_COOKIE["username"];

	if (!is_user_admin2($username, $connection)) {
		die();
		}

#Sanitize
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$Recorder=filter_var($_POST["Recorder"], FILTER_SANITIZE_STRING);
$Microphone=filter_var($_POST["Microphone"], FILTER_SANITIZE_STRING);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);

	$query = ("UPDATE Sensors SET Recorder='$Recorder', Microphone='$Microphone', Notes='$Notes' WHERE SensorID='$SensorID'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

header("Location: admin.php?t=4");
	die();
?>
