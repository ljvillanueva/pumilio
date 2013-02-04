<?php

$self=$_SERVER['PHP_SELF'];
$q=$_SERVER['QUERY_STRING'];

$q_logout = str_replace("&", "%", $q);

echo "<p style=\"text-align: right;\">";
	
if ($no_login==TRUE) {
	echo "&nbsp;";
	}
else {
	if (sessionAuthenticate($connection)) {
		$username = $_COOKIE["username"];
		echo "<small>Logged as <a href=\"edit_myinfo.php\" title=\"Edit my information or change password\">$username</a> 
			[<a href=\"search.php\">Search</a>] ";

		#Check if user can enter admin area
		$username = $_COOKIE["username"];
		if (is_user_admin($username, $connection)) {
			$this_page = basename($_SERVER['PHP_SELF']);
			if ($this_page != "admin.php"){
				echo " [<a href=\"admin.php\">Admin</a>]";
				}
			else {
				echo " [Admin]";
				}
			}

		echo " [<a href=\"include/logout.php?where_to=$self&q=$q_logout\">Logout</a>]";
			
		include("include/check_system.php");
		echo "</small>";
		}
	else {
		echo "<small>[<a href=\"search.php\">Search</a>]<br>";
		$e=filter_var($_GET["e"], FILTER_SANITIZE_NUMBER_INT);

		if ($e==1) {
			echo "<div class=\"error\"><small>Invalid username or password, try again or <a href=\"recover_password.php\">recover password</a></small>";
			}

		echo "<form action=\"include/login.php\" method=\"POST\" style=\"text-align: right;\">
			<input name=\"username\" type=\"text\" size=8 class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">	
			<input name=\"password\" type=\"password\" size=8 class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
			<input name=\"where_to\" type=\"hidden\" value=\"$self?$q\">
			<input type=\"submit\" value=\"Log in\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\"></form>";
		if ($e==1) {
			echo "</div>";
			}
		echo "</small>";
		}
	}

?>