<?php
header( 'Content-type: text/html; charset=utf-8' );

$e=filter_var($_GET["e"], FILTER_SANITIZE_STRING);

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>\n";

if ($e=="maint") {
	echo "<title>Pumilio - Maintenance Mode</title>";
	}
else {
	echo "<title>Pumilio - Error</title>";
	}


?>

<!-- Bootstrap core CSS -->
<link href="libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap theme -->
<link href="libs/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

<link href="libs/bootstrap/css/sticky-footer-navbar.css" rel="stylesheet">

<!-- JQuery -->
<link type="text/css" href="libs/jquery-ui/jquery-ui.min.css" rel="stylesheet">


<link type="text/css" href="js/jquery/jquery.custom.css" rel="stylesheet">
<link type="text/css" href="js/jquery/jquery.css.custom.css" rel="stylesheet">

<!-- font-awesome -->
<link rel="stylesheet" href="libs/font-awesome/css/font-awesome.min.css">


<!-- Scripts for JQuery -->
	<script type="text/javascript" src="js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.10.4.custom.min.js"></script>
	<script type="text/javascript" src="js/jquery.fg-button.js"></script>

<link rel="stylesheet" href="css/custom.css" type="text/css" media="screen, projection">
 

<!-- Tooltips-->
	<script>
		$(function() {
			$( document ).tooltip();
		});
	</script>

<?php
echo "</head>\n
	<body>


	<!--Blueprint container-->
	<div class=\"container\">";

#		require("include/topbar.php");
		

	echo "<a href=\"index.php\"><img src=\"images/logo2.png\"></a>";

		$config_file = 'config.php';


		if ($e=="db") {
			echo "<p><div class=\"alert alert-danger\"><h3> <span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> The system could not connect to the database. Please verify the settings and try again.</h3></div>";
			}
		elseif ($e=="login") {
			echo "<p><div class=\"alert alert-danger\"><h3> <span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> The requested area requires a valid user account. Please log in and try again.</h3></div>";
			}
		elseif ($e=="admin") {
			echo "<p><div class=\"alert alert-danger\"><h3> <span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> The requested area requires an administrative account.</h3></div>";
			}
		elseif ($e=="config") {
			echo "<p><div class=\"alert alert-danger\"><h3> <span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> The configuration file (<strong>config.php</strong>) could not be found. Please rename the file <strong>config.php.dist</strong> to <strong>config.php</strong> and fill the appropiate values.</h3></div>";
			}
		elseif ($e=="maint") {
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> The system is undergoing maintenance, please try again in a few hours. <br>If you see this message too long, please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.</h3></div>";
			}
		elseif ($e=="noopen") {
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> This installation is not set up to open files. Please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.</h3></div>";
			}
		elseif ($e=="upload") {
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> This installation is not set up to upload files. Please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.</h3></div>";
			}
		elseif ($e=="script") {
			$error_script = $_SERVER['HTTP_REFERER'];
			echo "<p><div class=\"notice\"> <img src=\"images/error.png\"> There was an error in a script or there was an invalid manual request. Please contact the administrator: <a href=\"mailto:$app_admin_email\">$app_admin_email</a>.<br>
				Error from: $error_script</h3></div>";
			}


?>

<hr noshade></div>
		<div class="footer">
			<div class="container">
				<div class="col-lg-8">
					
					Sample Pumilio archive<br><a href="http://creativecommons.org/licenses/by-sa/3.0/" target=_blank><img src="images/cc/CCBY-SA.png" alt="License"></a> CC BY-SA license: 
LJV
<br><br></div>

				<div class="col-lg-4">

					Powered by <a href="http://ljvillanueva.github.io/pumilio" target=_blank title="Website of the Pumilio application">Pumilio</a> v. 3.0.0.dev<br>
					<a href="about.php" title="Copyright information of the application">&copy; 2010-2016 LJV</a>. Licensed under the GPLv3.<br><br></div></div></div>

</body>
</html>