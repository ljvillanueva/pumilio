<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require("config.php");
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");

#Sanitize inputs
$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$QDate=filter_var($_GET["Date"], FILTER_SANITIZE_NUMBER_INT);
$startid=filter_var($_GET["startid"], FILTER_SANITIZE_NUMBER_INT);
$order_by=filter_var($_GET["order_by"], FILTER_SANITIZE_STRING);
$order_dir=filter_var($_GET["order_dir"], FILTER_SANITIZE_STRING);
$display_type=filter_var($_GET["display_type"], FILTER_SANITIZE_STRING);

if ($startid=="")
	$startid=1;
if ($order_by=="")
	$order_by = "SoundName";
if ($order_by=="Date")
	$order_by = "Date, Time";
if ($order_dir=="")
	$order_dir = "ASC";
if ($display_type=="")
	$display_type = "summary";
	
if ($order_by=="Date")
	$order_byq = "Date $order_dir, Time";
else
	$order_byq = $order_by;
	
#If user is not logged in, add check for QF
	if (!sessionAuthenticate($connection)) {
		$qf_check = "AND Sounds.QualityFlagID>='$default_qf'";
		}
	else {
		$qf_check = "";
		}
		
echo "
<html>
<head>

<title>$app_custom_name - Browse Sounds by Site and Date</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
require("include/nocache.php");

#Multiple delete script to select all
echo "
<SCRIPT type=\"text/javascript\">
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
	echo "
	<!-- JQuery Autocomplete http://docs.jquery.com/Plugins/Autocomplete -->
	<script type=\"text/javascript\" src=\"js/jquery/jquery.autocomplete.pack.js\"></script>
	<script>
	  $(document).ready(function(){
	var mytags = \" ";
	for ($a=0;$a<$nrows_all_tags;$a++) {
		$row_all_tags = mysqli_fetch_array($result_all_tags);
		extract($row_all_tags);
		echo "$Tag ";
		}
	echo "\".split(\" \");
	$(\"#newtag\").autocomplete(mytags);
	  });
	</script>
	<link rel=\"stylesheet\" href=\"js/jquery/jquery.autocomplete.css\" type=\"text/css\">
	";
 }
?>
  
  
<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type="text/javascript" src="js/jquery.form.js"></script> 
  

<?php

