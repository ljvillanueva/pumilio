<?php

$dir = filter_var($_POST["dir"], FILTER_SANITIZE_URL);

#Fields
$ColID = filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID = filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID = filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);

#Check codes
$codedyear = filter_var($_POST["codedyear"], FILTER_SANITIZE_STRING);
$codedmonth = filter_var($_POST["codedmonth"], FILTER_SANITIZE_STRING);
$codedday = filter_var($_POST["codedday"], FILTER_SANITIZE_STRING);
$codedhour = filter_var($_POST["codedhour"], FILTER_SANITIZE_STRING);
$codedminutes = filter_var($_POST["codedminutes"], FILTER_SANITIZE_STRING);
$codedseconds = filter_var($_POST["codedseconds"], FILTER_SANITIZE_STRING);
$sm = filter_var($_POST["sm"], FILTER_SANITIZE_NUMBER_INT);
$files_to_process_counter = filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);

if ($dir==""){
	die("The server did not get which directory to use. Please go back and try again.");
	}

#Check if all the codes required are present
if ($codedyear == "") {
	$codeerror=1;
	}
if ($codedmonth == "") {
	$codeerror=1;
	}
if ($codedday == "") {
	$codeerror=1;
	}
if ($codedhour == "") {
	$codeerror=1;
	}
if ($codedminutes == "") {
	$codeerror=1;
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
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php

			if ($sm==1) {
				echo "<h3>Add sound files from a Wildlife Acoustic SongMeter</h3>";
				}
			else {
				echo "<h3>Add sound files from the field</h3>";
				}

			if ($codeerror && $sm!=1) {
				echo "<div class=\"error\">One or more of the required codes are not present, seconds is the only optional code. Please go back and try again.</div>";
				}
			else {
				#Save values in cookies to avoid entering them all the time
				setcookie("codedyear", $codedyear, time()+(3600*24*30), $app_dir);
				setcookie("codedmonth", $codedmonth, time()+(3600*24*30), $app_dir);
				setcookie("codedday", $codedday, time()+(3600*24*30), $app_dir);
				setcookie("codedhour", $codedhour, time()+(3600*24*30), $app_dir);
				setcookie("codedminutes", $codedminutes, time()+(3600*24*30), $app_dir);
				setcookie("codedseconds", $codedseconds, time()+(3600*24*30), $app_dir);
				
				$handle = opendir($dir);
				$cc = 1;
				while (false !== ($file = readdir($handle)) && $cc == 1) {
				        if ($file != "." && $file != "..") {
						$afile = $file;
						$cc = 2;
				        	}
					}
				closedir($handle);

				if ($sm==1) {	
					$bfile=explode(".",$afile);
					$bfile_size = count($bfile);
					$bfile_size = $bfile_size - 1;
					$ext_offset=strlen($bfile[$bfile_size]);
					$this_file_format = $bfile[$bfile_size];
					$this_file_format = strtolower($this_file_format);
					#WA format: YYYMMDD_HHMMSS
					$yearcoded = substr($afile, -16 - $ext_offset, 4);
					$monthcoded = substr($afile, -12 - $ext_offset, 2);
					$daycoded = substr($afile, -10 - $ext_offset, 2);
					$hourcoded = substr($afile, -7 - $ext_offset, 2);
					$minutescoded = substr($afile, -5 - $ext_offset, 2);
					$secondscoded = substr($afile, -3 - $ext_offset, 2);
					}
				else {	
					$codedyear1=explode(":",$codedyear);
					$codedmonth1=explode(":",$codedmonth);
					$codedday1=explode(":",$codedday);
					$codedhour1=explode(":",$codedhour);
					$codedminutes1=explode(":",$codedminutes);
					$codedseconds1=explode(":",$codedseconds);
			
					$yearcoded = substr($afile, $codedyear1[0]-1, $codedyear1[1]-$codedyear1[0]+1);
					$monthcoded = substr($afile, $codedmonth1[0]-1, $codedmonth1[1]-$codedmonth1[0]+1);
					$daycoded = substr($afile, $codedday1[0]-1, $codedday1[1]-$codedday1[0]+1);
					$hourcoded = substr($afile, $codedhour1[0]-1, $codedhour1[1]-$codedhour1[0]+1);
					$minutescoded = substr($afile, $codedminutes1[0]-1, $codedminutes1[1]-$codedminutes1[0]+1);
					$secondscoded = substr($afile, $codedseconds1[0]-1, $codedseconds1[1]-$codedseconds1[0]+1);
					}

		
				echo "<p>The file <strong>$afile</strong> will be stored with this information:<br>
				
				&nbsp;&nbsp; Year: $yearcoded<br>
				&nbsp;&nbsp; Month: $monthcoded<br>
				&nbsp;&nbsp; Day: $daycoded<br>
				&nbsp;&nbsp; Hour: $hourcoded<br>
				&nbsp;&nbsp; Minutes: $minutescoded<br>
				&nbsp;&nbsp; Seconds: $secondscoded<br>";
				
				echo "<form action=\"add_manager.php\" method=\"POST\">";
					echo "<input type=\"hidden\" name=\"dir\" value=\"$dir\">
					<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
					<input type=\"hidden\" name=\"codedyear\" value=\"$codedyear\">
					<input type=\"hidden\" name=\"codedmonth\" value=\"$codedmonth\">
					<input type=\"hidden\" name=\"codedday\" value=\"$codedday\">
					<input type=\"hidden\" name=\"codedhour\" value=\"$codedhour\">
					<input type=\"hidden\" name=\"codedminutes\" value=\"$codedminutes\">
					<input type=\"hidden\" name=\"codedseconds\" value=\"$codedseconds\">
				
					<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
					<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
					<input type=\"hidden\" name=\"SensorID\" value=\"$SensorID\">";
				
					if ($sm==1) {
						echo "<p>If the files is coded in another format, use the <a href=\"add_from_field.php\">regular import</a>.";
						}
				
					echo "<br><br>
					<strong>If there is an error in the fields, go back to fix them.</strong><br><br>
					<input type=\"hidden\" name=\"sm\" value=\"$sm\">
					<input type=submit id=\"this_form_submit\" value=\" Insert to database \" class=\"fg-button ui-state-default ui-corner-all\" onclick=\"this.value='Please wait...'; return true;\">
				</form>\n";

				echo "<br><br>";
				}
			?>

