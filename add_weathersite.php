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
	
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<!-- Validation Script -->
<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddSiteForm").validate({
			rules: {
				SiteName: {
					required: true,
				},
				Lat: {
					required: true,
					number: true
				},
				Lon: {
					required: true,
					number: true
				}
			},
			messages: {
				SiteName: "Please enter a name for this site",
				Lat: "Please enter the latitude for this site",
				Lon: "Please enter the longitude for this site"
			}
			});
		});
	</script>
	<style type="text/css">
	#AddSiteForm label.error {
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
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">

			<h3>Add a weather data collection site:</h3>
			<form action="include/add_weathersite.php" method="POST" id="AddSiteForm">
				<p>Site Name: <br><input type="text" name="SiteName" maxlength="40" size="30" class="fg-button ui-state-default ui-corner-all formedge"><br>
				Latitude: <br><input type="text" name="Lat" maxlength="20" size="10" class="fg-button ui-state-default ui-corner-all formedge"> (decimal degrees) <br>
				Longitude: <br><input type="text" name="Lon" maxlength="20" size="10" class="fg-button ui-state-default ui-corner-all formedge"> (decimal degrees) <br>
				Elevation: <br><input type="text" name="Elevation" maxlength="20" size="10" class="fg-button ui-state-default ui-corner-all formedge"> (meters above sea level) <br>
				Data source: <br><input type="text" name="DataSource" maxlength="60" size="40" class="fg-button ui-state-default ui-corner-all formedge"><br>
				<input type=submit value=" Add Site " class="fg-button ui-state-default ui-corner-all">
			</form>
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
