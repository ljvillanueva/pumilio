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
	if (!sessionAuthenticate($connection)) {
		die();
		}
	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die();
		}

$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>

<title>$app_custom_name - Add File</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#EditForm").validate({
			rules: {
				userfile: {
					required: true
				},
				SiteID: {
					required: true
				}
			},
			messages: {
				userfile: "Please select a file",
				SiteID: "Please select a site"
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
			<h3>Add files to the database</h3>
			<?php
			$CollectionName=query_one("SELECT CollectionName FROM Collections WHERE ColID='$ColID' LIMIT 1", $connection);
			echo "<p>Add a file to <strong>$CollectionName</strong>:";
			$querysites = "SELECT * from Sites ORDER BY SiteName";
			$resultsites = mysqli_query($connection, $querysites)
				or die (mysqli_error($connection));
			$nrowssites = mysqli_num_rows($resultsites);
			if ($nrowssites>0) {
				echo "<p><form enctype=\"multipart/form-data\" action=\"add_file2.php\" method=\"post\" id=\"EditForm\">";

				echo "<input name=\"userfile\" type=\"file\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">&nbsp;&nbsp;";
				$max=ini_get("upload_max_filesize");
	
				echo "<p>Site: <select name=\"SiteID\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">";

				for ($i=0;$i<$nrowssites;$i++) {
					$row = mysqli_fetch_array($resultsites);
					extract($row);
					echo "<option value=\"$SiteID\">$SiteName";
					if ($SiteCode!="") {
						echo " - Code: $SiteCode";
						}
					echo "</option>\n";
					}

				echo "</select> <a href=\"#\" onclick=\"window.open('include/addsite.php', 'softcheck', 'width=600,height=400,status=yes,resizable=yes,scrollbars=yes')\">Add sites</a><br>

				<input name=\"ColID\" type=\"hidden\" value=\"$ColID\">
				
				<p><input type=\"submit\" value=\" Upload \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"> </form><br><br>
				<p><span class=\"notice\">Maximum file size is $max</span><br><br>";
				}
			else {
				echo "There are no sites yet. Please add sites before adding files.";
				}
			
			echo "<p><form method=\"GET\" action=\"include/addsite.php\" target=\"addsite\" onsubmit=\"window.open('', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">
				<input type=submit value=\" Add sites \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
				</form>";

			?>

			<br>
			<p class="notice">The file has to be in one of these formats:
			
			<?php
				include("include/sox_formats.php");
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