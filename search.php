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

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");

	echo "
	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script src=\"js/jquery.validate.js\"></script>";

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
				
		<h2>Search</h2>
			<?php


			echo "<p>Search sounds in the archive using several options:<br><hr noshade>";

			$no_sounds_s = query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);
			if ($no_sounds_s == 0){
				echo "<p>This archive does not have any sounds.";
				}
			else {

				#Search by ID
				echo "<div class=\"row\">
				<div class=\"col-lg-6\">";



					#By site
					echo "<div class=\"panel panel-primary\">
								<div class=\"panel-heading\">
									<h3 class=\"panel-title\">Browse a Site</h3>
								</div>
								<div class=\"panel-body\">
									<form action=\"browse_site.php\" method=\"GET\" class=\"form-inline\">

										<label for=\"\SiteID\">Site &nbsp;&nbsp;</label>
										<select name=\"SiteID\" id=\"SiteID\" class=\"form-control\">";
												
											#Get all dates
											$query_sites = "SELECT SiteID, SiteName FROM Sites ORDER BY SiteName";
											$result_sites = query_several($query_sites, $connection);
											$nrows_sites = mysqli_num_rows($result_sites);

											if ($nrows_sites > 0) {
												for ($s = 0; $s < $nrows_sites; $s++) {
													$row_sites = mysqli_fetch_array($result_sites);
													extract($row_sites);

													$check_site = query_one("SELECT COUNT(*) FROM Sounds WHERE SiteID='$SiteID'", $connection);

													if ($check_site > 0){
														echo "\n<option value=\"$SiteID\">$SiteName</option>";
														}
													}
												}
										echo "</select>
										<button type=\"submit\" class=\"btn btn-primary\"> Browse</button>
									</form>
								</div>
							</div>";


					#By collection
					echo "<div class=\"panel panel-primary\">
								<div class=\"panel-heading\">
									<h3 class=\"panel-title\">Browse a Collection</h3>
								</div>
								<div class=\"panel-body\">
									<form action=\"browse_col.php\" method=\"GET\" class=\"form-inline\">
										<label for=\"\ColID\">Collection &nbsp;&nbsp;</label>
										<select name=\"ColID\" id=\"ColID\" class=\"form-control\">";
						

										$query_dates = "SELECT ColID, CollectionName FROM Collections ORDER BY CollectionName";
										$result_dates = query_several($query_dates, $connection);
										$nrows_dates = mysqli_num_rows($result_dates);

										if ($nrows_dates > 0) {
											for ($d = 0; $d < $nrows_dates; $d++)	{
												$row_dates = mysqli_fetch_array($result_dates);
												extract($row_dates);
												echo "\n<option value=\"$ColID\">$CollectionName</option>";
												}
											}
									echo "</select> 
										
										<button type=\"submit\" class=\"btn btn-primary\">Browse</button>
									</form>
								</div>
							</div>";




					echo "</div><div class=\"col-lg-6\">




							<div class=\"panel panel-primary\">
								<div class=\"panel-heading\">
									<h3 class=\"panel-title\">Search by Sound ID</h3>
								</div>
								<div class=\"panel-body\">
									<form action=\"db_filedetails.php\" method=\"GET\" class=\"form-inline\">
									 	<label for=\"SoundID\">Sound ID &nbsp;&nbsp;</label>
										<input type=\"text\" name=\"SoundID\" id=\"SoundID\" class=\"form-control\" placeholder=\"SoundID\">
										
										<button type=\"submit\" class=\"btn btn-primary\"> Search </button>
									</form>
								</div>
							</div>



					</div></div>\n";



				echo "<br>
				<div class=\"row\">
					<div class=\"col-lg-8\">";
					require("include/mainsearch.php");
				echo "</div>

					<div class=\"col-lg-4\"></div>
				</div>
				<br>";



			}
		?>

		<br>
		</div>
		
<?php
	require("include/bottom.php");
?>


</body>
</html>