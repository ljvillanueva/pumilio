<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

set_time_limit(0);

#Check if user can edit files (i.e. has admin privileges)
	$username = $_COOKIE["username"];

	if (!is_user_admin2($username, $connection)){
		die();
		}

echo "
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
 require("get_css_include.php");
 require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px;">

<br><br><br>

<?php

require("check_db.php");

echo "<h4><div class=\"success\"><img src=\"../images/accept.png\"> Database fields were updated</div></h4>";

?>

<br><br><br>
<p><a href="#" onClick="window.close();">Close window</a>

</div>

</body>
</html>
