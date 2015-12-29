<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");
$force_admin = TRUE;
require("include/check_admin.php");

#Sanitize inputs
$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$d=filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>$app_custom_name - Edit File</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");
?>


<script type="text/javascript">
	$(function() {
		$("#tabs0").tabs();
	});
	</script>

	<script src="js/jquery.validate.js"></script>

	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#EditForm").validate({
			rules: {
				SoundName: {
					required: true
				},
				Date: {
					date: true
				}
			},
			messages: {
				SoundName: "Please enter the name of the file",
				Date: "Please enter a date"
			}
			});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>

<script type="text/javascript">
	$(function() {
		$('#datepicker').datepicker({
			changeMonth: true,
			changeYear: true,
			yearRange: '1960:2011'
		});
	});
	</script>

<?php
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		
			$query = "SELECT *, DATE_FORMAT(Date,'%m/%d/%Y') AS Date, TIME_FORMAT(Time,'%H:%i:%s') AS Time FROM Sounds WHERE SoundID='$SoundID' AND Sounds.SoundStatus!='9'";
			$result=query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
			if ($nrows==0) {
				echo "<div class=\"error\"><img src=\"images/exclamation.png\"> There was an error. That file ID could not be found. Please go back and try again or contact the administrator.</div>";
				}
			else {
				$row = mysqli_fetch_array($result);
				extract($row);
					
				$SiteName=query_one("SELECT SiteName FROM Sites WHERE SiteID='$SiteID'", $connection);
				$SiteLat=query_one("SELECT SiteLat FROM Sites WHERE SiteID='$SiteID'", $connection);
				$SiteLon=query_one("SELECT SiteLon FROM Sites WHERE SiteID='$SiteID'", $connection);
									
				echo "<h2>Edit sound file information</h2>

				<div class=\"row\">
				<div class=\"col-lg-8\">";

				if ($d==1) {
					echo "<p><div class=\"alert alert-success\"><img src=\"images/accept.png\"> File was updated successfully. Return to <a href=\"db_filedetails.php?SoundID=$SoundID\">the sound file</a></div>";
					}

				echo "<div class=\"alert alert-info\" role=\"alert\">
					<p><a href=\"db_filedetails.php?SoundID=$SoundID\"><strong>$SoundName</strong></a>
					<br>Filename: $OriginalFilename";

				$col_name=query_one("SELECT Collections.CollectionName from Collections,Sounds WHERE Collections.ColID=Sounds.ColID AND Sounds.SoundID='$SoundID'", $connection);

				#Source info
				echo "<br>From: <a href=\"db_browse.php?ColID=$ColID\">$col_name</a></div>

				<br><br>
				<form action=\"file_edit2.php\" method=\"POST\" id=\"EditForm\">
					<input name=\"SoundID\" type=\"hidden\" value=\"$SoundID\">

				<label for=\"SoundName\">New name of the soundfile:</label>
					<input name=\"SoundName\" type=\"text\" maxlength=\"160\" value=\"$SoundName\" class=\"form-control\">

				<label for=\"OtherSoundID\">Custom ID of sound:</label>
					<input name=\"OtherSoundID\" type=\"text\" maxlength=\"10\" value=\"$OtherSoundID\" class=\"form-control\">

				<label for=\"Date\">Date (month/day/year):</label>
					<input name=\"Date\" type=\"text\" value=\"$Date\" id=\"datepicker\" class=\"form-control\">

				<label for=\"Time\">Time (hour:minute:second):</label>
					<input name=\"Time\" type=\"text\" value=\"$Time\" class=\"form-control\">

				<br><strong>Site</strong>: <a href=\"browse_site.php?SiteID=$SiteID\" title=\"Browse all the recordings at this site\">$SiteName</a> - <a href=\"viewsite_map.php?SiteID=$SiteID\">Map</a><br>
				
				<label for=\"SiteID\">Change the site:</label>";
			
				$query_s = "SELECT SiteID AS this_SiteID, SiteName AS this_SiteName, SiteLat AS this_SiteLat, SiteLon AS this_SiteLon FROM Sites ORDER BY this_SiteName";
				$result_s = mysqli_query($connection, $query_s)
					or die (mysqli_error($connection));
				$nrows_s = mysqli_num_rows($result_s);
				echo "<select name=\"SiteID\" class=\"form-control\">";
					echo "<option value=\"$SiteID\">$SiteName ($SiteLat/$SiteLon)</option>\n";

				for ($j=0;$j<$nrows_s;$j++) {
					$row_s = mysqli_fetch_array($result_s);
					extract($row_s);
					echo "<option value=\"$this_SiteID\">$this_SiteName ($this_SiteLat/$this_SiteLon)</option>\n";
					}
				echo "</select>";

				echo " &nbsp;&nbsp;<a href=\"#\" onclick=\"window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">Add sites</a><br>";

				$query_e = "SELECT SensorID as SensorID_q, Recorder, Microphone from Sensors ORDER BY SensorID_q";
				$result_e = mysqli_query($connection, $query_e)
					or die (mysqli_error($connection));
				$nrows_e = mysqli_num_rows($result_e);
				echo "<br><label for=\"SensorID\">Sensor used:</label><select name=\"SensorID\" class=\"form-control\">";
					echo "<option value=\"\">Not set</option>\n";

				for ($e=0;$e<$nrows_e;$e++) {
					$row_e = mysqli_fetch_array($result_e);
					extract($row_e);

					if ($SensorID_q==$SensorID){
						echo "<option value=\"$SensorID_q\" SELECTED>$SensorID_q - $Recorder - $Microphone</option>\n";
						}
					else {
						echo "<option value=\"$SensorID_q\">$SensorID_q - $Recorder - $Microphone</option>\n";
						}
					}
				echo "</select><br>";

				echo "
				<label for=\"Notes\">Notes:</label>
					<input name=\"Notes\" type=\"text\" maxlength=\"250\" alue=\"$Notes\" class=\"form-control\">

				<input name=\"where_to\" type=\"hidden\" value=\"$self?SoundID=$SoundID\">
				<br>
				<button type=\"submit\" class=\"btn btn-primary form-control\"> Edit file </button></form>\n";


				echo "<br><p><strong>Technical file details</strong>:
				<ul>
					<li>Database ID: $SoundID
					<li>Duration: $Duration seconds
					<li>File Format: $SoundFormat
					<li>Sampling rate: $SamplingRate Hz
					<li>Bit rate: $BitRate
					<li>Number of channels: $Channels";

					if ($Notes!=""){
						echo "<li>Notes: $Notes";
						}

				echo "
				</ul>";
				echo "</div>
				</div><div class=\"col-lg-4\"></div></div>";

			}


require("include/bottom.php");
?>

</body>
</html>
