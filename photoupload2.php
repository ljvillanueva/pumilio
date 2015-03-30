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

$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$photodir=filter_var($_POST["photodir"], FILTER_SANITIZE_STRING);
$photonotes=filter_var($_POST["photonotes"], FILTER_SANITIZE_STRING);

if ($photodir==""){
	$photodir="NULL";
	}
if ($photonotes==""){
	$photonotes="NULL";
	}
		
$random_cookie=mt_rand();


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Upload photograph</title>";

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

			if (($_FILES['userfile']['type'] == "image/jpeg") || ($_FILES['userfile']['type'] == "image/pjpeg")) {
				$target_path = "sitephotos/$SiteID/";
				if (!is_dir($target_path)) {
					mkdir($target_path, 0777);
					}
				$filename=basename( $_FILES['userfile']['name']);
				
				if (is_file($target_path)) {
					$filename=str_ireplace(".jpg",$random_cookie . ".jpg",$filename);
					}

				$target_path = $target_path . $filename; 

				if(move_uploaded_file($_FILES['userfile']['tmp_name'], $target_path)) {
					$exif_data = exif_read_data($target_path, 0, true);
					if ($exif_data===false) {
						echo "<div class=\"error\">The file could not be processed because it does not have the EXIF headers.</div>";
						}
					else { 
						$exif_date = $exif_data['EXIF']['DateTimeOriginal'];
						$photo_date=convertExifToTimestamp($exif_date, "Y-m-d H:i:s");

						#Get username
						$username = $_COOKIE["username"];
						$result = mysqli_query($connection, "SELECT * FROM Users WHERE UserName='$username' LIMIT 1")
							or die (mysqli_error($connection));
						$row = mysqli_fetch_array($result);
						if (mysqli_num_rows($result) == 1) {
							extract($row);
							}
				
						$query_file = "INSERT INTO SitesPhotos 
							(SiteID,PhotoFilename,ViewDegrees,PhotoDate,UserID,PhotoNotes)
	 						VALUES
	 						('$SiteID','$filename','$photodir','$photo_date','$UserID','$PhotoNotes')";
						$result_file = mysqli_query($connection, $query_file)
							or die (mysqli_error($connection));
				
						echo "<div class=\"success\">The photograph was uploaded successfuly.</div>";
						}
					}
				else {
					echo "<div class=\"error\">The file could not be processed.</div>";
					}
				}
			else {
				echo "<div class=\"error\">The file is not a recognized jpg file.</div>";
				}

			?>

		<p><a href="photoupload.php">Upload another photograph.</a>

		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
