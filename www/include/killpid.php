<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

$PID=filter_var($_GET["pid"], FILTER_SANITIZE_NUMBER_INT);

if ($PID != ""){
	exec('kill ' . $PID, $out, $retval);
	}

// Relocate back to the first page of the application
	header("Location: ../admin.php");
	die();

?>
