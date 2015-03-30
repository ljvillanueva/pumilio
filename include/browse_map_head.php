<?php

if ($googlemaps_ver == "3"){
########################
# GOOGLE MAPS v3
########################
	echo "<script src=\"http://maps.googleapis.com/maps/api/js?key=$googlemaps3_key&amp;sensor=false\" type=\"text/javascript\"></script>\n";
	
	echo "<script type=\"text/javascript\">
		var infowindow = null;
    		$(document).ready(function () { initialize();  });

   		var sites = [\n";
		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);

			#Add error to the lat long for guests
			if ($pumilio_loggedin == FALSE && $hide_latlon_guests) {
				$rand_dir=rand(0,1);
				$rand_error=(rand(0,100))/10000;
				if ($rand_dir==0) {
					$SiteLat=$SiteLat+$rand_error;
					}
				else {
					$SiteLat=$SiteLat-$rand_error;
					}
			
				$rand_dir=rand(0,1);
				$rand_error=(rand(0,100))/10000;
				if ($rand_dir==0){
					$SiteLon=$SiteLon+$rand_error;
					}
				else {
					$SiteLon=$SiteLon-$rand_error;
					}
				}

			if ($date_to_browse=="") {
				$no_sounds=query_one("SELECT COUNT(*) AS no_sounds FROM Sounds WHERE SiteID=$SiteID AND Sounds.SoundStatus!='9' $qf_check", $connection);
				if ($no_sounds>0) {

					$SiteName=filter_var($SiteName, FILTER_SANITIZE_STRING);

					if ($no_sounds==1) {
						$no_sounds_f = "One sound";
						}
					else {
						$no_sounds_f = "$no_sounds sounds";
						}

					#Set each point
					#Each point will help determine the map's extent, from http://econym.org.uk/gmap/basic14.htm
					$first_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS first_date FROM Sounds 
						WHERE SiteID=$SiteID AND Sounds.SoundStatus!='9' $qf_check 
						ORDER BY Date ASC LIMIT 1", $connection);
					$last_date=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') AS last_date FROM Sounds 
						WHERE SiteID=$SiteID AND Sounds.SoundStatus!='9' $qf_check 
						ORDER BY Date DESC LIMIT 1", $connection);

					echo "['$SiteName', $SiteLat, $SiteLon, $SiteID, '$no_sounds_f at this site<br>Sounds available from $first_date to $last_date. ', 'browse_site.php?SiteID=$SiteID']";
					array_push($sites_bounds, "var p$i = new google.maps.LatLng($SiteLat, $SiteLon);\nmyBounds.extend(p$i);\n");
					
					$no_res++;
					if ($i == ($nrows - 1)){
						echo "\n";
						}
					else{
						echo ",\n";
						}
					}
				}
			else {

			$SiteName=filter_var($SiteName, FILTER_SANITIZE_STRING);

			if ($time_to_browse=="") {
				$no_sounds=query_one("SELECT COUNT(*) AS no_sounds FROM Sounds WHERE SiteID='$SiteID' AND Date='$date_to_browse' AND Sounds.SoundStatus!='9'", $connection);

				if ($no_sounds>0) {
					if ($no_sounds==1) {
						$no_sounds_f = "One sound";
						}
					else {
						$no_sounds_f = "$no_sounds sounds";
						}
					
					
					
					
					$query_by_dates = "SELECT DISTINCT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f, DATE_FORMAT(Time,'%h:%i %p') AS Time_f, SoundID, SoundName 
						FROM Sounds
						WHERE Date IS NOT NULL AND SiteID=$SiteID AND Date='$date_to_browse' 
						AND Sounds.SoundStatus!='9' $qf_check ORDER BY Time ASC";
					$result_by_dates=query_several($query_by_dates, $connection);
					$nrows_by_dates = mysqli_num_rows($result_by_dates);

					$thislist = "";

					for ($dd=0;$dd<$nrows_by_dates;$dd++) {
						$row_by_dates = mysqli_fetch_array($result_by_dates);
						extract($row_by_dates);

						if (is_odd($dd)) {
							$thislist = $thislist . "<p><a href=\"db_filedetails.php?SoundID=$SoundID\">$SoundName</a><br> &nbsp;$Date_f - $Time_f";
							}
						else {
							$thislist = $thislist . "<p style=\"background-color:#E0EEEE;\"><a href=\"db_filedetails.php?SoundID=$SoundID\">$SoundName</a><br> &nbsp;$Date_f - $Time_f";
							}
						}
					
					
					echo "['$SiteName', $SiteLat, $SiteLon, $SiteID, '$no_sounds_f on $Date_f: $thislist', 'browse_site_date.php?SiteID=$SiteID&Date=$date_to_browse']";
					array_push($sites_bounds, "var p$i = new google.maps.LatLng($SiteLat, $SiteLon);\nmyBounds.extend(p$i);\n");

					$no_res++;

					$this_page_title="Browse Map for $Date_f";
					
					if ($i == ($nrows - 1)){
						echo "\n";
						}
					else{
						echo ",\n";
						}
					}
				}
			else {
				$no_sounds=query_one("SELECT COUNT(*) AS no_sounds FROM Sounds 
					WHERE SiteID=$SiteID AND Date='$date_to_browse' AND Time='$time_to_browse' 
					AND Sounds.SoundStatus!='9' $qf_check", $connection);
				if ($no_sounds>0) {
					if ($no_sounds==1) {
						$no_sounds_f = "One sound";
						}
					else {
						$no_sounds_f = "$no_sounds sounds";
						}
						
					$query_by_dates = "SELECT DISTINCT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f, DATE_FORMAT(Time,'%h:%i %p') AS Time_f, SoundID, SoundName 
						FROM Sounds WHERE Date IS NOT NULL 
						AND SiteID=$SiteID AND Date='$date_to_browse' and Time='$time_to_browse' 
						AND Sounds.SoundStatus!='9' $qf_check ORDER BY Time ASC";
					$result_by_dates=query_several($query_by_dates, $connection);
					$nrows_by_dates = mysqli_num_rows($result_by_dates);

					$thislist = "";

					for ($dd=0;$dd<$nrows_by_dates;$dd++) {
						$row_by_dates = mysqli_fetch_array($result_by_dates);
						extract($row_by_dates);

						if (is_odd($dd)) {
							$thislist = $thislist . "<p><a href=\"db_filedetails.php?SoundID=$SoundID\">$SoundName</a><br> &nbsp;$Date_f - $Time_f";
							}
						else {
							$thislist = $thislist . "<p style=\"background-color:#E0EEEE;\"><a href=\"db_filedetails.php?SoundID=$SoundID\">$SoundName</a><br> &nbsp;$Date_f - $Time_f";
							}
						}
						
					echo "['$SiteName', $SiteLat, $SiteLon, $SiteID, '$no_sounds_f on $Date_f $Time_f: $thislist', 'browse_site_date.php?SiteID=$SiteID&Date=$date_to_browse']";
					array_push($sites_bounds, "var p$i = new google.maps.LatLng($SiteLat, $SiteLon);\nmyBounds.extend(p$i);\n");
					
					$no_res++;
					$this_page_title="Browse Map for $Date_f $Time_f";
					if ($i == ($nrows - 1)){
						echo "\n";
						}
					else{
						echo ",\n";
						}
					}
				}
			}

			#use the data from the sites to create a pull-down
			if (isset($date_to_browse) && $date_to_browse!="") {
				if ($no_sounds>0) {
					array_push($sites_rows, "<option value=\"$SiteID\">$SiteName - $no_sounds sounds on $Date_f</option>\n");
					}
				}
			else {
				if ($no_sounds>0) {
					array_push($sites_rows, "<option value=\"$SiteID\">$SiteName - $no_sounds sounds between $first_date and $last_date</option>\n");
					}
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
					html: '<div style=\"width:320px\"><div class=\"highlight4 ui-corner-all\"><a href=\"' + sites[5] + '\" style=\"color: white;\">' + sites[0] + '</a></div>' + sites[4] + '</div>'
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

			#KML layers forced on
			$querykml = "SELECT * FROM Kml WHERE KmlDefault='2'";
			$result_kml = query_several($querykml, $connection);
			$nrows_kml = mysqli_num_rows($result_kml);

			for ($kk=0; $kk<$nrows_kml; $kk++) {
				$row_kml = mysqli_fetch_array($result_kml);
				extract($row_kml);

				echo "\nvar ctaLayer$k = new google.maps.KmlLayer('$KmlURL',{preserveViewport:true});
				        ctaLayer$k.setMap(map);\n";
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
?>
