<?php

//Get available formats
require("include/sox_formats_list.php");

$fileName=$soundfile_name;
$fileName_exp=explode(".", $fileName);
$fileName_type=$fileName_exp[1];

echo "<h4>Convert the open file</h4>";
?>

<div id="tabs0">
	<ul>
		<li><a href="#tabs-1">Convert format</a></li>
		<li><a href="#tabs-2">Convert sampling rate</a></li>
		<li><a href="#tabs-3">Change number of channels</a></li>
	</ul>
	<div id="tabs-1">
		<?php
		echo "<form method=\"POST\" action=\"convert2.php\"><input type=\"hidden\" name=\"process\" value=\"format\">
			Convert the file $fileName from format <strong>$fileName_type</strong> to: 
			<select name=\"convert_to\" class=\"ui-state-default ui-corner-all\">";

				// FLAC
				unset($out, $retval);
				exec('flac --version', $out, $retval);
				if ($retval==0) {
					echo "<option>flac</option>";
					}

				// LAME
				unset($out, $retval);
				exec('lame --version', $out, $retval);
				if ($retval==0) {
					echo "<option>mp3</option>";
					}

				//SoX options
				for ($s=0;$s<count($sox_formats);$s++) {
					echo "<option>$sox_formats[$s]</option>";
					}

			echo "</select>
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<input type=\"submit\" value=\" Convert file \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";
		?>
	</div>
	<div id="tabs-2">
		<?php
			echo "<form method=\"POST\" action=\"convert2.php\"><input type=\"hidden\" name=\"process\" value=\"sampling\">
				Convert the file $fileName from a sampling rate of <strong>$soundfile_samplingrate Hz</strong> to: 
				<select name=\"samp\" class=\"ui-state-default ui-corner-all\">
					<option value=\"11025\">11025 Hz</option>
					<option value=\"22050\">22050 Hz</option>
					<option value=\"44100\">44100 Hz</option>
					<option value=\"48000\">48000 Hz</option>
				</select>
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<input type=\"submit\" id=\"convert_submit2\" value=\" Convert file \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>";

		?>
	</div>
	<div id="tabs-3">
		<?php
			echo "<form method=\"POST\" action=\"convert2.php\">
				Convert the file $fileName from a ";
				if ($no_channels==1) {
					echo "<strong>mono to stereo</strong> file:
					<input type=\"hidden\" name=\"process\" value=\"channels12\">";
					}
				if ($no_channels==2) {
					echo "<strong>stereo to mono</strong> file:
					<input type=\"hidden\" name=\"process\" value=\"channels21\">";
					}

				echo "<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<input type=\"submit\" id=\"convert_submit3\" value=\" Convert file \" class=\"fg-button ui-state-default ui-corner-all\" />
			</form>";

		?>
	</div>
</div>
