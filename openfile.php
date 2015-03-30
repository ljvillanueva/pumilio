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

if ($special_noopen==TRUE){
	header("Location: error.php?e=noopen");
	die();
	}

$random_cookie=$_COOKIE["random_cookie"];
$soundfile_format=$_GET["format"];
$soundfile_name=$_GET["filename"];
$soundfile_samplingrate=$_GET["samprate"];
$soundfile_samplingrateoriginal=$_GET["samprate"];

if (isset($_GET["filename"])) {
	$TokenID=mt_rand(1,10000);
	$TokenID = md5($TokenID);

	$soundfile_format = $_GET["format"];
	$soundfile_name = $_GET["filename"];
	$soundfile_duration = $_GET["duration"];
	$soundfile_samplingrate = $soundfile_samplingrate;
	$soundfile_samplingrateoriginal = $soundfile_samplingrateoriginal;
	$soundfile_id = $_GET["fileID"];
	$no_channels = $_GET["no_channels"];
	if ($_GET["from_db"]) {
		$from_db = "TRUE";
		}
	else {
		$from_db = "FALSE";
		}

	$username = $_COOKIE["username"];
	$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);

	$t_query = "INSERT INTO Tokens 
		(TokenID, UserID, soundfile_format, soundfile_name, soundfile_duration, soundfile_samplingrate, 
			soundfile_samplingrateoriginal, soundfile_id, no_channels, from_db, random_cookie) 
		VALUES ('$TokenID', '$UserID', '$soundfile_format', '$soundfile_name', '$soundfile_duration',
			'$soundfile_samplingrate', '$soundfile_samplingrateoriginal', '$soundfile_id', '$no_channels', 
			'$from_db', '$random_cookie')";
	$result = mysqli_query($connection, $t_query)
		or die (mysqli_error($connection));

	}
else {
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
	<html>
	<head>
	<title>$app_custom_name</title>

	<!-- Blueprint css -->
	<link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
	<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">	
	<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->

	</head>
	<body>

		<!--Blueprint container-->
		<div class=\"container\">
			<div class=\"span-18\">";
				require("include/topbar.php");
		echo "
			</div>
			<div class=\"span-6 last\">";
				require("include/topmenu.php");
		echo "
			</div>
			<div class=\"span-24 last\">
				<hr noshade>
			</div>
			<div class=\"span-24 last\">
				<div class=\"error\"><h4>There was an error, the system did not provide a file to use. Please go back and try again or contact your administrator.</h4></div>
			</div>
			<div class=\"span-24 last\">";
		require("include/bottom.php");
		echo "</div></div>
			</body>
			</html>";
	die();
	}

	#Make a wav file for the scripts to work on
	$original_filename=$soundfile_name;
	$fileName_exp=explode(".", $original_filename);

	#If a flac, extract
	if ($soundfile_format=="flac") {
		exec('flac -fd tmp/' . $random_cookie . '/' . $original_filename, $lastline, $retval);
			if ($retval!=0){
				die("<p class=\"error\">There was an error when opening the file with the FLAC decoder.</div>");
				}
	
		$new_soundfile_name=$fileName_exp[0] . ".wav";

		$result = mysqli_query($connection, "Update Tokens SET soundfile_wav='$new_soundfile_name' WHERE TokenID='$TokenID'")
			or die (mysqli_error($connection));
		}
	elseif ($soundfile_format=="wav") {
		#If its a wav file
		$result = mysqli_query($connection, "Update Tokens SET soundfile_wav='$soundfile_name' WHERE TokenID='$TokenID'")
			or die (mysqli_error($connection));
		$new_soundfile_name = $soundfile_name;
		}
	else {
		#If anything else, make a wav file
		exec('sox tmp/' . $random_cookie . '/' . $original_filename . ' tmp/' . $random_cookie . '/' . $fileName_exp[0] . ".wav", $lastline, $retval);
			if ($retval!=0){
				die("<p class=\"error\">There was a problem with SoX...</div>");
				}
		$new_soundfile_name=$fileName_exp[0] . ".wav";

		$result = mysqli_query($connection, "Update Tokens SET soundfile_wav='$new_soundfile_name' WHERE TokenID='$TokenID'")
			or die (mysqli_error($connection));
		}

	#If the sampling rate is not the standard 44.1kHz, convert. Otherwise, there may be problems with the mp3 player.
	if ($soundfile_samplingrate!=44100) {
		if ($soundfile_samplingrate>44100) {
			$to_SamplingRate=44100;
			$nyquist_freq=$to_SamplingRate/2;
			}
		elseif ($soundfile_samplingrate<44100 && $soundfile_samplingrate>22050) {
			$to_SamplingRate=44100;
			$nyquist_freq=$soundfile_samplingrate/2;
			}
		elseif ($soundfile_samplingrate<22050 && $soundfile_samplingrate>11025) {
			$to_SamplingRate=22050;
			$nyquist_freq=$soundfile_samplingrate/2;
			}
		elseif ($soundfile_samplingrate<11025) {
			$to_SamplingRate=11025;
			$nyquist_freq=$soundfile_samplingrate/2;
			}
		else {
			$to_SamplingRate=$soundfile_samplingrate;
			$nyquist_freq=$soundfile_samplingrate/2;
			}
		
		
		exec('sox tmp/' . $random_cookie . '/' . $new_soundfile_name . ' -r ' . $to_SamplingRate . ' tmp/' . $random_cookie . '/' . $fileName_exp[0] . '.1.wav', $lastline, $retval);
			if ($retval!=0) {
				if (!is_file('tmp/' . $random_cookie . '/' . $new_soundfile_name)){
					die("<p class=\"error\">Could not find the file tmp/$random_cookie/$new_soundfile_name</div>");
					}
				if (!is_file('tmp/' . $random_cookie . '/' . $fileName_exp[0] . '.1.wav')){
					die("<p class=\"error\">Could not create the temporary file with SoX.</div>");
					}
				else{
					die("<p class=\"error\">There was an unknown error with SoX...</div>");
					}
				}
		exec('rm tmp/' . $random_cookie . '/' . $new_soundfile_name, $lastline, $retval);
		exec('mv tmp/' . $random_cookie . '/' . $fileName_exp[0] . '.1.wav tmp/' . $random_cookie . '/' . $new_soundfile_name, $lastline, $retval);
		
		$result = mysqli_query($connection, "Update Tokens SET frequency_max='$nyquist_freq' WHERE TokenID='$TokenID'")
			or die (mysqli_error($connection));

		#setcookie("frequency_max", "$nyquist_freq", 0);

		$result = mysqli_query($connection, "Update Tokens SET frequency_min='10' WHERE TokenID='$TokenID'")
			or die (mysqli_error($connection));

		#setcookie("frequency_min", "10", 0);
		
		$result = mysqli_query($connection, "Update Tokens SET soundfile_samplingrate='$to_SamplingRate' WHERE TokenID='$TokenID'")
			or die (mysqli_error($connection));

		#setcookie("soundfile_samplingrate", $to_SamplingRate, 0);
		}

// Relocate back to the first page of the application
	header("Location: ./pumilio.php?Token=$TokenID");
	die();
?>
