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
require("include/check_admin.php");

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


$valid_id=query_one("SELECT COUNT(*) FROM Sounds WHERE SoundID='$SoundID'", $connection);
$SoundID_status = query_one("SELECT SoundStatus FROM Sounds WHERE SoundID='$SoundID'", $connection);
$SoundID_qf_check = query_one("SELECT QualityFlagID FROM Sounds WHERE SoundID='$SoundID'", $connection);

if ($d=="w"){
	$hidemarks = 1;
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - File Details</title>\n";

require("include/get_css.php");

if ($valid_id != 1) {
	echo "<body>
		<div class=\"error\" style=\"margins: 10px;\"><img src=\"images/exclamation.png\"> The file requested does not exists or the Sound ID is not valid. Please go back and try again.</div>
		</body>
		</html>";
	die();
	}

if ($SoundID_status == 9) {
	echo "<body>
		<div class=\"error\" style=\"margins: 10px;\"><img src=\"images/exclamation.png\"> The file requested was deleted.
			Please contact the administrator for more information.</div>
		</body>
		</html>";
	die();
	}

if ($SoundID_qf_check < $default_qf && $pumilio_loggedin==FALSE) {
	echo "<body>
		<div class=\"error\" style=\"margins: 10px;\"><img src=\"images/exclamation.png\"> You must be logged in to see
			this file. Please contact the administrator for more information.</div>
		</body>
		</html>";
	die();
	}


$query = "SELECT *, DATE_FORMAT(Date,'%d-%b-%Y') AS HumanDate, TIME_FORMAT(Time,'%H:%i:%s') AS HumanTime, TIME_FORMAT(Duration,'%i:%s') AS Duration_human FROM Sounds WHERE SoundID='$SoundID'";

$result=query_several($query, $connection);
$nrows = mysqli_num_rows($result);
$row = mysqli_fetch_array($result);
extract($row);

require("include/get_jqueryui.php");

?>
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
$result_all_tags=query_several($query_all_tags, $connection);
$nrows_all_tags = mysqli_num_rows($result_all_tags);

if ($nrows_all_tags>0) {
	echo "
	<!-- JQuery Autocomplete http://docs.jquery.com/Plugins/Autocomplete -->
	<script type=\"text/javascript\" src=\"$app_url/js/jquery/jquery.autocomplete.pack.js\"></script>
	<script type=\"text/javascript\">
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
	<link rel=\"stylesheet\" href=\"$app_url/js/jquery/jquery.autocomplete.css\" type=\"text/css\">
	";
	}
	
#flush();
#require("include/update_sites.php");
if ($use_googlemaps=="1" || $use_googlemaps=="3") {
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
		$SiteID=query_one("SELECT Sites.SiteID FROM Sites,Sounds WHERE Sites.SiteID=Sounds.SiteID AND Sounds.SoundID='$SoundID' LIMIT 1", $connection);
		$SiteLat=query_one("SELECT SiteLat FROM Sites WHERE SiteID='$SiteID' LIMIT 1", $connection);
		$SiteLon=query_one("SELECT SiteLon FROM Sites WHERE SiteID='$SiteID' LIMIT 1", $connection);
		$SiteName=query_one("SELECT SiteName FROM Sites WHERE SiteID='$SiteID' LIMIT 1", $connection);

		require("include/db_filedetails_map_head.php");
		}
	}

#HTML5 player
# http://www.jplayer.org
echo "\n<link href=\"$app_url/html5player/jplayer.css\" rel=\"stylesheet\" type=\"text/css\">";
echo "\n<script type=\"text/javascript\" src=\"$app_url/js/jquery.jplayer.min.js\"></script>\n";

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
				$AudioPreviewFormat: \"$app_url/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\"
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
$query_img = "SELECT COUNT(*) FROM SoundsImages WHERE SoundID='$SoundID'";
	$sound_images=query_one($query_img, $connection);
	if ($sox_images==FALSE){
		if ($sound_images!=6) {
			$makefigures=TRUE;
			}
		else{
			$query_img2 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img2")) {
				$makefigures=TRUE;
				}
	
			$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
				$makefigures=TRUE;
				}
	
			$query_img4 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img4")) {
				$makefigures=TRUE;
				}
	
			$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
				$makefigures=TRUE;
				}
	
			$query_img6 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='waveform-large'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img6"))	{
				$makefigures=TRUE;
				}
	
			$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
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
			$query_img3 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img3")) {
				$makefigures=TRUE;
				}
	
			$query_img5 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-small'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img5"))	{
				$makefigures=TRUE;
				}
	
			$query_img7 = query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' and ImageType='spectrogram-large'", $connection);
			if (!is_file("$absolute_dir/sounds/images/$ColID/$DirID/$query_img7"))	{
				$makefigures=TRUE;
				}
			}
		}

