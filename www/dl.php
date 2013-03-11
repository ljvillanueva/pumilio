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
require("include/check_login.php");

//From http://elouai.com/force-download.php
//Updated to automatically get the mime type from http://www.tuxradar.com/practicalphp/15/5/1

$filename = $_GET['file'];
$SoundID = $_GET['SoundID'];
$from_detail = $_GET['from_detail'];

if ($from_detail == "1" && $SoundID != ""){
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

	$filename = "sounds/sounds/$ColID/$DirID/$OriginalFilename";

	if (!file_exists("$filename")) {
		save_log($connection, $SoundID, "99", "The file sounds/sounds/$ColID/$DirID/$OriginalFilename could not be found.");
		header("HTTP/1.0 404 Not Found");
		die();
		}

	$random_dir=rand(500, 150000);
	mkdir("tmp/" . $random_dir, 0777);
	sleep(2);

	$fileName_exp=explode(".", $OriginalFilename);
	$wav_file = 'tmp/' . $random_dir . '/' . $fileName_exp[0] . '.wav';

	if ($SoundFormat == "flac") {
		#FLAC file
		exec('flac -d ' . $filename . ' -o ' . $wav_file, $lastline, $retval);
		if ($retval!=0) {
			die("<div class=\"error\">There was a problem with the FLAC decoder...</div>");
			save_log($connection, $SoundID, "70", "The file sounds/sounds/$ColID/$DirID/$OriginalFilename could not be opened from flac to wav.\n" . $lastline);
			}
		}
	else {
		#Any other format, use SoX
		exec('sox ' . $filename . ' ' . $wav_file, $lastline, $retval);
		if ($retval!=0) {
			die("<div class=\"error\">There was a problem with SoX...</div>");
				}
		}

	$filename = $wav_file;

	#copy sound to tmp and then let download
	copy($filename, "tmp/" . basename($filename));
	$filename = "tmp/" . basename($filename);
	header("Location: $filename");
	die();
	
	/*
	//From http://elouai.com/force-download.php
	//Updated to automatically get the mime type from http://www.tuxradar.com/practicalphp/15/5/1

	// required for IE, otherwise Content-disposition is ignored
	if(ini_get('zlib.output_compression'))
	  ini_set('zlib.output_compression', 'Off');
	  
	#From http://www.php.net/manual/en/function.finfo-file.php
	$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
	$ctype=finfo_file($finfo, $filename);
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
	*/
	}
	
// addition by Jorg Weske
$file_extension = strtolower(substr(strrchr($filename,"."),1));

if ($filename == "") {
	echo "<html><title>error</title><link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
	<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">
	<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
	<body><div style=\"padding: 10px;\"><p class=\"error\">ERROR: file not specified.</body></html>";
	die();
	}
elseif (!file_exists($filename)){
	echo "<html><title>error</title><link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
	<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">
	<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
	<body><div style=\"padding: 10px;\"><p class=\"error\">ERROR: file not found.</body></html>";
	die();
	}

// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
	ini_set('zlib.output_compression', 'Off');

//Automatically get the mime type
#Deprecated
#$ctype=mime_content_type( $filename );

#From http://www.php.net/manual/en/function.finfo-file.php
$finfo = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
$ctype=finfo_file($finfo, $filename);
finfo_close($finfo);
/*
switch( $file_extension )
{
  case "pdf": $ctype="application/pdf"; break;
  case "zip": $ctype="application/zip"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  case "jpeg":
  case "mp3": $ctype="audio/mpeg"; break;
  case "mp4": $ctype="audio/mp4"; break;
  case "wav": $ctype="audio/x-wav"; break;
  case "au": $ctype="audio/basic"; break;
  case "jpg": $ctype="image/jpg"; break;
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}
*/

if ($file_extension!="png") {
	if ($guests_can_dl || $pumilio_loggedin) {
		}
	else {
		echo "<html><title>Error</title><link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">
		<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		<body><div style=\"padding: 10px;\"><p class=\"error\">ERROR: You have to be logged in to download the file.</body></html>";
		die();
		}
	}

if ($file_extension == "png") {
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
	exit();
	}
else{
	#copy sound to tmp and then let download
	copy($filename, "tmp/" . basename($filename));
	$filename = "tmp/" . basename($filename);
	header("Location: $filename");
	die();
	}

?>
