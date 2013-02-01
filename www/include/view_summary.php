<?php

$username = $_COOKIE["username"];
if (is_user_admin($username, $connection)) {
	$admin=TRUE;
	}
	
#Special wrapper
if ($special_wrapper==TRUE){
	$db_filedetails_link = "$wrapper?page=db_filedetails";
	}
else {
	$db_filedetails_link = "db_filedetails.php?";
	}
	
#$nrows=2;

#Check if tablet or iOS
require_once("include/Mobile_Detect.php");
$detect = new Mobile_Detect();

if ($detect->isMobile()) {
	$mobile = TRUE;
	}
else {
	$mobile = FALSE;
	}


ob_flush(); flush();

#CHECK if multicore can happen
	unset($out, $retval);
	exec($absolute_dir . '/include/check_auxfiles/check.py', $out, $retval);
	if ($retval != 0) {
		$multicore = FALSE;
		}
	elseif ($retval == 0) {
		$multicore = TRUE;
		}

	if ($multicore) {
		$run_multicore_all = FALSE;
		for ($c=0;$c<$nrows;$c++) {
			$run_multicore = FALSE;
			$run_multicore_count = 0;
			$row_c = mysqli_fetch_array($check_result);
			extract($row_c);
			
			$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			$SiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);

			#Check if there are images
			$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
			$sound_images=query_one($query_img, $connection);
			if ($sound_images!=6) {
				$run_multicore = TRUE;
				}

			#check if spectrogram exists
			$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
				$run_multicore = TRUE;
				}

			$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
				$run_multicore = TRUE;
				}

			$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
				$run_multicore = TRUE;
				}

			$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
				$run_multicore = TRUE;
				}

			$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
				$run_multicore = TRUE;
				}

			$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
				$run_multicore = TRUE;
				}

			#MP3
			$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
			if (($AudioPreviewFilename=="") || (is_null($AudioPreviewFilename))) {
				$run_multicore = TRUE;
				}
			if (!is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
				$run_multicore = TRUE;
				}
			
			if ($run_multicore) {
				$result_c = mysqli_query($connection, "INSERT INTO CheckAuxfiles (`SoundID`) VALUES ('$SoundID')")
					or die (mysqli_error($connection));
				$run_multicore_all = TRUE;
				$run_multicore_count++;
				}
			}



		if ($run_multicore_all) {
			$result_check = mysqli_query($connection, "SELECT SoundID FROM CheckAuxfiles")
				or die (mysqli_error($connection));
			$nrows_check = mysqli_num_rows($result_check);
			for ($r=0;$r<$nrows_check;$r++) {
				$row_check = mysqli_fetch_array($result_check);
				extract($row_check);
		
				$ps_running = array();

				$random_val=mt_rand();
				mkdir("tmp/$random_val", 0777);
				copy($absolute_dir . '/include/check_auxfiles/check_auxfiles_db.py', $absolute_dir . '/tmp/' . $random_val . '/check_auxfiles_db.py');
				copy($absolute_dir . '/include/check_auxfiles/svt.py', $absolute_dir . '/tmp/' . $random_val . '/svt.py');
		
				$myFile = $absolute_dir . '/tmp/' . $random_val . '/configfile.py';
				$fh = fopen($myFile, 'w') or die("can't open file");
				$stringData = "use_section=1\ndb_hostname='$host'\ndb_database='$database'\ndb_username='$user'\ndb_password='$password'\nserver_dir='" . $absolute_dir . "/sounds/'\ncur_dir='" . $absolute_dir . "/tmp/" . $random_val . "'\n";
				fwrite($fh, $stringData);
				fclose($fh);

				exec('chmod +x ' . $absolute_dir . '/tmp/' . $random_val . '/*', $out, $retval);
				exec('chmod -R 777 ' . $absolute_dir . '/tmp/' . $random_val, $out, $retval);

				$ps = run_in_background("cd $absolute_dir/tmp/$random_val; ./check_auxfiles_db.py $SoundID");

				array_push($ps_running, $ps);

				mysqli_query($connection, "TRUNCATE TABLE `CheckAuxfiles`")
					or die (mysqli_error($connection));
				}

			$ps_running_count = count($ps_running);
			$ps_still_running = TRUE;
		
			while($ps_still_running == TRUE){
				for ($p=0;$p<$ps_running_count;$p++) {
					$this_ps = $ps_running[$p];
					if(is_process_running($this_ps)){
						$ps_still_running = TRUE;
						}
					else {
						$ps_still_running = FALSE;
						}
					}
				}
			mysqli_commit($connection);
			mysqli_close($connection);
			sleep(1);
			$connection = @mysqli_connect($host, $user, $password, $database);
			}
		}
		
