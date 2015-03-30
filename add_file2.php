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

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Upload file</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
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
			$random_cookie=mt_rand();
			mkdir("tmp/$random_cookie", 0777);
			$target_path = "tmp/$random_cookie/";
			$target_path = $target_path . basename($_FILES['userfile']['name']); 

			if(move_uploaded_file($_FILES['userfile']['tmp_name'], $target_path)) {
				$filename=basename( $_FILES['userfile']['name']);
				exec('python include/soundcheck.py tmp/' . $random_cookie . '/' . $filename, $lastline, $retval);
				if ($retval==0) {
					echo "<div class=\"success\"><img src=\"images/accept.png\"> The file has been uploaded successfully.<br>";
					$file_info = $lastline[0];
					$file_info = explode(",", $file_info);
					$sampling_rate=$file_info[0];
					$no_channels=$file_info[1];
					$file_format=$file_info[2];
					$file_duration=$file_info[3];
					$file_bits=$file_info[4];
					#Get the size of the file
					$soundfile_size = formatsize(filesize("tmp/$random_cookie/$filename"));

					$DirID = rand(1,100);

					$source_dir="sounds/sounds/$ColID";
					if (!is_dir($source_dir)) {
						mkdir($source_dir, 0777);
						}
					$source_dir="sounds/sounds/$ColID/$DirID";
					if (!is_dir($source_dir)) {
						mkdir($source_dir, 0777);
						}

					if (copy("tmp/$random_cookie/$filename",$source_dir . "/" . $filename)) {
						$query_file = "INSERT INTO Sounds 
						(ColID, DirID, OriginalFilename, SoundName, SamplingRate, Channels, Duration, SoundFormat, SiteID, BitRate)
 						VALUES ('$ColID', '$DirID', '$filename', '$filename', '$sampling_rate', '$no_channels', '$file_duration', '$file_format', '$SiteID', '$file_bits')";
						$result_file = mysqli_query($connection, $query_file)
							or die (mysqli_error($connection));
						}
					
					echo "</div>";

					$SoundID=mysqli_insert_id($connection);

					echo "<p><a href=\"file_edit.php?SoundID=$SoundID\">Edit the file information</a>";
					
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
				$max1 = filter_var(ini_get("upload_max_filesize"), FILTER_SANITIZE_NUMBER_INT);
				$max2 = filter_var(ini_get("post_max_size"), FILTER_SANITIZE_NUMBER_INT);

				if ($max1 < $max2){
					$max = ini_get("upload_max_filesize");
					}
				else{
					$max = ini_get("post_max_size");
					}
				
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
