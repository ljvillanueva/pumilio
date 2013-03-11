<?php
session_start();

$Token=filter_var($_POST["Token"], FILTER_SANITIZE_STRING);

if ($_POST["cookie_to_set"]=="freq_range") {
	$min_freq=$_POST["min_freq"];
	$max_freq=$_POST["max_freq"];

	if ($max_freq>$min_freq) {
	setcookie("frequency_min", "$min_freq");
	setcookie("frequency_max", "$max_freq");
		}
	}
elseif ($_POST["cookie_to_set"]=="fft") {
	$fft=$_POST["fft"];
	setcookie("fft", "$fft");
	}
elseif ($_POST["cookie_to_set"]=="palette") {
	$palette=$_POST["palette"];
	setcookie("palette", "$palette");
	}
elseif ($_POST["cookie_to_set"]=="clear") {
	setcookie("fft", "", time()-3600);
	setcookie("frequency_min", "", time()-3600);
	setcookie("frequency_max", "", time()-3600);
	}
elseif ($_POST["cookie_to_set"]=="jquerycss") {
	$css=$_POST["css"];
	setcookie("jquerycss", "$css", time()+3600);
	header("Location: ./edit_myinfo.php?d=1");
	die();
	}

// Relocate back to the first page of the application
	header("Location: ./settings.php?Token=$Token");
	die();

?>
