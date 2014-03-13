<?php
$e=filter_var($_GET["e"], FILTER_SANITIZE_STRING);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>\n";

if ($e=="maint") {
	echo "<title>Pumilio - Maintenance Mode</title>";
	}
else {
	echo "<title>Pumilio - Error</title>";
	}

echo "\n<!-- JQuery -->
	<link type=\"text/css\" href=\"css/jqueryui/jquery-ui-1.10.4.custom.min.css\" rel=\"stylesheet\">\n";
	
#Custom
echo "\n<link type=\"text/css\" href=\"js/jquery/jquery.custom.css\" rel=\"stylesheet\">
<link type=\"text/css\" href=\"js/jquery/jquery.css.custom.css\" rel=\"stylesheet\">

<!-- Blueprint -->
<link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">	
<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
<link rel=\"stylesheet\" type=\"text/css\" href=\"http://fonts.googleapis.com/css?family=Ubuntu\">\n";

echo "\n<!-- Scripts for JQuery -->
	<script type=\"text/javascript\" src=\"js/jquery-1.10.2.js\"></script>
	<script type=\"text/javascript\" src=\"js/jquery-ui-1.10.4.custom.min.js\"></script>
	<script type=\"text/javascript\" src=\"js/jquery.fg-button.js\"></script>\n";

#Custom CSS
echo "\n<link rel=\"stylesheet\" href=\"css/custom.css\" type=\"text/css\" media=\"screen, projection\">\n";

#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
?>

</head>
<body>

<!--Blueprint container-->
<div class="container">
	<div class="span-24 last">
		&nbsp;
	</div>
	<div class="span-24 last">
		<?php

		$config_file = 'config.php';

		echo "<a href=\"index.php\"><img src=\"images/logo2.png\"></a>";

		if ($e=="db") {
			echo "<p><div class=\"error\"> <img src=\"images/exclamation.png\"> The system could not connect to the database. Please verify the settings and try again.</div>";
			}
		elseif ($e=="login") {
			echo "<p><div class=\"error\"> <img src=\"images/exclamation.png\"> The requested area requires a valid user account. Please log in and try again.</div>";
			}
		elseif ($e=="admin") {
			echo "<p><div class=\"error\"> <img src=\"images/exclamation.png\"> The requested area requires an administrative account.</div>";
			}
		elseif ($e=="config") {
			echo "<p><div class=\"error\"> <img src=\"images/exclamation.png\"> The configuration file (<strong>config.php</strong>) could not be found. Please rename the file <strong>config.php.dist</strong> to <strong>config.php</strong> and fill the appropiate values.</div>";
			}
		elseif ($e=="maint") {
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> The system is undergoing maintenance, please try again in a few hours. <br>If you see this message too long, please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.</div>";
			}
		elseif ($e=="noopen") {
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> This installation is not set up to open files.
				Please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.</div>";
			}
		elseif ($e=="upload") {
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> This installation is not set up to upload files.
				Please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.</div>";
			}
		elseif ($e=="script") {
			$error_script = $_SERVER['HTTP_REFERER'];
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> There was an error in a script or there was an invalid manual request.
				Please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.<br>
				Error from: $error_script</div>";
			}
		?>

	</div>
	<div class="span-24 last">
		&nbsp;
	</div>
</div>
</body>
</html>
