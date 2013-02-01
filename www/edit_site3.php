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

	if (!is_user_admin($username, $connection) || !sessionAuthenticate($connection)) {
		die("You are not an admin and cannot be here.");
		}

$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
$Notes = filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
if ($Notes == ""){
	$Notes_q = "";}
else{
	$Notes_q = ", Notes='$Notes'";
	}


	$query = ("UPDATE Sounds SET SensorID='$SensorID' 
			$Notes_q
			WHERE SiteID='$SiteID'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

	// Relocate back to where you came from
	header("Location: edit_site.php?SiteID=$SiteID&d=2#anchor2");
	die();
	
?>
