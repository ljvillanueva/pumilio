<?php


#DB
use \DByte\DB;
DB::$c = $pdo;

//Custom name of the app
if ($tt==1) {
	echo "<div class=\"success\" id=\"tt1\">The database was updated.</div>";
	}

echo "<div class=\"panel panel-primary\">
		<div class=\"panel-heading\">
			<h3 class=\"panel-title\">General Settings</h3>
		</div>
        <div class=\"panel-body\">";

echo "<form action=\"include/editpumiliosettings.php\" method=\"POST\">
	<input type=\"hidden\" name=\"settings\" value=\"top\">
	<div class=\"form-group\">
		<label for=\"app_custom_name\">This installation custom name:</label>
		<input type=\"text\" id=\"app_custom_name\" name=\"app_custom_name\" value=\"$app_custom_name\" class=\"form-control\">
	</div>";

echo "<div class=\"form-group\">
	<label for=\"app_custom_text\">Short description of this system:</label>";
	echo "<input type=\"text\" name=\"app_custom_text\" id=\"app_custom_text\" value=\"$app_custom_text\" class=\"form-control\">
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
	<label for=\"map_only\">Main Menu:</label>";
		
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
	<label for=\"lic1\">Retain copyright or share with a <a href=\"http://creativecommons.org/licenses/\" target=_blank>Creative Commons</a> license? $files_license</label>";
		
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
		
		<input type=\"text\" name=\"files_license_detail\" size=\"40\" maxlength=\"250\" value=\"$files_license_detail\">
	</div>";
	
		
	#Temp dir
	$temp_add_dir_f = "";
	#$temp_add_dir=query_one("SELECT Value from PumilioSettings WHERE Settings='temp_add_dir'", $connection);
	if ($temp_add_dir!=""){
		#check if dir exists and is readable
		if (!is_dir($temp_add_dir) || !is_readable($temp_add_dir)){
			$temp_add_dir_d="<em style=\"color:red;\">Directory does not exist or could not be accessed.</em>";

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
		<input type=submit value=\" Change selection \"> <a href=\"#\" onclick=\"window.open('help.php?topic=R', 'help', 'width=650,height=550,status=yes,resizable=yes,scrollbars=auto')\"><img src=\"images/help.png\" title=\"Help\" alt=\"Help\"></a>
		</form>";
*/

	#Cores to use
	#$cores_to_use=query_one("SELECT Value from PumilioSettings WHERE Settings='cores_to_use'", $connection);
	/*if ($cores_to_use == ""){ 
		$cores_to_use_d = "Not set (use 1)";
		}
	else {
		$cores_to_use_d = $cores_to_use;
		}*/

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
</div>";






	#Image settings
	echo "
	<div class=\"panel panel-primary\">
	<div class=\"panel-heading\">
		<h3 class=\"panel-title\">Image Settings</h3>
	</div>
    <div class=\"panel-body\">";

	if ($imgset == 1) {
		echo "<div class=\"success\" id=\"imgset\">The database was updated. To force the system to recreate the images:<br>
		<form method=\"GET\" action=\"include/delauxfiles.php\" target=\"delauxfiles\" onsubmit=\"window.open('', 'delauxfiles', 'width=450,height=700,status=yes,resizable=yes,scrollbars=auto')\">
			<input type=\"hidden\" name=\"op\" value=\"7\">
			<input type=submit value=\" Delete all images from system \"></form>
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

	echo "<div class=\"form-group\">
	<label for=\"max_spec_freq\">Maximum acoustic frequency for the spectrograms</label>";
	
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

	echo " </select>
	</div>";

	#FFT window size
	echo "<div class=\"form-group\">
	<label for=\"fft\">FFT window size";
	
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

	echo " </select></div>";


	#spectrogram_palette
	#$spectrogram_palette=query_one("SELECT Value from PumilioSettings WHERE Settings='spectrogram_palette'", $connection);
	
	echo "<div class=\"form-group\">
	<label for=\"spectrogram_palette\">Color palette to use for the spectrograms</label>";
	
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
		
	echo "</div>";


	echo "
	<button type=\"submit\" class=\"btn btn-primary\"> Update spectrogram settings </button>
	</form>
	</div>
</div>";








#Custom pages
$btn1text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn1text'));
$btn1url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn1url'));
$btn2text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn2text'));
$btn2url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn2url'));
$btn3text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn3text'));
$btn3url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn3url'));
$btn4text = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn4text'));
$btn4url = DB::column('SELECT Value FROM `PumilioSettings` WHERE `Settings` = ?', array('btn4url'));

echo "
<a name=\"homelink\"></a>
	<div class=\"panel panel-primary\">
	<div class=\"panel-heading\">
		<h3 class=\"panel-title\">Custom Homepage Links</h3>
	</div>
    <div class=\"panel-body\">
    	Here you can add links to pages that will appear in the homepage. These links can be
    		useful to display a link to your lab's website, a page about the methods,
    		or other relevant pages. If the text or URL are empty, the button will not be used.";


	echo "

	<form action=\"include/editpumiliosettings.php\" method=\"POST\">
		<input type=\"hidden\" name=\"settings\" value=\"homelink\">
		
		<table class=\"table table-striped\">
		<thead> 
			<tr> 
				<th>Order</th>
				<th>Button text</th>
				<th>Link URL</th>
			</tr>
			</thead> 

		<tbody> 

			<tr> 
				<th>1</th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn1text\" name=\"btn1text\" placeholder=\"Text\" value=\"$btn1text\"></th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn1url\" name=\"btn1url\" placeholder=\"URL\" value=\"$btn1url\"></th>
			</tr>

			<tr> 
				<th>2</th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn2text\" name=\"btn2text\" placeholder=\"Text\" value=\"$btn2text\"></th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn2url\" name=\"btn2url\" placeholder=\"URL\" value=\"$btn2url\"></th>
			</tr>

			<tr> 
				<th>3</th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn3text\" name=\"btn3text\" placeholder=\"Text\" value=\"$btn3text\"></th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn3url\" name=\"btn3url\" placeholder=\"URL\" value=\"$btn3url\"></th>
			</tr>

			<tr> 
				<th>4</th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn4text\" name=\"btn4text\" placeholder=\"Text\" value=\"$btn4text\"></th>
				<th><input type=\"text\" class=\"form-control\" id=\"btn4url\" name=\"btn4url\" placeholder=\"URL\" value=\"$btn4url\"></th>
			</tr>";


	echo "
	</tbody>
	</table>
	<button type=\"submit\" class=\"btn btn-primary\"> Update homepage links </button>
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
	

	#audio preview format
	#$audiopreview_format=query_one("SELECT Value from PumilioSettings WHERE Settings='audiopreview_format'", $connection);
	if (!isset($audiopreview_format)){
		$audiopreview_format = "mp3";
		}

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
	<label for=\"use_xml\">Allow access using XML</label>";

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
?>
