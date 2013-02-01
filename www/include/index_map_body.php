<?php

echo "<div class=\"span-14\"><p><strong>Map:</strong></div>\n

<div class=\"span-10 last\">";

if ($no_res>0) {
	$query_dates = "SELECT DISTINCT DATE_FORMAT(Sounds.Date,'%d-%b-%Y') AS Date_f, Sounds.Date FROM Sounds, Sites 
		WHERE Sounds.Date IS NOT NULL AND Sites.SiteLat IS NOT NULL AND Sites.SiteLon IS NOT NULL 
		AND Sounds.SiteID=Sites.SiteID AND Sounds.SoundStatus!='9' $qf_check ORDER BY Sounds.Date";
	$result_dates=query_several($query_dates, $connection);
	$nrows_dates = mysqli_num_rows($result_dates);
	if ($nrows_dates>0) {
	
		if ($special_wrapper==TRUE){
			echo "<form action=\"$wrapper\" method=\"GET\">Filter by date: 
			<input type=\"hidden\" name=\"page\" value=\"browse_map\">";
			}
		else {
			echo "<form action=\"browse_map.php\" method=\"GET\">Filter by date: ";
			}
	
		echo "<select name=\"date_to_browse\" class=\"ui-state-default ui-corner-all\">
			<option value=\"\">All dates</option>";
		
		for ($d=0;$d<$nrows_dates;$d++) {
			$row_dates = mysqli_fetch_array($result_dates);
			extract($row_dates);
			if ($date_to_browse==$Date) {
				echo "\n<option value=\"$Date\" SELECTED>$Date_f</option>";
				}
			else {
				echo "\n<option value=\"$Date\">$Date_f</option>";
				}
			}
		echo "</select> ";

	if ($date_to_browse!="") {
		$query_times = "SELECT DISTINCT DATE_FORMAT(Sounds.Time,'%h:%i:%s %p') AS Time_f, Sounds.Time
				FROM Sounds,Sites 
				WHERE Sounds.Date='$date_to_browse' AND Sites.SiteLat IS NOT NULL 
				AND Sites.SiteLon IS NOT NULL AND Sounds.SiteID=Sites.SiteID 
				AND Sounds.SoundStatus!='9' $qf_check
				ORDER BY Time";
		$result_times=query_several($query_times, $connection);
		$nrows_times = mysqli_num_rows($result_times);

		echo "<select name=\"time_to_browse\" class=\"ui-state-default ui-corner-all\">
			<option value=\"\">All times</option>";
		
		for ($t=0;$t<$nrows_times;$t++) {
			$row_times = mysqli_fetch_array($result_times);
			extract($row_times);
			if ($time_to_browse==$Time) {
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

if ($no_res==0) {
	if ($time_to_browse!="" && $date_to_browse!="") {
		$Date_f=query_one("SELECT DATE_FORMAT('$date_to_browse', '%d-%b-%Y')", $connection);
		echo "<div class=\"notice\"><img src=\"images/error.png\"> No results were found for that date and time combination. Return to the <a href=\"browse_map.php\">default map view</a> or see results only for <a href=\"browse_map.php?date_to_browse=$date_to_browse\">$Date_f</a>.</div>";
		}
	elseif ($time_to_browse=="" && $date_to_browse!="") {
		$Date_f=query_one("SELECT DATE_FORMAT('$date_to_browse', '%d-%b-%Y')", $connection);
		echo "<div class=\"notice\"><img src=\"images/error.png\"> No results were found for that date. Return to the <a href=\"browse_map.php\">default map view</a>.</div>";
		}
	else {
		echo "<div class=\"notice\"><img src=\"images/error.png\"> No results were found. Return to the <a href=\"browse_map.php\">default map view</a>.</div>";
		}
	}
else {
	echo "<div id=\"map_canvas\" style=\"width: 940px; height: 500px\">$map_div_message</div>\n
		<p>There are $no_res sites with soundfiles. ";

	if ($no_res>1){
		echo "Some markers may be hidden behind others. Zoom in to see all the sites.";
		}

	echo "</p>
		<p>\n";
	}

?>
