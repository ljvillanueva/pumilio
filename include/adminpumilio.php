<?php


use \DByte\DB;
DB::$c = $pdo;


//Custom name of the app
if ($tt==1) {
	echo "<div class=\"success\" id=\"tt1\">The database was updated.</div>";
	}



echo "
	<div class=\"row\">
	<div class=\"col-lg-6\">
		<div class=\"panel panel-primary\">
		<div class=\"panel-heading\">
			<h3 class=\"panel-title\">General settings</h3>
		</div>
        <div class=\"panel-body\">";

echo "<form action=\"include/editpumiliosettings.php\" method=\"POST\">
	<input type=\"hidden\" name=\"settings\" value=\"top\">
	<div class=\"form-group\">
		<label for=\"app_custom_name\">Name for this system:</label>
		<input type=\"text\" id=\"app_custom_name\" name=\"app_custom_name\" value=\"$app_custom_name\" class=\"form-control\">
	</div>";

echo "<div class=\"form-group\">
	<label for=\"app_custom_text\">Short description of this system (will appear on the homepage):</label>";
	echo "<textarea class=\"form-control\" rows=\"3\" name=\"app_custom_text\" id=\"app_custom_text\">$app_custom_text</textarea>
</div>";

echo "<div class=\"form-group\">
	<label for=\"acknowledgement\">Acknowledgements (will appear on the homepage):</label>";
	echo "<textarea class=\"form-control\" rows=\"3\" name=\"acknowledgement\" id=\"acknowledgement\">$acknowledgement</textarea>
</div>";
	

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
	
echo "<div class=\"form-group\">
	<label for=\"mapping_system\">Mapping system:</label>";

	echo "<select name=\"mapping_system\" id=\"mapping_system\" class=\"form-control\">";

		if ($mapping_system=="GMaps") {
			echo "<option value=\"None\">None</option>
				<option SELECTED value=\"Gmaps\">Google Maps</option>
				<option value=\"Leaflet\">Leaflet</option>";
			}
		elseif ($mapping_system=="Leaflet") {
			echo "<option value=\"None\">None</option>
				<option value=\"Gmaps\">Google Maps</option>
				<option SELECTED value=\"Leaflet\">Leaflet</option>";
			}
		else {
			echo "<option SELECTED value=\"None\">None</option>
				<option value=\"Gmaps\">Google Maps</option>
				<option value=\"Leaflet\">Leaflet</option>";
			}

	echo "</select></div>\n";
			
	#key not needed if < 25k hits
/*	#googlemaps_key
	if ($use_googlemaps=="3") {
		echo "<p>
		<a href=\"https://developers.google.com/maps/documentation/javascript/tutorial\" target=_blank>Request a key for Google Maps JavaScript API v3</a> (free and required for each server) 
			<a href=\"#\" onclick=\"window.open('help.php?topic=GoogleMaps3', 'help', 'width=650,height=550,status=yes,resizable=yes,scrollbars=auto')\"><img src=\"images/help.png\" title=\"Click for instructions\"></a><br>
		GoogleMaps v3 key: $googlemaps3_key";
			
		echo "<input type=\"text\" name=\"googlemaps3_key\" size=\"50\" maxlength=\"250\" value=\"$googlemaps3_key\">\n";
		}	*/
	
	#$map_only=query_one("SELECT Value from PumilioSettings WHERE Settings='map_only'", $connection);
	
	if ($map_only=="1"){
		$map_only_d="Main map";
		}
	elseif ($map_only=="0" || $map_only==""){
		$map_only_d="Full menu";
		}



#Google Analytics:
	echo "<div class=\"form-group\">
	<label for=\"googleanalytics_ID\">Google Analytics Tracking ID</label>";

	echo "<input type=\"text\" name=\"googleanalytics_ID\" id=\"googleanalytics_ID\" value=\"$googleanalytics_ID\" class=\"form-control\">
			<a href=\"#\" onclick=\"window.open('help.php?topic=GoogleAnalytics', 'help', 'width=650,height=550,status=yes,resizable=yes,scrollbars=auto')\"><img src=\"images/help.png\" title=\"Click for instructions\"></a>
		</div>\n";




echo "<div class=\"form-group\">
	<label for=\"map_only\">Main menu:</label>";
		
	echo "<select name=\"map_only\" id=\"map_only\" class=\"form-control\">";
		if ($map_only=="1") {
			echo "<option value=\"0\">Full menu</option>
				<option SELECTED value=\"1\">Main map</option>";
			}
		elseif ($map_only=="0" || $map_only=="") {
			echo "<option SELECTED value=\"0\">Full menu</option>
				<option value=\"1\">Main map</option>";
			}
	echo "</select>\n
	</div>";
	
	
