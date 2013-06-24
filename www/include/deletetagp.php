<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$TagID=filter_var($_GET["TagID"], FILTER_SANITIZE_NUMBER_INT);


$query_tags = "DELETE FROM Tags WHERE TagID='$TagID'";
$result_tags = mysqli_query($connection, $query_tags)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
	header("Location: ../pumilio.php");
	die;

?>
