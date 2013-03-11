<?php
$tool=filter_var($_GET["tool"], FILTER_SANITIZE_STRING);

if ($tool=="none"){
	echo "&nbsp;";
	}
else{
	require("../tools/$tool");
	}
?>
