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
require("check_admin.php");

if ($pumilio_admin == FALSE){
	header("Location: ../error.php?e=admin");
	die();
	}

#Sanitize
if ($_POST["Recorder"]!=""){
	$Recorder=filter_var($_POST["Recorder"], FILTER_SANITIZE_STRING);
	}
if ($_POST["Microphone"]!=""){
	$Microphone=filter_var($_POST["Microphone"], FILTER_SANITIZE_STRING);
	}
if ($_POST["Notes"]!=""){
	$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
	}

if ($Notes == ""){
	$Notes = "NULL";
	}

	$sensor = array(
		'Recorder' => $Recorder,
		'Microphone' => $Microphone,
		'Notes' => $Notes
	);
		DB::insert('Sensors', $sensor);

header("Location: ../admin.php?t=4");
die();
?>
