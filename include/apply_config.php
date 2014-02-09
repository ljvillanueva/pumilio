<?php

$absolute_dir=dirname(__FILE__);

$absolute_dir = preg_replace('/include$/', '', $absolute_dir);

$app_dir = substr($absolute_dir, strlen($_SERVER['DOCUMENT_ROOT']));

$app_url = "http://" . $_SERVER['SERVER_NAME'] . $app_dir;

$app_url = rtrim(preg_replace('/include$/', '', $app_url), "/");

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

#Test settings variables
if ($app_custom_name==""){
	$app_custom_name="Pumilio";
	}
	
if (!isset($googlemaps_ver)){
	$googlemaps_ver = "";
	}

if ($googlemaps_ver=="") {
	if ($use_googlemaps == "3"){
		$googlemaps_ver="3";
		}
	}

if ($app_custom_text=="") {
	$app_custom_text="Pumilio is a free and open source sound archive manager for the visualization and manipulation of sound files.";
	}


if (isset($fft)==FALSE) {
	$fft=2048;
	query_one("REPLACE INTO PumilioSettings (Settings, Value) VALUES ('fft', '$fft')", $connection);
	}
else{
	if ($fft=="") {
		$fft=2048;
		query_one("REPLACE INTO PumilioSettings (Settings, Value) VALUES ('fft', '$fft')", $connection);
		}
	}
	
	
if ($googlemaps_ver=="3") {
	$googlemaps3_key = DB::column('SELECT Value FROM `PumilioSettings` WHERE Settings = "googlemaps3_key"');
	}

if (!isset($AudioPreviewFormat)){
	$AudioPreviewFormat = "";
	}

if ($AudioPreviewFormat=="ogg"){
	$player_encoder="dir2ogg";
	}
elseif ($AudioPreviewFormat=="mp3"){
	#LAME encoder
	$player_encoder="lame";
	}
elseif ($AudioPreviewFormat==""){
	#$AudioPreviewFormat="ogg";
	#$player_encoder="dir2ogg";
	$AudioPreviewFormat="mp3";
	$player_encoder="lame";
	}

$player_format = $AudioPreviewFormat;

if (!sessionAuthenticate($connection) && isset($_COOKIE["usercookie"])) {
	$cookie_to_test = $_COOKIE["usercookie"];
	$cookie_to_testa = explode(".", $cookie_to_test);
	$cookie_to_test1 = $cookie_to_testa['0'];
	$cookie_to_test2 = $cookie_to_testa['1'];

	$query = "DELETE FROM Cookies WHERE user_id = '$cookie_to_test1' AND cookie = '$cookie_to_test2'";
	$result = mysqli_query($connection, $query)
	       or die (mysqli_error($connection));

	setcookie("usercookie", "1", time()-3600, $app_dir);
	setcookie("username", "1", time()-3600, $app_dir);
	}

if (!isset($special_wrapper)){
	$special_wrapper = FALSE;
	}

if (!isset($useR)){
	$useR = FALSE;
	}

if ($useR == TRUE){
	if (!isset($R_ADI_db_value)){
		$R_ADI_db_value = "-50";
		}
	if (!isset($R_ADI_max_freq)){
		$R_ADI_max_freq = "10000";
		}
	if (!isset($R_ADI_freq_step)){
		$R_ADI_freq_step = "1000";
		}
	if (!isset($R_H_segment_length)){
		$R_H_segment_length = "60";
		}
	}

if (!isset($login_wordpress)){
	$login_wordpress = FALSE;
	}

if (!isset($special_noprocess)){
	$special_noprocess = FALSE;
	}

if (!isset($special_nofiles)){
	$special_nofiles = FALSE;
	}

if (!isset($force_login)){
	$force_login = FALSE;
	}

if (!isset($mark_tag_name)){
	$mark_tag_name = "Species";
	}

if ($login_wordpress == TRUE){
	if (is_file($wordpress_require)){
		require_once($wordpress_require);
		header('HTTP/1.1 200 OK');
		}
	else{
		if (is_file('../' . $wordpress_require)){
			require_once('../' . $wordpress_require);
			header('HTTP/1.1 200 OK');
			}
		else{
			die("Could not find the Wordpress installation.");
			}
		}
	}

#Check sox version
#$sox_version=query_one("SELECT Value from PumilioSettings WHERE Settings='sox_version'", $connection);
$sox_version = DB::column('SELECT Value FROM `PumilioSettings` WHERE Settings = "sox_version"');

# using only forward of version 14.3.2 (w: 5000 h: )
if ($sox_version == ""){
	exec('sox --version', $soxout, $soxretval);
	$sox_version=explode("v",$soxout[0]);
	$sox_version = $sox_version[1];
	$soxver=explode(".",$sox_version);
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('sox_version', '$sox_version')", $connection);
	}

	$soxver=explode(".",$sox_version);
	if ($soxver[0] > 14){
		$sox_images = TRUE;
		}
	elseif ($soxver[0] = 14){
		if ($soxver[1] > 3){
			$sox_images = TRUE;
			}
		elseif ($soxver[1] < 3){
			$sox_images = FALSE;
			}
		else{
			if ($soxver[2] >= 2){
				$sox_images = TRUE;
				}
			else{
				$sox_images = FALSE;
				}
			}
		}
	else{
		$sox_images = FALSE;
		}

################
#Turn SoX option off while finishing rest of code
$sox_images = FALSE;

#Execute custom code, if set
if (is_file("$absolute_dir/customcode.php")) {
		include("customcode.php");
	}

?>
