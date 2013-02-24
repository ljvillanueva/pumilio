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

$force_admin = TRUE;
require("include/check_admin.php");

$ColID = filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID = filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$type = filter_var($_POST["type"], FILTER_SANITIZE_STRING);
$setqf = filter_var($_POST["setqf"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$q = filter_var($_POST["q"], FILTER_SANITIZE_URL);

if ($type == "col"){
	$query = ("UPDATE Sounds SET QualityFlagID='$setqf' 
			WHERE ColID='$ColID' AND 
			QualityFlagID='0'");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
elseif ($type == "site"){
	$query = ("UPDATE Sounds SET QualityFlagID='$setqf' 
			WHERE SiteID='$SiteID' AND 
			QualityFlagID='0'");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
	
// Relocate back to where you came from
$q = str_replace("&d=1","",$q);
header("Location: qa.php?$q&d=1");
die();
	
?>
