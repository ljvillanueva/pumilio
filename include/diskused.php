<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
	require("get_css3_include.php");
	require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px;">

<?php



if (isset($_GET["run"])){
	$run = $_GET['run'];
	}
else{
	$run = FALSE;
	}
	
if ($run){
	set_time_limit(0);

	function du( $dir ) {
		$res = `du -sb $dir`;             // Unix command
		$B=explode( 'sounds/', $res); // Parse result
		return $B[0];
		}

	$sounds_size = formatsize(du("../sounds/sounds/"));
	$previewsounds_size = formatsize(du("../sounds/previewsounds/"));
	$images_size = formatsize(du("../sounds/images/"));
	$total_size=formatsize(du("../sounds/images/") + du("../sounds/previewsounds/") + du("../sounds/sounds/"));
	echo "<h2>Disk usage: $total_size</h2>
		<ul>
			<li>Sound files: $sounds_size
			<li>Sound preview files (mp3): $previewsounds_size
			<li>Image files (spectrograms and waveforms): $images_size
		</ul>";
		
	#Disk free space check
		$dir_to_check=$absolute_dir . "/tmp";
		$df=disk_free_space($dir_to_check);
		$dfh=formatsize($df);
		echo "<p>";
		if ($df<5000000000) {
			echo "<div class=\"alert alert-warning\"><strong>Warning</strong>: ";
			}
		else {
			echo "<div class=\"alert alert-success\">";	
			}
		echo "<h3>Disk free space: $dfh</h3></div>";

		echo "<br><p><a href=\"#\" onClick=\"window.close();\">Close window</a>";
}
else{
	echo "<meta http-equiv=\"refresh\" content=\"1;url=diskused.php?run=TRUE\">

	</head>
	<body>

	<div style=\"padding: 10px;\">

		<br><br><br>
		<h3>Working... 
		<br>Please wait... <i class=\"fa fa-cog fa-spin\"></i>
		</h3>

		<br><br><br>
	<br><p><a href=\"#\" onClick=\"window.close();\">Cancel and close window</a>";
}
?>


</div>

</body>
</html>
