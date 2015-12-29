<?php
session_start();

#ignore_user_abort(true);
set_time_limit(0);

#Calculate time to complete
$Time0=strtotime("now");

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");
$force_admin = TRUE;
require("include/check_admin.php");

$query = "SELECT * from Sounds WHERE SoundStatus!='9' ORDER BY RAND()";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
$no_sounds = mysqli_num_rows($result);

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>$app_custom_name</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");

#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	

?>

</head>
<body>

	<!--Bootstrap container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>

		<hr noshade>
		

			<?php

			echo "<h3>Working</h3>
			<p><strong>To cancel just click stop in your browser window.</strong>";

			flush();
			
			#ALL CHECKS OK
			
			echo "
			<div id=\"please_wait\"><p>Please wait, working... <img src=\"images/wait18trans.gif\"></p></div>";
			
			echo "<iframe src=\"include/progressbar.php?per=1\" width=\"100%\" height=\"30\" frameborder=\"0\" id=\"progress_bar\" scrolling=\"no\"></iframe>&nbsp;
			<div id=\"progress_counter\"><strong>0 of $no_sounds checked</strong></div>
			";

			require("include/generate.php");

			flush();
			
		
require("include/bottom.php");
?>

</body>
</html>