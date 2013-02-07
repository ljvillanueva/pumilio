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

#Check if user can edit files (i.e. has admin privileges)
	$username = $_COOKIE["username"];

	if (!is_user_admin2($username, $connection)) {
		die();
		}

#Sanitize
$CollectionName=filter_var($_POST["CollectionName"], FILTER_SANITIZE_STRING);
$Author=filter_var($_POST["Author"], FILTER_SANITIZE_STRING);
$FilesSource=filter_var($_POST["FilesSource"], FILTER_SANITIZE_STRING);
$CollectionFullCitation=filter_var($_POST["CollectionFullCitation"], FILTER_SANITIZE_STRING);
$MiscURL=filter_var($_POST["MiscURL"], FILTER_SANITIZE_STRING);
$Notes=filter_var($_POST["Notes"], FILTER_SANITIZE_STRING);

echo "
<html>
<head>

<title>$app_custom_name - Add Source</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<?php
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">

			<?php

	if ($CollectionName!="") {
		$query = ("INSERT INTO Collections 
			(Author,FilesSource,CollectionName,CollectionFullCitation,MiscURL,Notes) 
			VALUES ('$Author', '$FilesSource', '$CollectionName', '$CollectionFullCitation', '$MiscURL', '$Notes')");
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));

		$ColID=mysqli_insert_id($connection);

		echo "<h3>Add collection</h3>";
		
		echo "<div class=\"success\">Collection added.</div>";
		
		echo "<p><a href=\"add_collection.php\">Add more collections</a>";

		echo "<p><a href=\"add_from_field.php?sm=1\">Upload sound files from a Wildlife Acoustics SongMeter</a>";
					
		echo "<p><a href=\"add_from_field.php\">Upload sound files from the field</a>";
		
		echo "<p><a href=\"add_from_field.php?sm=1&local=1\">Add sound files from a Wildlife Acoustics SongMeter</a> (stored locally in server)";
					
		echo "<p><a href=\"add_from_field.php&local=1\">Add sound files from the field</a> (stored locally in server)";
		
		echo "<p><a href=\"add_from_db.php\">Import files from a database/spreadsheet</a>";
		
		echo "<p><a href=\"#\" onclick=\"window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">Add sites</a>";

		echo "<p><a href=\"admin.php?t=4\">Add sensors</a> (in the admin menu)";

		$result_fm = query_one("SELECT COUNT(*) FROM FilesToAddMembers", $connection);
		if ($result_fm>0){
			echo "<hr noshade><p><a href=\"file_manager.php\">Check uploaded file status</a>";
			}

		}
	else {
		echo "<div class=\"notice\">The name for the collection can not be empty.</div>";
		}
			?>

		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
