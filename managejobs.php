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

$JobID = filter_var($_GET["JobID"], FILTER_SANITIZE_NUMBER_INT);
$c = filter_var($_GET["c"], FILTER_SANITIZE_NUMBER_INT);
$NewPriority = filter_var($_GET["Priority"], FILTER_SANITIZE_NUMBER_INT);
$NewStatus = filter_var($_GET["Status"], FILTER_SANITIZE_NUMBER_INT);
$e = filter_var($_GET["e"], FILTER_SANITIZE_NUMBER_INT);
$r = filter_var($_GET["r"], FILTER_SANITIZE_NUMBER_INT);
$machine = filter_var($_GET["machine"], FILTER_SANITIZE_STRING);
$reset = filter_var($_GET["reset"], FILTER_SANITIZE_NUMBER_INT);
$QueueID = filter_var($_GET["QueueID"], FILTER_SANITIZE_NUMBER_INT);

if (isset($_GET["pageno"])){
	$pageno=filter_var($_GET["pageno"], FILTER_SANITIZE_NUMBER_INT);
	}
else {
	$pageno=0;
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Manage Jobs</title>\n";

#Get CSS
require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
?>

</head>
<body onblur="window.focus();">

<div style="padding: 10px;">

<?php

#How many items to show in each page
$items_per_page=100;

#Reset an item
if ($reset == 1) {
	$result_newstatus = mysqli_query($connection, "UPDATE Queue SET Status='0' WHERE QueueID='$QueueID' LIMIT 1")
		or die (mysqli_error($connection));
	}

if ($c==1) {
	$result1=mysqli_query($connection, "SELECT Queue.QueueID as QueueID FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID")
		or die (mysqli_error($connection));
	$nrows1 = mysqli_num_rows($result1);

	for ($ii=0;$ii<$nrows1;$ii++) {
		$row1 = mysqli_fetch_array($result1);
		extract($row1);
		$result_newpriority = mysqli_query($connection, "UPDATE Queue SET Priority='$NewPriority' WHERE QueueID='$QueueID' LIMIT 1")
			or die (mysqli_error($connection));
		}
	}
elseif ($c==2) {
	$result1=mysqli_query($connection, "SELECT Queue.QueueID AS QueueID, Queue.Status AS CurrentStatus FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID")
		or die (mysqli_error($connection));
	$nrows1 = mysqli_num_rows($result1);

	for ($ii=0;$ii<$nrows1;$ii++) {
		$row1 = mysqli_fetch_array($result1);
		extract($row1);
		if ($CurrentStatus!=2 && $CurrentStatus!=1) {
			$result_newstatus = mysqli_query($connection, "UPDATE Queue SET Status='$NewStatus' WHERE QueueID='$QueueID' LIMIT 1")
				or die (mysqli_error($connection));
			}
		}
	}

if ($e==1) {
	$no_items=query_one("SELECT COUNT(*) as no_items FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='3'", $connection);

	if ($no_items>$items_per_page) {
		$paging=1;
		$sql_limit=$pageno*$items_per_page;
		$query_limit=" LIMIT $sql_limit, $items_per_page";
		}

	$result=mysqli_query($connection, "SELECT 
		*, DATE_FORMAT(LastChange,'%d-%b-%y %T') AS LastChange_f, DATE_FORMAT(ClaimedDate,'%d-%b-%y %T') AS ClaimedDate_f, DATE_FORMAT(ProcessDoneDate,'%d-%b-%y %T') AS ProcessDoneDate_f, TIMEDIFF(ProcessDoneDate,ClaimedDate) AS RunningTime
		FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='3' $query_limit")
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	$JobName=query_one("SELECT JobName FROM QueueJobs WHERE JobID='$JobID' LIMIT 1", $connection);

	echo "<p style=\"float: right;\">[<a href=\"managejobs.php?JobID=$JobID\">Refresh information</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=1\">Jobs running</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=3\">Machines</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=1\">Script errors</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=2\">Missing file errors</a>]
		<h4>Job \"$JobName\" (ID: $JobID):</h4>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items){
				$p_to=$no_items;
				}
		
			if ($pageno==$p){
				echo "[$p_from-$p_to] &nbsp; ";
				}
			else{
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&e=1\">$p_from-$p_to</a>] &nbsp; ";
				}
			}
		echo "</div>\n";
		}

	echo "<hr noshade>";

		echo "<p>SoundID - Priority - Status
		<hr noshade>
		<p>\n";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		echo "<a href=\"viewlog.php?QueueID=$QueueID\" title=\"View the process log for this file\">$SoundID</a> - $Priority - ";
		if ($Status==0)
			echo "<strong style=\"color: black;\">Waiting";
		elseif ($Status==1)
			echo "<strong style=\"color: gray;\">Running";
		elseif ($Status==2)
			echo "<strong style=\"color: green;\">Completed";
		elseif ($Status==3)
			echo "<strong style=\"color: red;\">There was an error";
		elseif ($Status==4)
			echo "<strong style=\"color: red;\">On hold";
		elseif ($Status==5)
			echo "<strong style=\"color: red;\">The file could not be found";
				
		echo "</strong>";

		if (isset($ComputerDone))
			echo " - Computer: <a href=\"managejobs.php?JobID=$JobID&machine=$ComputerDone&r=2\">$ComputerDone</a>";

		echo "<br>";
	
		if ($Status==2) {
			echo "Process took: $RunningTime<br>";
			}
		else {
			echo "Last change: $LastChange_f<br>\n\n";
			}
		echo "<a href=\"managejobs.php?JobID=$JobID&e=1&reset=1&QueueID=$QueueID\">Reset this item</a><br>";
		}
	
	echo "<hr noshade>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&e=1\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}
	
	echo "<hr noshade>";
	}
