<?php

echo "<div id=\"loadingdiv\">
		<p>
			<div class=\"alert alert-info center\" role=\"alert\">
      			<h3>Please wait</h3><h4>Loading... <i class=\"fa fa-cog fa-spin\"></i></i></h4>
      		</div>
		</p>
	</div>";

flush();
ob_flush();

?>