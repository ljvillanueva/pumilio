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

$type=filter_var($_GET["type"], FILTER_SANITIZE_STRING);
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);

if ($type == ""){
	$type = "col";
	}

if ($type == "col"){
	$db_sel = "ColID";
	$type_s = "Collections";
	$type_s1 = "Collection";
	$type_id = "CollectionName";
	$type_link = "<a href=\"qc.php?type=site\">See by Sites</a>";
	}
elseif ($type == "site"){
	$db_sel = "SiteID";
	$type_s = "Sites";
	$type_s1 = "Site";
	$type_id = "SiteName";
	$type_link = "<a href=\"qc.php?type=col\">See by Collections</a>";
	}

if (isset($_GET["SoundID"])){
	$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
	$tab=filter_var($_GET["tab"], FILTER_SANITIZE_NUMBER_INT);
	query_one("UPDATE Sounds SET SoundStats='0' WHERE SoundID=$SoundID", $connection);
	header("Location: $app_url/qc.php?type=$type&tab=$tab");
	die();
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Files manager</title>\n";

if ($useR==TRUE){
	echo "<meta http-equiv=\"refresh\" content=\"30\">\n";
	}

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics) {
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
		<?php

		echo "<h3>Generation of extra quality control data</h3>
			<p>By default, the system uses the file size and sound duration to detect
				problems with particular files. If the system is set to use R, 
				it can generate additional data and indices that can be used to detect
				problems with files.
			<p>Check the data by exploring the <a href=\"qa.php\">figures</a>.";

		if ($useR==FALSE){
			echo "<p>This system is not set up to use R. Check your settings in the <em>config.php</em> file.";
			}
		elseif ($useR==TRUE){
			$no_files = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9'", $connection);
			if ($no_files>0){
				$no_files_done = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' AND SoundStats='2'", $connection);
				$no_files_waiting = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' AND SoundStats='0'", $connection);
				$no_files_inprocess = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' AND SoundStats='1'", $connection);
				$no_files_error = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' AND SoundStats='9'", $connection);
			
				$percent = floor(($no_files_done/($no_files-$no_files_error))*100);
			
				echo "<p><strong>Overall statistics</strong> [<a href=\"qc.php\">Refresh</a>]:
					<ul>
					 <li>Files in the system: $no_files</li>
					 <li>Completed files: $no_files_done</li>
					 <li>Files yet to be processed: $no_files_waiting</li>\n";
			
				if ($no_files_error > 0){
					echo "<li><img src=\"images/exclamation.png\"> Files with errors: $no_files_error</li>";
					}
					 
				echo "</ul>\n";
				
				if ($percent<100) {
					echo "\n<style type=\"text/css\">
						.ui-progressbar .ui-progressbar-value { background-image: url(js/jquery/start/images/pbar-ani.gif); }
					</style>

					<script type=\"text/javascript\">
					$(function() {
						$(\"#progressbar\").progressbar({
							value: $percent
						});
					});
					</script>\n";

					echo "<div id=\"progressbar\"></div>";
					$how_many = bgHowManyStats();
					if ($how_many > 0){
						$PID_array = bgHowManyStats_PID();
						if ($how_many == 1){
							echo "<br>$how_many script is running in the background:";
							}
						else{
							echo "<br>$how_many scripts are running in the background:";
							}
					
						echo "<ul>\n";
						for ($h=0; $h < $how_many; $h++) {
							$this_PID = $PID_array[$h];
							$how_long = bgProcess_howlong($this_PID);
							$how_long = explode(":", $how_long);
							$how_long_h = $how_long[0];
							$how_long_m = $how_long[1];
							if ($how_long_h == "00" && $how_long_m == "00"){
								$how_long = "less than a minute";
								}
							elseif ($how_long_h == "00" && $how_long_m == "01"){
								$how_long = "1 minute";
								}
							elseif ($how_long_h == "00" && $how_long_m != "00"){
								$how_long = "$how_long_m minutes";
								}
							else {
								$how_long = "$how_long_h hours and $how_long_m minutes";
								}
							
							echo "<li>Script running for $how_long [<a href=\"include/killpid.php?pid=$this_PID\">stop script</a>]\n</li>";
							}
						echo "</ul>\n";
						}
					}
				else {
					echo "\n<div aria-valuenow=\"100\" aria-valuemax=\"100\" aria-valuemin=\"0\" role=\"progressbar\" class=\"ui-progressbar ui-widget ui-widget-content ui-corner-all\" id=\"progressbar\">
						<div style=\"width: 100%;\" class=\"ui-progressbar-value ui-widget-header ui-corner-all\">
						</div>
					</div>\n";
					}
			
				echo "<br></p>";
			
				if (isset($tab)){
					echo "<script type=\"text/javascript\">
					$(function() {
						$( \"#accordion\" ).accordion({
							collapsible: true,
							autoHeight: false,
							active: $tab,
							icons: { \"header\": \"ui-icon-plus\", \"headerSelected\": \"ui-icon-minus\" }
						});
					});
					</script>
					";
					}
				else {
					echo "<script type=\"text/javascript\">
					$(function() {
						$( \"#accordion\" ).accordion({
							collapsible: true,
							autoHeight: false,
							active: false,
							icons: { \"header\": \"ui-icon-plus\", \"headerSelected\": \"ui-icon-minus\" }
						});
					});
					</script>
					";
					}
			
				echo "<p>$type_s: [$type_link]<br>
				<div id=\"accordion\">";
							
				$query = "SELECT DISTINCT $db_sel AS db_val FROM Sounds WHERE SoundStatus!='9' ORDER BY $db_sel";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));
				$nrows = mysqli_num_rows($result);
				if ($nrows>0){
				for ($i=0; $i < $nrows; $i++) {
					$row = mysqli_fetch_array($result);
					extract($row);

					$no_files = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' 
							AND $db_sel='$db_val'", $connection);
					$no_files_done = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' 
							AND $db_sel='$db_val' AND SoundStats='2'", $connection);
					$no_files_todo = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' 
							AND $db_sel='$db_val' AND SoundStats='0'", $connection);
					$no_files_inprogress = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' 
							AND $db_sel='$db_val' AND SoundStats='1'", $connection);
					$no_files_error = query_one("SELECT COUNT(*) FROM Sounds WHERE SoundStatus!='9' 
							AND $db_sel='$db_val' AND SoundStats='9'", $connection);
				
					$percent = floor(($no_files_done/$no_files)*100);

					$this_name = query_one("SELECT $type_id FROM $type_s WHERE $db_sel='$db_val'", $connection);

				
					echo "
					<h3><a href=\"#\">$type_s1: $this_name ($percent% completed)</a></h3>
					<div>
				
					<p>$type_s1: $this_name<br>
						<ul>
						 <li>Completed files: $no_files_done</li>
						 <li>Files being processed: $no_files_inprogress</li>
						 <li>Files yet to add: $no_files_todo</li>
						 <li>Files with errors: $no_files_error</li>
						</ul>\n";
							
					echo "<table>\n";
					echo "<tr><td>&nbsp;</td><td><strong>File</strong></td><td>&nbsp;</td><td><strong>Status</strong></td></tr>\n";

					$query_1 = "SELECT SoundID, SoundName, SoundStats from Sounds WHERE SoundStatus!='9' 
							AND $db_sel='$db_val' ORDER BY SoundStats DESC";
					$result_1 = mysqli_query($connection, $query_1)
						or die (mysqli_error($connection));
					$nrows_1 = mysqli_num_rows($result_1);
					if ($nrows_1>0){
						for ($j=0; $j < $nrows_1; $j++) {
							$row_1 = mysqli_fetch_array($result_1);
							extract($row_1);
							echo "<tr><td>&nbsp;</td><td>$SoundName</td><td>&nbsp;</td>\n";

							if ($SoundStats == 0){
								echo "<td> <img src=\"images/database.png\"> To check</td></tr>\n";
								}
							elseif ($SoundStats == 2){
								echo "<td> <img src=\"images/accept.png\"> Statistics generated</td></tr>\n";
								}
							elseif ($SoundStats == 1){
								echo "<td> <img src=\"images/ajax-loader.gif\"> Working...</td></tr>\n";
								}
							elseif ($SoundStats == 9){
								echo "<td> <img src=\"images/error.png\"> Error
									(<a href=\"qc.php?SoundID=$SoundID&type=$type&tab=$i\" title=\"Reset\">reset</a>)</td></tr>\n";
								}
							}
						}
					echo "</table>
					</div>\n";
					}
					echo "</div><!-- End accordion -->\n";
			
					}
				}
			else {
				echo "<p>There are no files in the system.
					<p><a href=\"add.php\">Add files</a>.\n";
				}
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

<?php
session_write_close();
flush(); @ob_flush();
if ($useR==TRUE){
	if ($special_noprocess == FALSE){
		stats_in_background($absolute_dir, $connection);
		}
	}
?>

</body>
</html>
