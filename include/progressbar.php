<?php

require("functions.php");
require("../config.php");
require("apply_config_include.php");

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
";

require("get_css3_include.php");
require("get_jqueryui_include.php");

$percent=round(filter_var($_GET["per"], FILTER_SANITIZE_NUMBER_INT));

echo "
	</head>
	<body>
	
	<div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"$percent\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: $percent%;\"><span class=\"sr-only\">$percent% Complete</span></div>";


?>
</body>
</html>