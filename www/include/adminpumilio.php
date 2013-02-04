<?php

//Custom name of the app
if ($_GET["tt"]==1) {
	echo "<div class=\"success\" id=\"tt1\">The database was updated.</div>";
	}

	$app_custom_name=query_one("SELECT Value from PumilioSettings WHERE Settings='app_custom_name'", $connection);
	if ($app_custom_name==""){
		$app_custom_name="Pumilio";
		}
	
	echo "<strong>General settings:</strong>
		<div style=\"margin-left: 10px;\">This installation custom name: $app_custom_name";
	
	echo "<form action=\"include/editpumiliosettings.php\" method=\"POST\">
		<input type=\"hidden\" name=\"settings\" value=\"top\">
		<input type=\"text\" name=\"app_custom_name\" maxlength=\"250\" size=\"36\" value=\"$app_custom_name\" class=\"fg-button ui-state-default ui-corner-all formedge\">";
	
	#app_custom_text
	$app_custom_text=query_one("SELECT Value from PumilioSettings WHERE Settings='app_custom_text'", $connection);
	
	echo "<br>Short description of this system: $app_custom_text";
	
	echo "<input type=\"text\" name=\"app_custom_text\" maxlength=\"250\" size=\"50\" value=\"$app_custom_text\" class=\"fg-button ui-state-default ui-corner-all formedge\">";
		
	#use_googlemaps
	$use_googlemaps=query_one("SELECT Value from PumilioSettings WHERE Settings='use_googlemaps'", $connection);
	
	if ($use_googlemaps=="1"){
		$use_googlemaps_d="No";
		$use_googlemaps="0";
		}
	elseif ($use_googlemaps=="0"){
		$use_googlemaps_d="No";
		}
	elseif ($use_googlemaps=="3"){
		$use_googlemaps_d="Yes";
		}
	else{
		$use_googlemaps_d="Not set";
		}
		
	echo "<br>Use the Google Maps system (using the v3 API): $use_googlemaps_d";
	
	echo "<select name=\"use_googlemaps\" class=\"ui-state-default ui-corner-all formedge\">";

		if ($use_googlemaps=="0") {
			echo "<option SELECTED value=\"0\">No</option>
				<option value=\"3\">Yes</option>";
			}
		elseif ($use_googlemaps=="3") {
			echo "<option SELECTED value=\"3\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"3\">Yes</option>
				<option value=\"0\">No</option>";
			}

		echo "</select>\n";
		
	#googlemaps_key
	if ($use_googlemaps=="3") {
		echo "<p>
		<a href=\"https://developers.google.com/maps/documentation/javascript/tutorial\" target=_blank>Request a key for Google Maps JavaScript API v3</a> (free and required for each server) <a href=\"#\" onclick=\"window.open('help.php?topic=GoogleMaps3', 'help', 'width=650,height=550,status=yes,resizable=yes,scrollbars=auto')\"><img src=\"images/help.png\" title=\"Help\" alt=\"Help\"></a><br>
			GoogleMaps v3 key: $googlemaps3_key";
		
		echo "<input type=\"text\" name=\"googlemaps3_key\" size=\"50\" maxlength=\"250\" value=\"$googlemaps3_key\" class=\"fg-button ui-state-default ui-corner-all formedge\">\n";
		}	
	
	
	$map_only=query_one("SELECT Value from PumilioSettings WHERE Settings='map_only'", $connection);
	
	if ($map_only=="1"){
		$map_only_d="Map only";
		}
	elseif ($map_only=="0" || $map_only==""){
		$map_only_d="Full menu";
		}

	echo "<br>Main menu: $map_only_d";
	
	echo "<select name=\"map_only\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($map_only=="1") {
			echo "<option value=\"0\">Full menu</option>
				<option SELECTED value=\"1\">Map only</option>";
			}
		elseif ($map_only=="0" || $map_only=="") {
			echo "<option SELECTED value=\"0\">Full menu</option>
				<option value=\"1\">Map only</option>";
			}
	echo "</select>\n";
	
	
	#Copyright or CC?
	$files_license = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license'", $connection);
	$files_license_detail = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license_detail'", $connection);

	if ($files_license=="") {
		$files_license = "Not set";
		}

	echo "<br>Retain copyright or share with a <a href=\"http://creativecommons.org/licenses/\" target=_blank>Creative Commons</a> license? $files_license";
	
	echo "<select name=\"files_license\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($files_license=="Copyright") {
			echo "<option SELECTED value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		elseif ($files_license=="CC BY") {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option SELECTED value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		elseif ($files_license=="CC BY-SA") {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option SELECTED value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		elseif ($files_license=="CC BY-ND") {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option SELECTED value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		elseif ($files_license=="CC BY-NC") {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option SELECTED value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		elseif ($files_license=="CC BY-NC-SA") {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option SELECTED value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		elseif ($files_license=="CC BY-NC-ND") {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option SELECTED value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
		else {
			echo "<option value=\"Copyright\">&#169; Copyright</option>
				<option value=\"CC BY\">CC BY</option>
				<option value=\"CC BY-SA\">CC BY-SA</option>
				<option value=\"CC BY-ND\">CC BY-ND</option>
				<option value=\"CC BY-NC\">CC BY-NC</option>
				<option value=\"CC BY-NC-SA\">CC BY-NC-SA</option>
				<option value=\"CC BY-NC-ND\">CC BY-NC-ND</option>\n";
				}
				
		echo " </select>
		
		<input type=\"text\" name=\"files_license_detail\" size=\"40\" maxlength=\"250\" value=\"$files_license_detail\" class=\"fg-button ui-state-default ui-corner-all\">";
	
	#Max freq to draw in spectrograms
	$max_spec_freq=query_one("SELECT Value from PumilioSettings WHERE Settings='max_spec_freq'", $connection);

	if ($max_spec_freq=="max") {
		$max_spec_freq_d="Maximum for each file";
		}
	elseif ($max_spec_freq=="") {
		$max_spec_freq_d="22050 Hz";
		}
	else {
		$max_spec_freq_d=$max_spec_freq . " Hz";
		}

	echo "<br>Maximum acoustic frequency for the spectrograms: $max_spec_freq_d";
	
	echo "<select name=\"max_spec_freq\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($max_spec_freq=="max") {
			echo "<option SELECTED value=\"max\">Maximum for each file</option>
				<option value=\"24000\">24000 Hz</option>
				<option value=\"22050\">22050 Hz</option>
				<option value=\"16000\">16000 Hz</option>
				<option value=\"11025\">11025 Hz</option>
				<option value=\"10000\">10000 Hz</option>
				<option value=\"8000\">8000 Hz</option>
				<option value=\"6000\">6000 Hz</option>
				<option value=\"4000\">4000 Hz</option>
				<option value=\"3000\">3000 Hz</option>";
				}
		elseif ($max_spec_freq=="") {
			echo "<option value=\"max\">Maximum for each file</option>
				<option value=\"24000\">24000 Hz</option>
				<option value=\"22050\">22050 Hz</option>
				<option value=\"16000\">16000 Hz</option>
				<option value=\"11025\">11025 Hz</option>
				<option value=\"10000\">10000 Hz</option>
				<option value=\"8000\">8000 Hz</option>
				<option value=\"6000\">6000 Hz</option>
				<option value=\"4000\">4000 Hz</option>
				<option value=\"3000\">3000 Hz</option>";
				}
		else {
			if ($max_spec_freq==24000)
				$m1="SELECTED";
			elseif ($max_spec_freq==22050)
				$m2="SELECTED";
			elseif ($max_spec_freq==16000)
				$m3="SELECTED";
			elseif ($max_spec_freq==11025)
				$m4="SELECTED";
			elseif ($max_spec_freq==10000)
				$m5="SELECTED";
			elseif ($max_spec_freq==8000)
				$m6="SELECTED";
			elseif ($max_spec_freq==6000)
				$m7="SELECTED";
			elseif ($max_spec_freq==4000)
				$m8="SELECTED";
			elseif ($max_spec_freq==3000)
				$m9="SELECTED";
			echo "<option SELECTED value=\"max\">Maximum for each file</option>
				<option $m1 value=\"24000\">24000 Hz</option>
				<option $m2 value=\"22050\">22050 Hz</option>
				<option $m3 value=\"16000\">16000 Hz</option>
				<option $m4 value=\"11025\">11025 Hz</option>
				<option $m5 value=\"10000\">10000 Hz</option>
				<option $m6 value=\"8000\">8000 Hz</option>
				<option $m7 value=\"6000\">6000 Hz</option>
				<option $m8 value=\"4000\">4000 Hz</option>
				<option $m9 value=\"3000\">3000 Hz</option>";
			}

		echo " </select>";

	$temp_add_dir=query_one("SELECT Value from PumilioSettings WHERE Settings='temp_add_dir'", $connection);
	if ($temp_add_dir!=""){ 
		if (!is_dir($temp_add_dir)){
			$temp_add_dir_d="<em style=\"color:red;\">Invalid directory</em>";
			query_one("DELETE from PumilioSettings WHERE Settings='temp_add_dir'", $connection);}
		elseif(!is_readable($temp_add_dir)){
			$temp_add_dir_d="<em style=\"color:red;\">Could not read directory</em>";
			query_one("DELETE from PumilioSettings WHERE Settings='temp_add_dir'", $connection);}
		else {
			$temp_add_dir_d="<em>$temp_add_dir</em>";}
		}
	else {
		$temp_add_dir_d="Not set";
		}

	echo "<br>Local directory for adding multiple files: $temp_add_dir_d";
	
	echo "<input type=\"text\" name=\"temp_add_dir\" class=\"fg-button ui-state-default ui-corner-all formedge\">";


