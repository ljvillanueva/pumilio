<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require($config_file);
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");

#Sanitize inputs
$site1=filter_var($_GET["site1"], FILTER_SANITIZE_NUMBER_INT);
$site2=filter_var($_GET["site2"], FILTER_SANITIZE_NUMBER_INT);
$site3=filter_var($_GET["site3"], FILTER_SANITIZE_NUMBER_INT);
$date=filter_var($_GET["date"], FILTER_SANITIZE_STRING);

$Date_h=query_one("SELECT DATE_FORMAT('$date','%d-%b-%Y') AS Date_h", $connection);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Compare Sounds Between Sites</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>
 
<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type="text/javascript" src="js/jquery.form.js"></script> 
  

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
<body>

<?php

if ($site1=="" && $site2=="" && $site3=="") {
	echo "<div class=\"error\"> <img src=\"images/exclamation.png\"> You should select at least one site. Please go back and try again.</div>
	</body>
	</html>";
	die();
	}


if ($site2==""){
	unset($site2);
	}
if ($site3==""){
	unset($site3);
	}

if ($site1=="") {
	echo "<div class=\"error\"> <img src=\"images/exclamation.png\"> You must make a selection for the first site. Please go back and try again.</div>
	</body>
	</html>";
	die();
	}
			
if ($date=="") {
	echo "<div class=\"error\"> <img src=\"images/exclamation.png\"> You should select a date to display. Please go back and try again.</div>
	</body>
	</html>";
	die();
	}

$no_sounds1=query_one("SELECT COUNT(*) from Sounds WHERE Date='$date' AND SiteID='$site1' AND SoundStatus!='9'", $connection);
if (isset($site2)) {
	$no_sounds2=query_one("SELECT COUNT(*) from Sounds WHERE Date='$date' AND SiteID='$site2' AND SoundStatus!='9'", $connection);
	$site2q="OR SiteID='$site2'";
	}
if (isset($site3)) {
	$no_sounds3=query_one("SELECT COUNT(*) from Sounds WHERE Date='$date' AND SiteID='$site3' AND SoundStatus!='9'", $connection);
	$site3q="OR SiteID='$site3'";
	}
		
$no_sounds=$no_sounds1+$no_sounds2+$no_sounds3;
if ($no_sounds==0) {
	echo "<div class=\"notice\"> <img src=\"images/error.png\"> There are no results for the sites and date selected. 
		Please go back and try again.</div>
		</body>
		</html>";
	die();
	}


#Get times
$query_times = "SELECT DATE_FORMAT(Time, '%H:%i') AS Time FROM Sounds WHERE Date='$date' AND (SiteID=$site1 $site2q $site3q) GROUP BY Time";
$result_times=query_several($query_times, $connection);
$nrows_times = mysqli_num_rows($result_times);

?>

