<?php

	echo "<hr noshade>";
	echo "</div>";
	echo "
		<div class=\"footer\">
			<div class=\"container\">
				<div class=\"col-lg-8\">
					
					$app_custom_name<br>";
						
					#License
					$files_license = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license'", $connection);
					$files_license_detail = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license_detail'", $connection);
					if ($files_license != ""){
						if ($files_license == "Copyright"){
							echo "&#169; Copyright: ";
							}
						else {
							$files_license_img = str_replace(" ", "", $files_license);
							$files_license_link = strtolower(str_replace("CC ", "", $files_license));
							echo "<a href=\"http://creativecommons.org/licenses/$files_license_link/3.0/\" target=_blank><img src=\"images/cc/$files_license_img.png\" alt=\"License\"></a> $files_license license: ";
							}
						
						if ($files_license_detail != ""){
							echo "\n$files_license_detail\n";
							}
						}
					echo "<br><br>";
					
					require("include/version.php");

				echo "</div>

				<div class=\"col-lg-4\">

					Powered by <a href=\"http://ljvillanueva.github.io/pumilio\" target=_blank title=\"Website of the Pumilio application\">Pumilio</a> v. $website_version<br>
					<a href=\"about.php\" title=\"Copyright information of the application\">&copy; $copyright_years LJV</a>. Licensed under the GPLv3.<br><br>";

			echo "</div></div>";



require("include/check_login.php");
if (isset($_GET["debug"]) && $pumilio_loggedin == TRUE){
	$debug = filter_var($_GET["debug"], FILTER_SANITIZE_NUMBER_INT);
	if ($debug == "1"){
		echo "<br>";
		print_r(DB::$q);
		echo "<br>";
		print_r($settings);
		}
	}

echo "</div>";
?>