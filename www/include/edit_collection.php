<?php
session_start();

require("functions.php");

$config_file = '../config.php';

if (file_exists($config_file)) {
    require($config_file);
} else {
    header("Location: ../error.php?e=config");
    die();
}

require("apply_config_include.php");
$force_admin = TRUE;
require("check_admin.php");

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$CollectionName=filter_var($_POST["CollectionName"], FILTER_SANITIZE_STRING);
$Author=filter_var($_POST["Author"], FILTER_SANITIZE_STRING);
$CollectionFullCitation=filter_var($_POST["CollectionFullCitation"], FILTER_SANITIZE_STRING);
$MiscURL=filter_var($_POST["MiscURL"], FILTER_SANITIZE_STRING);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
$FilesSource=filter_var($_POST["FilesSource"], FILTER_SANITIZE_STRING);

$query = ("UPDATE Collections SET CollectionName='$CollectionName', 
		Author='$Author',
		CollectionFullCitation='$CollectionFullCitation',
		MiscURL='$MiscURL',
		Notes='$Notes',
		FilesSource='$FilesSource'
		WHERE ColID='$ColID' LIMIT 1");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to where you came from
header("Location: ../edit_collection.php?ColID=$ColID&d=1");
die();
	
?>
