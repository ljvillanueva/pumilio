<?php
session_start();
header( 'Content-type: text/html; charset=utf-8' );

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");
require("include/check_admin.php");

#DB
use \DByte\DB;
DB::$c = $pdo;


#Sanitize inputs
$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
if (isset($_GET["hidemarks"])){
	$hidemarks=filter_var($_GET["hidemarks"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$hidemarks = "0";
	}
	
if (isset($_GET["d"])){
	$d=filter_var($_GET["d"], FILTER_SANITIZE_STRING);
	}
else {
	$d = "s";
	}

if (isset($_GET["hidekml"])){
	$hidekml=filter_var($_GET["hidekml"], FILTER_SANITIZE_STRING);
	}
else {
	$hidekml = 0;
	}


$valid_id = DB::column('SELECT COUNT(*) FROM `Sounds` WHERE SoundID = ' . $SoundID);
$SoundID_status = DB::column('SELECT SoundStatus FROM `Sounds` WHERE SoundID = ' . $SoundID);
$SoundID_qf_check = DB::column('SELECT QualityFlagID FROM `Sounds` WHERE SoundID = ' . $SoundID);

if ($d=="w"){
	$hidemarks = 1;
	}

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>$app_custom_name - File Details</title>\n";

require("include/get_css3.php");

if ($valid_id != 1) {
	echo "<body>

		<div class=\"alert alert-danger\" role=\"alert\">
    	    <img src=\"images/exclamation.png\"> The file requested does not exists or the Sound ID is not valid. Please go back and try again.
      	</div>

		</body>
		</html>";
	die();
	}

if ($SoundID_status == 9) {
	echo "<body>
		<div class=\"alert alert-danger\" role=\"alert\">
    	    <img src=\"images/exclamation.png\"> The file requested was deleted.
			Please contact the administrator for more information.
      	</div>
		</body>
		</html>";
	die();
	}

if ($SoundID_qf_check < $default_qf && $pumilio_loggedin==FALSE) {
	echo "<body>
		<div class=\"alert alert-danger\" role=\"alert\">
    	    <img src=\"images/exclamation.png\"> You must be logged in to see
			this file. Please contact the administrator for more information.
      	</div>
		</body>
		</html>";
	die();
	}



$this_sound = DB::row('SELECT *, DATE_FORMAT(Date, \'%d %M %Y\') AS HumanDate, TIME_FORMAT(Time, \'%H:%i:%s\') AS HumanTime, TIME_FORMAT(Duration, \'%i:%s\') AS Duration_human FROM `Sounds` WHERE `SoundID`="' . $SoundID . '"');
					  

$HumanDate = $this_sound->HumanDate;
$HumanTime = $this_sound->HumanTime;
$Duration_human = $this_sound->Duration_human;
$ColID = $this_sound->ColID;
$SiteID = $this_sound->SiteID;
$DirID = $this_sound->DirID;
$AudioPreviewFilename = $this_sound->AudioPreviewFilename;
$SoundName = $this_sound->SoundName;
$OriginalFilename = $this_sound->OriginalFilename;
$Duration = $this_sound->Duration;
$SoundFormat = $this_sound->SoundFormat;
$SamplingRate = $this_sound->SamplingRate;
$Channels = $this_sound->Channels;
$OtherSoundID = $this_sound->OtherSoundID;
$Notes = $this_sound->Notes;
$SensorID = $this_sound->SensorID;
$DerivedSound = $this_sound->DerivedSound;
$QualityFlagID = $this_sound->QualityFlagID;



require("include/get_jqueryui.php");
?>

<!--#Accordion-->
<script type="text/javascript">
$(function() {
	$("#accordion").accordion({
		heightStyle: "content",
		collapsible: true,
		active: false
		});
	});
</script>

<!-- JQuery Confirmation -->
<script type="text/javascript">
	$(function() {
		$("#dialog").dialog({
			autoOpen: false,
			bgiframe: true,
			resizable: false,
			draggable: false,
			height:140,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
		 buttons: {
		                "Delete file": function() {
		                    document.testconfirmJQ.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#testconfirmJQ').submit(function(){
		            $("p#dialog-email").html($("input#SoundID").val());
		            $('#dialog').dialog('open');
		            return false;
		        });
		});

	</script>


<?php
$query_all_tags = "SELECT DISTINCT Tag FROM Tags";
$result_all_tags = query_several($query_all_tags, $connection);
$nrows_all_tags = mysqli_num_rows($result_all_tags);

if ($nrows_all_tags>0) {
	
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
	
#flush();
#require("include/update_sites.php");


####################################################3
$use_googlemaps=FALSE;
$use_leaflet=TRUE;
####################################################3

if ($use_leaflet == TRUE){
		#Leafet
		echo "\n<link rel=\"stylesheet\" href=\"libs/leaflet/leaflet.css\" />\n

		<style>
			#map { height: 220px; 
					width: 320px;
				}
		</style>";
	}
elseif ($use_googlemaps=="3") {
	#Get points from the database
	$query_site = "SELECT * FROM Sites,Sounds WHERE SiteLat IS NOT NULL AND SiteLon IS NOT NULL
				AND Sites.SiteID=Sounds.SiteID AND Sounds.SoundID='$SoundID' 
				AND Sites.SiteLat IS NOT NULL AND Sites.SiteLon IS NOT NULL LIMIT 1";

	$result_site=query_several($query_site, $connection);
	$nrows_site = mysqli_num_rows($result_site);

	if ($nrows_site>0) {
		$map_div_message="Your browser does not have JavaScript enabled, which is required to proceed. 
					Please enable JavaScript or contact your system administrator for help.";
		}
	else {
		$map_div_message="This sound has no location data.";
		}

	if ($nrows_site>0) {
		$SiteID = DB::column('SELECT SiteID FROM `Sounds` WHERE SoundID = ' . $SoundID);
		
		$SiteLat = DB::column('SELECT SiteLat FROM `Sites` WHERE SiteID = ' . $SiteID);
		$SiteLon = DB::column('SELECT SiteLon FROM `Sites` WHERE SiteID = ' . $SiteID);
		$SiteName = DB::column('SELECT SiteName FROM `Sites` WHERE SiteID = ' . $SiteID);

		require("include/db_filedetails_map_head.php");
		}
	}


#HTML5 player
# http://www.jplayer.org
echo "\n<link href=\"html5player/jplayer.css\" rel=\"stylesheet\" type=\"text/css\">";
echo "\n<script type=\"text/javascript\" src=\"js/jquery.jplayer.min.js\"></script>\n";

if ($DirID == 0 || $DirID == ""){
	$DirID = rand(1,100);
	query_one("UPDATE Sounds SET DirID='$DirID' WHERE SoundID='$SoundID'", $connection);
		}

#Check MP3
if (($AudioPreviewFilename=="") || (is_null($AudioPreviewFilename))) {
	#File does not exists, create
	$AudioPreviewFilename=dbfile_mp3($OriginalFilename,$SoundFormat,$ColID,$DirID,$SamplingRate);
	$query_mp3 = "UPDATE Sounds SET AudioPreviewFilename='$AudioPreviewFilename' WHERE SoundID='$SoundID'";
	$result_mp3 = mysqli_query($connection, $query_mp3)
		or die (mysqli_error($connection));
	}
	
if (!is_file("$absolute_dir/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename")) {
	#File does not exists, create
	#Check if dir exists
	if (!is_dir("sounds/previewsounds/$ColID")) {
		mkdir("sounds/previewsounds/$ColID", 0777);
		}
	if (!is_dir("sounds/previewsounds/$ColID/$DirID")) {
		mkdir("sounds/previewsounds/$ColID/$DirID", 0777);
		}
			
	$AudioPreviewFilename=dbfile_mp3($OriginalFilename,$SoundFormat,$ColID,$DirID,$SamplingRate);
	$query_mp3 = "UPDATE Sounds SET AudioPreviewFilename='$AudioPreviewFilename' WHERE SoundID='$SoundID'";
	$result_mp3 = mysqli_query($connection, $query_mp3)
		or die (mysqli_error($connection));
	}

echo "\n<script type=\"text/javascript\">
//<![CDATA[
$(document).ready(function(){

	$(\"#jquery_jplayer_1\").jPlayer({
		ready: function (event) {
			$(this).jPlayer(\"setMedia\", {
				$AudioPreviewFormat: \"sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\"
			}).jPlayer(\"pause\");
		},
		solution: \"html, flash\",
		volume: \"0.9\",
		swfPath: \"$app_url/js\",
		supplied: \"$AudioPreviewFormat\",
		preload: \"auto\"
	});
});
//]]>
</script>
";

#CHECK IMAGES
#Check if there are images
$makefigures = FALSE;
#$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
#$sound_images=query_one($query_img, $connection);
$sound_images = DB::column('SELECT COUNT(*) FROM `SoundsImages` WHERE SoundID = ' . $SoundID);

if ($sox_images==FALSE){
	if ($sound_images!=6) {
		$makefigures=TRUE;
		}
	else{
		#$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
		$query_img2 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="waveform" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
			$makefigures=TRUE;
			}

		#$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
		$query_img3 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
			$makefigures=TRUE;
			}

		#$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
		$query_img4 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="waveform-small" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
			$makefigures=TRUE;
			}

		#$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
		$query_img5 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
			$makefigures=TRUE;
			}

		#$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
		$query_img6 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="waveform-large" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
			$makefigures=TRUE;
			}

		#$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
		$query_img7 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram-large" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
			$makefigures=TRUE;
			}
		}
	}