if ($mobile==TRUE) {
	#HTML5 player
	# http://www.jplayer.org
	echo "\n<link href=\"$app_url/html5player/jplayer.css\" rel=\"stylesheet\" type=\"text/css\" />
	<script type=\"text/javascript\" src=\"$app_url/js/jquery.jplayer.min.js\"></script>\n";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);

		echo "<div class=\"span-24 last\">\n";

		if ($i>0) {
			echo "<hr noshade>\n";
			}


		echo "<strong><a href=\"$db_filedetails_link&SoundID=$SoundID\">$SoundName</a></strong>\n";
		$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		$SiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);

		#Check if there are images
		if ($multicore == FALSE) {
			$makefigures=FALSE;
			flush();
			$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
			$sound_images=query_one($query_img, $connection);
			if ($sound_images!=6) {
				$makefigures=TRUE;
				}

			#check if spectrogram exists
			$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
				$makefigures=TRUE;
				}

			$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
				$makefigures=TRUE;
				}

			$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
				$makefigures=TRUE;
				}

			$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
				$makefigures=TRUE;
				}

			$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
				$makefigures=TRUE;
				}

			$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
				$makefigures=TRUE;
				}

			if ($makefigures==TRUE) {
				require("include/make_figs.php");
				}
			#MP3
			if (($AudioPreviewFilename=="") || (is_null($AudioPreviewFilename))) {
				#File does not exists, create
				$AudioPreviewFilename=dbfile_mp3($OriginalFilename,$SoundFormat,$ColID,$DirID,$SamplingRate);
				$query_mp3 = "UPDATE Sounds SET AudioPreviewFilename='$AudioPreviewFilename' WHERE SoundID='$SoundID'";
				$result_mp3 = mysqli_query($connection, $query_mp3)
					or die (mysqli_error($connection));
				}
			if (!is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
				#File does not exists, create

				#Check if dir exists
				if (!is_dir("sounds/previewsounds/$ColID")) {
					mkdir("sounds/previewsounds/$ColID", 0777);
					}
				if (!is_dir("sounds/previewsounds/$ColID/$DirID")) {
					mkdir("sounds/previewsounds/$ColID/$DirID", 0777);
					}

				$AudioPreviewFilename=dbfile_mp3($OriginalFilename,$SoundFormat,$ColID,$DirID,$SamplingRate);
				$query_mp3 = "UPDATE Sounds SET AudioPreviewFilename='$AudioPreviewFilename' WHERE SoundID='$SoundID'";
				$result_mp3 = mysqli_query($connection, $query_mp3)
					or die (mysqli_error($connection));
				}
			}
			
		$iplayer = $i + 1;

		mysqli_commit($connection);

		$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		$sound_spectrogram=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram' LIMIT 1", $connection);
		
		echo "\n<script type=\"text/javascript\">
		//<![CDATA[
		$(document).ready(function(){

		$(\"#jquery_jplayer_$iplayer\").jPlayer({
			ready: function (event) {
				$(this).jPlayer(\"setMedia\", {
					$AudioPreviewFormat: \"$app_url/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\"
				})
				},
				volume: \"0.9\",
				solution: \"flash, html\",
				swfPath: \"$app_url/js\",
				supplied: \"$AudioPreviewFormat\",
				cssSelectorAncestor: \"#jp_interface_$iplayer\"
				})
			.bind($.jPlayer.event.play, function() { // Using a jPlayer event to avoid both jPlayers playing together.
				$(this).jPlayer(\"pauseOthers\");
			});
			});
		//]]>
		</script>
		";


		#HTML5 player
		echo "<div id=\"jquery_jplayer_$iplayer\" class=\"jp-jplayer\"></div>\n";

		echo "	<div style=\"height: 460px; width: 920px; position: relative;\">
			<img src=\"$app_url/sounds/images/$ColID/$DirID/$sound_spectrogram\" width=\"920\" height=\"460\">";

		echo "	\n</div>
			<div class=\"jp-audio\">
				<div class=\"jp-type-single\">
					<div id=\"jp_interface_$iplayer\" class=\"jp-interface\">
						<div class=\"jp-progress\">
							<div class=\"jp-seek-bar\">
								<div class=\"jp-play-bar\"></div>
							</div>
						</div>
						<ul class=\"jp-controls\">
							<li><a href=\"javascript:;\" class=\"jp-play\" tabindex=\"1\">play</a></li>
							<li><a href=\"javascript:;\" class=\"jp-pause\" tabindex=\"1\">pause</a></li>
						</ul>
						<div class=\"jp-volume-bar\">
							<div class=\"jp-volume-bar-value\" title=\"volume\"></div>
						</div>
						<div class=\"jp-current-time\"></div>
						<div class=\"jp-duration\"></div>
					</div>

				</div>
			</div>\n";


		#Check if there are images of the site
		$site_pics=query_one("SELECT COUNT(*) FROM SitesPhotos WHERE SiteID='$SiteID'", $connection);
		if ($site_pics>0) {
			echo " <a href=\"#\" title=\"Show photographs of this site\" onclick=\"window.open('sitephotos.php?SiteID=$SiteID', 'pics', 'width=550,height=400,status=yes,resizable=yes,scrollbars=yes'); return false;\">
				<img src=\"images/image.png\" alt=\"Show photographs of this site\"></a>";
			}

		if (isset($Date_h) && $Date_h!="") {
			echo "Date: $Date_h | Time: $Time";
			}
		if (isset($Notes) && $Notes!="") {
			echo "<br>Notes: $Notes";
			}

		#Find weather data
		$weather_data_id=get_closest_weather($connection,$SiteLat, $SiteLon,$Date,$Time);
		$weather_data=explode(",",$weather_data_id);
		$weather_data_id=$weather_data[0];
		$time_diff=round(($weather_data[1]/60));
		$distance=round($weather_data[2],2);
		if ($weather_data_id!=0 && $time_diff<60) {
			$result_w = mysqli_query($connection, "SELECT * FROM WeatherData WHERE WeatherDataID='$weather_data_id' LIMIT 1")
				or die (mysqli_error($connection));
			$row_w = mysqli_fetch_array($result_w);
			extract($row_w);

			echo "<p>Weather data: \n <ul>";
			if (isset($Temperature) && $Temperature!=NULL)
				echo "<li>Temp: $Temperature &deg;C";
			if (isset($Precipitation) && $Precipitation!=NULL)
				echo "<li>Precipitation: $Precipitation mm";
			if (isset($RelativeHumidity) && $RelativeHumidity!=NULL)
				echo "<li>Relative Humidity: $RelativeHumidity %";
			if (isset($WindSpeed) && $WindSpeed!=NULL)
				echo "<li>WindSpeed: $WindSpeed m/s";
			echo "</ul>";
			}


		#Other data associated with this file
		$dir="data_sources/";
		$other_data=scandir($dir);
		if (count($other_data)>0) {
			for ($o=0;$o<count($other_data);$o++) {
				if (strpos(strtolower($other_data[$o]), ".php")) {
					require("$dir/$other_data[$o]");
					}
				}
			}

		if (!isset($notags)){
			$notags="0";
			}

		#Quality flags
		$QualityFlag=query_one("SELECT QualityFlag from QualityFlags WHERE QualityFlagID='$QualityFlagID'", $connection);
		echo "<p>File Quality flag: <em title=\"$QualityFlag\">$QualityFlagID</em>";
		if ($DerivedSound == "1"){
			echo "<li>Derived from: <a href=\"db_filedetails.php?SoundID=$DerivedFromSoundID\">$DerivedFromSoundID</li>";
			}

		#tags
		if (!sessionAuthenticate($connection) || $notags=="1") {
			require("include/gettags.php");
			}
		else {
			echo "<br>";

			#Tags
			if (sessionAuthenticate($connection)) {			
				echo "<div id=\"tagspace$i\">\n";

				echo "	<form method=\"get\" action=\"include/addtag_ajax.php\" id=\"addtags$i\">";
				$where_to=$_SERVER['PHP_SELF'];
				$where_toq=$_SERVER['QUERY_STRING'];

				require("include/managetags.php");

				echo "<p>Add tags:<br>
					<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
					<input type=\"hidden\" name=\"this_i\" value=\"$i\">
					<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
					<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\">
					<em>Separate tags with a space</em></form><br></div>";
				}
			else {
				require("include/gettags.php");
				}

			#add sound to a sample set
			$query_sample = "SELECT SampleID, SampleName from Samples ORDER BY SampleName";
			$result_sample = mysqli_query($connection, $query_sample)
				or die (mysqli_error($connection));
			$nrows_sample = mysqli_num_rows($result_sample);

			if ($nrows_sample!=0) {
				echo "<p><form method=\"GET\" action=\"add_to_sample.php\" target=\"add\" onsubmit=\"window.open('', 'add', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
				Add this sound to a sample set:<br>
				<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">";
				echo "<select name=\"SampleID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:10px\">";

				for ($sa=0;$sa<$nrows_sample;$sa++) {
					$row_sample = mysqli_fetch_array($result_sample);
					extract($row_sample);
					echo "<option value=\"$SampleID\">$SampleName</option>\n";
					}

				echo "</select>
				<input type=submit value=\" Add \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\"></form>";
				}
			}

		echo "&nbsp;\n</div>\n";

		flush(); @ob_flush();
		}
	}
