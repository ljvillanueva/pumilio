<?php
session_start();

set_time_limit(0);

require("functions.php");
require("../config.php");
require("apply_config.php");

$force_admin = TRUE;
require("check_admin.php");

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$method=filter_var($_POST["method"], FILTER_SANITIZE_STRING);
$archivefrom=filter_var($_POST["archivefrom"], FILTER_SANITIZE_STRING);

echo "
<html>
<head>

<meta http-equiv=\"refresh\" content=\"1;url=tar_data_complete.php?ColID=$ColID&SiteID=$SiteID&method=$method&archivefrom=$archivefrom\">

<title>Pumilio - Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px; text-align:center;">

	<br><br><br><br><br>
	<h3>Working... 
	<br>Please wait...
	<img src="../images/wait20trans.gif">
	</h3>

	<br><br><br><br><br>
	<br><p><a href="#" onClick="window.close();">Cancel and close window</a>
</div>

</body>
</html>
