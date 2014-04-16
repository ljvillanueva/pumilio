<?php

function cleanme($string) {
	$string = stripslashes($string);
	$string = trim($string);
	return $string;
	}


function is_odd($number) {
	return $number & 1; // 0 = even, 1 = odd
	}


function run_sox($input_file, $output_file, $trim_start, $trim_length, $filter_low, $filter_high) {
	exec('sox ' . $input_file . ' ' . $output_file . ' trim ' . $trim_start . 's ' . $trim_length . 's filter ' . $filter_low . '-' . $filter_high . ' 512', $lastline, $retval);
	return $retval;
	}


function flac2wav($input_file, $output_file) {
	exec('flac -d ' . $input_file . ' -f -o ' . $output_file, $lastline, $retval);
	return $retval;
	}


function query_one($query, $connection) {
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$row = mysqli_fetch_array($result);
	return $row[0];
	}


function query_several($query, $connection) {
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	return $result;
	}


function save_log($connection, $SoundID, $LogType, $LogText) {
	$username = $_COOKIE["username"];
	if ($username != ""){
		$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);
		}
	else{
		$UserID = 0;
		}
					
	if (is_array($LogText)==TRUE){
		$LogText = implode(".", $LogText);
		}
	$query = "INSERT INTO PumilioLog (UserID, LogType, SoundID, LogText) VALUES
			('$UserID',  '$LogType',  '$SoundID',  '$LogText')";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}

	
function formatSize($size){
	switch (true){
		case ($size > 1099511627776):
		$size /= 1099511627776;
		$suffix = ' TB';
		break;
		case ($size > 1073741824):
		$size /= 1073741824;
		$suffix = ' GB';
		break;
		case ($size > 1048576):
		$size /= 1048576;
		$suffix = ' MB';   
		break;
		case ($size > 1024):
		$size /= 1024;
		$suffix = ' KB';
		break;
		default:
		$suffix = ' B';
		}
	return round($size, 2).$suffix;
	}


function delete_old($dir, $days) {
	if (substr($dir, -1, 1) != "/"){
		$dir = $dir . "/";
		}
	exec('find '. $dir . '* -atime +' . $days . ' -exec rm {} \; > /dev/null &', $line, $retval);
	}


function player_file_mp3($file_in, $samplingrate, $file_out, $random_cookie) {
	$retval = 0;
	if ($samplingrate != 44100) {
		#Safe sampling rates for mp3 files
		if ($samplingrate > 44100) {
			$to_SamplingRate = 44100;
			$nyquist_freq = $to_SamplingRate/2;
			}
		elseif ($samplingrate < 44100 && $samplingrate > 22050) {
			$to_SamplingRate = 44100;
			$nyquist_freq = $samplingrate/2;
			}
		elseif ($samplingrate < 22050 && $samplingrate > 11025) {
			$to_SamplingRate = 22050;
			$nyquist_freq = $samplingrate/2;
			}
		elseif ($samplingrate < 11025) {
			$to_SamplingRate = 11025;
			$nyquist_freq = $samplingrate/2;
			}
		else {
			$to_SamplingRate = $samplingrate;
			$nyquist_freq = $samplingrate/2;
			}
		
		$random_cookie = $random_cookie;
		if ($random_cookie == ""){
			exit("<p class=\"error\">var random_cookie is empty</div>");
			}
		
		exec('sox tmp/' . $random_cookie . '/' . $file_in . ' -r ' . $to_SamplingRate . ' tmp/' . $random_cookie . '/1.' . $file_in, $lastline, $retval1);
		if ($retval1 != 0) {
			exit("<p class=\"error\">There was a problem with SoX... Please contact your administrator.</div>");
			}
		
		exec('lame --noreplaygain -f -b 128 tmp/' . $random_cookie . '/1.' . $file_in . ' tmp/' . $random_cookie . '/' . $file_out, $lastline3, $retval);	
		}
	else {
		exec('lame --noreplaygain -f -b 128 tmp/' . $random_cookie . '/' . $file_in . ' tmp/' . $random_cookie . '/' . $file_out, $lastline3, $retval);
		}

	return $retval;
	}


