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

$sm=filter_var($_GET["sm"], FILTER_SANITIZE_NUMBER_INT);
$local=filter_var($_GET["local"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>
";

if ($sm==1) {
	echo "<title>$app_custom_name - Add files from a Wildlife Acoustic SongMeter</title>";
	}
else {
	echo "<title>$app_custom_name - Add files from the field</title>";
	}

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddForm").validate({
			rules: {
				ColID: {
					required: true
					},
				SiteID: {
					required: true
					},
				SensorID: {
					required: true
					}
				},
				messages: {
					ColID: "Please select the collections to add the files to",
					SiteID: "Please select the site or select 'None'",
					SensorID: "Please select the sensor used or select 'None'"
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

<link rel="stylesheet" href="js/plupload/jquery.plupload.queue.css" type="text/css" media="screen" />

<script type="text/javascript" src="js/plupload/plupload.js"></script>
<script type="text/javascript" src="js/plupload/plupload.html5.js"></script>
<script type="text/javascript" src="js/plupload/jquery.plupload.queue.js"></script>

<?php
$random_dir=mt_rand();
mkdir("tmp/$random_dir", 0777);
setcookie("random_upload_dir", $random_dir, time()+(3600*24*30), $app_dir);
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
			if ($sm==1) {
				echo "<h3>Add sound files from a Wildlife Acoustic SongMeter</h3>
					<p>This form will allow you to import files that were recorded
					using a Wildlife Acoustics SongMeter box.";
				}
			else {
				echo "<h3>Add sound files from the field</h3>
					<p>This form will allow you to import files that have the site,
					date and time encoded in the file name.";
				}
			
			echo"
			<form method=\"post\" action=\"add_from_field2.php\" id=\"AddForm\">";
			
			if ($local == 1){
				$temp_add_dir=query_one("SELECT Value from PumilioSettings WHERE Settings='temp_add_dir'", $connection);
				if ($temp_add_dir == ""){
					echo "<div class=\"error\">The local directory is not set. Please set it up in the <a href=\"admin.php\">Administration</a> menu.</div>";
					}
				$localdir = $temp_add_dir;
				echo "<p>Server local directory: <input name=\"localdir\" type=\"text\" maxlength=\"160\" size=\"50\" value=\"$localdir\" class=\"fg-button ui-state-default ui-corner-all\">
					<input type=\"hidden\" name=\"local\" value=\"1\">";
				}
			else {
				echo"<div id=\"html5_uploader\" style=\"width: 650px; height: 330px;\">You browser doesn't support native upload. Try Firefox 3 or Safari 4.</div>
				<br style=\"clear: both\" />";
				}
				
			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
		
			if ($nrows>0) {
			echo "<p>Add files to this collection: 
				<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">
					<option></option>\n";
	
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
					echo "<option value=\"$ColID\">$CollectionName</option>\n";
				}

			echo "</select> <a href=\"add_collection.php\">Add Collections</a><br>
		
			Site: ";
			$query_s = "SELECT SiteID AS this_SiteID, SiteName AS this_SiteName, SiteLat AS this_SiteLat, SiteLon AS this_SiteLon FROM Sites ORDER BY this_SiteName";
			$result_s = mysqli_query($connection, $query_s)
				or die (mysqli_error($connection));
			$nrows_s = mysqli_num_rows($result_s);
			echo "<select name=\"SiteID\" class=\"ui-state-default ui-corner-all\">
				<option></option>\n";

			for ($j=0;$j<$nrows_s;$j++) {
				$row_s = mysqli_fetch_array($result_s);
				extract($row_s);
				echo "<option value=\"$this_SiteID\">$this_SiteName ($this_SiteLat/$this_SiteLon)</option>\n";
				}
			echo "</select>";
			
			echo " <a href=\"#\" onclick=\"window.open('include/addsite.php', 'addsite', 'width=650,height=350,status=yes,resizable=yes,scrollbars=auto')\">Add sites</a><br>
			
			Sensor: ";
			$query_s = "SELECT * FROM Sensors ORDER BY SensorID";
			$result_s = mysqli_query($connection, $query_s)
				or die (mysqli_error($connection));
			$nrows_s = mysqli_num_rows($result_s);
			echo "<select name=\"SensorID\" class=\"ui-state-default ui-corner-all\">
				<option></option>\n";

			for ($j=0;$j<$nrows_s;$j++) {
				$row_s = mysqli_fetch_array($result_s);
				extract($row_s);
				echo "<option value=\"$SensorID\">$Recorder $Microphone - $Notes</option>\n";
				}
			echo "</select>";
			
			echo " <a href=\"admin.php?t=4\">Add sensors</a>
			
			<input type=\"hidden\" name=\"sm\" value=\"$sm\"><br>
			<input type=submit value=\" Next step \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}
			else {
				echo "<p><strong>There are no collections in the archive. Please create at least <a href=\"add_collection.php\">one collection</a> to continue.</strong>";
				}

			require("include/sox_formats_list.php");
				
			echo "<script type=\"text/javascript\">
			$(function() {
				// Setup html5 version
				$(\"#html5_uploader\").pluploadQueue({
					// General settings
					runtimes : 'html5',
					url : 'uploaded.php',
					max_file_size : '1000mb',
					chunk_size : '1mb',
					unique_names : false
				});
			});
			</script>";
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
