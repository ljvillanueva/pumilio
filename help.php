<?php

$config_file = 'config.php';

if (file_exists($config_file)) {
    require($config_file);
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/functions.php");
require("include/apply_config.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>";

require("include/get_css.php");
require("include/get_jqueryui.php");

#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
?>
	
</head>
<body>

<div style="padding: 10px;">

<?php

$topic=filter_var($_GET["topic"], FILTER_SANITIZE_STRING);

if ($topic == "GoogleMaps3"){
	$help_title = "Obtaining a Google Maps API key";
	$help_text = "The new version of Pumilio uses the Google Maps v3 API. You will need a key to 
			use the maps. To obtain a key follow <a href=\"https://developers.google.com/maps/documentation/javascript/tutorial\">this tutorial</a> or these steps:
			<ul>
				<li>Go to https://code.google.com/apis/console
				<li>Log in to your Google account, if you are not logged in already
				<li>Click on Services
				<li>Browse down to \"Google Maps API v3\" and turn ON
				<li>Click on API Services
				<li>Click on \"Create new Browser key...\"
				<li>Add the URL of the webserver, in the following format: servername.com/*
				<li>Copy the API Key that is generated to the corresponding field in the Pumilio Admin page
			</ul>
			<strong>Note</strong>: The Developers Console has been recently updated, follow these steps for the new version:
			<ul>
				<li>Go to https://code.google.com/apis/console
				<li>Log in to your Google account, if you are not logged in already
				<li>Click on \"APIs & Auth\" in the left-side menu
				<li>Click on \"APIs\"
				<li>Browse down to \"Google Maps JavaScript API v3\" and turn ON
				<li>Click on \"Credentials\"
				<li>Click on \"Create New Key\"
				<li>Click on \"Browser key\"
				<li>Add the URL of the webserver, in the following format: servername.com/*
				<li>Copy the API Key that is generated to the corresponding field in the Pumilio Admin page
			</ul>	
			";
	}
elseif ($topic == "tempdir"){
	$help_title = "Local directory for adding multiple files";
	$help_text = "Users can add files to the archive that are stored in the server or a network location mounted in the server. Add the full system path 
				in this field. The path needs to exist and be readable by the Apache user (";
				echo exec('whoami');
				echo ")";
	}
elseif ($topic == "R"){
	$help_title = "Using R";
	$help_text = "Pumilio can use several R packages to extract data and indices from sound files. 
		If you select this option, R needs to be installed.";
	}
elseif ($topic == "GoogleAnalytics"){
	$help_title = "Using Google Analytics";
	$help_text = "<p>Pumilio can add the necessary code for tracking the visitors to the website using the Google Analytics system.
		You will need to create an account on http://www.google.com/analytics/

		<p>Once you have an account, then you can add a website to track as a \"Property\". This property will have under \"Property Settings\"
			a Tracking ID code, which looks like this: UA-000000-01.
		<p>For more details check this <a href=\"https://support.google.com/analytics/answer/1008080?hl=en\" target=_blank>help page (https://support.google.com/analytics/answer/1008080?hl=en)</a>.
			";
	}


echo "<h3>$help_title</h3>";

echo "<p>$help_text";

echo "<br><br><p><a href=\"#\" onClick=\"window.close();\">Close window</a>";

?>
</div>
</body>
</html>