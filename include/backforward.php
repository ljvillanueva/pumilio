<?php
echo "<div class=\"center\">";
	if ($special_wrapper==TRUE){
		$browse_site_link = "$wrapper?page=browse_site";
		$db_filedetails_link = "$wrapper?page=db_filedetails";
		}
	else {
		$browse_site_link = "browse_site.php?";
		$db_filedetails_link = "db_filedetails.php?";
		}
			
	#count and next
	echo "Results<br>";

	if ($startid>1) {
		$go_to=$startid-$how_many_to_show;
		echo "<a href=\"$browse_site_link&amp;SiteID=$SiteID&amp;startid=$go_to&amp;order_by=$order_by&amp;order_dir=$order_dir\"><img src=\"$app_url/images/arrowleft.png\"></a>";
		}

	echo " $startid to $endid_show ";

	if ($endid_show<$no_sounds) {
		$go_to=$startid+$how_many_to_show;
		echo "<a href=\"$browse_site_link&amp;SiteID=$SiteID&amp;startid=$go_to&amp;order_by=$order_by&amp;order_dir=$order_dir\"><img src=\"$app_url/images/arrowright.png\"></a>";
		}

	echo "<br>of $no_sounds</div>";
?>
