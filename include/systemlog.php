<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");
?>

</head>
<body>

<?php

if (!isset($_GET["LogTypeQ"])){
	$LogTypeQ = "";
	}
else{
	$LogTypeQ = $_GET["LogTypeQ"];
	}

$query = "SELECT DISTINCT LogType from PumilioLog ORDER BY LogType";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
$nrows = mysqli_num_rows($result);

if ($nrows == 0){
	echo "<p>There is nothing in the log.";
	}
else {
	echo "<form action=\"systemlog.php\" method=\"GET\">
		<select name=\"LogTypeQ\" class=\"ui-state-default ui-corner-all\">";

		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			if ($LogType==1){
				}
			elseif ($LogType==70){
				$LogCat = "FLAC processing error";
				}
			elseif ($LogType==80){
				$LogCat = "SoX processing error";
				}
			elseif ($LogType==90){
				$LogCat = "File was deleted";
				}
			elseif ($LogType==91){
				$LogCat = "File not processable";
				}
			elseif ($LogType==98){
				$LogCat = "Other file errors";
				}
			elseif ($LogType==99){
				$LogCat = "File not found";
				}
			//How many sounds associated with that source
			echo "<option value=\"$LogType\">$LogCat</option>\n";
			}

		echo "</select> 
		<input type=submit value=\" Filter log \" class=\"fg-button ui-state-default ui-corner-all\">
	</form><br><br>";

	if ($LogTypeQ!=""){
		$query = "SELECT *, DATE_FORMAT(timestamp,'%d-%b-%Y %T') AS timestamp from PumilioLog WHERE LogType='$LogTypeQ' ORDER BY timestamp DESC";
		}
	else {
		$query = "SELECT *, DATE_FORMAT(timestamp,'%d-%b-%Y %T') AS timestamp from PumilioLog ORDER BY timestamp DESC";
		}

	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	echo "<table>";
	echo "<tr>
		<td><strong>LogID</strong></td>
		<td><strong>User</strong></td>
		<td><strong>SoundID</strong></td>
		<td><strong>Timestamp</strong></td>
		<td><strong>Text of log message</strong></td>
	      </tr>\n";
	      
	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		$UserName = query_one("SELECT UserName FROM Users WHERE UserID='$UserID'", $connection);
		//How many sounds associated with that source
		echo "<tr>
			<td>$LogID</td>
			<td>$UserName</td>
			<td><a href=\"db_filedetails.php?SoundID=$SoundID\">$SoundID</a></td>
			<td>$timestamp</td>
			<td>$LogText</td>
		     </tr>\n";
		}
	echo "</table>";
	}
?>

<br><p><a href="#" onClick="window.close();">Close window</a>

</div>

</body>
</html>
