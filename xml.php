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

#XML head
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>
	<pumilio>
	<pumilio_xml_version>1.2</pumilio_xml_version>";
	if ($app_admin_email==""){
		$app_admin_email="unknown";
		}
	$pumilio_version = trim(file_get_contents($absolute_dir . '/include/version.txt', true));
	echo "<pumilio_title>$app_custom_name</pumilio_title>
	<pumilio_description>$app_custom_text</pumilio_description>
	<pumilio_administrator_email>$app_admin_email</pumilio_administrator_email>
	<pumilio_logo>$app_url/$app_logo</pumilio_logo>
	<pumilio_url>$app_url</pumilio_url>
	<pumilio_version>$pumilio_version</pumilio_version>\n";
	
	
#Check if allowed
if ($use_xml==""){
	$use_xml="0";
	}

#Allowed?
if ($use_xml==0) {
	echo "<pumilio_xml_access>FALSE</pumilio_xml_access>\n";
	}
elseif ($use_xml==1){
	
	if ($xml_access=="0") {
		$login = filter_var($_GET["login"], FILTER_SANITIZE_STRING);
		$login_exp = explode(":", $login);
		if (!authenticateUser($connection, $login_exp[0], $login_exp[1])){
			echo "<pumilio_xml_access>FALSE</pumilio_xml_access>\n";
			echo "</pumilio>";
			die();
			}
		}
	echo "<pumilio_xml_access>TRUE</pumilio_xml_access>\n";
	$type = filter_var($_GET["type"], FILTER_SANITIZE_STRING);

	if ($type == ""){

		$query = "SELECT DISTINCT SiteID from Sounds WHERE Sounds.SoundStatus!='9'";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			echo "<Sites>";
			for ($q=0;$q<$nrows;$q++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$query1 = "SELECT * from Sites WHERE SiteID='$SiteID'";
				$result1 = mysqli_query($connection, $query1)
					or die (mysqli_error($connection));

				$row1 = mysqli_fetch_array($result1);
				extract($row1);

				echo "<Site>
				<SiteID>$SiteID</SiteID>
				<SiteName>$SiteName</SiteName>
				<latitude>$SiteLat</latitude>
				<longitude>$SiteLon</longitude>
				<elevation>$SiteElevation</elevation>
				<notes>$SiteNotes</notes>
				</Site>\n";
				}
			echo "</Sites>";
			}

		$query = "SELECT DISTINCT ColID from Sounds WHERE Sounds.SoundStatus!='9'";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			echo "<Collections>";
			for ($q=0;$q<$nrows;$q++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$query1 = "SELECT * from Collections WHERE ColID='$ColID'";
				$result1 = mysqli_query($connection, $query1)
					or die (mysqli_error($connection));

				$row1 = mysqli_fetch_array($result1);
				extract($row1);

				echo "<collection>
				<ColID>$ColID</ColID>
				<CollectionName>$CollectionName</CollectionName>
				<Author>$Author</Author>
				<FilesSource>$FilesSource</FilesSource>
				<CollectionFullCitation>$CollectionFullCitation</CollectionFullCitation>
				<URL>$MiscURL</URL>
				<Notes>$Notes</Notes>
				</collection>\n";

				}
			echo "</Collections>";
			}
		}
	else{
		if ($type == "col"){
			$ColID = filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
			$query_subset = "AND ColID='$ColID'";
			}
		elseif ($type == "site"){
			$SiteID = filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
			$query_subset = "AND SiteID='$SiteID'";
			}
		elseif ($type == "both"){
			$ColID = filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
			$SiteID = filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
			$query_subset = "AND SiteID='$SiteID' AND ColID='$ColID'";
			}
		elseif ($type == "all"){
			$query_subset = "";
			}

		$query_s = "SELECT *, DATE_FORMAT(Date, '%d') AS day, DATE_FORMAT(Date, '%c') AS month, 
			DATE_FORMAT(Date, '%Y') AS year, DATE_FORMAT(Time, '%H') AS hour, 
			DATE_FORMAT(Time, '%i') AS minutes, DATE_FORMAT(Time, '%s') AS seconds 
			FROM Sounds WHERE SoundStatus!='9' " . $query_subset;
		$result_s = mysqli_query($connection, $query_s)
			or die (mysqli_error($connection));
		$nrows_s = mysqli_num_rows($result_s);

		if ($nrows_s > 0) {
			echo "<Sounds>";
			for ($s=0;$s<$nrows_s;$s++) {
				$row_s = mysqli_fetch_array($result_s);
				extract($row_s);
				echo "<sound>
					<SoundID>$SoundID</SoundID>
					<ColID>$ColID</ColID>
					<SiteID>$SiteID</SiteID>
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
			echo "</Sounds>";
			}
		}
	}

echo "</pumilio>";

?>
