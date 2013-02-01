<?php

// Check SoX formats
$out_formats=exec('sox -h | grep "FILE FORMATS:"', $out, $retval);
$formats_available=explode(" ", $out_formats);
$count_formats_available=count($formats_available);

//Display formats available for SoX
for ($f=3;$f<($count_formats_available-1);$f++) {
	echo "<em>" . $formats_available[$f] . "</em>, ";
	}

echo "or <em>" . $formats_available[($count_formats_available-1)] . "</em>";

?>
