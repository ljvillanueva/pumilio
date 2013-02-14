<?php

require("../../include/functions.php");
require("../../config.php");
require("../../include/apply_config.php");
?>

<html>
<head>

<title>Pumilio</title>

<link rel="stylesheet" href="../../css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../../css/print.css" type="text/css" media="print">	
<!--[if IE]><link rel="stylesheet" href="../../css/ie.css" type="text/css" media="screen, projection"><![endif]-->


<!-- Scripts for JQuery -->
	<script src="../../js/jquery-1.3.2.min.js"></script>
	<link type="text/css" href="../../js/jquery/start/jquery-ui-1.7.3.custom.css" rel="stylesheet" />	
	<script type="text/javascript" src="../../js/jquery/jquery-ui-1.7.3.custom.min.js"></script>
	<script src="../../js/jquery.fg-button.js"></script>

</head>
<body onblur="window.focus();">

<div style="padding: 10px;">

<?php

$t_min=filter_var($_POST["t_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$t_max=filter_var($_POST["t_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_min=filter_var($_POST["f_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_max=filter_var($_POST["f_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$SoundID=filter_var($_POST["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$from_db=filter_var($_POST["from_db"], FILTER_SANITIZE_STRING_INT);

if ($mark_tag_name=="")
	{die("<p class=\"error\">Please give the variable mark_tag_name in the config.php file a name.
			<p><a href=\"#\" onClick=\"window.close();\">Close window.</a>
			</body>
			</html>");}


if ($t_min=="")
	die("<p class=\"error\">Please select an area of the spectrogram first.");

	if ($login_wordpress == TRUE){
		if (is_user_logged_in()==TRUE){
				$logged_in=TRUE;
			}
		}
	else {
		if (sessionAuthenticate($connection)) {
			$logged_in=TRUE;
			}
		}


	if ($logged_in==TRUE) {
		echo "$SoundID
		<form method=\"POST\" action=\"add2.php\">

			<input type=\"hidden\" name=\"t_min\" value=\"$t_min\"  />
			<input type=\"hidden\" name=\"t_max\" value=\"$t_max\"  />
			<input type=\"hidden\" name=\"f_min\" value=\"$f_min\"  />
			<input type=\"hidden\" name=\"f_max\" value=\"$f_max\" />
			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\" />
			<input type=\"hidden\" name=\"from_db\" value=\"$from_db\" />

			<h3>Selection:</h3>
			<p>Frequency range: $f_min - $f_max Hz
			<p>Time range: $t_min - $t_max seconds
			<p>$mark_tag_name: <input type=\"text\" name=\"mark_tag\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\" />
			<p><input type=\"button\" id=\"add_submit\" value=\" Insert to database \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\" onClick=\"submit(); document.getElementById('add_submit').disabled = true; document.getElementById('add_submit').value = ' Please wait... ';\" />
			</form>
		";
		}
	else {
		echo "<p>You have to be logged in to use this tool.";
		}

?>

<br><br><p><a href="#" onClick="opener.location.reload();window.close();">Cancel and close window.</a>

</div>

</body>
</html>
