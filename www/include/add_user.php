<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

#Check if user can edit files (i.e. has admin privileges)
$username = $_COOKIE["username"];

if (!is_user_admin2($username, $connection)) {
	die("user not admin");
	}

#Sanitize
$UserName=filter_var($_POST["UserName"], FILTER_SANITIZE_STRING);
$UserFullname=filter_var($_POST["UserFullname"], FILTER_SANITIZE_STRING);
$UserEmail=filter_var($_POST["UserEmail"], FILTER_SANITIZE_STRING);
$UserRole=filter_var($_POST["UserRole"], FILTER_SANITIZE_STRING);

$username_check = query_one("SELECT COUNT(*) FROM Users WHERE UserName='$UserName'", $connection);

if ($username_check!=0) {
	// Relocate back to the first page of the application
	header("Location: ../admin.php?t=3&u=2");
	die();
	}
	
$enc_password = md5('sound');
	
$query = ("INSERT INTO Users 
	(UserName,UserFullname,UserEmail,UserRole,UserPassword) 
	VALUES ('$UserName','$UserFullname','$UserEmail','$UserRole', '$enc_password')");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
header("Location: ../admin.php?t=3&u=1");
die();
?>
