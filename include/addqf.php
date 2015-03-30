<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$QualityFlagID=filter_var($_POST["QualityFlagID"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$QualityFlag=filter_var($_POST["QualityFlag"], FILTER_SANITIZE_STRING);

if ($QualityFlagID==""){
	header("Location: ../admin.php?t=6&u=2");
	die();
	}

if ($QualityFlag==""){
	header("Location: ../admin.php?t=6&u=2");
	die();
	}

$flag_check = query_one("SELECT COUNT(*) FROM QualityFlags WHERE QualityFlagID='$QualityFlagID'", $connection);

if ($flag_check!=0) {
	header("Location: ../admin.php?t=6&u=3");
	die();
	}
			
$query = ("INSERT INTO QualityFlags 
		(QualityFlagID, QualityFlag) 
		VALUES ('$QualityFlagID','$QualityFlag')");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
	header("Location: ../admin.php?t=6&u=1");
	die();
?>
