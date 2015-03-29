<?php
### All checks passed
$wav_toflac=query_one("SELECT Value from PumilioSettings WHERE Settings='wav_toflac'", $connection);
#$wav_toflac=FALSE;

if ($sm==1) {
	$handle = opendir($dir);
	$cc=1;
	while (false !== ($file = readdir($handle)) && $cc==1) {
	        if ($file != "." && $file != "..") {
	            $afile=$file;
			$cc=2;
	        }
	    }
	closedir($handle);
	
	$bfile=explode(".",$afile);
	$ext_offset=strlen($bfile[1]);
	#WA format: YYYMMDD_HHMMSS
	}
else {
	$codedyear1=explode(":",$codedyear);
	$codedmonth1=explode(":",$codedmonth);
	$codedday1=explode(":",$codedday);
	$codedhour1=explode(":",$codedhour);
	$codedminutes1=explode(":",$codedminutes);
	$codedseconds1=explode(":",$codedseconds);
	}

$success_counter=0;
for ($k=0;$k<$files_to_process_counter;$k++) {
	$this_file=$files_to_process[$k];

	#Compress wav to flac
	if ($wav_toflac == "1") {
		$this_file_format = substr($this_file, -4);
		$this_file_format = strtolower($this_file_format);
		if ($this_file_format==".wav"){
			$this_file_exp = explode(".", $this_file);	
			$this_file_flac = $this_file_exp[0] . ".flac";
			exec("flac -Vf " . $dir . "/" . $this_file . " -o " . $dir . "/" . $this_file_flac, $lastline, $retval);
			if ($retval!=0) {
				echo "<div class=\"error\"><img src=\"images/exclamation.png\"> The file " . $dir . $this_file . " could not be compressed to flac format.</div>";
				die();
				}
			else {
				#all went ok
				unlink($dir . $this_file);
				unset($this_file);
				$this_file = $this_file_flac;
				$ext_offset = 4;
				$flac_file_done = TRUE;
				}
			}
		}


	if ($sm==1) {
			$yearcoded = substr($this_file, -16 - $ext_offset, 4);
			$monthcoded = substr($this_file, -12 - $ext_offset, 2);
			$daycoded = substr($this_file, -10 - $ext_offset, 2);
			$hourcoded = substr($this_file, -7 - $ext_offset, 2);
			$minutescoded = substr($this_file, -5 - $ext_offset, 2);
			$secondscoded = substr($this_file, -3 - $ext_offset, 2);
		}
	else {
			$yearcoded = substr($this_file, $codedyear1[0]-1, $codedyear1[1]-$codedyear1[0]+1);
			$monthcoded = substr($this_file, $codedmonth1[0]-1, $codedmonth1[1]-$codedmonth1[0]+1);
			$daycoded = substr($this_file, $codedday1[0]-1, $codedday1[1]-$codedday1[0]+1);
			$hourcoded = substr($this_file, $codedhour1[0]-1, $codedhour1[1]-$codedhour1[0]+1);
			$minutescoded = substr($this_file, $codedminutes1[0]-1, $codedminutes1[1]-$codedminutes1[0]+1);
			$secondscoded = substr($this_file, $codedseconds1[0]-1, $codedseconds1[1]-$codedseconds1[0]+1);
		}
					
	$datecoded=$yearcoded . "-" . $monthcoded . "-" . $daycoded;
	$timecoded=$hourcoded . ":" . $minutescoded . ":" . $secondscoded;

	$DirID = rand(1,100);

	#Insert to MySQL
	if ($wav_toflac == "1" && $flac_file_done == TRUE) {
		$query_to_insert="INSERT INTO Sounds (SoundName, OriginalFilename, Date, Time, SiteID, ColID, DirID, SensorID)
				VALUES ('$this_file_flac', '$this_file', '$datecoded', '$timecoded', '$SiteID', '$ColID', '$DirID', '$SensorID');";
		}
	else {
		$query_to_insert="INSERT INTO Sounds (SoundName, OriginalFilename, Date, Time, SiteID, ColID, DirID, SensorID)
				VALUES ('$this_file', '$this_file', '$datecoded', '$timecoded', '$SiteID', '$ColID', '$DirID', '$SensorID');";
		}

	$result = mysqli_query($connection, $query_to_insert)
		or die (mysqli_error($connection));

	$SoundID=mysqli_insert_id($connection);
	
	if ($wav_toflac == "1" && $flac_file_done == TRUE) {
		exec('python include/soundcheck.py ' . $dir . "/" . $this_file_flac, $lastline_file, $retval);
		}
	else {
		exec('python include/soundcheck.py ' . $dir . "/" . $this_file, $lastline_file, $retval);
		}
		
		if ($retval==0) {
			$file_info=$lastline_file[0];
			$file_info=explode(",",$file_info);
			$sampling_rate=$file_info[0];
			$no_channels=$file_info[1];
			$file_format=$file_info[2];
			$file_duration=$file_info[3];
			$file_bits=$file_info[4];

			$query_file = "UPDATE Sounds SET SamplingRate='$sampling_rate', Channels='$no_channels', 
					Duration='$file_duration', SoundFormat='$file_format', BitRate='$file_bits'
					WHERE SoundID='$SoundID' LIMIT 1";
			$result_file = mysqli_query($connection, $query_file)
				or die (mysqli_error($connection));
			unset($lastline_file);
			unset($query_file);
			unset($retval);
			unset($file_info);
			}
		else {
			#Failed to copy, delete.
			$resultdel = mysqli_query($connection, "DELETE FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
				or die(mysqli_error($connection));
			}

	#Copy file
	$source_dir="sounds/sounds/$ColID";
	if (!is_dir($source_dir))
		{mkdir($source_dir, 0777);}
	$source_dir="sounds/sounds/$ColID/$DirID";
	if (!is_dir($source_dir))
		{mkdir($source_dir, 0777);}

	if ($wav_toflac == "1" && $flac_file_done == TRUE) {
		if (copy($dir . "/" . $this_file_flac, $source_dir . "/" . $this_file_flac)) {
			$file_filesize=filesize("sounds/sounds/$ColID/$DirID/$this_file_flac");
			$file_md5hash=md5_file("sounds/sounds/$ColID/$DirID/$this_file_flac");
		
			$result_size = mysqli_query($connection, "UPDATE Sounds SET FileSize='$file_filesize',MD5_hash='$file_md5hash' WHERE SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$success_counter+=1;
			}
		else {
			#Failed to copy, delete.
			$resultdel = mysqli_query($connection, "DELETE FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			}

		}
	else {
		if (copy($dir . "/" . $this_file,$source_dir . "/" . $this_file)) {
			$file_filesize=filesize("sounds/sounds/$ColID/$DirID/$this_file");
			$file_md5hash=md5_file("sounds/sounds/$ColID/$DirID/$this_file");
		
			$result_size = mysqli_query($connection, "UPDATE Sounds SET FileSize='$file_filesize',MD5_hash='$file_md5hash' WHERE SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			$success_counter+=1;
			}
		else {
			#Failed to copy, delete.
			$resultdel = mysqli_query($connection, "DELETE FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
				or die (mysqli_error($connection));
			}
		}					
		
	if ($wav_toflac=="1" && $files_format == "wav") {
		unlink("tmp/" . $this_file_flac);
		}
		
	#Keep the script alive
	$kk=$k+1;
	#echo "<br>Imported the file $this_file successfully ($kk/$commadata_count).";
	$percent_done_display=round((($kk/$files_to_process_counter)*100),2);
	$percent_done=round($percent_done_display);

	#Estimate time to completion
	$Time1=strtotime("now");
	$elapsed_time=$Time1-$Time0;
	$elapsed_time_display=formatTime($elapsed_time);
	$time_to_complete=formatTime(round((($elapsed_time)/$kk)*$files_to_process_counter)-$elapsed_time);
					
		echo "\n<script type=\"text/javascript\">
		var url='include/progressbar.php?per=$percent_done';
		document.getElementById('progress_bar').src = url;
		document.getElementById('progress_counter').innerHTML=\"<strong>$kk of $files_to_process_counter files imported ($percent_done_display %) <br>Time elapsed: $elapsed_time_display<br>Estimated time left: $time_to_complete</strong>\";
		</script>\n";
		
	if ($kk==$files_to_process_counter) {
		echo "\n<script type=\"text/javascript\">
		var url='include/progressbar.php?per=100';
		document.getElementById('progress_bar').src = url;
		document.getElementById('progress_counter').innerHTML=\"<strong>Import operation completed<br>Time elapsed: $elapsed_time_display</strong>\";
		</script>\n";
		}
	flush(); @ob_flush();
	}
?>
