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
	if (!sessionAuthenticate($connection))
		{die();}
		
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection))
		{die();}

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$files_format=strtolower(filter_var($_POST["files_format"], FILTER_SANITIZE_STRING));
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$files_to_process=filter_var($_POST["files_to_process"], FILTER_SANITIZE_STRING);
$files_to_process_counter=filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);

if ($dir=="")
	{die();}

if ($files_format=="")
	{die();}

$files_format_length=strlen($files_format);

echo "
<html>
<head>

<title>$app_custom_name - Add from a database or spreadsheet</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<?php
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
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



			echo "<form action=\"add_from_db4.php\" method=\"POST\" id=\"AddForm\">";
			echo "<p>Select which fields you will add from the list below. Technical data like number of 
				channels, format, and duration are filled automatically.<br>";
			echo "<input type=\"checkbox\" name=\"OtherSoundID1\" value=\"1\" checked=\"checked\" DISABLED> Custom ID for the file (OtherSoundID - Required)<br>\n";
			echo "<input type=\"checkbox\" name=\"SoundName\" value=\"1\"> SoundName (default: same as the file name)<br>\n";
			echo "<input type=\"checkbox\" name=\"Date\" value=\"1\"> Date (YYYY-MM-DD)<br>\n";
			echo "<input type=\"checkbox\" name=\"Time\" value=\"1\"> Time (HH:MM:SS)<br>\n";
			echo "<input type=\"checkbox\" name=\"Location\" value=\"1\"> Location<br>\n";
			echo "<input type=\"checkbox\" name=\"Latitude\" value=\"1\"> Latitude (decimal degrees)<br>\n";
			echo "<input type=\"checkbox\" name=\"Longitude\" value=\"1\"> Longitude (decimal degrees)<br>\n";
			echo "<input type=\"checkbox\" name=\"Notes\" value=\"1\"> Notes<br>\n";
			echo "<input type=\"hidden\" name=\"dir\" value=\"$dir\"><input type=\"hidden\" name=\"files_format\" value=\"$files_format\"><input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
			<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
			<input type=\"hidden\" name=\"files_to_process\" value=\"$files_to_process\">
			<input type=\"hidden\" name=\"OtherSoundID\" value=\"1\">";
			echo " <input type=submit value=\" Continue \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";

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
