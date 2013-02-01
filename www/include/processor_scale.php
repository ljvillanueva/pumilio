<?php

	//Write scale
		$min_freq=$frequency_min;
		$max_freq=$frequency_max;
		$mid_freq=((($frequency_max-$frequency_min)/2) + $frequency_min);

		$range=$max_freq-$min_freq;
		$steps=round($range/8);
		$freq_1=$min_freq+$steps;
		$freq_2=$min_freq+($steps*2);
		$freq_3=$min_freq+($steps*3);
		$freq_4=$min_freq+($steps*4);
		$freq_5=$min_freq+($steps*5);
		$freq_6=$min_freq+($steps*6);
		$freq_7=$min_freq+($steps*7);

#tables are not elegant, but it works
echo "
<table height=\"$spectrogram_height\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
	<tr height=\"24\"><td style=\"background:#FFFFFF;\">$max_freq Hz
	</td></tr>
	<tr height=\"50\" style=\"background:#FFFFFF;\"><td style=\"background:#FFFFFF;\">&nbsp;
	</td></tr>
	<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
	</td></tr>
	<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
	</td></tr>
	<tr height=\"50\"><td style=\"background:#FFFFFF;\">$freq_4 Hz
	</td></tr>
	<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
	</td></tr>
	<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
	</td></tr>
	<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
	</td></tr>
	<tr><td style=\"background:#FFFFFF;\">$min_freq Hz
	</td></tr>
</table>";
?>
