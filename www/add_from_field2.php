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

#Check if user can edit files (i.e. has admin privileges)
	if (!sessionAuthenticate($connection)) {
		die();
		}
		
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die();
		}

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$sm=filter_var($_POST["sm"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$local=filter_var($_POST["local"], FILTER_SANITIZE_NUMBER_INT);
$localdir=filter_var($_POST["localdir"], FILTER_SANITIZE_STRING);

if ($ColID=="") {
	die();
	}

echo "
<html>
<head>
";

if ($sm==1) {
	echo "<title>$app_custom_name - Add files from a Wildlife Acoustic SongMeter</title>";
	}
else {
	echo "<title>$app_custom_name - Add files from the field</title>";
	}

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
	
$dir = "$absolute_dir/tmp/" . $_COOKIE["random_upload_dir"] . "/";

?>

</head>
<body>

<?php
if ($local == 1){
	$temp_add_dir=query_one("SELECT Value from PumilioSettings WHERE Settings='temp_add_dir'", $connection);
	$dir_allowed = strstr($localdir, $temp_add_dir);

	if ($dir_allowed != TRUE){
		echo "<div class=\"error\">That directory is not allowed. Please go back and try again. The allowed directory is set in the <a href=\"admin.php\">Administration</a> menu.</div>
		</body>
		</html>";
		die();
		}
	}
?>

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

			if ($sm==1) {
				echo "<h3>Add sound files from a Wildlife Acoustic SongMeter</h3>";
				}
			else {
				echo "<h3>Add sound files from the field</h3>";
				}

			if ($local == 1){
				$dir = $localdir;
				if (is_dir($dir) && opendir($dir)) {
				echo "<strong>These files were in the local folder:</strong><div style=\"width:450px; height: 200px; overflow:auto;\">";
				$handle = opendir($dir);
				$files_to_process = array();
				$files_to_process_counter=0;
				while (false !== ($file = readdir($handle))) {
			     	   if ($file != "." && $file != "..") {
				 		echo "$file<br>\n";
						array_push($files_to_process, $file);
						$files_to_process_counter+=1;
						}
			   		 }
					echo "</div><br><br>";
			    		closedir($handle);
			    		setcookie("localdir", $localdir, time()+(3600*24*30), $app_dir);
					}
				else {
					echo "<div class=\"error\">Could not read folder with the files. Please go back and try again.</div><br><br>";
					}
				}
			else {
				if (is_dir($dir) && opendir($dir)) {
					echo "<strong>These files were uploaded:</strong><div style=\"width:450px; height: 200px; overflow:auto;\">";
					$handle = opendir($dir);
					$files_to_process = array();
					$files_to_process_counter=0;
					while (false !== ($file = readdir($handle))) {
				     	   if ($file != "." && $file != "..") {
					 		echo "$file<br>\n";
							array_push($files_to_process, $file);
							$files_to_process_counter+=1;
						}
				   	 }
					echo "</div><br><br>";
				    closedir($handle);
				}
				else {
					echo "<div class=\"error\">No files were uploaded. Please go back and try again.</div><br><br>";
					}
				}
				


			if ($files_to_process_counter == 0){
				echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> No files were uploaded.
					Please go back and try again.</div>";
				}
			else {

			echo "There were <strong>$files_to_process_counter</strong> files found.<br>";
			if ($sm){
				echo "<form action=\"add_from_field4.php\" method=\"POST\" id=\"AddForm\">";
				}
			else {
				echo "<form action=\"add_from_field3.php\" method=\"POST\" id=\"AddForm\">";
				}
			echo "<p>If the list above seems right, continue to the next step:<br>";
			echo "<input type=\"hidden\" name=\"dir\" value=\"$dir\"><input type=\"hidden\" name=\"files_format\" value=\"$files_format\">
			<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
			<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
			<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
			<input type=\"hidden\" name=\"SensorID\" value=\"$SensorID\">
			<input type=\"hidden\" name=\"sm\" value=\"$sm\">
			<input type=submit value=\" Next step \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>";

			}
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

</body>
</html>
