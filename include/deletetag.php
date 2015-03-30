<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");

$SoundID = filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$TagID = filter_var($_GET["TagID"], FILTER_SANITIZE_NUMBER_INT);

if (isset($_GET["where_to"])){
	$where_to = filter_var($_GET["where_to"], FILTER_SANITIZE_URL);
	}
else{
	$where_to = "";
	}

if (isset($_GET["goto"])){
	$goto = filter_var($_GET["goto"], FILTER_SANITIZE_URL);
	}
else{
	$goto = "";
	}


$query_tags = "DELETE FROM Tags WHERE TagID = '$TagID'";
$result_tags = mysqli_query($connection, $query_tags)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
if ($goto == "db"){
	header("Location: ../db_filedetails.php?SoundID=$SoundID");
	die();
	}
elseif ($goto == "o"){
	header("Location: $where_to");
	die();
	}
else {
	header("Location: ../db_filedetails.php?SoundID=$SoundID");
	die();
	}
?>