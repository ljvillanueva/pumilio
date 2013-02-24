<?php
$e=filter_var($_GET["e"], FILTER_SANITIZE_STRING);

echo "<html>
<head>\n";

if ($e=="maint") {
	echo "<title>Pumilio - Maintenance Mode</title>";
	}
else {
	echo "<title>Pumilio - Error</title>";
	}

require("include/get_css.php");
require("include/get_jqueryui.php");

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

		if (file_exists($config_file)) {
			require("config.php");
			echo "<a href=\"$app_dir\"><img src=\"$app_logo\"></a>";
			}

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
			
		?>

	</div>
	<div class="span-24 last">
		&nbsp;
	</div>
</div>
</body>
</html>
