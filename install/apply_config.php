<?php

$absolute_dir=dirname(__FILE__);

$absolute_dir = preg_replace('/install$/', '', $absolute_dir);

$app_dir = substr($absolute_dir, strlen($_SERVER['DOCUMENT_ROOT']));

$app_url = "http://" . $_SERVER['SERVER_NAME'] . $app_dir;

$app_url = rtrim(preg_replace('/install$/', '', $app_url), "/");

#Maintenance mode
# just add an empty file named maintenance to the root of the application
if (is_file("$absolute_dir/maintenance")) {
	header("Location: $app_url/error.php?e=maint");
	die();
	}
	

#Try to connect to the db
$connection = @mysqli_connect($host, $user, $password, $database);

#If could not connect, redirect
if (!$connection) {
	header("Location: $app_url/error.php?e=db");
	die();
	}

mb_language('uni');
mb_internal_encoding('UTF-8');

#Disable caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // From http://us3.php.net/header

#Set all as utf-8 for DB
header( 'Content-Type: text/html; charset=utf-8' );
$queryutf = "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'";
$result = mysqli_query($connection, $queryutf)
		or die ("Could not execute query. Please try again later.");

			
?>
