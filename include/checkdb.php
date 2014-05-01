<?php
session_start();

require("functions.php");
require("../config.php");
#require("apply_config.php");

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

$force_admin = TRUE;
require("check_admin.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");


if (isset($_GET["run"])){
	$run = $_GET['run'];
	}
else{
	$run = FALSE;
	}
	
if ($run){

	set_time_limit(0);

	echo "</head>
	<body>

	<div style=\"padding: 10px;\">

	<br><br><br>\n";

	#Check if the file size is in the database
		$query_size="SELECT * FROM Sounds WHERE FileSize='' OR FileSize IS NULL OR FileSize='0'";
		$result_size = mysqli_query($connection, $query_size)
			or die (mysqli_error($connection));
		$nrows_size = mysqli_num_rows($result_size);
		if ($nrows_size>0) {
			for ($s2=0;$s2<$nrows_size;$s2++) {
				$row_size = mysqli_fetch_array($result_size);
				extract($row_size);
				$file_filesize=filesize("../sounds/sounds/$ColID/$DirID/$OriginalFilename");
				$result_size = mysqli_query($connection, "UPDATE Sounds SET FileSize='$file_filesize' WHERE SoundID='$SoundID' LIMIT 1")
					or die (mysqli_error($connection));
				}
			}



	#Check if the MD5 hash is in the database
		$query_md5="SELECT * FROM Sounds WHERE MD5_hash='' OR  MD5_hash IS NULL OR MD5_hash='0'";
		$result_md5 = mysqli_query($connection, $query_md5)
			or die (mysqli_error($connection));
		$nrows_md5 = mysqli_num_rows($result_md5);
		if ($nrows_md5>0) {
			for ($s3=0;$s3<$nrows_md5;$s3++) {
				$row_md5 = mysqli_fetch_array($result_md5);
				extract($row_md5);
				if (is_file("../sounds/sounds/$ColID/$DirID/$OriginalFilename")) {
					$file_md5hash=md5_file("../sounds/sounds/$ColID/$DirID/$OriginalFilename");
					$result_md5 = mysqli_query($connection, "UPDATE Sounds Set MD5_hash='$file_md5hash' WHERE SoundID='$SoundID'")
						or die (mysqli_error($connection));
					}
				}
			}
		

	#Check if the sampling rate is in the database
		$query_samp_rate="SELECT SoundID, ColID, OriginalFilename FROM Sounds WHERE SamplingRate='0' OR SamplingRate IS NULL 
				OR Channels='0' OR Channels IS NULL OR Duration='0' OR Duration IS NULL OR
				SoundFormat IS NULL";
		$result_samp_rate = mysqli_query($connection, $query_samp_rate)
			or die (mysqli_error($connection));
		$nrows_samp_rate = mysqli_num_rows($result_samp_rate);
		if ($nrows_samp_rate>0) {
			for ($s4=0;$s4<$nrows_samp_rate;$s4++) {
				$row_samp = mysqli_fetch_array($result_samp_rate);
				extract($row_samp);
				if (is_file("../sounds/sounds/$ColID/$DirID/$OriginalFilename")) {
					exec('python soundcheck.py ../sounds/sounds/' . $ColID . '/' . $DirID . '/' . $OriginalFilename, $lastline, $retval);
					if ($retval==0) {
						$file_info=$lastline[0];
						$file_info = explode(",", $file_info);
						$sampling_rate=$file_info[0];
						$no_channels=$file_info[1];
						$file_format=$file_info[2];
						$file_duration=$file_info[3];
						$file_bits=$file_info[4];

						$query_file1 = "UPDATE Sounds SET 
								SamplingRate='$sampling_rate', Channels='$no_channels', 
								Duration='$file_duration', SoundFormat='$file_format', BitRate='$file_bits'
								WHERE SoundID='$SoundID' LIMIT 1";
						$result_file1 = mysqli_query($connection, $query_file1)
							or die (mysqli_error($connection));
						unset($lastline);
						unset($query_file);
						unset($retval);
						unset($file_info);
						}
					}
				else {
					die("<div class=\"error\">Could not find file $ColID/$OriginalFilename");
					}
				}
			}


	#Change engine to MyISAM to keep all the same
		$to = 'MyISAM';
		$from = 'INNODB';

		$query="SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$database' AND ENGINE = 'InnoDB'";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				query_one("ALTER TABLE $TABLE_NAME ENGINE = MyISAM", $connection);
				}
			}
	
	
	#Change collation
		$query="SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '$database' AND TABLE_COLLATION != 'utf8_unicode_ci'";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
		if ($nrows>0) {
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				query_one("ALTER TABLE $TABLE_NAME DEFAULT CHARSET=utf8 COLLATE='utf8_unicode_ci'", $connection);
				}
			}
	
	#Change OtherSoundID to null
		$query="UPDATE Sounds SET OtherSoundID = NULL WHERE OtherSoundID = '0'";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));

	
	
	#Optimize tables
		$query_opt="OPTIMIZE TABLE CheckAuxfiles, Collections, Cookies, Kml, ProcessLog, PumilioLog, PumilioSettings, QualityFlags, Queue, QueueJobs, SampleMembers, Samples, Scripts, Sensors, Sites, SitesPhotos, Sounds, SoundsImages, SoundsMarks, Tags, Tokens, Users, WeatherData, WeatherSites";
		$result_opt = mysqli_query($connection, $query_opt)
			or die (mysqli_error($connection));

	echo "<h4><div class=\"success\"><img src=\"../images/accept.png\"> Database fields were updated</div></h4>
	<br><br><br>
	<p><a href=\"#\" onClick=\"window.close();\">Close window</a>\n";

	}
else{
	echo "<meta http-equiv=\"refresh\" content=\"1;url=checkdb.php?run=TRUE\">

	</head>
	<body>

	<div style=\"padding: 10px;\">

		<br><br><br>
		<h3>Working... 
		<br>Please wait...
		<img src=\"../images/wait20trans.gif\">
		</h3>

		<br><br><br>
	<br><p><a href=\"#\" onClick=\"window.close();\">Cancel and close window</a>";
	}
	
?>

</div>

</body>
</html>
