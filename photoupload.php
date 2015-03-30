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
<title>$app_custom_name - Upload photographs</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

	<script src="js/jquery.validate.js"></script>

	<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#fileForm").validate({
			rules: {
				file: {
					required: true
				},
				SiteID: {
					required: true
				}
			},
			messages: {
				file: "Please select a file",
				fileid: "Please enter a site"
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

			<h3>Upload a photograph:</h3>

			Photograph file:<br>
			<form enctype="multipart/form-data" action="photoupload2.php" method="post" id="fileForm">
				<input name="userfile" type="file" id="file" class="fg-button ui-state-default ui-corner-all">&nbsp;&nbsp;

			<?php
				$max=ini_get("upload_max_filesize");
				echo "<span class=\"notice\">Maximum file size is $max</span>";
			?>

			<br>
			Site where this photograph was taken:<br>
			
			<?php
			
			$query = "SELECT * from Sites ORDER BY SiteName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			echo "<select name=\"SiteID\" class=\"ui-state-default ui-corner-all\">
				<option></option>";

				for ($i=0;$i<$nrows;$i++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					echo "<option value=\"$SiteID\">$SiteName - $SiteID $SiteLat, $SiteLon</option>\n";
					}
			
			?>
			</select> 
			<br>
			Direction where the picture was taken:<br><input type="text" name="photodir" size="10" class="fg-button ui-state-default ui-corner-all"> (degrees or N/E/S/W)<br>
			Notes of this file:<br>
			<input name="photonotes" size="40" class="fg-button ui-state-default ui-corner-all"><br><br>
			<input type="submit" value=" Upload " class="fg-button ui-state-default ui-corner-all"> <br>
			</form>
			<br>
			<p class="notice">The photograph file has to be in jpg format.
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
