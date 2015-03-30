<?php

//Select a sound collection

$no_weather=query_one("SELECT COUNT(*) from WeatherSites", $connection);

	if ($no_weather==0) {
		echo "This archive has no weather data.";
		}
	else {
		echo "<p>The database has $no_weather sites with weather data. Select one site to add data:<br>";
		echo "<form action=\"add_weatherdata.php\" method=\"GET\">";
			$query_w = "SELECT * from WeatherSites ORDER BY WeatherSiteName";
			$result_w = mysqli_query($connection, $query_w)
				or die (mysqli_error($connection));
			$nrows_w = mysqli_num_rows($result_w);
			echo "<select name=\"WeatherSiteID\" class=\"fg-button ui-corner-all\">";

				for ($w=0;$w<$nrows_w;$w++) {
					$row_w = mysqli_fetch_array($result_w);
					extract($row_w);
					$no_datapoints=query_one("SELECT COUNT(*) from WeatherData WHERE WeatherSiteID='$WeatherSiteID'", $connection);
						echo "<option value=\"$WeatherSiteID\">$WeatherSiteName ($no_datapoints data points)</option>\n";
					}

			echo "</select> 
			<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";
		
		#Delete data
		echo "<p>If there are data errors, delete the whole data set for a site and re-import it. Select one site to delete the data:<br>";
	
	
		#Delete div
		echo "<div id=\"dialogdata\" title=\"Delete data?\">
			<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The data will be deleted, this action can not be undone! Are you sure?</p></div>";
					
		echo "<form action=\"include/delete_weatherdata.php\" method=\"POST\" id=\"del_weatherdata\" name=\"del_weatherdata\">";
			$query_w = "SELECT * from WeatherSites ORDER BY WeatherSiteName";
			$result_w = mysqli_query($connection, $query_w)
				or die (mysqli_error($connection));
			$nrows_w = mysqli_num_rows($result_w);
			echo "<select name=\"WeatherSiteID\" class=\"fg-button ui-corner-all\">";

			for ($w=0;$w<$nrows_w;$w++) {
				$row_w = mysqli_fetch_array($result_w);
				extract($row_w);
					echo "<option value=\"$WeatherSiteID\">$WeatherSiteName</option>\n";
				}

			echo "</select> 
			<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";
	}
	
#add weather sites
echo "<p>Other tasks:
	<br>&nbsp;&nbsp;&nbsp;<form action=\"add_weathersite.php\" method=\"GET\">
	<input type=submit value=\" Add weather sites \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
	
echo "<br>&nbsp;&nbsp;&nbsp;<form action=\"browse_weather.php\" method=\"GET\">
	<input type=submit value=\" View weather sites in a map \" class=\"fg-button ui-state-default ui-corner-all\"></form>";

?>
