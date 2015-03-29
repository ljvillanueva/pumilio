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
$SiteName=filter_var($_POST["SiteName"], FILTER_SANITIZE_STRING);
$Lat=filter_var($_POST["Lat"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$Lon=filter_var($_POST["Lon"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$Elevation=filter_var($_POST["Elevation"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$DataSource=filter_var($_POST["DataSource"], FILTER_SANITIZE_STRING);


$query = ("INSERT INTO WeatherSites
	(WeatherSiteName,WeatherSiteLat,WeatherSiteLon,WeatherSiteElev,WeatherSiteSource) 
	VALUES ('$SiteName','$Lat','$Lon','$Elevation', '$DataSource')");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
	header("Location: ../admin.php?t=6");
	die();
?>
