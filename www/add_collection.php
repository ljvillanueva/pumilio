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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Add Collection</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#EditForm").validate({
			rules: {
				CollectionName: {
					required: true
				},
				MiscURL: {
					url: true
				}
			},
			messages: {
				CollectionName: "Please enter a name for this collection",
				MiscURL: "Please enter an appropriate web address"
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
if ($use_googleanalytics) {
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
			echo "<h3>Add collections to the database</h3>
			<form action=\"add_collection2.php\" method=\"POST\" id=\"EditForm\">
				<p>Name of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"CollectionName\" maxlength=\"100\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
				Author of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"Author\" maxlength=\"80\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
				Media of the collection: &nbsp;&nbsp;
				<select name=\"FilesSource\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">
					<option></option>
					<option>Field Recording</option>
					<option>Book with CD</option>
					<option>Automated Audio Logger</option>
					<option>Audio CD</option>
					<option>CD-ROM</option>
					<option>DVD</option>
					<option>Tape</option>
					<option>LP</option>
					<option>Internet</option>
					<option>Donation</option>
					<option>Other</option>
				</select><br>
				Full citation of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"CollectionFullCitation\" maxlength=\"250\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
				Website: &nbsp;&nbsp;<input type=\"text\" name=\"MiscURL\" maxlength=\"250\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
				Notes of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"Notes\" maxlength=\"255\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>&nbsp;&nbsp;

				<input type=submit value=\" Add Collection \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
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
