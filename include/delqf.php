<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$QualityFlagID=filter_var($_GET["QualityFlagID"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

	DB::query('DELETE FROM `QualityFlags` WHERE `QualityFlagID` = ?', array($QualityFlagID));

	$settings = array(
		'QualityFlagID' => '0'
	);
	DB::update('Sounds', $settings, $QualityFlagID, 'QualityFlagID');

	
// Relocate back to the first page of the application
	header("Location: ../admin.php?t=6");
	die();
?>
