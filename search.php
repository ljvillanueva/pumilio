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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name</title>";

require("include/get_css.php");

echo "<!-- IE Fix for accordion http://dev.jqueryui.com/ticket/4444 -->
	<style type=\"text/css\">
	.ui-accordion-content{ zoom: 1; }
	</style>\n";

require("include/get_jqueryui.php");

	echo "
	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script src=\"$app_url/js/jquery.validate.js\"></script>";

	$DateLow = query_one("SELECT DATE_FORMAT(Date,'%Y, %c-1, %e') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date LIMIT 1", $connection);
	$DateHigh = query_one("SELECT DATE_FORMAT(Date,'%Y, %c-1, %e') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
	$DateLow1 = query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date LIMIT 1", $connection);
	$DateHigh1 = query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') FROM Sounds WHERE SoundStatus!='9' $qf_check ORDER BY Date DESC LIMIT 1", $connection);
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
/*
	echo "
		<style type=\"text/css\" media=\"all\">@import \"css/jquery.timepicker.css\";</style>
		<script type=\"text/javascript\" src=\"js/jquery.timepicker.min.js\"></script>
		<script>
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
*/	
	#Duration slider
	#Get min and max
	$DurationLow = floor(query_one("SELECT DISTINCT Duration FROM Sounds WHERE Duration IS NOT NULL AND SoundStatus!='9' $qf_check ORDER BY Duration LIMIT 1", $connection));
	$DurationHigh = ceil(query_one("SELECT DISTINCT Duration FROM Sounds WHERE Duration IS NOT NULL AND SoundStatus!='9' $qf_check ORDER BY Duration DESC LIMIT 1", $connection));

	echo "<script>
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

if ($special_wrapper == TRUE){
	$browse_map_link = "$wrapper";
	$db_browse_link = "$wrapper";
	$db_filedetails_link = "$wrapper";
	$advancedsearch_link = "$wrapper";
	}
else {
	$browse_map_link = "browse_map.php";
	$db_browse_link = "db_browse.php";
	$db_filedetails_link = "db_filedetails.php";
	$advancedsearch_link = "results.php";
	}
		
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
	

#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	

echo "</head>\n";
echo "<body>";

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
			<h3>Search</h3>
			<?php
				require("include/mainsearch.php");
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