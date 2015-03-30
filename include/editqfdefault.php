<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$defaultqf=filter_var($_POST["defaultqf"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

	$check = DB::column('SELECT COUNT(*) FROM `PumilioSettings` WHERE Settings = "default_qf"');
	$settings = array(
		'Settings' => 'default_qf',
		'Value' => $defaultqf
	);
	if ($check == 0){
		DB::insert('PumilioSettings', $settings);
		}
	else{
		DB::update('PumilioSettings', $settings, 'default_qf', 'Settings');
		}

// Relocate back to the first page of the application
	header("Location: ../admin.php?t=6&u=4");
	die();
?>
