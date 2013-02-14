<?php

if ($login_wordpress == TRUE){
	if (is_user_logged_in()==TRUE){
		if (!is_super_admin()) {
			header("Location: error.php?e=admin");
			die();
			}
		}
	else{
		header("Location: error.php?e=login");
		die();
		}
	}
else {
	$username = $_COOKIE["username"];

	if (!is_user_admin2($username, $connection)) {
		die("You are not an admin.");
		}
	}

?>
