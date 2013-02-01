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

#Check if user can edit files (i.e. has admin privileges)
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die("user not admin");
		}

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
	header("Location: admin.php?t=8");
	die();
?>
