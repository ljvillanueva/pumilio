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

require("include/check_admin.php");

$WeatherSiteID=filter_var($_GET["WeatherSiteID"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>

<title>$app_custom_name - Add weather data</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<?php
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
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


			$WeatherSiteName=query_one("SELECT WeatherSiteName FROM WeatherSites WHERE WeatherSiteID='$WeatherSiteID' LIMIT 1", $connection);
			echo "<h3>Add data to $WeatherSiteName</h3>";

			echo "There are 10 comma-separated fields:
				<ul>
					<li>Date (YYYY-MM-DD)
					<li>Time (HH:MM:SS)
					<li>Temperature (degrees Celcius)
					<li>Precipitation (mm)
					<li>Relative Humidity (%)
					<li>Dew Point
					<li>Wind Speed (m/s)
					<li>Wind Direction
					<li>Light Intensity
					<li>Barometric Pressure
				</ul>";


			echo "<form action=\"add_weatherdata2.php\" method=\"POST\" id=\"AddForm\">";
			echo "<p>Add one line for each data point (use [enter] for a new line) with each field separated by a comma (,). If the data is missing, enter \"NULL\" without the quotes or leave empty.";
			echo "<textarea name=\"commadata\" cols=\"60\" rows=\"10\"></textarea>\n";
			echo "<input type=\"hidden\" name=\"WeatherSiteID\" value=\"$WeatherSiteID\">";
			echo "<br><input type=submit value=\" Check data and insert to database \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";
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
