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
		
echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name - Generate preview files</title>";

require("include/get_css3.php");
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

	<!--Bootstrap container-->
	<div class="container">
		<?php
			require("include/topbar.php");


			echo "<h2>Generate preview sound and image files</h2>

			<div class=\"row\">
				<div class=\"col-lg-6\">";

				$no_sounds=query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9'", $connection);
				echo "<p>Use this link to generate the preview files for each sound file in the database. <br><br>";

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
				echo "<button type=\"submit\" class=\"btn btn-primary\"> Generate preview files </button>
						</form>
						<br><br>";



				echo "</div><div class=\"col-lg-6\">
					<div class=\"alert alert-warning\">Please note that this task may take some
					time and computer power. To cancel, just click stop in the browser.</div></div></div>";


					}
			else {
				echo "There are no sounds in the archive.";
					}




require("include/bottom.php");
?>

</body>
</html>
