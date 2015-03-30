<?php

//Select a sound collection

$no_Collections=query_one("SELECT COUNT(*) FROM Collections", $connection);
$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);
$no_sites=query_one("SELECT COUNT(DISTINCT SiteID) FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);
$total_no_sounds=$no_sounds;
if ($no_Collections==0) {
	echo "This archive has no Collections yet.";
	}
elseif ($no_sounds==0) {
	echo "This archive has no sounds yet.";
	}
else {
	echo "<p>Select a collection to browse:<br>";
	echo "<form action=\"$db_browse_link\" method=\"GET\">";
	if ($special_iframe==TRUE){
		echo "<input type=\"hidden\" name=\"page\" value=\"db_browse\">\n";
		}
	$query = "SELECT * from Collections ORDER BY CollectionName";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	echo "<select name=\"ColID\" class=\"ui-state-default ui-corner-all formedge\">";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		//How many sounds associated with that source
		$this_no_sounds=query_one("SELECT COUNT(*) as this_no_sounds FROM Sounds WHERE ColID='$ColID' 
			AND SoundStatus!='9' $qf_check", $connection);
		if ($this_no_sounds>0) {
			$this_no_sounds_f = number_format($this_no_sounds);
			echo "<option value=\"$ColID\">$CollectionName - $this_no_sounds_f sound files</option>\n";
			}
		}

	echo "</select>
	<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
	</form>";
	}
?>
