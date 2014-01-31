<?php

$fileName_exp=explode(".", $soundfile_name);
$filename='tmp/' . $random_cookie . '/' . $soundfile_wav;

if (!file_exists($filename))
	die("<div class=\"error\">The file was not found. Please try again.</div>");

$random_dir=rand(500, 150000);
mkdir("tmp/" . $random_cookie . '/' . $random_dir, 0777);
sleep(2);

if ($process=="format")	{
	if ($convert_to=="flac") {
		#FLAC file
		exec('flac ' . $filename . ' -f -o tmp/' . $random_cookie . '/' . $random_dir . '/' . $fileName_exp[0] . ".flac", $lastline, $retval);
		if ($retval!=0) {
			die("<div class=\"error\">There was a problem with the FLAC encoder...</div>");
			}
		else
			echo "<a href=\"dl.php?file=tmp/$random_cookie/$random_dir/" . $fileName_exp[0] . ".flac\">Download converted file</a>.";
		}
	elseif ($convert_to=="wav") {
		echo "<a href=\"dl.php?file=tmp/$random_cookie/$soundfile_wav\">Download converted file</a>.";
		}
	elseif ($convert_to=="mp3") {
		#MP3 file using lame
			exec('lame -f ' . $filename . ' tmp/' . $random_cookie . '/' . $random_dir . '/' . $fileName_exp[0] . ".mp3", $lastline, $retval);
		if ($retval!=0) {
			die("<div class=\"error\">There was a problem with the LAME encoder...</div>");
			}
		else {
			echo "<a href=\"dl.php?file=tmp/$random_cookie/$random_dir/" . $fileName_exp[0] . ".mp3\">Download converted file</a>.";
			}
		}
	else {
		#Any other format, use SoX
		exec('sox ' . $filename . ' tmp/' . $random_cookie. '/' . $random_dir . '/' . $fileName_exp[0] . "." . $convert_to, $lastline, $retval);
		if ($retval!=0) {
			die("<div class=\"error\">There was a problem with SoX...</div>");
			}
		else {
			echo "<a href=\"dl.php?file=tmp/$random_cookie/$random_dir/" . $fileName_exp[0] . ".$convert_to\">Download converted file</a>.";
			}
		}
	}
elseif ($process=="sampling"){
		
	#Use SoX
	exec('sox tmp/' . $random_cookie . '/' . $soundfile_name . ' -r ' . $samp . ' tmp/' . $random_cookie. '/' . $random_dir . '/' . $soundfile_name, $lastline, $retval);
	if ($retval!=0) {
		die("<div class=\"error\">There was a problem with SoX...</div>");
		}
	else {
		echo "<a href=\"dl.php?file=tmp/$random_cookie/$random_dir/$soundfile_name\">Download converted file</a>.";
		}
	}
elseif ($process=="channels12") {
	#Use SoX
	exec('sox tmp/' . $random_cookie . '/' . $soundfile_name . ' -c 2 tmp/' . $random_cookie. '/' . $random_dir . '/' . $soundfile_name, $lastline, $retval);
	if ($retval!=0) {
		die("<div class=\"error\">There was a problem with SoX...</div>");
		}
	else {
		echo "<a href=\"dl.php?file=tmp/$random_cookie/$random_dir/$soundfile_name\">Download converted file</a>.";
		}
	}
elseif ($process=="channels21") {
	#Use SoX
	exec('sox tmp/' . $random_cookie . '/' . $soundfile_name . ' -c 1 tmp/' . $random_cookie. '/' . $random_dir . '/' . $soundfile_name, $lastline, $retval);
	if ($retval!=0) {
		die("<div class=\"error\">There was a problem with SoX...</div>");
		}
	else {
		echo "<a href=\"dl.php?file=tmp/$random_cookie/$random_dir/$soundfile_name\">Download converted file</a>.";
		}
	}

echo "<br><br>";

?>