function dbfile_mp3($filename, $file_format, $ColID, $DirID, $SamplingRate) {
	#Function to make an mp3 file from a file in the database
	$mp3_name = "";

	#New: sampling rate can be any of accepted Flash values: 11025,22050,44100

	#Check if file is an mp3 already
	if ($file_format == "mp3" && ($SamplingRate == 44100 || $SamplingRate == 22050 || $SamplingRate == 11025)) {
		#OK to use file
		$mp3_name = $filename;
		}
	else {
		$random_value = mt_rand();
		mkdir("tmp/$random_value", 0777);

		if ($SamplingRate > 44100) {
			$to_SamplingRate = 44100;
			}
		elseif ($SamplingRate < 44100 && $SamplingRate > 22050) {
			$to_SamplingRate=44100;
			}
		elseif ($SamplingRate < 22050 && $SamplingRate > 11025) {
			$to_SamplingRate = 22050;
			}
		elseif ($SamplingRate < 11025) {
			$to_SamplingRate = 11025;
			}
		else {
			$to_SamplingRate = $SamplingRate;
			}

		#If a flac, extract
		if ($file_format == "flac") {
			exec('flac -fd sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' -o tmp/' . $random_value . '/temp1.wav', $lastline, $retval);
			if ($retval != 0) {
				exit("<p class=\"error\">There was a problem with the FLAC decoder...</div>");
				}

			if ($SamplingRate!=44100 || $SamplingRate!=22050 || $SamplingRate!=11025) {
				exec('sox tmp/' . $random_value . '/temp1.wav tmp/' . $random_value . '/temp2.wav rate ' . $to_SamplingRate, $lastline, $retval);
				if ($retval!=0) {
					exit("<p class=\"error\">There was a problem with SoX...</div>");
					}
					
				unlink("tmp/$random_value/temp1.wav");
				rename("tmp/$random_value/temp2.wav", "tmp/$random_value/temp1.wav");
				}
			}
		else {
			exec('sox sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' tmp/' . $random_value . '/temp1.wav rate ' . $to_SamplingRate, $lastline, $retval);
			if ($retval!=0) {
				exit("<p class=\"error\">There was a problem with SoX...</div>");
				}
			}

		$fileName_exp = explode(".", $filename);
		$mp3_name = $fileName_exp[0] . ".autopreview.mp3";
		
		exec('lame --noreplaygain -f -b 128 tmp/' . $random_value . '/temp1.wav sounds/previewsounds/' . $ColID . '/' . $DirID . '/' . $mp3_name, $lastline3, $final_retval);
		#delete the temp folder
		delTree('tmp/' . $random_value . '/');

		}
	return $mp3_name;
	}


function pumilio_user($check_role, $connection) {
	if ($check_role == "user"){
		if ($login_wordpress == TRUE){
			if (is_user_logged_in() == TRUE){
				return TRUE;
				}
			else{
				return FALSE;
				}
			}
		else {
			if (sessionAuthenticate($connection)) {
				return TRUE;
				}
			else{
				return FALSE;
				}
			}
		}
	elseif ($check_role == "admin"){
		if ($login_wordpress == TRUE){
			if (is_user_logged_in() == TRUE){
				return TRUE;
				}
			else{
				return FALSE;
				}
			}
		else {
			$username = $_COOKIE["username"];
			if (is_user_admin2($username, $connection)) {
				return TRUE;
				}
			else {
				return FALSE;
				}
			}
		}
	}


function dbfile_ogg($filename, $file_format, $ColID, $DirID, $SamplingRate) {
	#Function to make an ogg file from a file in the database
	$ogg_name = "";

	#Check if file is an ogg already
	if ($file_format == "ogg" && ($SamplingRate == 44100 || $SamplingRate == 22050 || $SamplingRate == 11025)) {
		#OK to use file
		$ogg_name = $filename;
		}
	else {
		$random_value = mt_rand();
		mkdir("tmp/$random_value", 0777);

		if ($SamplingRate > 44100) {
			$to_SamplingRate = 44100;
			}
		elseif ($SamplingRate < 44100 && $SamplingRate > 22050) {
			$to_SamplingRate = 44100;
			}
		elseif ($SamplingRate < 22050 && $SamplingRate > 11025) {
			$to_SamplingRate = 22050;
			}
		elseif ($SamplingRate < 11025) {
			$to_SamplingRate = 11025;
			}
		else {
			$to_SamplingRate = $SamplingRate;
			}

		#If a flac, extract
		if ($file_format == "flac") {
			exec('flac -fd sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' -o tmp/' . $random_value . '/temp1.wav', $lastline, $retval);
			if ($retval != 0) {
				save_log($connection, $SoundID, "70", "FLAC had a problem with sounds/sounds/$ColID/$DirID/$filename.\n" . $lastline);
				exit("<p class=\"error\">There was a problem with the FLAC decoder...</div>");
				}

			if ($SamplingRate != 44100 || $SamplingRate != 22050 || $SamplingRate != 11025) {
				exec('sox tmp/' . $random_value . '/temp1.wav tmp/' . $random_value . '/temp2.wav rate ' . $to_SamplingRate, $lastline, $retval);
				if ($retval != 0) {
					save_log($connection, $SoundID, "80", "SoX had a problem with sounds/sounds/$ColID/$DirID/$filename." . $lastline);
					exit("<p class=\"error\">There was a problem with SoX...</div>");
					}
					
				unlink("tmp/$random_value/temp1.wav");
				rename("tmp/$random_value/temp2.wav", "tmp/$random_value/temp1.wav");
				}
			}
		else {
			exec('sox sounds/sounds/' . $ColID . '/' . $DirID . '/' . $filename . ' tmp/' . $random_value . '/temp1.wav rate ' . $to_SamplingRate, $lastline, $retval);
			if ($retval != 0) {
				save_log($connection, $SoundID, "80", "SoX had a problem with sounds/sounds/$ColID/$DirID/$filename." . $lastline);
				exit("<p class=\"error\">There was a problem with SoX...</div>");
				}
			}

		$fileName_exp = explode(".", $filename);
		$ogg_name = $fileName_exp[0] . ".autopreview.ogg";
		
		exec('dir2ogg tmp/' . $random_value . '/temp1.wav sounds/previewsounds/' . $ColID . '/' . $DirID . '/' . $ogg_name, $lastline3, $final_retval);
		#delete the temp folder
		delTree('tmp/' . $random_value . '/');
		}
	return $ogg_name;
	}

	
function delTree($dir) {
	#$files = glob( $dir . '*', GLOB_MARK );
	#foreach( $files as $file ){
	#	if( substr( $file, -1 ) == '/' ){
	#		delTree( $file );
	#		}
	#	else {
	#		unlink( $file );
	#		}
	#	}
	#if (is_dir($dir)) rmdir( $dir );
	system('/bin/rm -rf ' . escapeshellarg($dir));
	} 


function delSubTree($dir) {
	#delete everything but the dir
	$files = glob( $dir . '*', GLOB_MARK );
	foreach( $files as $file ){
		if( substr( $file, -1 ) == '/' ){
			delTree( $file );
			}
		else {
			unlink( $file );
			}
		}
	#if (is_dir($dir)) rmdir( $dir );
	} 


function sessionAuthenticate($connection) {
	// Get the cookie
	$cookie_to_test = $_COOKIE["usercookie"];
	$cookie_to_testa = explode(".", $cookie_to_test);
	$cookie_to_test1 = $cookie_to_testa['0'];
	$cookie_to_test2 = $cookie_to_testa['1'];

	#$cookie_to_test1=filter_var($_GET["cookie_to_test1"], FILTER_SANITIZE_NUMBER_INT);
	#$cookie_to_test2=filter_var($_GET["cookie_to_test2"], FILTER_SANITIZE_STRING);
	
	#get host name of user
	$remote_host = $_SERVER['REMOTE_ADDR'];
	$user_loggedin = query_one("SELECT COUNT(*) FROM Cookies WHERE user_id = '$cookie_to_test1' AND cookie = '$cookie_to_test2' AND hostname = '$remote_host' LIMIT 1", $connection);

	#Check if active
	$user_active = query_one("SELECT UserActive from Users WHERE UserID='$cookie_to_test1' LIMIT 1", $connection);

	// exactly one row? then we have found the user
	if ($user_loggedin == 1 && $user_active == 1) {
		return true;
		}
	else {
		return false;
		}
	}


function WordpressSessionAuthenticate($wordpress_require) {
	if (is_user_logged_in() == TRUE){
		return true;
		}
	else{
		return false;
		}
	}


function is_user_admin2($username, $connection) {
	#Check if user can edit files (i.e. has admin privileges)
	if (sessionAuthenticate($connection)) {
		if ($username != "") {
			$resultname = mysqli_query($connection, "SELECT UserRole FROM Users WHERE UserName='$username' LIMIT 1");
			$rowname = mysqli_fetch_array($resultname);
			extract($rowname);
			if ($UserRole == "admin"){
				return true;
				}
			else {
				return false;
				}
			}
		else{
			return false;
			}
		}
	}



function check_cookies() {
	if (setcookie("test", "test", time() + 100)) {
		//COOKIE IS SET 
		if (isset ($_COOKIE['test'])) {
			setcookie("test", "test", time() - 100);
			return true;
			}
		else {
			return false;
			}
		}
	}


function get_closest_weather($connection, $Lat, $Lon, $Date, $Time) {
	$weather_data_id = 0;
	$query = "SELECT WeatherSiteID, ((ACOS((SIN( '$Lat' /57.2958) * SIN( WeatherSiteLat /57.2958)) + (COS( '$Lat' /57.2958) * COS( WeatherSiteLat /57.2958) * COS( WeatherSiteLon /57.2958 - '$Lon' /57.2958)))) * 6378.7) AS Distance FROM WeatherSites ORDER BY Distance";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	for ($i = 0; $i < $nrows; $i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		
		#Is close enough? 20km
		$datetime = $Date . " " . $Time;
		$query_dq = "SELECT WeatherDataID, ABS(UNIX_TIMESTAMP('$datetime') - UNIX_TIMESTAMP(TIMESTAMP(WeatherDate, WeatherTime))) AS TimeDifference  FROM WeatherData ORDER BY TimeDifference LIMIT 1";
		$result_dq = mysqli_query($connection, $query_dq)
			or die (mysqli_error($connection));
		$nrows_dq = mysqli_num_rows($result_dq);
		if ($nrows_dq > 0) {
			$row_dq = mysqli_fetch_array($result_dq);
			extract($row_dq);
			break;
			}
		}

	if (!isset($WeatherDataID)){
		$WeatherDataID = 0;
		$TimeDifference = 0;
		$Distance = 0;
		}

	$to_return = $WeatherDataID . "," . $TimeDifference . "," . $Distance;
	return $to_return;
	}


function formatTime($secs) {
	$times = array(3600, 60, 1);
	$time = '';
	$tmp = '';
	for($i = 0; $i < 3; $i++) {
		$tmp = floor($secs / $times[$i]);
		if($tmp < 1) {
			$tmp = '00';
			}
		elseif($tmp < 10) {
			$tmp = '0' . $tmp;
			}
		$time .= $tmp;
		if($i < 2) {
			$time .= ':';
			}
		$secs = $secs % $times[$i];
		}
	return $time;
	}


function convertExifToTimestamp($exifString, $dateFormat) {
	$exifPieces = explode(":", $exifString);
	return date($dateFormat, strtotime($exifPieces[0] . "-" . $exifPieces[1] .
		"-" . $exifPieces[2] . ":" . $exifPieces[3] . ":" . $exifPieces[4]));
	}


// Original PHP code by Chirp Internet: www.chirp.com.au 
// Please acknowledge use of this code by including this header. 

function truncate2($string, $limit, $break = " ", $pad = "...") { 
	// return with no change if string is shorter than $limit  
	if(strlen($string) <= $limit) return $string; 
	
	$string = substr($string, 0, $limit); 
	if(false !== ($breakpoint = strrpos($string, $break))) { 
		$string = substr($string, 0, $breakpoint); 
		} 
	return $string . $pad; 
}


#From http://www.weberdev.com/get_example-3307.html
#Usage :
 # echo timeDiff("2002-04-16 10:00:00","2002-03-16 18:56:32");
function timeDiff($firstTime, $lastTime) {
	// convert to unix timestamps
	$firstTime = strtotime($firstTime);
	$lastTime = strtotime($lastTime);

	// perform subtraction to get the difference (in seconds) between times
	$timeDiff = $lastTime-$firstTime;

	// return the difference
	return $timeDiff;
	}


#Function to find the number of cores available to do jobs
# from http://www.theunixtips.com/how-to-find-number-of-cpus-on-unix-system
function nocores() {
	$no_cores = exec("grep processor /proc/cpuinfo | wc -l", $lastline, $return);
	return $lastline[0];
	}


##Background functions
## from http://nsaunders.wordpress.com/2007/01/12/running-a-background-process-in-php/
## and modified in the comments
function run_in_background($Command) {
	$PID = exec("$Command > /dev/null 2> /dev/null & echo $!");
	return($PID);
	}


function is_process_running($PID) {
	exec("ps $PID", $ProcessState);
	return(count($ProcessState) >= 2);
	}

	/*
	#EXAMPLE TO RUN THE ABOVE:
	echo("Running hmmsearch. . .")
	$ps = run_in_background("hmmsearch $hmmfile $fastafile > $outfile");
	  while(is_process_running($ps))
	   {
	     echo(" . ");
	       ob_flush(); flush();
		    sleep(1);
	   }
	*/

/*
function getFilePath($SoundID, $connection)
	{
	#Something to make a more usable way to be able to change the code later if needed
	$query = "SELECT ColID, SiteID, OriginalFilename FROM Sounds WHERE SoundID = '$SoundID' LIMIT 1";
	// Execute the query
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	if ($nrows > 0)	{
		$row = mysqli_fetch_array($result);
		extract($row);
		return $ColID . "/" . $SiteID . "/" . $OriginalFilename;
		}
	else
		return false;	
	}
*/


function bgHowMany() {
	#Get how many background processed are running
	exec("pgrep add_to_pumiliodb* | wc -l", $bg_processes1);
	exec("pgrep stats_pumiliodb* | wc -l", $bg_processes2);
	$bg_processes = $bg_processes1[0] + $bg_processes2[0];
	return $bg_processes;
	}


function bgHowManyAdd() {
	#Get how many background processed are running
	exec("pgrep add_to_pumiliodb* | wc -l", $bg_processes);
	return $bg_processes[0];
	}


function bgHowManyAdd_PID() {
	#Get the PID of background processed are running
	exec("pgrep add_to_pumiliodb*", $bg_processes);
	return $bg_processes;
	}


function bgProcess_howlong($PID) {
	#Get the time running of background process
	# adapted from http://stackoverflow.com/questions/6134/how-do-you-find-the-age-of-a-long-running-linux-process
	exec("ps $PID | awk '{print $4}'", $bg_processes);
	return $bg_processes[1];
	}


function add_in_background($absolute_dir, $connection) {

	$cores_to_use = query_one("SELECT Value from PumilioSettings WHERE Settings='cores_to_use'", $connection);
	if ($cores_to_use == "" || $cores_to_use == "0"){
		$cores_to_use = 1;
		}
	require("config.php");
	#$bg_processes = bgHowMany();
	$bg_processes = query_one("SELECT COUNT(*) from FilesToAddMembers WHERE ReturnCode='2'", $connection);

	if($bg_processes < $cores_to_use) {
		$random_value = mt_rand();
		$tmp_dir = 'tmp/' . $random_value;
		mkdir($tmp_dir, 0777);

		#make htaccess to protect files
			$myFile = $tmp_dir . '/.htaccess';
			$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
			fwrite($fh, "order allow,deny" . PHP_EOL);
			fwrite($fh, "deny from all" . PHP_EOL);
			fclose($fh);

		#write config file
			$myFile = $tmp_dir . '/configfile.php';
			$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
			fwrite($fh, "<?php" . PHP_EOL);
			fwrite($fh, "$host" . PHP_EOL);
			fwrite($fh, "$database" . PHP_EOL);
			fwrite($fh, "$user" . PHP_EOL);
			fwrite($fh, "$password" . PHP_EOL);
			fwrite($fh, "$absolute_dir/" . PHP_EOL);
			fwrite($fh, "?>");
			fclose($fh);
		
		copy('include/add/add_to_pumiliodb.py', $tmp_dir . '/add_to_pumiliodb.py');
		copy('include/add/soundcheck.py', $tmp_dir . '/soundcheck.py');
		#exec('chmod +x ' . $tmp_dir . '/*', $out, $retval);
		exec('cd ' . $tmp_dir . '; python add_to_pumiliodb.py > /dev/null 2> /dev/null & echo $!', $out, $retval);
		#$thisPID = $out[0];
		#$username = $_COOKIE["username"];
		#query_one("INSERT INTO BackgroundProcs (PID, username) VALUES ('$thisPID', '$username')", $connection);
		
		#CREATE TABLE IF NOT EXISTS `BackgroundProcs` (`PID` int(11) NOT NULL, `username` varchar(40) NOT NULL, `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, KEY `PID` (`PID`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;			
		}
	}


function bgHowManyCheck() {
	#Get how many background processed are running
	exec("ps aux|grep check_auxfiles_pumiliodb | wc -l", $bg_processes);
	return $bg_processes[0];
	}


function check_in_background($absolute_dir, $connection) {
	require("config.php");
	$bg_processes = bgHowManyCheck();

	if($bg_processes < 3) {
		$random_value = mt_rand();
		$tmp_dir = 'tmp/' . $random_value;
		mkdir($tmp_dir, 0777);

		#make htaccess to protect files
			$myFile = $tmp_dir . '/.htaccess';
			$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
			fwrite($fh, "order allow,deny" . PHP_EOL);
			fwrite($fh, "deny from all" . PHP_EOL);
			fclose($fh);

		#write config file
			$myFile = $tmp_dir . '/configfile.php';
			$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
			fwrite($fh, "<?php" . PHP_EOL);
			fwrite($fh, "$host" . PHP_EOL);
			fwrite($fh, "$database" . PHP_EOL);
			fwrite($fh, "$user" . PHP_EOL);
			fwrite($fh, "$password" . PHP_EOL);
			fwrite($fh, "$absolute_dir/" . PHP_EOL);
			fwrite($fh, "?>");
			fclose($fh);
		
		copy('include/check_auxfiles/check_auxfiles_pumiliodb.py', $tmp_dir . '/check_auxfiles_pumiliodb.py');
		copy('include/check_auxfiles/svt.py', $tmp_dir . '/svt.py');

		exec('chmod -R 777 ' . $tmp_dir . ';cd ' . $tmp_dir . '; ./check_auxfiles_pumiliodb.py > /dev/null 2> /dev/null & echo $!', $out, $retval);

		}
	}



#function running_pid($PID) {
#	#Check if PID is running
#	#From http://stackoverflow.com/a/9874592
#	if (file_exists( "/proc/$PID" )){
#		return true;
#		}
#	else{
#		return false;
#		}
#	}



function stats_in_background($absolute_dir, $connection) {

	$cores_to_use = query_one("SELECT Value from PumilioSettings WHERE Settings='cores_to_use'", $connection);
	if ($cores_to_use == "" || $cores_to_use == "0"){
		$cores_to_use = 1;
		}
	require("config.php");
	$bg_processes = bgHowMany();
	if($bg_processes < $cores_to_use) {
		$random_value = mt_rand();
		$tmp_dir = 'tmp/' . $random_value;
		mkdir($tmp_dir, 0777);
	
		#make htaccess to protect files
			$myFile = $tmp_dir . '/.htaccess';
			$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
			fwrite($fh, "order allow,deny" . PHP_EOL);
			fwrite($fh, "deny from all" . PHP_EOL);
			fclose($fh);

		#write config file
			$myFile = $tmp_dir . '/configfile.php';
			$fh = fopen($myFile, 'w') or die("Can't write the configuration file $myFile. Please check that the webserver can write the tmp directory.");
			fwrite($fh, "<?php" . PHP_EOL);
			fwrite($fh, "$host" . PHP_EOL);
			fwrite($fh, "$database" . PHP_EOL);
			fwrite($fh, "$user" . PHP_EOL);
			fwrite($fh, "$password" . PHP_EOL);
			fwrite($fh, "$absolute_dir/" . PHP_EOL);
			fwrite($fh, "$random_value" . PHP_EOL);
			fwrite($fh, "$R_ADI_db_value" . PHP_EOL);
			fwrite($fh, "$R_ADI_max_freq" . PHP_EOL);
			fwrite($fh, "$R_ADI_freq_step" . PHP_EOL);
			fwrite($fh, "$R_H_segment_length" . PHP_EOL);
			fwrite($fh, "?>");
			fclose($fh);
		
		copy('include/R/stats_pumiliodb.py', $tmp_dir . '/stats_pumiliodb.py');
		copy('include/R/getstats.R', $tmp_dir . '/getstats.R');
		exec('chmod +x ' . $tmp_dir . '/*', $out, $retval);
		exec('chmod -R 777 ' . $tmp_dir . '', $out, $retval);
		exec('cd ' . $tmp_dir . '; ./stats_pumiliodb.py > /dev/null 2> /dev/null & echo $!', $out, $retval);
		}
	}


function bgHowManyStats() {
	#Get how many background processed are running
	exec("pgrep stats_pumiliodb* | wc -l", $bg_processes);
	return $bg_processes[0];
	}


function bgHowManyStats_PID() {
	#Get the PID of background processed are running
	exec("pgrep stats_pumiliodb*", $bg_processes);
	return $bg_processes;
	}
	
	

#From http://stackoverflow.com/questions/834303/php-startswith-and-endswith-functions	
function endsWith($haystack, $needle, $case = true){
	$expectedPosition = strlen($haystack) - strlen($needle);

	if($case)
	return strrpos($haystack, $needle, 0) === $expectedPosition;

	return strripos($haystack, $needle, 0) === $expectedPosition;
	}



function authenticateUser($connection, $username, $password) {
	// Test the username and password parameters
	if (!isset($username) || !isset($password))
		return false;

	#Use plain text passwords
	$enc_password = md5($password);

	// Formulate the SQL find the user
	$query = "SELECT * FROM Users WHERE UserName = '$username' AND UserPassword = '$enc_password' AND UserActive='1'";

	// Execute the query
	$result = mysqli_query($connection, $query)
		or die ("Could not execute query. Please try again later.");

	// exactly one row? then we have found the user
	if (mysqli_num_rows($result) != 1)
		return false;
	else
		return true;
	}
	
	
function WordpressAuthenticateUser() {
	// Test the username and password parameters
	if (!file_exists($wordpress_require)) {
		return false;
		}
	else {
		require_once('../wp-blog-header.php');

		if (is_user_logged_in() == TRUE){
			return true;
			}
		else{
			return false;
			}
		}
	}
	
?>