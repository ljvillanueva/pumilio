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

require("include/apply_config_xml.php");

#Check if allowed
$use_chorus=query_one("SELECT Value from PumilioSettings WHERE Settings='use_chorus'", $connection);
if ($use_chorus==""){
	$use_chorus="0";
	}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
	<pumilio>
	<pumilio_xml_version>1.0</pumilio_xml_version>\n";
	
if ($use_chorus==0) {
	echo "<pumilio_chorus>FALSE</pumilio_chorus>\n";
	}
else {
	echo "<pumilio_chorus>TRUE</pumilio_chorus>
	<pumilio_title>$app_custom_name</pumilio_title>
	<pumilio_description>$app_custom_text</pumilio_description>
	<pumilio_administrator_email>$app_admin_email</pumilio_administrator_email>
	<pumilio_logo>$app_url/$app_logo</pumilio_logo>
	<pumilio_url>$app_url</pumilio_url>\n";

	$query = "SELECT DISTINCT SiteID from Sounds WHERE Sounds.SoundStatus='0'";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	if ($nrows>0) {
		for ($q=0;$q<$nrows;$q++) {
			$row = mysqli_fetch_array($result);
			extract($row);

			$query1 = "SELECT * from Sites WHERE SiteID='$SiteID'";
			$result1 = mysqli_query($connection, $query1)
				or die (mysqli_error($connection));

			$row1 = mysqli_fetch_array($result1);
			extract($row1);

			echo "<site>
			<SiteID>$SiteID</SiteID>
			<SiteName>$SiteName</SiteName>
			<latitude>$SiteLat</latitude>
			<longitude>$SiteLon</longitude>
			<elevation>$SiteElevation</elevation>
			<notes>$SiteNotes</notes>\n";
		
			$query_s = "SELECT *, DATE_FORMAT(Date, '%d') AS day, DATE_FORMAT(Date, '%c') AS month, 
					DATE_FORMAT(Date, '%Y') AS year, DATE_FORMAT(Time, '%H') AS hour, 
					DATE_FORMAT(Time, '%i') AS minutes, DATE_FORMAT(Time, '%s') AS seconds 
					FROM Sounds WHERE SiteID='$SiteID'";
			$result_s = mysqli_query($connection, $query_s)
				or die (mysqli_error($connection));
			$nrows_s = mysqli_num_rows($result_s);

			for ($s=0;$s<$nrows_s;$s++) {
				$row_s = mysqli_fetch_array($result_s);
				extract($row_s);
				echo "<sound>
					<SoundID>$SoundID</SoundID>
					<OriginalFilename>$OriginalFilename</OriginalFilename>
					<SoundName>$SoundName</SoundName>
					<day>$day</day>
					<month>$month</month>
					<year>$year</year>
					<hour>$hour</hour>
					<minutes>$minutes</minutes>
					<seconds>$seconds</seconds>
				</sound>\n";
				}
			echo "</site>\n";
			}
		}
	}
echo "</pumilio>";
?>
