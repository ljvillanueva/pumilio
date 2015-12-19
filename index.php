<?php
session_start();
header( 'Content-type: text/html; charset=utf-8' );

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: $app_url/error.php?e=config");
    die();
}

require("include/apply_config.php");
require("include/check_admin.php");


#DB
use \DByte\DB;
DB::$c = $pdo;



#If user is not logged in, add check for QF
if ($pumilio_loggedin == FALSE) {
	$qf_check = "AND `Sounds`.`QualityFlagID`>=$default_qf";
	}
else {
	$qf_check = "";
	}

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");



####################################################3
#$use_googlemaps=FALSE;
#$use_leaflet=TRUE;
#Get points from the database
$results_map = DB::fetch('SELECT `Sites`.`SiteID`, `Sites`.`SiteLat`, `Sites`.`SiteLon`, `Sites`.`SiteName` FROM `Sites`, `Sounds` WHERE `Sites`.`SiteLat` IS NOT NULL AND `Sites`.`SiteLon` IS NOT NULL AND `Sites`.`SiteID`=`Sounds`.`SiteID` AND `Sounds`.`SoundStatus` != 9 GROUP BY `Sites`.`SiteID`, `Sites`.`SiteLat`, `Sites`.`SiteLon`, `Sites`.`SiteName`');
$no_results_map = count($results_map);

####################################################3


if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
require("include/index_map_head.php");
echo "</head>\n";

if ($mapping_system == "Gmaps"){
	echo "<body onload=\"initialize()\" onunload=\"GUnload()\">";
	}
else{
	echo "<body>";
	}

?>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		


	echo "<div class=\"jumbotron\">
			<h2>Welcome to $app_custom_name</h2>";

			#Custom links
			echo "<div class=\"pull-right\">";
				if ($custom_link_url_1!=""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$custom_link_url_1\" role=\"button\">$custom_link_title_1</a></p>";
				}
				
				if ($custom_link_url_2!=""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$custom_link_url_2\" role=\"button\">$custom_link_title_1</a></p>";
				}

				if ($custom_link_url_3!=""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$custom_link_url_3\" role=\"button\">$custom_link_title_1</a></p>";
				}

			echo "</div>


			<p>$app_custom_text</p>\n";


				$no_Collections = DB::column('SELECT COUNT(DISTINCT ColID) FROM `Sounds` WHERE SoundStatus!=9 ' . $qf_check);
				$no_sounds = DB::column('SELECT COUNT(*) FROM `Sounds` WHERE SoundStatus!=9 ' . $qf_check);
				$no_sites = DB::column('SELECT COUNT(DISTINCT SiteID) FROM `Sounds` WHERE SoundStatus!=9 ' . $qf_check);

				
					
				if ($no_sounds > 0) {
					$no_sounds_f = number_format($no_sounds);
					$no_Collections_f = number_format($no_Collections);
					$no_sites_f = number_format($no_sites);		
					
					echo "<p>This archive has $no_sounds_f sound files ";
					if ($no_sites>0){
						echo "from $no_sites_f sites ";
						}
					echo "in $no_Collections_f ";
						if ($no_Collections==1) {
							echo "collection.</p>";
							}
						else {
							echo "collections.</p>";
							}

					}


		echo "</div>";

		

			if ($mapping_system == "Leaflet"){
				echo "<div id=\"map\">Your browser does not have JavaScript enabled or can not connect to the tile server. Please contact your administrator.</div>\n";
			}
			elseif ($mapping_system == "GMaps"){
				require("include/index_map_body.php");
			}
			
		
				#Tag cloud
				if ($use_tags=="1" || $use_tags=="") {
					echo "<h3>Tag cloud</h3>
						<p>Select sounds to browse according to their tags.";
					require("include/tagcloud.php");

					}

	

			
			echo " <div class=\"row\">
			        <div class=\"col-lg-4 text-center\">
			          <h2><span class=\"glyphicon glyphicon-cloud-upload\" aria-hidden=\"true\"></span> Add sounds<br>to this archive</h2>
			          <p><a class=\"btn btn-primary\" href=\"add.php\" role=\"button\">Add sounds »</a></p>
			        </div>
			        <div class=\"col-lg-4 text-center\">
			          <h2><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span> Explore the<br>sound archive</h2>
			          <p><a class=\"btn btn-primary\" href=\"search.php\" role=\"button\">Explore sounds »</a></p>
			       </div>
			        <div class=\"col-lg-4 text-center\">
			          <h2><span class=\"glyphicon glyphicon-cloud-download\" aria-hidden=\"true\"></span> Export<br>sounds and data</h2>
			          <p><a class=\"btn btn-primary\" href=\"export.php\" role=\"button\">Export data »</a></p>
			        </div>
			       </div>";

##################################
#$thanks_text = "Donec fringilla tortor metus, eu faucibus nunc hendrerit quis. Pellentesque in ex at arcu interdum laoreet. Curabitur in aliquam lacus. Pellentesque sed nibh enim. Nam condimentum tellus quam, ut efficitur turpis fermentum a. Morbi ut orci vitae tortor dapibus congue non nec neque. Nullam egestas tortor eu leo mollis condimentum. Aliquam sodales vel purus quis tincidunt. ";
##################################

if (isset($acknowledgement)){
	if ($acknowledgement != ""){
		echo "<dl class=\"dl-horizontal\">
		  <dt>Acknowledgements</dt>
		  <dd>$acknowledgement</dd>
		</dl>";
		}
	}

require("include/bottom.php");
?>


</body>
</html>

<?php
if ($mapping_system == "Leaflet"){
	require("include/leaflet2.php");
}

#Close session to release script from php session
	session_write_close();
	flush(); @ob_flush();
	#Delete old temp files
	delete_old('tmp/', 30);
?>