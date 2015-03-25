<?php
session_start();

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
$map_only="1";
$use_googlemaps=FALSE;
$use_leaflet=TRUE;
#Get points from the database
$results_map = DB::fetch('SELECT SiteID, SiteLat, SiteLon, SiteName FROM `Sites` WHERE `SiteLat` IS NOT NULL AND `SiteLon` IS NOT NULL');
$no_results_map = count($results_map);

####################################################3


if ($map_only=="1"){
	require("include/index_map_head.php");
	}
#else{
	echo "

	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script src=\"js/jquery.validate.js\" type=\"text/javascript\"></script>

	<script type=\"text/javascript\">
	$().ready(function() {
		// validate signup form on keyup and submit
		$(\"#AddSample1\").validate({
			rules: {
				samplesize: {
					required: true,
					number: true
				},
				samplename: {
					required: true
				}
			},
			messages: {
				samplesize: \"Please enter the size of the sample set you want to create\",
				samplenaMe: \"Please enter a name for this sample set\"
			}
			});
		});
	</script>
	
	<script type=\"text/javascript\">
	$().ready(function() {
		// validate signup form on keyup and submit
		$(\"#AddSample2\").validate({
			rules: {
				samplesize: {
					required: true,
					number: true
				},
				samplename: {
					required: true
				}
			},
			messages: {
				samplesize: \"Please enter the size of the sample set you want to create\",
				samplename: \"Please enter a name for this sample set\"
			}
			});
		});
	</script>
	
	<script type=\"text/javascript\">
	$().ready(function() {
		// validate signup form on keyup and submit
		$(\"#AddSample3\").validate({
			rules: {
				sample_percent: {
					required: true,
					number: true
				},
				samplename: {
					required: true
				}
			},
			messages: {
				samplesize: \"Please enter the percent size of the sample set you want to create\",
				samplename: \"Please enter a name for this sample set\"
			}
			});
		});
	</script>

	<script type=\"text/javascript\">
	$().ready(function() {
		// validate signup form on keyup and submit
		$(\"#AddSample4\").validate({
			rules: {
				sample_percent: {
					required: true,
					number: true
				},
				samplename: {
					required: true
				}
			},
			messages: {
				samplesize: \"Please enter the percent size of the sample set you want to create\",
				samplename: \"Please enter a name for this sample set\"
			}
			});
		});
	</script>
	
	<script type=\"text/javascript\">
	$().ready(function() {
		// validate signup form on keyup and submit
		$(\"#AddSample5\").validate({
			rules: {
				sample_percent: {
					required: true,
					number: true
				},
				samplename: {
					required: true
				}
			},
			messages: {
				samplesize: \"Please enter the percent size of the sample set you want to create\",
				samplename: \"Please enter a name for this sample set\"
			}
			});
		});
	</script>
		
	<style type=\"text/css\">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>";


	$DateLow = DB::column('SELECT DATE_FORMAT(`Date`,"%Y, %c-1, %e") FROM `Sounds` WHERE `SoundStatus`!=9 ' . $qf_check . ' ORDER BY `Date` LIMIT 1');
	$DateHigh = DB::column('SELECT DATE_FORMAT(`Date`, "%Y, %c-1, %e") FROM `Sounds` WHERE `SoundStatus`!=9 ' . $qf_check . ' ORDER BY `Date` DESC LIMIT 1');
	$DateLow1 = DB::column('SELECT DATE_FORMAT(`Date`, "%d-%b-%Y") FROM `Sounds` WHERE `SoundStatus`!=9 ' . $qf_check . ' ORDER BY `Date` LIMIT 1');
	$DateHigh1 = DB::column('SELECT DATE_FORMAT(`Date`, "%d-%b-%Y") FROM `Sounds` WHERE `SoundStatus`!=9 ' . $qf_check . ' ORDER BY `Date` DESC LIMIT 1');

	
	#from http://jsbin.com/orora3/75/
	echo "	
	<script type=\"text/javascript\">
	$(function() {
		var dates = $( \"#startDate, #endDate\" ).datepicker({
			minDate: new Date($DateLow),
			maxDate: new Date($DateHigh),
			numberOfMonths: 3,
			changeYear: true,
			dateFormat: 'dd-MM-yy',
			onSelect: function( selectedDate ) {
				var option = this.id == \"startDate\" ? \"minDate\" : \"maxDate\",
				instance = $( this ).data( \"datepicker\" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
				dates.not( this ).datepicker( \"option\", option, date );
			}
		});
	});

	</script>
	";

	
	#Duration slider
	#Get min and max
	$DurationLow = floor(DB::column('SELECT DISTINCT `Duration` FROM `Sounds` WHERE `Duration` IS NOT NULL AND `SoundStatus`!=9 ' . $qf_check . ' ORDER BY `Duration` LIMIT 1'));
	$DurationHigh = ceil(DB::column('SELECT DISTINCT `Duration` FROM `Sounds` WHERE `Duration` IS NOT NULL AND `SoundStatus`!=9 ' . $qf_check . ' ORDER BY `Duration` DESC LIMIT 1'));

	echo "<script type=\"text/javascript\">
		$(function() {
			$( \"#durationslider\" ).slider({
			range: true,
				min: $DurationLow,
				max: $DurationHigh,
				values: [ $DurationLow, $DurationHigh ],
				slide: function( event, ui ) {
					$( \"#startDuration\" ).val( $( \"#durationslider\" ).slider( \"values\", 0 ));
					$( \"#endDuration\" ).val( $( \"#durationslider\" ).slider( \"values\", 1 ));
				}
			});
			$( \"#startDuration\" ).val( $( \"#durationslider\" ).slider( \"values\", 0 ));
			$( \"#endDuration\" ).val( $( \"#durationslider\" ).slider( \"values\", 1 ));
			});
		</script>";
#	}

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
echo "</head>\n";

if ($map_only=="1"){
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
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">

			<?php

			echo "<h2>Welcome to $app_custom_name</h2>";

			echo "<h4>$app_custom_text</h4>";

			$no_Collections = DB::column('SELECT COUNT(DISTINCT ColID) FROM `Sounds` WHERE SoundStatus!=9 ' . $qf_check);
			$no_sounds = DB::column('SELECT COUNT(*) FROM `Sounds` WHERE SoundStatus!=9 ' . $qf_check);
			$no_sites = DB::column('SELECT COUNT(DISTINCT SiteID) FROM `Sounds` WHERE SoundStatus!=9 ' . $qf_check);
			

			#from old wrapper
			$browse_map_link = "browse_map.php";
			$db_browse_link = "db_browse.php";
			$db_filedetails_link = "db_filedetails.php";
			$advancedsearch_link = "results.php";


			if ($no_sounds > 0) {
				$no_sounds_f = number_format($no_sounds);
				$no_Collections_f = number_format($no_Collections);
				$no_sites_f = number_format($no_sites);		
				
				echo "<h4>This archive has $no_sounds_f soundfiles ";
				if ($no_sites>0){
					echo "from $no_sites_f sites ";
					}
				echo "in $no_Collections_f ";
					if ($no_Collections==1) {
						echo "collection.</h4>";
						}
					else {
						echo "collections.</h4>";
						}
				}
		
			flush(); @ob_flush();

		if ($map_only=="1"){
			echo "<hr noshade></div>";
			require("include/index_map_body.php");
			}
			

				echo "<h3>Main Menu</h3>";
				
					if ($pumilio_loggedin == TRUE) {
						if ($pumilio_admin == TRUE || $allow_upload){
							echo "<form action=\"add.php\" method=\"GET\">
									<input type=submit value=\" Add files to the archive \" class=\"fg-button ui-state-default ui-corner-all\">
								</form>
							<hr noshade>";
							}
						}

					require("include/db_select.php");
					require("include/site_select.php");

					if ($use_googlemaps=="1" || $use_googlemaps=="3") {
						if ($no_sounds==0) {
							echo "This archive has no sounds yet.";
							}
						else {
							echo "<hr noshade>
							<p><strong>Browse the archive using the Google Maps system:</strong></p>
								<form action=\"$browse_map_link\" method=\"GET\">";
							if ($special_wrapper==TRUE){
								echo "<input type=\"hidden\" name=\"page\" value=\"browse_map\">\n";
								}
							echo "<input type=submit value=\" Open GoogleMaps \" class=\"fg-button ui-state-default ui-corner-all\">
								</form>";
							}
						}
				

				#Search
				echo "<h3>Search</h3>";
					require("include/mainsearch.php");


				#Compare sites
				if ($sidetoside_comp=="1" || $sidetoside_comp=="") {
					echo "<h3>Side-to-side comparison</h3>
						
						<p>Select up to three sites to compare their sounds side-to-side on a particular date.</p>";
					require("include/comparesites.php");

					}

				#Tag cloud
				if ($use_tags=="1" || $use_tags=="") {
					echo "<h3>Tag cloud</h3>
						<p>Select sounds to browse according to their tags.";
					require("include/tagcloud.php");

					}

				#Only for logged in users
				if ($pumilio_loggedin == TRUE) {
					echo "<h3>Quality control</h3>\n";
											
					if ($useR==TRUE){
						echo "<form action=\"qc.php\" method=\"GET\">
							<input type=submit value=\" Data extraction for quality control \" class=\"fg-button ui-state-default ui-corner-all\">
						</form>";
						}
					echo "<form action=\"qa.php\" method=\"GET\">
						<input type=submit value=\" Figures for quality control \" class=\"fg-button ui-state-default ui-corner-all\">
						</form>";

					echo "<h3>Other tasks</h3>\n";
						echo "
						
						<form action=\"sample_archive.php\" method=\"GET\">
							<input type=submit value=\" Sample the archive \" class=\"fg-button ui-state-default ui-corner-all\">
						</form>";
						/*
						echo "<form action=\"script_jobs.php\" method=\"GET\">
							<input type=submit value=\" Script jobs \" class=\"fg-button ui-state-default ui-corner-all\">
						</form>";
						*/
						echo "<form action=\"export_marks.php\" method=\"GET\">
							<input type=submit value=\" Export marks data \" class=\"fg-button ui-state-default ui-corner-all\">
						</form>";

					
					echo "<h3>Upload site photographs</h3>
					<p>Upload photographs of the sites to serve as a reference.</p>
					<p><form action=\"photoupload.php\" method=\"GET\">
					<input type=submit value=\" Upload a photo from your computer \" class=\"fg-button ui-state-default ui-corner-all\">
					</form></p>\n";
					}

			
			
			?>

		<br>
		
<?php
require("include/bottom.php");
?>


</body>
</html>

<?php
if ($use_leaflet == TRUE){
	require("include/leaflet2.php");
}

#Close session to release script from php session
	session_write_close();
	flush(); @ob_flush();
	#Delete old temp files
	delete_old('tmp/', 30);
?>