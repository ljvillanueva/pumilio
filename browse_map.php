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

if(isset($_GET["date_to_browse"])){
	$date_to_browse = filter_var($_GET["date_to_browse"], FILTER_SANITIZE_STRING);
	}
else{
	$date_to_browse = "";
	}


if(isset($_GET["time_to_browse"])){
	$time_to_browse = filter_var($_GET["time_to_browse"], FILTER_SANITIZE_STRING);
	}
else{
	$time_to_browse = "";	
	}

if(isset($_GET["usekml"])){
	$usekml = filter_var($_GET["usekml"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$usekml = FALSE;
	}

if(isset($_GET["nokml"])){
	$nokml = filter_var($_GET["nokml"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$nokml = FALSE;
	}

$this_page_title = "Browse Map";

#If user is not logged in, add check for QF
	if ($pumilio_loggedin == FALSE) {
		$qf_check = "AND Sounds.QualityFlagID>='$default_qf'";
		}
	else {
		$qf_check = "";
		}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - $this_page_title</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

$no_res = 0;
$error_msg = "";

#Get points from the database
$query = "SELECT * FROM Sites WHERE SiteLat IS NOT NULL AND SiteLon IS NOT NULL ORDER BY SiteName";
$result = query_several($query, $connection);
$nrows = mysqli_num_rows($result);

if ($nrows>0) {
	$map_div_message = "Your browser does not have JavaScript enabled, which is required to proceed. Please enable JavaScript or contact your system administrator for help.";
	}
else {
	$error_msg = "There are no sound files with location data.";
	}

if ($nrows > 0) {

$sites_rows = array();
$sites_bounds = array();

require("include/browse_map_head.php");

}

$kml_count = query_one("SELECT COUNT(*) FROM Kml", $connection);

if ($kml_count > 0){
	for ($k = 0; $k < $kml_count; $k++) {
		echo "<script type=\"text/javascript\">
			$(document).ready(function() {
				    $( \"#kmldialog$k\" ).dialog({ autoOpen: false });
				});
			</script>
			
			<script>
			$(document).ready(function() {
				$(\"#kmlnotelink$k\").click(function() {
				    $( \"#kmldialog$k\" ).dialog('open');
				});
			    });
			</script>\n";
		}
	}
	
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
	
#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	

?>

</head>
<body onload="initialize()" onunload="GUnload()">

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-14">
			<?php
			echo "<h2>$this_page_title</h2>";
			?>
		</div>
		<div class="span-10 last">
			<?php
			if ($no_res > 0) {
				$query_dates = "SELECT DISTINCT DATE_FORMAT(Sounds.Date,'%d-%b-%Y') AS Date_f, Sounds.Date FROM Sounds, Sites 
					WHERE Sounds.Date IS NOT NULL AND Sites.SiteLat IS NOT NULL AND Sites.SiteLon IS NOT NULL 
					AND Sounds.SiteID=Sites.SiteID AND Sounds.SoundStatus!='9' $qf_check ORDER BY Sounds.Date";
				$result_dates = query_several($query_dates, $connection);
				$nrows_dates = mysqli_num_rows($result_dates);
				if ($nrows_dates > 0) {
				
					if ($special_wrapper == TRUE){
						echo "<form action=\"$wrapper\" method=\"GET\">Filter by date: 
						<input type=\"hidden\" name=\"page\" value=\"browse_map\">";
						}
					else {
						echo "<form action=\"browse_map.php\" method=\"GET\">Filter by date: ";
						}
				
					echo "<select name=\"date_to_browse\" class=\"ui-state-default ui-corner-all\">
						<option value=\"\">All dates</option>";
					
					for ($d = 0; $d < $nrows_dates; $d++) {
						$row_dates = mysqli_fetch_array($result_dates);
						extract($row_dates);
						if ($date_to_browse == $Date) {
							echo "\n<option value=\"$Date\" SELECTED>$Date_f</option>";
							}
						else {
							echo "\n<option value=\"$Date\">$Date_f</option>";
							}
						}
					echo "</select> ";

				if ($date_to_browse != "") {
					$query_times = "SELECT DISTINCT DATE_FORMAT(Sounds.Time,'%h:%i:%s %p') AS Time_f, Sounds.Time
							FROM Sounds,Sites 
							WHERE Sounds.Date='$date_to_browse' AND Sites.SiteLat IS NOT NULL 
							AND Sites.SiteLon IS NOT NULL AND Sounds.SiteID=Sites.SiteID 
							AND Sounds.SoundStatus!='9' $qf_check
							ORDER BY Time";
					$result_times = query_several($query_times, $connection);
					$nrows_times = mysqli_num_rows($result_times);
	
					echo "<select name=\"time_to_browse\" class=\"ui-state-default ui-corner-all\">
						<option value=\"\">All times</option>";
					
					for ($t = 0; $t < $nrows_times; $t++) {
						$row_times = mysqli_fetch_array($result_times);
						extract($row_times);
						if ($time_to_browse == $Time) {
							echo "\n<option value=\"$Time\" SELECTED>$Time_f</option>";
							}
						else {
							echo "\n<option value=\"$Time\">$Time_f</option>";
							}
						}
					echo "</select>";

					}

					echo "<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
					</form>";
					}
				}
			?>
		</div>
		<div class="span-24 last">
			<?php

			if ($no_res == 0) {
				if ($time_to_browse != "" && $date_to_browse != "") {
					$Date_f = query_one("SELECT DATE_FORMAT('$date_to_browse', '%d-%b-%Y')", $connection);
					echo "<div class=\"notice\"><img src=\"images/error.png\"> No results were found for that date and time combination. Return to the <a href=\"browse_map.php\">default map view</a> or see results only for <a href=\"browse_map.php?date_to_browse=$date_to_browse\">$Date_f</a>.</div>";
					}
				elseif ($time_to_browse == "" && $date_to_browse != "") {
					$Date_f = query_one("SELECT DATE_FORMAT('$date_to_browse', '%d-%b-%Y')", $connection);
					echo "<div class=\"notice\"><img src=\"images/error.png\"> No results were found for that date. Return to the <a href=\"browse_map.php\">default map view</a>.</div>";
					}
				else {
					echo "<div class=\"notice\"><img src=\"images/error.png\"> No results were found. Return to the <a href=\"browse_map.php\">default map view</a>.</div>";
					}
				}
			else {
				echo "<div id=\"map_canvas\" style=\"width: 940px; height: 500px\">$map_div_message</div>\n
					<p>There are $no_res sites with soundfiles. ";

				if ($no_res > 1){
					echo "Some markers may be hidden behind others. Zoom in to see all the sites.";
					}

				echo "</p>
					<p>\n";

				#KML Menu
				$no_kml = query_one("SELECT COUNT(*) FROM Kml", $connection);
				if ($no_kml > 0) {
					echo "Other layers to display:";
					
					if ($special_wrapper == TRUE){
						echo "<form action=\"$wrapper\" method=\"GET\">
						<input type=\"hidden\" name=\"page\" value=\"browse_map\">";
						}
					else {
						echo "<form action=\"browse_map.php\" method=\"GET\">";
						}

					echo "
					<input type=\"hidden\" name=\"date_to_browse\" value=\"$date_to_browse\">
					<input type=\"hidden\" name=\"time_to_browse\" value=\"$time_to_browse\">
					<input type=\"hidden\" name=\"usekml\" value=\"1\">";

					$query_kml = "SELECT * FROM Kml ORDER BY KmlName";
					$result_kml = query_several($query_kml, $connection);
					$nrows_kml = mysqli_num_rows($result_kml);

					for ($k = 0; $k < $nrows_kml; $k++) {
						$row_kml = mysqli_fetch_array($result_kml);
						extract($row_kml);

						echo "<div id=\"kmldialog$k\" title=\"Notes of KML layer\">
							<p>Notes: ";
							if ($KmlNotes == ""){
								echo "none";
								}
							else {
								echo "$KmlNotes;";
								}
						echo "</p>
						</div>";

						if (!isset($kml_default)){
							$kml_default = 0;
							}
						
						if ($kml_default == 1 && $KmlDefault == 1) {
							echo "<input type=\"checkbox\" name=\"kml$k\" value=\"$KmlID\" checked>$KmlName (<a id=\"kmlnotelink$k\" href=\"javascript:void(0);\">notes</a>)<br>\n";
							}
						if ($KmlDefault == 2) {
							echo "$KmlName (<a id=\"kmlnotelink$k\" href=\"javascript:void(0);\">notes</a>)<br>\n";
							}
						else {
							echo "<input type=\"checkbox\" name=\"kml$k\" value=\"$KmlID\">$KmlName (<a id=\"kmlnotelink$k\" href=\"javascript:void(0);\">notes</a>)<br>\n";
							}
						}

					echo "<input type=\"hidden\" name=\"nokml\" value=\"$nrows_kml\">
						<p>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value=\" Update layers \" class=\"fg-button ui-state-default ui-corner-all\">
					</form>";
					}
				}
			?>
		<br>
		</div>
		<div class="span-24 last">		

		<?php
			if (count($sites_rows) > 0) {
				echo "<p>";
				if (isset($date_to_browse) && $date_to_browse!=""){
					if ($special_wrapper == TRUE){
						echo "<form action=\"$wrapper\" method=\"GET\">
						<input type=\"hidden\" name=\"page\" value=\"browse_site_date\">
						<input type=\"hidden\" name=\"Date\" value=\"$date_to_browse\">";
						}
					else {
						echo "<form action=\"browse_site_date.php\" method=\"GET\">
						<input type=\"hidden\" name=\"Date\" value=\"$date_to_browse\">";
						}
					}
				else {
					if ($special_wrapper == TRUE){
						echo "<form action=\"$wrapper\" method=\"GET\">
						<input type=\"hidden\" name=\"page\" value=\"browse_site\">";
						}
					else {
						echo "<form action=\"browse_site.php\" method=\"GET\">";
						}
					}
				
				echo "Select a particular site:<br>";
				echo "<select name=\"SiteID\" class=\"ui-state-default ui-corner-all\">";
					for ($p = 0; $p < count($sites_rows); $p++) {
						echo $sites_rows[$p];
						}
					echo "</select> 
					<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
					</form><br>";
				}
			else {
				echo "&nbsp;";
				}
		?>

		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>
		</div>
	</div>

</body>
</html>