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

require("apply_config.php");
$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$SensorID=filter_var($_GET["SensorID"], FILTER_SANITIZE_NUMBER_INT);

	$query = ("DELETE FROM Sensors WHERE SensorID='$SensorID'");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));

	$query = ("UPDATE Sounds SET SensorID = NULL WHERE SensorID='$SensorID'");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));

header("Location: admin.php?t=4");
die();

?>
