<?php
session_start();

echo "<html>
<head>
";

require("include/functions.php");

$config_file = 'config.php';
if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");
require("include/get_css.php");
require("include/get_jqueryui.php");

$percent=$_GET["per"];

if ($percent<100) {
	echo "
	<style type=\"text/css\">
		.ui-progressbar-value { background-image: url(/js/jquery/start/images/pbar-ani.gif); }
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
