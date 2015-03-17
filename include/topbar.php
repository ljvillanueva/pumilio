<?php

echo " <!-- Fixed navbar -->
    <nav class=\"navbar navbar-inverse navbar-default\">
      <div class=\"container-fluid\">
        <div class=\"navbar-header\">
          <a class=\"navbar-brand\" href=\"$app_dir\">$app_custom_name</a>
        </div>
        <div id=\"navbar\" class=\"navbar-collapse collapse\">
          <ul class=\"nav navbar-nav\">
            <li><a href=\"$app_dir\"><span class=\"glyphicon glyphicon-home\" aria-hidden=\"true\"></span> Home</a></li>
            <li><a href=\"browse_map.php\"><span class=\"glyphicon glyphicon-map-marker\" aria-hidden=\"true\"></span> Map of Sounds</a></li>
            <li><a href=\"search.php\"><span class=\"glyphicon glyphicon-search\" aria-hidden=\"true\"></span> Search</a></li>

            
            <li><a href=\"about.php\"><span class=\"glyphicon glyphicon-info-sign\" aria-hidden=\"true\"></span> About</a></li>
          </ul>
          <ul class=\"nav navbar-nav navbar-right\">";


	if ($login_wordpress == TRUE){
		if (is_user_logged_in() == TRUE){
			require_once($wordpress_require);
			wp_get_current_user();

			$username = $current_user->user_login;
		
			echo "<li class=\"right\">Logged in as <a href=\"edit_myinfo.php\" title=\"Edit my information or change password\"><span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\"></span> $username</a></li>";

			#Check if user can enter admin area
			if (is_super_admin()) {
				$this_page = basename($_SERVER['PHP_SELF']);
				echo "<li class=\"right\"><a href=\"admin.php\" title=\"Administration menu\"><span class=\"glyphicon glyphicon-wrench\" aria-hidden=\"true\"></span> Admin</a></li>";
				}

			$logout_url = wp_logout_url( "$self?$q" );
			echo "<li class=\"right\"><a href=\"$logout_url\"><span class=\"glyphicon glyphicon-log-out\" aria-hidden=\"true\"></span> Logout</a></li>";
			
			}
		else {
			$login_url = wp_login_url( "$self?$q" );
			echo "<li class=\"right\"><a href=\"$login_url\" title=\"Login as a user\"><span class=\"glyphicon glyphicon-log-in\" aria-hidden=\"true\"></span> Login</a></li>";
			$notlogged = TRUE;
			}
		}
	else{
		if (sessionAuthenticate($connection)) {
			$username = $_COOKIE["username"];
			echo "<li class=\"right\"><a href=\"edit_myinfo.php\" title=\"Edit my information or change password\"><span class=\"glyphicon glyphicon-user\" aria-hidden=\"true\"></span> Logged in as $username</a></li>";

			#Check if user can enter admin area
			$username = $_COOKIE["username"];
			if (is_user_admin2($username, $connection)) {
				$this_page = basename($_SERVER['PHP_SELF']);
				echo "<li class=\"right\"><a href=\"admin.php\" title=\"Administration menu\"><span class=\"glyphicon glyphicon-wrench\" aria-hidden=\"true\"></span> Admin</a></li>";
				}

			echo "<li class=\"right\"><a href=\"include/logout.php?where_to=$self&q=$q_logout\"><span class=\"glyphicon glyphicon-log-out\" aria-hidden=\"true\"></span> Logout</a></li>";
			
			}
		else {
			echo "<li class=\"right\"><a href=\"login.php\" title=\"Login as a user\"><span class=\"glyphicon glyphicon-log-in\" aria-hidden=\"true\"></span> Login</a></li>";
			}
		}
	

      

      echo "</ul>
    </div><!--/.nav-collapse -->
  </div>
</nav>";

?>
