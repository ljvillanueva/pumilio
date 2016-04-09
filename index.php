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



####################################################
$use_googlemaps=FALSE;
$use_leaflet=TRUE;
#Get points from the database
$results_map = DB::fetch('SELECT `Sites`.`SiteID`, `Sites`.`SiteLat`, `Sites`.`SiteLon`, `Sites`.`SiteName` FROM `Sites`, `Sounds` WHERE `Sites`.`SiteLat` IS NOT NULL AND `Sites`.`SiteLon` IS NOT NULL AND `Sites`.`SiteID`=`Sounds`.`SiteID` AND `Sounds`.`SoundStatus` != 9 GROUP BY `Sites`.`SiteID`, `Sites`.`SiteLat`, `Sites`.`SiteLon`, `Sites`.`SiteName`');
$no_results_map = count($results_map);
####################################################


require("include/index_map_head.php");


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

if ($use_leaflet == FALSE){
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
			<h1>$app_custom_name</h1>";

			$btn1text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn1text'));
			$btn1url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn1url'));
			$btn2text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn2text'));
			$btn2url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn2url'));
			$btn3text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn3text'));
			$btn3url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn3url'));
			$btn4text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn4text'));
			$btn4url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn4url'));

			echo "<div class=\"pull-right\">";

				if ($btn1text != "" && $btn1url != ""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$btn1url\" role=\"button\">$btn1text</a></p>";
					}
				if ($btn2text != "" && $btn2url != ""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$btn2url\" role=\"button\">$btn2text</a></p>";
					}
				if ($btn3text != "" && $btn3url != ""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$btn3url\" role=\"button\">$btn3text</a></p>";
					}
				if ($btn4text != "" && $btn4url != ""){
					echo "<p><a class=\"btn btn-primary btn-lg\" href=\"$btn4url\" role=\"button\">$btn4text</a></p>";
					}


			echo "</div>";

			$app_custom_text = DB::column('SELECT `Value` FROM `PumilioSettings` WHERE Settings=?', array('app_custom_text'));

			if ($app_custom_text == "") {
				$app_custom_text = "Pumilio is a free and open source sound archive manager for the visualization and manipulation of sound files.";
				}

			echo "<p>$app_custom_text</p>\n";


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

			#include("include/check_system.php");


			echo "<div id=\"map\">Your browser does not have JavaScript enabled or can not connect to the tile server. Please contact your administrator.</div>\n";


				#Tag cloud
				/*if ($use_tags=="1" || $use_tags=="") {
					echo "<h3>Tag cloud</h3>
						<p>Select sounds to browse according to their tags.";
					require("include/tagcloud.php");

					}
				*/



			echo " <div class=\"row\">
			        <div class=\"col-lg-4 text-center\">
			          <h2><span class=\"glyphicon glyphicon-cloud-upload\" aria-hidden=\"true\"></span> Add sounds<br>to this archive</h2>
			          <p><a class=\"btn btn-primary\" href=\"add.php\" role=\"button\">Add sounds »</a></p>
			        </div>
			        <div class=\"col-lg-4 text-center\">
			          <h2><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span> Explore the<br>sound archive</h2>
			          <p><a class=\"btn btn-primary\" href=\"search.php\" role=\"button\">Search »</a></p>
			       </div>
			        <div class=\"col-lg-4 text-center\">
			          <h2><span class=\"glyphicon glyphicon-tasks\" aria-hidden=\"true\"></span> Data extraction<br>and analysis</h2>
			          <p><a class=\"btn btn-primary\" href=\"data.php\" role=\"button\">Explore »</a></p>
			        </div>
			       </div>";
		?>


<hr noshade>
<?php

$thanks_text = DB::column('SELECT `Value` FROM `PumilioSettings` WHERE Settings=?', array('thanks_text'));

	if ($thanks_text != "") {
		echo "<dl class=\"dl-horizontal\">
		  <dt>Acknowledgements</dt>
		  <dd>$thanks_text</dd>
		</dl>";
		}

require("include/bottom.php");
?>

</body>
</html>

<?php
require("include/leaflet2.php");


#Close session to release script from php session
	session_write_close();
	flush(); @ob_flush();
	#Delete old temp files
	delete_old('tmp/', 30);
?>
