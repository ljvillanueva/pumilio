<?php
/*
if ($player_format=="mp4")
	{
	#Nero AAC encoder
	$player_encoder="include/neroAacEnc";
	}
elseif ($player_format=="mp3")
	{
	#LAME encoder
	$player_encoder="lame";
	}
*/
$player_format="mp3";
$player_encoder="lame";

#Database is mandatory now
#if ($use_database=="TRUE")
#	{
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
			
#	}
	
?>