for ($ajax=0;$ajax<10;$ajax++) {
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
?>

<?php
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
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
		<div class="span-24 last" id="loadingdiv">
			<h5 class="highlight2 ui-corner-all">Please wait... loading... <img src="images/ajax-loader.gif" border="0"></h5>
		</div>		
		<?php
		flush();
		?>
		<div class="span-11">
			<?php
				$query = "SELECT * from Sites WHERE SiteID=$SiteID";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));

				$row = mysqli_fetch_array($result);
				extract($row);
				//How many sounds associated with that source
				$no_sounds=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SiteID='$SiteID' 
					AND Date='$QDate' AND Sounds.SoundStatus!='9' $qf_check", $connection);

				$startid_q=$startid-1;
				
				if ($display_type=="summary")
					$how_many_to_show=10;
				elseif ($display_type=="gallery")
					$how_many_to_show=18;
					
				$endid = $how_many_to_show;
				$endid_show=$startid_q+$endid;

				if ($startid_q+$how_many_to_show >= $no_sounds)
					$endid_show = $no_sounds; 

				$sql_limit = "$startid_q, $endid";

				$QDate_human=query_one("SELECT DATE_FORMAT('$QDate','%d-%b-%Y')", $connection);
				echo "<p class=\"highlight3 ui-corner-all\" style=\"text-align: left;\"><strong>Site: $SiteName</strong><br>$no_sounds sounds on $QDate_human</p>";


			?>
		</div>
		<div class="span-4">
			<?php
			#count and next
			echo "<p>";

			if ($startid>1) {
				$go_to=$startid-$how_many_to_show;
				echo "<a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&startid=$go_to&order_by=$order_by&order_dir=$order_dir&display_type=$display_type\"><img src=\"$app_url/images/arrowleft.png\"></a> ";
				}

			echo "$startid - $endid_show of $no_sounds";

			if ($endid_show<$no_sounds) {
				$go_to=$startid+$how_many_to_show;
				echo "<a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&startid=$go_to&order_by=$order_by&order_dir=$order_dir&display_type=$display_type\"><img src=\"$app_url/images/arrowright.png\"></a> ";
				}
			?>
		</div>
		<div class="span-3">
			<?php
			#Order by sound name
			echo "<p>Name <a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&order_by=SoundName&order_dir=ASC&display_type=$display_type\"><img src=\"$app_url/images/arrowdown.png\"></a> <a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&order_by=SoundName&order_dir=DESC&display_type=$display_type\"><img src=\"$app_url/images/arrowup.png\"></a>";

			?>
		</div>
		<div class="span-3">
			<?php
			#Order by sound date
			echo "<p>Date <a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&order_by=Date&order_dir=ASC&display_type=$display_type\"><img src=\"$app_url/images/arrowdown.png\"></a> <a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&order_by=Date&order_dir=DESC&display_type=$display_type\"><img src=\"$app_url/images/arrowup.png\"></a>";

			?>
		</div>
		<div class="span-3 last">
			<?php
			#Order by sound duration
			echo "<p>Display: <a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&order_by=$order_by&order_dir=$order_dir&display_type=summary&startid=$startid\" title=\"Display as summary\"><img src=\"$app_url/images/application_view_columns.png\" alt=\"Display as summary\"></a> <a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&order_by=$order_by&order_dir=$order_dir&display_type=gallery&startid=$startid\" title=\"Display as gallery\"><img src=\"$app_url/images/application_view_tile.png\" alt=\"Display as gallery\"></a>";

			?>
		</div>
			<?php

			echo "<div class=\"span-24 last\">
				<hr noshade style=\"margin-top: 10px;\">
			</div>";

			$query = "SELECT *, DATE_FORMAT(Date, '%d-%b-%Y') AS Date_h FROM Sounds WHERE SiteID='$SiteID' AND Date='$QDate' 
				AND Sounds.SoundStatus!='9' $qf_check ORDER BY $order_byq $order_dir LIMIT $sql_limit";

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

			echo "	<div class=\"span-24 last\">
					<br><hr noshade>
				</div>
			<div class=\"span-10\">";

			#Quick form to select a file based on its name
			echo "<form action=\"db_filedetails.php\" method=\"GET\">Select a file from this site and date:<br>";
				$query_q = "SELECT * FROM Sounds WHERE SiteID='$SiteID' AND Date='$QDate' 
					AND Sounds.SoundStatus!='9' $qf_check ORDER BY SoundName";
				$result_q = mysqli_query($connection, $query_q)
					or die (mysqli_error($connection));
				$nrows_q = mysqli_num_rows($result_q);

				echo "<select name=\"SoundID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\" >";

				for ($q=0;$q<$nrows_q;$q++) {
					$row_q = mysqli_fetch_array($result_q);
					extract($row_q);

					echo "<option value=\"$SoundID\">$SoundName</option>\n";
					}

				echo "</select> 
				<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>
			</div>";

				?>
				<div class="span-8">
				<?php
				#count and next
				echo "<p>";

				if ($startid>1) {
					$go_to=$startid-$how_many_to_show;
					echo "<a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&startid=$go_to&order_by=$order_by&order_dir=$order_dir&display_type=$display_type\"><img src=\"$app_url/images/arrowleft.png\"></a> ";
					}

				echo "$startid - $endid_show of $no_sounds";

				if ($endid_show<$no_sounds) {
					$go_to=$startid+$how_many_to_show;
					echo "<a href=\"browse_site_date.php?SiteID=$SiteID&Date=$QDate&startid=$go_to&order_by=$order_by&order_dir=$order_dir&display_type=$display_type\"><img src=\"$app_url/images/arrowright.png\"></a> ";
					}

				echo "</div>
				
				<div class=\"span-6 last\">";

				if (($no_sounds%$how_many_to_show)==0)
					$no_pages=floor($no_sounds/$how_many_to_show)-1;
				else
					$no_pages=floor($no_sounds/$how_many_to_show);
					
				echo "<form action=\"browse_site_date.php\" method=\"GET\">Jump to page:<br> 
					<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
					<input type=\"hidden\" name=\"Date\" value=\"$QDate\">
					<input type=\"hidden\" name=\"order_by\" value=\"$order_by\">
					<input type=\"hidden\" name=\"order_dir\" value=\"$order_dir\">
					<input type=\"hidden\" name=\"display_type\" value=\"$display_type\">";

				echo "<select name=\"startid\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\" >";

					for ($p=0;$p<($no_pages+1);$p++) {
						$this_p=$p+1;
						$s_id=($p*$how_many_to_show)+1;
						if ($s_id==$startid)
							{echo "<option value=\"$s_id\" SELECTED>$this_p</option>\n";}
						else
							{echo "<option value=\"$s_id\">$this_p</option>\n";}
						}

				echo "</select> 
				<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>

				</div>";

			?>

		<div class="span-24 last">
			&nbsp;
			<script type="text/javascript">
			function hidediv()
			      {
				loadingdiv.style.visibility= "hidden";
				loadingdiv.style.height= "0";
			      };
		
			hidediv();
			</script>
			
			<style type="text/css">
			#loadingdiv {visibility:hidden;
					height:0;}
			</style>
			
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
