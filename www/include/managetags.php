<?php
$query_tags = "SELECT TagID,Tag FROM Tags WHERE SoundID='$SoundID' ORDER BY Tag";
$result_tags=query_several($query_tags, $connection);
$nrows_tags = mysqli_num_rows($result_tags);

if ($nrows_tags>0){
	$self=$_SERVER['PHP_SELF'];
	$q=$_SERVER['QUERY_STRING'];
	echo "<p><strong>Tags</strong>: ";
	for ($t=0;$t<$nrows_tags;$t++){
		$row_tags = mysqli_fetch_array($result_tags);
		extract($row_tags);
		echo "$Tag ";
		}
	echo "<br>";
	}
?>
