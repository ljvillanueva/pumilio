<form method="GET" action="pumilio.php" class="form-inline">

<?php
	echo "
	<div class=\"form-group\"><input size=\"3\" type=\"text\" id=\"x\" name=\"t_min\" value=\"$time_min\" title=\"Minimum time of selection\" class=\"form-control input-sm\" /> -
	 <input type=\"text\" size=\"3\" id=\"x2\" value=\"$time_max\" class=\"form-control input-sm\" name=\"t_max\" title=\"Maximum time of selection\" /> sec</div>
	<div class=\"form-group\">
	<input type=\"text\" size=\"4\" id=\"y\" class=\"form-control input-sm\" name=\"f_min\" value=\"$frequency_min\" title=\"Minimum frequency of selection\" /> - 
	 <input type=\"text\" size=\"4\" id=\"y2\" class=\"form-control input-sm\" name=\"f_max\" value=\"$frequency_max\" title=\"Maximum frequency of selection\" /> Hz</div>";
	 
?>


	<div class="form-group"><input type="checkbox" name="filter" value="yes" title="Select to apply a bandpass filter outside the area selected" class="form-control input-sm" /> Filter
	&nbsp;&nbsp;

	<button type="submit" class="btn btn-sm btn-primary" id="zoom_submit" onClick="submit(); document.getElementById('zoom_submit').disabled = true; document.getElementById('zoom_submit').value = 'Please wait';">Zoom in</button>


<?php
echo "<input type=\"hidden\" name=\"ch\" value=\"$ch\">
	<input type=\"hidden\" name=\"Token\" value=\"$Token\">";

/*
if (isset($tool)) {
	echo "<input type=\"hidden\" name=\"tool\" value=\"$tool\">";
	}
*/
	
if (!isset($showmarks)){
	$showmarks = 0;
	}
	
if ($showmarks) {
	echo "<input type=\"hidden\" name=\"showmarks\" value=\"1\">";
	}
?>
</div>
</form>
