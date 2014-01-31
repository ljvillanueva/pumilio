<?php

#Image size calculations
	$spectrogram_width=400;
	$spectrogram_height=180;

#Check if frequency range is set in cookies
	if (isset($_GET["f_min"])) {
		$frequency_min=$_GET["f_min"];
		$frequency_max=$_GET["f_max"];
		$frequency_range=$frequency_max-$frequency_min;
		}
	elseif (isset($_COOKIE["frequency_min"])) {
		$frequency_min=$_COOKIE["frequency_min"];
		$frequency_max=$_COOKIE["frequency_max"];
		$frequency_range=$frequency_max-$frequency_min;
		}
	else {
		$frequency_min=10;
		$frequency_max=10000;
		$frequency_range=$frequency_max-$frequency_min;
		}

#Check if time is set in GET
	if (isset($_GET["t_min"])) {
		$time_min=$_GET["t_min"];
		$time_max=$_GET["t_max"];
		}
	else {
		$time_min=0;
		$time_max=$soundfile_duration;
		}

	$total_time=$time_max-$time_min;

	#Check if fft size is set
	if (isset($_COOKIE["fft"])) {
		$fft_size=$_COOKIE["fft"];
		}
	else {
		$fft_size=2048;
		}


	//Check file format
	$fileName_exp=explode(".", $soundfile_wav);

	#Get color palette
	$spectrogram_palette=query_one("SELECT Value FROM PumilioSettings WHERE Settings='spectrogram_palette' LIMIT 1", $connection);
	if ($spectrogram_palette=="")
		$spectrogram_palette=2;
	if ($palette!="")
		$spectrogram_palette=$palette;

	//If wav file does not exists
	$fileName_exp=explode(".", $soundfile_wav);
	$filename='tmp/' . $random_cookie . '/' . $soundfile_wav;
	$imgfile=$fileName_exp[0] . '_' . $frequency_min . '-' . $frequency_max . '_' . $time_min . '-' . $time_max . '_' . $fft_size . '_' . $spectrogram_palette . '.s.png';

	if (!file_exists($filename)){
		die("<div class=\"error\">The file was not found. Please try again.</div>");
		}

	//Write spectrogram
	if (!file_exists('tmp/' . $random_cookie . '/' . $imgfile)) {
		exec('include/svt.py -s tmp/' . $random_cookie . '/' . $imgfile . ' -w ' . $spectrogram_width . ' -h ' . $spectrogram_height . ' -i ' . $frequency_min . ' -m ' . $frequency_max . ' -f ' . $fft_size . ' -p ' . $spectrogram_palette . ' tmp/' . $random_cookie . '/' . $soundfile_wav, $lastline, $retval);
		if ($retval!=0){
			die("<div class=\"error\">There was a problem with svt...</div>");
			}
		}

//Image Container
echo "<img src=\"tmp/$random_cookie/$imgfile\">";
?>
