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

#Check if user can edit files (i.e. has admin privileges)
if (!sessionAuthenticate($connection))
		{die();}

#Sanitize
$SampleMembersID=filter_var($_GET["SampleMembersID"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>

<title>$app_custom_name</title>";

#Get CSS
require("include/get_css.php");
require("include/get_jqueryui.php");
?>

</head>
<body>

<div style="padding: 10px;">

<?php

$query = "DELETE FROM SampleMembers WHERE SampleMembersID='$SampleMembersID' LIMIT 1";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
echo "<div class=\"success\">Sound removed from the sample set.</div>";

?>

<br><p><a href="#" onClick="opener.location.reload();window.close();">Close window.</a>

</div>

</body>
</html>