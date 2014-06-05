<?php
session_start();

$notags=1;

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");

#Sanitize form inputs
$Col_comparison=filter_var($_GET["Col_comparison"], FILTER_SANITIZE_NUMBER_INT);
$Col=filter_var($_GET["Col"], FILTER_SANITIZE_NUMBER_INT);

#Display type saved as a cookie
if (isset($_GET["display_type"])){
	$display_type = filter_var($_GET["display_type"], FILTER_SANITIZE_STRING);
	setcookie("display_type", $display_type, time()+(3600*24*30), $app_dir);
	}
else{
	if(isset($_COOKIE["display_type"])) {
		$display_type = $_COOKIE["display_type"];
		}
	else {
		$display_type = "summary";
		setcookie("display_type", $display_type, time()+(3600*24*30), $app_dir);
		}
	}


if ($Col!="0"){
	if ($Col_comparison==1) {
		$Col_comparisonq = "=";}
	elseif ($Col_comparison==2) {
		$Col_comparisonq = "!=";}
	$Colq = "AND Sounds.ColID $Col_comparisonq '$Col' AND Sounds.ColID=Collections.ColID ";
	}
else{
	$Colq = "";
	}


$Site_comparison=filter_var($_GET["Site_comparison"], FILTER_SANITIZE_STRING);
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);

if ($SiteID!="0"){
	if ($Site_comparison==1) {
		$Site_comparisonq = "=";}
	elseif ($Site_comparison==2) {
		$Site_comparisonq = "!=";}
	$Siteq = "AND Sounds.SiteID $Site_comparisonq '$SiteID' AND Sounds.SiteID=Sites.SiteID ";
	}
else{
	$Siteq = "";
	}


