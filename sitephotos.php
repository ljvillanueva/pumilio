<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require("config.php");
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");

$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Site Photographs</title>";

#Get CSS
require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
	echo $googleanalytics_code;
	}
	

#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
?>

</head>
<body>

<div style="padding: 10px;">

<?php

$SiteName=query_one("SELECT SiteName FROM Sites WHERE SiteID='$SiteID'", $connection);

echo "<h3>Photographs at $SiteName:</h3>";

$query = "SELECT *, DATE_FORMAT(PhotoDate, '%d-%b-%Y %H:%i:%s') AS PhotoDate_f FROM Sites, SitesPhotos WHERE Sites.SiteID='$SiteID' AND 
		Sites.SiteID=SitesPhotos.SiteID ORDER BY PhotoDate, ViewDegrees";

$result=query_several($query, $connection);
$nrows = mysqli_num_rows($result);
	if ($nrows>0) {
		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			echo "<p><strong>$PhotoDate_f</strong><br>
			<div style=\"padding: 10px;\"><a href=\"#\" onclick=\"window.open('sitephotos/$SiteID/$PhotoFilename', 'pics1', 'width=1000,height=800,status=yes,resizable=yes,scrollbars=yes'); return false;\"><img src=\"sitephotos/$SiteID/$PhotoFilename\" width=\"200\"></a><br>";
			if (isset($ViewDegrees) && $ViewDegrees!=""){
				echo "Direction: $ViewDegrees<br>";
				}
			if (isset($PhotoNotes) && $PhotoNotes!=""){
				echo "Notes: $PhotoNotes<br>";
				}

			echo "(ID: $SitesPhotoID)</div>";
			}
		}
	else {
		echo "<div class=\"error\">This site has no photographs in the database.</div>";
		}

?>

<br><p><a href="#" onClick="window.close();">Close window.</a>

</div>

</body>
</html>
