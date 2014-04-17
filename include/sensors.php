<?php

echo "<strong>Add sensors to the database</strong>
	<form action=\"include/add_sensors.php\" method=\"POST\" id=\"AddSensors\">
		<p>Recorder:<br><input type=\"text\" name=\"Recorder\" maxlength=\"100\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
		Microphone: <br><input type=\"text\" name=\"Microphone\" maxlength=\"80\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
		Notes of the sensor: <br><input type=\"text\" name=\"Notes\" maxlength=\"255\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
		<input type=submit value=\" Add sensor \" class=\"fg-button ui-state-default ui-corner-all\">
	</form>";

#Sensors in the db:
echo "<hr noshade>";

$no_sensors = DB::column('SELECT COUNT(*) FROM `Sensors`');

if ($no_sensors == 0){
	echo "<p>There are no sensors in the system.";
	}
else {
	$rows = DB::fetch('SELECT * FROM `Sensors` ORDER BY `SensorID`', array(TRUE));
	echo "<p>The system has the following ". count($rows) ." sensors:
		<table>";

	echo "<tr>
			<td>Sensor ID</td>
			<td>&nbsp;</td>
			<td>Recorder</td>
			<td>&nbsp;</td>
			<td>Microphone</td>
			<td>&nbsp;</td>
			<td>Notes</td>
			<td>&nbsp;</td>
			<td>Edit</td>
		</tr>\n";


 	foreach($rows as $row){
#	for ($i = 0; $i < $nrows; $i++) {
		#$row = mysqli_fetch_array($result);
		#extract($row);
		
			echo "<tr>
				<td>" . $row->SensorID . "</td>
				<td>&nbsp;</td>
				<td>" . $row->Recorder . "</td>
				<td>&nbsp;</td>
				<td>" . $row->Microphone . "</td>
				<td>&nbsp;</td>
				<td>" . $row->Notes . "</td>
				<td>&nbsp;</td>
				<td><a href=\"sensor_edit.php?SensorID=" . $row->SensorID . "\"><img src=\"images/pencil.png\"></td>
			</tr>\n";
		
		}
	echo "</table>";
	}

?>
