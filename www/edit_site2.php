<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");
$force_admin = TRUE;
require("include/check_admin.php");

$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);

$SiteName=filter_var($_POST["SiteName"], FILTER_SANITIZE_STRING);
$SiteLat=filter_var($_POST["SiteLat"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$SiteLon=filter_var($_POST["SiteLon"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$query = ("UPDATE Sites SET SiteName='$SiteName',
		SiteLat='$SiteLat',
		SiteLon='$SiteLon'
		WHERE SiteID='$SiteID' LIMIT 1");
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));

// Relocate back to where you came from
header("Location: edit_site.php?SiteID=$SiteID&d=1");
die();
	
?>
