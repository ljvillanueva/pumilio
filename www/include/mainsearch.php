<?php
echo "<p>Search sounds in the archive using several options:<br><hr noshade>";

	$no_sounds_s=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9' $qf_check", $connection);
	if ($no_sounds_s==0){
		echo "<p>This archive does not have any sounds.";
		}
	else {
		require("include/searchid.php");
		require("include/searchadvanced.php");
		}
?>
