<?php

if (!file_exists($filename)){
	die("<div class=\"error\"><img src=\"images/exclamation.png\"> The file ($filename) was not found. Please contact the administrator.</div>");
	}
	
//Write trimmed file
if ($filter=="yes") {
	$time_length_s=$time_max-$time_min;
	$time_length=round($time_length_s*$soundfile_samplingrate); //Set to number of samples
	$start_time=round($time_min*$soundfile_samplingrate); //Set to number of samples

	exec('sox ' . $filename . ' tmp/1.' . $sound_zoom . ' trim ' . $start_time . 's ' . $time_length . 's', $lastline, $retval);
	if ($retval!=0)	{
		#$lastline0=$lastline[0];
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with SoX. Please contact the administrator.</div>");
		}

	exec('sox tmp/1.' . $sound_zoom . ' tmp/' . $random_cookie . '/' . $sound_zoom . ' filter ' . $frequency_min . '-' . $frequency_max . ' 512', $lastline, $retval);
	if ($retval!=0){
		#filter was deprecated, replaced with "sinc"
		exec('sox tmp/1.' . $sound_zoom . ' tmp/' . $random_cookie . '/' . $sound_zoom . ' sinc ' . $frequency_min . '-' . $frequency_max, $lastline, $retval);
		if ($retval!=0){
			die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with SoX. Please contact the administrator.</div>");
			}
		}

	exec('include/svt.py -s tmp/' . $random_cookie . '/' . $imgfile . ' -w ' . $spectrogram_width . ' -h ' . $spectrogram_height . ' -i ' . $frequency_min . ' -m ' . $frequency_max . ' -f ' . $fft_size . ' -c ' . $ch . ' -p ' . $spectrogram_palette . ' tmp/' . $random_cookie . '/' . $sound_zoom, $lastline, $retval);
	if ($retval!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with svt. Please contact the administrator.</div>");
		}

	$player_result=player_file_mp3($sound_zoom,$soundfile_samplingrateoriginal,$player_file,$random_cookie);
	if ($player_result!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with the mp3 encoder...</div>");
		}
	}
elseif ($time_min!=0 || $time_max!=$soundfile_duration) {
	$time_length_s = $time_max-$time_min;
	$time_length = round($time_length_s*$soundfile_samplingrate); //Set to number of samples
	$start_time = round($time_min*$soundfile_samplingrate); //Set to number of samples

	exec('sox ' . $filename . ' tmp/' . $random_cookie . '/' . $sound_zoom . ' trim ' . $start_time . 's ' . $time_length . 's', $lastline, $retval);
	if ($retval!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with SoX. Please contact the administrator.</div>");
		}

	exec('include/svt.py -s tmp/' . $random_cookie . '/' . $imgfile . ' -w ' . $spectrogram_width . ' -h ' . $spectrogram_height . ' -i ' . $frequency_min . ' -m ' . $frequency_max . ' -f ' . $fft_size . ' -c ' . $ch . ' -p ' . $spectrogram_palette . ' tmp/' . $random_cookie . '/' . $sound_zoom, $lastline, $retval);
	if ($retval!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with svt. Please contact the administrator.</div>");
		}

	$player_result=player_file_mp3($sound_zoom,$soundfile_samplingrateoriginal,$player_file,$random_cookie);

	if ($player_result!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with the mp3 encoder...</div>");
		}
	}

#Write spectrogram
if (!file_exists('tmp/' . $random_cookie . '/' . $imgfile)) {
	exec('include/svt.py -s tmp/' . $random_cookie . '/' . $imgfile . ' -w ' . $spectrogram_width . ' -h ' . $spectrogram_height . ' -i ' . $frequency_min . ' -m ' . $frequency_max . ' -f ' . $fft_size . ' -c ' . $ch . ' -p ' . $spectrogram_palette . ' tmp/' . $random_cookie . '/' . $soundfile_wav, $lastline, $retval);
	if ($retval!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with svt. Please contact the administrator.</div>");
		}

	//Write scale in spectrogram
	$min_freq=$frequency_min;
	$max_freq=$frequency_max;
	$mid_freq=((($frequency_max-$frequency_min)/2) + $frequency_min);
	}

//mp3
if (!file_exists('tmp/' . $random_cookie . '/' . $player_file)) {
	if ($from_db) {
		$ColID = query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID'", $connection);
		$AudioPreviewFilename = query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID'", $connection);
		if (file_exists("sounds/previewsounds/$ColID/$AudioPreviewFilename")) {
			copy("sounds/previewsounds/$ColID/$AudioPreviewFilename","tmp/$random_cookie/$player_file");
			}
		else {
			$player_result = player_file_mp3($soundfile_wav, $soundfile_samplingrateoriginal, $player_file, $random_cookie);
			}
		}
	else {
		$player_result = player_file_mp3($soundfile_wav, $soundfile_samplingrateoriginal, $player_file, $random_cookie);
		}

	if ($player_result!=0){
		die("<div class=\"error\"><img src=\"images/exclamation.png\"> There was a problem with the $player_encoder encoder. Please contact the administrator.</div>");
		}
	}

//Enclosing div
echo "<div id=\"myCanvas\" style=\"position:relative; left: 0px;\">";

//Marks
// if marks are wanted, show
if (!isset($showmarks)){
	$showmarks = 0;
	}
	
if ($showmarks==1) {
	require("include/showmarks.php");
	}

//Playback line indicator
echo "<div id=\"myLine\" style=\"position:absolute; width:1px; height:400px; background-color:red; border:solid 1px red; top:0px; float:left; z-index:399; margin-left:0;\"></div>";

//Image Container
echo "<img src=\"tmp/$random_cookie/$imgfile\" id=\"cropbox\" style=\"margin: 0;\" />
</div>";
?>