$startDuration=filter_var($_GET["startDuration"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$endDuration=filter_var($_GET["endDuration"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

	$Durationq = "AND Sounds.Duration >= '$startDuration' AND Sounds.Duration <= '$endDuration' ";

$Channels_comparison=filter_var($_GET["Channels_comparison"], FILTER_SANITIZE_STRING);
$Channels=filter_var($_GET["Channels"], FILTER_SANITIZE_NUMBER_INT);

if ($Channels!="0"){

	if ($Channels_comparison==1) {
		$Channels_comparisonq = "=";}
	elseif ($Channels_comparison==2) {
		$Channels_comparisonq = "<";}
	elseif ($Channels_comparison==3) {
		$Channels_comparisonq = ">";}
	$Channelsq = "AND Sounds.Channels $Channels_comparisonq '$Channels' ";
	}
else{
	$Channelsq = "";
	}


$SamplingRate_comparison=filter_var($_GET["SamplingRate_comparison"], FILTER_SANITIZE_STRING);
$SamplingRate=filter_var($_GET["SamplingRate"], FILTER_SANITIZE_NUMBER_INT);

if ($SamplingRate!="0"){

	if ($SamplingRate_comparison==1) {
		$SamplingRate_comparisonq = "=";}
	elseif ($SamplingRate_comparison==2) {
		$SamplingRate_comparisonq = "!=";}
	$SamplingRateq = "AND Sounds.SamplingRate $SamplingRate_comparisonq '$SamplingRate' ";
	}
else{
	$SamplingRateq = "";
	}


$startDate=filter_var($_GET["startDate"], FILTER_SANITIZE_STRING);
$endDate=filter_var($_GET["endDate"], FILTER_SANITIZE_STRING);

date_default_timezone_set('GMT');

$startDate = date('Y-m-d', strtotime($startDate));
$endDate = date('Y-m-d', strtotime($endDate));

	$Dateq = "AND Sounds.Date >= '$startDate' AND Sounds.Date <= '$endDate' ";


$startTime=filter_var($_GET["startTime"], FILTER_SANITIZE_STRING);
$endTime=filter_var($_GET["endTime"], FILTER_SANITIZE_STRING);

	$Timeq = "AND Sounds.Time >= '$startTime' AND Sounds.Time <= '$endTime' ";

$Orderby=filter_var($_GET["Orderby"], FILTER_SANITIZE_STRING);
$Orderby_dir=filter_var($_GET["Orderby_dir"], FILTER_SANITIZE_STRING);

if ($Orderby=="0")
	$Orderby = "SoundID";

if ($Orderby=="Time")
	$Orderby = "Date, Time";

if ($Orderby_dir=="0")
	$Orderby_dir = "ASC";

$order_byq = " $Orderby $Orderby_dir";



if (isset($_GET["filename"])){
	$filename = filter_var($_GET["filename"], FILTER_SANITIZE_STRING);
	$filename_q = " AND Sounds.SoundName LIKE '%" . $filename. "%'";
	}
else{
	$filename_q = " ";
	$Filen = 0;
	}




$Tag_comparison=filter_var($_GET["Tag_comparison"], FILTER_SANITIZE_STRING);
$Tags=filter_var($_GET["Tags"], FILTER_SANITIZE_STRING);
if ($Tags!="0"){
	if ($Tag_comparison=="1"){
		$Tagq = "AND Sounds.SoundID=Tags.SoundID AND Tags.Tag='$Tags'";
		}
	elseif ($Tag_comparison=="2"){
		$Tagq = "AND Sounds.SoundID=Tags.SoundID AND Tags.Tag!='$Tags'";
		}
	}
else{
	$Tagq = "";
	}


#Sanitize browsing vars
if (isset($_GET["startid"])){
	$startid=filter_var($_GET["startid"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$startid=1;
	}


echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Search Results</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

#Multiple delete script to select all
echo "
<SCRIPT language=\"javascript\">
$(function(){
 
    // add multiple select / deselect functionality
    $(\"#selectall\").click(function () {
          $('.case').attr('checked', this.checked);
    });
 
    // if all checkbox are selected, check the selectall checkbox
    // and viceversa
    $(\".case\").click(function(){
 
        if($(\".case\").length == $(\".case:checked\").length) {
            $(\"#selectall\").attr(\"checked\", \"checked\");
        } else {
            $(\"#selectall\").removeAttr(\"checked\");
        }
 
    });
});
</SCRIPT>";

?>

<?php
$query_all_tags = "SELECT DISTINCT Tag FROM Tags";
$result_all_tags = query_several($query_all_tags, $connection);
$nrows_all_tags = mysqli_num_rows($result_all_tags);

if ($nrows_all_tags>0) {
	/*
	#Deprecated
	echo "
	<!-- JQuery Autocomplete http://docs.jquery.com/Plugins/Autocomplete -->
	<script type=\"text/javascript\" src=\"$app_url/js/jquery/jquery.autocomplete.pack.js\"></script>";
	*/
	
	echo "<script type=\"text/javascript\">
	$(function() {
		var mytags = [ ";
		for ($a=0; $a<($nrows_all_tags - 1); $a++) {
			$row_all_tags = mysqli_fetch_array($result_all_tags);
			extract($row_all_tags);
			echo "\"$Tag\", ";
			}
		for ($a=$nrows_all_tags - 1; $a<$nrows_all_tags; $a++) {
			$row_all_tags = mysqli_fetch_array($result_all_tags);
			extract($row_all_tags);
			echo "\"$Tag\"";
			}

		echo "];
			$( \"#newtag\" ).autocomplete({
			      source: mytags
		    });
		  });
	</script>
	";
	}
?>

 
<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type="text/javascript" src="js/jquery.form.js"></script> 
  

<?php

for ($ajax = 0; $ajax < 10; $ajax++) {
	echo "
	<script type=\"text/javascript\">
	$(document).ready(function() { 
	    var options = { 
	        target:        '#tagspace$ajax',   // target element(s) to be updated with server response 
	        // beforeSubmit:  showRequest,  // pre-submit callback 
	        // success:       showResponse,  // post-submit callback 
	 	clearForm: true,
	 	resetForm: true
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#addtags$ajax').ajaxForm(options); 
	}); 
	</script>
	";
	}

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

		<?php
			#If no options where selected, dont waste time searching
			if ($SiteID == "0" && $Duration == "0" && $Channels == "0" && $SamplingRate == "0" && $Date == "0" && $Time == "0" && $Tags == "0" && $Col == "0" && Filen == "0") {
				echo "
				<div class=\"span-24 last\">
				<div class=\"notice\" style=\"margins: 10px;\">
				<img src=\"images/error.png\"> The search was empty. Please go back and try again.<br>";
				#echo $q;
				echo "</div>
				</div>

				<div class=\"span-24 last\">";
					require("include/bottom.php");
		
				echo "</div>
					</div>
				</body></html>";
				die();
				}


			//How many sounds associated with that search
			if ($Tags != "0" && $Col == "0") {
				$q = "SELECT COUNT(*) FROM Sounds,Sites,Tags WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Tagq";
				}
			elseif ($Col != "0" && $Tags == "0") {
				$q = "SELECT COUNT(*) FROM Sounds,Sites,Collections WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Colq";
				}
			elseif ($Col != "0" && $Tags != "0") {
				$q = "SELECT COUNT(*) FROM Sounds,Sites,Tags,Collections WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Colq, $Tagq";
				}
			else {
				$q = "SELECT COUNT(*) FROM Sounds,Sites WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq";
				}
			#debug
			#echo $q;
			$no_sounds = query_one($q, $connection);

			if ($no_sounds == 0) {
				echo "
				<div class=\"span-24 last\">
				<div class=\"notice\" style=\"margins: 10px;\">
				<img src=\"images/error.png\"> There are no files that match the search made. Please go back and try again.<br>";
				#echo $q;
				echo "</div>
				</div>
				<div class=\"span-24 last\">";
				require("include/bottom.php");
		
				echo "</div>
					</div>
				</body>
				</html>";
				die();
				}

			if ($startid < 1)
				$startid = 1;
				
			$startid_q = $startid - 1;

			if ($display_type == "summary"){
				$how_many_to_show = 10;
				}
			elseif ($display_type == "gallery"){
				$how_many_to_show = 18;
				}
			$endid = $how_many_to_show;
			$endid_show = $startid_q + $endid;

			if ($startid_q + $how_many_to_show >= $no_sounds)
				$endid_show = $no_sounds; 

			$sql_limit = "$startid_q, $endid";

			if ($Tags != "0" && $Col == "0") {
				$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds,Sites,Tags WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $filename_q $Siteq $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Tagq ORDER BY $order_byq LIMIT $sql_limit";
			}
			elseif ($Col != "0" && $Tags == "0") {
				$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds,Sites,Collections WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $filename_q $Siteq $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Colq ORDER BY $order_byq LIMIT $sql_limit";
			}
			elseif ($Col != "0" && $Tags != "0") {
				$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds,Sites,Collections,Tags WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $filename_q $Siteq $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Tagq $Colq ORDER BY $order_byq LIMIT $sql_limit";
			}
			else {
				$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds,Sites WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID $filename_q $Siteq $Durationq $Channelsq $SamplingRateq $Dateq $Timeq ORDER BY $order_byq LIMIT $sql_limit";
			}

			#debug
			#echo $query;
			$result = query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
		?>

		<div class="span-10">
			<?php

				echo "<p class=\"highlight3 ui-corner-all\" style=\"text-align: left;\"><strong>Search results</strong>";
				echo "<br>$no_sounds sounds</p>";
			?>
			
		</div>
		<div class="span-6">
		<?php
			echo "<div class=\"center\">";

			#count and next
			echo "Results<br>";

			if ($startid > 1) {
				$go_to = $startid - $how_many_to_show;
				echo "
				<form action=\"advancedsearch.php\" method=\"GET\" style=\"display:inline;\">
				<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
				<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
				<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
				<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
				<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
				<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
				<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
				<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
				<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
				<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
				<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
				<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
				<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
				<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
				<input type=\"hidden\" value=\"$go_to\" name=\"startid\">
				<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
				<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
				<input type=\"hidden\" value=\"$Col\" name=\"Col\">
				<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
				<input type=\"hidden\" value=\"$filename\" name=\"filename\">

				<input type=\"image\" src=\"images/arrowleft.png\" alt=\" Prev \" title=\" Prev \">
				</form>";
				}

			echo " $startid to $endid_show ";

			if ($endid_show < $no_sounds) {
				$go_to = $startid + $how_many_to_show;
				echo "
				<form action=\"advancedsearch.php\" method=\"GET\" style=\"display:inline;\">
				<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
				<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
				<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
				<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
				<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
				<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
				<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
				<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
				<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
				<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
				<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
				<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
				<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
				<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
				<input type=\"hidden\" value=\"$go_to\" name=\"startid\">
				<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
				<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
				<input type=\"hidden\" value=\"$Col\" name=\"Col\">
				<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
				<input type=\"hidden\" value=\"$filename\" name=\"filename\">

				<input type=\"image\" src=\"images/arrowright.png\" alt=\" Next \" title=\" Next \">
				</form>";

				}

			echo "<br>of $no_sounds</div>";
		?>
		
		
		</div>
		<div class="span-6">
			<?php
			#Order by sound date
			echo "<p style=\"text-align: right;\">Display:";

			?>
		</div>
		<div class="span-2 last">
			<?php
			echo "
				<form action=\"advancedsearch.php\" method=\"GET\" style=\"display:inline;\">
				<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
				<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
				<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
				<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
				<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
				<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
				<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
				<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
				<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
				<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
				<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
				<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
				<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
				<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
				<input type=\"hidden\" value=\"$startid\" name=\"startid\">
				<input type=\"hidden\" value=\"summary\" name=\"display_type\">
				<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
				<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
				<input type=\"hidden\" value=\"$Col\" name=\"Col\">
				<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
				<input type=\"hidden\" value=\"$filename\" name=\"filename\">

				<input type=\"image\" src=\"images/application_view_columns.png\" alt=\" Display as summary \" title=\" Display as summary \">
				</form>";

		
			echo "
				<form action=\"advancedsearch.php\" method=\"GET\" style=\"display:inline;\">
				<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
				<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
				<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
				<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
				<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
				<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
				<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
				<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
				<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
				<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
				<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
				<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
				<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
				<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
				<input type=\"hidden\" value=\"$startid\" name=\"startid\">
				<input type=\"hidden\" value=\"gallery\" name=\"display_type\">
				<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
				<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
				<input type=\"hidden\" value=\"$Col\" name=\"Col\">
				<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
				<input type=\"hidden\" value=\"$filename\" name=\"filename\">

				<input type=\"image\" src=\"images/application_view_tile.png\" alt=\" Display as gallery \" title=\" Display as gallery \">
				</form>";

			?>
		</div>
			<?php

				echo "<div class=\"span-24 last\">
					<hr noshade style=\"margin-top: 10px;\">
				</div>";


			if ($display_type == "summary") {
					require("include/view_summary.php");
				}
			elseif ($display_type == "gallery") {
					require("include/view_gallery.php");
				}
			
						
			echo "<div class=\"span-24 last\">
					<br><hr noshade>
				</div>
				<div class=\"span-10\">";

			#Quick form to select a file based on its name
			echo "<form action=\"db_filedetails.php\" method=\"GET\">Select a file from this search:<br>";
			if ($Tags != "0" && $Col == "0") {
				$query_q = "SELECT * FROM Sounds,Sites,Tags WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID 
					$Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Tagq ORDER BY $order_byq LIMIT $sql_limit";
				}
			elseif ($Col != "0" && $Tags == "0") {
				$query_q = "SELECT * FROM Sounds,Sites,Collections WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID 
					$Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Colq ORDER BY $order_byq LIMIT $sql_limit";
				}
			elseif ($Col != "0" && $Tags != "0") {
				$query_q = "SELECT * FROM Sounds,Sites,Collections,Tags WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID 
					$Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq $Tagq $Colq ORDER BY $order_byq LIMIT $sql_limit";
				}
			else {
				$query_q = "SELECT * FROM Sounds,Sites WHERE Sounds.SoundStatus!='9' AND Sounds.SiteID=Sites.SiteID 
					$Siteq $filename_q $Durationq $Channelsq $SamplingRateq $Dateq $Timeq ORDER BY $order_byq LIMIT $sql_limit";
				}

			$result_q = mysqli_query($connection, $query_q)
				or die (mysqli_error($connection));
			$nrows_q = mysqli_num_rows($result_q);

			echo "<select name=\"SoundID\" class=\"ui-state-default ui-corner-all\">";

			for ($q = 0; $q < $nrows_q; $q++) {
				$row_q = mysqli_fetch_array($result_q);
				extract($row_q);

				echo "<option value=\"$SoundID\">$SoundName</option>\n";
				}

			echo "</select> 
			<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>
			</div>";
		?>

		<div class="span-5">

			<?php

			$Col_comparison = filter_var($_GET["Col_comparison"], FILTER_SANITIZE_NUMBER_INT);
			$Col = filter_var($_GET["Col"], FILTER_SANITIZE_NUMBER_INT);
			$Site_comparison = filter_var($_GET["Site_comparison"], FILTER_SANITIZE_STRING);
			$SiteID = filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
			$startDuration = filter_var($_GET["startDuration"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$endDuration = filter_var($_GET["endDuration"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
			$Channels_comparison = filter_var($_GET["Channels_comparison"], FILTER_SANITIZE_STRING);
			$Channels = filter_var($_GET["Channels"], FILTER_SANITIZE_NUMBER_INT);
			$SamplingRate_comparison = filter_var($_GET["SamplingRate_comparison"], FILTER_SANITIZE_STRING);
			$SamplingRate = filter_var($_GET["SamplingRate"], FILTER_SANITIZE_NUMBER_INT);
			$startDate = filter_var($_GET["startDate"], FILTER_SANITIZE_STRING);
			$endDate = filter_var($_GET["endDate"], FILTER_SANITIZE_STRING);
			$startTime = filter_var($_GET["startTime"], FILTER_SANITIZE_STRING);
			$endTime = filter_var($_GET["endTime"], FILTER_SANITIZE_STRING);
			$Orderby = filter_var($_GET["Orderby"], FILTER_SANITIZE_STRING);
			$Orderby_dir = filter_var($_GET["Orderby_dir"], FILTER_SANITIZE_STRING);
			$Tag_comparison = filter_var($_GET["Tag_comparison"], FILTER_SANITIZE_STRING);
			$Tags = filter_var($_GET["Tags"], FILTER_SANITIZE_STRING);
			$filename = filter_var($_GET["filename"], FILTER_SANITIZE_STRING);
 
			 echo "<div class=\"center\">";

				#count and next
				echo "Results<br>";
	 
				#count and prev
				if ($startid > 1) {
					$go_to = $startid - $how_many_to_show;
					echo "
					<form action=\"advancedsearch.php\" method=\"GET\" style=\"display:inline;\">
					<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
					<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
					<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
					<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
					<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
					<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
					<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
					<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
					<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
					<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
					<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
					<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
					<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
					<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
					<input type=\"hidden\" value=\"$go_to\" name=\"startid\">
					<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
					<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
					<input type=\"hidden\" value=\"$Col\" name=\"Col\">
					<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
					<input type=\"hidden\" value=\"$filename\" name=\"filename\">

					<input type=\"image\" src=\"images/arrowleft.png\" alt=\" Prev \" title=\" Prev \">
					</form>";
					}

				echo " $startid to $endid_show ";

				if ($endid_show < $no_sounds) {
					$go_to = $startid + $how_many_to_show;
					echo "
					<form action=\"advancedsearch.php\" method=\"GET\" style=\"display:inline;\">
					<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
					<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
					<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
					<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
					<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
					<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
					<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
					<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
					<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
					<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
					<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
					<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
					<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
					<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
					<input type=\"hidden\" value=\"$go_to\" name=\"startid\">
					<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
					<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
					<input type=\"hidden\" value=\"$Col\" name=\"Col\">
					<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
					<input type=\"hidden\" value=\"$filename\" name=\"filename\">

					<input type=\"image\" src=\"images/arrowright.png\" alt=\" Next \" title=\" Next \">
					</form>";
					}

				echo "&nbsp;</div></div>
				<div class=\"span-3\">&nbsp;</div>
				<div class=\"span-6 last\">";

				if (($no_sounds%$how_many_to_show) == 0)
					$no_pages = floor($no_sounds / $how_many_to_show) - 1;
				else
					$no_pages = floor($no_sounds / $how_many_to_show);

				echo "<form action=\"advancedsearch.php\" method=\"GET\">Jump to page:<br> 
					<input type=\"hidden\" value=\"$startDate\" name=\"startDate\">
					<input type=\"hidden\" value=\"$endDate\" name=\"endDate\">
					<input type=\"hidden\" value=\"$startTime\" name=\"startTime\">
					<input type=\"hidden\" value=\"$endTime\" name=\"endTime\">
					<input type=\"hidden\" value=\"$Site_comparison\" name=\"Site_comparison\">
					<input type=\"hidden\" value=\"$SiteID\" name=\"SiteID\">
					<input type=\"hidden\" value=\"$startDuration\" name=\"startDuration\">
					<input type=\"hidden\" value=\"$endDuration\" name=\"endDuration\">
					<input type=\"hidden\" value=\"$Channels_comparison\" name=\"Channels_comparison\">
					<input type=\"hidden\" value=\"$Channels\" name=\"Channels\">
					<input type=\"hidden\" value=\"$SamplingRate_comparison\" name=\"SamplingRate_comparison\">
					<input type=\"hidden\" value=\"$SamplingRate\" name=\"SamplingRate\">
					<input type=\"hidden\" value=\"$Orderby\" name=\"Orderby\">
					<input type=\"hidden\" value=\"$Orderby_dir\" name=\"Orderby_dir\">
					<input type=\"hidden\" value=\"$Tags\" name=\"Tags\">
					<input type=\"hidden\" value=\"$Tag_comparison\" name=\"Tag_comparison\">
					<input type=\"hidden\" value=\"$Col\" name=\"Col\">
					<input type=\"hidden\" value=\"$Col_comparison\" name=\"Col_comparison\">
					<input type=\"hidden\" value=\"$filename\" name=\"filename\">";

				echo "<select name=\"startid\" class=\"ui-state-default ui-corner-all\">";

				for ($p = 0; $p < ($no_pages + 1); $p++) {
					$this_p = $p + 1;
					$s_id = ($p * $how_many_to_show) + 1;
					if ($s_id == $startid) {
						echo "<option value=\"$s_id\" SELECTED>$this_p</option>\n";
						}
					else {
						echo "<option value=\"$s_id\">$this_p</option>\n";
						}
					}

				echo "</select> 
				<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>
				</div>";

			?>

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