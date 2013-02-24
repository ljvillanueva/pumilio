<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

$op=filter_var($_GET["op"], FILTER_SANITIZE_NUMBER_INT);
$KmlID=filter_var($_GET["KmlID"], FILTER_SANITIZE_NUMBER_INT);

$force_admin = TRUE;
require("include/check_admin.php");

#check if it exists
if ($op == "1") {
	$query = "UPDATE Kml SET KmlDefault='1' WHERE KmlID='$KmlID' LIMIT 1";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
elseif ($op == "2") {
	$query = "UPDATE Kml SET KmlDefault='0' WHERE KmlID='$KmlID' LIMIT 1";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}

header("Location: ../admin.php?t=2");
	die();
?>
