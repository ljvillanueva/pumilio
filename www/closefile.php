<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require("config.php");
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");

$Token=filter_var($_GET["Token"], FILTER_SANITIZE_STRING);
$username = $_COOKIE["username"];
$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);

$valid_token = query_one("SELECT COUNT(*) FROM Tokens WHERE TokenID='$Token' AND UserID='$UserID'", $connection);

if ($valid_token==1) {
	$SoundID = query_one("SELECT soundfile_id FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$random_cookie = query_one("SELECT random_cookie FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$from_db = query_one("SELECT from_db FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	}

$del_token=query_one("DELETE FROM Tokens WHERE TokenID='$Token'", $connection);

#rm temp dir
delTree('tmp/' . $random_cookie . '/');


// Relocate back to the first page of the application

if ($from_db=="TRUE") {
	#echo $from_db;
	header("Location: ./db_filedetails.php?SoundID=$SoundID");
	die();
	}
else {
	header("Location: ./");
	die();
	}
?>