#Copyright or CC?
	#$files_license = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license'", $connection);
	#$files_license_detail = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license_detail'", $connection);

	if ($files_license=="") {
		$files_license = "Not set";
		}

	echo "<div class=\"form-group\">
	<label for=\"lic1\">Retain copyright or share with a <a href=\"http://creativecommons.org/licenses/\" target=_blank>Creative Commons</a> license? $files_license</label><div class=\"form-group\">";
		
		echo "<select name=\"files_license\" id =\"lic1\" class=\"form-control\">";
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
		
		<input type=\"text\" class=\"form-control\" name=\"files_license_detail\" size=\"40\" maxlength=\"250\" value=\"$files_license_detail\">
	</div></div>";
	
		
	#Temp dir
	$temp_add_dir_f = "";
	#$temp_add_dir=query_one("SELECT Value from PumilioSettings WHERE Settings='temp_add_dir'", $connection);
	if ($temp_add_dir!=""){
		#check if dir exists and is readable
		if (!is_dir($temp_add_dir) || !is_readable($temp_add_dir)){
			$temp_add_dir_d="<em class=\"text-danger\">Directory does not exist or could not be accessed.</em>";

			#DB::query('DELETE FROM `PumilioSettings` WHERE `Settings` = "temp_add_dir"');
			}
		else {
			$temp_add_dir_d="<em>$temp_add_dir</em>";
			$temp_add_dir_f="$temp_add_dir";
			}
		}
	else {
		$temp_add_dir_d="Not set";
		}

	echo "<div class=\"form-group\">
	<label for=\"temp_add_dir\">Local directory for adding multiple files:</label>";

		$apacheuser = exec('whoami');
		echo "<input type=\"text\" name=\"temp_add_dir\" id=\"temp_add_dir\" value=\"$temp_add_dir_f\" class=\"form-control\">
				<img src=\"images/help.png\" title=\"Users can add files to the archive that are stored in the
					server or a network location mounted in the server. Add the full system path 
					in this field. The path needs to exist and be readable by the Apache user ($apacheuser).\">
			</div>";



	echo "<div class=\"form-group\">
	<label for=\"cores_to_use\">How many cores to use for background processes:</label>";
	$machine_cores = nocores();
	
	echo "<select name=\"cores_to_use\" id=\"cores_to_use\" class=\"form-control\">";
		for ($c = 0; $c < $machine_cores; $c++) {
			$cc = $c + 1;
			if ($cc == $cores_to_use){
				echo "<option SELECTED value=\"$cc\">$cc</option>\n";
				}
			else{
				echo "<option value=\"$cc\">$cc</option>\n";
				}
			}

	echo " </select> 
		<img src=\"images/help.png\" title=\"Set to maximum to speed up the background processes at the cost of server performance for other
			tasks. Set to a lower number to leave some cores for other processes.\">
		</div>";
	
	
	echo "<p><button type=\"submit\" class=\"btn btn-primary\"> Update system settings </button>
	</form>



	</div>
</div>

