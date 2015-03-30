<?php
session_start();

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

$force_loggedin = TRUE;
require("include/check_login.php");

$jquerycss = $_COOKIE["jquerycss"];

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - User Settings Page</title>";

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

			echo "<h3>User settings</h3>
			<form action=\"set_cookies.php\" method=\"post\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"jquerycss\">
				Select a theme for the application:
				<select name=\"css\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">\n";

				if ($jquerycss=="" || $jquerycss=="cupertino") {
					$cupertino_s = "SELECTED";
					}
				elseif ($jquerycss=="blitzer") {
					$blitzer_s = "SELECTED";
					}
				elseif ($jquerycss=="start") {
					$start_s = "SELECTED";
					}
				elseif ($jquerycss=="humanity") {
					$humanity_s = "SELECTED";
					}
				elseif ($jquerycss=="lightness") {
					$lightness_s = "SELECTED";
					}
				elseif ($jquerycss=="overcast") {
					$overcast_s = "SELECTED";
					}
				elseif ($jquerycss=="peppergrinder") {
					$peppergrinder_s = "SELECTED";
					}
				elseif ($jquerycss=="smoothness") {
					$smoothness_s = "SELECTED";
					}
				elseif ($jquerycss=="sunny") {
					$sunny_s = "SELECTED";
					}
				elseif ($jquerycss=="hotsneaks") {
					$hotsneaks_s = "SELECTED";
					}
				elseif ($jquerycss=="excitebike") {
					$excitebike_s = "SELECTED";
					}
				elseif ($jquerycss=="southstreet") {
					$southstreet_s = "SELECTED";
					}
				elseif ($jquerycss=="blacktie") {
					$blacktie_s = "SELECTED";
					}

				echo "	<option value=\"blacktie\" $blacktie_s>Black Tie</option>
					<option value=\"blitzer\" $blitzer_s>Blitzer</option>
					<option value=\"cupertino\" $cupertino_s>Cupertino (default)</option>
					<option value=\"excitebike\" $excitebike_s>Excite Bike</option>
					<option value=\"hotsneaks\" $hotsneaks_s>Hot Sneaks</option>
					<option value=\"humanity\" $humanity_s>Humanity</option>
					<option value=\"lightness\" $lightness_s>Lightness</option>
					<option value=\"overcast\" $overcast_s>Overcast</option>
					<option value=\"peppergrinder\" $peppergrinder_s>Pepper-Grinder</option>
					<option value=\"smoothness\" $smoothness_s>Smoothness</option>
					<option value=\"southstreet\" $southstreet_s>South Street</option>
					<option value=\"start\" $start_s>Start</option>
					<option value=\"sunny\" $sunny_s>Sunny</option>\n";

			echo "</select> 
				<br><br>
				<input type=submit value=\" Change theme \" class=\"fg-button ui-state-default ui-corner-all\">
				<br><br>";
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
