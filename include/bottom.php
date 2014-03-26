<?php
if ($special_wrapper == FALSE && $special_iframe == FALSE){
	echo "<hr noshade>";

	echo "<p style=\"float: left;\">
		<small>$app_custom_name - 
		<a href=\"#\" onClick=\"window.open('include/contact.php', 'admins', 'width=650,height=400,status=yes,resizable=yes,scrollbars=yes')\" title=\"Contact information\">Contact</a><br>";
		
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
	echo "</small>";
	
	require("include/version.php");

	echo "<p class=\"right\">
		<small>Powered by <a href=\"http://ljvillanueva.github.io/pumilio\" target=_blank title=\"Website of the Pumilio application\">Pumilio</a> v. $website_version<br>
		<a href=\"#\" onClick=\"window.open('include/copyright.php', 'copyright', 'width=650,height=400,status=yes,resizable=yes,scrollbars=yes')\" title=\"Copyright information of the application\">&copy; 2010-2014 LJV</a>. Licensed under the GPLv3.</small>";
	}

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
?>