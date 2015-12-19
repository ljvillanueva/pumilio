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

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>$app_custom_name - Add File</title>";

require("include/get_css3.php");
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


		<h3>Add files to the database</h3>

		<div class="row">
			<div class="col-lg-6">

		<?php
			$CollectionName=query_one("SELECT CollectionName FROM Collections WHERE ColID='$ColID' LIMIT 1", $connection);
			echo "<p>Add a file to <strong>$CollectionName</strong>:";
			$querysites = "SELECT * from Sites ORDER BY SiteName";
			$resultsites = mysqli_query($connection, $querysites)
				or die (mysqli_error($connection));
			$nrowssites = mysqli_num_rows($resultsites);
			if ($nrowssites>0) {
				echo "<p>
					<form enctype=\"multipart/form-data\" action=\"add_file2.php\" method=\"post\" id=\"EditForm\">";

					echo "<label for=\"userfile\">Select a file:</label>
						<input name=\"userfile\" type=\"file\" class=\"form-control\">&nbsp;&nbsp;";

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

					echo "<label for=\"ColID\">Collection:</label>
						<select name=\"ColID\" class=\"form-control\">\n";

					if ($nrows>0) {
						for ($i=0;$i<$nrows;$i++) {
							$row = mysqli_fetch_array($result);
							extract($row);
								echo "<option value=\"$ColID\">$CollectionName</option>\n";
							}
						}

					echo "</select>";
					

					echo "<label for=\"SiteID\">Site:</label><select name=\"SiteID\" class=\"form-control\">";

					for ($i=0;$i<$nrowssites;$i++) {
						$row = mysqli_fetch_array($resultsites);
						extract($row);
						echo "<option value=\"$SiteID\">$SiteName";
						if ($SiteCode!="") {
							echo " - Code: $SiteCode";
							}
						echo "</option>\n";
						}

					echo "</select> <a href=\"#\" onclick=\"window.open('include/addsite.php', 'softcheck', 'width=600,height=400,status=yes,resizable=yes,scrollbars=yes')\">Add sites</a><br><br>

					<input name=\"ColID\" type=\"hidden\" value=\"$ColID\">
				
					<p><button type=\"submit\" class=\"btn btn-lg btn-primary btn-block\"> Upload </button>
				</form><br><br>";
				
				}
			else {
				echo "There are no sites yet. Please add sites before adding files.";
				}
			
			?>

			<br>
			
			
			<?php

			echo "</div><div class=\"col-lg-6\"><p><div class=\"alert alert-warning\">Maximum file size allowed is <strong>$max</strong>. The administrator can increase this limit.<br><br>";

				echo "The file has to be in one of these formats: <strong>";
					include("include/sox_formats.php");
				echo "</strong></div>
				</div></div>";


require("include/bottom.php");
?>

</body>
</html>