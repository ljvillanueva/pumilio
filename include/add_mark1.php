<?php
require("functions.php");
require("../config.php");
require("apply_config_include.php");
require("check_login.php");

echo "<!DOCTYPE html>
<html>
<head>

<title>Pumilio</title>";

require("get_css3_include.php");
require("get_jqueryui_include.php");
?>

</head>
<body onblur="window.focus();">
<div style="padding: 10px;">

<?php

$allowuse = FALSE;

if ($no_login == TRUE) {
	$allowuse = FALSE;
	}
else {
	if ($login_wordpress == TRUE){
		if (is_user_logged_in() == TRUE){
			$allowuse = TRUE;
			}
		else {
			$allowuse = FALSE;
			}
		}
	else{
		if (sessionAuthenticate($connection)) {
			$allowuse = TRUE;
			}
		}
	}	
		

if ($allowuse == FALSE){
	echo "<p>You have to be logged in to use this tool.";
	die();
	}
	
	
$t_min=filter_var($_POST["t_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$t_max=filter_var($_POST["t_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_min=filter_var($_POST["f_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_max=filter_var($_POST["f_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$SoundID=filter_var($_POST["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$from_db=filter_var($_POST["from_db"], FILTER_SANITIZE_STRING_INT);

if ($mark_tag_name==""){
	$mark_tag_name = "Species";
	}


if ($t_min==""){
	die("<p class=\"error\">Please select an area of the spectrogram first.");
	}


echo "$SoundID
<form method=\"POST\" action=\"add_mark2.php\">
	<input type=\"hidden\" name=\"t_min\" value=\"$t_min\">
	<input type=\"hidden\" name=\"t_max\" value=\"$t_max\">
	<input type=\"hidden\" name=\"f_min\" value=\"$f_min\">
	<input type=\"hidden\" name=\"f_max\" value=\"$f_max\">
	<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
	<input type=\"hidden\" name=\"from_db\" value=\"$from_db\">

	<h3>Selection:</h3>
	<p>Frequency range: $f_min - $f_max Hz
	<p>Time range: $t_min - $t_max seconds
	<p>$mark_tag_name: <input type=\"text\" name=\"mark_tag\" class=\"form-control\">
	<p><button type=\"submit\" class=\"btn btn-xs btn-primary\">Insert to database</button> 
</form>\n";

?>

<br><br><p><a href="#" onClick="opener.location.reload();window.close();">Cancel and close window</a>

</div>

</body>
</html>
