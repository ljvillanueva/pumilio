<?php

#Check if tablet or iOS
require_once("include/Mobile_Detect.php");
$detect = new Mobile_Detect();

if ($detect->isMobile() || $detect->isTablet()) {
	$mobile = TRUE;
	}
else {
	$mobile = FALSE;
	}

#Special wrapper
if ($special_wrapper==TRUE){
	$db_filedetails_link = "$wrapper?page=db_filedetails";
	}
else {
	$db_filedetails_link = "db_filedetails.php?";
	}

if ($pumilio_admin==TRUE && $mobile==FALSE){
	$mult_delete=TRUE;
	}
else {
	$mult_delete=FALSE;
	}

if ($mult_delete==TRUE){
	echo "<form action=\"del_multiple_files.php\" method=\"POST\" id=\"delmult\">";
	}


ob_flush(); flush();

#CHECK if multicore can happen
#	unset($out, $retval);
#	exec($absolute_dir . '/include/check_auxfiles/check.py', $out, $retval);
#	if ($retval != 0) {
#		$multicore = FALSE;
#		}
#	elseif ($retval == 0) {
#		$multicore = TRUE;
#		}

#temp fix
$multicore = FALSE;

#	if ($multicore == TRUE) {
#		$run_multicore_all = FALSE;
#		for ($c=0;$c<$nrows;$c++) {
#			$run_multicore = FALSE;
#			$run_multicore_count = 0;
#			$row_c = mysqli_fetch_array($check_result);
#			extract($row_c);
#			
#			$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
#			$SiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
#			$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);

#			#Check if there are images
#			$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
#			$sound_images=query_one($query_img, $connection);

#			if ($sox_images==FALSE){
#				if ($sound_images!=6) {
#					$run_multicore = TRUE;
#					}

#				#check if spectrogram exists
#				$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
#					$run_multicore = TRUE;
#					}

#				$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
#					$run_multicore = TRUE;
#					}

#				$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
#					$run_multicore = TRUE;
#					}

#				$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
#					$run_multicore = TRUE;
#					}

#				$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
#					$run_multicore = TRUE;
#					}

#				$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
#					$run_multicore = TRUE;
#					}
#				}
#			elseif ($sox_images==TRUE){
#				if ($sound_images!=3) {
#					$run_multicore = TRUE;
#					}

#				#check if spectrogram exists
#				$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
#					$run_multicore = TRUE;
#					}

#				$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
#					$run_multicore = TRUE;
#					}

#				$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
#				if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
#					$run_multicore = TRUE;
#					}
#				}

#			#MP3
#			$AudioPreviewFilename=query_one("SELECT AudioPreviewFilename FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
#			if (($AudioPreviewFilename=="") || (is_null($AudioPreviewFilename))) {
#				$run_multicore = TRUE;
#				}
#			if (!is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
#				$run_multicore = TRUE;
#				}
#			
#			if ($run_multicore) {
#				$result_c = mysqli_query($connection, "INSERT INTO CheckAuxfiles (`SoundID`) VALUES ('$SoundID')")
#					or die (mysqli_error($connection));
#				$run_multicore_all = TRUE;
#				$run_multicore_count++;
#				}
#			}


#		if ($run_multicore_all) {
#			$result_check = mysqli_query($connection, "SELECT SoundID FROM CheckAuxfiles")
#				or die (mysqli_error($connection));
#			$nrows_check = mysqli_num_rows($result_check);
#			for ($r=0;$r<$nrows_check;$r++) {
#				$row_check = mysqli_fetch_array($result_check);
#				extract($row_check);
#		
#				$ps_running = array();

#				$random_val=mt_rand();
#				mkdir("tmp/$random_val", 0777);
#				copy($absolute_dir . '/include/check_auxfiles/check_auxfiles_db.py', $absolute_dir . '/tmp/' . $random_val . '/check_auxfiles_db.py');
#				copy($absolute_dir . '/include/check_auxfiles/svt.py', $absolute_dir . '/tmp/' . $random_val . '/svt.py');
#		
#				$myFile = $absolute_dir . '/tmp/' . $random_val . '/configfile.py';
#				$fh = fopen($myFile, 'w') or die("can't open file");
#				$stringData = "use_section=1\ndb_hostname='$host'\ndb_database='$database'\ndb_username='$user'\ndb_password='$password'\nserver_dir='" . $absolute_dir . "/sounds/'\ncur_dir='" . $absolute_dir . "/tmp/" . $random_val . "'\n";
#				fwrite($fh, $stringData);
#				fclose($fh);

#				#exec('chmod +x ' . $absolute_dir . '/tmp/' . $random_val . '/*', $out, $retval);
#				#exec('chmod -R 777 ' . $absolute_dir . '/tmp/' . $random_val, $out, $retval);
#				$ps = run_in_background("cd $absolute_dir/tmp/$random_val; python /check_auxfiles_db.py $SoundID");

#				array_push($ps_running, $ps);

#				mysqli_query($connection, "TRUNCATE TABLE `CheckAuxfiles`")
#					or die (mysqli_error($connection));
#				}

