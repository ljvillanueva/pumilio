<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>";

require("get_css_include.php");
require("get_jqueryui_include.php");

echo "
</head>
<body>\n";

$scriptname=filter_var($_POST["scriptname"], FILTER_SANITIZE_STRING);
$scriptver=filter_var($_POST["scriptver"], FILTER_SANITIZE_STRING);
$Lang=filter_var($_POST["Lang"], FILTER_SANITIZE_STRING);
#$script=$_POST["script"];
$script=str_replace("'", "\'", $_POST["script"]);
$script=str_replace("\\n", "\\\\n", $script);
$script=str_replace("\\t", "\\\\t", $script);
$script=str_replace("\\%", "\\\\%", $script);

#Check that it does not exist already for this sound
	$result=query_several("SELECT ScriptName FROM Scripts WHERE ScriptName='$scriptname'", $connection);
	$nrows = mysqli_num_rows($result);
	if ($nrows==0) {			
		$query_script = "INSERT INTO Scripts (ScriptName, Language, Script, ScriptVersion) 
				VALUES ('$scriptname', '$Lang', '$script', '$scriptver')";
		
		$result_script = mysqli_query($connection, $query_script)
			or die (mysqli_error($connection));
		echo "<div class=\"success\">The script was added to the database.</div>";
		}
	else {
		echo "<div class=\"error\">There is a script with that name already. Please try again.</div>";
		}

?>
<br><br><p><a href="#" onClick="opener.location.reload();window.close();">Close window.</a>

</body>
</html>
