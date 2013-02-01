<?php

echo "<strong>Add sensors to the database</strong>
	<form action=\"add_equipment2.php\" method=\"POST\" id=\"AddSensors\">
	<p>Recorder:<br><input type=\"text\" name=\"Recorder\" maxlength=\"100\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
	Microphone: <br><input type=\"text\" name=\"Microphone\" maxlength=\"80\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
	Notes of the sensor: <br><input type=\"text\" name=\"Notes\" maxlength=\"255\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
	<input type=submit value=\" Add sensor \" class=\"fg-button ui-state-default ui-corner-all\">
	</form>";

#Sensors in the db:
echo "<hr noshade>";
$query = "SELECT * from Sensors ORDER BY SensorID";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
$nrows = mysqli_num_rows($result);

if ($nrows == 0){
	echo "<p>There are no sensors in the system.";
	}
else {
	echo "<p>The system has the following sensors:
		<table>";

	echo "<tr><td>Sensor ID</td><td>&nbsp;</td><td>Recorder</td><td>&nbsp;</td><td>Microphone</td><td>&nbsp;</td><td>Notes</td><td>&nbsp;</td><td>Edit</td></tr>\n";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		if ($SensorID==1){
			echo "<tr><td>$SensorID</td><td>&nbsp;</td><td>$Recorder</td><td>&nbsp;</td><td>$Microphone</td><td>&nbsp;</td><td>$Notes</td><td>&nbsp;</td><td>&nbsp;</td></tr>\n";
			}
		else{
			echo "<tr><td>$SensorID</td><td>&nbsp;</td><td>$Recorder</td><td>&nbsp;</td><td>$Microphone</td><td>&nbsp;</td><td>$Notes</td><td>&nbsp;</td><td><a href=\"sensor_edit.php?SensorID=$SensorID\"><img src=\"images/pencil.png\"></td></tr>\n";
			}
		}
	echo "</table>";
	}

?>