elseif($sox_images==TRUE){
	if ($sound_images!=3) {
		$makefigures=TRUE;
		}
	else{
		#$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
		$query_img3 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
			$makefigures=TRUE;
			}

		#$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
		$query_img5 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram-small" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
			$makefigures=TRUE;
			}

		#$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
		$query_img7 = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram-large" AND SoundID = ' . $SoundID);
		if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
			$makefigures=TRUE;
			}
		}
	}

if ($makefigures==TRUE) {
	require("include/make_figs.php");
	}

#$sound_spectrogram=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-large'", $connection);
$sound_spectrogram = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="spectrogram-large"
	AND SoundID = ' . $SoundID);

if ($sox_images==FALSE){
	#$sound_waveform=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='waveform-large'", $connection);
	$sound_waveform = DB::column('SELECT `ImageFile` FROM `SoundsImages` WHERE ImageType="waveform-large" AND SoundID = ' . $SoundID);
	}
else{
	#$spectrogram_palette=query_one("SELECT ColorPalette FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-large' AND ImageCreator='SoX' LIMIT 1", $connection);
	$spectrogram_palette = DB::column('SELECT `ColorPalette` FROM `SoundsImages` WHERE ImageType="spectrogram-large" AND ImageCreator="SoX" LIMIT 1');
	}

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
?>

