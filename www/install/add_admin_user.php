<?php

require("../include/functions.php");
require("../config.php");
require("apply_config.php");

#Sanitize
$UserName=filter_var($_POST["UserName"], FILTER_SANITIZE_STRING);
$UserFullname=filter_var($_POST["UserFullname"], FILTER_SANITIZE_STRING);
$UserEmail=filter_var($_POST["UserEmail"], FILTER_SANITIZE_STRING);

$newpassword1=filter_var($_POST["newpassword1"], FILTER_SANITIZE_STRING);
$newpassword2=filter_var($_POST["newpassword2"], FILTER_SANITIZE_STRING);

if ($newpassword1!=$newpassword2) {
	header("Location: index.php?e=1");
	die();
	}

$enc_password = md5($newpassword1);

//Empty database
$result_check = mysqli_query($connection, "DROP TABLE IF EXISTS Cookies, Equipment, ProcessLog, PumilioSettings, Queue, QueueJobs, SampleMembers, Samples, Scripts, Sites, SitesPhotos, Sounds, SoundsImages, SoundsMarks, Sources, Tags, Users, WeatherData, WeatherSites")
	or die (mysqli_error($connection));

$connection2 = mysqli_connect($host, $user, $password, $database);
$all_query = file_get_contents("./pumilio.sql");
mysqli_multi_query($connection2, $all_query);
mysqli_close($connection2);
sleep(5);

#$enc_password = md5('sound');

$query = ("INSERT INTO Users 
	(UserName,UserFullname,UserEmail,UserRole,UserPassword) 
	VALUES ('$UserName','$UserFullname','$UserEmail','admin', '$enc_password')");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
header("Location: index.php?e=0");
die();
?>
