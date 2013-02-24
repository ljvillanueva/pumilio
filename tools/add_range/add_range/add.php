<?php

require("../../include/functions.php");
require("../../config.php");
require("../../include/apply_config.php");

$force_loggedin = TRUE;
require("include/check_login.php");
?>

<html>
<head>

<title>Pumilio</title>

<link rel="stylesheet" href="../../css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../../css/print.css" type="text/css" media="print">	
<!--[if IE]><link rel="stylesheet" href="../../css/ie.css" type="text/css" media="screen, projection"><![endif]-->


<!-- Scripts for JQuery -->
	<script src="../../js/jquery-1.3.2.min.js"></script>
	<link type="text/css" href="../../js/jquery/start/jquery-ui-1.7.2.custom.css" rel="stylesheet" />	
	<script type="text/javascript" src="../../js/jquery/jquery-ui-1.7.2.custom.min.js"></script>
	<script src="../../js/jquery.fg-button.js"></script>

</head>
<body onblur="window.focus();">

<?php

$f_min=filter_var($_POST["f_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_max=filter_var($_POST["f_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);


if ($f_min==""){
	die("<p class=\"error\">Please select an area of the spectrogram first.");
	}

	echo "
	<form method=\"POST\" action=\"add2.php\">
		<input type=\"hidden\" name=\"f_min\" value=\"$f_min\"  />
		<input type=\"hidden\" name=\"f_max\" value=\"$f_max\" />

		<h3>Selection:</h3>
		<p>Frequency range: $f_min - $f_max Hz
		<p>Species: <input type=\"text\" name=\"Species\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\" />
		<p><input type=\"button\" id=\"add_submit\" value=\" Insert to database \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\" onClick=\"submit(); document.getElementById('add_submit').disabled = true; document.getElementById('add_submit').value = ' Please wait... ';\" />
	</form>";

?>

<br><br><p><a href="#" onClick="window.close();">Cancel and close window.</a>

</body>
</html>