elseif ($e==2) {
	$no_items=query_one("SELECT COUNT(*) as no_items FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='5'", $connection);

	if ($no_items>$items_per_page) {
		$paging=1;
		$sql_limit=$pageno*$items_per_page;
	
		$query_limit=" LIMIT $sql_limit, $items_per_page";
		}

	$result=mysqli_query($connection, "SELECT 
		*, DATE_FORMAT(LastChange,'%d-%b-%y %T') AS LastChange_f, DATE_FORMAT(ClaimedDate,'%d-%b-%y %T') AS ClaimedDate_f, DATE_FORMAT(ProcessDoneDate,'%d-%b-%y %T') AS ProcessDoneDate_f, TIMEDIFF(ProcessDoneDate,ClaimedDate) AS RunningTime
		FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='5' $query_limit")
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	$JobName=query_one("SELECT JobName FROM QueueJobs WHERE JobID='$JobID' LIMIT 1", $connection);

	echo "<p style=\"float: right;\">[<a href=\"managejobs.php?JobID=$JobID\">Refresh information</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=1\">Jobs running</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=3\">Machines</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=1\">Script errors</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=2\">Missing file errors</a>]

		<h4>Job \"$JobName\" (ID: $JobID):</h4>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&e=2\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}

	echo "<hr noshade>";

		echo "<p>SoundID - Priority - Status
		<hr noshade>
		<p>";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		echo "<a href=\"viewlog.php?QueueID=$QueueID\" title=\"View the process log for this file\">$SoundID</a> - $Priority - ";
		if ($Status==0)
			echo "<strong style=\"color: black;\">Waiting";
		elseif ($Status==1)
			echo "<strong style=\"color: gray;\">Running";
		elseif ($Status==2)
			echo "<strong style=\"color: green;\">Completed";
		elseif ($Status==3)
			echo "<strong style=\"color: red;\">There was an error";
		elseif ($Status==4)
			echo "<strong style=\"color: red;\">On hold";
		elseif ($Status==5)
			echo "<strong style=\"color: red;\">The file could not be found";
				
		echo "</strong>";

		if (isset($ComputerDone))
			echo " - Computer: <a href=\"managejobs.php?JobID=$JobID&machine=$ComputerDone&r=2\">$ComputerDone</a>";

		echo "<br>";
	
		if ($Status==2) {
			echo "Process took: $RunningTime<br>";
			}
		else {
			echo "Last change: $LastChange_f<br>";
			}
		echo "<a href=\"managejobs.php?JobID=$JobID&e=2&reset=1&QueueID=$QueueID\">Reset this item</a><br>";
		}
	
	echo "<hr noshade>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&e=2\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}
	
	echo "<hr noshade>";
}
elseif ($r==1) {
	$no_items=query_one("SELECT COUNT(*) as no_items FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='1'", $connection);

	if ($no_items>$items_per_page) {
		$paging=1;
		$sql_limit=$pageno*$items_per_page;
	
		$query_limit=" LIMIT $sql_limit, $items_per_page";

		}

	$result=mysqli_query($connection, "SELECT 
		*, DATE_FORMAT(LastChange,'%d-%b-%y %T') AS LastChange_f, DATE_FORMAT(ClaimedDate,'%d-%b-%y %T') AS ClaimedDate_f, 
			DATE_FORMAT(ProcessDoneDate,'%d-%b-%y %T') AS ProcessDoneDate_f, TIMEDIFF(ProcessDoneDate,ClaimedDate) AS RunningTime, 
			TIME_TO_SEC(TIMEDIFF(NOW(), ClaimedDate)) AS JobRunning
		FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='1' $query_limit")
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	$JobName=query_one("SELECT JobName FROM QueueJobs WHERE JobID='$JobID' LIMIT 1", $connection);

	echo "<p style=\"float: right;\">[<a href=\"managejobs.php?JobID=$JobID\">Refresh information</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=1\">Jobs running</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=3\">Machines</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=1\">Script errors</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=2\">Missing file errors</a>]

		<h4>Job \"$JobName\" (ID: $JobID):</h4>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
	
			if ($p_to>$no_items)
				$p_to=$no_items;
	
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&r=1\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}

	echo "<hr noshade>";

		echo "<p>SoundID - Priority - Status
		<hr noshade>
		<p>";

	#Get an average running time to detect problems
		#Get number of done
		$no_items_done=query_one("SELECT COUNT(*) FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='2'", $connection);
		$no_items_done_90 = round($no_items_done*0.90);
		$ave_RunningTime=query_one("SELECT AVG(TIME_TO_SEC(TIMEDIFF(ProcessDoneDate, ClaimedDate))) FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='2'", $connection);

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);

		$warning_color=0;

		if ($JobRunning > ($ave_RunningTime*5)){
			$warning_color = "1";
			}
			
		if ($JobRunning > ($ave_RunningTime*10)){
			$warning_color = "2";
			}

		if ($warning_color == "1"){
			echo "<div class=\"notice\">";}
		elseif ($warning_color == "2"){
			echo "<div class=\"error\">";}

		echo "<a href=\"viewlog.php?QueueID=$QueueID\" title=\"View the process log for this file\">$SoundID</a> - $Priority - ";
		if ($Status==0)
			echo "<strong style=\"color: black;\">Waiting";
		elseif ($Status==1)
			echo "<strong style=\"color: gray;\">Running";
		elseif ($Status==2)
			echo "<strong style=\"color: green;\">Completed";
		elseif ($Status==3)
			echo "<strong style=\"color: red;\">There was an error";
		elseif ($Status==4)
			echo "<strong style=\"color: red;\">On hold";
		elseif ($Status==5)
			echo "<strong style=\"color: red;\">The file could not be found";
				
		echo "</strong>";

		if (isset($ComputerDone))
			echo " - Computer: <a href=\"managejobs.php?JobID=$JobID&machine=$ComputerDone&r=2\">$ComputerDone</a>";

		echo "<br>";
	
		if ($Status==2) {
			echo "Process took: $RunningTime<br>";
			}
		else {
			echo "Last change: $LastChange_f<br>";
			}

		echo "<em>Job has been running for $JobRunning seconds.</em><br>";
		
		if ($warning_color == "1" || $warning_color == "2"){
			echo "<a href=\"managejobs.php?JobID=$JobID&r=1&reset=1&QueueID=$QueueID\">Reset this item</a></div>";
			}
		
		}
	
	echo "<hr noshade>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&r=1\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}
	
	echo "<hr noshade>";
	}