/*
	#Use R?
	if ($useR=="" || $useR=="0") {
		$useR_d="No";
		}
	elseif ($useR=="1") {
		$useR_d="Yes";
		}

	echo "<br>Use R: $useR_d";
	
	echo "<form action=\"include/editpumiliosettings.php\" method=\"POST\">
		<input type=\"hidden\" name=\"Settings\" value=\"useR\">
		<select name=\"Value\" class=\"ui-state-default ui-corner-all\">";
		if ($useR=="1") {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>\n";
				}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>\n";
				}

		echo " </select>
		<input type=submit value=\" Change selection \" class=\"fg-button ui-state-default ui-corner-all\"> <a href=\"#\" onclick=\"window.open('help.php?topic=R', 'help', 'width=650,height=550,status=yes,resizable=yes,scrollbars=auto')\"><img src=\"images/help.png\" title=\"Help\" alt=\"Help\"></a>
		</form>";
*/

	#Cores to use
	$cores_to_use=query_one("SELECT Value from PumilioSettings WHERE Settings='cores_to_use'", $connection);
	if ($cores_to_use==""){ 
		$cores_to_use_d = "Not set (use 1)";
		}
	else {
		$cores_to_use_d = $cores_to_use;
		}

	echo "<br>How many cores to use for background processes: $cores_to_use_d";
	$machine_cores = nocores();
	
	echo "<select name=\"cores_to_use\" class=\"ui-state-default ui-corner-all formedge\">";
		for ($c=0; $c<$machine_cores; $c++) {
			$cc=$c+1;
			if ($cc == $cores_to_use){
				echo "<option SELECTED value=\"$cc\">$cc</option>\n";
				}
			else{
				echo "<option value=\"$cc\">$cc</option>\n";
				}
			}

		echo " </select>
		<p><input type=submit value=\" Update system settings \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";




	echo "</div>
	<p><strong>System behavior:</strong>
	<br>
	<div style=\"margin-left: 10px;\">
	<form action=\"include/editpumiliosettings.php\" method=\"POST\">";	
		
	if ($_GET["tt"]==2) {
		echo "<div class=\"success\" id=\"tt2\">The database was updated.</div>";
		}
	
	#allow chorus
	$use_chorus=query_one("SELECT Value from PumilioSettings WHERE Settings='use_chorus'", $connection);

	if ($use_chorus=="1"){
		$use_chorus_d="Yes";
		}
	elseif ($use_chorus=="0"){
		$use_chorus_d="No";
		}
	else {
		$use_chorus_d="Not set";
		}

	echo "<br>Allow this website to be indexed in the <a href=\"http://pumilio.sourceforge.net/chorus.php\" target=_blank>Pumilio Chorus</a>: $use_chorus_d";
	
	echo "<input type=\"hidden\" name=\"settings\" value=\"bottom\">
		<select name=\"use_chorus\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($use_chorus) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

		echo " </select>\n";


	#allow access using XML
	$use_xml=query_one("SELECT Value from PumilioSettings WHERE Settings='use_xml'", $connection);

	if ($use_xml=="1"){
		$use_xml_d="Yes";
		}
	elseif ($use_xml=="0"){
		$use_xml_d="No";
		}
	else {
		$use_xml_d="Not set";
		}

	echo "<br>Allow access using XML: $use_xml_d";
	
	echo "<select name=\"use_xml\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($use_xml) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		elseif ($use_xml=="0"){
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}
		else {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}

		echo " </select>";

	
	#use tag cloud
	$use_tags=query_one("SELECT Value from PumilioSettings WHERE Settings='use_tags'", $connection);

	if ($use_tags=="1"){
		$use_tags_d="Yes";
		}
	elseif ($use_tags=="0"){
		$use_tags_d="No";
		}
	else {
		$use_tags_d="Not set";
		}

	echo "<br>Use a tag cloud: $use_tags_d";
	
	echo "<select name=\"use_tags\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($use_tags) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

		echo " </select>";

	#audio preview format
	$audiopreview_format=query_one("SELECT Value from PumilioSettings WHERE Settings='audiopreview_format'", $connection);
	if ($audiopreview_format==""){
		$audiopreview_format = "mp3";
		}

