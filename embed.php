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

#Sanitize inputs
$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$show_spectrogram=filter_var($_GET["spectrogram"], FILTER_SANITIZE_NUMBER_INT);

if ($show_spectrogram==""){
	$show_spectrogram=1;
	}

$valid_id=query_one("SELECT COUNT(*) FROM Sounds WHERE SoundID='$SoundID'", $connection);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name</title>";

require("include/get_css.php");

if ($valid_id!=1) {
	echo "<body>
		<div class=\"error\" style=\"margins: 10px;\">The file requested does not exists or the Sound ID is not valid. Please go back and try again.</div>
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

#HTML5 player
# http://www.jplayer.org
echo "\n<link href=\"$app_url/html5player/jplayer_small.css\" rel=\"stylesheet\" type=\"text/css\" />
<script type=\"text/javascript\" src=\"$app_url/js/jquery.jplayer.min.js\"></script>\n";

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
		ready: function () {
			$(this).jPlayer(\"setMedia\", {
				mp3: \"$app_url/sounds/previewsounds/$ColID/$DirID/$AudioPreviewFilename\"
			}).jPlayer(\"pause\");
		},
		solution: \"flash, html\",
		volume: \"0.9\",
		swfPath: \"$app_url/js\",
		supplied: \"mp3\",
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
	if ($sound_images!=6) {
		$makefigures=TRUE;
	}
else {	
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

if ($makefigures==TRUE) {
	require("include/make_figs.php");
	}

$sound_spectrogram=query_one("SELECT ImageFile FROM SoundsImages WHERE SoundID='$SoundID' AND ImageType='spectrogram-small'", $connection);

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

<div style="width: 300px;">

<?php

$source_name=query_one("SELECT Collections.CollectionName from Collections,Sounds WHERE Collections.ColID=Sounds.ColID AND Sounds.SoundID='$SoundID'", $connection);

#HTML5 player
echo "<div id=\"jquery_jplayer_1\" class=\"jp-jplayer\"></div>\n";

	if ($show_spectrogram=="1"){
		echo "	<div style=\"height: 150px; width: 300px; position: relative;\">";
		echo "<img src=\"$app_url/sounds/images/$ColID/$DirID/$sound_spectrogram\">
		</div>\n";
		}

	echo "<div class=\"jp-audio\">
			<div class=\"jp-type-single\">
				<div id=\"jp_interface_1\" class=\"jp-interface\">
					<div class=\"jp-progress\">
						<div class=\"jp-seek-bar\">
							<div class=\"jp-play-bar\"></div>
						</div>
					</div>
					<ul class=\"jp-controls\">
						<li><a href=\"#\" class=\"jp-play\" tabindex=\"1\">play</a></li>
						<li><a href=\"#\" class=\"jp-pause\" tabindex=\"1\">pause</a></li>
					</ul>
					<div class=\"jp-current-time\"></div>
					<div class=\"jp-duration\"></div>
				</div>

			</div>
		</div>\n";

	
		echo "<p><small>Filename: $OriginalFilename<br>";
		echo "Date: $HumanDate";
		if ($Time!=""){
			echo " Time: $HumanTime";
			}
		echo "<br>ID: $SoundID</small>";
				
?>
</div>
</body>
</html>
