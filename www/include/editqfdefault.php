<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$defaultqf=filter_var($_POST["defaultqf"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$check=query_one("SELECT COUNT(*) from PumilioSettings WHERE Settings='default_qf'", $connection);

if ($check==0) {
	$query = ("INSERT INTO PumilioSettings (Settings, Value) 
		VALUES ('default_qf','$defaultqf')");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
else {
	$query = ("UPDATE PumilioSettings SET Value='$defaultqf' WHERE Settings='default_qf'");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}

// Relocate back to the first page of the application
	header("Location: ../admin.php?t=9&u=4");
	die();
?>
