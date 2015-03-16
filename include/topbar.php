<?php

	echo " <!-- Fixed navbar -->
	    <nav class=\"navbar navbar-inverse navbar-default\">
	      <div class=\"container-fluid\">
	        <div class=\"navbar-header\">
	          <a class=\"navbar-brand\" href=\"$app_dir\">$app_custom_name</a>
	        </div>
	        <div id=\"navbar\" class=\"navbar-collapse collapse\">
	          <ul class=\"nav navbar-nav\">
	            <li><a href=\"$app_dir\">Home</a></li>
	            <li><a href=\"map.php\">Map of Sounds</a></li>
	            <li><a href=\"about.php\">About</a></li>
	          </ul>
	          <ul class=\"nav navbar-nav navbar-right\">
	          	<li class=\"right\"><a href=\"login.php\">Login </a></li>
	          </ul>
	        </div><!--/.nav-collapse -->
	      </div>
	    </nav>";

/*
	echo "<div class=\"row\">
	        <div class=\"col-lg-6\">
				<a href=\"$logolink\"><img src=\"$mainlogo\" alt=\"Logo\"></a>
			</div>
			<div class=\"col-lg-6\">
				<div class=\"right\">";
					require("include/toplogin.php");
				echo "</div>
			</div>
		</div>";*/

?>
