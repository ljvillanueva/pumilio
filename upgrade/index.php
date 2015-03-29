<?php

require("../include/functions.php");
require("../config.php");

$absolute_dir=dirname(__FILE__);

$absolute_dir = preg_replace('/include$/', '', $absolute_dir);

$app_dir = substr($absolute_dir, strlen($_SERVER['DOCUMENT_ROOT']));

$app_url = "http://" . $_SERVER['SERVER_NAME'] . $app_dir;

$app_url = rtrim(preg_replace('/include$/', '', $app_url), "/");

$connection = @mysqli_connect($host, $user, $password, $database);

#If could not connect, redirect
if (!$connection) {
	header("Location: ../error.php?e=db");
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
	





require("../config.php");

//Check database
#Current version
$current_db_version=26;

$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);

#From version 3
if ($db_version==3) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio3.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio4.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio5.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio6.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio7.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio8.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$UserPassword_enc = md5($UserPassword);

				$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
				$result_pass = mysqli_query($connection, $query_pass)
					or die (mysqli_error($connection));
					
				unset($UserPassword);
				unset($UserID);
				}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	if ($use_googlemaps=="FALSE")
		$use_googlemaps=0;
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);

	if ($hide_latlon_guests=="FALSE")
		$hide_latlon_guests=0;	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);

	if ($allow_upload=="FALSE")
		$allow_upload=0;	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);

	if ($guests_can_open=="FALSE")
		$guests_can_open=0;	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==4) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio4.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio5.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
		
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio6.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio7.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);	

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio8.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$UserPassword_enc = md5($UserPassword);

				$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
				$result_pass = mysqli_query($connection, $query_pass)
					or die (mysqli_error($connection));
					
				unset($UserPassword);
				unset($UserID);
				}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
		
		
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);	

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
		

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==5) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio5.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio6.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio7.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio8.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
		
		
	#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$UserPassword_enc = md5($UserPassword);

				$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
				$result_pass = mysqli_query($connection, $query_pass)
					or die (mysqli_error($connection));
					
				unset($UserPassword);
				unset($UserID);
				}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
		
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==6) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio6.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio7.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio8.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$UserPassword_enc = md5($UserPassword);

				$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
				$result_pass = mysqli_query($connection, $query_pass)
					or die (mysqli_error($connection));
					
				unset($UserPassword);
				unset($UserID);
				}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
				

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==7) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio7.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio8.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$UserPassword_enc = md5($UserPassword);

				$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
				$result_pass = mysqli_query($connection, $query_pass)
					or die (mysqli_error($connection));
					
				unset($UserPassword);
				unset($UserID);
				}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);	

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
			

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==8) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio8.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
		#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

			if ($nrows>0) {
				for ($i=0;$i<$nrows;$i++) {
					$row = mysqli_fetch_array($result);
					extract($row);

					$UserPassword_enc = md5($UserPassword);

					$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
					$result_pass = mysqli_query($connection, $query_pass)
						or die (mysqli_error($connection));
						
					unset($UserPassword);
					unset($UserID);
					}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
			

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==9) {
		#UPDATE PASSWORDS TO MD5

		$query = "SELECT UserID,UserPassword FROM Users";
		$result = mysqli_query($connection, $query)
			or die ("Could not execute query. Please try again later.");
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$UserPassword_enc = md5($UserPassword);

				$query_pass = "UPDATE Users SET UserPassword='$UserPassword_enc' WHERE UserID='$UserID' LIMIT 1";
				$result_pass = mysqli_query($connection, $query_pass)
					or die (mysqli_error($connection));
					
				unset($UserPassword);
				unset($UserID);
				}
			}
			
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio9.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);		

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}


	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==10) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio10.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);		

	#Get variables from config.php to database
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_name', '$app_custom_name')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('app_custom_text', '$app_custom_text')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('use_googlemaps', '$use_googlemaps')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('googlemaps_key', '$googlemaps_key')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('hide_latlon_guests', '$hide_latlon_guests')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('allow_upload', '$allow_upload')", $connection);
	
	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('guests_can_open', '$guests_can_open')", $connection);

	query_one("INSERT INTO PumilioSettings (Settings, Value) VALUES ('spectrogram_palette', '$spectrogram_palette')", $connection);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);


	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==11) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio11.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);		

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==12) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio12.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}


	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==13) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio13.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Delete images for v2.0
	$del_img = query_one("DELETE * FROM SoundsImages", $connection);
	$query_del = "SELECT DISTINCT ColID from Sounds";
	$result_del = mysqli_query($connection, $query_del)
		or die (mysqli_error($connection));
	$nrows_del = mysqli_num_rows($result_del);
	if ($nrows_del>0) {
		for ($d=0;$d<$nrows_del;$d++) {
				$row_del = mysqli_fetch_array($result_del);
				extract($row_del);
				delTree("../sounds/images/" . $ColID);
			}
		}

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==14) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio14.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==15) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio15.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}

	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==16) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio16.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#MOVE FILES
	$query_move = "SELECT * from Sounds";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = rand(1,100);

			#Check if dir exists
			if (!is_dir("../sounds/sounds/$ColID/$DirID")) {
				mkdir("../sounds/sounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/sounds/$ColID/$OriginalFilename")) {
				if (!rename("../sounds/sounds/$ColID/$OriginalFilename","../sounds/sounds/$ColID/$DirID/$OriginalFilename")){
					die("Could not move file sounds/sounds/$ColID/$OriginalFilename.");}
				}

			query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);

			#Move MP3
			#Check if dir exists
			if (!is_dir("../sounds/previewsounds/$ColID/$DirID")) {
				mkdir("../sounds/previewsounds/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/previewsounds/$ColID/$MP3Filename")) {
				if (!rename("../sounds/previewsounds/$ColID/$MP3Filename","../sounds/previewsounds/$ColID/$DirID/$MP3Filename")){
					die("Could not move file sounds/previewsounds/$ColID/$MP3Filename.");}
				}
		}
		}

	$query_move = "SELECT * from Sounds, SoundsImages WHERE Sounds.SoundID=SoundsImages.SoundID";
	$result_move = mysqli_query($connection, $query_move)
		or die (mysqli_error($connection));
	$nrows_move = mysqli_num_rows($result_move);

	if ($nrows_move > 0) {
		for ($m=0; $m<$nrows_move; $m++) {
			$row_move = mysqli_fetch_array($result_move);
			extract($row_move);

			$DirID = query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID'", $connection);

			#Check if dir exists
			if (!is_dir("../sounds/images/$ColID/$DirID")) {
				mkdir("../sounds/images/$ColID/$DirID", 0777);
				}

			if (is_file("../sounds/images/$ColID/$ImageFile")) {
				if (!rename("../sounds/images/$ColID/$ImageFile","../sounds/images/$ColID/$DirID/$ImageFile")){
					die("Could not move file sounds/images/$ColID/$ImageFile.");}
				}
		}
		}


	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version) {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else
		{
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==17) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio17.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==18) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio18.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==19) {
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio19.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==20) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio20.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==21) {
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio21.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==22) {
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio22.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==23) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio23.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==24) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio24.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==25) {

	$connection2 = mysqli_connect($host, $user, $password, $database);
	$all_query = file_get_contents("./upgrade_pumilio25.sql");
	mysqli_multi_query($connection2, $all_query);
	mysqli_close($connection2);
	sleep(5);
	
	#Verify that the upgrade was successful
	$db_version=query_one("SELECT Value FROM PumilioSettings WHERE Settings LIKE 'db_version' LIMIT 1", $connection);
	if ($db_version==$current_db_version){
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system has been upgraded. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();
		}
	else {
		echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">There was an error in the upgrade process. Please check the mysql and apache logs or contact the developer for help.</div>
		</body>
		</html>"; 
		die();
		}

	}
elseif ($db_version==$current_db_version) {
	echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"success\">This system is already up to date. You can delete the folders 'upgrade' and 'install'.</div>
		</body>
		</html>"; 
		die();

	}
else {
	echo "
		<html>
		<head>

		<title>Pumilio - Upgrade</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"../css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div class=\"error\">This system has a database that is too old or was edited manually.</div>
		</body>
		</html>"; 
		die();
	}

?>
