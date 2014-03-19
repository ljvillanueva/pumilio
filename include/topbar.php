<?php
if (!isset($special_wrapper)){
	$special_wrapper = FALSE;
	}

if (!isset($special_iframe)){
	$special_iframe = FALSE;
	}


if ($special_wrapper == FALSE && $special_iframe == FALSE){

	#Check if custom home link
	if (isset($homelink)){
		$logolink = $homelink;
		}
	else{
		$logolink = $app_dir;
		}


	#Check if custom logo
	if (isset($app_logo)){
		$mainlogo = $app_logo;
		}
	else{
		$mainlogo = "images/logo2.png";
		}


	echo "<div class=\"span-12\">
			<a href=\"$logolink\"><img src=\"$mainlogo\" alt=\"Logo\"></a>
		</div>
		<div class=\"span-12 last\">";
			require("include/toplogin.php");
	echo "</div>";
	}
?>
