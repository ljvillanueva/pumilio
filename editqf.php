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

#Sanitize
$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$newqf=filter_var($_GET["newqf"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name</title>";

#Get CSS
require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
?>

</head>
<body>

<div style="padding: 10px;">

<?php
$a = query_one("UPDATE Sounds SET QualityFlagID='$newqf' WHERE SoundID='$SoundID'", $connection);

echo "<div class=\"success\">Quality Flag changed for this file. $a</div>";

?>

<br><p><a href="#" onClick="opener.location.reload();window.close();">Close window.</a>

</div>

</body>
</html>