#			$ps_running_count = count($ps_running);
#			$ps_still_running = TRUE;
#		
#			while($ps_still_running == TRUE){
#				for ($p=0;$p<$ps_running_count;$p++) {
#					$this_ps = $ps_running[$p];
#					if(is_process_running($this_ps)){
#						$ps_still_running = TRUE;
#						}
#					else {
#						$ps_still_running = FALSE;
#						}
#					}
#				}
#			mysqli_commit($connection);
#			mysqli_close($connection);
#			sleep(1);
#			$connection = @mysqli_connect($host, $user, $password, $database);
#			}
#		}

$row_break_counter=0;
for ($i=0;$i<$nrows;$i++) {
	$row = mysqli_fetch_array($result);
	extract($row);

	$row_break_counter=$row_break_counter+1;

	if ($row_break_counter==3) {
		echo "<div class=\"span-8 last\">&nbsp;<br>";
		$row_break_counter=0;
		}
	else {
		echo "<div class=\"span-8\">&nbsp;<br>";
		}

	#$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	$ColID = DB::column('SELECT ColID FROM `Sounds` WHERE SoundID = ' . $SoundID);
	#$SiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	$SiteID = DB::column('SELECT SiteID FROM `Sounds` WHERE SoundID = ' . $SoundID);
	#$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	$DirID = DB::column('SELECT DirID FROM `Sounds` WHERE SoundID = ' . $SoundID);

	if ($multicore == FALSE) {
		#Check if there are images
		$makefigures = FALSE;
#		$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
#		$sound_images=query_one($query_img, $connection);
		$sound_images = DB::column('SELECT COUNT(*) FROM `SoundsImages` WHERE SoundID = ' . $SoundID);
		
		if ($sox_images==FALSE){
			if ($sound_images!=6) {
				$makefigures=TRUE;
				}

			#check if spectrogram exists
			#$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
			$query_img2 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="waveform" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
				$makefigures=TRUE;
				}

			#$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
			$query_img3 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
				$makefigures=TRUE;
				}

			#$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
			$query_img4 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="waveform-small" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
				$makefigures=TRUE;
				}

			#$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
			$query_img5 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
				$makefigures=TRUE;
				}

			#$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
			$query_img6 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="waveform-large" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
				$makefigures=TRUE;
				}

			#$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
			$query_img7 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-large" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
				$makefigures=TRUE;
				}
			}
		elseif ($sox_images==TRUE){
			if ($sound_images!=3) {
				$makefigures=TRUE;
				}

			#check if spectrogram exists
			#$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
			$query_img3 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
				$makefigures=TRUE;
				}

			#$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
			$query_img5 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
				$makefigures=TRUE;
				}

			#$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
			$query_img7 = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-large" AND SoundID = ' . $SoundID);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
				$makefigures=TRUE;
				}
			}


		if ($makefigures==TRUE) {
			require("include/make_figs.php");
			}
		}
		
	#$small_spectrogram = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
	$small_spectrogram = DB::column('SELECT ImageFile FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);

	echo "<a href=\"$db_filedetails_link&SoundID=$SoundID\">
		<img src=\"$app_url/sounds/images/$ColID/$DirID/$small_spectrogram\" width=\"300\" height=\"150\"><br>
		$SoundName</a>";

	if ($mult_delete==TRUE){
		echo "<input type=\"checkbox\" name=SoundIDs[] value=\"$SoundID\" class=\"case\">";
		}
		
	if (isset($Date_h) && $Date_h!="") {
		echo "<br>$Date_h | $Time";
		}
	
if (!isset($show_tags)){
	$show_tags = 0;
	}

if ($show_tags=="1") {
	#Tags
	if ($pumilio_loggedin == TRUE) {
		echo "<div id=\"tagspace$i\">
		<form method=\"get\" action=\"include/addtag_ajax.php\" id=\"addtags$i\">";
			$where_to = $_SERVER['PHP_SELF'];
			$where_toq = $_SERVER['QUERY_STRING'];

			require("include/managetags.php");

			echo "<p>Add tags:
			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
			<input type=\"hidden\" name=\"this_i\" value=\"$i\">
			<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\">
			<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\">
		</form><br>
		</div>\n\n";
		}
	else {
		require("include/gettags.php");
		}
	}

	echo "</div>";				
	flush(); @ob_flush();
	}
	
if ($mult_delete==TRUE){
	$self=$_SERVER['PHP_SELF'];
	$q=$_SERVER['QUERY_STRING'];
	echo "<div class=\"span-24 last\"><hr noshade></div>
		<div class=\"span-8\">&nbsp;</div>
		<div class=\"span-8\">
			<input type=\"checkbox\" id=\"selectall\"/>Select all 
			<input name=\"where_to\" type=\"hidden\" value=\"$self?$q\">
			<input type=submit value=\" Delete selected files \" class=\"fg-button ui-state-default ui-corner-all\">
		</form></div>
	<div class=\"span-8 last\">&nbsp;</div>";
	}

?>
