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

require("include/check_login.php");

$Token=filter_var($_GET["Token"], FILTER_SANITIZE_STRING);

$username = $_COOKIE["username"];
$UserID = query_one("SELECT UserID FROM Users WHERE UserName='$username'", $connection);

$valid_token = query_one("SELECT COUNT(*) FROM Tokens WHERE TokenID='$Token' AND UserID='$UserID'", $connection);

if ($valid_token==1) {
	$soundfile_format = query_one("SELECT soundfile_format FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_duration = query_one("SELECT soundfile_duration FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_name = query_one("SELECT soundfile_name FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_wav = query_one("SELECT soundfile_wav FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_id = query_one("SELECT soundfile_id FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$no_channels = query_one("SELECT no_channels FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_samplingrate = query_one("SELECT soundfile_samplingrate FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$soundfile_samplingrateoriginal = query_one("SELECT soundfile_samplingrateoriginal FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$random_cookie = query_one("SELECT random_cookie FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	$from_db = query_one("SELECT from_db FROM Tokens WHERE TokenID='$Token' LIMIT 1", $connection);
	}

$SoundID=$soundfile_id;
$palette=$_COOKIE["palette"];

#Sanitize
$ch=filter_var($_GET["ch"], FILTER_SANITIZE_NUMBER_INT);
if ($ch=="") {
	$ch="1";
	}

if (isset($_GET["t_min"])) {
	$t_min=filter_var($_GET["t_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
if (isset($_GET["t_max"])) {
	$t_max=filter_var($_GET["t_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
if (isset($_GET["f_min"])) {
	$f_min=filter_var($_GET["f_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
if (isset($_GET["f_max"])) {
	$f_max=filter_var($_GET["f_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
	}
if (isset($_GET["filter"])) {
	$filter=filter_var($_GET["filter"], FILTER_SANITIZE_STRING);
	}
if (isset($_GET["tool"])) {
	$tool=filter_var($_GET["tool"], FILTER_SANITIZE_STRING);
	}
if (isset($_GET["showmarks"])) {
	$showmarks=filter_var($_GET["showmarks"], FILTER_SANITIZE_STRING);
	}

#Check if cookies are empty
if ($soundfile_name=="") {
	echo "
	<html>
	<head>
	<title>$app_custom_name - Pumilio Viewer</title>

	<!-- Blueprint css -->
	<link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
	<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">	
	<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->

	</head>
	<body>

	<!--Blueprint container-->
	<div class=\"container\">";

	require("include/topbar.php");

	echo "	<div class=\"span-24 last\">
			<hr noshade>
		</div>
		<div class=\"span-24 last\">
			<div class=\"error\"><h4>You do not have an open file. Please select or open a file and try again.</h4></div>
		</div>
		<div class=\"span-24 last\">";
	require("include/bottom.php");
	echo "</div></div>
		</body>
		</html>";
	die();
	}

#Image size calculations
$window_width=940;
$spectrogram_left=0;
$spectrogram_right=70;
$spectrogram_width=$window_width - ($spectrogram_left+$spectrogram_right);
$spectrogram_height=400;

#Check if frequency range is set in cookies
if ($f_min!="") {
	$frequency_min=$f_min;
	$frequency_max=$f_max;
	$frequency_range=$frequency_max-$frequency_min;
	}
elseif (isset($_COOKIE["frequency_min"])) {
	$frequency_min=$_COOKIE["frequency_min"];
	$frequency_max=$_COOKIE["frequency_max"];
	$frequency_range=$frequency_max-$frequency_min;
	}
elseif ($frequency_max<$frequency_min) {
	$frequency_min=10;
	$frequency_max=10000;
	$frequency_range=$frequency_max-$frequency_min;
	}
else {
	$frequency_min=10;
	$frequency_max=10000;
	$frequency_range=$frequency_max-$frequency_min;
	}

#Check if time is set in GET
if ($t_min!="") {
	$time_min=$t_min;
	$time_max=$t_max;
	}
else {
	$time_min=0;
	$time_max=$soundfile_duration;
	}

$total_time=$time_max-$time_min;

#Check if fft size is set
if (isset($_COOKIE["fft"])) {
	$fft_size=$_COOKIE["fft"];
	}
else {
	$fft_size=2048;
	}


//Check file format
$fileName_exp=explode(".", $soundfile_wav);

#Get color palette
$spectrogram_palette=query_one("SELECT Value FROM PumilioSettings WHERE Settings='spectrogram_palette' LIMIT 1", $connection);
if ($spectrogram_palette=="")
	$spectrogram_palette=2;
	
if ($palette!=""){
	$spectrogram_palette = $palette;
	}
else{
	$palette = $spectrogram_palette;
	}

//If wav file does not exists
$fileName_exp=explode(".", $soundfile_wav);
$filename='tmp/' . $random_cookie . '/' . $soundfile_wav;

#Image filename
$imgfile=$fileName_exp[0] . '_' . $frequency_min . '-' . $frequency_max . '_' . $time_min . '-' . $time_max . '_' . $fft_size . '_' . $ch . '_' . $spectrogram_palette . '.png';

#Web Player file calculations
$sound_zoom=$fileName_exp[0] . '_' . $frequency_min . '-' . $frequency_max . '_' . $time_min . '-' . $time_max . '.wav';
$player_file_duration = $time_max-$time_min;

$player_file=$fileName_exp[0] . '_' . $frequency_min . '-' . $frequency_max . '_' . $time_min . '-' . $time_max . '.mp3';

		
echo "
<html>
<head>
<script type=\"text/javascript\" src=\"mediaplayer/swfobject.js\"></script>
<title>$app_custom_name - Pumilio Viewer</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

?>

<!-- Scripts for Javascript Click app JCrop -->
<script src="js/jquery.Jcrop.min.js"></script>
<link rel="stylesheet" href="js/jquery.Jcrop.css" type="text/css" />

<script language="Javascript">
	// Initialize Jcrop
	jQuery(document).ready(function(){

		jQuery('#cropbox').Jcrop({
			onChange: showCoords,
			onSelect: showCoords,
			addClass: 'custom',
			bgColor: 'gray'
		});

	<?php
	echo "xmin=0;
	xmax=$soundfile_duration;";
	?>

	});

	function showCoords(c)
		{
		<?php
		echo "
			xmin=Math.round((c.x/$spectrogram_width*$total_time+$time_min)*100)/100;
			xmax=Math.round((((c.x2/$spectrogram_width)*$total_time)*100)+($time_min*100))/100;
			ymax=Math.round((c.y/$spectrogram_height)*-($frequency_max-$frequency_min)+$frequency_max);
			ymin=Math.round((c.y2/$spectrogram_height)*-($frequency_max-$frequency_min)+$frequency_max);

			if (xmin!=xmax && ymin!=ymax)
				{}
			else
				{
					xmin=$time_min;
					xmax=$time_max;
					ymin=$frequency_min;
					ymax=$frequency_max;
				}
				//form showing values
				jQuery('#x').val(xmin);
				jQuery('#x2').val(xmax);
				jQuery('#y').val(ymin);
				jQuery('#y2').val(ymax);

				//secondary form with hidden values
				jQuery('#x_2').val(xmin);
				jQuery('#x2_2').val(xmax);
				jQuery('#y_2').val(ymin);
				jQuery('#y2_2').val(ymax);
				";
		?>
		document.getElementById('zoom_submit').disabled = false;
		};
</script>

<style type="text/css">	
	.fg-button1 { outline: 0; margin:0 4px 0 0; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
	.fg-button1 .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
	
	a.fg-button1 { float:left; }
	
	/* remove extra button width in IE */
	button.fg-button1 { width:auto; overflow:visible; }
	
	.fg-button1-icon-solo { display:block; width:2px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */	
	
	.fg-buttonset { float:left; }
	.fg-buttonset .fg-button1 { float: left; }
	.fg-buttonset-single .fg-button1, 
	.fg-buttonset-multi .fg-button1 { margin-right: -1px;}

</style>

<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type="text/javascript" src="js/jquery.form.js"></script> 
 
<script type="text/javascript">
     // prepare the form when the DOM is ready 
  $(document).ready(function() { 
    var options = { 
        target:        '#toolcontainer',   // target element(s) to be updated with server response 
    }; 
 
    // bind to the form's submit event 
    $('#myForm2').submit(function() { 
        // inside event callbacks 'this' is the DOM element so we first 
        // wrap it in a jQuery object and then invoke ajaxSubmit 
        $(this).ajaxSubmit(options); 
 
        // !!! Important !!! 
        // always return false to prevent standard browser submit and page navigation 
        return false; 
    }); 
}); 
</script>

<script type="text/javascript">
$(document).ready(function() { 
    var options = { 
        target:        '#tagspace',   // target element(s) to be updated with server response 
 	clearForm: true,
 	resetForm: true
    }; 
 
    // bind form using 'ajaxForm' 
    $('#addtags').ajaxForm(options); 
}); 
</script>

<?php
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
?>

</head>
<body>

<!-- Scripts for Javascript tooltip from http://www.walterzorn.com/tooltip/tooltip_e.htm -->
	<script type="text/javascript" src="js/wz_tooltip/wz_tooltip.js"></script>

	<!--Blueprint container-->
	<div class="container">
		<?php
		echo "<div class=\"span-7\">
			<a href=\"$app_dir\"><img src=\"$app_logo\"></a>
		</div>
		<div class=\"span-10\">
			<h5 class=\"highlight2 ui-corner-all\">$soundfile_name</h5>
		</div>
		<div class=\"span-7 last\">";
			require("include/toplogin.php");
		echo "</div>";

		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last" id="loadingdiv">
			<h5 class="highlight2 ui-corner-all">Please wait... loading... <img src="images/ajax-loader.gif" border="0" /></h5>
		</div>		
			<?php
			flush();
			?>
		<div class="span-8">
			
			<?php
			echo "<form id=\"myForm2\" action=\"include/gettool.php\" method=\"get\">";
			echo "<select name=\"tool\" class=\"ui-state-default ui-corner-all\" style=\"font-size:10px\">";

			//Space to include custom icons from addons
 			$dir="tools/";
 			$tools=scandir($dir);
 
			$tool_counter=0;
			$lasttool=0;
 			for ($a=0;$a<count($tools);$a++) {
				if (strpos(strtolower($tools[$a]), ".php")) {
					$lines = file("tools/$tools[$a]");
					echo "<option value=\"$tools[$a]\">$lines[2]</option>";
					$tool_counter++;
					$lasttool=$a;
					}
 				}
			if ($tool_counter==1)
				$tool=$tools[$lasttool];
 			//End addons

			echo "</select>
			<input type=submit value=\" Select tool \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\"></form>
			";

			?>

		</div>
		<div class="span-8" id="toolcontainer">
			<?php
				if (isset($tool)) {
					require("tools/$tool");
					}
				else {
					echo "&nbsp;";
					}
			?>
		</div>
		<div class="span-4">
			<?php
			if ($no_channels==2) {
				echo "<form id=\"form\" action=\"pumilio.php\" method=\"get\">
					<input type=\"hidden\" name=\"Token\" value=\"$Token\">
				Channel: <select name=\"ch\" id=\"selectchannel\" class=\"ui-state-default ui-corner-all\" style=\"font-size:10px\" onChange=\"this.form.submit();\">";
				if ($ch=="1") {
					echo "<option value=\"1\" SELECTED>LEFT</option>
					<option value=\"2\">RIGHT</option>";
					}
				elseif ($ch=="2") {
					echo "<option value=\"1\">LEFT</option>
					<option value=\"2\" SELECTED>RIGHT</option>";
					}
				echo "</select></form>";
				}
			else {
				echo "Channel: <select name=\"ch\" id=\"selectchannel\" class=\"ui-state-default ui-corner-all\" style=\"font-size:10px\" DISABLED><option value=\"1\" SELECTED>MONO</option></select>";
				}
			?>
		</div>
		<div class="span-4 last">
			<?php
			echo "<p class=\"right\">
				<a href=\"dl.php?file=tmp/$random_cookie/$imgfile\" title=\"Download spectrogram\"><img src=\"images/action_save.gif\" alt=\"Download spectrogram\"></a>
				<a href=\"convert.php?Token=$Token\" class=\"small\" title=\"Convert file\"><img src=\"images/drive_go.png\" alt=\"Convert file\"></a>
				<a href=\"file_details.php?Token=$Token\" class=\"small\" title=\"File details\"><img src=\"images/information.png\" alt=\"File details\"></a>
				<a href=\"settings.php?Token=$Token\" title=\"Visualization settings\"><img src=\"images/wrench.png\" alt=\"Visualization settings\"></a>";

			echo "<a href=\"closefile.php?Token=$Token\" class=\"small\" title=\"Close file\"><img src=\"images/cross.png\" alt=\"Close file\"></a>";
			?>
		</div>

			<?php
			#Time scale
			require('include/processor_timescale.php');
			?>

		<div class="span-22">
			<?php
			require('include/processor_view.php');

			echo "\n<script type=\"text/javascript\">
				function changeText(){
					document.getElementById('loadingdiv').innerHTML = ' <h5 class=\"highlight2\">Please wait... loading... 50% done... <img src=\"images/ajax-loader.gif\" border=\"0\" /></h5> ';
				}
				changeText();
				</script>\n";

			flush();

			require('include/processor_mp3.php');
			?>
		</div>
		<div class="span-2 last">
			<?php
				#SCALE
				$min_freq=$frequency_min;
				$max_freq=$frequency_max;
				$mid_freq=((($frequency_max-$frequency_min)/2) + $frequency_min);

				$range=$max_freq-$min_freq;
				$steps=round($range/8);
				$freq_1=$min_freq+$steps;
				$freq_2=$min_freq+($steps*2);
				$freq_3=$min_freq+($steps*3);
				$freq_4=$min_freq+($steps*4);
				$freq_5=$min_freq+($steps*5);
				$freq_6=$min_freq+($steps*6);
				$freq_7=$min_freq+($steps*7);

				echo "
				<table height=\"$spectrogram_height\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
					<tr height=\"24\"><td style=\"background:#FFFFFF;\">$max_freq Hz
					</td></tr>
					<tr height=\"50\" style=\"background:#FFFFFF;\"><td style=\"background:#FFFFFF;\">&nbsp;
					</td></tr>
					<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
					</td></tr>
					<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
					</td></tr>
					<tr height=\"50\"><td style=\"background:#FFFFFF;\">$freq_4 Hz
					</td></tr>
					<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
					</td></tr>
					<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
					</td></tr>
					<tr height=\"50\"><td style=\"background:#FFFFFF;\">&nbsp;
					</td></tr>
					<tr><td style=\"background:#FFFFFF;\">$min_freq Hz
					</td></tr>
				</table>";
			?>
		</div>

		<div class="span-4">
			<?php
				echo "
				<a href=\"#\" onclick=\"pause(); return false\" class=\"fg-button1 ui-state-default fg-button1-icon-solo ui-corner-all\" title=\"Pause\"><span class=\"ui-icon ui-icon-pause\"></span> Pause</a>
				<a href=\"#\" onclick=\"play(xmin); return false\" class=\"fg-button1 ui-state-default fg-button1-icon-solo ui-corner-all\" title=\"Play\"><span class=\"ui-icon ui-icon-play\"></span> Play</a>
				<a href=\"#\" onclick=\"stop(); return false\" class=\"fg-button1 ui-state-default fg-button1-icon-solo ui-corner-all\" title=\"Stop\"><span class=\"ui-icon ui-icon-stop\"></span> Stop</a>";

			?>
		</div>
		<div class="span-8">
			<?php
			
			#Timer
				#Use minimum time of current display to show default time
				#minutes
				if ($time_min>0) {
					$min_to_show=floor($time_min/60);
					}
				else {
					$min_to_show=0;
					}

				#seconds
				if ($time_min>0) {
					$sec_to_show=(($time_min/60)-$min_to_show)*60;
					if ($sec_to_show<10) {
						$sec_to_show="0" . $sec_to_show;
						}
					}
				else {
					$sec_to_show="00";
					}

				echo "<h1 style=\"font-size:4em;\"><div id=\"time_min_div\" style=\"float: left;\">$min_to_show</div><div style=\"float: left;\">:</div><div id=\"time_sec_div\" style=\"float: left;\">$sec_to_show</div></h1>";
			
			?>
		</div>
		<div class="span-8">
			<?php
			require("include/processor_tools.php");
			?>
		</div>
		<div class="span-4 last">
			<?php
			require("include/processor_viewport.php");
			?>
		</div>
		<div class="span-24 last">
		
			<?php
			#Tags
			$use_tags=query_one("SELECT Value from PumilioSettings WHERE Settings='use_tags'", $connection);
			if ($use_tags=="1" || $use_tags==""){
				if ($pumilio_loggedin) {
					echo "<div id=\"tagspace\"><form method=\"get\" action=\"include/addtag_ajax2.php\" id=\"addtags\">";
					require("include/managetagsp.php");
					echo "&nbsp;&nbsp;&nbsp;Add tags:
						<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
						<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
						<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\" alt=\"Add new tag\">
						<em>Separate tags with a space</em></form><br></div>
						\n\n";
					}
				else {
					require("include/gettags.php");
					}
				}
			?>
		
			<script type="text/javascript">
			function hidediv()
			      {
				loadingdiv.style.visibility= "hidden";
				loadingdiv.style.height= "0";
			      };
		
			hidediv();
			</script>
		
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>
		</div>
	</div>

</body>
</html>
