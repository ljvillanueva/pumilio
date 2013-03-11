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
$files_format=strtolower(filter_var($_POST["files_format"], FILTER_SANITIZE_STRING));
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);

if ($dir==""){
	die();
	}

if ($files_format==""){
	die();
	}

if (substr($dir, -1)!="/")
	$dir = $dir . "/";

$files_format_length=strlen($files_format);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Add from a database or spreadsheet</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
	echo $googleanalytics_code;
	}
?>

</head>
<body>

	<!-- Scripts for Javascript tooltip from http://www.walterzorn.com/tooltip/tooltip_e.htm -->
	<script type="text/javascript" src="include/wz_tooltip/wz_tooltip.js"></script>

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

			echo "<h3>Add files from a database or spreadsheet</h3>";

			if (is_dir($dir)) {
				echo "<strong>The directory has these files:</strong><div style=\"width:450px; height: 200px; overflow:auto;\">";
				$handle = opendir($dir);
				$files_to_process = array();
				$files_to_process_counter=0;
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && substr($file, -$files_format_length)==$files_format) {
						echo "$file<br>\n";
						array_push($files_to_process, $file);
						$files_to_process_counter+=1;
						}
					}
				echo "</div><br><br>";
				closedir($handle);
				}
			else{
				echo "<div class=\"error\">Could not read directory. Please make sure that it exists and that the webserver can read it.</div><br><br>";
				}

			echo "<p>There are a total of <strong>$files_to_process_counter<strong> files to add.<br><br>
			<form action=\"add_from_db3.php\" method=\"POST\" id=\"AddForm\">
				<p>If the list above seems right, continue to the next step:<br>
				<input type=\"hidden\" name=\"dir\" value=\"$dir\">
				<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
				<input type=\"hidden\" name=\"files_format\" value=\"$files_format\">
				<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
				<input type=\"hidden\" name=\"files_to_process\" value=\"$files_to_process\">
				<input type=submit value=\" Select fields \" class=\"fg-button ui-state-default ui-corner-all\">
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
