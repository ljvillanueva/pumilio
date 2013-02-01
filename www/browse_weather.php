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

$date_to_browse=cleanme($_GET["date"]);

require("include/update_sites.php");
?>

<html>
<head>

<?php

echo "<title>$app_custom_name - Browse Weather Stations</title>";
require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>



<?php

	#Get points from the database
	$query = "SELECT * FROM WeatherSites";
	$result=query_several($query, $connection);
	$nrows = mysqli_num_rows($result);

	if ($nrows>0)
	{

	echo "<script src=\"http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=$googlemaps_key\"
            type=\"text/javascript\"></script>
		<script type=\"text/javascript\">
    
	function initialize() {
	if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById(\"map_canvas\"));
	map.setCenter(new GLatLng(0,0),0);
	var bounds = new GLatLngBounds();
	map.addMapType(G_PHYSICAL_MAP);
	map.addControl(new GMapTypeControl());
	map.addControl(new GLargeMapControl3D());
	map.addControl(new GScaleControl());
	map.enableDoubleClickZoom();
	map.enableContinuousZoom();
	map.enableScrollWheelZoom(); 

 var baseIcon = new GIcon();
          baseIcon.iconSize=new GSize(32,32);
          baseIcon.shadowSize=new GSize(56,32);
          baseIcon.iconAnchor=new GPoint(16,32);
          baseIcon.infoWindowAnchor=new GPoint(16,0);

	var dicon = new GIcon(G_DEFAULT_ICON);
	var weathericon = new GIcon(baseIcon, \"http://maps.google.com/mapfiles/kml/pal4/icon22.png\", null, \"http://maps.google.com/mapfiles/kml/pal4/icon22s.png\");

	function createMarker(point,html,icon) {
		var marker = new GMarker(point,icon);
		GEvent.addListener(marker, \"click\", function() {
			marker.openInfoWindowHtml(html);
		});
		return marker;
	}\n";

	for ($i=0;$i<$nrows;$i++)
		{
		$row = mysqli_fetch_array($result);
		extract($row);

			$first_w_date=query_one("SELECT DATE_FORMAT(WeatherDate,'%d-%b-%Y') AS first_w_date FROM WeatherData WHERE WeatherSiteID=$WeatherSiteID ORDER BY WeatherDate ASC LIMIT 1", $connection);
			$last_w_date=query_one("SELECT DATE_FORMAT(WeatherDate,'%d-%b-%Y') AS first_w_date FROM WeatherData WHERE WeatherSiteID=$WeatherSiteID ORDER BY WeatherDate DESC LIMIT 1", $connection);

			echo "var point = new GLatLng($WeatherSiteLat, $WeatherSiteLon);";

			if ($first_w_date=="")
				echo "var marker = createMarker(point,'<div style=\"width:240px\">Weather Station: $WeatherSiteName<br>No data available yet.<\/div>',weathericon);";
			else
				echo "var marker = createMarker(point,'<div style=\"width:240px\">Weather Station: $WeatherSiteName<br>Data available from $first_w_date to $last_w_date.<\/div>',weathericon);";

			echo "map.addOverlay(marker);
				bounds.extend(point);\n\n";

		}


	echo "
		map.setZoom(map.getBoundsZoomLevel(bounds));
		map.setCenter(bounds.getCenter());
		}
    		}";
	}
?>    

    </script>

<?php
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
?>

</head>
<body onload="initialize()" onunload="GUnload()">

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			<h2>View weather stations in a map</h2>
		</div>
		<div class="span-24 last">
			<div id="map_canvas" style="width: 940px; height: 500px">There are no weather sites.</div>
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
