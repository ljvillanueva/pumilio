<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

$force_admin = TRUE;
require("check_admin.php");

echo "
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");
?>

<meta http-equiv="refresh" content="1;url=checkdb2.php">

</head>
<body>

<div style="padding: 10px;">

	<br><br><br>
	<h3>Working... 
	<br>Please wait...
	<img src="../images/wait20trans.gif">
	</h3>

	<br><br><br>
<br><p><a href="#" onClick="window.close();">Cancel and close window</a>

</div>

</body>
</html>
