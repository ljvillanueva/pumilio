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

$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Add File</title>";

require("include/get_css.php");
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
				},
				ColID: {
					required: true
				}
			},
			messages: {
				userfile: "Please select a file",
				SiteID: "Please select a site",
				ColID: "Please select a collection"
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

					echo "<input name=\"userfile\" type=\"file\" class=\"fg-button ui-state-default ui-corner-all\">&nbsp;&nbsp;";

					#check php.ini values for uploads
					$max1 = filter_var(ini_get("upload_max_filesize"), FILTER_SANITIZE_NUMBER_INT);
					$max2 = filter_var(ini_get("post_max_size"), FILTER_SANITIZE_NUMBER_INT);

					if ($max1 < $max2){
						$max = ini_get("upload_max_filesize");
						}
					else{
						$max = ini_get("post_max_size");
						}


					$query = "SELECT * from Collections ORDER BY CollectionName";
					$result = mysqli_query($connection, $query)
						or die (mysqli_error($connection));
					$nrows = mysqli_num_rows($result);

					echo "<p>Collection: 
						<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">\n";

					if ($nrows>0) {
						for ($i=0;$i<$nrows;$i++) {
							$row = mysqli_fetch_array($result);
							extract($row);
								echo "<option value=\"$ColID\">$CollectionName</option>\n";
							}
						}

					echo "</select>";
					

					echo "<p>Site: <select name=\"SiteID\" class=\"ui-state-default ui-corner-all\">";

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
				
					<p><input type=\"submit\" value=\" Upload \" class=\"fg-button ui-state-default ui-corner-all\">
				</form><br><br>
				<p><span class=\"notice\">Maximum file size allowed is $max</span><br><br>";
				}
			else {
				echo "There are no sites yet. Please add sites before adding files.";
				}
			
			echo "<p><form method=\"GET\" action=\"include/addsite.php\" target=\"addsite\" onsubmit=\"window.open('', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Add sites \" class=\"fg-button ui-state-default ui-corner-all\">
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
