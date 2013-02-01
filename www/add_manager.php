<?php
session_start();

#ignore_user_abort(true);
set_time_limit(0);

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");

#Check if user can edit files (i.e. has admin privileges)
	if (!sessionAuthenticate($connection)){
		die();
		}
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die();
		}

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);

$codedyear=filter_var($_POST["codedyear"], FILTER_SANITIZE_STRING);
$codedmonth=filter_var($_POST["codedmonth"], FILTER_SANITIZE_STRING);
$codedday=filter_var($_POST["codedday"], FILTER_SANITIZE_STRING);
$codedhour=filter_var($_POST["codedhour"], FILTER_SANITIZE_STRING);
$codedminutes=filter_var($_POST["codedminutes"], FILTER_SANITIZE_STRING);
$codedseconds=filter_var($_POST["codedseconds"], FILTER_SANITIZE_STRING);
$sm=filter_var($_POST["sm"], FILTER_SANITIZE_NUMBER_INT);
$files_to_process_counter=filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);

if ($dir=="") {
	die("The server did not get which directory to use. Please go back and try again.");
	}

#Make sure the path ends in a slash
if (substr($dir, -1) != "/") {
	$dir = $dir . "/";
	}
	
echo "
<html>
<head>

<title>$app_custom_name - Add files from the field</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php


			if (is_dir($dir) && opendir($dir)) {
				$handle = opendir($dir);
				$files_to_process = array();
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						array_push($files_to_process, $file);
						}
					}
				closedir($handle);
				}

			$cookie_to_test = $_COOKIE["usercookie"];
			$cookie_to_testa = explode(".", $cookie_to_test);
			$UserID = $cookie_to_testa['0'];
			
			$query_1="INSERT INTO FilesToAdd (UserID, StartTime, FilesPath) VALUES ('$UserID', NOW(), '$dir')";

			$result_1 = mysqli_query($connection, $query_1)
					or die (mysqli_error($connection));

			$ToAddID=mysqli_insert_id($connection);
			
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

			$kk=0;
			for ($k=0;$k<$files_to_process_counter;$k++) {
				$this_file=$files_to_process[$k];
				
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

				$file_path = $dir . $this_file;

				$query_to_insert="INSERT INTO FilesToAddMembers (FilesToAddID, FullPath, OriginalFilename, Date, Time, SiteID, ColID, DirID, SensorID)
				VALUES ('$ToAddID', '$file_path', '$this_file', '$datecoded', '$timecoded', '$SiteID', '$ColID', '$DirID', '$SensorID')";

				$result = mysqli_query($connection, $query_to_insert)
					or die (mysqli_error($connection));
				$kk=$k+1;
				}			

			$CollectionName=query_one("SELECT CollectionName from Collections WHERE ColID='$ColID'", $connection);

			echo "<br><div class=\"success\">$kk files were scheduled to be added to the collection $CollectionName.</div>
				<p><a href=\"add.php\">Add more files</a> or <a href=\"file_manager.php\">check file status</a>.";
				
			?>

		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

<?php
session_write_close();
flush(); @ob_flush();
add_in_background($absolute_dir, $connection);
?>

</body>
</html>

