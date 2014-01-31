<?php

if ($googlemaps_ver == "3"){
########################
# GOOGLE MAPS v3
########################
	echo "<script src=\"http://maps.googleapis.com/maps/api/js?key=$googlemaps3_key&sensor=false\" type=\"text/javascript\"></script>\n";
	
	echo "<script type=\"text/javascript\">
	$(document).ready(function () { initialize();  });

	function initialize() {
		var myLatlng = new google.maps.LatLng($SiteLat, $SiteLon);
		var mapOptions = {
		  zoom: 11,
		  center: myLatlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		}
	var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);\n";

	#Check if any KML to use
	$result_kml=query_several("SELECT * FROM Kml WHERE KmlDefault='1'", $connection);
	$nrows_kml = mysqli_num_rows($result_kml);
	if ($nrows_kml > 0) {
		$kml_default=1;
		if ($hidekml != 1){
			for ($k=0;$k<$nrows_kml;$k++) {
				$row_kml = mysqli_fetch_array($result_kml);
				extract($row_kml);
				echo "\nvar ctaLayer$k = new google.maps.KmlLayer('$KmlURL',{preserveViewport:true});
					ctaLayer$k.setMap(map);\n";
				}
			}
		}
		
	echo "var marker = new google.maps.Marker({
		    position: myLatlng,
		    map: map,
		    title: '$SiteName'
		});
	      }
	</script>\n";
	}
?>
