<?php

if(isset($_COOKIE["jquerycss"])) {
	$jquerycss = $_COOKIE["jquerycss"];
	echo "\n<!-- JQuery -->
	<link type=\"text/css\" href=\"$app_url/js/jquery/$jquerycss/jquery-ui-1.7.3.custom.css\" rel=\"stylesheet\">\n";
	}
else {
	echo "\n<!-- JQuery -->
	<link type=\"text/css\" href=\"$app_url/js/jquery/cupertino/jquery-ui-1.7.3.custom.css\" rel=\"stylesheet\">\n";
	}

#Custom
echo "\n<link type=\"text/css\" href=\"$app_url/js/jquery/jquery.custom.css\" rel=\"stylesheet\">
<link type=\"text/css\" href=\"$app_url/js/jquery/jquery.css.custom.css\" rel=\"stylesheet\">

<!-- Blueprint -->
<link rel=\"stylesheet\" href=\"$app_url/css/screen.css\" type=\"text/css\" media=\"screen, projection\">
<link rel=\"stylesheet\" href=\"$app_url/css/print.css\" type=\"text/css\" media=\"print\">	
<!--[if IE]><link rel=\"stylesheet\" href=\"$app_url/css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://fonts.googleapis.com/css?family=Ubuntu\">\n";
?>