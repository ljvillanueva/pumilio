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
				<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>\n";
			
			$default_kml=query_one("SELECT KmlDefault FROM Kml WHERE KmlID='$KmlID'", $connection);
			if ($default_kml == 0) {
				$selkml0 = "SELECTED";
				$selkml1 = "";
				$selkml2 = "";
				}
			elseif ($default_kml == 1) {
				$selkml0 = "";
				$selkml1 = "SELECTED";
				$selkml2 = "";
				}
			elseif ($default_kml == 2) {
				$selkml0 = "";
				$selkml1 = "";
				$selkml2 = "SELECTED";
				}
				
			echo "
			<form action=\"include/editkml2.php\" method=\"GET\"> 
				<input type=\"hidden\" name=\"KmlID\" value=\"$KmlID\">
				<select name=\"KmlDefault\" class=\"ui-state-default ui-corner-all formedge\">
					<option value=\"0\" $selkml0>optional</option>
					<option value=\"1\" $selkml1>default</option>
					<option value=\"2\" $selkml2>always on</option>
				</select>
				<input type=submit value=\" Change status \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>\n";
		}
		echo "</ul>";
	}
else {
	echo "<p>There are no KML/KMZ data layers.";
	}

?>
