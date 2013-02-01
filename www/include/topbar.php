<?php
if (!isset($special_wrapper)){
	$special_wrapper = FALSE;
	}

if (!isset($special_iframe)){
	$special_iframe = FALSE;
	}

if ($special_wrapper == FALSE && $special_iframe == FALSE){
	echo "<div class=\"span-12\">
			<a href=\"$app_dir\"><img src=\"$app_logo\"></a>
		</div>
		<div class=\"span-12 last\">";
			require("include/toplogin.php");
	echo "</div>";
	}
?>
