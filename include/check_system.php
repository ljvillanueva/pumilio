<?php

require("include/check_admin.php");

if ($pumilio_admin==TRUE) {
		
	$sys_errors = 0;

	#Does apache own the folders?
	$fileowner = posix_getpwuid(fileowner("index.php"));
	$this_user = exec('whoami');
	if ($fileowner['name'] != $this_user){
		$sys_errors++;
		}

	#Disk free space check
	$dir_to_check=$absolute_dir . "/tmp";
	$df=disk_free_space($dir_to_check);
	$dfh=formatsize($df);

	if ($df<1000000000) {
		$sys_errors++;
		}


	// Test if the required programs are installed.
	// SoX
	unset($out, $retval);
	exec('sox --version', $soxout, $soxretval);
	$soxout=explode("v",$soxout[0]);
	$soxout=$soxout[1];
	if ($soxretval!=0) {
		$sys_errors++;
		}

	// Test if the required programs are installed.
	// SoX
	unset($out, $retval);
	exec('soxi', $out, $retval);
	if ($retval!=0)
		{
		if ($soxretval==0) {
			$sys_errors++;
			}
		}


	if ($AudioPreviewFormat=="ogg"){
		// ogg encoder
		unset($out, $retval);
		exec('dir2ogg --version', $out, $retval);
		if ($retval!=0) {
			$sys_errors++;
			}
		}
	elseif ($player_format=="mp3"){
		// LAME
		unset($out, $retval);
		exec('lame --version', $out, $retval);
		if ($retval!=0) {
			$sys_errors++;
			}
		}


	// FLAC
	unset($out, $retval);
	exec('flac --version', $out, $retval);
	if ($retval!=0) {
		$sys_errors++;
		}


	// Imagemagick
	unset($out, $retval);
	exec('convert --version', $out, $retval);
	if ($retval>1) {
		$sys_errors++;
		}


	// audiolab
	unset($out, $retval);
	exec($absolute_dir . '/include/check_audiolab.py', $out, $retval);
	if ($retval!=0) {
		$sys_errors++;
		}


	// svt.py script
	unset($out, $retval);
	exec($absolute_dir . '/include/svt.py -v', $out, $retval);
	if ($retval!=0) {
		$sys_errors++;
		}


	//Check for tmp folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/tmp/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("tmp/") || !is_writable("tmp/")) {
		$sys_errors++;
		}


	//Check for sounds folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/sounds/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("sounds/") || !is_writable("sounds/")) {
		$sys_errors++;
		}
	elseif (!is_dir("sounds/images") || !is_writable("sounds/images")) {
		$sys_errors++;
		}
	elseif (!is_dir("sounds/previewsounds") || !is_writable("sounds/previewsounds")) {
		$sys_errors++;
		}
		
	if ($special_nofiles == FALSE && (!is_dir("sounds/sounds") || !is_writable("sounds/sounds"))) {
		$sys_errors++;
		}


	//Check for sitepictures folder
	unset($out, $retval);
	#$tmpperms=substr(decoct(fileperms("$absolute_dir/sitephotos/")),2);
	#if ($tmpperms!=777)
	if (!is_dir("sitephotos/") || !is_writable("sitephotos/")) {
		$sys_errors++;
		}


	if ($useR == TRUE){
	#Test if the R and the packages are installed
	exec($Rscript . ' include/R/test.R', $lastline, $R_retval);

		if ($R_retval=="91") {
			$sys_errors++;
			}
		elseif ($R_retval=="92") {
			$sys_errors++;
			}
		elseif ($R_retval=="93") {
			$sys_errors++;
			}
		elseif ($R_retval=="94") {
			$sys_errors++;
			}
		elseif ($R_retval=="0") {
			}
		else {
			$sys_errors++;
			}
		}

	$use_googlemaps=query_one("SELECT Value from PumilioSettings WHERE Settings='use_googlemaps'", $connection);
		if ($use_googlemaps=="1"){
			$sys_errors++;
			}

	if ($sys_errors == 1) {
		echo "<div class=\"notice\" style=\"padding: 0.2px; width: 160px; margin-right: 0px; margin-left: 200px; text-align: center;\"><small>There is <a href=\"#\" onclick=\"window.open('include/alerts.php', 'alerts', 'width=550,height=400,status=yes,resizable=yes,scrollbars=auto')\">$sys_errors alert</a>.</small></div>";
		}
	elseif ($sys_errors > 1) {
		echo "<div class=\"notice\" style=\"padding: 0.2px; width: 160px; margin-right: 0px; margin-left: 200px; text-align: center;\"><small>There are <a href=\"#\" onclick=\"window.open('include/alerts.php', 'alerts', 'width=550,height=400,status=yes,resizable=yes,scrollbars=auto')\">$sys_errors alerts</a>.</small></div>";
		}
	}
?>
