<?php
session_start();

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - File Details</title>\n";

require("include/get_css3.php");

require("include/get_jqueryui.php");
?>

</head>

<body>

<!--Bootstrap container-->
<div class="container">

		<p>
			<div class="alert alert-info center" role="alert">
      			<h2>Please wait</h2><h3>Loading... <i class="fa fa-cog fa-spin"></i></i></h3>
      		</div>
		</p>

</div>

<?php

header("Location: db_filedetails.php?SoundID=$SoundID");
die();

?>