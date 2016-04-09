<?php
session_start();
header( 'Content-type: text/html; charset=utf-8' );

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: $app_url/error.php?e=config");
    die();
}

require("include/apply_config.php");
#require("include/check_admin.php");


#DB
use \DByte\DB;
DB::$c = $pdo;


echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name - Explore Data</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");

?>

<!-- Tooltips-->
	<script>
		$(function() {
			$( document ).tooltip();
		});
	</script>

<?php

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
echo "</head>\n
	<body>


	<!--container-->
	<div class=\"container\">";

		require("include/topbar.php");
		

	echo "<h2>Explore data</h2>

		<p>This section lets the user explore the data behind the files in the archive.</p>";

		echo "<h4><form method=\"GET\" action=\"qa.php\">
				<button type=\"submit\" class=\"btn btn-primary\"> Figures for Quality Control </button>
				</form>";

		echo "<p><form method=\"GET\" action=\"include/exportsounds.php\" target=\"disk\" onsubmit=\"window.open('', 'disk', 'width=650,height=600,status=yes,resizable=yes,scrollbars=auto')\"><button type=\"submit\" class=\"btn btn-primary\"";
			if (!$pumilio_loggedin){
				echo " title=\"Only logged in users can use this feature\" DISABLED";
				}
			echo "> Open sound export window </button></form>";


		/*echo "<p><strong><a href=\"qc.php\">Data extraction for quality control</a>";*/

require("include/bottom.php");
?>


</body>
</html>