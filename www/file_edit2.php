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

$SoundID=filter_var($_POST["SoundID"], FILTER_SANITIZE_NUMBER_INT);
if ($_POST["OtherSoundID"]!="")
	$OtherSoundID=filter_var($_POST["OtherSoundID"], FILTER_SANITIZE_NUMBER_INT);

if ($_POST["Date"]!="")
	$Date=$_POST["Date"];

if ($_POST["Time"]!="")
	$Time=$_POST["Time"];

$OldSiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID'", $connection);

$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$SoundName=filter_var($_POST["SoundName"], FILTER_SANITIZE_STRING);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
$where_to=filter_var($_POST["where_to"], FILTER_SANITIZE_URL);

$date=explode("/", $Date);
$date_f=$date[2] . "-" . $date[0] . "-" . $date[1];


	$query = ("UPDATE Sounds SET SoundName='$SoundName', 
			OtherSoundID='$OtherSoundID',
			Date='$date_f',
			Time='$Time',
			SiteID='$SiteID',
			SensorID='$SensorID',
			Notes='$Notes'
			WHERE SoundID='$SoundID' LIMIT 1");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

	$query = ("UPDATE Sounds SET OtherSoundID = NULL 
			WHERE OtherSoundID ='0'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

	$query = ("UPDATE Sounds SET SensorID = NULL 
			WHERE SensorID ='0'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

	$query = ("UPDATE Sounds SET Date = NULL 
			WHERE Date ='0000-00-00'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

	$query = ("UPDATE Sounds SET Time = NULL 
			WHERE Time ='00:00'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));


	// Relocate back to where you came from
	header("Location: $where_to&d=1");
	die();
	
?>
