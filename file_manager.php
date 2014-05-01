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

if (isset($_GET["ToAddMemberID"])){
	$ToAddMemberID=filter_var($_GET["ToAddMemberID"], FILTER_SANITIZE_NUMBER_INT);
	$tab=filter_var($_GET["tab"], FILTER_SANITIZE_NUMBER_INT);
	$action = filter_var($_GET["action"], FILTER_SANITIZE_NUMBER_INT);
	if ($action == 1){
		query_one("UPDATE FilesToAddMembers SET ReturnCode='1' WHERE ToAddMemberID=$ToAddMemberID", $connection);
		header("Location: $app_url/file_manager.php?tab=$tab");
		die();
		}
	elseif ($action == 2){
		query_one("DELETE FROM FilesToAddMembers WHERE ToAddMemberID=$ToAddMemberID", $connection);
		header("Location: $app_url/file_manager.php?tab=$tab");
		die();
		}
	}

if (isset($_GET["tab"])){
	$tab=filter_var($_GET["tab"], FILTER_SANITIZE_NUMBER_INT);
	}
else {
	$tab = -1;
	}
	
if ($special_noprocess == FALSE){
	add_in_background($absolute_dir, $connection);
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Files manager</title>
<meta http-equiv=\"refresh\" content=\"30\">\n";

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

		echo "<h3>Added files status</h3>";

		$query = "SELECT *, DATE_FORMAT(FilesToAdd.StartTime, '%d-%b-%Y %H:%i:%s') AS StartTime from FilesToAdd, Users WHERE FilesToAdd.UserID=Users.UserID ORDER BY FilesToAdd.StartTime DESC";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
		if ($nrows>0){
			$no_files = query_one("SELECT COUNT(*) FROM FilesToAddMembers", $connection);
			$no_files_done = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE ReturnCode='0'", $connection);
			$no_files_todo = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE ReturnCode='1'", $connection);
			$no_files_running = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE ReturnCode='2'", $connection);
			$no_files_error = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE ReturnCode='9'", $connection);
			
			$percent = floor(($no_files_done/($no_files-$no_files_error))*100);
			
			echo "<p><strong>Overall statistics</strong> [<a href=\"file_manager.php\">Refresh</a>]:
				<ul>
				 <li>Completed files: $no_files_done</li>
				 <li>Files being processed: $no_files_running</li>
				 <li>Files yet to add: $no_files_todo</li>\n";
			
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
				$how_many = bgHowManyAdd();
				if ($how_many > 0){
					$PID_array = bgHowManyAdd_PID();
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
							
						echo "<li>Script running for $how_long\n</li>";
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
			
			if ($tab >= 0){
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
			
			echo "
			<div id=\"accordion\">";
			
			for ($i=0; $i < $nrows; $i++) {
				$row = mysqli_fetch_array($result);
				extract($row);

				$no_files = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE FilesToAddID='$FilesToAddID'", $connection);
				$no_files_done = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE FilesToAddID='$FilesToAddID' AND ReturnCode='0'", $connection);
				$no_files_todo = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE FilesToAddID='$FilesToAddID' AND ReturnCode='1'", $connection);
				$no_files_inprogress = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE FilesToAddID='$FilesToAddID' AND ReturnCode='2'", $connection);
				$no_files_error = query_one("SELECT COUNT(*) FROM FilesToAddMembers WHERE FilesToAddID	='$FilesToAddID' AND ReturnCode='9'", $connection);
				
				$percent = floor(($no_files_done/$no_files)*100);
				
				echo "
				
				<h3><a href=\"#\">Path: $FilesPath ($percent% completed)</a></h3>
				<div>
				
				<p>Path: $FilesPath<br>Started on: <strong>$StartTime</strong> by $UserFullname<br>
					<ul>
					 <li>Completed files: $no_files_done</li>
					 <li>Files being processed: $no_files_inprogress</li>
					 <li>Files yet to add: $no_files_todo</li>
					 <li>Files with errors: $no_files_error</li>
					</ul>\n";
				
				
				#don't show table with details unless it is requested or there is a problem
				$this_show = FALSE;
				
				if ($no_files_error > 0 || $no_files_inprogress > 0 || $no_files_todo > 0){
					$this_show = TRUE;
					}

				if ($tab == $i){
					$this_show = TRUE;
					}

				if ($this_show){
					echo "<table>\n";
					echo "<tr><td>
					&nbsp;</td><td><strong>File</strong></td><td>&nbsp;</td><td><strong>Status</strong></td></tr>\n";

					$query_1 = "SELECT * from FilesToAddMembers WHERE FilesToAddID='$FilesToAddID' ORDER BY ReturnCode DESC";
					$result_1 = mysqli_query($connection, $query_1)
						or die (mysqli_error($connection));
					$nrows_1 = mysqli_num_rows($result_1);
					if ($nrows_1>0){
						for ($j=0; $j < $nrows_1; $j++) {
							$row_1 = mysqli_fetch_array($result_1);
							extract($row_1);
							echo "<tr><td>&nbsp;</td><td>$FullPath</td><td>&nbsp;</td>\n";

							if ($ReturnCode == 1){
								echo "<td> <img src=\"images/database.png\"> To add</td></tr>\n";
								}
							elseif ($ReturnCode == 0){
								echo "<td> <img src=\"images/accept.png\"> Added to archive</td></tr>\n";
								}
							elseif ($ReturnCode == 2){
								echo "<td> <img src=\"images/ajax-loader.gif\"> Working... ";
								$mins_working = query_one("SELECT TIMESTAMPDIFF(MINUTE, TimeStamp, NOW()) FROM FilesToAddMembers
										WHERE ToAddMemberID='$ToAddMemberID'", $connection);
								if ($mins_working > 5){
									echo "(working for more than five minutes, <a href=\"file_manager.php?ToAddMemberID=$ToAddMemberID&tab=$i&action=1\" title=\"Reset\">reset</a> or 
										<a href=\"file_manager.php?ToAddMemberID=$ToAddMemberID&tab=$i&action=2\" title=\"Reset\">delete</a>?)";
									}
								echo "</td></tr>\n";
								}
							elseif ($ReturnCode == 9){
								echo "<td> <img src=\"images/error.png\"> Error: $ErrorCode<br> 
									<a href=\"file_manager.php?ToAddMemberID=$ToAddMemberID&tab=$i&action=1\" title=\"Reset\">reset</a> |
									<a href=\"file_manager.php?ToAddMemberID=$ToAddMemberID&tab=$i&action=2\" title=\"Reset\">delete</a>
									</td></tr>\n";
								}
							}
						}
					echo "</table>\n";
					}
				else{
					echo "<p><a href=\"file_manager.php?tab=$i\" title=\"Show details\">Show details</a>";
					}
					
					
				echo "</div>\n";
				}
			echo "</div><!-- End accordion -->\n";
				
			}
		else {
			echo "<p>There are no files waiting to be added.
				<p><a href=\"add.php\">Add files</a>.\n";
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
if ($special_noprocess == FALSE){
	add_in_background($absolute_dir, $connection);
	}
?>

</body>
</html>
