<?php
session_start();

$Time0=strtotime("now");

require("../../include/functions.php");
require("../../config.php");
require("../../include/apply_config.php");

#Check if user is logged
	if (!sessionAuthenticate($connection))
		{die();}
		
	$username = $_COOKIE["username"];

#Get variables
$SampleID=filter_var($_POST["SampleID"], FILTER_SANITIZE_NUMBER_INT);
$run_one=filter_var($_POST["run_one"], FILTER_SANITIZE_NUMBER_INT);
$load_RMySQL=filter_var($_POST["load_RMySQL"], FILTER_SANITIZE_NUMBER_INT);

$R_script=$_POST["R_script"];


$R_script = str_ireplace("\\\"", "\"", $R_script);

#Save script as a file
$scriptfile='../../tmp/' . mt_rand() . ".R";
$fp = fopen($scriptfile, 'w');
fwrite($fp, "#Reduce output
options(echo = FALSE)
options(warn = -1)

#Libraries
library(tuneR)
library(seewave)

#Get command line arguments
args <- commandArgs(TRUE)
        #Get filename
        FileName = args[1]
        Col_ID = as.numeric(args[2])
	Site_ID = as.numeric(args[3])
        Sound_ID = as.numeric(args[4])
        SoundFile <- readWave(FileName)\n");

if ($load_RMySQL){
	fwrite($fp, "#Load RMySQL
	library(DBI)
	library(RMySQL)
	#Open MySQL connection
	drv <- dbDriver(\"MySQL\")
	con <- dbConnect(drv, user=\"$user\", password = \"$password\", host=\"$host\", dbname = \"$database\")");
	}

fwrite($fp, $R_script);

if ($load_RMySQL){
	fwrite($fp, "#Close RMySQL
	dbDisconnect(con)");
	}

fwrite($fp, "\n#exit\n
q(save = \"no\", status = 0, runLast = FALSE)\n");
fclose($fp);


echo "
<html>
<head>

<title>$app_custom_name - R Script</title>";

#Get CSS
$jquerycss = $_COOKIE["jquerycss"];

if ($jquerycss=="") {
	echo "<!-- JQuery -->
	<link type=\"text/css\" href=\"../../js/jquery/cupertino/jquery-ui-1.7.3.custom.css\" rel=\"stylesheet\" />";
	}
else {
	echo "<!-- JQuery -->
	<link type=\"text/css\" href=\"../../js/jquery/$jquerycss/jquery-ui-1.7.3.custom.css\" rel=\"stylesheet\" />";
	}

#Custom
echo "
	<link type=\"text/css\" href=\"../../js/jquery/jquery.custom.css\" rel=\"stylesheet\" />";

echo "<link type=\"text/css\" href=\"../../js/jquery/jquery.css.custom.css\" rel=\"stylesheet\" />

<!-- Blueprint -->
<link rel=\"stylesheet\" href=\"../../css/screen.css\" type=\"text/css\" media=\"screen, projection\">
<link rel=\"stylesheet\" href=\"../../css/print.css\" type=\"text/css\" media=\"print\">	
<!--[if IE]><link rel=\"stylesheet\" href=\"../../css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://fonts.googleapis.com/css?family=Ubuntu\">
";
?>

</head>
<body>

<div style="padding: 10px;">
		
<?php
echo $random_name;

$SampleName=query_one("SELECT SampleName FROM Samples WHERE SampleID='$SampleID' LIMIT 1", $connection);

if ($run_one==1){
	$run_limit=" LIMIT 1";	
	}
else{
	$run_limit=" ";
	}
	
$query = "SELECT * FROM Sounds,SampleMembers,Collections WHERE SampleMembers.SampleID='$SampleID' 
	AND SampleMembers.SoundID=Sounds.SoundID 
	AND Sounds.ColID=Collections.ColID ORDER BY RAND() $run_limit";

$result=query_several($query, $connection);
$nrows = mysqli_num_rows($result);

if ($nrows>0) {
	echo "<p style=\"float: right;\"><a href=\"#\" onClick=\"window.close();\">[Cancel and close window]</a>
	<h3>Working on the \"$SampleName\" sample set</h3>";

	if ($run_one==1){
		echo "<p><b>Test of the script on one file.</b>";
		}
	
	echo "
	<p>Please wait, working...</p>
		<iframe src=\"../../progressbar.php?per=1\" width=\"100%\" height=\"30\" frameborder=\"0\" id=\"progress_bar\" scrolling=\"no\"></iframe>&nbsp;
		<div id=\"progress_counter\"><strong>0 of $nrows completed</strong></div>";
	
	echo "<p>The results appear as a comma-separated list: <br>Sound_ID,Filename,script output</p>
	<hr noshade>";
	
	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		$random_name=mt_rand() . ".wav";
		
		#If a flac, extract
		if ($SoundFormat=="flac") {
			exec('flac -fd ../../sounds/sounds/' . $ColID . '/' . $DirID . '/' . $OriginalFilename . ' -o ../../tmp/' . $random_name, $lastline, $retval);
			if ($retval!=0){
				die("<p class=\"error\">There was a problem with the FLAC decoder...</div>");
				}
			}
		elseif ($SoundFormat!="wav") {
			#If anything else, make a wav file
			exec('sox ../../sounds/sounds/' . $ColID . '/' . $DirID . '/' . $OriginalFilename . ' ../../tmp/' . $random_name, $lastline, $retval);
			if ($retval!=0){
				die("<p class=\"error\">There was a problem with SoX...</div>");
				}
			}
		elseif ($SoundFormat=="wav") {
			#If its a wav file
			copy('../../sounds/sounds/' . $ColID . '/' . $DirID . '/' . $OriginalFilename, ' ../../tmp/' . $random_name);
			}

		#EXECUTE
		exec('Rscript --vanilla ' . $scriptfile . ' ../../tmp/' . $random_name . ' ' . $ColID  . ' ' . $SiteID  . ' ' . $SoundID, $script_output, $retval);
	        if ($retval!=0){
	        	die("<p class=\"error\">There was a problem with the script. Please check the log.<p><a href=\"#\" onClick=\"window.close();\">Close window</a>");
	        	}
		else {
                        echo "$SoundID, $OriginalFilename,";
                        for ($s=0;$s<count($script_output);$s++) {
                                echo "$script_output[$s]";
                                }
                        echo "<br>";
		                        
			unlink('../../tmp/' . $random_name);
			flush();
			
			$kk=$i+1;
				
			#Estimate time to completion
			$Time1=strtotime("now");
			$elapsed_time=$Time1-$Time0;
			$elapsed_time_display=formatTime($elapsed_time);
			$time_to_complete=formatTime(round((($elapsed_time)/$kk)*$nrows)-$elapsed_time);
				
			$percent_done_display=round((($kk/$nrows)*100),2);
			$percent_done=round($percent_done_display);
			
			echo "\n<script type=\"text/javascript\">
				var url='../../progressbar.php?per=$percent_done';
				document.getElementById('progress_bar').src = url;
				document.getElementById('progress_counter').innerHTML=\"<strong>$kk of $nrows completed ($percent_done_display %)<br>Time elapsed: $elapsed_time_display<br>Estimated time left: $time_to_complete</strong>\";
				</script>\n";
					
					
			if ($kk==$nrows) {
				echo "\n<script type=\"text/javascript\">
				var url='../../progressbar.php?per=100';
				document.getElementById('progress_bar').src = url;
				document.getElementById('progress_counter').innerHTML=\"<strong>Operation completed<br>Time elapsed: $elapsed_time_display</strong>\";
				</script>\n";
				}
					
			flush();
			unset($script_output);			
			
			}
		}
	}

unlink($scriptfile);

?>

<br><p><a href="#" onClick="window.close();">Close window.</a>

</div>
</body>
</html>
