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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Edit File</title>";

require("include/get_css.php");
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
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			&nbsp;
		</div>

			<?php
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
									
				echo "<div class=\"span-24 last\">			
					<h3>Edit sound file information</h3>";

				if ($d==1) {
					echo "<p><div class=\"success\"><img src=\"images/accept.png\"> File was updated successfully. Return to <a href=\"db_filedetails.php?SoundID=$SoundID\">the sound file</a></div>";
					}

				echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID\">$SoundName</a>
				<br>Filename: $OriginalFilename";

				$col_name=query_one("SELECT Collections.CollectionName from Collections,Sounds WHERE Collections.ColID=Sounds.ColID AND Sounds.SoundID='$SoundID'", $connection);

				#Source info
				echo "<br>From: <a href=\"db_browse.php?ColID=$ColID\">$col_name</a>
				<p><form action=\"file_edit2.php\" method=\"POST\" id=\"EditForm\">
					<input name=\"SoundID\" type=\"hidden\" value=\"$SoundID\">

				New name of the sound: <br><input name=\"SoundName\" type=\"text\" maxlength=\"160\" size=\"60\" value=\"$SoundName\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				Custom ID of sound: <br><input name=\"OtherSoundID\" type=\"text\" maxlength=\"10\" value=\"$OtherSoundID\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				Date: <br><input name=\"Date\" type=\"text\" size=\"10\" value=\"$Date\" id=\"datepicker\" class=\"fg-button ui-state-default ui-corner-all formedge\"> (month/day/year)<br>
				Time: <br><input name=\"Time\" type=\"text\" size=\"10\" value=\"$Time\" class=\"fg-button ui-state-default ui-corner-all formedge\"> (hour:minute:second)<br>
				Site: <a href=\"browse_site.php?SiteID=$SiteID\" title=\"Browse all the recordings at this site\">$SiteName</a> - <a href=\"viewsite_map.php?SiteID=$SiteID\">Map</a><br>
				Change the site: <br>";
			
				$query_s = "SELECT SiteID AS this_SiteID, SiteName AS this_SiteName, SiteLat AS this_SiteLat, SiteLon AS this_SiteLon FROM Sites ORDER BY this_SiteName";
				$result_s = mysqli_query($connection, $query_s)
					or die (mysqli_error($connection));
				$nrows_s = mysqli_num_rows($result_s);
				echo "<select name=\"SiteID\" class=\"ui-state-default ui-corner-all formedge\">";
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
				echo "Sensor used: <br><select name=\"SensorID\" class=\"ui-state-default ui-corner-all formedge\">";
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
				Notes: <br><input name=\"Notes\" type=\"text\" maxlength=\"250\" size=\"60\" value=\"$Notes\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				<input name=\"where_to\" type=\"hidden\" value=\"$self?SoundID=$SoundID\">
				<input type=\"submit\" value=\" Edit file \"  class=\"fg-button ui-state-default ui-corner-all\"></form>\n";


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
				echo "</div>";
				}

			?>

		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
