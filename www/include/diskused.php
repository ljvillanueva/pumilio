<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px;">

<?php

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
		echo "<div class=\"notice\"><strong>Warning</strong>: ";
		}
	else {
		echo "<div class=\"success\">";	
		}
	echo "<h3>Disk free space: $dfh</h3></div>";

?>

<br><p><a href="#" onClick="window.close();">Close window</a>

</div>

</body>
</html>
