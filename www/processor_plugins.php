<?php

$dir="plugins/";
$plugins=scandir($dir);

for ($d=0;$d<count($plugins);$d++) {
	if (strpos(strtolower($plugins[$d]), ".php")) {
		require("plugins/$plugins[$d]");
		}
	}

?>