if ($makefigures==TRUE) {
	require("include/make_figs.php");
	}

$sound_spectrogram=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-large'", $connection);

if ($sox_images==FALSE){
	$sound_waveform=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='waveform-large'", $connection);
	}
else{
	$spectrogram_palette=query_one("SELECT ColorPalette FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-large' AND ImageCreator='SoX' LIMIT 1", $connection);
	}

if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
?>

</head>
<?php

if ($use_googlemaps=="1" || $use_googlemaps=="3") {
	echo "<body onload=\"initialize()\" onunload=\"GUnload()\">";
	}
else {
	echo "<body>";
	}
	
?>

	<!-- Scripts for Javascript tooltip from http://www.walterzorn.com/tooltip/tooltip_e.htm -->
	<!-- For marks data -->
         <script type="text/javascript" src="js/wz_tooltip/wz_tooltip.js"></script>

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
		
		<div class="span-24 last" id="loadingdiv2">
			&nbsp;
			<?php
				flush();
			?>
		</div>
			<?php

			$source_name=query_one("SELECT Collections.CollectionName from Collections,Sounds WHERE Collections.ColID=Sounds.ColID AND Sounds.SoundID='$SoundID'", $connection);

			#New top infobar
			#file info
			echo "<div class=\"span-8\">
				<p class=\"highlight3 ui-corner-all\">";
				if ($guests_can_open || $pumilio_loggedin) {
					echo "<a href=\"file_obtain.php?fileid=$SoundID&method=3\" title=\"Open file for analysis\" style=\"color: white;\"><strong>$SoundName</strong></a>";
					}
				else {
					echo "<strong>$SoundName</strong>";
					}
				if ($Date!="" && $Time!="") {
					echo "<br>$HumanDate $HumanTime";
					}
				elseif ($Date!="") {
					echo "<br>Date: $HumanDate";
					}
				elseif ($Time!="") {
					echo "<br>Time: $HumanTime";
					}
				echo "</p>
			</div>";

			#source info
			echo "<div class=\"span-8\">
				<p class=\"highlight3 ui-corner-all\">Collection: ";
				
			if ($special_wrapper==TRUE){
				echo "<a href=\"$wrapper?page=db_browse&ColID=$ColID\" title=\"Browse this collection\" style=\"color: white;\">";
				}
			else {
				echo "<a href=\"db_browse.php?ColID=$ColID\" title=\"Browse this collection\" style=\"color: white;\">";
				}
				
			echo "<strong>$source_name</strong></a></p>
			</div>";
				
			#site info
			echo "<div class=\"span-8 last\">
				<p class=\"highlight3 ui-corner-all\">";

			if ($SiteID!="") {
				$result_site=query_several("SELECT * FROM Sites WHERE SiteID=$SiteID LIMIT 1", $connection);
				$row_site = mysqli_fetch_array($result_site);
				extract($row_site);

				if ($SiteLat!="" && $SiteLon!=""){
					if ($special_wrapper==TRUE){
						echo "Site: <a href=\"$wrapper?page=browse_site&SiteID=$SiteID\" title=\"Browse the recordings made at this site\" style=\"color: white;\"><strong>$SiteName</strong></a>";
						}
					else {
						echo "Site: <a href=\"browse_site.php?SiteID=$SiteID\" title=\"Browse the recordings made at this site\" style=\"color: white;\"><strong>$SiteName</strong></a>";
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
			
			echo "</p>
			</div>";
			#END NEW Topbar

			echo "
			<div class=\"span-24 last\">";

			#HTML5 player
			echo "<div id=\"jquery_jplayer_1\" class=\"jp-jplayer\"></div>\n";

			echo "	<div style=\"height: 460px; width: 920px; position: relative;\">";

			if ($sox_images==FALSE){
				if ($d=="w"){
					echo "<img src=\"$app_url/sounds/images/$ColID/$DirID/$sound_waveform\">";
					}
				else {
					echo "<img src=\"$app_url/sounds/images/$ColID/$DirID/$sound_spectrogram\">";
					}
				}
			else{
				echo "<img src=\"$app_url/sounds/images/$ColID/$DirID/$sound_spectrogram\">";
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


			if ($sox_images==FALSE){
				#dropped waveform when using SoX
				if ($d=="w"){
					echo "&nbsp;<a href=\"db_filedetails.php?SoundID=$SoundID&hidekml=$hidekml&hidemarks=$hidemarks\" style=\"position: relative; top: -28px; left: 200px; z-index: 2500;\" id=\"clickMe\">show spectrogram</a>";
					}
				else {
					echo "&nbsp;<a href=\"db_filedetails.php?SoundID=$SoundID&d=w&hidekml=$hidekml&hidemarks=$hidemarks\" style=\"position: relative; top: -28px; left: 200px; z-index: 2500;\" id=\"clickMe\">show waveform</a>";
					}
				}
			else{
				echo "&nbsp;<a href=\"#\" style=\"position: relative; top: -28px; left: 200px; z-index: 2500;\" onclick=\"window.open('images/SoX$spectrogram_palette.png', 'scale', 'width=20,height=434,status=no,resizable=no,scrollbars=no')\">show scale</a>";
				}

		echo "</div>";
				
		#MD5 hash calculation
		if ($pumilio_loggedin && $special_nofiles == FALSE) {
			if (!file_exists("sounds/sounds/$ColID/$DirID/$OriginalFilename")) {
				echo "<div class=\"span-24 last\" style=\"text-align: center;\"><div class=\"error\"><img src=\"images/exclamation.png\"> The file could not be found.</div></div>";
				$file_error = 1;
				$username = $_COOKIE["username"];
				$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);
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
					echo "<div class=\"span-24 last\" style=\"text-align: center;\"><div class=\"error\"><img src=\"images/exclamation.png\"> 
						The file does not match the stored MD5 hash.</div></div>";
							
					save_log($connection, $SoundID, "98", "The file sounds/sounds/$ColID/$DirID/$OriginalFilename does not match the stored MD5 hash.");
					}
				}
			}

		echo "<div class=\"span-10\">";							
		
		#Marks
		if ($d!="w") {
			$resultm=mysqli_query($connection, "SELECT marks_ID FROM SoundsMarks WHERE SoundID='$SoundID'")
					or die (mysqli_error($connection));
			$nrowsm = mysqli_num_rows($resultm);
			if ($nrowsm>0) {
				if ($hidemarks!=1){
					echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID&hidemarks=1&d=$d&hidekml=$hidekml\">Hide marks on spectrogram</a><br>";
					}
				else{
					echo "<p><a href=\"db_filedetails.php?SoundID=$SoundID&d=$d&hidekml=$hidekml\">Show marks on spectrogram</a><br>";
					}
				echo "<a href=\"#\" onclick=\"window.open('db_filemarks.php?SoundID=$SoundID', 'marks', 'width=600,height=550,status=yes,resizable=yes,scrollbars=auto')\">Show list of marks</a><br>";
				}
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
					echo "<p>From the sample set: <a href=\"browse_sample.php?SampleID=$SampleID\">$SampleName</a>";
					}
				}

			#Tags
			$use_tags=query_one("SELECT Value from PumilioSettings WHERE Settings='use_tags'", $connection);
			if ($use_tags=="1" || $use_tags=="") {
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
				}

			#File quality data
			$QualityFlag=query_one("SELECT QualityFlag from QualityFlags WHERE QualityFlagID='$QualityFlagID'", $connection);
			echo "<p><strong>Record quality data</strong>:
				<ul>";
			echo "<li>Quality flag: $QualityFlagID ($QualityFlag)</li>";
			if ($DerivedSound == "1"){
				echo "<li>Derived from: <a href=\"db_filedetails.php?SoundID=$DerivedFromSoundID\">$DerivedFromSoundID</li>";
				}
			echo "</ul>";

			if ($pumilio_admin == TRUE) {
				echo "<form method=\"GET\" action=\"editqf.php\" target=\"editqf\" onsubmit=\"window.open('', 'editqf', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
				Edit the Quality Flag for this file:<br>
				<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">";

				$thisfile_QualityFlagID = $QualityFlagID;

				$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
				$result_qf = mysqli_query($connection, $query_qf)
					or die (mysqli_error($connection));
				$nrows_qf = mysqli_num_rows($result_qf);

				echo "<select name=\"newqf\" class=\"ui-state-default ui-corner-all formedge\">";
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

				echo "</select><br>
				<input type=submit value=\" Change \" class=\"fg-button ui-state-default ui-corner-all\">
				</form><br>";
				}

			#File technical data
			echo "<p><strong>File data</strong>:
			<ul>
			<li>Original filename: $OriginalFilename";
				if ($guests_can_dl || $pumilio_loggedin) {
					echo "<br>&nbsp;&nbsp;&nbsp;Download: ";
					if ($special_nofiles == FALSE){
						echo "<a href=\"dl.php?file=sounds/sounds/$ColID/$DirID/$OriginalFilename\">$SoundFormat</a>";
						if ($SoundFormat != "wav" && $special_noprocess==FALSE){
							echo " | <a href=\"dl.php?from_detail=1&SoundID=$SoundID\">wav</a>";
							}
						echo " | ";
						}
					echo "<a href=\"dl.php?file=sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\">$AudioPreviewFormat</a>
						</li>";
					}

			if ($Date!="") {
				echo "<li>Date: $HumanDate</li>";
				}
			if ($Time!="") {
				echo "<li>Time: $HumanTime</li>";
				}
			if ($Duration>60) {
				$formated_Duration=formatTime(round($Duration));
				echo "<li>Duration: $formated_Duration (hh:mm:ss)</li>";
				}
			else {
				echo "<li>Duration: $Duration seconds</li>";
				}
				
			echo "	<li>File Format: $SoundFormat</li>
				<li>Sampling rate: $SamplingRate Hz</li>
				<li>Number of channels: $Channels</li>";

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
			echo "<li>File size: $FileSize</li>";
			echo "<li>Database ID: $SoundID</li>";
			if ($OtherSoundID!="") {
				echo "<li>Custom ID: $OtherSoundID</li>";
				}
			
			if ($SensorID!="") {
				#$Recorder=query_one("SELECT Recorder from Sensors WHERE SensorID=$SensorID LIMIT 1", $connection);
				$Recorder = DB::column('SELECT `Recorder` from `Sensors` WHERE `SensorID`=' . $SensorID . ' LIMIT 1');
				#$Microphone=query_one("SELECT Microphone from Sensors WHERE SensorID=$SensorID LIMIT 1", $connection);
				$Microphone = DB::column('SELECT `Microphone` from `Sensors` WHERE `SensorID`=' . $SensorID . ' LIMIT 1');
				#$SensorNotes=query_one("SELECT Notes from Sensors WHERE SensorID=$SensorID LIMIT 1", $connection);
				$SensorNotes = DB::column('SELECT `Notes` from `Sensors` WHERE `SensorID`=' . $SensorID . ' LIMIT 1');
				echo "<li>Sensor used: $Recorder, $Microphone ($SensorNotes)</li>";
				}
			elseif ($SensorID == "1") {
				echo "<li>Sensor used: Not set</li>";
				}
			else{
				echo "<li>Sensor used: Not set</li>";
				}
			
			if ($Notes!="") {
				echo "<li>Notes: $Notes</li>";
				}

			echo "</ul>";

			#Other data associated with this file
			$dir="data_sources/";
 			$other_data=scandir($dir);
	 
	 		if (count($other_data)>0) {
	 			for ($o=0;$o<count($other_data);$o++) {
 					if (strpos(strtolower($other_data[$o]), ".php")) {
						require("$dir/$other_data[$o]");
 						}
	 				}
	 			}

			#Find weather data
			$weather_data_id=get_closest_weather($connection,$SiteLat, $SiteLon,$Date,$Time);
			$weather_data=explode(",",$weather_data_id);
			$weather_data_id=$weather_data[0];
			$time_diff=round(($weather_data[1]/60));
			$distance=round($weather_data[2],2);
			if ($weather_data_id!=0 && $time_diff<60) {
				$result_w = mysqli_query($connection, "SELECT * FROM WeatherData WHERE WeatherDataID='$weather_data_id' LIMIT 1")
					or die (mysqli_error($connection));
				$row_w = mysqli_fetch_array($result_w);
				extract($row_w);

				echo "<p><strong>Weather data</strong>: (From $distance km, $time_diff min)\n <ul>";
				if ($Temperature!=NULL)
					echo "<li>Temp: $Temperature &deg;C";
				if ($Precipitation!=NULL)
					echo "<li>Precipitation: $Precipitation mm";
				if ($RelativeHumidity!=NULL)
					echo "<li>Relative Humidity: $RelativeHumidity %";
				if ($WindSpeed!=NULL)
					echo "<li>Wind Speed: $WindSpeed m/s";
				if ($WindDirection!=NULL)
					echo "<li>Wind Direction: $WindDirection";
				if ($LightIntensity!=NULL)
					echo "<li>Light Intensity: $LightIntensity";
				if ($BarometricPressure!=NULL)
					echo "<li>Barometric Pressure: $BarometricPressure";
				if ($DewPoint!=NULL)
					echo "<li>Dew Point: $DewPoint";
					
				echo "</ul>";
				}
				
			echo "</div>\n";

		echo "<div class=\"span-5\">\n";
		
		#$username = $_COOKIE["username"];
		#Check if user can edit files (i.e. has admin privileges)
		if ($pumilio_admin) {
			echo "<p><strong>Administrative options</strong>:
			<form method=\"get\" action=\"file_edit.php\">
			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
			<input type=\"submit\" value=\" Edit file information \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>";
	
			#Delete file div
			echo "<div id=\"dialog\" title=\"Delete the file?\">
				<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The file will be permanently deleted and cannot be recovered. Are you sure?</p>
			</div>";

			echo "<p>
			<form id=\"testconfirmJQ\" name=\"testconfirmJQ\" method=\"post\" action=\"del_file.php\">
			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
			<input type=\"submit\" value=\" Delete file from archive \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>";
			}
		if ($guests_can_open || $pumilio_loggedin) {
			echo "<p>";
			if ($file_error == 1 || $special_noopen == TRUE || $special_noprocess == TRUE){
				}
			else {
				echo "<form method=\"get\" action=\"file_obtain.php\">
				<input type=\"hidden\" name=\"fileid\" value=\"$SoundID\">
				<input type=\"hidden\" name=\"method\" value=\"3\">
				<input type=\"submit\" value=\" Open file \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}
				
			}

			echo "&nbsp;</div>";	
			echo "<div class=\"span-9 last\">";
			#Add small GMap
			if ($use_googlemaps=="1" || $use_googlemaps=="3") {
				if ($SiteID!="" && $SiteLat!="" && $SiteLon!=""){
					echo "\n<p>Map:<br>
						<div id=\"map_canvas\" style=\"width: 320px; height: 220px\">Your browser does not have JavaScript enabled, which is required to proceed, or can not connect to GoogleMaps. Please contact your administrator.</div>\n";
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

			#License
			$files_license = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license'", $connection);
			$files_license_detail = query_one("SELECT Value from PumilioSettings WHERE Settings='files_license_detail'", $connection);

			if ($files_license != ""){
				echo "<div class=\"notice\"><strong>License:</strong><br>\n";
				if ($files_license == "Copyright"){
					echo "&#169; Copyright: ";
					}
				else {
					$files_license_img = str_replace(" ", "", $files_license);
					$files_license_link = strtolower(str_replace("CC ", "", $files_license));
					echo "<p>File available under a <a href=\"http://creativecommons.org/licenses/$files_license_link/3.0/\" target=_blank><img src=\"images/cc/$files_license_img.png\"></a> $files_license license: ";
					}
		
				echo "\n<br>$files_license_detail</div>\n";
				}


			echo "&nbsp;</div>";	
			flush();

			?>
			
		<div class="span-24 last">	
			<script type="text/javascript">
				function hidediv()
				      {
					loadingdiv.style.visibility= "hidden";
					loadingdiv2.style.visibility= "hidden";
					loadingdiv.style.height= "0";
					loadingdiv2.style.height= "0";
				      };
			
				hidediv();
			</script>
		
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>
