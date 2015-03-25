<?php

echo " <!-- Fixed navbar -->
    <nav class=\"navbar navbar-inverse navbar-default\">
      <div class=\"container-fluid\">
        <div class=\"navbar-header\">
          <a class=\"navbar-brand\" href=\"$app_dir\"><span class=\"glyphicon glyphicon-home\"></span> $app_custom_name</a>
        </div>
        <div id=\"navbar\" class=\"navbar-collapse collapse\">
          <ul class=\"nav navbar-nav\">
            <li><a href=\"map.php\"><span class=\"glyphicon glyphicon-map-marker\"></span> Map of Sounds</a></li>
            <li><a href=\"about.php\"><span class=\"glyphicon glyphicon-info-sign\"></span> About</a></li>
          </ul>
          <ul class=\"nav navbar-nav navbar-right\">\n";


	if ($login_wordpress == TRUE){
		if (is_user_logged_in() == TRUE){
			require_once($wordpress_require);
			wp_get_current_user();

			$username = $current_user->user_login;
		
			echo "<li><a href=\"edit_myinfo.php\" title=\"Edit my information or change password\"><span class=\"glyphicon glyphicon-user\"></span> $username</a></li>";

			#Check if user can enter admin area
			if (is_super_admin()) {
				echo "<li><a href=\"admin.php\" title=\"Administration menu\"><span class=\"glyphicon glyphicon-wrench\"></span> Admin</a></li>";
				}

			include("include/check_system.php");

			echo "<li><a href=\"login.php?logout=TRUE\"><span class=\"glyphicon glyphicon-log-out\"></span> Logout</a></li>";
			
			}
		else {
			echo "<li><a href=\"login.php\"><span class=\"glyphicon glyphicon-log-in\"></span> Login</a></li>";
			$notlogged = TRUE;
			}
		}
	else{
		if (sessionAuthenticate($connection)) {
			$username = $_COOKIE["username"];
			echo "<li><a href=\"edit_myinfo.php\" title=\"Edit my information or change password\"><span class=\"glyphicon glyphicon-user\"></span> $username</a></li>";

			#Check if user can enter admin area
			$username = $_COOKIE["username"];
			if (is_user_admin2($username, $connection)) {
					echo "<li><a href=\"admin.php\" title=\"Administration menu\"><span class=\"glyphicon glyphicon-wrench\"></span> Admin</a></li>";
				}

			include("include/check_system.php");

			echo "<li><a href=\"login.php?logout=TRUE\"><span class=\"glyphicon glyphicon-log-out\"></span> Logout</a></li>";
						
			}
		else {
			echo "<li><a href=\"login.php\"><span class=\"glyphicon glyphicon-log-in\"></span> Login</a></li>";
			$notlogged = TRUE;
			}
		}

	          	


          echo "</ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>";


?>