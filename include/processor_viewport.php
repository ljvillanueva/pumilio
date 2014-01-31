<?php

#set viewport back div size to keep proportions with the spectrogram
$viewport_black_width=150;
$viewport_black_height=round(($spectrogram_height/$spectrogram_width)*$viewport_black_width);
	
$nyquist=$soundfile_samplingrateoriginal/2;

$viewport_box_low=round($viewport_black_height - ((($frequency_min-10)/$nyquist)*$viewport_black_height));
$viewport_box_high=round((($nyquist-$frequency_max)/$nyquist)*$viewport_black_height);
$viewport_box_left=round(($time_min/$soundfile_duration)*$viewport_black_width);
$viewport_box_right=round(($time_max/$soundfile_duration)*$viewport_black_width);

$viewport_box_width=round($viewport_box_right-$viewport_box_left);
$viewport_box_height=round($viewport_box_low-$viewport_box_high);

$viewport_blackbox = $soundfile_wav . "_" . $viewport_box_low . '_' . $viewport_box_high . "_" . $viewport_box_left . "_" . $viewport_box_right . "_" . $ch . ".png";
if (!file_exists('tmp/' . $random_cookie . '/' . $viewport_blackbox)) {
	exec('include/svt.py -s tmp/' . $random_cookie . '/' . $viewport_blackbox . ' -w ' . $viewport_black_width . ' -h ' . $viewport_black_height . ' -m ' . $nyquist . ' -f ' . $fft_size . ' -c ' . $ch . ' -p ' . $spectrogram_palette . ' tmp/' . $random_cookie . '/' . $soundfile_wav, $lastline, $retval);
	exec("convert -stroke red -fill none -draw \"rectangle " . $viewport_box_left . "," . $viewport_box_high. " " . $viewport_box_right . "," . $viewport_box_low . "\" tmp/" . $random_cookie . "/" . $viewport_blackbox . " tmp/" . $random_cookie . "/" . $viewport_blackbox, $lastline, $retval);
	if ($retval!=0) {
		echo "<div class=\"error\">There was a problem with ImageMagick...</div>";
		die();
		}
	}

if (!isset($showmarks)){
	$showmarks = 0;
	}

echo "<img src=\"tmp/$random_cookie/$viewport_blackbox\" onClick=\"parent.location='pumilio.php?Token=$Token&showmarks=$showmarks'\" alt=\"Click to return to default view\" title=\"Click to return to default view\">";

?>
