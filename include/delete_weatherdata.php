<?php

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

$WeatherSiteID=filter_var($_POST["WeatherSiteID"], FILTER_SANITIZE_NUMBER_INT);

	$query_file = "DELETE FROM WeatherData WHERE WeatherSiteID='$WeatherSiteID'";
	$result_file = mysqli_query($connection, $query_file)
		or die (mysqli_error($connection));
	// Relocate back to where you came from

	header("Location: ../admin.php?t=6");
	die();

?>
