<?php

#Turn this script on or off
$allow_flac2wav = FALSE;

#Use ip addresses to limit access? Which IP range?
$filter_by_ip = FALSE;
$ip_from = "1.1.1.1";
$ip_to = "1.1.1.2";


#Allowed IP range of addresses, otherwise you make the system vulnerable to attack and slowdown
# Repeat for multiple ranges
# From http://stackoverflow.com/questions/3163213/redirecting-ip-if-its-between-a-certain-ip-range/3163221#3163221
if ($filter_by_ip){
	$range_start = ip2long($ip_from);
	$range_end   = ip2long($ip_to);
	$ip          = ip2long($_SERVER['REMOTE_ADDR']);
	if ($ip >= $range_start && $ip <= $range_end) {
		$allow_flac2wav = TRUE;
		}
	else {
		$allow_flac2wav = FALSE;
		}

	#localhost
	if ($_SERVER['REMOTE_ADDR'] == "127.0.0.1") {
		$allow_flac2wav = TRUE;
		}
	}


##Start script
if ($allow_flac2wav) {

	require("include/functions.php");

	$config_file = 'config.php';

	if (file_exists($config_file)) {
		require($config_file);
		}
	else {
		header("Location: error.php?e=config");
		die();
		}

	require("include/apply_config.php");

	#Get variables
	$SoundID = filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
	$original = filter_var($_GET["original"], FILTER_SANITIZE_NUMBER_INT);
	$from = filter_var($_GET["from"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	$to = filter_var($_GET["to"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

	if ($SoundID == "") {
		header("HTTP/1.0 404 Not Found");
		die();
		}

	$query = "SELECT * FROM Sounds WHERE SoundID='$SoundID'";
	$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	if ($nrows == 1) {
		$row = mysqli_fetch_array($result);
		extract($row);
		}
	else {
		header("HTTP/1.0 404 Not Found");
		die();
		}

	if ($original == "1"){
		$filename = "sounds/sounds/$ColID/$DirID/$OriginalFilename";
		}
	elseif ($original == ""){
		$filename = "sounds/sounds/$ColID/$DirID/$OriginalFilename";

		if (!file_exists("$filename")) {
			header("HTTP/1.0 404 Not Found");
			die();
			}

		$random_dir=rand(500, 150000);
		mkdir("tmp/" . $random_dir, 0777);
		sleep(2);

		$fileName_exp=explode(".", $OriginalFilename);
		$wav_file = 'tmp/' . $random_dir . '/' . $fileName_exp[0] . '.wav';
	
		exec('flac -d ' . $filename . ' -o ' . $wav_file, $lastline, $retval);
		if ($retval != 0) {
			header("HTTP/1.0 404 Not Found");
			die();
			}

		if ($from!="" && $to!=""){
			$wav_file_cut = 'tmp/' . $random_dir . '/' . $fileName_exp[0] . '.segment.wav';
			$from = floor($SamplingRate * $from);
			$to = floor($SamplingRate * $to);
			exec('sox ' . $wav_file . ' ' . $wav_file_cut . ' trim ' . $from . 's ' . $to . 's', $lastline, $retval);
			$wav_file = $wav_file_cut;
			}
		$filename = $wav_file;
		}

	//From http://elouai.com/force-download.php
	//Updated to automatically get the mime type from http://www.tuxradar.com/practicalphp/15/5/1

	// required for IE, otherwise Content-disposition is ignored
	if(ini_get('zlib.output_compression'))
	  ini_set('zlib.output_compression', 'Off');
	  
	#From http://www.php.net/manual/en/function.finfo-file.php
	$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	$ctype = finfo_file($finfo, $filename);
	finfo_close($finfo);


	header("Pragma: public"); // required
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false); // required for certain browsers 
	header("Content-Type: $ctype");
	// change, added quotes to allow spaces in filenames, by Rajkumar Singh
	header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: ".filesize($filename));
	readfile("$filename");
	die();
}

?>