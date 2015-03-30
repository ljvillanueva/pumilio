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

if (!$allow_upload || !sessionAuthenticate($connection)) {
	header("Location: error.php?e=login");
	die();
	}
	
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Upload file</title>";

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
					required: true
				},
				fileid: {
					required: true
				}
			},
			messages: {
				file: "Please select a file",
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

			<h3>Upload a sound file:</h3>

			<form enctype="multipart/form-data" action="fileupload2.php" method="post" id="fileForm">
				<input name="userfile" type="file" id="file" class="fg-button ui-state-default ui-corner-all">&nbsp;&nbsp;

				<?php
					$max=ini_get("upload_max_filesize");
					echo "<span class=\"notice\">Maximum file size is $max</span>";
				?>

				<br>
				Unique ID of the file: <input type="text" name="fileID" id="fileid" value="101" size="6" class="fg-button ui-state-default ui-corner-all"><br>
				<input type="submit" value=" Upload " class="fg-button ui-state-default ui-corner-all"> <br>
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
