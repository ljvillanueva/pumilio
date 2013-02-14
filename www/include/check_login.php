<?php

if ($login_wordpress == TRUE){
	if (is_user_logged_in()==FALSE){
			header("Location: error.php?e=login");
			die();
		}
	}
else {
	if (!sessionAuthenticate($connection)) {
		header("Location: error.php?e=login");
		die();
		}
	}

?>
