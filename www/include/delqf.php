<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$QualityFlagID=filter_var($_GET["QualityFlagID"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

if ($QualityFlagID==""){
	die("That value does not exists.");
	}

$flag_check = query_one("SELECT COUNT(*) FROM QualityFlags WHERE QualityFlagID='$QualityFlagID'", $connection);

if ($flag_check==0) {
	die("That value does not exists.");
	}
			
$query = ("DELETE FROM QualityFlags WHERE QualityFlagID='$QualityFlagID'");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

$query = ("UPDATE QualityFlags SET QualityFlagID='0' WHERE QualityFlagID='$QualityFlagID'");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
	
// Relocate back to the first page of the application
	header("Location: ../admin.php?t=9");
	die();
?>
