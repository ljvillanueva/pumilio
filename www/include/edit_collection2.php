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

$ColID = filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID = filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$Notes = filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
if ($Notes == ""){
	$Notes_q = "";}
else{
	$Notes_q = ", Notes='$Notes'";
	}

$query = ("UPDATE Sounds SET SensorID='$SensorID' 
	$Notes_q
	WHERE ColID='$ColID'");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to where you came from
header("Location: ../edit_collection.php?ColID=$ColID&d=2#anchor2");
die();
	
?>
