<?php

$resultm=mysqli_query($connection, "SELECT marks_ID FROM SoundsMarks WHERE SoundID='$soundfile_id'")
	or die (mysqli_error($connection));;
$nrowsm = mysqli_num_rows($resultm);
if ($nrowsm>0) {
	for ($w=0;$w<$nrowsm;$w++) {
		$rowm = mysqli_fetch_array($resultm);
		extract($rowm);

		//Query for the last mark edit
		$res=mysqli_query($connection, "SELECT marks_ID, SoundID AS mark_fileID, time_min AS mark_time_min, time_max AS mark_time_max, freq_min AS mark_freq_min, freq_max AS mark_freq_max, mark_tag FROM SoundsMarks WHERE marks_ID='$marks_ID' LIMIT 1");
		$row = mysqli_fetch_array($res);
		extract($row);
		unset($row);

		#Only show if some part of the mark is inside the current window
		if ($mark_time_min<$time_max && $mark_time_max>$time_min && $mark_freq_min<$frequency_max && $mark_freq_max>$frequency_min){
			//Time and freq calculations to draw the boxes of marks

			if ($mark_time_max>$time_max) {
				$mark_time_max=$time_max;
				}

			if ($mark_time_min<$time_min) {
				$time_i=0;
				$mark_time_max=$mark_time_max-$time_min;
				}
			else {
				$time_i=(($mark_time_min-$time_min)/$total_time)*$spectrogram_width;
				}

			if ($mark_freq_max>$frequency_max) {
				$freq_i=0;
				$mark_freq_max=$frequency_max;
				}
			else {
				$freq_i=(((($frequency_range+$frequency_min)-$mark_freq_max)/$frequency_range)*$spectrogram_height);
				}

			if ($mark_freq_min<$frequency_min) {
				$freq_w=$spectrogram_height-$freq_i;
				}
			else {
				$freq_w=(($mark_freq_max-$mark_freq_min)/$frequency_range)*$spectrogram_height;
				}

			$time_w=(($mark_time_max-$mark_time_min)/$total_time)*$spectrogram_width;
		
			//Mark
			echo "\n<div id=\"mark$w\" style=\"z-index:800; border-style: solid; border-color: #ffff00; border-width: thin; left: " . $time_i . "px; top: " . $freq_i . "px; position: absolute; height: " . $freq_w . "px; width: " . $time_w . "px;\" title=\"$mark_tag_name: $mark_tag (ID:$marks_ID)\" > </div>\n";
			}
		}
	}

?>