<!--Blueprint container-->
<div class="container">
	<?php
		require("include/topbar.php");
	?>
	<div class="span-24 last">
		<hr noshade>
	</div>

	<?php
	flush();

		echo "<div class=\"span-12\"><h4>Date: $Date_h - total of $no_sounds sounds in the sites selected</h4>
			</div>\n";

		#Navigation for next and previous day
		echo "<div class=\"span-12 last\" style=\"text-align: center;\">";
		
		$prev_date=query_one("SELECT DATE_SUB('$date', INTERVAL 1 DAY)", $connection);
		$prev_date_h=query_one("SELECT DATE_FORMAT('$prev_date','%d-%b-%Y')", $connection);
		$next_date=query_one("SELECT DATE_ADD('$date', INTERVAL 1 DAY)", $connection);
		$next_date_h=query_one("SELECT DATE_FORMAT('$next_date','%d-%b-%Y')", $connection);			
		
		echo "<p><a href=\"compare.php?date=$prev_date&site1=$site1&site2=$site2&site3=$site3\"><img src=\"images/arrowleft.png\"> $prev_date_h</a> | 
		$Date_h | 
		<a href=\"compare.php?date=$next_date&site1=$site1&site2=$site2&site3=$site3\">$next_date_h <img src=\"images/arrowright.png\"></a>";
		
		echo "</div>";

	?>
	<div class="span-24 last" id="loadingdiv">
		<h5 class="highlight2 ui-corner-all">Please wait... loading... <img src="images/ajax-loader.gif" border="0" /></h5>
	</div>		
	<?php
	flush();

		#Line identifying the sites
		echo "<div class=\"span-24 last\">&nbsp;</div>";

		for ($t=0;$t<$nrows_times;$t++) {
			$row_times = mysqli_fetch_array($result_times);
			extract($row_times);

			if (($t % 5) == 0) {
				#Headings:
				$site1_name=query_one("SELECT SiteName FROM Sites WHERE SiteID='$site1'", $connection);
				$site1_name=truncate2($site1_name, 60);
				echo "<div class=\"span-8\" style=\"background-color:#CCCCCC;\"><strong>$site1_name</strong></div>\n";
				
				if (isset($site2)) {
					$site2_name=query_one("SELECT SiteName FROM Sites WHERE SiteID='$site2'", $connection);
					$site2_name=truncate2($site2_name, 60);
					echo "<div class=\"span-8\" style=\"background-color:#CCCCCC;\"><strong>$site2_name</strong></div>\n";
					}
				else {
					echo "<div class=\"span-8\" style=\"background-color:#CCCCCC;\">&nbsp;</div>\n";
					}
				if (isset($site3)) {
					$site3_name=query_one("SELECT SiteName FROM Sites WHERE SiteID='$site3'", $connection);
					$site3_name=truncate2($site3_name, 60);
					echo "<div class=\"span-8 last\" style=\"background-color:#CCCCCC;\"><strong>$site3_name</strong></div>\n";
					}
				else {
					echo "<div class=\"span-8 last\" style=\"background-color:#CCCCCC;\">&nbsp;</div>\n";
					}
				}

			$this_time=$Time . ":%";

			#Column 1 for site 1
			echo "<div class=\"span-8\">";

			$query1 = "SELECT * from Sounds WHERE SiteID=$site1 AND Time LIKE '$this_time' AND Date='$date' AND SoundStatus!='9' ORDER BY Time LIMIT 1";
			$result1 = mysqli_query($connection, $query1)
				or die (mysqli_error($connection));
			$nrows1 = mysqli_num_rows($result1);

			if ($nrows1==1) {
				$row1 = mysqli_fetch_array($result1);
				extract($row1);

				#Check if there are images
				$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
				$sound_images=query_one($query_img, $connection);
				if ($sound_images!=6) {
					include("include/make_figs.php");
					}

				$query_img1 = "SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-small'";
				$sound_images1=query_one($query_img1, $connection);
				$sound_spectrogram=$sound_images1;
				
				echo "<a href=\"db_filedetails.php?SoundID=$SoundID\">
					<img src=\"sounds/images/$ColID/$DirID/$sound_spectrogram\" width=\"300\" height=\"150\"><br>
					$SoundName</a><br>";
				if ($Date!="")
					echo "Date: $Date";
				if ($Time!="")
					echo " $Time";

				echo "</div>";				
				flush();
				}
			else {
				echo "&nbsp;</div>";
				}

			#Column 2 for site 2
			echo "<div class=\"span-8\">";

			if (isset($site2)) {
				$query2 = "SELECT * from Sounds WHERE SiteID=$site2 AND Time LIKE '$this_time' AND Date='$date' AND SoundStatus!='9' ORDER BY Time LIMIT 1";
				$result2 = mysqli_query($connection, $query2)
					or die (mysqli_error($connection));
				$nrows2 = mysqli_num_rows($result2);

				if ($nrows2==1) {
					$row2 = mysqli_fetch_array($result2);
					extract($row2);

					#Check if there are images
					$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
					$sound_images=query_one($query_img, $connection);
					if ($sound_images!=6) {
						include("include/make_figs.php");
						}

					$query_img1 = "SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-small'";	
					$sound_images1=query_one($query_img1, $connection);
					$sound_spectrogram=$sound_images1;
				
					echo "<a href=\"db_filedetails.php?SoundID=$SoundID\">
						<img src=\"sounds/images/$ColID/$DirID/$sound_spectrogram\" width=\"300\" height=\"150\"><br>
						$SoundName</a><br>";
					if ($Date!="")
						echo "Date: $Date";
					if ($Time!="")
						echo " $Time";

					echo "</div>";				
					flush();
					}
				else {
					echo "&nbsp;</div>";
					}
				}
			else {
				echo "&nbsp;</div>";
				}
				
				
			#Column 3 for site 3
			echo "<div class=\"span-8 last\">";

			if (isset($site3)) {
				$query3 = "SELECT * from Sounds WHERE SiteID=$site3 AND Time LIKE '$this_time' AND Date='$date' AND SoundStatus!='9' ORDER BY Time LIMIT 1";
				$result3 = mysqli_query($connection, $query3)
					or die (mysqli_error($connection));
				$nrows3 = mysqli_num_rows($result3);

				if ($nrows3==1) {
					$row3 = mysqli_fetch_array($result3);
					extract($row3);

					#Check if there are images
					$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
					$sound_images=query_one($query_img, $connection);
					if ($sound_images!=6) {
						include("include/make_figs.php");
						}

					$query_img1 = "SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-small'";
					$sound_images1=query_one($query_img1, $connection);
					$sound_spectrogram=$sound_images1;
					
					echo "<a href=\"db_filedetails.php?SoundID=$SoundID\">
						<img src=\"sounds/images/$ColID/$DirID/$sound_spectrogram\" width=\"300\" height=\"150\"><br>
						$SoundName</a><br>";
					if ($Date!="")
						echo "Date: $Date";
					if ($Time!="")
						echo " $Time";

					echo "</div>";				
					flush();
					}
				else {
					echo "&nbsp;</div>";
					}
				}
			else {
				echo "&nbsp;</div>";
				}

			echo "<div class=\"span-24 last\">&nbsp;</div>";

			}

		?>

	<div class="span-24 last">
		&nbsp;
		<script type="text/javascript">
		function hidediv()
		      {
			loadingdiv.style.visibility= "hidden";
			loadingdiv.style.height= "0";
		      };
	
		hidediv();
		</script>
		<style type="text/css">
		#loadingdiv {visibility:hidden;
				height:0;}
		</style>
	</div>
	<div class="span-24 last">
		<?php
		require("include/bottom.php");
		?>

		</div>
	</div>

</body>
</html>
