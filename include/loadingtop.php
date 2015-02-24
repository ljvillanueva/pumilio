<?php

echo "<div id=\"loadingdiv\">
		<p>
			<div class=\"alert alert-info center\" role=\"alert\">
      			<h2>Please wait</h2><h3>Loading... <i class=\"fa fa-cog fa-spin\"></i></i></h3>
      		</div>
		</p>
	</div>";


flush();
ob_flush();
#FOR TESTING:
#sleep(5);

?>