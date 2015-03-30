<?php
### All checks passed
flush(); @ob_flush();

$success_counter=0;
for ($k=0;$k<$no_sounds;$k++) {
	$row = mysqli_fetch_array($result);
	extract($row);

	$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	$SiteID=query_one("SELECT SiteID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	$DirID=query_one("SELECT DirID FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);

	$file_format=$SoundFormat;

	#Check if there are images
	$makefigures = FALSE;
	$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
	$sound_images=query_one($query_img, $connection);
	if ($sound_images!=6) {
		$makefigures=TRUE;
		}

	$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
	if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
		$makefigures=TRUE;
		}

	$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
	if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3"))	{
		$makefigures=TRUE;
		}

	$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
	if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
		$makefigures=TRUE;
		}

	$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
	if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5")) {
		$makefigures=TRUE;
		}

	$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
	if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6")) {
		$makefigures=TRUE;
		}

	$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
	if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7")) {
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
	
	
	#Check if the file size is in the database
	if ($FileSize=="" || $FileSize==NULL) {
		$file_filesize=filesize("sounds/sounds/$ColID/$DirID/$OriginalFilename");
		$result_size = mysqli_query($connection, "UPDATE Sounds SET FileSize='$file_filesize' WHERE SoundID='$SoundID' LIMIT 1")
			or die (mysqli_error($connection));
		}
	
		
	#Check if the MD5 hash is in the database
	if ($MD5_hash=="" || $MD5_hash==NULL) {
		$file_md5hash=md5_file("sounds/sounds/$ColID/$DirID/$OriginalFilename");
		$result_md5 = mysqli_query($connection, "UPDATE Sounds Set MD5_hash='$file_md5hash' WHERE SoundID='$SoundID'")
			or die (mysqli_error($connection));
		$MD5_hash=$file_md5hash;
		}
		
	#Keep the script alive
	$kk=$k+1;

	$percent_done_display=round((($kk/$no_sounds)*100),2);
	$percent_done=round($percent_done_display);

	#Estimate time to completion
	$Time1=strtotime("now");
	$elapsed_time=$Time1-$Time0;
	$elapsed_time_display=formatTime($elapsed_time);
	$time_to_complete=formatTime(round((($elapsed_time)/$kk)*$no_sounds)-$elapsed_time);

	if (!is_odd($percent_done)) {
		echo "\n<script type=\"text/javascript\">
		document.getElementById('progress_counter').innerHTML=\"<strong> $kk of $no_sounds checked ($percent_done_display %) <br>Time elapsed: $elapsed_time_display<br>Estimated time left: $time_to_complete</strong>\";
		</script>\n";
		}
	else {
		echo "\n<script type=\"text/javascript\">
		var url='include/progressbar.php?per=$percent_done';
		document.getElementById('progress_bar').src = url;
		document.getElementById('progress_counter').innerHTML=\"<strong> $kk of $no_sounds checked ($percent_done_display %)<br>Time elapsed: $elapsed_time_display<br>Estimated time left: $time_to_complete</strong>\";
		</script>\n";
		}
	
	if ($kk==$no_sounds) {
		echo "\n<script type=\"text/javascript\">
		var url='include/progressbar.php?per=100';
		document.getElementById('progress_bar').src = url;
		document.getElementById('progress_counter').innerHTML=\"<strong>Operation completed<br>Time elapsed: $elapsed_time_display</strong>\";
		document.getElementById('please_wait').innerHTML=\"<strong>Process completed.</strong>\";
		</script>\n";
		}
	flush(); @ob_flush();
}

?>
