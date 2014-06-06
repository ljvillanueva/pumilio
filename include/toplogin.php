<?php

$self = $_SERVER['PHP_SELF'];
$q = $_SERVER['QUERY_STRING'];

$q_logout = str_replace("&", "%", $q);

if (isset($_GET["e"])){
	$e = filter_var($_GET["e"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$e = FALSE;
	}

echo "<p style=\"text-align: right;\"><small>";

if (!isset($no_login)){
	$no_login = FALSE;
	}

if ($no_login == TRUE) {
	echo "[<a href=\"index.php\">Home</a>] 
			[<a href=\"search.php\" title=\"Search soundfiles\">Search</a>] ";
	}
else {
	if ($login_wordpress == TRUE){
		if (is_user_logged_in() == TRUE){
			require_once($wordpress_require);
			wp_get_current_user();

			$username = $current_user->user_login;
		
			echo "Logged as <a href=\"edit_myinfo.php\" title=\"Edit my information or change password\">$username</a> 
				[<a href=\"index.php\" title=\"Home of the application\">Home</a>] 
				[<a href=\"search.php\" title=\"Search soundfiles\">Search</a>] ";

			#Check if user can enter admin area
			if (is_super_admin()) {
				$this_page = basename($_SERVER['PHP_SELF']);
				if ($this_page != "admin.php"){
					echo " [<a href=\"admin.php\" title=\"Administration menu\">Admin</a>]";
					}
				else {
					echo " [Admin]";
					}
				}

			$logout_url = wp_logout_url( "$self?$q" );
			echo " [<a href=\"$logout_url\">Logout</a>]";
			
			include("include/check_system.php");
			}
		else {
			$login_url = wp_login_url( "$self?$q" );
			echo "[<a href=\"index.php\" title=\"Home of the application\">Home</a>] 
				[<a href=\"$login_url\" title=\"Login as a user\">Login</a>]
				[<a href=\"search.php\" title=\"Search soundfiles\">Search</a>]</small>";
			$notlogged = TRUE;
			}
		}
	else{
		if (sessionAuthenticate($connection)) {
			$username = $_COOKIE["username"];
			echo "Logged as <a href=\"edit_myinfo.php\" title=\"Edit my information or change password\">$username</a> 
				[<a href=\"index.php\" title=\"Home of the application\">Home</a>] 
				[<a href=\"search.php\" title=\"Search soundfiles\">Search</a>] ";

			#Check if user can enter admin area
			$username = $_COOKIE["username"];
			if (is_user_admin2($username, $connection)) {
				$this_page = basename($_SERVER['PHP_SELF']);
				if ($this_page != "admin.php"){
					echo " [<a href=\"admin.php\" title=\"Administration menu\">Admin</a>]";
					}
				else {
					echo " [Admin]";
					}
				}

			echo " [<a href=\"include/logout.php?where_to=$self&q=$q_logout\">Logout</a>]";
			
			include("include/check_system.php");
			}
		else {
			echo "[<a href=\"index.php\" title=\"Home of the application\">Home</a>] 
				[<a href=\"search.php\" title=\"Search soundfiles\">Search</a>]<br>";
			
			if ($e == 1) {
				echo "<div class=\"error\"><small>Invalid username or password, try again or <a href=\"recover_password.php\">recover password</a></small>";
				}

			echo "<form action=\"include/login.php\" method=\"POST\" style=\"text-align: right;\">
				<input name=\"username\" type=\"text\" size=8 class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">	
				<input name=\"password\" type=\"password\" size=8 class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
				<input name=\"where_to\" type=\"hidden\" value=\"$self?$q\">
				<input type=\"submit\" value=\"Log in\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\"></form>";
			if ($e == 1) {
				echo "</div>";
				}
			
			$notlogged = TRUE;
			}
		}
	if ($force_login == TRUE && $notlogged == TRUE){
		echo "<br><br><p><strong>You must be logged in to see this site.</strong></p>
			</div>
			</div>
			</body>
			</html>";
		die();
		}
	}

echo "</small>\n";

?>