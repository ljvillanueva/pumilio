<?php

//Select a site

echo "<br>";

$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);
$no_sites=query_one("SELECT COUNT(DISTINCT SiteID) FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);

if ($no_sites==0) {
	echo "This archive has no sites yet.";
	}
elseif ($no_sounds==0) {
	echo "This archive has no sounds yet.";
	}
else {
	echo "<p>Select a site to browse:<br>";
	echo "<form action=\"browse_site.php\" method=\"GET\">";
	$query = "SELECT * from Sites ORDER BY SiteName";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	echo "<select name=\"SiteID\" class=\"ui-state-default ui-corner-all formedge\">";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		//How many sounds associated with that site
		$this_no_sounds=query_one("SELECT COUNT(*) FROM Sounds WHERE SiteID='$SiteID' AND SoundStatus!='9' $qf_check", $connection);
		if ($this_no_sounds>0) {
			$this_no_sounds_f = number_format($this_no_sounds);
			echo "<option value=\"$SiteID\">$SiteName - $this_no_sounds_f sound files</option>\n";}
		}

		echo "</select>
		<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
	}

?>
