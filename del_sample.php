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

$force_admin = TRUE;
require("include/check_admin.php");

$SampleID=filter_var($_POST["SampleID"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
	<html>
	<head>
<title>$app_custom_name - Delete sample</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");


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
			$query = "DELETE FROM SampleMembers WHERE SampleID='$SampleID'";
			$result=query_several($query, $connection);	
			$query = "DELETE FROM Samples WHERE SampleID='$SampleID' LIMIT 1";
			$result=query_several($query, $connection);	

			echo "<p><div class=\"success\">The sample was deleted.</div>
			<p><a href=\"./\">Return to the homepage</a>";
				
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
