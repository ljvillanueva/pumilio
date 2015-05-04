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

$force_loggedin = TRUE;
require("include/check_login.php");

$Token=filter_var($_GET["Token"], FILTER_SANITIZE_STRING);
$markID=filter_var($_GET["markID"], FILTER_SANITIZE_NUMBER_INT);
$e=filter_var($_GET["e"], FILTER_SANITIZE_NUMBER_INT);

if ($e==1) {
	$markq=filter_var($_GET["markq"], FILTER_SANITIZE_STRING);
	$result = mysqli_query($connection, "UPDATE SoundsMarks SET mark_tag='$markq' WHERE marks_ID='$markID' LIMIT 1")
		or die (mysqli_error($connection));
	}

echo "<!DOCTYPE html>
<html>
<head>
<title>$app_custom_name - Edit Marks</title>";

#Get CSS
require("include/get_css3.php");
require("include/get_jqueryui.php");

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

<div style="padding: 10px;">

<?php

$result=mysqli_query($connection, "SELECT * FROM SoundsMarks, Sounds WHERE SoundsMarks.marks_ID='$markID' AND SoundsMarks.SoundID=Sounds.SoundID LIMIT 1")
	or die (mysqli_error($connection));;
$row = mysqli_fetch_array($result);
extract($row);

echo "<h4>Edit mark for the file $SoundName (ID: $SoundID):</h4>";
			
echo "

<form action=\"editmarks.php\" method=\"GET\" id=\"EditForm\">
	<input name=\"e\" type=\"hidden\" value=\"1\">
	<input name=\"markID\" type=\"hidden\" value=\"$markID\">
	<input name=\"Token\" type=\"hidden\" value=\"$Token\">
	<p>$mark_tag_name:
		&nbsp;<input name=\"markq\" type=\"text\" maxlength=\"200\" size=\"26\" value=\"$mark_tag\" class=\"form-control\"><br>
	ID:$marks_ID<br>
	Time: $time_min - $time_max sec<br>
	Frequency: $freq_min - $freq_max Hz<br><br>
	&nbsp;&nbsp;
	<button type=\"submit\" class=\"btn btn-sm btn-primary\">Edit Mark</button>
</form>";

if ($e==1) {
	echo "<br><br><h3><a href=\"managemarks.php?Token=$Token\">Go back</a></h3>";
	}
else {
	echo "<br><br><p><a href=\"managemarks.php?Token=$Token\">Cancel and go back</a>";
	}
?>

</div>
</body>
</html>
