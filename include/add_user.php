<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

#Check if user can edit files (i.e. has admin privileges)
$username = $_COOKIE["username"];

$force_admin = TRUE;
require("check_admin.php");

#Sanitize
$UserName=filter_var($_POST["UserName"], FILTER_SANITIZE_STRING);
$UserFullname=filter_var($_POST["UserFullname"], FILTER_SANITIZE_STRING);
$UserEmail=filter_var($_POST["UserEmail"], FILTER_SANITIZE_STRING);
$UserRole=filter_var($_POST["UserRole"], FILTER_SANITIZE_STRING);
$newpassword1=filter_var($_POST["newpassword1"], FILTER_SANITIZE_STRING);
$newpassword2=filter_var($_POST["newpassword2"], FILTER_SANITIZE_STRING);

$username_check = query_one("SELECT COUNT(*) FROM Users WHERE UserName='$UserName'", $connection);

if ($username_check!=0) {
	// Relocate back to the first page of the application
	header("Location: ../admin.php#manageusers?u=2");
	die();
	}
	

if ($newpassword1 != $newpassword2){
	header("Location: ../admin.php#manageusers?u=4");
	die();	
}

$enc_password = md5($newpassword1);
	
$query = ("INSERT INTO Users 
	(UserName,UserFullname,UserEmail,UserRole,UserPassword) 
	VALUES ('$UserName','$UserFullname','$UserEmail','$UserRole', '$enc_password')");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
header("Location: ../admin.php#manageusers?u=1");
die();
?>
