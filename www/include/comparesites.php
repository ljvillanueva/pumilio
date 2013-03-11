<?php

$dates=query_several("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f, Date FROM Sounds WHERE SoundStatus!='9' $qf_check GROUP BY Date ORDER BY Date", $connection);

$nrows_dates = mysqli_num_rows($dates);
if ($nrows_dates>0) {

	echo "<form action=\"compare.php\" method=\"GET\">
		Select a date to browse:
		<select name=\"date\" class=\"ui-state-default ui-corner-all\">";
					
	for ($dc=0;$dc<$nrows_dates;$dc++) {
		$row_date = mysqli_fetch_array($dates);
		extract($row_date);
		echo "\n<option value=\"$Date\">$Date_f</option>";
		}
	
	echo "</select>";
				
	echo "<p>Select up to three sites:</p><br>";
	
	$sites=query_several("SELECT Sites.SiteID,Sites.SiteName FROM Sites,Sounds WHERE Sites.SiteID=Sounds.SiteID 
		AND SoundStatus!='9' $qf_check GROUP BY SiteName", $connection);
	$nrows_sites = mysqli_num_rows($sites);
			
	echo "<p style=\"margin-left:10px;\">Site 1:</p>
		<select name=\"site1\" class=\"ui-state-default ui-corner-all\">
		<option></option>";
	for ($sc=0;$sc<$nrows_sites;$sc++) {
		$row_site = mysqli_fetch_array($sites);
		extract($row_site);
		$SiteName=truncate2($SiteName, 60);
		$from_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f from Sounds WHERE SiteID='$SiteID' 
			AND SoundStatus!='9' $qf_check ORDER BY Date ASC LIMIT 1", $connection);
		$to_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f from Sounds WHERE SiteID='$SiteID' 
			AND SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
		echo "\n<option value=\"$SiteID\">$SiteName ($from_date - $to_date)</option>";
		}			
	echo "</select> (Required)";
		
	$sites=query_several("SELECT Sites.SiteID,Sites.SiteName FROM Sites,Sounds WHERE Sites.SiteID=Sounds.SiteID 
		AND Sounds.SoundStatus!='9' $qf_check GROUP BY SiteName", $connection);
	$nrows_sites = mysqli_num_rows($sites);
			
	echo "<p style=\"margin-left:10px;\">Site 2:</p>
		<select name=\"site2\" class=\"ui-state-default ui-corner-all\">
		<option></option>";
	for ($sc=0;$sc<$nrows_sites;$sc++) {
		$row_site = mysqli_fetch_array($sites);
		extract($row_site);
		$SiteName=truncate2($SiteName, 60);
		$from_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f from Sounds WHERE SiteID='$SiteID' 
			AND Sounds.SoundStatus!='9' $qf_check ORDER BY Date ASC LIMIT 1", $connection);
		$to_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f from Sounds WHERE SiteID='$SiteID' 
			AND Sounds.SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
		echo "\n<option value=\"$SiteID\">$SiteName ($from_date - $to_date)</option>";
		}			
	echo "</select>";
		
	$sites=query_several("SELECT Sites.SiteID,Sites.SiteName FROM Sites,Sounds WHERE Sites.SiteID=Sounds.SiteID 
		AND Sounds.SoundStatus!='9' $qf_check GROUP BY SiteName", $connection);
	$nrows_sites = mysqli_num_rows($sites);
			
	echo "<p style=\"margin-left:10px;\">Site 3:</p>
		<select name=\"site3\" class=\"ui-state-default ui-corner-all\">
		<option></option>";
	for ($sc=0;$sc<$nrows_sites;$sc++) {
		$row_site = mysqli_fetch_array($sites);
		extract($row_site);
		$SiteName=truncate2($SiteName, 60);
		$from_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f from Sounds WHERE SiteID='$SiteID' 
			AND Sounds.SoundStatus!='9' $qf_check ORDER BY Date ASC LIMIT 1", $connection);
		$to_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f from Sounds WHERE SiteID='$SiteID' 
			AND Sounds.SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
		echo "\n<option value=\"$SiteID\">$SiteName ($from_date - $to_date)</option>";
		}			
	echo "</select>";
		
	echo "<input type=submit value=\" Show comparison \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";
	}
else {
	echo "<p>There are no sounds with dates in the database.</p>";
	}
?>
