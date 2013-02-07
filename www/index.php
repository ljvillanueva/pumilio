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

#If user is not logged in, add check for QF
if (!sessionAuthenticate($connection)) {
	$qf_check = "AND Sounds.QualityFlagID>='$default_qf'";
	}
else {
	$qf_check = "";
	}

echo "<html>
<head>\n";

echo "<title>$app_custom_name</title>";
require("include/get_css.php");

echo "<!-- IE Fix for accordion http://dev.jqueryui.com/ticket/4444 -->
	<style type=\"text/css\">
	.ui-accordion-content{ zoom: 1; }
	</style>\n";

require("include/get_jqueryui.php");

$map_only=query_one("SELECT Value from PumilioSettings WHERE Settings='map_only'", $connection);

if ($map_only=="1"){
	require("include/index_map_head.php");
	}
else{
	echo "
	<script type=\"text/javascript\">
	$(function() {
		$(\"#accordion\").accordion({
			autoHeight: false,
			Collapsible: true
		});
	});
	</script>

	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script src=\"$app_url/js/jquery.validate.js\" type=\"text/javascript\"></script>

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


	$DateLow=query_one("SELECT DATE_FORMAT(Date,'%Y, %c-1, %e') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date LIMIT 1", $connection);
	$DateHigh=query_one("SELECT DATE_FORMAT(Date,'%Y, %c-1, %e') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
	$DateLow1=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date LIMIT 1", $connection);
	$DateHigh1=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
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

	#Time 
	#From https://github.com/perifer/timePicker
	echo "
		<style type=\"text/css\" media=\"all\">@import \"js/timePicker.css\";</style>
		<script type=\"text/javascript\" src=\"js/jquery.timePicker.js\"></script>
		<script type=\"text/javascript\">
		jQuery(function() {
		// An example how the two helper functions can be used to achieve 
		// advanced functionality.
		// - Linking: When changing the first input the second input is updated and the
		//   duration is kept.
		// - Validation: If the second input has a time earlier than the firs input,
		//   an error class is added.

		// Use default settings
		$(\"#startTime, #endTime\").timePicker({
			startTime: \"00:00\",
			endTime: \"23:59\",
			show24Hours: true,
			step: 1
			});
		    
		// Store time used by duration.
		var oldTime = $.timePicker(\"#startTime\").getTime();

		// Keep the duration between the two inputs.
		$(\"#startTime\").change(function() {
		  if ($(\"#endTime\").val()) { // Only update when second input has a value.
		    // Calculate duration.
		    var duration = ($.timePicker(\"#endTime\").getTime() - oldTime);
		    var time = $.timePicker(\"#startTime\").getTime();
		    // Calculate and update the time in the second input.
		    $.timePicker(\"#endTime\").setTime(new Date(new Date(time.getTime() + duration)));
		    oldTime = time;
		  }
		});
		// Validate.
		$(\"#endTime\").change(function() {
		  if($.timePicker(\"#startTime\").getTime() > $.timePicker(this).getTime()) {
		    $(this).addClass(\"error\");
		    $( \"#datemsg\" ).addClass(\"error\");
		    $( \"#datemsg\" ).html(\"The end time can not be after start time.\");
		  }
		  else {
		    $(this).removeClass(\"error\");
		    $( \"#datemsg\" ).removeClass(\"error\");
	    	    $( \"#datemsg\" ).html(\"\");
		  }
		});
		});
		</script>
		";
	
	#Duration slider
	#Get min and max
	$DurationLow=floor(query_one("SELECT DISTINCT Duration FROM Sounds WHERE Duration IS NOT NULL AND SoundStatus!='9' $qf_check ORDER BY Duration LIMIT 1", $connection));
	$DurationHigh=ceil(query_one("SELECT DISTINCT Duration FROM Sounds WHERE Duration IS NOT NULL AND SoundStatus!='9' $qf_check ORDER BY Duration DESC LIMIT 1", $connection));

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
	}

if ($use_googleanalytics) {
	echo $googleanalytics_code;
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

			echo "<h2>Welcome to $app_custom_name $this_time</h2>";

			echo "<h4>$app_custom_text</h4>";

			$no_Collections=query_one("SELECT COUNT(*) FROM Collections", $connection);
			$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);
			$no_sites=query_one("SELECT COUNT(DISTINCT SiteID) FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);

			#Special when in iframe or inside another site
			if ($special_wrapper==TRUE){
				$browse_map_link = "$wrapper";
				$db_browse_link = "$wrapper";
				$db_filedetails_link = "$wrapper";
				$advancedsearch_link = "$wrapper";
				}
			else {
				$browse_map_link = "browse_map.php";
				$db_browse_link = "db_browse.php";
				$db_filedetails_link = "db_filedetails.php";
				$advancedsearch_link = "advancedsearch.php";
				}

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
			if (is_user_admin2($username, $connection)) {
				echo "<hr noshade>
				<p><strong><a href=\"add.php\">Add files to the archive</a></strong><br>";
				}
			}
		else{
			echo "<!--JQuery accordion container-->
			<div id=\"accordion\">";

				echo "<h3><a href=\"#\">Main Menu</a></h3>
					<div>";
				
					#Check if user can edit files (i.e. has admin privileges)
					$username = $_COOKIE["username"];

					if (is_user_admin2($username, $connection)) {
						echo "<p><strong><a href=\"add.php\">Add files to the archive</a></strong><br>
						<hr noshade>";
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
												
				echo "</div>";

				#Search
				echo "<h3><a href=\"#\">Search</a></h3>
					<div>";
					require("include/mainsearch.php");
				echo "</div>";

				#Compare sites
				$sidetoside_comparison=query_one("SELECT Value from PumilioSettings WHERE Settings='sidetoside_comp'", $connection);
				if ($sidetoside_comparison=="1" || $sidetoside_comparison=="") {
					echo "<h3><a href=\"#\">Side-to-side comparison</a></h3>
						<div>
						<p>Select up to three sites to compare their sounds side-to-side on a particular date.</p>";
					require("include/comparesites.php");
					echo "</div>";
					}

				#Tag cloud
				$use_tags=query_one("SELECT Value from PumilioSettings WHERE Settings='use_tags'", $connection);
				if ($use_tags=="1" || $use_tags=="") {
					echo "<h3><a href=\"#\">Tag cloud</a></h3>
						<div>
						<p>Select sounds to browse according to their tags.";
					require("include/tagcloud.php");
					echo "</div>";
					}

				#Only for logged in users
				if (sessionAuthenticate($connection)) {
					echo "<h3><a href=\"#\">Quality control</a></h3>
					<div>\n";
						echo "<p><a href=\"qc.php\">Data extraction for quality control</a>
							<p><a href=\"qa.php\">Figures for quality control</a>
					</div>";

					echo "<h3><a href=\"#\">Other tasks</a></h3>
					<div>\n";
						echo "<ul>
							<li><a href=\"sample_archive.php\">Sample the archive</a></li>
							<li><a href=\"script_jobs.php\">Script jobs</a></li>
							<li><a href=\"export_marks.php\">Export marks data</a></li>\n
						</ul>
					</div>";

					#Special section for plugins
					$dir="plugins/";
		 			$plugins=scandir($dir);
		 
			 		if (count($plugins)>0) {
			 			for ($a=0; $a<count($plugins); $a++) {
		 					if (strpos(strtolower($plugins[$a]), ".php")) {
								$lines = file("plugins/$plugins[$a]");
								echo "<h3><a href=\"#\">$lines[2]</a></h3>
									<div>\n";

									require("plugins/$plugins[$a]");
								
						 		echo "\n</div>\n";
		 						}
			 				}
			 			}

					if ($allow_upload) {
						echo "<h3><a href=\"#\">Upload a file</a></h3>
						<div>
						<p>Upload a file to the system from your computer or from the web for
							visualization and analysis. <br>
							The files uploaded with this method
							will not be added to the database.</p>
						<p><form action=\"fileupload.php\" method=\"GET\">
						<input type=submit value=\" Upload a file from your computer \" class=\"fg-button ui-state-default ui-corner-all\">
						</form></p>
				
						<p><form action=\"file_from_web.php\" method=\"GET\">
						<input type=submit value=\" Obtain a file from the web \" class=\"fg-button ui-state-default ui-corner-all\">
						</form></p>
						</div>\n";
						}


					echo "<h3><a href=\"#\">Upload site photographs</a></h3>
					<div>
					<p>Upload photographs of the sites to serve as a reference.</p>
					<p><form action=\"photoupload.php\" method=\"GET\">
					<input type=submit value=\" Upload a photo from your computer \" class=\"fg-button ui-state-default ui-corner-all\">
					</form></p>
					</div>\n";
					}

			
			
				echo "</div>";
			}
			?>

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
<?php
	session_write_close();
	flush(); @ob_flush();
	#Delete temp files older than 3 days
	delete_old('tmp/', 3);
?>
