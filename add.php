<?php
session_start();
header( 'Content-type: text/html; charset=utf-8' );

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

echo "<!DOCTYPE html>
<html>
<head>

<title>$app_custom_name - Add File</title>";

require("include/get_css3.php");
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

<h3>Add files to the database</h3>

<div class="row">
	<div class="col-md-4">

	
		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Add a single file to a collection</h3>
			</div>
			<div class="panel-body">
				
			<?php
			echo "<form action=\"add_file.php\" method=\"GET\" class=\"form-inline\">";
			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			if ($nrows>0) {
				echo "Collection:<br><select name=\"ColID\" class=\"form-control\">";

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
				<button type=\"submit\" class=\"btn btn-primary\"> Select </button></form>";
				}
			else {
				echo "<p>This archive has no Collections yet.";
				}
			?>



			</div>
		</div>
	</div>

	<div class="col-md-4">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Add files from a<br>Wildlife Acoustics SongMeter</h3>
			</div>
			<div class="panel-body">
				<p><a href="add_from_field.php?sm=1">Upload sound files</a></p>
				<p><a href="add_from_field.php?sm=1&local=1">Add sound files stored locally in server</a></p>
			</div>
		</div>
	</div>
	<div class="col-md-4">

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Add files from other types of sensors</h3>
			</div>
			<div class="panel-body">
				<p><a href="add_from_field.php">Upload sound files</a></p>
				<p><a href="add_from_field.php?local=1">Add sound files stored locally in server</a></p>
				<p><a href="add_from_db.php">Import files from a database/spreadsheet</a></p>
			</div>
		</div>
	</div>
</div>

<?php
		$result_fm = query_one("SELECT COUNT(*) FROM FilesToAddMembers", $connection);
		if ($result_fm > 0){
			echo "<p><a href=\"file_manager.php\"><button type=\"button\" class=\"btn btn-primary\">Check uploaded file status</button></a></p>";
			}
	?>

	<p><a href="#" onclick="window.open('include/addcollection.php', 'addcollection', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')"><button type="button" class="btn btn-primary">Add Collections</button></a></p>
	
	<p><a href="#" onclick="window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')"><button type="button" class="btn btn-primary">Add Sites</button></a>


<?php
require("include/bottom.php");
?>

</body>
</html>
