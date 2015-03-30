<?php

	
if ($SiteID==""){
	$SiteID = "NULL";
	}
else{
	$SiteID = "$SiteID";
	}

if ($SensorID==""){
	$SensorID = "NULL";
	}
else{
	$SensorID = "$SensorID";
	}


### All checks passed
$success_counter=0;
for ($k=0;$k<$commadata_count;$k++) {
	$this_row=$commadata[$k];
	$this_row=filter_var($this_row, FILTER_SANITIZE_STRING);
	
	$this_row_imploded="";
	$this_row_exploded=explode("|", $this_row);
	$this_file=$this_row_exploded[0];
	for ($t=0;$t<1;$t++) {
		$this_value=trim($this_row_exploded[$t]);
		if ($this_value=="NULL") {
			$this_row_imploded="NULL";
			}
		else {
			$this_row_imploded="'$this_value'";
			}
		}
	for ($t=1;$t<count($this_row_exploded);$t++) {
		$this_value=trim($this_row_exploded[$t]);
		if ($this_value=="NULL") {
			$this_row_imploded=$this_row_imploded . ",NULL";
			}
		else {
			$this_row_imploded=$this_row_imploded . ",'$this_value'";
			}
		}

	#Insert to MySQL
	$DirID = rand(1,100);
	
	
	$query_to_insert="INSERT INTO Sounds ($fields, ColID, DirID, SiteID, SensorID) VALUES ($this_row_imploded, '$ColID', '$DirID', '$SiteID', '$SensorID');";
	$result = mysqli_query($connection, $query_to_insert)
			or die (mysqli_error($connection));

	$SoundID=mysqli_insert_id($connection);
	$SoundName=query_one("SELECT SoundName FROM Sounds WHERE SoundID='$SoundID' LIMIT 1", $connection);
	if ($SoundName=="") {
		$result1 = mysqli_query($connection, "UPDATE Sounds SET SoundName=OriginalFilename WHERE SoundID='$SoundID' LIMIT 1")
			or die(mysqli_error($connection));
		}

	exec('python include/soundcheck.py ' . $dir . $this_file, $lastline, $retval);
	if ($retval==0) {
		$file_info=$lastline[0];
		$file_info=explode(",",$file_info);
		$sampling_rate=$file_info[0];
		$no_channels=$file_info[1];
		$file_format=$file_info[2];
		$file_duration=$file_info[3];
		$file_bits=$file_info[4];

		$query_file = "UPDATE Sounds SET 
				SamplingRate='$sampling_rate', Channels='$no_channels', 
				Duration='$file_duration',SoundFormat='$file_format', BitRate='$file_bits' 
				WHERE SoundID='$SoundID' LIMIT 1";
		$result_file = mysqli_query($connection, $query_file)
				or die (mysqli_error($connection));
		unset($lastline);
		unset($query_file);
		unset($retval);
		unset($file_info);
		}
	else {
		#Failed to copy, delete.
		$resultdel = mysqli_query($connection, "DELETE FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
			or die (mysqli_error($connection));
		}

	#Copy file
	$source_dir="sounds/sounds/$ColID";
	if (!is_dir($source_dir)) {
		mkdir($source_dir, 0777);
		}
	$source_dir="sounds/sounds/$ColID/$DirID";
	if (!is_dir($source_dir)) {
		mkdir($source_dir, 0777);
		}

	if (copy($dir . $this_file,$source_dir . "/" . $this_file)) {
		$success_counter+=1;
		}
	else {
		#Failed to copy, delete.
		$resultdel = mysqli_query($connection, "DELETE FROM Sounds WHERE SoundID='$SoundID' LIMIT 1")
			or die (mysqli_error($connection));
		}
		
	#Keep the script alive
	$kk=$k+1;
	#echo "<br>Imported the file $this_file successfully ($kk/$commadata_count).";
	$percent_done_display=round((($kk/$commadata_count)*100),2);
	$percent_done=round($percent_done_display);

	#Estimate time to completion
	$Time1=strtotime("now");
	$elapsed_time=$Time1-$Time0;
	$elapsed_time_display=formatTime($elapsed_time);
	$time_to_complete=formatTime(round((($elapsed_time)/$kk)*$commadata_count)-$elapsed_time);
					
	if (!is_odd($percent_done)) {
		echo "\n<script type=\"text/javascript\">
		document.getElementById('progress_counter').innerHTML=\"<strong>$kk of $commadata_count files imported ($percent_done_display %) <br>Time elapsed: $elapsed_time_display<br>Estimated time left: $time_to_complete</strong>\";
		</script>\n";
		}
	else {
		echo "\n<script type=\"text/javascript\">
		var url='include/progressbar.php?per=$percent_done';
		document.getElementById('progress_bar').src = url;
		document.getElementById('progress_counter').innerHTML=\"<strong>$kk of $commadata_count files imported ($percent_done_display %) <br>Time elapsed: $elapsed_time_display<br>Estimated time left: $time_to_complete</strong>\";
		</script>\n";
		}
		
	if ($kk==$commadata_count) {
		echo "\n<script type=\"text/javascript\">
		var url='include/progressbar.php?per=100';
		document.getElementById('progress_bar').src = url;
		document.getElementById('progress_counter').innerHTML=\"<strong>Import operation completed<br>Time elapsed: $elapsed_time_display</strong>\";
		</script>\n";
		}
	flush();
	}

?>
