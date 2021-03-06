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
require("include/check_login.php");

#Sanitize inputs
$Tagq=filter_var($_GET["Tag"], FILTER_SANITIZE_STRING);
if (isset($_GET["SiteID"])){
	$SiteID=filter_var($_GET["SiteID"], FILTER_SANITIZE_NUMBER_INT);
	}

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


if ($order_by=="Date")
	$order_byq = "Date $order_dir, Time";
else
	$order_byq = $order_by;


#If user is not logged in, add check for QF
	if ($pumilio_loggedin==FALSE) {
		$qf_check = "AND Sounds.QualityFlagID>='$default_qf'";
		}
	else {
		$qf_check = "";
		}
		
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Browse Sounds by Tag</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
require("include/nocache.php");

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


if ($use_googleanalytics) {
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
		<div class="span-11">
			<?php

			//How many sounds associated with that tag
			$no_sounds=query_one("SELECT COUNT(*) as no_sounds_tag FROM Tags WHERE Tag='$Tagq'", $connection);

			if ($startid<1)
				$startid=1;

			$startid_q=$startid-1;
			
			if ($display_type=="summary")
				$how_many_to_show=10;
			elseif ($display_type=="gallery")
				$how_many_to_show=18;
				
			$endid = $how_many_to_show;
			$endid_show=$startid_q+$endid;

			if ($startid_q+$how_many_to_show >= $no_sounds){
				$endid_show = $no_sounds;
				}

			$sql_limit = "$startid_q, $endid";

			#Check if user can edit files (i.e. has admin privileges)
			$username = $_COOKIE["username"];

			echo "<p class=\"highlight3 ui-corner-all\" style=\"text-align: left;\"><strong>Tag: $Tagq</strong><br>$no_sounds sounds</p>";

			?>
		</div>
		<div class="span-4">
			<?php
				echo "<div class=\"center\">";
				if ($special_wrapper==TRUE){
					$browse_site_link = "$wrapper?page=browse_by_tag";
					$db_filedetails_link = "$wrapper?page=db_filedetails";
					}
				else {
					$browse_site_link = "browse_by_tag.php?";
					$db_filedetails_link = "db_filedetails.php?";
					}
	
				#count and next
				echo "Results<br>";

				if ($startid>1) {
					$go_to=$startid-$how_many_to_show;
					echo "<a href=\"$browse_site_link&amp;Tag=$Tagq&amp;startid=$go_to&amp;order_by=$order_by&amp;order_dir=$order_dir\"><img src=\"$app_url/images/arrowleft.png\"></a>";
					}

				echo " $startid to $endid_show ";

				if ($endid_show<$no_sounds) {
					$go_to=$startid+$how_many_to_show;
					echo "<a href=\"$browse_site_link&amp;Tag=$Tagq&amp;startid=$go_to&amp;order_by=$order_by&amp;order_dir=$order_dir\"><img src=\"$app_url/images/arrowright.png\"></a>";
					}

				echo "<br>of $no_sounds</div>";
			?>
		</div>
		<div class="span-3">
			<?php
			#Order by sound name
			echo "<p>Name <a href=\"browse_by_tag.php?Tag=$Tagq&order_by=SoundName&order_dir=ASC\"><img src=\"images/arrowdown.png\"></a> <a href=\"browse_by_tag.php?Tag=$Tagq&order_by=SoundName&order_dir=DESC\"><img src=\"images/arrowup.png\"></a>";

			?>
		</div>
		<div class="span-3">
			<?php
			#Order by sound date
			echo "<p>Date <a href=\"browse_by_tag.php?Tag=$Tagq&order_by=Date&order_dir=ASC\"><img src=\"images/arrowdown.png\"></a> <a href=\"browse_by_tag.php?Tag=$Tagq&order_by=Date&order_dir=DESC\"><img src=\"images/arrowup.png\"></a>";

			?>
		</div>
		<div class="span-3 last">
			<?php
			#Order by sound duration
			echo "<p>Display: <a href=\"browse_by_tag.php?Tag=$Tagq&order_by=$order_by&order_dir=$order_dir&display_type=summary&startid=$startid\" title=\"Display as summary\"><img src=\"images/application_view_columns.png\" alt=\"Display as summary\"></a> <a href=\"browse_by_tag.php?Tag=$Tagq&order_by=$order_by&order_dir=$order_dir&display_type=gallery&startid=$startid\" title=\"Display as gallery\"><img src=\"images/application_view_tile.png\" alt=\"Display as gallery\"></a>";

			?>
		</div>
			<?php

			echo "<div class=\"span-24 last\">
				<hr noshade style=\"margin-top: 10px;\">";
				
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
			echo "</div>";

			$query = "SELECT *, DATE_FORMAT(Sounds.Date, '%d-%b-%Y') AS Date_h FROM Sounds,Tags WHERE Tags.Tag='$Tagq' AND Tags.SoundID=Sounds.SoundID AND Sounds.SoundStatus!='9' $qf_check ORDER BY $order_byq $order_dir LIMIT $sql_limit";

			$result=query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
			$check_result=query_several($query, $connection);
			
			#echo "<div class=\"span-24 last\">";

			if ($nrows>0) {
				if ($display_type=="summary") {
					require("include/view_summary.php");
					}
				elseif ($display_type=="gallery") {
					require("include/view_gallery.php");
					}
				}
			else {
				echo "<div class=\"span-24 last\"><p>There are no more files with that tag.
					</div>";
				}

				echo "<div class=\"span-24 last\">
					<br><hr noshade>
				</div>
				<div class=\"span-10\">";

				#Quick form to select a file based on its name
				echo "<form action=\"db_filedetails.php\" method=\"GET\">Select a file from this tag:<br>";
				
				$query_q = "SELECT * FROM Sounds,Tags WHERE Tags.Tag='$Tagq' AND Tags.SoundID=Sounds.SoundID 
						AND Sounds.SoundStatus!='9' $qf_check ORDER BY SoundName";
								
				$result_q = mysqli_query($connection, $query_q)
					or die (mysqli_error($connection));
				$nrows_q = mysqli_num_rows($result_q);

				echo "<select name=\"SoundID\" class=\"ui-state-default ui-corner-all\">";

				for ($q=0;$q<$nrows_q;$q++) {
					$row_q = mysqli_fetch_array($result_q);
					extract($row_q);

					echo "<option value=\"$SoundID\">$SoundName</option>\n";
					}

				echo "</select> 
				<input type=submit value=\" Select \" class=\"fg-button ui-state-default ui-corner-all\"></form>
				</div>";

				?>
				<div class="span-8">
					<?php
						echo "<div class=\"center\">";
						if ($special_wrapper==TRUE){
							$browse_site_link = "$wrapper?page=browse_by_tag";
							$db_filedetails_link = "$wrapper?page=db_filedetails";
							}
						else {
							$browse_site_link = "browse_by_tag.php?";
							$db_filedetails_link = "db_filedetails.php?";
							}
			
						#count and next
						echo "Results<br>";

						if ($startid>1) {
							$go_to=$startid-$how_many_to_show;
							echo "<a href=\"$browse_site_link&amp;Tag=$Tagq&amp;startid=$go_to&amp;order_by=$order_by&amp;order_dir=$order_dir\"><img src=\"$app_url/images/arrowleft.png\"></a>";
							}

						echo " $startid to $endid_show ";

						if ($endid_show<$no_sounds) {
							$go_to=$startid+$how_many_to_show;
							echo "<a href=\"$browse_site_link&amp;Tag=$Tagq&amp;startid=$go_to&amp;order_by=$order_by&amp;order_dir=$order_dir\"><img src=\"$app_url/images/arrowright.png\"></a>";
							}

						echo "<br>of $no_sounds</div>";

				echo "</div>
				
				<div class=\"span-6 last\">";

					if (($no_sounds%$how_many_to_show)==0)
						$no_pages=floor($no_sounds/$how_many_to_show)-1;
					else
						$no_pages=floor($no_sounds/$how_many_to_show);
						
					echo "<form action=\"browse_by_tag.php\" method=\"GET\">Jump to page:<br> 
						<input type=\"hidden\" name=\"Tag\" value=\"$Tagq\">
						<input type=\"hidden\" name=\"order_by\" value=\"$order_by\">
						<input type=\"hidden\" name=\"order_dir\" value=\"$order_dir\">";

					echo "<select name=\"startid\" class=\"ui-state-default ui-corner-all\">";

					for ($p=0;$p<($no_pages+1);$p++) {
						$this_p=$p+1;
						$s_id=($p*$how_many_to_show)+1;
						if ($s_id==$startid) {
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
