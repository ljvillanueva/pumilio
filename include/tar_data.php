<?php
session_start();

set_time_limit(0);

require("functions.php");
require("../config.php");
require("apply_config_include.php");
require("version.php");

$force_admin = TRUE;
require("check_admin.php");

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$method=filter_var($_POST["method"], FILTER_SANITIZE_STRING);
$archivefrom=filter_var($_POST["archivefrom"], FILTER_SANITIZE_STRING);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>Pumilio - Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");

if (isset($_GET["run"])){
	$run = filter_var($_GET["run"], FILTER_SANITIZE_STRING);
	}
else{
	$run = FALSE;
	}


if ($run){
	$ColID = filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
	$SiteID = filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
	$method = filter_var($_GET["method"], FILTER_SANITIZE_STRING);
	$archivefrom = filter_var($_GET["archivefrom"], FILTER_SANITIZE_STRING);

	$archive_name = date("YMd_His");

	session_write_close();
	flush(); @ob_flush();

	echo "</head>
	<body>

	<div style=\"padding: 10px;\">

	<h3>Download file</h3>
	<p>\n";

	if ($archivefrom == "ColID") {
		#Set a random dir
		$random_dir = mt_rand();
		$random_dir2 = mt_rand();
		mkdir("$absolute_dir/tmp/$random_dir", 0777);
		mkdir("$absolute_dir/tmp/$random_dir/$random_dir2", 0777);
		$target_path = "$absolute_dir/tmp/$random_dir/";

		chdir("$absolute_dir/tmp/$random_dir/$random_dir2/");
	
		#Write a csv file
		$fp = fopen('data.csv', 'w');
		fwrite($fp, "SoundID,Filename,Date,Time,SamplingRate,BitRate,Channels,Duration,FileFormat,SiteID,SiteName,Latitude,Longitude,SoundNotes,ColID,CollectionName,CollectionNotes,Sensor_Recorder,Sensor_Microphone,Sensor_Notes,QualityFlagID,QualityFlag\n");
		fclose($fp);
	
		$result_f = mysqli_query($connection, "SELECT SoundID, ColID, DirID, OriginalFilename FROM Sounds
			WHERE ColID='$ColID' AND SoundStatus!='9'")
		or die (mysqli_error($connection));
		$nrows_f = mysqli_num_rows($result_f);
		for ($f = 0; $f < $nrows_f; $f++) {
			$row_f = mysqli_fetch_array($result_f);
			extract($row_f);
			if (!copy("../../../sounds/sounds/$ColID/$DirID/$OriginalFilename", $OriginalFilename)) {
				die("<div class=\"error\"><img src=\"../images/exclamation.png\"> Failed to copy $OriginalFilename.</div>\n");
				}

			$result_i = mysqli_query($connection, "SELECT Sounds.SoundID,Sounds.OriginalFilename, Sounds.Date,
					Sounds.Time, Sounds.SamplingRate, Sounds.BitRate, Sounds.Channels, 
					Sounds.Duration, Sounds.SoundFormat, Sounds.SiteID, Sites.SiteName, 
					Sites.SiteLat, Sites.SiteLon, Sounds.Notes AS SoundNotes, Collections.ColID, 
					Collections.CollectionName, Collections.Notes AS ColNotes
					FROM Sounds, Sites, Collections
					WHERE Sounds.ColID=Collections.ColID AND
					Sounds.SiteID=Sites.SiteID AND
					Sounds.SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$row_i = mysqli_fetch_array($result_i);
			extract($row_i);

			$result_j = mysqli_query($connection, "SELECT Sounds.SoundID, Sensors.Recorder AS Sensors_Recorder, Sensors.Microphone AS Sensors_Microphone, Sensors.Notes AS Sensors_Notes
					FROM Sounds, Sensors
					WHERE Sounds.SensorID=Sensors.SensorID AND
					Sounds.SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$row_j = mysqli_fetch_array($result_j);
			extract($row_j);
		
			$result_k = mysqli_query($connection, "SELECT Sounds.SoundID, QualityFlags.QualityFlagID AS QualityFlagID,
					QualityFlags.QualityFlag AS Sound_QualityFlag
					FROM Sounds, QualityFlags
					WHERE Sounds.QualityFlagID=QualityFlags.QualityFlagID AND
					Sounds.SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$row_k = mysqli_fetch_array($result_k);
			extract($row_k);

			#Append to the csv file
			$fp = fopen('data.csv', 'a');
			fwrite($fp, "$SoundID,\"$OriginalFilename\",$Date,$Time,$SamplingRate,$BitRate,$Channels,$Duration,\"$SoundFormat\",$SiteID,\"$SiteName\",$SiteLat,$SiteLon,\"$SoundNotes\",$ColID,\"$CollectionName\",\"$ColNotes\",\"$Sensor_Recorder\",\"$Sensor_Microphone\",\"$Sensor_Notes\",$QualityFlagID,\"$Sound_QualityFlag\"\n");
			fclose($fp);
		
			}

		#Add readme file
		$readmefile = fopen('readme.txt', 'a');
		fwrite($readmefile, "Data exported from Pumilio v. $website_version");
		fwrite($readmefile, " from Collection: $CollectionName (ColID: $ColID)");
		fclose($readmefile);

		if ($method == "zip") {
			exec("zip -0 " . $target_path . $archive_name . ".zip *", $out, $retval);
			if ($retval == 0) {
				$file_size = formatsize(filesize($target_path . $archive_name . ".zip"));
				echo "<div class=\"success\"><img src=\"../images/accept.png\"> The archive was created successfully.
					<a href=\"../tmp/" . $random_dir . "/" . $archive_name . ".zip\">Download the file</a> ($file_size).</div>";
				}
			}
		elseif ($method == "tar") {
			exec("tar -cvf " . $target_path . $archive_name . ".tar *", $out, $retval);
			if ($retval == 0) {
				$file_size = formatsize(filesize($target_path . $archive_name . ".tar"));
				echo "<div class=\"success\"><img src=\"../images/accept.png\"> The archive was created successfully.
					<a href=\"../tmp/" . $random_dir . "/" . $archive_name . ".tar\">Download the file</a> ($file_size).</div>";
				}
			}
		}
	elseif ($archivefrom == "SiteID") {
	
		#Set a random dir
		$random_dir = mt_rand();
		$random_dir2 = mt_rand();
		mkdir("$absolute_dir/tmp/$random_dir", 0777);
		mkdir("$absolute_dir/tmp/$random_dir/$random_dir2", 0777);
		$target_path = "$absolute_dir/tmp/$random_dir/";

		chdir("$absolute_dir/tmp/$random_dir/$random_dir2/");
	
		#Write a csv file
		$fp = fopen('data.csv', 'w');
		fwrite($fp, "SoundID,Filename,Date,Time,SamplingRate,BitRate,Channels,Duration,FileFormat,SiteID,SiteName,Latitude,Longitude,SoundNotes,ColID,CollectionName,CollectionNotes,Sensor_Recorder,Sensor_Microphone,Sensor_Notes,QualityFlagID,QualityFlag\n");
		fclose($fp);
	
		$result_f = mysqli_query($connection, "SELECT SoundID, ColID, DirID, OriginalFilename from Sounds 
				WHERE SiteID='$SiteID' AND SoundStatus!='9'")
			or die (mysqli_error($connection));
		$nrows_f = mysqli_num_rows($result_f);
		for ($f = 0; $f < $nrows_f; $f++) {
			$row_f = mysqli_fetch_array($result_f);
			extract($row_f);
		
			if (!copy("../../../sounds/sounds/$ColID/$DirID/$OriginalFilename", $OriginalFilename)) {
				die("<div class=\"error\"><img src=\"../images/exclamation.png\"> Failed to copy $OriginalFilename.</div>\n");
				}
			
			$result_i = mysqli_query($connection, "SELECT Sounds.SoundID,Sounds.OriginalFilename, Sounds.Date,
					Sounds.Time, Sounds.SamplingRate, Sounds.BitRate, Sounds.Channels, 
					Sounds.Duration, Sounds.SoundFormat, Sounds.SiteID, Sites.SiteName, 
					Sites.SiteLat, Sites.SiteLon, Sounds.Notes AS SoundNotes, Collections.ColID, 
					Collections.CollectionName, Collections.Notes AS ColNotes
					FROM Sounds, Sites, Collections
					WHERE Sounds.ColID=Collections.ColID AND
					Sounds.SiteID=Sites.SiteID AND
					Sounds.SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$row_i = mysqli_fetch_array($result_i);
			extract($row_i);

			$result_j = mysqli_query($connection, "SELECT Sounds.SoundID, Sensors.Recorder AS Sensors_Recorder, Sensors.Microphone AS Sensors_Microphone, Sensors.Notes AS Sensors_Notes
					FROM Sounds, Sensors
					WHERE Sounds.SensorID=Sensors.SensorID AND
					Sounds.SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$row_j = mysqli_fetch_array($result_j);
			extract($row_j);
		
		
			$result_k = mysqli_query($connection, "SELECT Sounds.SoundID, QualityFlags.QualityFlagID AS QualityFlagID,
					QualityFlags.QualityFlag AS Sound_QualityFlag
					FROM Sounds, QualityFlags
					WHERE Sounds.QualityFlagID=QualityFlags.QualityFlagID AND
					Sounds.SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$row_k = mysqli_fetch_array($result_k);
			extract($row_k);

			#Append to the csv file
			$fp = fopen('data.csv', 'a');
		
			fwrite($fp, "$SoundID,\"$OriginalFilename\",$Date,$Time,$SamplingRate,$BitRate,$Channels,$Duration,\"$SoundFormat\",$SiteID,\"$SiteName\",$SiteLat,$SiteLon,\"$SoundNotes\",$ColID,\"$CollectionName\",\"$ColNotes\",\"$Sensor_Recorder\",\"$Sensor_Microphone\",\"$Sensor_Notes\",$QualityFlagID,\"$Sound_QualityFlag\"\n");
			fclose($fp);
		
			}
	
		#Add readme file
		$readmefile = fopen('readme.txt', 'a');
		fwrite($readmefile, "Data exported from Pumilio v. $website_version");
		fwrite($readmefile, " from Site: $SiteName (SiteID: $SiteID)");
		fclose($readmefile);
		
		if ($method == "zip") {
			exec("zip -0 " . $target_path . $archive_name . ".zip *", $out, $retval);
			if ($retval == 0) {
				$file_size = formatsize(filesize($target_path . $archive_name . ".zip"));
				echo "<div class=\"success\"><img src=\"../images/accept.png\"> The archive was created successfully.
					<a href=\"../tmp/" . $random_dir . "/" . $archive_name . ".zip\">Download the file</a> ($file_size).</div>";
				}
			}
		elseif ($method == "tar") {
			exec("tar -cvf " . $target_path . $archive_name . ".tar *", $out, $retval);
			if ($retval == 0) {
				$file_size = formatsize(filesize($target_path . $archive_name . ".tar"));
				echo "<div class=\"success\"><img src=\"../images/accept.png\"> The archive was created successfully.
					<a href=\"../tmp/" . $random_dir . "/" . $archive_name . ".tar\">Download the file</a> ($file_size).</div>";
				}
			}
		flush();
		delTree("$absolute_dir/tmp/$random_dir/$random_dir2");
		}
		
	chdir($absolute_dir);

	echo "<br>
		
	<p><a href=\"exportsounds.php\">Export another set</a>
	<p><a href=\"#\" onClick=\"window.close();\">Close window</a>\n";

	}
else{
	echo "<meta http-equiv=\"refresh\" content=\"1;url=tar_data.php?run=TRUE&ColID=$ColID&SiteID=$SiteID&method=$method&archivefrom=$archivefrom\">

	</head>
	<body>

	<div style=\"padding: 10px; text-align:center;\">

	<br><br><br><br><br>
	<h3>Working... 
	<br>Please wait...
	<img src=\"../images/wait20trans.gif\">
	</h3>

	<br><br><br><br><br>
	<br><p><a href=\"#\" onClick=\"window.close();\">Cancel and close window</a>";
	}

?>

</div>

</body>
</html>