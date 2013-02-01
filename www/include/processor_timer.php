<?php

#Use minimum time of current display to show default time
#minutes
if ($time_min>0) {
	$min_to_show=floor($time_min/60);
	}
else {
	$min_to_show=0;
	}

#seconds
if ($time_min>0) {
	$sec_to_show=(($time_min/60)-$min_to_show)*60;
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}
	}
else {
	$sec_to_show="00";
	}

echo "<h1 style=\"font-size:4em;\"><div id=\"time_min_div\" style=\"float: left;\">$min_to_show</div><div style=\"float: left;\">:</div><div id=\"time_sec_div\" style=\"float: left;\">$sec_to_show</div></h1>";

?>
