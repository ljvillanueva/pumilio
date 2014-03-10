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

$type=filter_var($_GET["type"], FILTER_SANITIZE_STRING);
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
$startDate=filter_var($_GET["startDate"], FILTER_SANITIZE_STRING);
$endDate=filter_var($_GET["endDate"], FILTER_SANITIZE_STRING);
$data=filter_var($_GET["data"], FILTER_SANITIZE_STRING);

if ($startDate != ""){
	$startDate = date('Y-m-d', strtotime($startDate));
	$endDate = date('Y-m-d', strtotime($endDate));
	}
else {
	$startDate="";
	$endDate="";
	}

$type_q=$type;
$SiteID_q=$SiteID;
$ColID_q=$ColID;
$DateFrom_q=$DateFrom;
$DateTo_q=$DateTo;
$data_q=$data;

#In case dates fall outside, reload
if ($type == "col"){
	$db_sel = "ColID";
	$db_val = $ColID;
	}
elseif ($type == "site"){
	$db_sel = "SiteID";
	$db_val = $SiteID;
	}

if ($startDate != ""){
	$no_files = query_one("SELECT COUNT(*) FROM Sounds WHERE Sounds.SoundStatus!='9' AND
			$db_sel='$db_val' AND Sounds.Date >= '$startDate' AND Sounds.Date <= '$endDate'", $connection);

	if ($no_files == 0){
		header("Location: qa.php?type=$type&ColID=$ColID&SiteID=$SiteID&data=$data");
		die();
		}
	}


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Quality Control figures</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

echo "<script language=\"javascript\" type=\"text/javascript\" src=\"js/flot/jquery.flot.js\"></script>";


if ($data == "Duration"){
	$data_d = "Duration (in seconds)";
	$Rdata = FALSE;
	}
elseif ($data == "FileSize"){
	$data_d = "File size (in kb)";
	$Rdata = FALSE;
	}
elseif ($data == "ADI"){
	$data_d = "Acoustic Diversity Index";
	$Rdata = TRUE;
	}
elseif ($data == "Gini"){
	$data_d = "Gini coefficient";
	$Rdata = TRUE;
	}
elseif ($data == "diffspec"){
	$data_d = "Difference between two frequency spectra";
	$Rdata = TRUE;
	}


if ($type != ""){
	#Flot
	echo "
	<script type=\"text/javascript\">
	$(function () {
	    var d = [";
    
		#Set time to UTC because flot only displays in UTC
		query_one("SET SESSION time_zone = '+0:00'", $connection);
		
		if ($startDate == ""){
			$startDate = query_one("SELECT Date FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9'
					ORDER BY Date LIMIT 1", $connection);
			$endDate = query_one("SELECT Date FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9'
					ORDER BY Date DESC LIMIT 1", $connection);
			}
		else {
			$startDate2 = query_one("SELECT Date FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9'
					ORDER BY Date LIMIT 1", $connection);
			$endDate2 = query_one("SELECT Date FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9'
					ORDER BY Date DESC LIMIT 1", $connection);
			if ((strtotime($startDate) - strtotime($startDate2)) > 0){
				$startDate = $startDate2;
				}

			if ((strtotime($endDate2) - strtotime($endDate)) < 0){
				$endDate = $endDate2;
				}
			}
		
		if ($Rdata){
			$data = query_one("SELECT DISTINCT Stat FROM SoundsStatsResults WHERE Stat LIKE '$data_q%left' LIMIT 1", $connection);
			$query = "SELECT Sounds.SoundID, Sounds.QualityFlagID, 
				DATE_FORMAT(Sounds.Date,'%d-%b-%Y') AS HumanDate, TIME_FORMAT(Sounds.Time,'%H:%i:%s') AS HumanTime,
				UNIX_TIMESTAMP(CONCAT(Sounds.Date, ' ', Sounds.Time)) AS UnixTime,
				SoundsStatsResults.StatValue AS this_data
				FROM Sounds, SoundsStatsResults 
				WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9' AND
				Sounds.Date >= '$startDate' AND Sounds.Date <= '$endDate' AND
				Sounds.SoundID=SoundsStatsResults.SoundID AND
				SoundsStatsResults.Stat='$data'
				ORDER BY Date, Time";
			#echo $query;
			}
		else{
			$query = "SELECT Sounds.SoundID AS SoundID, Sounds.QualityFlagID AS QualityFlagID, 
				Sounds.ColID AS ColID, Sounds.DirID AS DirID, SoundsImages.ImageFile AS ImageFile,
				$data AS this_data, DATE_FORMAT(Date,'%d-%b-%Y') AS HumanDate,
				TIME_FORMAT(Time,'%H:%i:%s') AS HumanTime, 
				UNIX_TIMESTAMP(CONCAT(Date, ' ', Time)) AS UnixTime
				FROM Sounds, SoundsImages
				WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9' AND
				Sounds.Date >= '$startDate' AND Sounds.Date <= '$endDate' AND
				Sounds.SoundID=SoundsImages.SoundID AND
				SoundsImages.ImageType='spectrogram-small'
				ORDER BY Date, Time";
			}
		
		
		$result=query_several($query, $connection);
		$nrows = mysqli_num_rows($result);

		if ($nrows>0) {
			for ($r=0;$r<$nrows;$r++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				$UnixTime = $UnixTime * 1000;
				
				#Convert size to kilobytes
				if ($data == "FileSize"){
					$this_data = round($this_data / 1024, 1);
					}
				
				if ($r == ($nrows-1)){
					echo "[$UnixTime, $this_data, '$SoundID', '$QualityFlagID', '$HumanDate', '$HumanTime', '$ColID', '$DirID', '$ImageFile'] ";
					}
				else{
					echo "[$UnixTime, $this_data, '$SoundID', '$QualityFlagID', '$HumanDate', '$HumanTime', '$ColID', '$DirID', '$ImageFile'], ";
					}
				}
			}
			
	echo "];\n";
	

	echo "
	var plot = $.plot($(\"#placeholder\"), [ { data: d, label: \"$data_d\"} ],
		{xaxis: {mode: \"time\"},
		series: {lines: { show: true },	points: { show: true },	color: '#2779AA'},
		grid: { hoverable: true, clickable: true }});


	$(\"#placeholder\").bind(\"plotclick\", function (event, pos, item) {
		if (item) {
		dvalue = $(this).find('d').text()
		$(\"#SoundIDtocheck\").val(item.series.data[item.dataIndex][2]);
		plot.highlight(item.series, item.datapoint);
		$(\"#clickdata\").html('<div style=\"height:156;\"><img src=\"$app_url/sounds/images/' + item.series.data[item.dataIndex][6] + '/' + item.series.data[item.dataIndex][7] + '/' + '/' + item.series.data[item.dataIndex][8] + '\" width=\"300\" height=\"150\" style=\"float:right;\">You clicked the point for the SoundID ' + item.series.data[item.dataIndex][2] + ', recorded on ' + item.series.data[item.dataIndex][4] + ' ' + item.series.data[item.dataIndex][5] + ',<br>which had a $data_d of ' + item.series.data[item.dataIndex][1] + ' and a QualityFlag of ' + item.series.data[item.dataIndex][3] + '.<br><br><form action=\"db_filedetails.php\" method=\"GET\" id=\"openfile\" target=\"_blank\"><input type=\"hidden\" name=\"SoundID\" value=\"' + item.series.data[item.dataIndex][2] + '\" id=\"SoundIDtocheck\" /><input type=submit value=\" Open file page \" class=\"fg-button ui-state-default ui-corner-all\" /></form></div>');
		$(\"#clickdata\").addClass(\"notice\");
	        }
	    });
	});
	</script>\n";
	}

$d = 0;
if (isset($_GET["d"])) {
	if ($_GET["d"] == 1) {
		$d = 1;
		echo "<script type=\"text/javascript\">
		setTimeout(function() {
		$('#updated_div').fadeOut('slow');
		}, 2000); // <-- time in milliseconds
		</script>\n";
		}
	}
	


if ($type != ""){
	$DateLow=query_one("SELECT DATE_FORMAT(Date,'%Y, %c-1, %e') FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9' ORDER BY Date LIMIT 1", $connection);
	$DateHigh=query_one("SELECT DATE_FORMAT(Date,'%Y, %c-1, %e') FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9' ORDER BY Date DESC LIMIT 1", $connection);
	$DateLow1=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9' ORDER BY Date LIMIT 1", $connection);
	$DateHigh1=query_one("SELECT DATE_FORMAT(Date,'%d-%b-%Y') FROM Sounds WHERE $db_sel='$db_val' AND Sounds.SoundStatus!='9' ORDER BY Date DESC LIMIT 1", $connection);

	#from http://jsbin.com/orora3/75/
	echo "	
	<script type=\"text/javascript\">
	$(function() {
		var dates = $( \"#startDate, #endDate\" ).datepicker({
			minDate: new Date($DateLow),
			maxDate: new Date($DateHigh),
			numberOfMonths: 3,
			changeYear: true,
			dateFormat: 'dd-MM-yy',
			onSelect: function( selectedDate ) {
				var option = this.id == \"startDate\" ? \"minDate\" : \"maxDate\",
				instance = $( this ).data( \"datepicker\" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
				dates.not( this ).datepicker( \"option\", option, date );
			}
		});
	});

	</script>
	";
	}

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
		<h3>Quality control figures</h3>
		<p>This page lets you see data associated with each file to detect problems.</p>

		<?php
		
		if ($d == 1) {
			echo "<div class=\"success\" id=\"updated_div\">The database was updated.</div>";
			}
			
		echo "<div style=\"margin-left: 10px;\">";
				
		//Select a sound collection
		$no_Collections=query_one("SELECT COUNT(*) FROM Collections", $connection);
		$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9'", $connection);
		$no_sites=query_one("SELECT COUNT(DISTINCT SiteID) FROM Sounds WHERE SoundStatus!='9'", $connection);
		$total_no_sounds=$no_sounds;
		if ($no_Collections==0) {
			echo "This archive has no Collections yet.";
			}
		elseif ($no_sounds==0) {
			echo "This archive has no sounds yet.";
			}
		else {
			echo "<p>Select to browse using a collection or a site:<br>";
			echo "<form action=\"qa.php\" method=\"GET\">";
			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			
			if ($type == "" || $type == "col"){
				echo "<input type=\"radio\" name=\"type\" value=\"col\" checked=\"checked\" />";
				}
			else{	
				echo "<input type=\"radio\" name=\"type\" value=\"col\" />";
				}
			
			echo " Collection: 
			<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">";

			$ColIDq = $ColID;
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				//How many sounds associated with that source
				$this_no_sounds=query_one("SELECT COUNT(*) as this_no_sounds FROM Sounds 
					WHERE ColID='$ColID' AND SoundStatus!='9'", $connection);
				if ($this_no_sounds>0) {
					$this_no_sounds_f = number_format($this_no_sounds);
					if ($ColID == $ColIDq){
						echo "<option value=\"$ColID\" SELECTED>$CollectionName - $this_no_sounds_f sound files</option>\n";
						}
					else{
						echo "<option value=\"$ColID\">$CollectionName - $this_no_sounds_f sound files</option>\n";
						}
					}
				}
			echo "</select>";
			}
			
		#Sites
		$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SoundStatus!='9'", $connection);
		$no_sites=query_one("SELECT COUNT(DISTINCT SiteID) FROM Sounds WHERE SoundStatus!='9'", $connection);

		if ($no_sites==0) {
			echo "This archive has no sites yet.";
			}
		elseif ($no_sounds==0) {
			echo "This archive has no sounds yet.";
			}
		else {
			$query = "SELECT * from Sites ORDER BY SiteName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			echo "<br>";
			
			if ($type == "site"){
				echo "<input type=\"radio\" name=\"type\" value=\"site\" checked=\"checked\" />";
				}
			else{	
				echo "<input type=\"radio\" name=\"type\" value=\"site\" />";
				}
			
			echo "Site: 
			<select name=\"SiteID\" class=\"ui-state-default ui-corner-all\">";
			
			$SiteIDq = $SiteID;
			for ($i=0;$i<$nrows;$i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				//How many sounds associated with that site
				$this_no_sounds=query_one("SELECT COUNT(*) FROM Sounds WHERE SiteID='$SiteID' AND SoundStatus!='9'", $connection);
				if ($this_no_sounds>0) {
					$this_no_sounds_f = number_format($this_no_sounds);
					if ($SiteIDq == $SiteID){
						echo "<option value=\"$SiteID\" SELECTED>$SiteName - $this_no_sounds_f sound files</option>\n";
						}
					else{
						echo "<option value=\"$SiteID\">$SiteName - $this_no_sounds_f sound files</option>\n";}
					}
				}

				echo "</select> 
				<br>\n";
			}
			
			#Dates
			if ($type != ""){
				echo "Between: <input type=\"text\" id=\"startDate\" name=\"startDate\" value=\"$DateLow1\" size=\"10\" class=\"fg-button ui-state-default ui-corner-all\" readonly /> and 
				<input type=\"text\" id=\"endDate\" name=\"endDate\" value=\"$DateHigh1\" size=\"10\" class=\"fg-button ui-state-default ui-corner-all\" readonly /><br>";
				}
			
			echo "Data to check: <select name=\"data\" class=\"ui-state-default ui-corner-all\">";
			if ($useR == FALSE){
				if ($data_q == "Duration"){
					echo "<option value=\"Duration\" SELECTED>File duration</option>
					<option value=\"FileSize\">File size</option>";
					}
				elseif ($data_q == "FileSize"){
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\" SELECTED>File size</option>";
					}
				else{
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\">File size</option>";
					}
				}
			elseif ($useR == TRUE){
				if ($data_q == "Duration"){
					echo "<option value=\"Duration\" SELECTED>File duration</option>
					<option value=\"FileSize\">File size</option>
					<option value=\"ADI\">ADI</option>
					<option value=\"Gini\">Gini</option>
					<option value=\"diffspec\">diffspec</option>";
					}
				elseif ($data_q == "FileSize"){
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\" SELECTED>File size</option>
					<option value=\"ADI\">ADI</option>
					<option value=\"Gini\">Gini</option>
					<option value=\"diffspec\">diffspec</option>";
					}
				elseif ($data_q == "ADI"){
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\">File size</option>
					<option value=\"ADI\" SELECTED>ADI</option>
					<option value=\"Gini\">Gini</option>
					<option value=\"diffspec\">diffspec</option>";
					$data_exp = explode("_", $data);
					$data = $data_exp[0] . " <small>(in steps of " . $data_exp[1] . "Hz, up to " . $data_exp[2] . "Hz, with a threshold of " . $data_exp[3] . " for the " . $data_exp[4] . " channel)</small>";
					}
				elseif ($data_q == "Gini"){
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\">File size</option>
					<option value=\"ADI\">ADI</option>
					<option value=\"Gini\" SELECTED>Gini</option>
					<option value=\"diffspec\">diffspec</option>";
					$data_exp = explode("_", $data);
					$data = $data_exp[0] . " <small>(in steps of " . $data_exp[1] . "Hz, up to " . $data_exp[2] . "Hz, with a threshold of " . $data_exp[3] . " for the " . $data_exp[4] . " channel)</small>";
					}
				elseif ($data_q == "diffspec"){
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\">File size</option>
					<option value=\"ADI\">ADI</option>
					<option value=\"Gini\">Gini</option>
					<option value=\"diffspec\" SELECTED>diffspec</option>";
					}
				else{
					echo "<option value=\"Duration\">File duration</option>
					<option value=\"FileSize\">File size</option>
					<option value=\"ADI\">ADI</option>
					<option value=\"Gini\">Gini</option>
					<option value=\"diffspec\">diffspec</option>";
					}
				}				
			echo "</select><br>
			<input type=submit value=\" Show figure \" class=\"fg-button ui-state-default ui-corner-all\" />
			</form>
			<form action=\"qa.php\" method=\"GET\">
				<input type=submit value=\" Reset \" class=\"fg-button ui-state-default ui-corner-all\" />
			</form>
			<br>
			</div>";

		?>
	</div>
	<div class="span-24 last">
		<?php
		if ($type != ""){
			echo "<hr noshade>
				<h3>$data for the ";
				
			$Dateq = "AND Sounds.Date >= '$DateFromq' AND Sounds.Date <= '$DateToq'";

			if ($type == "col"){
				$query = "SELECT * from Collections WHERE ColID='$ColID_q'";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));
				$row = mysqli_fetch_array($result);
				extract($row);
				echo "collection: $CollectionName between $DateLow1 and $DateHigh1";
				}
			elseif ($type == "site"){
				$query = "SELECT * from Sites WHERE SiteID='$SiteID_q'";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));
				$row = mysqli_fetch_array($result);
				extract($row);
				echo "site: $SiteName between $DateLow1 and $DateHigh1";
				}
		
			echo "</h3>
			<br><!--Div that will hold the chart-->
			<div id=\"placeholder\" style=\"width:930px;height:500px\"></div><br>
			<p>Click on a point in the figure to show the file data and spectrogram.
			<div id=\"clickdata\"></div>
			
			<br><hr noshade>";
			
			#ALL CHECKS PASSED
			echo "<p><h3>All checks passed</h3>
			<form action=\"edit_qa.php\" method=\"POST\">
			<strong>Set the Quality flag for the files in this ";

			if ($type == "col"){
				echo "collection</strong>: <input name=\"ColID\" type=\"hidden\" value=\"$ColID_q\">
				<input name=\"type\" type=\"hidden\" value=\"col\">";
				}
			elseif ($type == "site"){
				echo "site</strong>: <input name=\"SiteID\" type=\"hidden\" value=\"$SiteID_q\">
				<input name=\"type\" type=\"hidden\" value=\"site\">";
				}

			$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
			$result_qf = mysqli_query($connection, $query_qf)
				or die (mysqli_error($connection));
			$nrows_qf = mysqli_num_rows($result_qf);

			echo "<select name=\"setqf\" class=\"ui-state-default ui-corner-all\">";
			for ($f=0;$f<$nrows_qf;$f++) {
				$row_qf = mysqli_fetch_array($result_qf);
				extract($row_qf);
				echo "<option value=\"$QualityFlagID\">$QualityFlagID: $QualityFlag</option>\n";
				}

			echo "</select>";
			
			$q=$_SERVER['QUERY_STRING'];
			
			echo "<input name=\"q\" type=\"hidden\" value=\"$q\">
			<input type=\"submit\" value=\" Edit \"  class=\"fg-button ui-state-default ui-corner-all\">
			</form>
			<em>This form will only change the value of files with a flag of 0 ('Unknown').</em>";

			}
		?>
	</div>
	<div class="span-24 last">
		<br>
		<?php
		require("include/bottom.php");
		?>
	</div>
</div>

</body>
</html>
