<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");

#Check if user can edit files (i.e. has admin privileges)
	$username = $_COOKIE["username"];

	if (!is_user_admin2($username, $connection) || !sessionAuthenticate($connection))
		{die();}

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);

$CollectionName=filter_var($_POST["CollectionName"], FILTER_SANITIZE_STRING);


$Author=filter_var($_POST["Author"], FILTER_SANITIZE_STRING);
$SourceFullCitation=filter_var($_POST["SourceFullCitation"], FILTER_SANITIZE_STRING);
$MiscURL=filter_var($_POST["MiscURL"], FILTER_SANITIZE_STRING);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);
$FilesSource=filter_var($_POST["FilesSource"], FILTER_SANITIZE_STRING);


	$query = ("UPDATE Collections SET CollectionName='$CollectionName', 
			Author='$Author',
			SourceFullCitation='$SourceFullCitation',
			MiscURL='$MiscURL',
			Notes='$Notes',
			FilesSource='$FilesSource'
			WHERE ColID='$ColID' LIMIT 1");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));

	// Relocate back to where you came from

		header("Location: edit_source.php?ColID=$ColID&d=1");
		die;
	
?>
