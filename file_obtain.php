<?php
#
# Use this file to give Pumilio a file from another database or web app
# Call this script with 3 GET variables:
#  - file: full file path and file name
#  - method: 1 for a file in the same server; 2 for a file available from the web
#  - fileid: a numeric id that identifies the file
#
# IMPORTANT NOTES:
#  - don't include the http:// part
#  - if the file is in the same server, the path name must be the internal path, for example '/var/www/'

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require("config.php");
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");
require("include/check_login.php");

#Generate a random number and store in cookies
	$random_cookie = mt_rand();
	setcookie("random_cookie", $random_cookie, 0);

#Create the directory using that random number
	mkdir("tmp/$random_cookie", 0777);
	$target_path = "tmp/$random_cookie/";

#Sanitize GET variables
$obtain_method = filter_var($_GET["method"], FILTER_SANITIZE_NUMBER_INT);

	if(isset($_GET["file"])){
		$obtain_soundfile = filter_var($_GET["file"], FILTER_SANITIZE_SPECIAL_CHARS);
		#Remove the http://
		$obtain_soundfile = str_ireplace("http://", "", $obtain_soundfile);
		}
	else{
		$obtain_soundfile = "";
		}

$obtain_fileid = filter_var($_GET["fileid"], FILTER_SANITIZE_NUMBER_INT);


#Check if internal transfer
if ($obtain_method==3) {
	if ($guests_can_open == FALSE && $pumilio_loggedin == FALSE) {
		header("Location: error.php?e=login");
		die();
		}

	$result=query_several("SELECT * FROM Sounds WHERE SoundID='$obtain_fileid'", $connection);
	$row = mysqli_fetch_array($result);
	extract($row);

	$obtain_soundfile="sounds/sounds/" . $ColID . "/" . $DirID . "/" . $OriginalFilename;
	if (!copy($obtain_soundfile, $target_path . $OriginalFilename)) {
		die ("<div class=\"error\">Failed to copy $obtain_soundfile...</div>\n");
		}

	header("Location: ./openfile.php?filename=$OriginalFilename&format=$SoundFormat&duration=$Duration&samprate=$SamplingRate&fileID=$SoundID&no_channels=$Channels&from_db=TRUE");
	die();
	}


if (!$allow_upload){
	header("Location: error.php?e=upload");
	die();
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Obtain a file from the web</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
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

		<?php

		#Check that variables are not empty
		if ($obtain_soundfile=="" || $obtain_fileid=="" || $obtain_method=="") {
			echo "<div class=\"error\">Script was called incorrectly. Please check the documentation and try again.</div>
				</div>
				<div class=\"span-24 last\">";

					require("include/bottom.php");

			echo "</div></div>
			</body>
			</html>";
			die();
			}

		#Check that any error check was triggered
		if ($error_msg!="") {
			echo "<div class=\"error\">$error_msg</div>
				</div>
				<div class=\"span-24 last\">";

					require("include/bottom.php");

			echo "</div></div>
			</body>
			</html>";
			die();
			}

		#General variables
		$obtain_soundfile_exp=explode("/", $obtain_soundfile);
		$filename_pos=(count($obtain_soundfile_exp))-1;
		$filename=$obtain_soundfile_exp[$filename_pos];

		#The file is in the same server
		if ($obtain_method==1) {
			if (!copy($obtain_soundfile, $target_path . $filename)) {
				die ("<div class=\"error\">Failed to copy $obtain_soundfile...</div>\n");
				}
			}
		if ($obtain_method==2) {
			exec('wget http://' . $obtain_soundfile . ' -O ' . $target_path . $filename, $lastline, $retval);
			if ($retvar!=0) {
				die("<div class=\"error\">Could not find the file or there was a problem with wget.</div>");
				}
			}


		#Once the file is in the tmp/random dir, check it
		exec('python include/soundcheck.py tmp/' . $random_cookie . '/' . $filename, $lastline, $retval);
		if ($retval==0) {
			echo "<div class=\"success\"><img src=\"images/accept.png\"> The file has been obtained successfully.<br>";
		$file_info=$lastline[0];
		$file_info=explode(",",$file_info);
		$sampling_rate=$file_info[0];
		$no_channels=$file_info[1];
		$file_format=$file_info[2];
		$file_duration=$file_info[3];
		$file_bits=$file_info[4];

		#Get the size of the file
		$soundfile_size=formatsize(filesize("tmp/$random_cookie/$filename"));
		$fileID=$obtain_fileid;

		echo "File name: $filename<br>
		File format: $file_format<br>
		File size: $soundfile_size<br>
		Number of channels: $no_channels<br>
		Duration: $file_duration seconds<br>
		Sampling Rate: $sampling_rate Hz<br>
		Bits: $file_bits<br>
		File ID: $fileID
		</div>";

		echo "<p><a href=\"openfile.php?filename=$filename&format=$file_format&duration=$file_duration&samprate=$sampling_rate&fileID=$fileID&no_channels=$no_channels\"><img src=\"images/drive_magnify.png\"> Open file</a>";
			}
		else {
			echo "<div class=\"error\">There was an error opening the file or it is not a recognized sound file. Please try again.</div><p>The accepted file formats are:<br>";
			exec('include/audiolab_formats.py', $lastline1);
				$available_formats=$lastline1[0];
				$available_formats=explode(",",$available_formats);
				for ($f=0;$f<count($available_formats);$f++) {
					echo $available_formats[$f] . " ";
					}
			#delete the folder and destroy cookie
			delTree('tmp/' . $random_cookie . '/');
			#setcookie("random_cookie", "", time()-3600);
			}

		?>

		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
