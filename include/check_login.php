<?php

if ($login_wordpress == TRUE){
	if (is_user_logged_in() == FALSE){
		$pumilio_loggedin = FALSE;
		}
	else{
		$pumilio_loggedin = TRUE;
		}
	}
else {
	if (!sessionAuthenticate($connection)) {
		#header("Location: error.php?e=login");
		#die();
		$pumilio_loggedin = FALSE;
		}
	else{
		$pumilio_loggedin = TRUE;
		}
	}


if (!isset($force_loggedin)){
	$force_loggedin = FALSE;
	}

if ($force_loggedin == TRUE && $pumilio_loggedin == FALSE){
	header("Location: error.php?e=login");
	die();
	}

?>
