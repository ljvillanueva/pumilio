<?php

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$files_format=strtolower(filter_var($_POST["files_format"], FILTER_SANITIZE_STRING));
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$files_to_process=filter_var($_POST["files_to_process"], FILTER_SANITIZE_STRING);
$files_to_process_counter=filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);

if ($dir==""){
	die();
	}

if ($files_format==""){
	die();
	}

$files_format_length=strlen($files_format);

#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php

			echo "<h3>Add files from a database or spreadsheet</h3>";

			echo "<form action=\"add_from_db.php\" method=\"POST\" id=\"AddForm\">
				<input type=\"hidden\" name=\"step\" value=\"4\">
				<p>Select which fields you will add from the list below. Technical data like number of 
					channels, format, and duration are filled automatically.<br>
				<input type=\"checkbox\" name=\"OtherSoundID1\" value=\"1\" checked=\"checked\" DISABLED> Custom ID for the file (OtherSoundID - Required and can be NULL)<br>
				<input type=\"checkbox\" name=\"SoundName\" value=\"1\"> SoundName (default: same as the file name)<br>
				<input type=\"checkbox\" name=\"Date\" value=\"1\"> Date (YYYY-MM-DD)<br>
				<input type=\"checkbox\" name=\"Time\" value=\"1\"> Time (HH:MM:SS)<br>
				<input type=\"checkbox\" name=\"Notes\" value=\"1\"> Notes<br>
				<input type=\"hidden\" name=\"dir\" value=\"$dir\">
				<input type=\"hidden\" name=\"files_format\" value=\"$files_format\">
				<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
				<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
				<input type=\"hidden\" name=\"SensorID\" value=\"$SensorID\">
				<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
				<input type=\"hidden\" name=\"files_to_process\" value=\"$files_to_process\">
				<input type=\"hidden\" name=\"OtherSoundID\" value=\"1\">";
				
				$query = "SELECT * from Collections ORDER BY CollectionName";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));
				$nrows = mysqli_num_rows($result);
		
				if ($nrows>0) {
					echo "<p>Add files to this collection: 
					<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">
						<option></option>\n";
	
					for ($i=0;$i<$nrows;$i++) {
						$row = mysqli_fetch_array($result);
						extract($row);
							echo "<option value=\"$ColID\">$CollectionName</option>\n";
						}

					echo "</select> <a href=\"add_collection.php\">Add Collections</a><br>
		
					Site: ";
					$query_s = "SELECT SiteID AS this_SiteID, SiteName AS this_SiteName, SiteLat AS this_SiteLat, SiteLon AS this_SiteLon FROM Sites ORDER BY this_SiteName";
					$result_s = mysqli_query($connection, $query_s)
						or die (mysqli_error($connection));
					$nrows_s = mysqli_num_rows($result_s);
					echo "<select name=\"SiteID\" class=\"ui-state-default ui-corner-all\">
						<option></option>\n";

					for ($j=0;$j<$nrows_s;$j++) {
						$row_s = mysqli_fetch_array($result_s);
						extract($row_s);
						echo "<option value=\"$this_SiteID\">$this_SiteName ($this_SiteLat/$this_SiteLon)</option>\n";
						}
					echo "</select>";
			
					echo " <a href=\"#\" onclick=\"window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">Add sites</a><br>
			
					Sensor: ";
					$query_s = "SELECT * FROM Sensors ORDER BY SensorID";
					$result_s = mysqli_query($connection, $query_s)
						or die (mysqli_error($connection));
					$nrows_s = mysqli_num_rows($result_s);
					echo "<select name=\"SensorID\" class=\"ui-state-default ui-corner-all\">
						<option></option>\n";

					for ($j=0;$j<$nrows_s;$j++) {
						$row_s = mysqli_fetch_array($result_s);
						extract($row_s);
						echo "<option value=\"$SensorID\">$Recorder $Microphone - $Notes</option>\n";
						}
					echo "</select>";
				
					echo "
				
					<p><input type=submit value=\" Continue \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}
			?>