elseif ($r==2) {
	$no_items=query_one("SELECT COUNT(*) as no_items FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.ComputerDone='$machine'", $connection);

	if ($no_items>$items_per_page) {
		$paging=1;
		$sql_limit=$pageno*$items_per_page;
	
		$query_limit=" LIMIT $sql_limit, $items_per_page";
		}

	$result=mysqli_query($connection, "SELECT 
		*, DATE_FORMAT(LastChange,'%d-%b-%y %T') AS LastChange_f, DATE_FORMAT(ClaimedDate,'%d-%b-%y %T') AS ClaimedDate_f, DATE_FORMAT(ProcessDoneDate,'%d-%b-%y %T') AS ProcessDoneDate_f, TIMEDIFF(ProcessDoneDate,ClaimedDate) AS RunningTime
		FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.ComputerDone='$machine' $query_limit")
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	$JobName=query_one("SELECT JobName FROM QueueJobs WHERE JobID='$JobID' LIMIT 1", $connection);

	echo "<p style=\"float: right;\">[<a href=\"managejobs.php?JobID=$JobID\">Refresh information</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=1\">Jobs running</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=3\">Machines</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=1\">Script errors</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=2\">Missing file errors</a>]

		<h4>Job \"$JobName\" (ID: $JobID):</h4>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&r=2&machine=$machine\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}

	echo "<hr noshade>";

		$ave_RunningTime=round(query_one("SELECT AVG(TIME_TO_SEC(TIMEDIFF(ProcessDoneDate, ClaimedDate))) FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='2' AND Queue.ComputerDone='$machine'", $connection));

		echo "<p>$machine has run $no_items items in this job, taking an average of $ave_RunningTime secs per item
		<p>SoundID - Priority - Status
		<hr noshade>
		<p>";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		echo "<a href=\"viewlog.php?QueueID=$QueueID\" title=\"View the process log for this file\">$SoundID</a> - $Priority - ";
		if ($Status==0)
			echo "<strong style=\"color: black;\">Waiting";
		elseif ($Status==1)
			echo "<strong style=\"color: gray;\">Running";
		elseif ($Status==2)
			echo "<strong style=\"color: green;\">Completed";
		elseif ($Status==3)
			echo "<strong style=\"color: red;\">There was an error";
		elseif ($Status==4)
			echo "<strong style=\"color: red;\">On hold";
		elseif ($Status==5)
			echo "<strong style=\"color: red;\">The file could not be found";
				
		echo "</strong>";

		if (isset($ComputerDone))
			echo " - Computer: <a href=\"managejobs.php?JobID=$JobID&machine=$ComputerDone&r=2\">$ComputerDone</a>";

		echo "<br>";
	
		if ($Status==2) {
			echo "Process took: $RunningTime<br>";
			}
		else {
			echo "Last change: $LastChange_f<br>";
			}
		}
	
	echo "<hr noshade>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p&r=2&machine=$machine\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}
	
	echo "<hr noshade>";
	}
