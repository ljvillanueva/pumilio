<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddForm").validate({
			rules: {
				dir: {
					required: true
				},
				files_format: {
					required: true
				},
				ColID: {
					required: true
				}
			},
			messages: {
				dir: "Please enter a directory",
				files_format: "Please select the format of the files",
				ColID: "Please select the source to add the files to"
			}
			});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>

<?php

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
			echo "<h3>Add files from a database or spreadsheet</h3>
			<form action=\"add_from_db.php\" method=\"POST\" id=\"AddForm\">
			<input type=\"hidden\" name=\"step\" value=\"2\">
			<p>First, upload the files to a directory in the server that the webserver can read.<br>";
		
			$temp_add_dir=query_one("SELECT Value from PumilioSettings WHERE Settings='temp_add_dir'", $connection);
			if ($temp_add_dir == ""){
				echo "<div class=\"error\">The local directory is not set. Please set it up in the <a href=\"admin.php\">Administration</a> menu.</div>";
				$valid_form = 0;
				}
			else{
				$localdir = $temp_add_dir;
				echo "<p>Server local directory: <input name=\"dir\" type=\"text\" maxlength=\"160\" size=\"50\" value=\"$localdir\" class=\"fg-button ui-state-default ui-corner-all\">";
				$valid_form = 1;
				}
				
			echo "<input type=\"hidden\" name=\"local\" value=\"1\">";
				
			echo "<br>Select the format of the files: 
				<select name=\"files_format\" class=\"ui-state-default ui-corner-all\">
				<option></option>";
				require("include/sox_formats_list.php");
				//SoX options
				for ($s=0;$s<count($sox_formats);$s++) {
					echo "<option>$sox_formats[$s]</option>";
					}
						
			echo "</select><br>";

			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);

			if ($nrows>0) {
				echo "Add files to this Collection: 
				<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">
					<option></option>";
				for ($i=0;$i<$nrows;$i++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					echo "<option value=\"$ColID\">$CollectionName</option>\n";
					}
				echo "</select> <a href=\"add_source.php\">Add Collections</a><br>";
				
				}
			else {
				echo "<p><strong>There are no collections in the archive. Please create at least one to continue.</strong>";
				}
				
			#Site
			echo "Site: ";
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
			
				echo " <a href=\"admin.php?t=4\">Add sensors</a><br>";

			if ($valid_form == 0){
				echo "<input type=submit value=\" Check \" DISABLED class=\"fg-button ui-state-default ui-corner-all\">";
				}
			elseif ($valid_form == 1){
				echo "<input type=submit value=\" Check \" class=\"fg-button ui-state-default ui-corner-all\">";
				}

			echo "</form>";
			?>

