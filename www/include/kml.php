<?php

echo "<strong>Add a KML/KMZ layer:</strong>
	<form action=\"include/editkml.php\" method=\"POST\" id=\"AddKML\">
	<input type=\"hidden\" name=\"op\" value=\"1\">

	Name: <br><input type=\"text\" name=\"KmlName\" maxlength=\"50\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
	Complete URL: <br><input type=\"text\" name=\"KmlURL\" maxlength=\"250\" size=\"36\" value=\"http://\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
	Notes: <br><input type=\"text\" name=\"KmlNotes\" maxlength=\"250\" size=\"36\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
	<input type=submit value=\" Add layer \" class=\"fg-button ui-state-default ui-corner-all formedge\">
	</form>
	<br><br>\n";
	

$no_kml=query_one("SELECT COUNT(*) FROM Kml", $connection);
if ($no_kml>0) {
	echo "<p>KML/KMZ layers:
		<ul>";

	$query_kml = "SELECT * FROM Kml ORDER BY KmlName";
	$result_kml=query_several($query_kml, $connection);
	$nrows_kml = mysqli_num_rows($result_kml);

	for ($k=0;$k<$nrows_kml;$k++) {
		$row_kml = mysqli_fetch_array($result_kml);
		extract($row_kml);

		echo "<li><form action=\"include/editkml.php\" method=\"POST\">$KmlName 
			(<a href=\"http://maps.google.com/maps?q=$KmlURL\" title=\"Open layer in GoogleMaps\" target=\"_blank\">$KmlURL</a>)
			<input type=\"hidden\" name=\"op\" value=\"2\">
			<input type=\"hidden\" name=\"KmlID\" value=\"$KmlID\">
			<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";
			
			$default_kml=query_one("SELECT KmlDefault FROM Kml WHERE KmlID='$KmlID'", $connection);
			if ($default_kml == 1) {
				echo " &nbsp;&nbsp;&nbsp;[<a href=\"include/editkml2.php?KmlID=$KmlID&op=2\">Remove as default layer</a>]";
				}
			elseif ($default_kml == 0) {
				echo " &nbsp;&nbsp;&nbsp;[<a href=\"include/editkml2.php?KmlID=$KmlID&op=1\">Set as default layer</a>]";
				}
		}
		echo "</ul>";
		
	}
else {
	echo "<p>There are no KML/KMZ data layers.";
	}


?>