</div>
<div class=\"col-lg-6\">
";



	#Image settings
	echo "
	<div class=\"panel panel-primary\">
	<div class=\"panel-heading\">
		<h3 class=\"panel-title\">Image settings</h3>
	</div>
    <div class=\"panel-body\">";

	if ($imgset == 1) {
		echo "<div class=\"success\" id=\"imgset\">The database was updated. To force the system to recreate the images:<br>
		<form method=\"GET\" action=\"include/delauxfiles.php\" target=\"delauxfiles\" onsubmit=\"window.open('', 'delauxfiles', 'width=450,height=700,status=yes,resizable=yes,scrollbars=auto')\">
			<input type=\"hidden\" name=\"op\" value=\"7\">
			<button type=\"submit\" class=\"btn btn-primary\"> Delete all images from system </button>
			</form>
		</div>";
		}

	echo "<form action=\"include/editpumiliosettings.php\" method=\"POST\">
		<input type=\"hidden\" name=\"settings\" value=\"image\">";
	#Max freq to draw in spectrograms
	#$max_spec_freq=query_one("SELECT Value from PumilioSettings WHERE Settings='max_spec_freq'", $connection);

	if ($max_spec_freq == "max") {
		$max_spec_freq_d = "Maximum for each file";
		}
	elseif ($max_spec_freq == "") {
		$max_spec_freq_d = "22050 Hz";
		}
	else {
		$max_spec_freq_d = $max_spec_freq . " Hz";
		}

	echo "
	<label for=\"max_spec_freq\">Maximum acoustic frequency for the spectrograms:</label>";
	
	echo "<select name=\"max_spec_freq\" id=\"max_spec_freq\" class=\"form-control\">";
		if ($max_spec_freq == "max") {
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
		elseif ($max_spec_freq == "") {
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
			$m1 = "";
			$m2 = "";
			$m3 = "";
			$m4 = "";
			$m5 = "";
			$m6 = "";
			$m7 = "";
			$m8 = "";
			$m9 = "";
		
			if ($max_spec_freq == 24000)
				$m1 = "SELECTED";
			elseif ($max_spec_freq == 22050)
				$m2 = "SELECTED";
			elseif ($max_spec_freq == 16000)
				$m3 = "SELECTED";
			elseif ($max_spec_freq == 11025)
				$m4 = "SELECTED";
			elseif ($max_spec_freq == 10000)
				$m5 = "SELECTED";
			elseif ($max_spec_freq == 8000)
				$m6 = "SELECTED";
			elseif ($max_spec_freq == 6000)
				$m7 = "SELECTED";
			elseif ($max_spec_freq == 4000)
				$m8 = "SELECTED";
			elseif ($max_spec_freq == 3000)
				$m9 = "SELECTED";
				
				
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

	#FFT window size
	echo "<label for=\"fft\">FFT window size:</label>";
	
	echo "<select name=\"fft\" id=\"fft\" class=\"form-control\">";
		
		$fft1="";
		$fft2="";
		$fft3="";
		$fft4="";
		$fft5="";
		$fft6="";

		if ($fft == 4096)
			$fft1="SELECTED";
		elseif ($fft == 2048)
			$fft2="SELECTED";
		elseif ($fft == 1024)
			$fft3="SELECTED";
		elseif ($fft == 512)
			$fft4="SELECTED";
		elseif ($fft == 256)
			$fft5="SELECTED";
		elseif ($fft == 128)
			$fft6="SELECTED";
			
		echo "<option $fft1>4096</option>
			<option $fft2>2048</option>
			<option $fft3>1024</option>
			<option $fft4>512</option>
			<option $fft5>256</option>
			<option $fft6>128</option>";

	echo " </select>";


	#spectrogram_palette
	#$spectrogram_palette=query_one("SELECT Value from PumilioSettings WHERE Settings='spectrogram_palette'", $connection);
	
	echo "<label for=\"spectrogram_palette\">Color palette to use for the spectrograms</label>";
	
	if ($sox_images == TRUE){
		$sox_pal1 = "yellow, red, purple";
		$sox_pal2 = "yellow, green, blue";
		$sox_pal3 = "blue, green red";
		$sox_pal4 = "pink, red, green";
		$sox_pal5 = "pink, blue, green";
		$sox_pal6 = "blue, purple, red";
		
		if ($spectrogram_palette=="1"){
			echo $sox_pal1;
			$selected1 = "SELECTED";
			}
		elseif ($spectrogram_palette=="2"){
			echo $sox_pal2;
			$selected2 = "SELECTED";
			}
		elseif ($spectrogram_palette=="3"){
			echo $sox_pal3;
			$selected3 = "SELECTED";
			}
		elseif ($spectrogram_palette=="4"){
			echo $sox_pal4;
			$selected4 = "SELECTED";
			}
		elseif ($spectrogram_palette=="5"){
			echo $sox_pal5;
			$selected5 = "SELECTED";
			}
		elseif ($spectrogram_palette=="6"){
			echo $sox_pal6;
			$selected6 = "SELECTED";
			}
	
		echo "<select name=\"spectrogram_palette\" id=\"spectrogram_palette\" class=\"form-control\">";
			echo "<option $selected1 value=\"1\">$sox_pal1</option>
			<option $selected2 value=\"2\">$sox_pal2</option>
			<option $selected3 value=\"3\">$sox_pal3</option>
			<option $selected4 value=\"4\">$sox_pal4</option>
			<option $selected5 value=\"5\">$sox_pal5</option>
			<option $selected6 value=\"6\">$sox_pal6</option>";
			
		echo "</select>";
		}
	else{
		/*if ($spectrogram_palette == "1"){
			echo "dark background";
			}
		elseif ($spectrogram_palette == "2"){
			echo "white background";
			}*/
	
		echo "<select name=\"spectrogram_palette\" id=\"spectrogram_palette\" class=\"form-control\">";
		if ($spectrogram_palette == 2 || $spectrogram_palette == "") {
			echo "<option value=\"1\">dark background</option>
			<option SELECTED value=\"2\">white background</option>";
			}
		else {
			echo "<option SELECTED value=\"1\">dark background</option>
			<option value=\"2\">white background</option>";
			}
		echo "</select>";
		}
		

	echo "
	<br><button type=\"submit\" class=\"btn btn-primary\"> Update spectrogram settings </button>
	</form>
	</div>
</div>";




	#System behavior
	echo "
	<div class=\"panel panel-primary\">
	<div class=\"panel-heading\">
		<h3 class=\"panel-title\">System behavior</h3>
	</div>
    <div class=\"panel-body\">


	<form action=\"include/editpumiliosettings.php\" method=\"POST\">
	<input type=\"hidden\" name=\"settings\" value=\"bottom\">";	
		
	if ($tt == 2) {
		echo "<div class=\"success\" id=\"tt2\">The database was updated.</div>";
		}
	


	#compress wav to flac
	#$wav_toflac=query_one("SELECT Value from PumilioSettings WHERE Settings='wav_toflac'", $connection);

	if ($wav_toflac=="1") {
		$wav_toflac_d="Yes";
		}
	elseif ($wav_toflac=="0") {
		$wav_toflac_d="No";
		}
	else {
		$wav_toflac_d="Not set";
		}
			
	echo "<div class=\"form-group\">
	<label for=\"wav_toflac\">Compress uploaded wav files to flac</label>";
	
	echo "<select name=\"wav_toflac\" id=\"wav_toflac\" class=\"form-control\">";
		if ($use_tags) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

	echo " </select></div>";


	#guests_can_open
	#$guests_can_open=query_one("SELECT Value from PumilioSettings WHERE Settings='guests_can_open'", $connection);

	if ($guests_can_open=="1"){
		$guests_can_open_d="Yes";
		}
	elseif ($guests_can_open=="0"){
		$guests_can_open_d="No";
		}
	else{
		$guests_can_open_d="Not set";
		}

	echo "<div class=\"form-group\">
	<label for=\"guests_can_open\">Allow users that are not logged in to open the files</label>";
	
	echo "<select name=\"guests_can_open\" id=\"guests_can_open\" class=\"form-control\">";
		if ($guests_can_open) {
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else {
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}

	echo " </select></div>";


	#guests_can_download
	#$guests_can_dl=query_one("SELECT Value from PumilioSettings WHERE Settings='guests_can_dl'", $connection);

	if ($guests_can_dl=="1"){
		$guests_can_dl_d="Yes";
		}
	elseif ($guests_can_dl=="0"){
		$guests_can_dl_d="No";
		}
	else{
		$guests_can_dl_d="Not set";
		}

	echo "<div class=\"form-group\">
	<label for=\"guests_can_dl\">Allow users that are not logged in to download the files</label>";
	
	echo "<select name=\"guests_can_dl\" id=\"guests_can_dl\" class=\"form-control\">";
		if ($guests_can_dl){
			echo "<option SELECTED value=\"1\">Yes</option>
				<option value=\"0\">No</option>";
			}
		else{
			echo "<option value=\"1\">Yes</option>
				<option SELECTED value=\"0\">No</option>";
			}
	echo " </select></div>";



	#level data to share
	#$public_leveldata=query_one("SELECT Value from PumilioSettings WHERE Settings='public_leveldata'", $connection);
	
	if ($default_qf==""){
		$default_qf = "0";
		}

	echo "<div class=\"form-group\">
	<label for=\"default_qf\">Level of data to display to guests</label>";
	
	echo "<select name=\"default_qf\" id=\"default_qf\" class=\"form-control\">";

	$query_level = "SELECT * FROM QualityFlags ORDER BY QualityFlagID";
	$result_level = query_several($query_level, $connection);
	$nrows_level = mysqli_num_rows($result_level);

	for ($i=0; $i<$nrows_level; $i++){
		$row_level = mysqli_fetch_array($result_level);
		extract($row_level);
		if ($QualityFlagID == $default_qf){
			echo "\n<option value=\"$QualityFlagID\" SELECTED>$QualityFlagID - $QualityFlag</option>";
			}
		else{
			echo "\n<option value=\"$QualityFlagID\">$QualityFlagID - $QualityFlag</option>";
			}
		}
	echo "</select></div>";
	
	
	#allow access using XML
	#$use_xml=query_one("SELECT Value from PumilioSettings WHERE Settings='use_xml'", $connection);

	if ($use_xml == "1"){
		$use_xml_d = "Yes";
		}
	elseif ($use_xml == "0"){
		$use_xml_d = "No";
		}
	else {
		$use_xml_d = "Not set";
		}

	echo "<div class=\"form-group\">
	<label for=\"use_xml\">Allow access using the XML API</label>";

	echo "<select name=\"use_xml\" id=\"use_xml\" class=\"form-control\">";
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
	echo " </select></div>";
	
	
	#Who to allow to access XML?
	if ($use_xml=="1"){
		if ($xml_access=="1"){
			$xml_access_d="Any";
			}
		elseif ($xml_access=="0"){
			$xml_access_d="Users";
			}
		else{
			$xml_access="1";
			$xml_access_d="Any";
			}

		echo "<div class=\"form-group\">
		<label for=\"xml_access\">Who can access via XML</label>";

		echo "<select name=\"xml_access\" id=\"xml_access\" class=\"form-control\">";
			if ($xml_access=="1") {
				echo "<option SELECTED value=\"1\">Any</option>
					<option value=\"0\">Only Users</option>";
				}
			elseif ($xml_access=="0"){
				echo "<option value=\"1\">Any</option>
					<option SELECTED value=\"0\">Only Users</option>";
				}
		echo " </select></div>";
		}
	
	echo "
	<button type=\"submit\" class=\"btn btn-primary\"> Update system behavior </button>
	</form>";

	echo "</div>
	</div>";




#close half and row div
echo "</div></div>";











#NEW ROW
echo "<div class=\"row\">
	<div class=\"col-lg-6\">";


	?>



		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Manage users</h3>
		</div>
        <div class="panel-body">
	

			<?php
			if ($u==1) {
				echo "<p><div class=\"success\">User was added successfully</div>";
				}
			if ($u==2) {
				echo "<p><div class=\"error\">That username is already in use, please use another.</div>";
				}
			
			echo "<form action=\"$app_url/include/add_user.php\" method=\"POST\" id=\"AddUserForm\">";
			?>
			<p>Username: <br><input type="text" name="UserName" maxlength="20" class="form-control"><br>
			Full name of the user: <br><input type="text" name="UserFullname" maxlength="100" class="form-control"><br>
			User email address: <br><input type="text" name="UserEmail" maxlength="100" class="form-control"><br>
			User password:<br><input type="password" name="newpassword1" id="newpassword1" maxlength="20" class="form-control" /><br>
			Please retype the password:<br><input type="password" name="newpassword2" id="newpassword2" maxlength="20" class="form-control" /><br>
			User role:<br><select name="UserRole" class="form-control">
				<option value="user">Regular user</option>
				<option value="admin">Administrator</option>
			</select><br>
			<button type="submit" class="btn btn-primary"> Add user </button>
			</form><br><br>
			
			<?php

			if ($u==3) {
				echo "<p><div class=\"success\">Change was made successfully</div>";
				}

			$no_users = DB::column('SELECT COUNT(*) FROM `Users` WHERE `UserActive` LIKE 1');
			
			$query = "SELECT * from Users WHERE UserActive='1' ORDER BY UserName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);

			echo "<p>This system has $no_users users:
				<table border=\"0\">";

			for ($i=0; $i<$nrows; $i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				
				echo "<tr>
					<td><strong>Name</strong></td><td>&nbsp;</td><td><strong>Username</strong></td><td>&nbsp;</td><td><strong>Role</strong></td><td>&nbsp;</td><td><strong>Change password</strong></td>
					</tr><tr>";
				
				echo "<td><form action=\"include/edit_user.php\" method=\"POST\">$UserFullname</td><td>&nbsp;</td><td>$UserName</td><td>&nbsp;</td><td>";
				if ($UserRole == "admin") {
					#$other_admins=query_one("SELECT COUNT(*) FROM Users WHERE UserRole='admin' AND UserID!='$UserID'", $connection);
					$other_admins = DB::column('SELECT COUNT(*) FROM `Users` WHERE  `UserRole`=`admin` AND `UserID`!= ?', $UserID);
					if ($other_admins > 0 && $UserName != $username) {
						echo "<input type=\"hidden\" name=\"ac\" value=\"remadmin\">
						<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
						<input type=submit value=\" Remove from administrators \"></form>";
						}
					else {
						echo "[Administrator]</form>";
						}
					}
				else {
					echo "<input type=\"hidden\" name=\"ac\" value=\"makeadmin\">
					<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
					<input type=submit value=\" Make administrator \"></form>";
					}

				echo "</td><td>&nbsp;</td><td>";
				
				if ($UserName == $username){
					echo "<a href=\"edit_myinfo.php?t=2\" title=\"Edit my information or change password\">Change my password</a>";
					}
				else{
					echo "<form method=\"GET\" action=\"include/edit_user_password.php\" target=\"editpassword\" onsubmit=\"window.open('', 'editpassword', 'width=450,height=400,status=yes,resizable=yes,scrollbars=yes')\">
						<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
						<button type=\"submit\" class=\"btn btn-primary\"> Edit user password </button>
					</form>
					</td></tr>";
					}
				}
			echo "</table>";
			?>
				</ul>
				
			<hr noshade>
			<h4>Set users as inactive</h4>
			<?php

			if ($u==4) {
				echo "<p><div class=\"success\">User was set as inactive successfully</div>";
				}

			#Delete div
			echo "<div id=\"dialog\" title=\"Set user as inactive?\">
			<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The user will be set as inactive immediately and will not be able to log in. Are you sure?</p></div>";

			$query = "SELECT * from Users WHERE UserName!='$username' AND UserActive='1' ORDER BY UserName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			if ($nrows==0) {
				echo "There are no other users and you can not set yourself as inactive.";
				}
			else {
				echo "
				<form action=\"include/edit_user.php\" method=\"POST\" id=\"delform\" name=\"delform\">
				<input type=\"hidden\" name=\"ac\" value=\"inactive\">
				<select name=\"UserID\">";

				for ($j=0; $j<$nrows; $j++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					echo "<option value=\"$UserID\">$UserFullname ($UserName)</option>";
					}
				echo "</select>";

				echo " &nbsp;&nbsp;<button type=\"submit\" class=\"btn btn-primary\"> Set user as inactive </button>
				</form>";
				}

			$query = "SELECT * from Users WHERE UserName!='$username' AND UserActive='0' ORDER BY UserName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			if ($nrows==0) {
				#echo "There are no other users and you can not set yourself as inactive.";
				}
			else {
				echo "
				<form action=\"include/edit_user.php\" method=\"POST\">
				<input type=\"hidden\" name=\"ac\" value=\"activate\">
				<select name=\"UserID\" class=\"form-control\">";

				for ($j=0; $j<$nrows; $j++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					echo "<option value=\"$UserID\">$UserFullname ($UserName)</option>";
					}
				echo "</select>";

				echo " &nbsp;&nbsp;
				<button type=\"submit\" class=\"btn btn-primary\"> Reset user as active </button>
				</form>";
				}
			?>
			</p>
			</div>
			</div>
</div>

			<?php
				
echo "
<div class=\"col-lg-6\">
<div class=\"panel panel-primary\">
	<div class=\"panel-heading\">
		<h3 class=\"panel-title\">Sensors</h3>
	</div>
    <div class=\"panel-body\">

	<strong>Add sensors to the database</strong>
		<form action=\"include/add_sensors.php\" method=\"POST\" id=\"AddSensors\">
			<p>Recorder:<br><input type=\"text\" name=\"Recorder\" maxlength=\"100\" size=\"40\"><br>
			Microphone: <br><input type=\"text\" name=\"Microphone\" maxlength=\"80\" size=\"40\"><br>
			Notes of the sensor: <br><input type=\"text\" name=\"Notes\" maxlength=\"255\" size=\"40\"><br>
			<button type=\"submit\" class=\"btn btn-primary\"> Add sensor </button>
		</form>";

#Sensors in the db:
echo "<hr noshade>";

$no_sensors = DB::column('SELECT COUNT(*) FROM `Sensors`');

if ($no_sensors == 0){
	echo "<p>There are no sensors in the system.";
	}
else {
	$rows = DB::fetch('SELECT * FROM `Sensors` ORDER BY `SensorID`', array(TRUE));
	echo "<p>The system has the following ". count($rows) ." sensors:
		<table>";

	echo "<tr>
			<td>Sensor ID</td>
			<td>&nbsp;</td>
			<td>Recorder</td>
			<td>&nbsp;</td>
			<td>Microphone</td>
			<td>&nbsp;</td>
			<td>Notes</td>
			<td>&nbsp;</td>
			<td>Edit</td>
		</tr>\n";


 	foreach($rows as $row){
#	for ($i = 0; $i < $nrows; $i++) {
		#$row = mysqli_fetch_array($result);
		#extract($row);
		
			echo "<tr>
				<td>" . $row->SensorID . "</td>
				<td>&nbsp;</td>
				<td>" . $row->Recorder . "</td>
				<td>&nbsp;</td>
				<td>" . $row->Microphone . "</td>
				<td>&nbsp;</td>
				<td>" . $row->Notes . "</td>
				<td>&nbsp;</td>
				<td><a href=\"sensor_edit.php?SensorID=" . $row->SensorID . "\"><img src=\"images/pencil.png\"></td>
			</tr>\n";
		
		}
	echo "</table>";
	}

echo "</div>


<!-- close row -->
</div></div></div>
";
			
		





#NEW ROW
echo "<div class=\"row\">
	<div class=\"col-lg-6\">";			

	#KML

	echo "

	<div class=\"panel panel-primary\">
			<div class=\"panel-heading\">
				<h3 class=\"panel-title\">Add a KML/KMZ layer</h3>
			</div>
	        <div class=\"panel-body\">


		<form action=\"include/editkml.php\" method=\"POST\" id=\"AddKML\">
			<input type=\"hidden\" name=\"op\" value=\"1\">
			
			<div class=\"form-group\">
				<label for=\"KmlName\">Name</label>
				<input type=\"text\" name=\"KmlName\" id=\"KmlName\" class=\"form-control\">
			</div>
			
			<div class=\"form-group\">
				<label for=\"KmlURL\">Complete URL</label>
				<input type=\"text\" name=\"KmlURL\" id=\"KmlURL\" class=\"form-control\" value=\"http://\">
			</div>

			<div class=\"form-group\">
				<label for=\"KmlNotes\">Notes</label>
				<input type=\"text\" name=\"KmlNotes\" id=\"KmlNotes\" class=\"form-control\">
			</div>

			<button type=\"submit\" class=\"btn btn-primary\"> Add layer </button>
		</form>
		<br><br>\n";
		

	$no_kml=query_one("SELECT COUNT(*) FROM Kml", $connection);
	if ($no_kml>0) {
		echo "<div class=\"form-group\">
				<label>KML/KMZ layers</label>
			<ul>";

		$query_kml = "SELECT * FROM Kml ORDER BY KmlName";
		$result_kml=query_several($query_kml, $connection);
		$nrows_kml = mysqli_num_rows($result_kml);

		for ($k=0;$k<$nrows_kml;$k++) {
			$row_kml = mysqli_fetch_array($result_kml);
			extract($row_kml);

			echo "<li><form action=\"include/editkml.php\" method=\"POST\">$KmlName 
					(<a href=\"http://maps.google.com/maps?q=$KmlURL\" title=\"Open layer in GoogleMaps\" target=\"_blank\">$KmlURL</a>)
					<input type=\"hidden\" name=\"op\" value=\"2\">
					<input type=\"hidden\" name=\"KmlID\" value=\"$KmlID\">
					<button type=\"submit\" class=\"btn btn-primary\"> Delete </button>
				</form>\n";
				
				$default_kml=query_one("SELECT KmlDefault FROM Kml WHERE KmlID='$KmlID'", $connection);
				if ($default_kml == 0) {
					$selkml0 = "SELECTED";
					$selkml1 = "";
					$selkml2 = "";
					}
				elseif ($default_kml == 1) {
					$selkml0 = "";
					$selkml1 = "SELECTED";
					$selkml2 = "";
					}
				elseif ($default_kml == 2) {
					$selkml0 = "";
					$selkml1 = "";
					$selkml2 = "SELECTED";
					}
					
				echo "
				<form action=\"include/editkml2.php\" method=\"GET\"> 
					<input type=\"hidden\" name=\"KmlID\" value=\"$KmlID\">
					<select name=\"KmlDefault\" class=\"form-control\">
						<option value=\"0\" $selkml0>optional</option>
						<option value=\"1\" $selkml1>default</option>
						<option value=\"2\" $selkml2>always on</option>
					</select>
					<button type=\"submit\" class=\"btn btn-primary\"> Change status </button>
				</form>\n";
			}
			echo "</ul></div>";
		}
	else {
		echo "<p>There are no KML/KMZ data layers.";
		}

	echo "</div></div>
	</div>
	<div class=\"col-lg-6\">";

		
echo "
<div class=\"panel panel-primary\">
	<div class=\"panel-heading\">
		<h3 class=\"panel-title\">Quality control</h3>
	</div>
    <div class=\"panel-body\">";



//Custom name of the app
if ($t==9) {
	if ($u==1) {
		echo "<div class=\"success\">The database was updated.</div>";
		}
	elseif ($u==2) {
		echo "<div class=\"error\">The Quality Flag could not be added. Please try again.</div>";
		}
	elseif ($u==3) {
		echo "<div class=\"notice\">The Quality Flag already exists in the database.</div>";
		}
	}


	echo "<p><strong><a href=\"qc.php\">Data extraction for quality control</a>
	<p><a href=\"qa.php\">Figures for quality control</a></strong><br><br>";
							
							
$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
$result_qf = mysqli_query($connection, $query_qf)
	or die (mysqli_error($connection));
$nrows_qf = mysqli_num_rows($result_qf);

echo "<p>
	<table border=\"0\">
	<tr>
		<td><strong>Quality Flag</strong></td><td>&nbsp;</td><td><strong>Meaning</strong></td><td>&nbsp;</td><td><strong>Delete (files that have it will be changed to 0)</strong></td>
	</tr>";

	for ($f=0;$f<$nrows_qf;$f++) {
		$row_qf = mysqli_fetch_array($result_qf);
		extract($row_qf);
		echo "	<tr>
		<td>$QualityFlagID</td><td>&nbsp;</td><td>$QualityFlag</td><td>&nbsp;</td><td>";
		if ($QualityFlagID=="0"){
			echo " (default) ";
			}
		else {
			echo "<a href=\"include/delqf.php?QualityFlagID=$QualityFlagID\"><img src=\"images/cross.png\"></a>";
			}
		echo "</td>
		</tr>";
		}

echo "</table>";

echo "<p><div style=\"width: 200px;\"><form action=\"include/addqf.php\" method=\"POST\" id=\"AddQF\">Add new Quality Flags:<br>
		Quality Flag Value:<br>
			<input name=\"QualityFlagID\" type=\"text\" maxlength=\"4\" size=\"4\" class=\"form-control\"> (Integer or decimal value)<br>
		Quality Flag Meaning:<br>
			<input name=\"QualityFlag\" type=\"text\" maxlength=\"40\" size=\"40\" class=\"form-control\"><br>
		<button type=\"submit\" class=\"btn btn-primary\"> Add quality flag </button>
	</form></div><br><br>";

if ($u==4) {
	echo "<div class=\"success\">The database was updated.</div>";
	}

echo "Minimum Quality Flag to display to anonymous users: $default_qf
	<br>&nbsp;&nbsp;&nbsp;(useful to hide unchecked data to the public)";

echo "<p><form action=\"include/editqfdefault.php\" method=\"POST\" id=\"EditQFDef\">";

	$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
	$result_qf = mysqli_query($connection, $query_qf)
		or die (mysqli_error($connection));
	$nrows_qf = mysqli_num_rows($result_qf);

	echo "<select name=\"defaultqf\">";

		for ($f=0;$f<$nrows_qf;$f++) {
			$row_qf = mysqli_fetch_array($result_qf);
			extract($row_qf);
			if ($QualityFlagID == $default_qf){
				echo "<option value=\"$QualityFlagID\" SELECTED>$QualityFlagID: $QualityFlag</option>\n";
				}
			else {
				echo "<option value=\"$QualityFlagID\">$QualityFlagID: $QualityFlag</option>\n";
				}
			}

	echo "</select><br>
	<button type=\"submit\" class=\"btn btn-primary\"> Change </button>
	<br>
	</form>";
	
echo "</div></div>
</div></div>";

					?>
			

<div class="row">
	<div class="col-lg-6">


			<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Maintenance</h3>
			</div>
	        <div class="panel-body">

				<?php
					echo "<p>Execute maintenance tasks:";

					echo "<p><form method=\"GET\" action=\"admin_generate.php\">
					<button type=\"submit\" class=\"btn btn-primary\"> Generate mp3 and image files </button>
					</form> <br>";
					
					echo "<p><form method=\"GET\" action=\"include/emptytmp.php\" target=\"tmp\" onsubmit=\"window.open('', 'tmp', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Cleanup temp folder </button>
					</form> <br>";
					
					/*
					echo "<p><form method=\"GET\" action=\"include/systemlog.php\" target=\"systemlog\" onsubmit=\"window.open('', 'systemlog', 'width=850,height=620,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" System log \"></form><br><hr noshade>";
					*/

					#Check database values
					echo "<p><form method=\"GET\" action=\"include/checkdb.php\" target=\"checkdb\" onsubmit=\"window.open('', 'checkdb', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Check database for missing data and optimize tables </button>
					</form>  <br>";
					
					#Window to get disk used
					echo "<p><form method=\"GET\" action=\"include/diskused.php\" target=\"disk\" onsubmit=\"window.open('', 'disk', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Check disk usage </button>
					</form> <br>";

					#Delete mp3 or images
					echo "<p><form method=\"GET\" action=\"include/delauxfiles.php\" target=\"delauxfiles\" onsubmit=\"window.open('', 'delauxfiles', 'width=450,height=700,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Delete mp3 and/or images from system </button>
					</form> <br>";

					#Delete collection
					echo "<p><form method=\"GET\" action=\"include/delcol.php\" target=\"delcol\" onsubmit=\"window.open('', 'delcol', 'width=550,height=400,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Delete a collection and all the files </button>

					</form>  <br>";


				?>
			</div></div>
</div><div class="col-lg-6">&nbsp;</div></div>