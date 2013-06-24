<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$KmlName=filter_var($_POST["KmlName"], FILTER_SANITIZE_STRING);
$KmlNotes=filter_var($_POST["KmlNotes"], FILTER_SANITIZE_STRING);
$KmlURL=filter_var($_POST["KmlURL"], FILTER_SANITIZE_URL);
$op=filter_var($_POST["op"], FILTER_SANITIZE_NUMBER_INT);
$KmlID=filter_var($_POST["KmlID"], FILTER_SANITIZE_NUMBER_INT);

$force_admin = TRUE;
require("include/check_admin.php");

#check if it exists
if ($op == "1") {
	$query = "INSERT INTO Kml (KmlName, KmlNotes, KmlURL) VALUES ('$KmlName', '$KmlNotes', '$KmlURL')";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
elseif ($op == "2") {
	$query = "DELETE FROM Kml WHERE KmlID='$KmlID' LIMIT 1";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}

header("Location: ../admin.php?t=2");
	die();
?>
