<?php
echo "<div class=\"span-24 last\">";
	if (is_file($wav_todl)){
		echo "<form method=\"GET\" action=\"dl.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"file\" value=\"$wav_todl\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/action_save.gif\" title=\"Download current sound file\"> Download current sound file</button>
		</form>";
		}
	else{
		echo "<form method=\"GET\" action=\"dl.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"file\" value=\"$filename\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/action_save.gif\" title=\"Download current sound file\"> Download current sound file</button>
		</form>";
		}
		
	echo "<form method=\"GET\" action=\"dl.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"file\" value=\"tmp/$random_cookie/$imgfile\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/image.png\" title=\"Download spectrogram\"> Download spectrogram</button>
		</form>\n";

	echo "<form method=\"GET\" action=\"convert.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/drive_go.png\" title=\"Convert file\"> Convert file</button>
		</form>\n";

	echo "<form method=\"GET\" action=\"file_details.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/information.png\" title=\"File details\"> File details</button>
		</form>\n";

	echo "<form method=\"GET\" action=\"settings.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/wrench.png\" title=\"Visualization settings\"> Visualization settings</button>
		</form>\n";

	echo "<form method=\"GET\" action=\"closefile.php\" style=\"float: inherit;\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" class=\"fg-button ui-state-default ui-corner-all\">
			<img src=\"images/cross.png\" title=\"Close file\"> Close file</button>
		</form>\n
			
	</div>";
?>
