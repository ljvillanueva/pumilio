<?php

if ($login_wordpress == TRUE){
	if (is_user_logged_in() == TRUE){
		if (is_super_admin()) {
			$pumilio_admin = TRUE;
			$pumilio_loggedin = TRUE;
			#header("Location: error.php?e=admin");
			#die();
			}
		else{
			$pumilio_admin = FALSE;
			$pumilio_loggedin = TRUE;
			}
		}
	else{
		$pumilio_admin = FALSE;
		$pumilio_loggedin = FALSE;
		}
	}
else {
	$username = $_COOKIE["username"];

	if (is_user_admin2($username, $connection) == TRUE) {
		#die("You are not an admin.");
		$pumilio_admin = TRUE;
		$pumilio_loggedin = TRUE;
		}
	else{
		$pumilio_admin = FALSE;
		$pumilio_loggedin = FALSE;
		}
	}
	
if (!isset($force_loggedin)){
	$force_loggedin = FALSE;
	}
if (!isset($force_admin)){
	$force_admin = FALSE;
	}

if ($force_loggedin == TRUE && $pumilio_loggedin == FALSE){
	header("Location: error.php?e=login");
	die();
	}

if ($force_admin == TRUE && $pumilio_admin == FALSE){
	header("Location: error.php?e=admin");
	die();
	}

?>