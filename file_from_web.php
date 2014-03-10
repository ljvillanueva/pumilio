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

if (!$allow_upload){
	header("Location: error.php?e=upload");
	die();
	}


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Get a file from the web</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

	<script src="js/jquery.validate.js"></script>

	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#fileForm").validate({
			rules: {
				file: {
					required: true,
					url: true
				},
				fileid: {
					required: true
				}
			},
			messages: {
				file: "Please enter the file web address",
				fileid: "Please enter a number"
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
		<div class="span-24 last">

			<h3>Get a sound file from the web:</h3>

			<p><form action="file_obtain.php" method="get" id="fileForm">
				Address of the file: <input name="file" type="text" id="file" size="40" value="http://" class="fg-button ui-state-default ui-corner-all">
				<p>Assign a numeric ID to the file: <input type="text" name="fileid" id="fileid" class="fg-button ui-state-default ui-corner-all">
				<input type="hidden" name="method" value="2">
				<p><input type="submit" value=" Get the file " class="fg-button ui-state-default ui-corner-all"> <br>
			</form>
			<br>
			<p class="notice">The file has to be in one of these formats:
			
			<?php
				include("include/sox_formats.php");
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
