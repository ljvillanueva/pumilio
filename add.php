<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");

$force_loggedin = TRUE;
require("include/check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Add File</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<?php
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


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
		echo "<h3>Add files to the database</h3>";
		echo "<form action=\"add_file.php\" method=\"GET\">";
			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			if ($nrows>0) {
				echo "Add a single file to the collection &nbsp;&nbsp;<select name=\"ColID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";

				for ($i = 0; $i < $nrows; $i++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					//How many sounds associated with that source
					$result1 = mysqli_query($connection, "SELECT COUNT(*) as no_sounds FROM Sounds WHERE ColID='$ColID' AND SoundStatus!='9'");
					$row1 = mysqli_fetch_array($result1);
					extract($row1);

					echo "<option value=\"$ColID\">$CollectionName - $no_sounds soundfiles</option>\n";
					unset($nrows1);
					}

				echo "</select> 
				<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";
				}
			else {
				echo "<p>This archive has no Collections yet.";
				}


		echo "<hr noshade>
			<p><strong> Add files from a Wildlife Acoustics SongMeter:</strong>";
			echo "<p><a href=\"add_from_field.php?sm=1\">Upload sound files from a Wildlife Acoustics SongMeter</a>";
			echo "<p><a href=\"add_from_field.php?sm=1&local=1\">Add sound files from a Wildlife Acoustics SongMeter</a> (stored locally in server)";
				
		echo "<hr noshade>
			<p><strong> Add files from other sound recorders:</strong>";
		echo "<p><a href=\"add_from_field.php\">Upload sound files from the field</a>
			<p><a href=\"add_from_field.php?local=1\">Add sound files from the field</a> (stored locally in server)";
		
		echo "<p><a href=\"add_from_db.php\">Import files from a database/spreadsheet</a>";
		
		echo "<hr noshade>
			<p><a href=\"add_collection.php\">Add Collections</a>";
			echo "<p><a href=\"#\" onclick=\"window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">Add Sites</a>";
			echo "<p><a href=\"admin.php?t=4\">Add Sensors</a> (in the admin menu)";
			
		$result_fm = query_one("SELECT COUNT(*) FROM FilesToAddMembers", $connection);
		if ($result_fm > 0){
			echo "<p><a href=\"file_manager.php\">Check uploaded file status</a>";
			}

		?>

	</div>
	<div class="span-24 last">
		&nbsp;
	</div>
	<div class="span-24 last">
		<?php
		require("include/bottom.php");
		?>

	</div>
</div>

</body>
</html>
