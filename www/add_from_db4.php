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

require("include/check_login.php");

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$files_format=strtolower(filter_var($_POST["files_format"], FILTER_SANITIZE_STRING));
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);

#Fields
$OtherSoundID=filter_var($_POST["OtherSoundID"], FILTER_SANITIZE_NUMBER_INT);
$SoundName=filter_var($_POST["SoundName"], FILTER_SANITIZE_NUMBER_INT);
$Date=filter_var($_POST["Date"], FILTER_SANITIZE_NUMBER_INT);
$Time=filter_var($_POST["Time"], FILTER_SANITIZE_NUMBER_INT);
$Location=filter_var($_POST["Location"], FILTER_SANITIZE_NUMBER_INT);
$Latitude=filter_var($_POST["Latitude"], FILTER_SANITIZE_NUMBER_INT);
$Longitude=filter_var($_POST["Longitude"], FILTER_SANITIZE_NUMBER_INT);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_NUMBER_INT);
$files_to_process=filter_var($_POST["files_to_process"], FILTER_SANITIZE_STRING);
$files_to_process_counter=filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);

if ($dir=="")
	{die();}

if ($files_format=="")
	{die();}

$files_format_length=strlen($files_format);

#Parse together which fields to use
$fields_to_use="OriginalFilename,";
$fields_to_use_counter=1;

if ($OtherSoundID)
	{$fields_to_use=$fields_to_use . "OtherSoundID,";
	$fields_to_use_counter+=1;}
if ($SoundName)
	{$fields_to_use=$fields_to_use . "SoundName,";
	$fields_to_use_counter+=1;}
if ($Date)
	{$fields_to_use=$fields_to_use . "Date,";
	$fields_to_use_counter+=1;}
if ($Time)
	{$fields_to_use=$fields_to_use . "Time,";
	$fields_to_use_counter+=1;}
if ($Location)
	{$fields_to_use=$fields_to_use . "Location,";
	$fields_to_use_counter+=1;}
if ($Latitude)
	{$fields_to_use=$fields_to_use . "Latitude,";
	$fields_to_use_counter+=1;}
if ($Longitude)
	{$fields_to_use=$fields_to_use . "Longitude,";
	$fields_to_use_counter+=1;}
if ($Notes)
	{$fields_to_use=$fields_to_use . "Notes,";
	$fields_to_use_counter+=1;}

if (substr($fields_to_use, -1)==",")
	$fields_to_use=substr($fields_to_use, 0, -1);

echo "
<html>
<head>

<title>$app_custom_name - Add from a database or spreadsheet</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddForm").validate({
			rules: {
				commadata: {
					required: true
				},
			},
			messages: {
				commadata: "Please enter the data"
			}
			});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>

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
/*
			if ($files_to_process_counter>50)
				die("<div class=\"error\">There is a limit of 50 files at a time to avoid server overload and timeouts. Please go back and try again.</div>");
*/
			echo "There are $fields_to_use_counter fields: $fields_to_use<br>
				OriginalFilename is the file name, it must match the file in the directory.";


			echo "<form action=\"add_from_db5.php\" method=\"POST\" id=\"AddForm\">";
			echo "<p>Add one line for each file (use [enter] for a new line) with $fields_to_use_counter fields separated by pipes (|). If the data is missing, enter \"NULL\" without the quotes. <br>There must be $files_to_process_counter lines, one for each file in the directory to import.<br>";
			echo "<textarea name=\"commadata\" cols=\"60\" rows=\"10\"></textarea>\n";
			echo "<input type=\"hidden\" name=\"fields\" value=\"$fields_to_use\"><input type=\"hidden\" name=\"dir\" value=\"$dir\"><input type=\"hidden\" name=\"files_format\" value=\"$files_format\"><input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
			<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
			<input type=\"hidden\" name=\"fields_to_use_counter\" value=\"$fields_to_use_counter\">
			<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
			<input type=\"hidden\" name=\"files_to_process\" value=\"$files_to_process\">";
			echo " <br><br><input type=submit value=\" Check data and insert to database \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
			<span class=\"notice\">This operation may take some time, do not click more than once.</span></form>";
			echo "<br><br>";
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
