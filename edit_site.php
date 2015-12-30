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
$force_admin = TRUE;
require("include/check_admin.php");

#Sanitize inputs
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$d=filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE html>
<html>
<head>
<title>$app_custom_name - Edit Site Info</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");
?>


<script type="text/javascript">
	$(function() {
		$("#tabs0").tabs();
	});
	</script>

	<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#EditForm").validate({
			rules: {
				SiteName: {
					required: true
				}
			},
			messages: {
				SiteName: "Please enter the name of the file"
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
if ($use_googleanalytics){
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
		
			$query = "SELECT * FROM Sites WHERE SiteID='$SiteID'";

			$result=query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
			if ($nrows==1) {
				$row = mysqli_fetch_array($result);
				extract($row);
					
				echo "<h3>Edit site information</h3>";

				if ($d==1) {
					echo "<p><div class=\"alert alert-success\">Site was updated successfully. 
					Return to <a href=\"browse_site.php?SiteID=$SiteID\">browsing</a></div>";
					}


				echo "<p>
				<form action=\"edit_site2.php\" method=\"POST\" id=\"EditForm\">
					<input name=\"SiteID\" type=\"hidden\" value=\"$SiteID\">
					<div class=\"row\">
			        	<div class=\"col-md-5\">
							<p>Name of the site: <br><input name=\"SiteName\" type=\"text\" maxlength=\"160\" value=\"$SiteName\" class=\"form-control\"></p>
						</div>
						<div class=\"col-md-3\">
							<p>Latitude: <br><input name=\"SiteLat\" type=\"text\" maxlength=\"20\" value=\"$SiteLat\" class=\"form-control\"></p>
							<p>Longitude: <br><input name=\"SiteLon\" type=\"text\" maxlength=\"20\" value=\"$SiteLon\" class=\"form-control\"></p>
						</div>
					</div>
					

					<button type=\"submit\" class=\"btn btn-primary\"> Edit Site </button>
				</form>";


				$no_sounds = query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SiteID='$SiteID' AND Sounds.SoundStatus!='9'", $connection);
				echo "<br>
					<a name=\"anchor2\"></a>		
					<h3>Add or edit metadata for all the files in this site</h3>\n";

				if ($d==2) {
					echo "<p><div class=\"alert alert-success\">Metadata was updated successfully. 
					Return to <a href=\"browse_site.php?SiteID=$SiteID\">browsing</a></div>";}


				echo "<p>
				<form action=\"edit_site3.php\" method=\"POST\">
					<input name=\"SiteID\" type=\"hidden\" value=\"$SiteID\">";

				echo "<div class=\"row\">
			        	<div class=\"col-md-7\">";

					$query_e = "SELECT SensorID as SensorID_q, Recorder from Sensors ORDER BY Recorder";
					$result_e = mysqli_query($connection, $query_e)
						or die (mysqli_error($connection));
					$nrows_e = mysqli_num_rows($result_e);
					echo "Sensor used: <br><select name=\"SensorID\" class=\"form-control\">";
						echo "<option value=\"\"> </option>\n";

					for ($e=0;$e<$nrows_e;$e++){
						$row_e = mysqli_fetch_array($result_e);
						extract($row_e);
						echo "<option value=\"$SensorID_q\">$Recorder</option>\n";
						}
					echo "</select> <small><a href=\"admin.php?t=4\">Add sensors to the list</a></small><br>
					
					Notes: <br><input name=\"Notes\" type=\"text\" maxlength=\"250\" value=\"$Notes\" class=\"form-control\"><br>
					<div class=\"alert alert-warning\"><span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> This will replace the fields in the $no_sounds files at this site.</div>
					</div>
					</div>
				<button type=\"submit\" class=\"btn btn-primary\">  Add metadata of the files </button>
				</form>
			
				<br>";
				}

		require("include/bottom.php");
		?>

	</div>

</body>
</html>
