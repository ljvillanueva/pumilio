<?php
/*
Execute an R script to all the files in a sample set
*/

#Test if the libraries are installed
exec($Rscript . ' plugins/execute_R/test.R', $lastline, $R_retval);

$lastline0=$lastline[1];

	if ($R_retval=="91") {
		echo "<p class=\"error\">The R package 'tuneR' was not found. This plug-in can not be used.</p>";
		}
	elseif ($R_retval=="92") {
		echo "<p class=\"error\">The R package 'seewave' was not found. This plug-in can not be used.</p>";
		}
	elseif ($R_retval=="93") {
		echo "<p class=\"notice\">R and the required packages are set up correctly. However, RMySQL is not installed.</p>";
		$R_status=1;
		}
	elseif ($R_retval=="0")	{
		echo "<p class=\"success\">R and the required packages are set up correctly.</p>";
		$R_status=1;
		}
	else {
		echo "<p class=\"error\">R is not installed or there was an unknown error. This plug-in can not be used.<br>
		<i>$lastline0</i></p>";
		}
		

if ($R_status) {
        echo "<p>NOTE: The script should be self-contained. The results must either
	be stored in a database, for example using RMySQL, or displayed as text, for example using cat().</b><br>
	<p>The plugin will call the packages 
	<i><a href=\"http://cran.r-project.org/web/packages/sound/index.html\" target=\"_blank\" title=\"sound package page in CRAN\">sound</a></i> 
	and <i><a href=\"http://cran.r-project.org/web/packages/seewave/index.html\" target=\"_blank\" title=\"seewave package page in CRAN\">seewave</a></i> 
	and then loads the sound file. The following variables are available:
		<ul>
			<li><i>SoundFile</i> - object that contains the sound file
			<li><i>FileName</i> - name of the sound file
			<li><i>Sound_ID</i> - ID of the sound file
		        <li><i>Col_ID</i> - ID of the collection of the soundfile
			<li><i>Site_ID</i> - ID of the site of the soundfile
	       </ul>";
		       
        $query_sources1 = "SELECT * from Samples ORDER BY SampleName";
	$result_sources1 = mysqli_query($connection, $query_sources1)
		or die (mysqli_error($connection));
	$nrows_sources1 = mysqli_num_rows($result_sources1);
	
	echo "<p><b>Select a sample set to run the script:</b><br>
	<form method=\"POST\" action=\"plugins/execute_R/run.php\" target=\"R\" onsubmit=\"window.open('', 'R', 'width=700,height=400,status=yes,resizable=yes,scrollbars=yes')\">";
	if ($nrows_sources1>0){
		echo "<select name=\"SampleID\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">";
		for ($s2=0;$s2<$nrows_sources1;$s2++) {
			$row_sources1 = mysqli_fetch_array($result_sources1);
			extract($row_sources1);
			//How many sounds associated with that source
			$no_sounds_samples1=query_one("SELECT COUNT(*) as no_sounds FROM SampleMembers WHERE SampleID='$SampleID'", $connection);
			if ($no_sounds_samples1>0) {
				echo "<option value=\"$SampleID\">$SampleName - Sample_ID: $SampleID - $no_sounds_samples1 sound files</option>\n";
				}
			}
		echo "</select>";
	
		echo "<p><b>Script:</b><br>
		<textarea name=\"R_script\" class=\"ui-corner-all\" style=\"font-size:12px; width: 600px; height: 400px;\">
			#This is a sample script that can be run with the plugin option
			#This script will open the file and display the sampling rate, nothing more.

			#Get sampling rate
			samplingrate<-rate(SoundFile)

			#Display sampling rate
			cat(paste(\" Sampling Rate: \", samplingrate, sep=\"\"))
		</textarea>\n";

		 if ($R_retval=="93") {
		 	echo "<p><input type=\"checkbox\" name=\"load_RMySQL\" value=\"1\" class=\"ui-corner-all\" style=\"font-size:12px\" DISABLED /> ";
		 	}
		else {
			echo "<p><input type=\"checkbox\" name=\"load_RMySQL\" value=\"1\" class=\"ui-corner-all\" style=\"font-size:12px\"  onchange=\"R_message.style.visibility = 'visible';\" id=\"change\" /> ";
			}
			
		echo "<b>Load <a href=\"http://cran.r-project.org/web/packages/RMySQL/index.html\" target=\"_blank\" title=\"RMySQL package page in CRAN\">RMySQL</a> and connect to the database</b> &nbsp;&nbsp;&nbsp;<span id=\"R_message\" style=\"visibility: hidden;\">To execute a query use: <i>result <- dbSendQuery(con, \"SELECT column from table_name\")</i></span>";
		echo "<p><input type=\"checkbox\" name=\"run_one\" value=\"1\" class=\"ui-corner-all\" style=\"font-size:12px\" /> <b>Test script on one file only</b>
		<p><input type=submit value=\" Run script \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
		</form>";
		
		echo "<p class=\"notice\">Depending on the size of the sound files, the number of files and the script, this operation may take a few minutes. Please be patient.</p>";
		}
        else {
        	echo "<p> &nbsp;&nbsp;There are no sounds in the database.<br><br>";
        	}
	}
?>
