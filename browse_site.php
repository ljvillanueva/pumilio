<?php
session_start();
header( 'Content-type: text/html; charset=utf-8' );

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require($config_file);
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");
require("include/check_login.php");


use \DByte\DB;
DB::$c = $pdo;


#Sanitize inputs
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);

#Check if site has files or is valid
	$valid_id = DB::column('SELECT COUNT(*) FROM `Sounds` WHERE SiteID = ' . $SiteID);

	if ($valid_id == 0) {
		echo "<!DOCTYPE html>
		<html lang=\"en\">
		<head>

		<title>$app_custom_name - Error</title>\n";

		require("include/get_css3.php");

		echo "<body>
			<div class=\"alert alert-danger\"><span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span> The site requested does not exists or it has no recordings. Please go back and try your query again.</div>
			</body>
			</html>";
		die();
		}



#Display type saved as a cookie
#NOW only gallery
/*if (isset($_GET["display_type"])){
	$display_type = filter_var($_GET["display_type"], FILTER_SANITIZE_STRING);
	}
else{
	$display_type = "gallery";
	}
*/
$display_type = "gallery";

if (isset($_GET["startid"])){
	$startid=filter_var($_GET["startid"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$startid=1;
	}
		
if (isset($_GET["order_by"])){
	$order_by=filter_var($_GET["order_by"], FILTER_SANITIZE_STRING);
	}
else{
	$order_by = "Date";
	}
	
if (isset($_GET["order_dir"])){
	$order_dir=filter_var($_GET["order_dir"], FILTER_SANITIZE_STRING);
	}
else{
	$order_dir = "ASC";
	}

if ($order_by=="Date"){
	$order_byq = "Date $order_dir, Time $order_dir ";
	}
elseif ($order_by=="SoundName"){
	$order_byq = "SoundName $order_dir";
	}



#If user is not logged in, add check for QF
	if ($pumilio_loggedin==FALSE) {
		$qf_check = "AND Sounds.QualityFlagID>='$default_qf'";
		}
	else {
		$qf_check = "";
		}
		
echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>$app_custom_name - Browse Sounds at a Site</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");
#require("include/nocache.php");


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
$result_all_tags=query_several($query_all_tags, $connection);
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
<?php
echo "<script type=\"text/javascript\" src=\"$app_url/js/jquery.form.js\"></script>\n";

for ($ajaxi = 0; $ajaxi < 10; $ajaxi++) {
	echo "
	<script type=\"text/javascript\">
	$(document).ready(function() { 
	    var options = { 
	        target:        '#tagspace$ajaxi',   // target element(s) to be updated with server response 
	        // beforeSubmit:  showRequest,  // pre-submit callback 
	        // success:       showResponse,  // post-submit callback 
	 	clearForm: true,
	 	resetForm: true
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#addtags$ajaxi').ajaxForm(options); 
	}); 
	</script>
	
	";
	}
?>

<?php
if ($use_googleanalytics){
	echo $googleanalytics_code;
	}
?>

<!-- Hide success messages -->
<script type="text/javascript">
$(function() {
    // setTimeout() function will be fired after page is loaded
    // it will wait for 5 sec. and then will fire
    // $("#successMessage").hide() function
    setTimeout(function() {
        $("#md").hide('blind', {}, 500)
    }, 5000);
});
</script>

<?php
#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}


#Leaflet
echo "\n<link rel=\"stylesheet\" href=\"libs/leaflet/leaflet.css\" />\n

<style>
	#map { height: 220px; 
			width: 320px;
		}
</style>";

?>


</head>
<body>

	<?php
	
		echo "<!--Blueprint container-->
			<div class=\"container\">";

			require("include/topbar.php");

			#Loading... message
			require("include/loadingtop.php");
		
		
				$query = "SELECT * from Sites WHERE SiteID=$SiteID";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));

				$row = mysqli_fetch_array($result);
				extract($row);
				//How many sounds associated with that source
				$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SiteID='$SiteID' 
					AND Sounds.SoundStatus!='9' $qf_check", $connection);

				if ($startid < 1) {
					$startid = 1;
					}
					
				$startid_q = $startid - 1;

				if ($display_type == "summary"){
					$how_many_to_show = 10;}
				elseif ($display_type == "gallery"){
					$how_many_to_show = 36;}
				$endid = $how_many_to_show;
				$endid_show = $startid_q + $endid;

				if ($startid_q + $how_many_to_show >= $no_sounds) {
					$endid_show = $no_sounds;}

				$sql_limit = "$startid_q, $endid";



			echo "
			<div class=\"page-header\">
			<div class=\"row\">
				<div class=\"col-lg-8\">
					<h2>Sounds at the Site: $SiteName</h2>
					<p>Coordinates: $SiteLat, $SiteLon</p>
					<p>$no_sounds sounds at this site</p>
				</div>
				<div class=\"col-lg-4\">
					<div id=\"map\"></div>
				</div>
			</div>

			</div>
		
			<div class=\"row\">
				<div class=\"col-lg-4\">";

				/*if (sessionAuthenticate($connection) || !$hide_latlon_guests) {
					#echo "Coordinates: $SiteLat, $SiteLon | <a href=\"viewsite_map.php?SiteID=$SiteID\" title=\"View site in a map\"><strong>Map</strong></a>";
					echo "Coordinates: $SiteLat, $SiteLon";
					}
				else {
					#echo "<a href=\"viewsite_map.php?SiteID=$SiteID\" title=\"View site in a map\"><strong>Map</strong></a>";
					}*/

				if (sessionAuthenticate($connection) && is_user_admin2($username, $connection)) {
					echo "<br><a href=\"edit_site.php?SiteID=$SiteID\" title=\"Edit this Site\">[edit site]</a>";
						}


			#Select particular date
			/*$query_dates = "SELECT DISTINCT DATE_FORMAT(Date,'%d-%b-%Y') AS Date_f, Date FROM Sounds 
				WHERE Date IS NOT NULL AND SiteID='$SiteID' AND Sounds.SoundStatus!='9' $qf_check ORDER BY Date";
			$result_dates=query_several($query_dates, $connection);
			$nrows_dates = mysqli_num_rows($result_dates);
			if ($nrows_dates>0) {
				if ($special_wrapper==TRUE){
					echo "<form action=\"$wrapper\" method=\"GET\">
					<input type=\"hidden\" name=\"page\" value=\"browse_site_date\">";
					}
				else {
					echo "<form action=\"browse_site_date.php\" method=\"GET\">";
					}

				echo "Filter by date: 
					<select name=\"Date\" class=\"ui-state-default ui-corner-all\">
					<option value=\"\">All dates</option>";

				for ($d=0;$d<$nrows_dates;$d++)	{
					$row_dates = mysqli_fetch_array($result_dates);
					extract($row_dates);
					echo "\n<option value=\"$Date\">$Date_f</option>";
					}
				echo "</select>
				<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
				<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}*/

			?>
			
		

		</div>
		<div class="col-lg-6">
			<?php
				#count and next
				echo "<dl class=\"dl-horizontal\"><dt>Results</dt><dd>$startid to $endid_show of $no_sounds</dd></dl>";
			?>
		</div>
		<div class="col-lg-1 center">
			<?php
			#Order by sound name
			echo "Name<br><a href=\"browse_site.php?SiteID=$SiteID&order_by=SoundName&order_dir=ASC\"><span class=\"glyphicon glyphicon-triangle-bottom\" aria-hidden=\"true\"></span></a> &nbsp;&nbsp; <a href=\"browse_site.php?SiteID=$SiteID&order_by=SoundName&order_dir=DESC\"><span class=\"glyphicon glyphicon-triangle-top\" aria-hidden=\"true\"></span></a>";

			?>
		</div>
		<div class="col-lg-1 center">
			<?php
			#Order by sound date
			echo "Date<br><a href=\"browse_site.php?SiteID=$SiteID&order_by=Date&order_dir=ASC\"><span class=\"glyphicon glyphicon-triangle-bottom\" aria-hidden=\"true\"></span></a> &nbsp;&nbsp; <a href=\"browse_site.php?SiteID=$SiteID&order_by=Date&order_dir=DESC\"><span class=\"glyphicon glyphicon-triangle-top\" aria-hidden=\"true\"></span></a>";

			?>
		</div>

		</div>
			<?php

			#Confirm delete
			if (isset($_GET["md"])){
				$md=filter_var($_GET["md"], FILTER_SANITIZE_NUMBER_INT);
				if ($md == 1){
					echo "<div <div class=\"success\" id=\"md\">One file was deleted.</div>";
					}
				else{
					echo "<div <div class=\"success\" id=\"md\">$md files were deleted.</div>";
					}
				}


			$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds WHERE SiteID='$SiteID' 
				AND Sounds.SoundStatus!='9' $qf_check ORDER BY $order_byq LIMIT $sql_limit";

			#print $query;

			$result=query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
			$check_result=query_several($query, $connection);
			
			if ($nrows>0) {
				if ($display_type=="summary") {
					require("include/view_summary.php");
					}
				elseif ($display_type=="gallery") {
					require("include/view_gallery.php");
					}
				}

			echo "<div class=\"row\"><div class=\"col-lg-12\"><hr noshade></div></div>";

			#Pagination

			if ($startid < 1){
					$prev = -1;
					$startid = 1;
					$next = $startid + $how_many_to_show;
				}
				else{
					$prev = $startid - $how_many_to_show;
					$next = $startid + $how_many_to_show;
				}

				if ($next > $no_sounds){
					$next = $no_sounds;
				}

				if (($startid + $how_many_to_show) > $no_sounds){
					$next = "NA";
				}


					echo "<nav class=\"text-center\">
				  		<ul class=\"pagination pagination-sm\">";
						if ($prev > -1){
							echo "<li>
								<a href=\"browse_site.php?SiteID=$SiteID&startid=$prev\" aria-label=\"Previous\">
								<span aria-hidden=\"true\">&laquo;</span>
								</a>
							</li>\n";
						}
						
					    $prevellipsis = FALSE;
					    $nextellipsis = FALSE;
					    $pages = ceil($no_sounds / $how_many_to_show);
					    for ($p=1; $p < ($pages + 1); $p++) {
					    	$this_page = ($p - 1) * $how_many_to_show + 1;

					    	if ($this_page == $startid){
					    		echo "<li class=\"active\"><a href=\"browse_site.php?SiteID=$SiteID&startid=$this_page\">$p <span class=\"sr-only\">(current)</span></a></li>";
					    	}
					    	else{
					    	
					    		if ($this_page < ($startid - 120)){
					    			if ($prevellipsis == FALSE){
						    			echo "<li><span aria-hidden=\"true\">...</span></li>";
						    			$prevellipsis = TRUE;
						    			}
					    			}
					    		elseif ($this_page > ($startid + 120)){
					    			if ($nextellipsis == FALSE){
						    			echo "<li><span aria-hidden=\"true\">...</span></li>";
						    			$nextellipsis = TRUE;
						    			}
					    			}
					    		else{
					    			echo "<li><a href=\"browse_site.php?SiteID=$SiteID&startid=$this_page\">$p</a></li>";
					    			}
					    	}
					    }

					    if ($next != "NA"){
							
							echo "<li>
							  <a href=\"browse_site.php?SiteID=$SiteID&startid=$next\" aria-label=\"Next\">
							    <span aria-hidden=\"true\">&raquo;</span>
							  </a>
							</li>\n";
						}
					echo "</ul></nav>";
				#}
			

require("include/bottom.php");

#Loading... message
require("include/loadingbottom.php");

require("include/leaflet1.php");
?>

</body>
</html>