else {
	#NON MOBILE VERSION
	#HTML5 player
	# http://www.jplayer.org
	echo "\n<link href=\"$app_url/html5player/jplayer_summary.css\" rel=\"stylesheet\" type=\"text/css\" />
	<script type=\"text/javascript\" src=\"$app_url/js/jquery.jplayer.min.js\"></script>\n";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);

		#Get site data
			$query_site = "SELECT * from Sites WHERE SiteID='$SiteID' LIMIT 1";
			$result_site = mysqli_query($connection, $query_site)
				or die (mysqli_error($connection));
			$nrows_site = mysqli_num_rows($result_site);

			if ($nrows_site > 0) {
				$row_site = mysqli_fetch_array($result_site);
				extract($row_site);
				}

		echo "<div class=\"span-8 summary-left\">";

		echo "<strong><a href=\"$db_filedetails_link&SoundID=$SoundID\">$SoundName</a></strong>";
		$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		$SiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);

		#Check if there are images of the site
		$site_pics=query_one("SELECT COUNT(*) FROM SitesPhotos WHERE SiteID='$SiteID'", $connection);
		if ($site_pics>0) {
			echo " <a href=\"#\" title=\"Show photographs of this site\" onclick=\"window.open('sitephotos.php?SiteID=$SiteID', 'pics', 'width=550,height=400,status=yes,resizable=yes,scrollbars=yes'); return false;\"><img src=\"images/image.png\" alt=\"Show photographs of this site\"></a>";
			}

		if (isset($Date) && $Date!="")
			echo "<br>Date: $Date_h | Time: $Time";
		if (isset($Notes) && $Notes!="")
			echo "<br>Notes: $Notes";

		#Find weather data
		$weather_data_id = get_closest_weather($connection, $SiteLat, $SiteLon, $Date, $Time);
		$weather_data = explode(",", $weather_data_id);
		$weather_data_id = $weather_data[0];
		$time_diff = round(($weather_data[1]/60));
		$distance = round($weather_data[2], 2);
		if ($weather_data_id!=0 && $time_diff<60) {
			$result_w = mysqli_query($connection, "SELECT * FROM WeatherData WHERE WeatherDataID='$weather_data_id' LIMIT 1")
				or die (mysqli_error($connection));
			$row_w = mysqli_fetch_array($result_w);
			extract($row_w);

			echo "<p>Weather data: \n <ul>";
			if (isset($Temperature) && $Temperature!=NULL)
				echo "<li>Temp: $Temperature &deg;C";
			if (isset($Precipitation) && $Precipitation!=NULL)
				echo "<li>Precipitation: $Precipitation mm";
			if (isset($RelativeHumidity) && $RelativeHumidity!=NULL)
				echo "<li>Relative Humidity: $RelativeHumidity %";
			if (isset($WindSpeed) && $WindSpeed!=NULL)
				echo "<li>WindSpeed: $WindSpeed m/s";
		
			echo "</ul>";
			}
	

		#Other data associated with this file
		$dir="data_sources/";
		 	$other_data=scandir($dir);
			if (count($other_data)>0) {
		 		for ($o=0;$o<count($other_data);$o++) {
		 			if (strpos(strtolower($other_data[$o]), ".php")) {
						require("$dir/$other_data[$o]");
		 				}
		 			}
		 		}

		#Quality flags
		if (sessionAuthenticate($connection)) {
			$QualityFlag=query_one("SELECT QualityFlag from QualityFlags WHERE QualityFlagID='$QualityFlagID'", $connection);

			echo "<p>File Quality flag: <em title=\"$QualityFlag\">$QualityFlagID</em>";
			if ($DerivedSound == "1"){
				echo "<br>&nbsp;&nbsp;&nbsp; - Derived from: <a href=\"db_filedetails.php?SoundID=$DerivedFromSoundID\">$DerivedFromSoundID";
				}

			echo "<div style=\"margin-left: 10px;\"><form method=\"GET\" action=\"editqf.php\" target=\"editqf\" onsubmit=\"window.open('', 'editqf', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
				Edit the Quality Flag for this file:<br>
				<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">";

			$thisfile_QualityFlagID = $QualityFlagID;

			$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
			$result_qf = mysqli_query($connection, $query_qf)
				or die (mysqli_error($connection));
			$nrows_qf = mysqli_num_rows($result_qf);

			echo "<select name=\"newqf\" class=\"ui-state-default ui-corner-all\" style=\"font-size:10px\">";

			for ($f=0;$f<$nrows_qf;$f++) {
				$row_qf = mysqli_fetch_array($result_qf);
				extract($row_qf);

				if ($QualityFlagID==$thisfile_QualityFlagID){
					echo "<option value=\"$QualityFlagID\" SELECTED>$QualityFlagID: $QualityFlag</option>\n";
					}
				else{
					echo "<option value=\"$QualityFlagID\">$QualityFlagID: $QualityFlag</option>\n";
					}
				}

			echo "</select><br>
			<input type=submit value=\" Change \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
			</form></div>";
			}
		else {
			if ($DerivedSound == "1"){
				echo "<br>&nbsp;&nbsp;&nbsp; - Derived from: <a href=\"db_filedetails.php?SoundID=$DerivedFromSoundID\">$DerivedFromSoundID";
				}
			}



		#tags
		if (!sessionAuthenticate($connection)) {
			require("include/gettags.php");
			}
		else {
			echo "<br>";

			#Tags
			$use_tags=query_one("SELECT Value from PumilioSettings WHERE Settings='use_tags'", $connection);
			if ($use_tags=="1" || $use_tags=="") {
				if (sessionAuthenticate($connection)) {							

				$where_to=$_SERVER['PHP_SELF'];
				$where_toq=$_SERVER['QUERY_STRING'];

				echo "<div id=\"tagspace$i\">
					<form method=\"get\" action=\"include/addtag_ajax.php\" id=\"addtags$i\">";

				require("include/managetags.php");
				$where_to=$_SERVER['PHP_SELF'];
				$where_toq=$_SERVER['QUERY_STRING'];
				echo "<p>Add tags:<br>
					<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
					<input type=\"hidden\" name=\"this_i\" value=\"$i\">
					<input type=\"hidden\" name=\"where_to\" value=\"$where_to\">
					<input type=\"hidden\" name=\"where_toq\" value=\"$where_toq\">
	
					<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
					<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\">
					<em>Separate tags with a space</em></form><br></div>";
					}
				else {
					require("include/gettags.php");
					}
				}

			#add sound to a sample set
			$query_sample = "SELECT SampleID, SampleName from Samples ORDER BY SampleName";
			$result_sample = mysqli_query($connection, $query_sample)
				or die (mysqli_error($connection));
			$nrows_sample = mysqli_num_rows($result_sample);

			if ($nrows_sample!=0) {
				echo "<p><form method=\"GET\" action=\"add_to_sample.php\" target=\"add\" onsubmit=\"window.open('', 'add', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
				Add this sound to a sample set:<br>
				<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">";
				echo "<select name=\"SampleID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:10px\">";

				for ($sa=0;$sa<$nrows_sample;$sa++) {
					$row_sample = mysqli_fetch_array($result_sample);
					extract($row_sample);
					echo "<option value=\"$SampleID\">$SampleName</option>\n";
					}

				echo "</select>
				<input type=submit value=\" Add \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\"></form>";
				}
			}

		#Check if there are images
		flush();
		
		if ($multicore == FALSE) {
			$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
			$sound_images=query_one($query_img, $connection);

			$makefigures=FALSE;
			if ($sound_images!=6) {
				$makefigures==TRUE;
				}

			#check if spectrogram exists
			$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
					$makefigures==TRUE;
					}
	
				$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
					$makefigures==TRUE;
					}
	
				$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
					$makefigures==TRUE;
					}
	
				$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
					$makefigures==TRUE;
					}
	
				$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
					$makefigures==TRUE;
					}
	
				$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
					$makefigures==TRUE;
					}


			if ($makefigures==TRUE) {
				require("include/make_figs.php");
				}

			#MP3
			if (($AudioPreviewFilename=="") || (is_null($AudioPreviewFilename))) {
				#File does not exists, create
				$AudioPreviewFilename=dbfile_mp3($OriginalFilename,$SoundFormat,$ColID,$DirID,$SamplingRate);
				$query_mp3 = "UPDATE Sounds SET AudioPreviewFilename='$AudioPreviewFilename' WHERE SoundID='$SoundID'";
				$result_mp3 = mysqli_query($connection, $query_mp3)
					or die (mysqli_error($connection));
				}
			if (!is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
				#File does not exists, create
	
				#Check if dir exists
				if (!is_dir("sounds/previewsounds/$ColID")) {
					mkdir("sounds/previewsounds/$ColID", 0777);
					}
				if (!is_dir("sounds/previewsounds/$ColID/$DirID")) {
					mkdir("sounds/previewsounds/$ColID/$DirID", 0777);
					}
		
				$AudioPreviewFilename=dbfile_mp3($OriginalFilename,$SoundFormat,$ColID,$DirID,$SamplingRate);
				$query_mp3 = "UPDATE Sounds SET AudioPreviewFilename='$AudioPreviewFilename' WHERE SoundID='$SoundID'";
				$result_mp3 = mysqli_query($connection, $query_mp3)
					or die (mysqli_error($connection));
				}
			}
	
			echo "</div>
				<div class=\"span-16 last summary-right\">\n";

		$iplayer = $i + 1;

		mysqli_commit($connection);

		$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
		
		
		$sound_spectrogram=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram'", $connection);
		if ($sound_spectrogram==""){
			mysqli_commit($connection);
			$sound_spectrogram=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram'", $connection);
			}

		echo "\n<script type=\"text/javascript\">
		//<![CDATA[
		$(document).ready(function(){

			$(\"#jquery_jplayer_$iplayer\").jPlayer({
				ready: function () {
					$(this).jPlayer(\"setMedia\", {
						$AudioPreviewFormat: \"$app_url/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\"
					})
				},
				volume: \"0.9\",
				swfPath: \"$app_url/js\",
				supplied: \"$AudioPreviewFormat\",
				solution: \"html, flash\",
				preload: \"none\",
				cssSelectorAncestor: \"#jp_interface_$iplayer\"
			})
			.bind($.jPlayer.event.play, function() { // Using a jPlayer event to avoid both jPlayers playing together.
					$(this).jPlayer(\"pauseOthers\");
			});
			});
		//]]>
		</script>
		";

		#HTML5 player
		echo "<div id=\"jquery_jplayer_$iplayer\" class=\"jp-jplayer\"></div>\n";

		echo "	<div style=\"height: 300px; width: 600px; position: relative;\">
			<img src=\"$app_url/sounds/images/$ColID/$DirID/$sound_spectrogram\">";

		echo "	\n</div>
			<div class=\"jp-audio\">
				<div class=\"jp-type-single\">
					<div id=\"jp_interface_$iplayer\" class=\"jp-interface\">
						<div class=\"jp-progress\">
							<div class=\"jp-seek-bar\">
								<div class=\"jp-play-bar\"></div>
							</div>
						</div>
						<ul class=\"jp-controls\">
							<li><a href=\"#\" class=\"jp-play\" tabindex=\"1\">play</a></li>
							<li><a href=\"#\" class=\"jp-pause\" tabindex=\"1\">pause</a></li>
						</ul>
						<div class=\"jp-volume-bar\">
							<div class=\"jp-volume-bar-value\" title=\"volume\"></div>
						</div>
						<div class=\"jp-current-time\"></div>
						<div class=\"jp-duration\"></div>
					</div>

				</div>
			</div>\n";

		echo "&nbsp;\n</div>\n";

		flush(); @ob_flush();
	}
}
?>
