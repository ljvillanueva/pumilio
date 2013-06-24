<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$KmlDefault=filter_var($_GET["KmlDefault"], FILTER_SANITIZE_NUMBER_INT);
$KmlID=filter_var($_GET["KmlID"], FILTER_SANITIZE_NUMBER_INT);

$force_admin = TRUE;
require("check_admin.php");

$query = "UPDATE Kml SET KmlDefault='$KmlDefault' WHERE KmlID='$KmlID' LIMIT 1";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

header("Location: ../admin.php?t=2");
die();
?>
