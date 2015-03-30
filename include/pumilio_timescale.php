<?php
#Not very elegant, but it works
# Try to find a better way to do these scales

$dur=$time_max-$time_min;
$dur_ea=$dur/10.85;

#Mins:secs scale
	$min_to_show=floor($time_min/60);
#seconds
	$sec_to_show=round((($time_min/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$time_min+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}

echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";

$second1=$second1+$dur_ea;
$second=round($second1,1);

#Mins:secs scale
	$min_to_show=floor($second/60);
	#seconds
	$sec_to_show=round((($second/60)-$min_to_show)*60);
	if ($sec_to_show<10) {
		$sec_to_show="0" . $sec_to_show;
		}
		
echo "<div class=\"span-2\">";
	echo "<span class=\"small\"><img style=\"margin-bottom:-0.8em;\" src=\"images/vert_line.png\"> $min_to_show:$sec_to_show</span>";
echo "</div>";


echo "<div class=\"span-2 last\">";
	echo "&nbsp;";
echo "</div>";

?>
