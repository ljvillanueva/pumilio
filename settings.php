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

$Token=filter_var($_GET["Token"], FILTER_SANITIZE_STRING);

$username = $_COOKIE["username"];
$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);

$valid_token = query_one("SELECT COUNT(*) FROM Tokens WHERE TokenID='$Token' AND UserID='$UserID'", $connection);

if ($valid_token==1) {
	$soundfile_name = query_one("SELECT soundfile_name FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Settings Page</title>";

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

		<?php

	
		if ($soundfile_name!="") {
			//If its not empty, then there is a file open
			require("include/pumilio_buttons_partial.php");
			echo "<div class=\"span-24 last\">
				<h5 class=\"highlight2 ui-corner-all\">$soundfile_name</h5>
			</div>";
			}
		?>
		<div class="span-24 last">
			<?php

		echo "<h3>Your settings</h3>
			
			<form name=\"frequencies\" action=\"set_cookies.php\" method=\"post\" class=\"inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"freq_range\">
				Frequency range when opening files:
				<select name=\"min_freq\" class=\"ui-state-default ui-corner-all\">
					<option selected>10</option>
					<option>100</option>
					<option>500</option>
					<option>1000</option>
					<option>2000</option>
					<option>3000</option>
					<option>4000</option>
					<option>5000</option>
					<option>8000</option>
					<option>10000</option>
					<option>12000</option>
				</select> Hz
				to
				<select name=\"max_freq\" class=\"ui-state-default ui-corner-all\">
					<option>300</option>
					<option>500</option>
					<option>1000</option>
					<option>2000</option>
					<option>3000</option>
					<option>4000</option>
					<option>5000</option>
					<option>8000</option>
					<option selected>10000</option>
					<option>12000</option>
					<option>14000</option>
					<option>16000</option>
					<option>22050</option>
					<option>24000</option>
				</select> Hz 
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<input type=submit value=\"Update\" class=\"fg-button ui-state-default ui-corner-all\">";

			if (isset($_COOKIE["frequency_min"])) {
				$frequency_min=$_COOKIE["frequency_min"];
				$frequency_max=$_COOKIE["frequency_max"];
				echo " | (current: $frequency_min - $frequency_max Hz)";
				}

			echo "
			</form>

			<form name=\"fft\" action=\"set_cookies.php\" method=\"post\" class=\"inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"fft\">
				FFT size:
				<select name=\"fft\" class=\"ui-state-default ui-corner-all\">
					<option>256</option>
					<option>512</option>
					<option>1024</option>
					<option selected>2048</option>
					<option>4096</option>
				</select> 
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<input type=submit value=\"Update\" class=\"fg-button ui-state-default ui-corner-all\">";

			if (isset($_COOKIE["fft"])) {
				$fft=$_COOKIE["fft"];
				echo " | (current: $fft)";
				}
			echo "
			</form>

			<form name=\"palette\" action=\"set_cookies.php\" method=\"post\" class=\"inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"palette\">
				Color palette to use in spectrograms:
				<select name=\"palette\" class=\"ui-state-default ui-corner-all\">
					<option value=\"2\">white background</option>
					<option value=\"1\">black background</option>
				</select> 
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<input type=submit value=\"Update\" class=\"fg-button ui-state-default ui-corner-all\">";

			if (isset($_COOKIE["palette"])) {
				$palette=$_COOKIE["palette"];
				if ($palette==1)
					echo " | (current: black background)";
				if ($palette==2)
					echo " | (current: white background)";
				}

			echo "
			</form>

			<br>
			<form name=\"clear\" action=\"set_cookies.php\" method=\"post\" class=\"inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"clear\">
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<input type=submit value=\"Return settings to their defaults\" class=\"fg-button ui-state-default ui-corner-all\">
				</form>

			<br>
			<hr noshade>\n";
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
