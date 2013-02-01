<?php
session_start();

require("functions.php");

$config_file = '../config.php';

if (file_exists($config_file)) {
    require("../config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("apply_config.php");

?>
<html>
<head>

<?php
 require("get_css_include.php");
 require("get_jqueryui_include.php");
?>
	
	<script src="../js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddForm").validate({
			rules: {
				SiteName: {
					required: true
				},
				SiteLat: {
					required: true,
					number: true
				},
				SiteLon: {
					required: true,
					number: true
				}
			},
			messages: {
				SiteName: "Please enter the name of the site",
				SiteLat: "Please enter a latitude",
				SiteLon: "Please enter a longitude"
			}
			});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>
	
</head>
<body>

<div style="padding: 10px;">

<?php

#Check if user can edit files (i.e. has admin privileges)
	if (!sessionAuthenticate($connection)) {
		die("<div class=\"error\">You are not logged in.</div></body></html>");
		}
		
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die("<div class=\"error\">You are not and admin.</div></body></html>");
		}


	if ($_POST["submitted"]==1) {
		$SiteName=filter_var($_POST["SiteName"], FILTER_SANITIZE_STRING);
		$SiteNotes=filter_var($_POST["SiteNotes"], FILTER_SANITIZE_STRING);
		$SiteLat=filter_var($_POST["SiteLat"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$SiteLon=filter_var($_POST["SiteLon"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
				
		#Check that the name is not taken already
		$result_name=query_one("SELECT COUNT(*) FROM Sites WHERE SiteName='$SiteName'", $connection);
		if ($result_name==0) {
			$checkfail1=0;
			}
		else {
			$checkfail1=1;
			}
			
		#Check that the site coords are already in the db
		$result_coords=query_one("SELECT COUNT(*) FROM Sites WHERE SiteLat='$SiteLat' AND SiteLon='$SiteLon'", $connection);
		if ($result_coords==0) {
			$checkfail2=0;
			}
		else {
			$checkfail2=1;
			}

		if ($checkfail1==1) {
			echo "<div class=\"error\">There is a site with that name already. Please try again.</div>";
			$checkfail=1;
			}
		if ($checkfail2==1) {
			echo "<div class=\"error\">There is a site with those coordinates already. Please try again.</div>";
			$checkfail=1;
			}
		if ($checkfail!=1) {
			if ($SiteCode=="") {
				$SiteCode="NULL";
				}
			$query_tags = "INSERT INTO Sites (SiteName, SiteLat, SiteLon, SiteNotes) VALUES ('$SiteName', '$SiteLat', '$SiteLon', '$SiteNotes')";
			$result_tags = mysqli_query($connection, $query_tags)
				or die (mysqli_error($connection));
			echo "<div class=\"success\">The site was added to the database.</div>
				<br><br><p><a href=\"#\" onClick=\"opener.location.reload();window.close();\">Close window</a>";
				die();
			}
		}


echo "<h3>Add a site to the database</h3>
	<p><form action=\"addsite.php\" method=\"POST\" id=\"AddForm\">
	<input name=\"submitted\" type=\"hidden\" value=\"1\">
	Name of the site: <input name=\"SiteName\" type=\"text\" maxlength=\"60\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\"><br>
	Notes about this site: <input name=\"SiteNotes\" type=\"text\" maxlength=\"60\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\"><br>
	Latitude: <input name=\"SiteLat\" type=\"text\" maxlength=\"20\" size=\"24\" class=\"fg-button ui-state-default ui-corner-all\"> (decimal degrees)<br>
	Longitude: <input name=\"SiteLon\" type=\"text\" maxlength=\"20\" size=\"24\" class=\"fg-button ui-state-default ui-corner-all\"> (decimal degrees)<br>
	<input type=\"submit\" value=\" Add site \" class=\"fg-button ui-state-default ui-corner-all\"></form>
	
	<br><br><p><a href=\"#\" onClick=\"window.close();\">Cancel and close window</a>";

?>
</div>
</body>
</html>
