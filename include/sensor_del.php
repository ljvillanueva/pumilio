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
$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$SensorID=filter_var($_GET["SensorID"], FILTER_SANITIZE_NUMBER_INT);

	DB::query('DELETE FROM `Sensors` WHERE `SensorID` = ?', array($SensorID));

	$settings = array(
		'SensorID' => 'NULL'
	);
	DB::update('Sounds', $settings, $SensorID, 'SensorID');

header("Location: admin.php?t=4");
die();

?>