<!--
	<script type="text/javascript">
	$(window).load(function() {
		$(".loader").fadeOut("slow");
	})
	</script>
-->

<?php
	
echo "</head>";

if ($use_googlemaps=="3") {
	echo "<body onload=\"initialize()\" onunload=\"GUnload()\">";
	}
else {
	echo "<body>";
	}
	
?>

<!--Bootstrap container-->
<div class="container">
	<?php
		require("include/topbar.php");

/*	#Loading... message
	require("include/loadingtop.php");*/

	?>

	<hr noshade>

	<?php

	/*if ($sox_images==FALSE){*/
		echo "
		<div class=\"row\">
		<div class=\"col-lg-1\">&nbsp;</div>
		<div class=\"col-lg-11\">";

		#HTML5 player
		echo "<div id=\"jquery_jplayer_1\" class=\"jp-jplayer\"></div>\n";

		echo "	<div style=\"height: 460px; width: 920px; position: relative;\">";

			if ($d=="w"){
				echo "<img src=\"sounds/images/$ColID/$DirID/$sound_waveform\">";
				}
			else {
				echo "<img src=\"sounds/images/$ColID/$DirID/$sound_spectrogram\">";
				}
			
			#Marks
			if ($d!="w") {
				$resultm=mysqli_query($connection, "SELECT marks_ID FROM SoundsMarks WHERE SoundID='$SoundID'")
						or die (mysqli_error($connection));
				$nrowsm = mysqli_num_rows($resultm);
				if ($nrowsm>0) {
					if ($hidemarks!=1){
						echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID&amp;hidemarks=1&amp;d=$d&amp;hidekml=$hidekml\">Hide marks on spectrogram</a><br>";
						}
					else{
						echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID&amp;d=$d&amp;hidekml=$hidekml\">Show marks on spectrogram</a><br>";
						}
					echo "<a href=\"#\" onclick=\"window.open('db_filemarks.php?SoundID=$SoundID', 'marks', 'width=600,height=550,status=yes,resizable=yes,scrollbars=auto')\">Show list of marks</a><br>";
					}
				}

			if ($hidemarks!=1){
				require("include/showmarks_browse.php");
				}

			echo "	\n</div>
				<div id=\"jp_container_1\" class=\"jp-audio\">
					<div class=\"jp-type-single\">
						<div id=\"jp_interface_1\" class=\"jp-interface\">
							<div class=\"jp-progress\">
								<div class=\"jp-seek-bar\">
									<div class=\"jp-play-bar\"></div>
								</div>
							</div>
							<ul class=\"jp-controls\">
								<li><a href=\"javascript:;\" class=\"jp-play\" tabindex=\"1\">play</a></li>
								<li><a href=\"javascript:;\" class=\"jp-pause\" tabindex=\"1\">pause</a></li>
							</ul>
							<div class=\"jp-volume-bar\">
								<div class=\"jp-volume-bar-value\" title=\"volume\"></div>
							</div>
							<div class=\"jp-current-time\"></div>
							<div class=\"jp-duration\"></div>
						</div>

					</div>
				</div>\n";


				if ($d=="w"){
					echo "&nbsp;<a href=\"db_filedetails.php?SoundID=$SoundID&amp;hidekml=$hidekml&amp;hidemarks=$hidemarks\" style=\"position: relative; top: -28px; left: 200px; z-index: 2500;\" id=\"clickMe\">show spectrogram</a>";
					}
				else {
					echo "&nbsp;<a href=\"db_filedetails.php?SoundID=$SoundID&amp;d=w&amp;hidekml=$hidekml&amp;hidemarks=$hidemarks\" style=\"position: relative; top: -28px; left: 200px; z-index: 2500;\" id=\"clickMe\">show waveform</a>";
					}


		echo "</div>
		</div>"; #Close row
		
			
	#MD5 hash calculation
	if ($pumilio_loggedin && $special_nofiles == FALSE) {
		if (!file_exists("sounds/sounds/$ColID/$DirID/$OriginalFilename")) {
			echo "
			<div class=\"alert alert-danger center\"><img src=\"images/exclamation.png\"> The file could not be found.</div>";
			$file_error = 1;
			$username = $_COOKIE["username"];
			#$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);
			$UserID = DB::column('SELECT `UserID` from `Users` WHERE `UserName`=' . $SensorID);
			save_log($connection, $SoundID, "99", "The file sounds/sounds/$ColID/$DirID/$OriginalFilename could not be found.");
			}
		else {
			$file_error = 0;
			$file_md5hash=md5_file("sounds/sounds/$ColID/$DirID/$OriginalFilename");
			if ($MD5_hash==NULL) {
				$result_md5 = mysqli_query($connection, "UPDATE Sounds Set MD5_hash='$file_md5hash' WHERE SoundID='$SoundID'")
					or die (mysqli_error($connection));
				$MD5_hash=$file_md5hash;
				}

			if ($MD5_hash!=$file_md5hash) {
				echo "<div class=\"alert alert-danger center\"><img src=\"images/exclamation.png\"> 
					The file does not match the stored MD5 hash.</div>";
						
				save_log($connection, $SoundID, "98", "The file sounds/sounds/$ColID/$DirID/$OriginalFilename does not match the stored MD5 hash.");
				}
			}
		}



	echo "<div class=\"well well-sm\">";


		echo "<div class=\"row\">";
		echo "<div class=\"col-lg-5\">";


	     	$CollectionName = DB::column('SELECT `Collections`.`CollectionName` from `Collections`, `Sounds` WHERE `Collections`.`ColID` = `Sounds`.`ColID` AND `Sounds`.`SoundID`="' . $SoundID . '"');

			#New top infobar
			#file info
				if ($guests_can_open || $pumilio_loggedin) {
					$filename_text = "<h3><a href=\"file_obtain.php?fileid=$SoundID&method=3\" title=\"Open file for analysis\">$OriginalFilename</a></h3>";
					}
				else {
					$filename_text = "<h3>$OriginalFilename</h3>";
					}
				
			if ($guests_can_open || $pumilio_loggedin) {
				if ($file_error == 1 || $special_noopen == TRUE || $special_noprocess == TRUE){
					echo $filename_text;
					}
				else {
					echo "<form method=\"get\" action=\"file_obtain.php\" class=\"form-inline\">
					$filename_text &nbsp;&nbsp;
					<input type=\"hidden\" name=\"fileid\" value=\"$SoundID\">
					<input type=\"hidden\" name=\"method\" value=\"3\">
					<button type=\"submit\" class=\"btn btn-primary btn-xs\"> Open file in Pumilio Viewer </button>
					</form>";
					}
				}
				else{
					echo $filename_text . "<br>";
				}



			#source info
			echo "</div>
			<div class=\"col-lg-7\">
				<dl class=\"dl-horizontal\">";

				if ($HumanDate!="") {
					echo "<dt>Date</dt><dd>$HumanDate</dd>";
					}
				if ($HumanTime!="") {
					echo "<dt>Time</dt><dd>$HumanTime</dd>";
					}

				echo "<dt>Collection</dt><dd>";
					
				if ($special_wrapper==TRUE){
					echo "<a href=\"$wrapper?page=db_browse&ColID=$ColID\" title=\"Browse this collection\">";
					}
				else {
					echo "<a href=\"db_browse.php?ColID=$ColID\" title=\"Browse this collection\">";
					}
					
				echo "<strong>$CollectionName</strong></a></dd>";
					
				#site info
				if ($SiteID!="") {
					echo "<dt>Site</dt><dd>";

					$this_site = DB::row('SELECT * FROM `Sites` WHERE `SiteID`="' . $SiteID . '"');
										  
					$SiteLat = $this_site->SiteLat;
					$SiteLon = $this_site->SiteLon;
					$SiteName = $this_site->SiteName;

					if ($SiteLat!="" && $SiteLon!=""){
						if ($special_wrapper==TRUE){
							echo "<a href=\"$wrapper?page=browse_site&SiteID=$SiteID\" title=\"Browse the recordings made at this site\"><strong>$SiteName</strong></a>";
							}
						else {
							echo "<a href=\"browse_site.php?SiteID=$SiteID\" title=\"Browse the recordings made at this site\"><strong>$SiteName</strong></a>";
							}
						
						#Check if there are images of the site
						$site_pics = DB::column('SELECT COUNT(*) FROM `SitesPhotos` WHERE `SiteID`="' . $SiteID . '"');
						if ($site_pics>0) {
							echo "<a href=\"#\" title=\"Show photographs of this site\" onclick=\"window.open('sitephotos.php?SiteID=$SiteID', 'pics', 'width=550,height=400,status=yes,resizable=yes,scrollbars=yes'); return false;\">
								<img src=\"images/image.png\" alt=\"Show photographs of this site\"></a>";
							}

						if ($pumilio_loggedin==FALSE && $hide_latlon_guests){
							}
						else {
							echo "<br>Coordinates: $SiteLat, $SiteLon";
							}
						}
					else {
						echo "No site data";
						}
					}
				else {
					echo "No site data";
					}
				echo "</dd>";

			
	echo "</dl></div></div></div>";



	echo "<div class=\"row\">";
		echo "<div class=\"col-lg-4\">";#MAP

			if ($use_leaflet == TRUE){
					echo "<div id=\"map\">Your browser does not have JavaScript enabled or can not connect to the tile server. Please contact your administrator.</div>\n";
			}
			elseif ($use_googlemaps=="1" || $use_googlemaps=="3") {#Add small GMap
				if ($SiteID!="" && $SiteLat!="" && $SiteLon!=""){
					echo "<div id=\"map_canvas\" style=\"width: 320px; height: 220px\">Your browser does not have JavaScript enabled or can not connect to GoogleMaps. Please contact your administrator.</div>\n";
					if (!isset($kml_default)){
						$kml_default = 0;
						}

					if ($kml_default == 1){
						if ($hidekml==1){
							echo "<a href=\"db_filedetails.php?SoundID=$SoundID&hidekml=0&d=$d&hidemarks=$hidemarks\">Show default KML layers</a>";
							}
						else{
							echo "<a href=\"db_filedetails.php?SoundID=$SoundID&hidekml=1&d=$d&hidemarks=$hidemarks\">Hide default KML layers</a>";
							}
						}
					echo "<br>";
					}
				}



				if ($pumilio_admin) {
					echo "<br><br>
							<form method=\"get\" action=\"file_edit.php\">
							<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
							<button type=\"submit\" class=\"btn btn-primary btn-xs\"> Edit file information </button>
							</form>";

							#Delete file div
							echo "<div id=\"dialog\" title=\"Delete the file?\">
								<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The file will be permanently deleted and cannot be recovered. Are you sure?</p>
								</div>";

							echo "
							<form id=\"testconfirmJQ\" name=\"testconfirmJQ\" method=\"post\" action=\"del_file.php\">
							<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
							<button type=\"submit\" class=\"btn btn-primary btn-xs\"> Delete file from archive </button>
							</form>\n";
					}



		echo "</div>";

		echo "<div class=\"col-lg-8\">";


					#Tags
					#$use_tags=query_one("SELECT Value from PumilioSettings WHERE Settings='use_tags'", $connection);
					if ($use_tags=="1" || $use_tags=="") {
						echo "<h3><a href=\"#\">File Tags</a></h3>
							<div>";
						if ($pumilio_loggedin) {
							require("include/managetags_db.php");
							echo "<p><strong>Add tags</strong>:<form method=\"get\" action=\"include/addtag.php\">
								<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
								<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\">
								<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\">
								<br><em>Separate tags with a space</em></form><br>";
							}
						else {
							require("include/gettags.php");
							}
						echo "</div>";
						}
					

					#Marks
					if ($d!="w") {
						$no_marks = DB::column('SELECT COUNT(*) FROM `SoundsMarks` WHERE `SoundID`="' . $SoundID . '"');
						if ($no_marks>0) {
							if ($hidemarks!=1){
								echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID&amp;hidemarks=1&amp;d=$d&amp;hidekml=$hidekml\">Hide marks on spectrogram</a><br>";
								}
							else{
								echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID&amp;d=$d&amp;hidekml=$hidekml\">Show marks on spectrogram</a><br>";
								}
							echo "<a href=\"#\" onclick=\"window.open('db_filemarks.php?SoundID=$SoundID', 'marks', 'width=600,height=550,status=yes,resizable=yes,scrollbars=auto')\">Show list of marks</a><br>";
							}
						}

						


				echo "
					<dl class=\"dl-horizontal\">
						<dt>Original filename</dt>
						<dd>$OriginalFilename</dd>";


					#Check if the file size is in the database
					if ($FileSize==NULL || $FileSize==0) {
						$file_filesize=filesize("sounds/sounds/$ColID/$DirID/$OriginalFilename");
						#$result_size = mysqli_query($connection, "UPDATE Sounds SET FileSize='$file_filesize' WHERE SoundID='$SoundID' LIMIT 1")
						#	or die (mysqli_error($connection));
						$this_array = array(
							'FileSize' => $file_filesize,
							);
						DB::update('Sounds', $this_array, $SoundID, 'SoundID');
						
						$FileSize=formatSize($file_filesize);
						}
					else {
						$FileSize=formatSize($FileSize);
						}

					echo "<dt>File Format</dt><dd>$SoundFormat</dd>
						<dt>Sampling rate</dt><dd>$SamplingRate Hz</dd>
						<dt>Number of channels</dt><dd>$Channels</dd>";

					echo "
						<dt>File size</dt>
						<dd>$FileSize</dd>";


					if ($Duration>60) {
						$formated_Duration=formatTime(round($Duration));
						echo "<dt>Duration</dt><dd>$formated_Duration (hh:mm:ss)</dd>";
						}
					else {
						echo "<dt>Duration</dt><dd>$Duration seconds</dd>";
						}

					#Check if from a sample set
					if ($pumilio_loggedin) {
						$sample_check = mysqli_query($connection, "SELECT Samples.SampleName,Samples.SampleID FROM
							Samples,SampleMembers WHERE Samples.SampleID=SampleMembers.SampleID 
							AND SampleMembers.SoundID='$SoundID'")
							or die (mysqli_error($connection));
						$check_nrows = mysqli_num_rows($sample_check);
					if ($check_nrows>0) {
						$check_row = mysqli_fetch_array($sample_check);
						extract($check_row);
						echo "<dt>From the sample set</dt><dd><a href=\"browse_sample.php?SampleID=$SampleID\">$SampleName</a></dd>";
						}
					}


					$specinfo = DB::row("SELECT `SpecMaxFreq`, `ImageFFT` from `SoundsImages` WHERE `SoundID`=" . $SoundID . " AND `ImageType`='spectrogram'");
					$SpecMaxFreq = $specinfo->SpecMaxFreq;
					$ImageFFT = $specinfo->ImageFFT;

					echo "<dt>Spectrogram settings</dt>
							<dd>Max frequency: $SpecMaxFreq Hz<br>
							FFT size: $ImageFFT</dd>\n";

					if ($OtherSoundID!="") {
						echo "<dt>Custom ID</dt><dd>$OtherSoundID</dd>";
						}


					if ($SensorID!="") {
						$sensor = DB::row('SELECT `Recorder`, `Microphone`, `Notes` from `Sensors` WHERE `SensorID`=' . $SensorID);
						$Recorder = $sensor->Recorder;
						$Microphone = $sensor->Microphone;
						$SensorNotes = $sensor->Notes;

						echo "<dt>Sensor used</dt><dd>$Recorder, $Microphone ($SensorNotes)</dd>";
						}
					elseif ($SensorID == "1") {
						echo "<dt>Sensor used</dt><dd>Not set</dd>";
						}
					else{
						echo "<dt>Sensor used</dt><dd>Not set</dd>";
						}

					if ($SiteElevation != ""){
						echo "<dt>Elevation</dt><dd>$SiteElevation</dd>\n";
						}
					
					if ($SiteURL != ""){
						echo "<dt>Site URL</dt><dd>$SiteURL</dd>\n";
						}
					
					if ($Notes!="") {
						echo "<dt>Notes</dt><dd>$Notes</dd>";
						}


					$QualityFlag = DB::column('SELECT `QualityFlag` from `QualityFlags` WHERE `QualityFlagID` = ' . $QualityFlagID);
					echo "<dt>File Quality</dt><dd>$QualityFlag (Quality ID: $QualityFlagID)</dd>";
					if ($DerivedSound == "1"){
						echo "<dt>Derived from</dt><dd><a href=\"db_filedetails.php?SoundID=$DerivedFromSoundID\">$DerivedFromSoundID";
						}


					if ($pumilio_admin == TRUE) {
						echo "<form method=\"GET\" action=\"editqf.php\" target=\"editqf\" onsubmit=\"window.open('', 'editqf', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\" class=\"form-horizontal\">
						Edit the Quality Flag for this file: 
						<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">";

						$thisfile_QualityFlagID = $QualityFlagID;

						$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
						$result_qf = mysqli_query($connection, $query_qf)
							or die (mysqli_error($connection));
						$nrows_qf = mysqli_num_rows($result_qf);

						echo "<select name=\"newqf\">";
						for ($f=0;$f<$nrows_qf;$f++) {
							$row_qf = mysqli_fetch_array($result_qf);
							extract($row_qf);
							if ($QualityFlagID==$thisfile_QualityFlagID){
								echo "<option value=\"$QualityFlagID\" SELECTED>$QualityFlag ($QualityFlagID)</option>\n";
								}
							else{
								echo "<option value=\"$QualityFlagID\">$QualityFlag ($QualityFlagID)</option>\n";
								}
							}

						echo "</select>
						<button type=\"submit\" class=\"btn btn-primary btn-xs\"> Change </button>
						</form>";
						}


					echo "<dt>Database ID</dt><dd>$SoundID</dd>";


					#License
					$files_license = DB::column('SELECT `Value` from `PumilioSettings` WHERE `Settings`="files_license"');
					$files_license_detail = DB::column('SELECT `Value` from `PumilioSettings` WHERE `Settings`="files_license_detail"');

					if ($files_license != ""){
						echo "<dt>License</dt><dd>";
						if ($files_license == "Copyright"){
							echo "&#169; Copyright ";
							}
						else {
							$files_license_img = str_replace(" ", "", $files_license);
							$files_license_link = strtolower(str_replace("CC ", "", $files_license));
							echo "<p>File available under a 
								<a href=\"http://creativecommons.org/licenses/$files_license_link/3.0/\" target=_blank><img src=\"images/cc/$files_license_img.png\"></a>
								$files_license license by ";
							}

						echo "$files_license_detail</dd>\n";
						}

					if ($guests_can_dl || $pumilio_loggedin) {
						echo "<dt>Download</dt>";
						
						echo "<dd><a href=\"dl.php?file=sounds/sounds/$ColID/$DirID/$OriginalFilename\" title=\"Please read the license field on the right for legal limitations on the use of these files.\">$SoundFormat</a>";
						echo " | ";					
						echo "<a href=\"dl.php?file=sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\" title=\"Please read the license field on the right for legal limitations on the use of these files.\">$AudioPreviewFormat</a>
							</dd>";
						}


				
				echo "</dl>";
				
		echo "</div>";
	echo "</div>";#end row

require("include/bottom.php");

echo "</body>
	</html>";

<?php
if ($use_leaflet == TRUE){
	require("include/leaflet1.php");
}
?>
