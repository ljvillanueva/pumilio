<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: $app_url/error.php?e=config");
    die();
}

require("include/apply_config.php");

$force_loggedin = TRUE;
require("include/check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Sample the archive</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

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

			<?php

			echo "<h3>Export marks data</h3>";
			echo "<p><strong>Export marks data as comma-separated values</strong></p>
				<p>Select the data below and save as comma-separated (csv). 
				Then that file can be imported to any database, spreadsheet or statistics program.";

			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			$no_sounds=query_one("SELECT COUNT(*) FROM Sounds", $connection);
			$no_marks=query_one("SELECT COUNT(*) FROM SoundsMarks", $connection);

			if ($nrows>0 && $no_sounds>0 && $no_marks>0) {
				echo "<form action=\"include/export_csv.php\" method=\"POST\" id=\"csvform\" target=\"csv\" onsubmit=\"window.open('', 'csv', 'width=800,height=400,status=yes,resizable=yes,scrollbars=auto')\">
				<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">";

				for ($i=0;$i<$nrows;$i++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					$no_marks_i = query_one("SELECT COUNT(*) FROM SoundsMarks, Sounds 
						WHERE SoundsMarks.SoundID=Sounds.SoundID
						AND Sounds.ColID='$ColID'
						AND Sounds.SoundStatus!='9'", $connection);
		
					if ($no_marks_i > 0){
						echo "<option value=\"$ColID\">$CollectionName</option>\n";
						}
					}

				echo "</select> 
				<input type=submit value=\" Get data \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}
			else {
				echo "<p>There is no data available yet.";
				}
			?>
			
		<br>
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			
			?>
		</div>
	</div>

</body>
</html>
