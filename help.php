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

if ($topic == "GoogleMaps"){
	$help_title="Obtaining a Google Maps API key";
	$help_text = "The current version of Pumilio uses the Google Maps v2 API. You will need a key to 
			use the maps. To obtain a key:
			<ul>
				<li>Go to https://code.google.com/apis/console
				<li>Log in to your Google account, if you are not logged in already
				<li>Click on Services
				<li>Browse down to \"Google Maps API v2\" and turn ON
				<li>Click on API Services
				<li>Click on \"Create new Browser key...\"
				<li>Add the URL of the webserver, in the following format: servername.com/*
				<li>Copy the API Key that is generated to the corresponding field in the Pumilio Admin page
			</ul>";
	}
elseif ($topic == "GoogleMaps3"){
	$help_title="Obtaining a Google Maps API key";
	$help_text = "The new version of Pumilio uses the Google Maps v3 API. You will need a key to 
			use the maps. To obtain a key:
			<ul>
				<li>Go to https://code.google.com/apis/console
				<li>Log in to your Google account, if you are not logged in already
				<li>Click on Services
				<li>Browse down to \"Google Maps API v3\" and turn ON
				<li>Click on API Services
				<li>Click on \"Create new Browser key...\"
				<li>Add the URL of the webserver, in the following format: servername.com/*
				<li>Copy the API Key that is generated to the corresponding field in the Pumilio Admin page
			</ul>";
	}
elseif ($topic == "R"){
	$help_title="Using R";
	$help_text = "Pumilio can use several R packages to extract data and indices from sound files. 
		If you select this option, R needs to be installed.";
	}

echo "<h3>$help_title</h3>";

echo "<p>$help_text";

echo "<br><br><p><a href=\"#\" onClick=\"window.close();\">Close window</a>";

?>
</div>
</body>
</html>
