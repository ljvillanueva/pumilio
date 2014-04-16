<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

if (isset($_GET["op"])) {
	$op=filter_var($_GET["op"], FILTER_SANITIZE_NUMBER_INT);
	}
else {
	$op = 0;
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name</title>";

require("get_css.php");
require("get_jqueryui.php");
?>

</head>
<body>

<div style="padding: 10px;">

<?php
	echo "<p>";
	if ($op == 1){
		delete_old('../tmp/',3);
		echo "<div class=\"success\" >Deleted temporary files older than 3 days. <a href=\"alerts.php?op=2\">Delete all temporary files</a>.</div>";
		}
	elseif ($op == 2){
		delete_old('tmp/',0);
		$dir = glob("tmp/*", GLOB_ONLYDIR);
			foreach($dir as $this_dir) { 
				    delTree($this_dir);
			}
		echo "<div class=\"success\" >Deleted all the temporary files.</div>";
		}
	
	#Disk free space check
	$dir_to_check=$absolute_dir . "/tmp";
	$df=disk_free_space($dir_to_check);
	$dfh=formatsize($df);

	if ($df<1000000000) {
		echo "<div class=\"notice\" ><strong>Warning</strong>: Disk free space: $dfh. 
			It is recommended to <a href=\"alerts.php?op=1\">delete temporary files</a> 
			or upgrade the hard drive.</div>";
		}

	//Test if apache owns the files
	#Does apache own the folders?
	$fileowner = posix_getpwuid(fileowner("../index.php"));
	$this_user = exec('whoami');
	if ($fileowner['name'] != $this_user){
		echo "<div class=\"error\"><strong>The permissions are not set up correctly</strong>. The apache user needs to own the files and folders of Pumilio.
			You can change it using this command, changing PUMILIO_FOLDER for the path where Pumilio is installed:<br>
			sudo chown -R $this_user:$this_user /var/www/PUMILIO_FOLDER</div>";

		}
		
		

	// Test if the required programs are installed.
	// SoX
	unset($out, $retval);
	exec('sox --version', $soxout, $soxretval);
	$soxout=explode("v",$soxout[0]);
	$soxout=$soxout[1];
	if ($soxretval!=0) {
		echo "<div class=\"error\"><strong>SoX is not installed</strong>. Please install by using this command:<br>
			sudo apt-get install sox libsox*</div>";
		}

	// Test if the required programs are installed.
	// SoX
	unset($out, $retval);
	exec('soxi', $out, $retval);
	if ($retval!=0) {
		if ($soxretval==0) {
			echo "<div class=\"error\"><strong>The soxi utility from SoX is not installed</strong>. 
				The version of SoX installed is $soxout, you need at least version 14.3.0. 
				Download the latest version from http://sox.sourceforge.net/</div>";
			}
		else {
			echo "<div class=\"error\"><strong>The soxi utility from SoX is not installed</strong>. 
				Download the latest version of SoX from http://sox.sourceforge.net/</div>";
			}
		}


	#Encoder
	if ($AudioPreviewFormat=="ogg"){
		// dir2ogg
		unset($out, $retval);
		exec('dir2ogg --version', $out, $retval);
		if ($retval!=0) {
			echo "<div class=\"error\"><strong>The ogg encoder is not installed</strong>. 
				If ogg encoding is desired, please install by using this command: <br>
				sudo apt-get install dir2ogg</div>";
			}
		}
	elseif ($AudioPreviewFormat=="mp3"){
		// LAME
		unset($out, $retval);
		exec('lame --version', $out, $retval);
		if ($retval!=0) {
			echo "<div class=\"error\"><strong>The LAME MP3 encoder is not installed</strong>. 
				If mp3 encoding is desired, please install by using this command: <br>
				sudo apt-get install lame liblame*</div>";
			}
		}


	// FLAC
	unset($out, $retval);
	exec('flac --version', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>FLAC is not installed</strong>. 
			If flac support is desired, please install by using this command: <br>
			sudo apt-get install flac</div>";
		}

	// Imagemagick
	unset($out, $retval);
	exec('convert --version', $out, $retval);
	if ($retval>1) {
		echo "<div class=\"error\"><strong>Imagemagick is not installed</strong>. 
			Please install by using this command: <br>
			sudo apt-get install imagemagick</div>";
		}

	// audiolab
	unset($out, $retval);
	exec($absolute_dir . '/include/check_audiolab.py', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>The Python module 'audiolab' is not installed</strong>. 
			Please visit 
			<a href=\"http://www.scipy.org/scipy/scikits/wiki/AudioLab\" target=_blank>http://www.scipy.org/scipy/scikits/wiki/AudioLab</a></div>";
		$audiolab=1;
		}

	// svt.py script
	unset($out, $retval);
	exec($absolute_dir . '/include/svt.py -v', $out, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\"><strong>The svt script is not installed or can not run</strong>.";
		if ($audiolab==1) {
			echo " Please install audiolab first.";
			}
		echo "</div>";
		}



	if ($useR == TRUE){
	#Test if the R and the packages are installed
	exec($Rscript . ' R/test.R', $lastline, $R_retval);
	#seewave_1.5.8.tar.gz uses tuneR and does not fail with fftw3
	# signal, rpanel needed
		if ($R_retval=="91") {
			echo "<p class=\"error\"><strong>The R package 'tuneR' was not found.</strong></p>";
			}
		elseif ($R_retval=="92") {
			echo "<p class=\"error\"><strong>The R package 'seewave' was not found.</strong></p>";
			}
		elseif ($R_retval=="93") {
			echo "<p class=\"notice\"><strong>The R package 'RMySQL' was not found.</strong></p>";
			}
		elseif ($R_retval=="94") {
			echo "<p class=\"notice\"><strong>The R package 'ineq' was not found.</strong></p>";
			}
		elseif ($R_retval=="0") {
			}
		else {
			echo "<p class=\"error\"><strong>R is not installed or there was an unknown error.</strong></p>";
			}
		}
		


	$use_googlemaps=query_one("SELECT Value from PumilioSettings WHERE Settings='use_googlemaps'", $connection);
		if ($use_googlemaps=="1"){
			echo "<p class=\"error\"><strong>The system is set up to use Google Maps v2. This version has been deprecated. Please update your settings in the administration menu or contact your administrator.</strong></p>";
			}




	//Check for tmp folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/tmp/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("../tmp/") || !is_writable("../tmp/")) {
		echo "<div class=\"error\"><strong>The server can not write to the temporary folder, tmp/, 
			some features will not work.</strong> Please set the webserver as the owner of the
			directory or change the permissions to read and write.</div>";
		}

	//Check for sounds folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/sounds/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("../sounds/")) {
		echo "<div class=\"error\"><strong>The archive folder <em>sounds</em> does not exist. 
			</strong></div>";
		}
	elseif(!is_writable("../sounds/")) {
		echo "<div class=\"error\"><strong>The server can not write to the folder <em>sounds</em>, 
			some features will not work.</strong> Please set the webserver as the owner of the 
			directory or change the permissions to read and write.</div>";
		}


	if (!is_dir("../sounds/images")) {
		echo "<div class=\"error\"><strong>The archive folder <em>sounds/images</em> does not exist. 
			</strong></div>";
		}
	elseif(!is_writable("../sounds/images")) {
		echo "<div class=\"error\"><strong>The server can not write to the folder <em>sounds/images</em>, 
			some features will not work.</strong> Please set the webserver as the owner of the 
			directory or change the permissions to read and write.</div>";
		}

	
	if (!is_dir("../sounds/previewsounds")) {
		echo "<div class=\"error\"><strong>The archive folder <em>sounds/previewsounds</em> does not exist. 
			</strong></div>";
		}
	elseif(!is_writable("../sounds/previewsounds")) {
		echo "<div class=\"error\"><strong>The server can not write to the folder <em>sounds/previewsounds</em>, 
			some features will not work.</strong> Please set the webserver as the owner of the 
			directory or change the permissions to read and write.</div>";
		}

	
	if (!is_dir("../sounds/sounds")) {
		echo "<div class=\"error\"><strong>The archive folder <em>sounds/sounds</em> does not exist. 
			</strong></div>";
		}
	elseif(!is_writable("../sounds/sounds")) {
		echo "<div class=\"error\"><strong>The server can not write to the folder, <em>sounds/sounds</em>, 
			some features will not work.</strong> Please set the webserver as the owner of the 
			directory or change the permissions to read and write.</div>";
		}



	//Check for sitepictures folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/sitephotos/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("../sitephotos/") || !is_writable("../sitephotos/")) {
		echo "<div class=\"error\"><strong>The server can not write to the site photographs folder, sitephotos/,
			some features will not work.</strong> Please set the webserver as the owner of the 
			directory or change the permissions to read and write.</div>";
		}

	echo "<br><br><p><a href=\"#\" onClick=\"window.close();\">Close window</a>";
?>

</body>
</html>
