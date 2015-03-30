<?php

#Clean tags first
$result_tagcloud1 = query_several("SELECT * FROM Tags", $connection);
$nrows_tagcloud1 = mysqli_num_rows($result_tagcloud1);
if ($nrows_tagcloud1>0) {
	for ($tc1 = 0; $tc1 < $nrows_tagcloud1; $tc1++) {
		$row_tagcloud1 = mysqli_fetch_array($result_tagcloud1);
		extract($row_tagcloud1);
		$no_times = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundID='$SoundID'", $connection);
		if ($no_times == 0){
			query_one("DELETE From Tags WHERE TagID='$TagID'", $connection);
			}
		}


	$result_tagcloud = query_several("SELECT DISTINCT Tag FROM Tags ORDER BY RAND()", $connection);
	$nrows_tagcloud = mysqli_num_rows($result_tagcloud);
	if ($nrows_tagcloud > 0) {
		$tags_count = array();
		for ($tc = 0; $tc < $nrows_tagcloud; $tc++) {
			$row_tagcloud = mysqli_fetch_array($result_tagcloud);
			extract($row_tagcloud);
			$no_times = query_one("SELECT COUNT(*) FROM Tags WHERE Tag='$Tag'", $connection);
			array_push($tags_count, $no_times);
			}
		
		$max_tag_count = max($tags_count);
	
		$result_tagcloud = query_several("SELECT DISTINCT Tag FROM Tags ORDER BY RAND()", $connection);
		$nrows_tagcloud = mysqli_num_rows($result_tagcloud);
		echo "<p>";

		$counter = 0;
		if ($nrows_tagcloud > 10) {
			$counter_break = 5;
			}
		else {
			$counter_break = 10;
			}

		for ($tc = 0; $tc < $nrows_tagcloud; $tc++) {
			$row_tagcloud = mysqli_fetch_array($result_tagcloud);
			extract($row_tagcloud);
			$no_times = query_one("SELECT COUNT(*) FROM Tags WHERE Tag='$Tag'", $connection);
			$this_tag_size=10+round((round($no_times/$max_tag_count))*10);
			echo "<a href=\"browse_by_tag.php?Tag=$Tag\" style=\"font-size: $this_tag_size;\">$Tag</a> &nbsp;&nbsp; ";
			$counter++;
			if ($counter > $counter_break) {
				$counter = 0;
				echo "<br>";
				}
			}

		}
	}
else {
	echo "<p>There are no tags in the database.";
	}
?>