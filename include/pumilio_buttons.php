<?php

#show marks
$current_address=$_SERVER["REQUEST_URI"];

$add_id=mt_rand(1,100000);

echo "<p><strong>Tag regions</strong></p>";

	echo "<form method=\"POST\" action=\"include/add_mark1.php\" target=\"add$add_id\" onsubmit=\"window.open('', 'add$add_id', 'width=400,height=460,status=yes,resizable=yes,scrollbars=auto')\" id=\"addform\">";
	echo "<input type=\"hidden\" id=\"x_2\" name=\"t_min\" value=\"\" />
		<input type=\"hidden\" id=\"x2_2\" name=\"t_max\" value=\"\" />
		<input type=\"hidden\" id=\"y_2\" name=\"f_min\" value=\"\" />
		<input type=\"hidden\" id=\"y2_2\" name=\"f_max\" value=\"\" />
		<input type=\"hidden\" name=\"ch\" value=\"$ch\" />
		<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\" />
		<button type=\"submit\" title=\"Save selected region to the database\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon glyphicon-hdd\" aria-hidden=\"true\"></span> Save selected region</button> 
	</form><br>";


$current_address1=explode("pumilio.php?", $current_address);
$current_address2=explode("&", $current_address1[1]);
#$link="pumilio.php?tool=add_to_db.php";
$link="pumilio.php?";

for ($b=0;$b<count($current_address2);$b++){
	if (($current_address2[$b]!="showmarks=1") && ($current_address2[$b]!="tool=add_to_db.php")){
		$link=$link . "&" . $current_address2[$b];
		}
	}

$str = 'This is still a test.';
if ($link[strlen($link)-1]=="&"){
	$link_marks=$link . "showmarks=1";
	}
else {
	$link_marks=$link . "&showmarks=1";
	}


if (isset($_GET["showmarks"])){
	$showmarks = $_GET["showmarks"];
	}
else{
	$showmarks = FALSE;
	}

if ($showmarks) {
	echo "<a href=\"$link\" title=\"Hide marked regions from the display\"><button type=\"submit\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-unchecked\" aria-hidden=\"true\"></span> Hide marked regions</button></a><br>";
	}
else {
	echo "<a href=\"$link_marks\" title=\"Show marked regions in the display\"><button type=\"submit\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-modal-window\" aria-hidden=\"true\"></span> Show marked regions</button></a><br>";
	}

#Window to manage marks
if ($showmarks){
	echo "<br><a href=\"managemarks.php?Token=$Token\" target=\"managemarks$add_id\" onclick=\"window.open('managemarks.php?SoundID=$SoundID', 'managemarks$add_id', 'width=580,height=680,status=yes,resizable=yes,scrollbars=auto')\" title=\"Open window to manage the marked regions\"><button type=\"submit\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-list\" aria-hidden=\"true\"></span> Manage marks</button></a>";
	}
						
echo "<hr noshade>";





#other buttons
echo "<p><strong>Pumilio Viewer Options</strong></p>";


#select channel
if ($no_channels==2) {
				echo "<form id=\"form\" action=\"pumilio.php\" method=\"get\" class=\"form-inline\">
					<input type=\"hidden\" name=\"Token\" value=\"$Token\">
					Channel: <select name=\"ch\" id=\"selectchannel\" class=\"form-control input-sm\" title=\"Change channel in viewer\" onChange=\"this.form.submit();\">";
						if ($ch=="1") {
							echo "<option value=\"1\" SELECTED>LEFT</option>
							<option value=\"2\">RIGHT</option>";
							}
						elseif ($ch=="2") {
							echo "<option value=\"1\">LEFT</option>
							<option value=\"2\" SELECTED>RIGHT</option>";
							}
				echo "</select>
				</form>";
				}
			else {
				echo "Channel: <em>Mono file</em><br>";
				}

	echo "<br>";

	if (is_file($wav_todl)){
		echo "<form method=\"GET\" action=\"dl.php\">
			<input type=\"hidden\" name=\"file\" value=\"$wav_todl\">
			<button type=\"submit\" title=\"Download current sound\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-cloud-download\" aria-hidden=\"true\"></span> Download sound</button>
		</form><br>";
		}
	else{
		echo "<form method=\"GET\" action=\"dl.php\">
			<input type=\"hidden\" name=\"file\" value=\"$filename\">
			<button type=\"submit\" title=\"Download current sound\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-cloud-download\" aria-hidden=\"true\"></span> Download sound</button>
		</form><br>";
		}
		
	echo "<form method=\"GET\" action=\"dl.php\">
			<input type=\"hidden\" name=\"file\" value=\"tmp/$random_cookie/$imgfile\">
			<button type=\"submit\" title=\"Download current spectrogram image\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-cloud-download\" aria-hidden=\"true\"></span> Download spectrogram</button>
		</form><br>";

/*	echo "<form method=\"GET\" action=\"convert.php\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" class=\"btn btn-xs btn-primary\">Convert file</button>
		</form><br>";*/

/*	echo "<form method=\"GET\" action=\"file_details.php\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" class=\"btn btn-xs btn-primary\">File details</button>
		</form><br>";*/

	echo "<form method=\"GET\" action=\"settings.php\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" title=\"Edit viewer settings\" class=\"btn btn-xs btn-primary\"><span class=\"glyphicon glyphicon-wrench\" aria-hidden=\"true\"></span> Settings</button>
		</form><br>";

	echo "<form method=\"GET\" action=\"closefile.php\">
			<input type=\"hidden\" name=\"Token\" value=\"$Token\">
			<button type=\"submit\" title=\"Close viewer of file\" class=\"btn btn-xs btn-danger\"><span class=\"glyphicon glyphicon-remove-sign\" aria-hidden=\"true\"></span> Close Viewer</button>
		</form>";
?>


