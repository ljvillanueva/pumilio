<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

session_write_close();
flush(); @ob_flush();

$archive_name=date("YMd_His");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px;">
<h4>Export sound files</h4>

<p>Select a collection or a site below to export all the sound files from that source in a tar 
	or zip file. The system will create a comma-separated value (csv) 
	file with the data from the files in the database. The size in parenthesis 
	is the estimated size of the archive.<br>

<div class="notice">The archiving process may take from a few minutes to a few hours, 
	depending on the number and size of the files, server load, disk speed, 
	free space, and other factors.<br><br> 
	The archives will not be compressed. Sound files do not compress well, 
	so this option is not enabled to save computing power.
	</div>

<?php
$total_no_sounds=query_one("SELECT COUNT(*) FROM Sounds", $connection);

if ($total_no_sounds>0) {
	$query = "SELECT * from Collections ORDER BY CollectionName";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	echo "<form action=\"tar_data.php\" method=\"POST\" id=\"tarform\" name=\"tarform\">
	<input type=\"hidden\" name=\"archivefrom\" value=\"ColID\">
	<strong>Export all files from a collection</strong>: <br>
	<select name=\"ColID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		//How many sounds associated with that source
		$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE ColID='$ColID' AND SoundStatus!='9'", $connection);

		#Get filesize of all the files
		$thisfilesize=0;
		$result_f = mysqli_query($connection, "SELECT OriginalFilename, DirID, FileSize from Sounds WHERE ColID='$ColID'")
			or die (mysqli_error($connection));
		$nrows_f = mysqli_num_rows($result_f);
		for ($f=0;$f<$nrows_f;$f++) {
			$row_f = mysqli_fetch_array($result_f);
			extract($row_f);
			#$thisfilesize=$thisfilesize + filesize("../sounds/sounds/$ColID/$DirID/$OriginalFilename");
			$thisfilesize=$thisfilesize + $FileSize;
			}
		#if ($no_sounds>0)
		$thisfilesize = formatsize($thisfilesize);
		echo "<option value=\"$ColID\">$CollectionName ($thisfilesize)</option>\n";
		unset($thisfilesize);
		}

	echo "</select><br>";

	echo "Archive format: <select name=\"method\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";
	unset($out, $retval);
	exec('zip -v', $out, $retval);
	if ($retval==0) {
		echo "<option>zip</option>";
		}

	unset($out, $retval);
	exec('tar --help', $out, $retval);
	if ($retval==0) {
		echo "<option>tar</option>";
		}

	echo "</select><br>";
	echo "<input type=submit value=\" Package files from this collection \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
		</form>
		<br>
	<hr noshade>";

	$query = "SELECT * from Sites ORDER BY SiteName";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	echo "<form action=\"tar_data.php\" method=\"POST\" id=\"tarform\" name=\"tarform\">
		<input type=\"hidden\" name=\"archivefrom\" value=\"SiteID\">
		<strong>Export all files from a site</strong>: <br><select name=\"SiteID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";

		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			//How many sounds associated with that source
			$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SiteID='$SiteID' AND SoundStatus!='9'", $connection);

			#Get filesize of all the files
			$thisfilesize=0;
			$result_f = mysqli_query($connection, "SELECT DirID, ColID, OriginalFilename, FileSize from Sounds WHERE SiteID='$SiteID' AND Sounds.SoundStatus!='9'")
				or die (mysqli_error($connection));
			$nrows_f = mysqli_num_rows($result_f);
			for ($f=0;$f<$nrows_f;$f++) {
				$row_f = mysqli_fetch_array($result_f);
				extract($row_f);
				#$thisfilesize=$thisfilesize + filesize("../sounds/sounds/$ColID/$DirID/$OriginalFilename");
				$thisfilesize=$thisfilesize + $FileSize;
				}
			#if ($no_sounds>0)
			$thisfilesize = formatsize($thisfilesize);
			echo "<option value=\"$SiteID\">$SiteName ($thisfilesize)</option>\n";
			unset($thisfilesize);
			}
		echo "</select><br>";

		echo "Archive format: <select name=\"method\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";
		unset($out, $retval);
		exec('zip -v', $out, $retval);
		if ($retval==0) {
			echo "<option>zip</option>";
			}

		unset($out, $retval);
		exec('tar --help', $out, $retval);
		if ($retval==0) {
			echo "<option>tar</option>";
			}

		echo "</select><br>";
		echo "<input type=submit value=\" Package files from this site \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
	</form>";
	}
else {
	echo "<div class=\"notice\">There are no sound files in the system.</div>";
	}

#Display free disk space
	$dir_to_check=$absolute_dir . "/tmp";
	$df=disk_free_space($dir_to_check);
	$dfh=formatsize($df);
	echo "<br><br><p>";
	if ($df<5000000000){
		echo "<div class=\"notice\"><strong>Warning</strong>: ";
		}
	else {
		echo "<div class=\"success\">";
		}
	echo "Free disk space available on this server: $dfh</div>";
?>

<br><p><a href="#" onClick="window.close();">Close window</a>

</div>

</body>
</html>
