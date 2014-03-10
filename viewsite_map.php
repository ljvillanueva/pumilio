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

require("include/check_login.php");

$date_to_browse=filter_var($_GET["date_to_browse"], FILTER_SANITIZE_STRING);
$usekml=filter_var($_GET["usekml"], FILTER_SANITIZE_NUMBER_INT);
$nokml=filter_var($_GET["nokml"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);

$this_page_title="Browse Map";

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - $this_page_title</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

#Get points from the database
	$query = "SELECT * FROM Sites WHERE SiteID='$SiteID'";

	$result=query_several($query, $connection);
	$nrows = mysqli_num_rows($result);
	$row = mysqli_fetch_array($result);
	extract($row);

require("include/viewsite_map_head.php");
	
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
		<div class="span-12">
			<?php
			echo "<h2>$this_page_title</h2>";
			?>
		</div>
		<div class="span-12 last">
			<?php
				$query_dates = "SELECT DISTINCT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f, Date FROM Sounds WHERE SiteID=$SiteID AND Date IS NOT NULL ORDER BY Date";
				$result_dates=query_several($query_dates, $connection);
				$nrows_dates = mysqli_num_rows($result_dates);
				if ($nrows_dates>0) {
					echo "<form action=\"viewsite_map.php\" method=\"GET\">Filter by date: 
						<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
						<select name=\"date_to_browse\" class=\"ui-state-default ui-corner-all\">
						<option value=\"\">All dates</option>";
					
					for ($d=0;$d<$nrows_dates;$d++) {
						$row_dates = mysqli_fetch_array($result_dates);
						extract($row_dates);
						if ($date_to_browse==$Date){
							echo "\n<option value=\"$Date\" SELECTED>$Date_f</option>";
							}
						else{
							echo "\n<option value=\"$Date\">$Date_f</option>";
							}
						}
					echo "</select>
					<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
					</form>";
					}
			?>
		</div>
		<div class="span-24 last">
			<?php
			echo "<div id=\"map_canvas\" style=\"width: 940px; height: 500px\">$map_div_message</div>\n";

			#KML Menu
			$no_kml=query_one("SELECT COUNT(*) FROM Kml", $connection);
			if ($no_kml>0) {
				echo "<br><p>Other layers to display:
				<form action=\"viewsite_map.php\" method=\"GET\">
					<input type=\"hidden\" name=\"date_to_browse\" value=\"$date_to_browse\">
					<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
					<input type=\"hidden\" name=\"usekml\" value=\"1\">";

				$query_kml = "SELECT * FROM Kml ORDER BY KmlName";
				$result_kml=query_several($query_kml, $connection);
				$nrows_kml = mysqli_num_rows($result_kml);

				for ($k=0;$k<$nrows_kml;$k++) {
					$row_kml = mysqli_fetch_array($result_kml);
					extract($row_kml);

					echo "<input type=\"checkbox\" name=\"kml$k\" value=\"$KmlID\" /> <b title=\"$KmlNotes\">$KmlName</strong><br />\n";
					}

				echo "<input type=\"hidden\" name=\"nokml\" value=\"$nrows_kml\">
					<p>&nbsp;&nbsp;&nbsp;&nbsp;<input type=submit value=\" Display selected layers \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}
			?>
		<br><br>
		</div>
		<div class="span-24 last">		

			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
