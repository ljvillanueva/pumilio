<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require("config.php");
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");

$date_to_browse=cleanme($_GET["date"]);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Browse Weather Stations</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

#Get points from the database
$query = "SELECT * FROM WeatherSites";
$result=query_several($query, $connection);
$nrows = mysqli_num_rows($result);

if ($nrows>0){

	if ($googlemaps_ver == "3"){
	########################
	# GOOGLE MAPS v3
	########################
		echo "<script src=\"http://maps.googleapis.com/maps/api/js?key=$googlemaps3_key&sensor=false\" type=\"text/javascript\"></script>\n";
	
		echo "<script type=\"text/javascript\">
			var infowindow = null;
	    		$(document).ready(function () { initialize();  });

	   		var sites = [\n";
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$first_w_date=query_one("SELECT DATE_FORMAT(WeatherDate,'%d-%b-%Y') AS first_w_date FROM WeatherData WHERE WeatherSiteID=$WeatherSiteID ORDER BY WeatherDate ASC LIMIT 1", $connection);
				$last_w_date=query_one("SELECT DATE_FORMAT(WeatherDate,'%d-%b-%Y') AS first_w_date FROM WeatherData WHERE WeatherSiteID=$WeatherSiteID ORDER BY WeatherDate DESC LIMIT 1", $connection);

				array_push($sites_bounds, "var p$i = new google.maps.LatLng($WeatherSiteLat, $WeatherSiteLon);\nmyBounds.extend(p$i);\n");

				if ($first_w_date==""){
					echo "['$WeatherSiteName', $WeatherSiteLat, $WeatherSiteLon, $WeatherSiteID, 'Weather Station: $WeatherSiteName<br>No data available yet.']";
					}
				else{
					echo "['$WeatherSiteName', $WeatherSiteLat, $WeatherSiteLon, $WeatherSiteID, 'Weather Station: $WeatherSiteName<br>Data available from $first_w_date to $last_w_date.']";
					}
				}

				echo "];

				function setMarkers(map, markers) {
					for (var i = 0; i < markers.length; i++) {
					    var sites = markers[i];
					    var siteLatLng = new google.maps.LatLng(sites[1], sites[2]);
					    var marker = new google.maps.Marker({
						position: siteLatLng,
						map: map,
						title: sites[0],
						html: '<div style=\"width:220px\"><div class=\"highlight4 ui-corner-all\">' + sites[0] + '</div>' + sites[4] + '</div>'
					 });
					    var contentString = \"Some content\";

					    google.maps.event.addListener(marker, \"click\", function () {
						infowindow.setContent(this.html);
						infowindow.open(map, this);
					    });
					}
				    }

				function initialize() {

					var centerMap = new google.maps.LatLng(0, 0);

					var myOptions = {
					    zoom: 4,
					    center: centerMap,
					    mapTypeId: google.maps.MapTypeId.ROADMAP
					}

					var map = new google.maps.Map(document.getElementById(\"map_canvas\"), myOptions);\n";


				#Check if any KML to use
				if ($usekml=="1"){
					for ($k=0;$k<$nokml;$k++) {
						$this_kmlID=filter_var($_GET["kml$k"], FILTER_SANITIZE_NUMBER_INT);
						$this_kmlurl=query_one("SELECT KmlURL FROM Kml WHERE KmlID='$this_kmlID'", $connection);
						#add selected kml layers
						echo "\nvar ctaLayer$k = new google.maps.KmlLayer('$this_kmlurl',{preserveViewport:true});
							ctaLayer$k.setMap(map);\n";
						}
					}
				else {
					$result_kml=query_several("SELECT * FROM Kml WHERE KmlDefault='1'", $connection);
					$nrows_kml = mysqli_num_rows($result_kml);
					if ($nrows_kml > 0) {
						$kml_default=1;
						for ($k=0;$k<$nrows_kml;$k++) {
							$row_kml = mysqli_fetch_array($result_kml);
							extract($row_kml);
							echo "\nvar ctaLayer$k = new google.maps.KmlLayer('$this_kmlurl',{preserveViewport:true});
								ctaLayer$k.setMap(map);\n";
							}
						}
					}

				echo "var myBounds = new google.maps.LatLngBounds(); 
				   
					setMarkers(map, sites);
					    infowindow = new google.maps.InfoWindow({
						content: \"loading...\"
					 	});\n";

				    for ($p=0;$p<(count($sites_bounds));$p++) {
						echo $sites_bounds[$p];
						}
				    
					echo "\nmap.fitBounds(myBounds);
					    }
					</script>\n";

		}
	else {
		$use_googlemaps=query_one("SELECT Value from PumilioSettings WHERE Settings='use_googlemaps'", $connection);
		if ($use_googlemaps=="1"){
			die("<div class=\"error\">The system is set up to use Google Maps v2. This version has been deprecated. Please update your settings in the administration menu or contact your administrator.</div>");
			}
		else{
			die("<div class=\"error\">The system is not set up for the use of Google Maps.</div>");
			}
		}
	}

?>    

    </script>

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
