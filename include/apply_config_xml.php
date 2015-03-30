<?php

$absolute_dir=dirname(__FILE__);

$absolute_dir = preg_replace('/include$/', '', $absolute_dir);

$app_dir = substr($absolute_dir, strlen($_SERVER['DOCUMENT_ROOT']));

$app_url = "http://" . $_SERVER['SERVER_NAME'] . $app_dir;

$app_url = rtrim(preg_replace('/include$/', '', $app_url), "/");

$app_host = $_SERVER['HTTP_HOST'];

#Maintenance mode
# just add an empty file named maintenance to the root of the application

if (is_file("$absolute_dir/maintenance")) {
	header("Location: $app_url/error.php?e=maint");
	die();
	}

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
header("Content-type: text/xml");
#header( 'Content-Type: text/html; charset=utf-8' );
$queryutf = "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'";
$result = mysqli_query($connection, $queryutf)
		or die ("Could not execute query. Please try again later.");



#Get variables from db - new way
# using 
# https://github.com/Xeoncross/DByte
// We need this!
require('include/db/DB.php');

// Create a new PDO connection to MySQL
$pdo = new PDO(
	"mysql:dbname=$database;host=$host",
	"$user",
	"$password",
	array(
		PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
	)
);

DB::$c = $pdo;

$settings = DB::pairs('SELECT `Settings`, `Value` FROM `PumilioSettings`');

extract($settings, EXTR_OVERWRITE);
					 
?>
