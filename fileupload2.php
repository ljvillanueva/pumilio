<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");
$force_loggedin = TRUE;
require("include/check_login.php");

$random_cookie=mt_rand();
setcookie("random_cookie", $random_cookie, 0);


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Upload file</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


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

			mkdir("tmp/$random_cookie", 0777);
			$target_path = "tmp/$random_cookie/";

			$target_path = $target_path . basename( $_FILES['userfile']['name']); 

			if(move_uploaded_file($_FILES['userfile']['tmp_name'], $target_path)) {
				$filename=basename( $_FILES['userfile']['name']);
				exec('python include/soundcheck.py tmp/' . $random_cookie . '/' . $filename, $lastline, $retval);
				if ($retval==0) {
					echo "<div class=\"success\"><img src=\"images/accept.png\"> The file has been uploaded successfully.<br>";
					$file_info=$lastline[0];
					$file_info=explode(",",$file_info);
					$sampling_rate=$file_info[0];
					$no_channels=$file_info[1];
					$file_format=$file_info[2];
					$file_duration=$file_info[3];
					$file_bits=$file_info[4];
					#Get the size of the file
					$soundfile_size=formatsize(filesize("tmp/$random_cookie/$filename"));
					$fileID=$_POST["fileID"];

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
					#delete the folder and destroy cookie
					delTree('tmp/' . $random_cookie . '/');
					#setcookie("random_cookie", "", time()-3600);
					$max=ini_get("upload_max_filesize");
					echo "<div class=\"error\">The file is not a recognized sound file.</div><p>The accepted file formats are:<br>";
					include("include/sox_formats.php");
					}
				}
			else {
				$max=ini_get("upload_max_filesize");
				echo "<div class=\"error\">There was a problem with the upload or the file is too big. The maximum file size is $max. Please try again.</div>";
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
