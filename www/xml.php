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
$use_xml=query_one("SELECT Value from PumilioSettings WHERE Settings='use_xml'", $connection);
if ($use_xml==""){
	$use_xml="0";
	}

#JSON instead of XML?
if (isset($_GET["json"])){
	$json = filter_var($_GET["json"], FILTER_SANITIZE_STRING);
	}
else{
	$json = FALSE;
	}

#Allowed?
if ($use_xml==0) {
	}
else{
	if ($json){
		#JSON Format
		#From http://lostechies.com/seanbiefeld/2011/10/21/simple-xml-to-json-with-php/
		$fileContents= file_get_contents("$app_url/xml.php");
		$fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
		$fileContents = trim(str_replace('"', "'", $fileContents));
		$simpleXml = simplexml_load_string($fileContents);
		$json = json_encode($simpleXml);
		print $json;
		}
	else{
		#XML Format
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
		<pumilio>
		<pumilio_xml_version>1.0</pumilio_xml_version>";
	
		echo "<pumilio_title>$app_custom_name</pumilio_title>
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
		
				$query_s = "SELECT *, DATE_FORMAT(Date, '%d') AS day, DATE_FORMAT(Date, '%c') AS month, DATE_FORMAT(Date, '%Y') AS year, DATE_FORMAT(Time, '%H') AS hour, DATE_FORMAT(Time, '%i') AS minutes, DATE_FORMAT(Time, '%s') AS seconds from Sounds WHERE SiteID='$SiteID'";
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
						<SamplingRate>$SamplingRate</SamplingRate>
						<BitRate>$BitRate</BitRate>
						<Channels>$Channels</Channels>
						<Duration>$Duration</Duration>
						<SoundFormat>$SoundFormat</SoundFormat>
						<SensorID>$SensorID</SensorID>
						<Notes>$Notes</Notes>
						<timestamp>$stamp</timestamp>
						<FileSize>$FileSize</FileSize>
						<FilePath>$app_url/sounds/sounds/$ColID/$DirID/$OriginalFilename</FilePath>
						<AudioPreviewFormat>$AudioPreviewFormat</AudioPreviewFormat>
						<AudioPreviewFilename>$AudioPreviewFilename</AudioPreviewFilename>
						<AudioPreviewFilePath>$app_url/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename</AudioPreviewFilePath>
					</sound>\n";
					}
				echo "</site>\n";
				}
			}
		echo "</pumilio>";
		}
	}

?>
