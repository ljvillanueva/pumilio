<html>
<head>
<title>Pumilio - Software Check</title>

<?php
 require("../config.php");

$absolute_dir=dirname(__FILE__);

$absolute_dir = preg_replace('/include$/', '', $absolute_dir);

$app_dir = substr($absolute_dir, strlen($_SERVER['DOCUMENT_ROOT']));

$app_url = "http://" . $_SERVER['SERVER_NAME'] . $app_dir;

$app_url = rtrim(preg_replace('/include$/', '', $app_url), "/");

 require("get_css_include.php");
 require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px;">

<?php

// Test if the required programs are installed.
// SoX
	unset($out, $retval);
	exec('sox --version', $soxout, $soxretval);
	$soxout=explode("v",$soxout[0]);
	$soxout=$soxout[1];
	if ($soxretval!=0) {
		echo "<div class=\"error\"><strong>SoX is not installed</strong>. 
			Please install by using this command: sudo apt-get install sox libsox*</div>";
		}
	else {
		echo "<div class=\"success\"><strong>Sox is installed</strong>.</div>";
		}

// Test if the required programs are installed.
// SoX
	unset($out, $retval);
	exec('soxi', $out, $retval);
	if ($retval!=0) {
		if ($soxretval==0) {
			echo "<div class=\"error\"><strong>The soxi utility from SoX is not installed</strong>. The version of SoX installed is $soxout, you need at least version 14.3.0. Download the latest version from http://sox.sourceforge.net/</div>";
			}
		else {
			echo "<div class=\"error\"><strong>The soxi utility from SoX is not installed</strong>. Download the latest version of SoX from http://sox.sourceforge.net/</div>";
			}
		}



if ($AudioPreviewFormat=="ogg"){
	// ogg encoder
	unset($out, $retval);
	exec('dir2ogg --version', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>The OGG encoder is not installed</strong>. If ogg encoding is desired, please install by using this command: sudo apt-get install dir2ogg</div>";
		}
	else {
		echo "<div class=\"success\"><strong>The OGG encoder is installed</strong>.</div>";
		}
	}
elseif ($player_format=="mp3"){
	// LAME
	unset($out, $retval);
	exec('lame --version', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>The LAME MP3 encoder is not installed</strong>. If mp3 encoding is desired, please install by using this command: sudo apt-get install lame liblame*</div>";
		}
	else {
		echo "<div class=\"success\"><strong>LAME MP3 encoder is installed</strong>.</div>";
		}
	}




// FLAC
	unset($out, $retval);
	exec('flac --version', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>FLAC is not installed</strong>. If flac support is desired, please install by using this command: sudo apt-get install flac</div>";
		}
	else {
		echo "<div class=\"success\"><strong>FLAC is installed</strong>.</div>";
		}


// Imagemagick
	unset($out, $retval);
	exec('convert --version', $out, $retval);
	if ($retval>1) {
		echo "<div class=\"error\"><strong>Imagemagick is not installed</strong>. Please install by using this command: sudo apt-get install imagemagick</div>";
		}
	else {
		echo "<div class=\"success\"><strong>Imagemagick is installed</strong>.</div>";
		}



// audiolab
	unset($out, $retval);
	exec($absolute_dir . '/include/check_audiolab.py', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>The Python module 'audiolab' is not installed</strong>. Please visit <a href=\"http://www.scipy.org/scipy/scikits/wiki/AudioLab\" target=_blank>http://www.scipy.org/scipy/scikits/wiki/AudioLab</a></div>";
		$audiolab=1;
		}
	else {
		echo "<div class=\"success\"><strong>The audiolab Python module is installed</strong>.</div>";
		}



// svt.py script
	unset($out, $retval);
	exec($absolute_dir . '/include/svt.py -v', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>The svt script is not installed or can not run</strong>.";
		if ($audiolab==1)
			echo " Please install audiolab first.";
		echo "</div>";
		}
	else {
		echo "<div class=\"success\"><strong>The svt script is installed</strong>.</div>";
		}


//Check for tmp folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/tmp/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("../tmp/") || !is_writable("../tmp/")) {
		echo "<div class=\"error\"><strong>The server can not write to the temporary folder, tmp/, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
		}
	else {
		echo "<div class=\"success\"><strong>Temporary directory permissions are correct</strong>.</div>";
		}

//Check for sounds folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/sounds/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("../sounds/") || !is_writable("../sounds/")) {
		echo "<div class=\"error\"><strong>The server can not write to the archive folder, sounds/, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
		}
	elseif (!is_dir("../sounds/images") || !is_writable("../sounds/images")) {
		echo "<div class=\"error\"><strong>The server can not write to the archive folder, sounds/images, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
		}
	elseif (!is_dir("../sounds/previewsounds") || !is_writable("../sounds/previewsounds")) {
		echo "<div class=\"error\"><strong>The server can not write to the archive folder, sounds/previewsounds, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
		}
	elseif (!is_dir("../sounds/sounds") || !is_writable("../sounds/sounds")) {
		echo "<div class=\"error\"><strong>The server can not write to the archive folder, sounds/sounds, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
		}
	else {
		echo "<div class=\"success\"><strong>Sound archive directory permissions are correct</strong>.</div>";
		}

//Check for sitepictures folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/sitephotos/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("../sitephotos/") || !is_writable("../sitephotos/")) {
		echo "<div class=\"error\"><strong>The server can not write to the site photographs folder, sitephotos/, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
		}
	else {
		echo "<div class=\"success\"><strong>Site photograph directory permissions are correct</strong>.</div>";
		}

?>

<p><a href="#" onClick="window.close();">Close window</a>

</body>
</html>
