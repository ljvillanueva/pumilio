<form method="GET" action="pumilio.php" style="text-align: center;">
	<input type="text" size="3" id="x" name="t_min" title="Minimum time of selection" class="ui-state-default ui-corner-all" readonly /> -
	 <input type="text" size="3" id="x2" class="ui-state-default ui-corner-all" name="t_max" title="Maximum time of selection" readonly /> sec | 
	<input type="text" size="4" id="y" class="ui-state-default ui-corner-all" name="f_min" title="Minimum frequency of selection" readonly /> - 
	 <input type="text" size="4" id="y2" class="ui-state-default ui-corner-all" name="f_max" title="Maximum frequency of selection" readonly /> Hz<br>
	<input type="checkbox" name="filter" value="yes" title="Select to apply a bandpass filter outside the area selected" class="fg-button ui-state-default ui-corner-all" /> Filter
	&nbsp;&nbsp;<input type="button" id="zoom_submit" value=" Zoom in " class="fg-button ui-state-default ui-corner-all" onClick="submit(); document.getElementById('zoom_submit').disabled = true; document.getElementById('zoom_submit').value = 'Please wait';" disabled />

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

</form>
