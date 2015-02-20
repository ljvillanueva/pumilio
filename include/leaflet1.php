<?

use \DByte\DB;
DB::$c = $pdo;


$SiteID = DB::column('SELECT SiteID FROM `Sounds` WHERE SoundID = ' . $SoundID);

$SiteLat = DB::column('SELECT SiteLat FROM `Sites` WHERE SiteID = ' . $SiteID);
$SiteLon = DB::column('SELECT SiteLon FROM `Sites` WHERE SiteID = ' . $SiteID);
$SiteName = DB::column('SELECT SiteName FROM `Sites` WHERE SiteID = ' . $SiteID);

if ($SiteLat != "" && $SiteLon != ""){

		echo " <script src=\"http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js\"></script>\n
				<script>

				var map = L.map('map').setView([$SiteLat, $SiteLon], 13);";

				$tileserver = "mapbox";
				if ($tileserver == "mapbox"){
					echo "
					L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
						maxZoom: 18,
						attribution: 'Map data &copy; <a href=\"http://openstreetmap.org\">OpenStreetMap</a> contributors, ' +
							'<a href=\"http://creativecommons.org/licenses/by-sa/2.0/\">CC-BY-SA</a>, ' +
							'Imagery © <a href=\"http://mapbox.com\">Mapbox</a>',
						id: 'examples.map-i875mjb7'
					}).addTo(map);
					";
				}
				elseif ($tileserver == "openstreet"){
					echo "
					L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
						maxZoom: 18,
						attribution: '&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
					}).addTo(map);
					";
				}
				elseif ($tileserver == "cartodb"){
					echo "
					L.tileLayer('http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
						maxZoom: 18,
						attribution: '&copy; <a href=\"http://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors, &copy; <a href=\"http://cartodb.com/attributions\">CartoDB</a>'
					}).addTo(map);
					";
				}


			echo "	L.marker([$SiteLat, $SiteLon]).addTo(map)
						.bindPopup('<div style=\"width: 160px\">Site: <a href=\"browse_site.php?SiteID=$SiteID\" title=\"Browse the recordings made at this site\"><strong>$SiteName</strong></a></div>');

				var popup = L.popup();

			</script>
				 ";

}
?>