elseif ($r==3) {
	$result=mysqli_query($connection, "SELECT DISTINCT Queue.ComputerDone AS machine FROM Queue, QueueJobs 
				WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='2'")
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	$JobName=query_one("SELECT JobName FROM QueueJobs WHERE JobID='$JobID' LIMIT 1", $connection);

	echo "<p style=\"float: right;\">[<a href=\"managejobs.php?JobID=$JobID\">Refresh information</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=1\">Jobs running</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=3\">Machines</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=1\">Script errors</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=2\">Missing file errors</a>]

	<h4>Job \"$JobName\" (ID: $JobID):</h4>
	<p><br><br>Computer - Items done - Average time per item (secs)";

	echo "<hr noshade>";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		
		$no_items=query_one("SELECT COUNT(*) as no_items FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID 
				AND Queue.ComputerDone='$machine'", $connection);
		$ave_RunningTime=round(query_one("SELECT AVG(TIME_TO_SEC(TIMEDIFF(ProcessDoneDate, ClaimedDate))) FROM Queue, QueueJobs 
				WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID AND Queue.Status='2' AND Queue.ComputerDone='$machine'", $connection));

		echo "<p>$machine - $no_items - $ave_RunningTime";

	}
	echo "<hr noshade>";

	}
else {
	$no_items=query_one("SELECT COUNT(*) as no_items FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID", $connection);

	if ($no_items>50) {
		$paging=1;
		$sql_limit=$pageno*$items_per_page;
	
		$query_limit=" LIMIT $sql_limit, $items_per_page";
		}

	$result=mysqli_query($connection, "SELECT 
		*, DATE_FORMAT(LastChange,'%d-%b-%y %T') AS LastChange_f, DATE_FORMAT(ClaimedDate,'%d-%b-%y %T') AS ClaimedDate_f, DATE_FORMAT(ProcessDoneDate,'%d-%b-%y %T') AS ProcessDoneDate_f, TIMEDIFF(ProcessDoneDate,ClaimedDate) AS RunningTime
		FROM Queue, QueueJobs WHERE Queue.JobID='$JobID' AND Queue.JobID=QueueJobs.JobID $query_limit")
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	$JobName=query_one("SELECT JobName FROM QueueJobs WHERE JobID='$JobID' LIMIT 1", $connection);

	echo "<p style=\"float: right;\">[<a href=\"managejobs.php?JobID=$JobID\">Refresh information</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=1\">Jobs running</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&r=3\">Machines</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=1\">Script errors</a>]<br>[<a href=\"managejobs.php?JobID=$JobID&e=2\">Missing file errors</a>]
		<h4>Job \"$JobName\" (ID: $JobID):</h4>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}

	echo "<hr noshade>";

		echo "<p>SoundID - Priority - Status
		<hr noshade>
		<p>";

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		echo "<a href=\"viewlog.php?QueueID=$QueueID\" title=\"View the process log for this file\">$SoundID</a> - $Priority - ";
		if ($Status==0)
			echo "<strong style=\"color: black;\">Waiting";
		elseif ($Status==1)
			echo "<strong style=\"color: gray;\">Running";
		elseif ($Status==2)
			echo "<strong style=\"color: green;\">Completed";
		elseif ($Status==3)
			echo "<strong style=\"color: red;\">There was an error";
		elseif ($Status==4)
			echo "<strong style=\"color: red;\">On hold";
		elseif ($Status==5)
			echo "<strong style=\"color: red;\">The file could not be found";
				
		echo "</strong>";

		if (isset($ComputerDone))
			echo " - Computer: <a href=\"managejobs.php?JobID=$JobID&machine=$ComputerDone&r=2\">$ComputerDone</a>";

		echo "<br>";
	
		if ($Status==2) {
			echo "Process took: $RunningTime<br>";
			}
		else {
			echo "Last change: $LastChange_f<br>";
			}
		}
	
	echo "<hr noshade>";

	if ($paging) {
		echo "<div style=\"height:100px; overflow:scroll;\"><p>Items: ";
		$no_pages=ceil($no_items/$items_per_page);
		for ($p=0;$p<$no_pages;$p++) {
			$p_to=($p+1)*$items_per_page;
			$p_from=$p_to-$items_per_page;
		
			if ($p_to>$no_items)
				$p_to=$no_items;
		
			if ($pageno==$p)
				echo "[$p_from-$p_to] &nbsp; ";
			else
				echo "[<a href=\"managejobs.php?JobID=$JobID&pageno=$p\">$p_from-$p_to</a>] &nbsp; ";
			}
		echo "</div>";
		}
	
	echo "<hr noshade>";

	echo "<p><strong>Change the priority for this job</strong>
		<form method=\"GET\" action=\"managejobs.php\">
			<input type=\"hidden\" name=\"c\" value=\"1\">
			<input type=\"hidden\" name=\"JobID\" value=\"$JobID\">
			New priority: <select name=\"Priority\" class=\"ui-state-default ui-corner-all\">";
			for ($p=0;$p<4;$p++) {
				if ($p==$Priority)
					echo "<option value=\"$p\" SELECTED>$p</option>\n";
				else
					echo "<option value=\"$p\">$p</option>\n";
				}
			echo "</select> <p>Lower values have higher priority.
			<p><input type=submit value=\" Change Priority \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";

	echo "<hr noshade>";

	echo "<p><strong>Change the status for this job</strong>
		<form method=\"GET\" action=\"managejobs.php\">
			<input type=\"hidden\" name=\"c\" value=\"2\">
			<input type=\"hidden\" name=\"JobID\" value=\"$JobID\">
			New status: <select name=\"Status\" class=\"ui-state-default ui-corner-all\">
	
				<option value=\"0\">Waiting</option>
				<option value=\"4\">On Hold</option>
		
			</select> 
			<p>Completed items will not be changed. To re-queue completed items, click on the SoundID.</p>
			<p><input type=submit value=\" Change Status \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";
	
	echo "<hr noshade>";
	}
?>

<br><br><p><a href="#" onClick="window.close();">Close window</a>

</div>
</body>
</html>
