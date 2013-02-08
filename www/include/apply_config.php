<?php

if ($app_url == ""){
	header("Location: error.php?e=appurl");
	die();
	}
	
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
header( 'Content-Type: text/html; charset=utf-8' );
$queryutf = "SET NAMES 'utf8' COLLATE 'utf8_unicode_ci'";
$result = mysqli_query($connection, $queryutf)
		or die ("Could not execute query. Please try again later.");
		
#Get variables from db
$app_custom_name=query_one("SELECT Value from PumilioSettings WHERE Settings='app_custom_name'", $connection);

if ($app_custom_name==""){
	$app_custom_name="Pumilio";
	}
	
$app_custom_text=query_one("SELECT Value from PumilioSettings WHERE Settings='app_custom_text'", $connection);

if ($app_custom_text=="") {
	$app_custom_text="Pumilio is a free and open source sound archive manager for the visualization and manipulation of sound files.";
	}
					 
$use_googlemaps=query_one("SELECT Value from PumilioSettings WHERE Settings='use_googlemaps'", $connection);

$googlemaps_ver=query_one("SELECT Value from PumilioSettings WHERE Settings='googlemaps_ver'", $connection);

if ($googlemaps_ver=="") {
	if ($use_googlemaps == "1"){
		$googlemaps_ver="2";
		}
	elseif ($use_googlemaps == "3"){
		$googlemaps_ver="3";
		}
	}

if ($googlemaps_ver=="2") {
	$googlemaps_key=query_one("SELECT Value from PumilioSettings WHERE Settings='googlemaps_key'", $connection);
	}
elseif ($googlemaps_ver=="3") {
	$googlemaps3_key=query_one("SELECT Value from PumilioSettings WHERE Settings='googlemaps3_key'", $connection);
	}


$hide_latlon_guests=query_one("SELECT Value from PumilioSettings WHERE Settings='hide_latlon_guests'", $connection);

$allow_upload=query_one("SELECT Value from PumilioSettings WHERE Settings='allow_upload'", $connection);

$guests_can_open=query_one("SELECT Value from PumilioSettings WHERE Settings='guests_can_open'", $connection);

$guests_can_dl=query_one("SELECT Value from PumilioSettings WHERE Settings='guests_can_dl'", $connection);

$spectrogram_palette=query_one("SELECT Value from PumilioSettings WHERE Settings='spectrogram_palette'", $connection);

$default_qf=query_one("SELECT Value from PumilioSettings WHERE Settings='default_qf'", $connection);

$AudioPreviewFormat=query_one("SELECT Value from PumilioSettings WHERE Settings='AudioPreviewFormat'", $connection);

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

if (!isset($force_login)){
	$force_login = FALSE;
	}

#Execute custom code, if set
if (is_file("$absolute_dir/customcode.php")) {
		include("customcode.php");
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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";

?>
