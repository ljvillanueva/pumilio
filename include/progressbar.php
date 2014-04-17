<?php


require("functions.php");
require("../config.php");
require("apply_config_include.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
";

require("get_css_include.php");
require("get_jqueryui_include.php");

$percent=$_GET["per"];

if ($percent<100) {
	echo "
	<style type=\"text/css\">
		.ui-progressbar-value { background-image: url(../js/jquery/start/images/pbar-ani.gif); }
	</style>

	<script type=\"text/javascript\">
	$(function() {
		$(\"#progressbar\").progressbar({
			value: $percent
		});
	});
	</script>
	
	</head>
	<body>";

	echo "<div id=\"progressbar\"></div>";
	}
else {
	echo "\n</head>
	<body>
	<div aria-valuenow=\"100\" aria-valuemax=\"100\" aria-valuemin=\"0\" role=\"progressbar\" class=\"ui-progressbar ui-widget ui-widget-content ui-corner-all\" id=\"progressbar\">
		<div style=\"width: 100%;\" class=\"ui-progressbar-value ui-widget-header ui-corner-all\">
		</div>
	</div>";
	}

?>
</body>
</html>
