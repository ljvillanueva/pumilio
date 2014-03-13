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
	$soundfile_format = query_one("SELECT soundfile_format FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_duration = query_one("SELECT soundfile_duration FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_name = query_one("SELECT soundfile_name FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_id = query_one("SELECT soundfile_id FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$no_channels = query_one("SELECT no_channels FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_samplingrate = query_one("SELECT soundfile_samplingrate FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	}
else{	
	die("File not found.");
	}


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Convert File</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<!-- JQuery Tabs -->
<script type="text/javascript">
	$(function() {
		$("#tabs0").tabs();
	});
	</script>

<?php
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
		echo "<div class=\"span-7\">
			<a href=\"$app_dir\"><img src=\"$app_logo\"></a>
		</div>
		<div class=\"span-10\">
			<h5 class=\"highlight2 ui-corner-all\">$soundfile_name</h5>
		</div>
		<div class=\"span-7 last\">";
			require("include/toplogin.php");
		echo "</div>";

		require("include/pumilio_buttons_partial.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		
		<div class="span-24 last">
			<?php
				require('include/processor_convert.php');
			?>
			<br>
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
