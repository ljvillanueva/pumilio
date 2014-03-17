<?php
session_start();

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
		
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Generate preview files</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
	echo $googleanalytics_code;
	}
#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	

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
			<?php
				$no_sounds=query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9'", $connection);
				echo "<h4>Generate preview sound and image files</h4>
				<p>Use this link to generate the preview files for each sound file in the database. 
				<p><div class=\"notice\">Please note that this task may take some
				time and computer power. To cancel, just click stop in the browser.</div>
				<p>";

			if ($no_sounds>0) {

				/*How to check how many processors are available on the current machine.
					by gordon@incero.com http://www.Incero.com. Feel free to use this code,
					but keep this header. June 30th, 2010.*/
				/*
				$numberOfProcessors=`cat /proc/cpuinfo | grep processor | tail -1`;
				$numberOfProcessors=preg_replace('/\s+/', '',$numberOfProcessors);
				$numberOfProcessors=str_replace(":","", $numberOfProcessors);
				$numberOfProcessors=str_replace("processor","", $numberOfProcessors);
				$numberOfProcessors++;
				echo "Number of processors on this server is $numberOfProcessors.";
				*/
				echo "<form action=\"admin_generate2.php\" method=\"POST\">";
				#echo "<input type=\"hidden\" name=\"code\" value=\"$no_sounds\">";

					/*
					echo "Number of processes to run simultaneously: &nbsp;&nbsp;<select name=\"no_processes\" class=\"fg-button ui-state-default ui-corner-all\" >";
						for ($c=0;$c<$numberOfProcessors;$c++) {
							$cc = $c + 1; 
							echo "<option>$cc</option>\n";}

					echo "	</select><br>";

					*/
				echo "&nbsp;&nbsp;&nbsp;<input type=submit value=\" Generate preview files \" class=\"fg-button ui-state-default ui-corner-all\">
						</form>
						<br><br>";
					}
			else {
				echo "There are no sounds in the archive.";
					}

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
