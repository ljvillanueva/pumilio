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

$force_loggedin = TRUE;
require("include/check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Add Collection</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if (isset($_POST["run"])){
	$run = filter_var($_POST["run"], FILTER_SANITIZE_STRING);
	}
else{
	$run = FALSE;
	}

if ($run){

	#Sanitize
	$CollectionName=filter_var($_POST["CollectionName"], FILTER_SANITIZE_STRING);
	$Author=filter_var($_POST["Author"], FILTER_SANITIZE_STRING);
	$FilesSource=filter_var($_POST["FilesSource"], FILTER_SANITIZE_STRING);
	$CollectionFullCitation=filter_var($_POST["CollectionFullCitation"], FILTER_SANITIZE_STRING);
	$MiscURL=filter_var($_POST["MiscURL"], FILTER_SANITIZE_STRING);
	$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);

	if ($use_googleanalytics) {
		echo $googleanalytics_code;
		}


	#Execute custom code for head, if set
	if (is_file("$absolute_dir/customhead.php")) {
			include("customhead.php");
		}
	
	
	
	echo "</head>
	<body>

	<!--Blueprint container-->
	<div class=\"container\">";
		require("include/topbar.php");

	echo "	<div class=\"span-24 last\">
			<hr noshade>
		</div>
		<div class=\"span-24 last\">
			&nbsp;
		</div>
		<div class=\"span-24 last\">";

		if ($CollectionName!="") {
			$query = ("INSERT INTO Collections 
				(Author,FilesSource,CollectionName,CollectionFullCitation,MiscURL,Notes) 
				VALUES ('$Author', '$FilesSource', '$CollectionName', '$CollectionFullCitation', '$MiscURL', '$Notes')");
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));

			$ColID=mysqli_insert_id($connection);

			echo "<h3>Add collection</h3>
			<div class=\"success\">Collection added.</div>

			<p><a href=\"add_collection.php\">Add more collections</a>
			<p><a href=\"add_from_field.php?sm=1\">Upload sound files from a Wildlife Acoustics SongMeter</a>
			<p><a href=\"add_from_field.php\">Upload sound files from the field</a>
			<p><a href=\"add_from_field.php?sm=1&local=1\">Add sound files from a Wildlife Acoustics SongMeter</a> (stored locally in server)
			<p><a href=\"add_from_field.php?&local=1\">Add sound files from the field</a> (stored locally in server)
			<p><a href=\"add_from_db.php\">Import files from a database/spreadsheet</a>
			<p><a href=\"#\" onclick=\"window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">Add sites</a>
			<p><a href=\"admin.php?t=4\">Add sensors</a> (in the admin menu)";

			$result_fm = query_one("SELECT COUNT(*) FROM FilesToAddMembers", $connection);
			if ($result_fm>0){
				echo "<hr noshade><p><a href=\"file_manager.php\">Check uploaded file status</a>";
				}

			}
		else {
			echo "<div class=\"notice\">The name for the collection can not be empty.</div>";
			}

		echo "</div>
		<div class=\"span-24 last\">
			&nbsp;
		</div>
		<div class=\"span-24 last\">";
		require("include/bottom.php");
	echo "</div>
	</div>";
	}
else{

	echo "<script src=\"js/jquery.validate.js\"></script>

	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

		<script type=\"text/javascript\">
		$().ready(function() {
			// validate signup form on keyup and submit
			$(\"#EditForm\").validate({
				rules: {
					CollectionName: {
						required: true
					},
					MiscURL: {
						url: true
					}
				},
				messages: {
					CollectionName: \"Please enter a name for this collection\",
					MiscURL: \"Please enter an appropriate web address\"
				}
				});
			});
		</script>
		<style type=\"text/css\">
		#fileForm label.error {
			margin-left: 10px;
			width: auto;
			display: inline;
		}
		</style>\n";

	if ($use_googleanalytics) {
		echo $googleanalytics_code;
		}

	echo "</head>
	<body>

		<!--Blueprint container-->
		<div class=\"container\">";
			require("include/topbar.php");

		echo "<div class=\"span-24 last\">
				<hr noshade>
			</div>
			<div class=\"span-24 last\">
				&nbsp;
			</div>
			<div class=\"span-24 last\">";
			
			echo "<h3>Add collections to the database</h3>
				<form action=\"add_collection.php\" method=\"POST\" id=\"EditForm\">
					<input type=\"hidden\" name=\"run\" value=\"TRUE\">
					<p>Name of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"CollectionName\" maxlength=\"100\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
					Author of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"Author\" maxlength=\"80\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
					Media of the collection: &nbsp;&nbsp;
					<select name=\"FilesSource\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">
						<option></option>
						<option>Field Recording</option>
						<option>Book with CD</option>
						<option>Automated Audio Logger</option>
						<option>Audio CD</option>
						<option>CD-ROM</option>
						<option>DVD</option>
						<option>Tape</option>
						<option>LP</option>
						<option>Internet</option>
						<option>Donation</option>
						<option>Other</option>
					</select><br>
					Full citation of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"CollectionFullCitation\" maxlength=\"250\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
					Website: &nbsp;&nbsp;<input type=\"text\" name=\"MiscURL\" maxlength=\"250\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>
					Notes of the collection: &nbsp;&nbsp;<input type=\"text\" name=\"Notes\" maxlength=\"255\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>&nbsp;&nbsp;

					<input type=submit value=\" Add Collection \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
				</form>

			</div>
			<div class=\"span-24 last\">
				&nbsp;
			</div>
			<div class=\"span-24 last\">";
				
				require("include/bottom.php");
				

			echo "</div>
			</div>";
	}
?>

</body>
</html>
