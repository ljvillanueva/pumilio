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

echo "<!DOCTYPE html>
<html>
<head>

<title>$app_custom_name - Settings Page</title>";

require("include/get_css3.php");
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
		
	
		if ($soundfile_name!="") {
			//If its not empty, then there is a file open
			#require("include/pumilio_buttons_partial.php");
			#echo "<h5 class=\"highlight2 ui-corner-all\">$soundfile_name</h5>";
			}

		echo "<div class=\"row\">
			<div class=\"col-md-8\">";


		echo "<div class=\"panel panel-primary\">
            <div class=\"panel-heading\">
              <h3 class=\"panel-title\">$soundfile_name</h3>
            </div>
            <div class=\"panel-body\">";


		echo "<form method=\"GET\" action=\"pumilio.php\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" title=\"Return to file\" class=\"btn btn-sm btn-primary\">Return to file</button> 
		</form>\n";

		echo "<h3>Your settings</h3>
			
			<form name=\"frequencies\" action=\"set_cookies.php\" method=\"post\" class=\"form-inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"freq_range\">
				Frequency range when opening files:
				<select name=\"min_freq\" class=\"form-control\">
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
				<select name=\"max_freq\" class=\"form-control\">
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
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">&nbsp;&nbsp;
				<button type=\"submit\" title=\"Update settings\" class=\"btn btn-sm btn-primary\">Update</button> 
				";

			if (isset($_COOKIE["frequency_min"])) {
				$frequency_min=$_COOKIE["frequency_min"];
				$frequency_max=$_COOKIE["frequency_max"];
				echo " | (current: $frequency_min - $frequency_max Hz)";
				}

			echo "
			</form>

			<form name=\"fft\" action=\"set_cookies.php\" method=\"post\" class=\"form-inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"fft\">
				FFT size:
				<select name=\"fft\" class=\"form-control\">
					<option>256</option>
					<option>512</option>
					<option>1024</option>
					<option selected>2048</option>
					<option>4096</option>
				</select> 
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				&nbsp;&nbsp;
				<button type=\"submit\" title=\"Update settings\" class=\"btn btn-sm btn-primary\">Update</button>";

			if (isset($_COOKIE["fft"])) {
				$fft=$_COOKIE["fft"];
				echo " | (current: $fft)";
				}
			echo "
			</form>

			<form name=\"palette\" action=\"set_cookies.php\" method=\"post\" class=\"form-inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"palette\">
				Color palette to use in spectrograms:
				<select name=\"palette\" class=\"form-control\">
					<option value=\"2\">white background</option>
					<option value=\"1\">black background</option>
				</select> 
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				&nbsp;&nbsp;
				<button type=\"submit\" title=\"Update settings\" class=\"btn btn-sm btn-primary\">Update</button>";

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
			<form name=\"clear\" action=\"set_cookies.php\" method=\"post\" class=\"form-inline\">
				<input type=\"hidden\" name=\"cookie_to_set\" value=\"clear\">
				<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				<button type=\"submit\" title=\"Return settings to their defaults\" class=\"btn btn-sm btn-primary\">Return settings to their defaults</button>
				</form>

			</div></div>

			<div class=\"col-md-4\">&nbsp;</div></div>";
		
require("include/bottom.php");
?>

</body>
</html>
