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

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$files_to_process_counter=filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);
$sm=filter_var($_POST["sm"], FILTER_SANITIZE_NUMBER_INT);

if ($dir=="") {
	die();
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
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

			if ($sm==1) {
				echo "<h3>Add sound files from a Wildlife Acoustic SongMeter</h3>";
				}
			else {
				echo "<h3>Add sound files from the field</h3>";
				}

			echo "<form action=\"add_from_field4.php\" method=\"POST\" id=\"AddForm\">";
			
				if ($sm==1) {
					echo "<p>The date and time will be extracted from the filename. It is assumed to be encoded as: *YYYYMMDD_HHMMSS.(wav/flac), where * is
						the prefix of the box, if used, and (wav/flac) refers to either flac or wav format. The WA wac format is NOT supported.
						If the files are in another format, use the <a href=\"add_from_field.php\">regular import</a>.";
					
						$handle = opendir($dir);
						$cc=1;
						while (false !== ($file = readdir($handle)) && $cc==1) {
							if ($file != "." && $file != "..") {
							    echo "<p>Filename example: <strong>$file</strong><br>\n";
								$cc=2;
							}
						    }
						    closedir($handle);
					}
				else {
					echo "<p>Type the positions of the coded information in the filename. 
						For example, the file \"SM88_20080802_170000.flac,\" recorded at the site SM88, at 5:00 pm on 2 August 2008, would be coded as:<br>
						&nbsp;&nbsp; Year: 6:9<br>
						&nbsp;&nbsp; Month: 10:11<br>
						&nbsp;&nbsp; Day: 12:13<br>
						&nbsp;&nbsp; Hour: 15:16<br>
						&nbsp;&nbsp; Minutes: 17:18<br>
						&nbsp;&nbsp; Seconds: 19:20<br>
						";
				
				$handle = opendir($dir);
				$cc=1;
				while (false !== ($file = readdir($handle)) && $cc==1) {
					if ($file != "." && $file != "..") {
					    echo "<p>Filename example: <strong>$file</strong><br>\n";
						$cc=2;
					}
				    }
				    closedir($handle);
				    
				echo "&nbsp;&nbsp; Year: <input type=\"text\" size=\"6\" name=\"codedyear\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"";
				if (isset($_COOKIE["codedyear"]))
					echo " value=\"" . $_COOKIE["codedyear"] . "\"";
				echo "><br>\n";

				echo "&nbsp;&nbsp; Month: <input type=\"text\" size=\"6\" name=\"codedmonth\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"";
				if (isset($_COOKIE["codedmonth"]))
					echo " value=\"" . $_COOKIE["codedmonth"] . "\"";
				echo "><br>\n";

				echo "&nbsp;&nbsp; Day: <input type=\"text\" size=\"6\" name=\"codedday\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"";
				if (isset($_COOKIE["codedday"]))
					echo " value=\"" . $_COOKIE["codedday"] . "\"";
				echo "><br>\n";

				echo "&nbsp;&nbsp; Hour: <input type=\"text\" size=\"6\" name=\"codedhour\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"";
				if (isset($_COOKIE["codedhour"]))
					echo " value=\"" . $_COOKIE["codedhour"] . "\"";
				echo "><br>\n";

				echo "&nbsp;&nbsp; Minutes: <input type=\"text\" size=\"6\" name=\"codedminutes\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"";
				if (isset($_COOKIE["codedminutes"]))
					echo " value=\"" . $_COOKIE["codedminutes"] . "\"";
				echo "><br>\n";

				echo "&nbsp;&nbsp; Seconds: <input type=\"text\" size=\"6\" name=\"codedseconds\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"";
				if (isset($_COOKIE["codedseconds"]))
					echo " value=\"" . $_COOKIE["codedseconds"] . "\"";
				echo "><br>\n";

				echo "<p>These values will be stored in a cookie in your computer to save you from entering them next time.";
					}
				echo "<input type=\"hidden\" name=\"dir\" value=\"$dir\">
				<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
				<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
				<input type=\"hidden\" name=\"SensorID\" value=\"$SensorID\">
				<input type=\"hidden\" name=\"sm\" value=\"$sm\">
				<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
				<p><input type=submit value=\" Next step \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>";
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
