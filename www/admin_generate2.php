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

require("include/check_admin.php");

$code=filter_var($_POST["code"], FILTER_SANITIZE_STRING);

$query = "SELECT * from Sounds WHERE SoundStatus!='9' ORDER BY RAND()";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
$no_sounds = mysqli_num_rows($result);

if ($no_sounds!=$code)
	die("There was an error or the databased was changed while you were trying to execute this command. Please go back and try again.");

echo "
<html>
<head>

<title>$app_custom_name</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<?php
#if ($use_googleanalytics)
#	{echo $googleanalytics_code;}
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php

			echo "<h3>Working</h3>
			<p><strong>To cancel just click stop in your browser window.</strong>";

			flush();
			
			#ALL CHECKS OK
			
			echo "
			<div id=\"please_wait\"><p>Please wait, working... <img src=\"images/wait18trans.gif\"></p></div>";
			
			echo "<iframe src=\"progressbar.php?per=1\" width=\"100%\" height=\"30\" frameborder=\"0\" id=\"progress_bar\" scrolling=\"no\"></iframe>&nbsp;
			<div id=\"progress_counter\"><strong>0 of $no_sounds checked</strong></div>
			</div>
			<div class=\"span-24 last\">";

			require("include/generate.php");

			flush();
			
			?>

		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>