/*
	echo "<br>Audio preview format: $audiopreview_format";
	
	echo "<form action=\"include/editpumiliosettings.php\" method=\"POST\">
		<input type=\"hidden\" name=\"Settings\" value=\"audiopreview_format\">
		<select name=\"Value\" class=\"ui-state-default ui-corner-all\">";
		if ($audiopreview_format=="mp3") {
			echo "<option SELECTED value=\"mp3\">mp3</option>
				<option value=\"ogg\">ogg</option>";
			}
		elseif ($audiopreview_format=="ogg") {
			echo "<option value=\"mp3\">mp3</option>
				<option SELECTED value=\"ogg\">ogg</option>";
			}
		elseif ($audiopreview_format=="wav") {
			echo "<option value=\"mp3\">mp3</option>
				<option value=\"ogg\">ogg</option>
				<option SELECTED value=\"wav\">wav</option>";
			}

		echo " </select>
		<input type=submit value=\" Change selection \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
*/
	
	#hide_latlon_guests
	$hide_latlon_guests=query_one("SELECT Value from PumilioSettings WHERE Settings='hide_latlon_guests'", $connection);

	if ($hide_latlon_guests=="1")
		$hide_latlon_guests_d="Yes";
	elseif ($hide_latlon_guests=="0")
		$hide_latlon_guests_d="No";
	else
		$hide_latlon_guests_d="Not set";
			
	echo "<br>Hide the coordinates from users that are not logged in: $hide_latlon_guests_d";
	
	echo "<select name=\"hide_latlon_guests\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($hide_latlon_guests) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

		echo " </select>";


	#use side-to-side comparison
	$sidetoside_comparison=query_one("SELECT Value from PumilioSettings WHERE Settings='sidetoside_comp'", $connection);

	if ($sidetoside_comparison=="1")
		$sidetoside_comparison_d="Yes";
	elseif ($sidetoside_comparison=="0")
		$sidetoside_comparison_d="No";
	else
		$sidetoside_comparison_d="Not set";
			
	echo "<br>Use Side-to-side comparison: $sidetoside_comparison_d";
	
	echo "<select name=\"sidetoside_comp\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($sidetoside_comparison)
			{echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";}
		else
			{echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";}

		echo " </select>";


	#allow_upload
	$allow_upload=query_one("SELECT Value from PumilioSettings WHERE Settings='allow_upload'", $connection);

	if ($allow_upload=="1")
		$allow_upload_d="Yes";
	elseif ($allow_upload=="0")
		$allow_upload_d="No";
	else
		$allow_upload_d="Not set";
			
	echo "<br>Allow users to upload files: $allow_upload_d";
	
	echo "<select name=\"allow_upload\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($allow_upload){
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

		echo " </select>";


	#compress wav to flac
	$wav_toflac=query_one("SELECT Value from PumilioSettings WHERE Settings='wav_toflac'", $connection);

	if ($wav_toflac=="1") {
		$wav_toflac_d="Yes";}
	elseif ($wav_toflac=="0") {
		$wav_toflac_d="No";}
	else {
		$wav_toflac_d="Not set";}
			
	echo "<br>Compress uploaded wav files to flac: $wav_toflac_d";
	
	echo "<select name=\"wav_toflac\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($use_tags) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

		echo " </select>";


	#guests_can_open
	$guests_can_open=query_one("SELECT Value from PumilioSettings WHERE Settings='guests_can_open'", $connection);

	if ($guests_can_open=="1")
		$guests_can_open_d="Yes";
	elseif ($guests_can_open=="0")
		$guests_can_open_d="No";
	else
		$guests_can_open_d="Not set";

	echo "<br>Allow users that are not logged in to open the files: $guests_can_open_d";
	
	echo "<select name=\"guests_can_open\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($guests_can_open) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

		echo " </select>";


	#guests_can_download
	$guests_can_dl=query_one("SELECT Value from PumilioSettings WHERE Settings='guests_can_dl'", $connection);

	if ($guests_can_dl=="1")
		$guests_can_dl_d="Yes";
	elseif ($guests_can_dl=="0")
		$guests_can_dl_d="No";
	else
		$guests_can_dl_d="Not set";

	echo "<br>Allow users that are not logged in to download the files: $guests_can_dl_d";
	
	echo "<select name=\"guests_can_dl\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($guests_can_dl)
			{echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";}
		else
			{echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";}

		echo " </select>";



	#level data to share
	$public_leveldata=query_one("SELECT Value from PumilioSettings WHERE Settings='public_leveldata'", $connection);
	
	if ($public_leveldata==""){
		$public_leveldata = "0";
		}

	echo "<br>Level of data to display to guests: $public_leveldata";
	
	echo "<select name=\"public_leveldata\" class=\"ui-state-default ui-corner-all formedge\">";
		echo "<option value=\"0\">0</option>
			<option value=\"1\">1</option>
			<option value=\"2\">2</option>
			<option value=\"3\">3</option>";
		echo "</select>";


	#spectrogram_palette
	$spectrogram_palette=query_one("SELECT Value from PumilioSettings WHERE Settings='spectrogram_palette'", $connection);
	
	echo "<br>Color palette to use for the spectrograms: ";
	if ($spectrogram_palette=="1")
		echo "dark background";
	elseif ($spectrogram_palette=="2")
		echo "white background";

	
	echo "<select name=\"spectrogram_palette\" class=\"ui-state-default ui-corner-all formedge\">";
		if ($spectrogram_palette==2 || $spectrogram_palette=="") {
				echo "<option value=\"1\">dark background</option>
				<option SELECTED value=\"2\">white background</option>";}
		else {
				echo "<option SELECTED value=\"1\">dark background</option>
				<option value=\"2\">white background</option>";}

		echo "</select>
		<p><input type=submit value=\" Update system behavior \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";

	echo "</div>";
?>