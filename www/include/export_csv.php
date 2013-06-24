<?php

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);

$force_loggedin = TRUE;
require("check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>";

require("get_css_include.php");
require("get_jqueryui_include.php");
?>

</head>
<body>

<div style="padding: 10px;">

<?php

$query = "SELECT SoundsMarks.SoundID,SoundsMarks.time_min,SoundsMarks.time_max,SoundsMarks.freq_min,
		SoundsMarks.freq_max,SoundsMarks.mark_tag,SoundsMarks.fft_size,SoundsMarks.UserID,
		Sounds.OtherSoundID, Sounds.SoundName, Sounds.OriginalFilename
		from SoundsMarks,Collections,Sounds
		WHERE Collections.ColID='$ColID' AND
		Sounds.SoundID=SoundsMarks.SoundID AND
		Sounds.ColID=Collections.ColID AND
		Sounds.SoundStatus!='9'
		ORDER BY SoundID, time_min, freq_min";
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
$nrows = mysqli_num_rows($result);
if ($nrows==0) {
	echo "<p>There are no data records.";
	}
else {
	echo "<p><strong>Sound_ID, Other_ID, Name, Complete_filename, time_min, time_max, freq_min, freq_max, $mark_tag_name, fft_size, Username</strong><br>";
	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		$User=query_one("SELECT UserName FROM Users WHERE UserID='$UserID' LIMIT 1", $connection);
		echo "$SoundID, $OtherSoundID, $SoundName, $OriginalFilename, $time_min, $time_max, $freq_min, $freq_max, $mark_tag, $fft_size, $User<br>";
		}
	echo "</p>";
	}
		
echo "<br><br>========================================<br>\n
	Note: Time is in seconds and frequency is in Hz.";

?>

</div>

</body>
</html>
