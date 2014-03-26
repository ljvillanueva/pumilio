<?php
session_start();

require("functions.php");

$config_file = '../config.php';

if (file_exists($config_file)) {
    require($config_file);
} else {
    header("Location: ../error.php?e=config");
    die();
}

require("apply_config_include.php");
$force_loggedin = TRUE;
require("check_login.php");

#Sanitize
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$Recorder=filter_var($_POST["Recorder"], FILTER_SANITIZE_STRING);
$Microphone=filter_var($_POST["Microphone"], FILTER_SANITIZE_STRING);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);


	$settings = array(
		'Recorder' => $Recorder,
		'Microphone' => $Microphone,
		'Notes' => $Notes
	);
	DB::update('Sensors', $settings, $SensorID, 'SensorID');

header("Location: ../sensor_edit.php?u=1&SensorID=$SensorID");
die();
?>
