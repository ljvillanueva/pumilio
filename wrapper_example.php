<?php

#This is an example of how Pumilio can be integrated easily to another site.
# This is a quick hack and only will work when browsing a site, not for adding, editing, opening the files, etc.
# Those functions will come later.

$pumilio_URL = "http://HOST/DIR";

$page = filter_var($_GET["page"], FILTER_SANITIZE_STRING);
if ($page == ""){
	$p = file_get_contents("$pumilio_URL");}
elseif ($page == "db_browse"){
	$ColID = filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
	$startid=filter_var($_GET["startid"], FILTER_SANITIZE_NUMBER_INT);
	$order_by=filter_var($_GET["order_by"], FILTER_SANITIZE_STRING);
	$order_dir=filter_var($_GET["order_dir"], FILTER_SANITIZE_STRING);
	$display_type=filter_var($_GET["display_type"], FILTER_SANITIZE_STRING);
	$show_tags=filter_var($_GET["show_tags"], FILTER_SANITIZE_STRING);
	$p = file_get_contents("$pumilio_URL/$page.php?ColID=$ColID&startid=$startid&order_by=$order_by&order_dir=$order_dir&display_type=$display_type&show_tags=$show_tags");
	}
elseif ($page == "browse_map"){
	$time_to_browse = filter_var($_GET["time_to_browse"], FILTER_SANITIZE_STRING);
	$date_to_browse = filter_var($_GET["date_to_browse"], FILTER_SANITIZE_STRING);
	$usekml = filter_var($_GET["usekml"], FILTER_SANITIZE_NUMBER_INT);
	$nokml = filter_var($_GET["nokml"], FILTER_SANITIZE_NUMBER_INT);	
	$p = file_get_contents("$pumilio_URL/$page.php?time_to_browse=$time_to_browse&time_to_browse=$time_to_browse&usekml=$usekml&nokml=$nokml");
	}
elseif ($page == "browse_site"){
	$SiteID = filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
	$startid = filter_var($_GET["startid"], FILTER_SANITIZE_NUMBER_INT);
	$order_by = filter_var($_GET["order_by"], FILTER_SANITIZE_STRING);
	$order_dir = filter_var($_GET["order_dir"], FILTER_SANITIZE_STRING);
	$display_type = filter_var($_GET["display_type"], FILTER_SANITIZE_STRING);
	$p = file_get_contents("$pumilio_URL/$page.php?SiteID=$SiteID&startid=$startid&order_by=$order_by&order_dir=$order_dir&display_type=$display_type");
	}
elseif ($page == "browse_site_date"){
	$SiteID = filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
	$QDate = filter_var($_GET["Date"], FILTER_SANITIZE_NUMBER_INT);
	$startid = filter_var($_GET["startid"], FILTER_SANITIZE_NUMBER_INT);
	$order_by = filter_var($_GET["order_by"], FILTER_SANITIZE_STRING);
	$order_dir = filter_var($_GET["order_dir"], FILTER_SANITIZE_STRING);
	$display_type = filter_var($_GET["display_type"], FILTER_SANITIZE_STRING);
	$p = file_get_contents("$pumilio_URL/$page.php?SiteID=$SiteID&Date=$QDate&startid=$startid&order_by=$order_by&order_dir=$order_dir&display_type=$display_type");
	}
elseif ($page == "db_filedetails"){
	$SoundID = filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
	$hidemarks = filter_var($_GET["hidemarks"], FILTER_SANITIZE_NUMBER_INT);
	$d = filter_var($_GET["d"], FILTER_SANITIZE_STRING);
	$p = file_get_contents("$pumilio_URL/$page.php?SoundID=$SoundID&hidemarks=$hidemarks&d=$d");
	}

echo $p;

?>
