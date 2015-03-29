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
<title>$app_custom_name - Add from a database or spreadsheet</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

#Call particular scripts from include, post to same with variable 'step' to indicate which

if (isset($_POST["step"])){
	$step = filter_var($_POST["step"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$step = 1;
	}

if ($step > 5){
	header("Location: error.php?e=script");
	die();
	}

require("include/add_from_field$step.php");
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
