<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

#Check if user can edit files (i.e. has admin privileges)
	if (!sessionAuthenticate($connection)){
		die("You are not logged in.");
		}
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die("You are not admin.");
		}

$PID=filter_var($_GET["pid"], FILTER_SANITIZE_NUMBER_INT);

if ($PID != ""){
	exec('kill ' . $PID, $out, $retval);
	}

// Relocate back to the first page of the application
	header("Location: ../admin.php");
	die();

?>
