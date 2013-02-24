<?php
require("../../include/functions.php");
require("../../config.php");
require("../../include/apply_config.php");

$force_loggedin = TRUE;
require("../../include/check_login.php");

?>
<html>
<head>

<title>Pumilio</title>

<link rel="stylesheet" href="../../css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../../css/print.css" type="text/css" media="print">	
<!--[if IE]><link rel="stylesheet" href="../../css/ie.css" type="text/css" media="screen, projection"><![endif]-->

</head>
<body>

<?
$username = $_COOKIE["username"];

$UserID=query_one("SELECT UserID FROM Users WHERE UserName='$username' LIMIT 1", $connection);

#Sanitize inputs:
$f_min=filter_var($_POST["f_min"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$f_max=filter_var($_POST["f_max"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

#Escape to prevent SQL injection
$Species=mysqli_real_escape_string($connection, $_POST["Species"]);

if (isset($_COOKIE["fft"])) {
	$fft_size=$_COOKIE["fft"];
	}
else {
	$fft_size=2048;
	}

$soundfile_id=$_COOKIE["soundfile_id"];
$ColID=query_one("SELECT ColID FROM Sounds WHERE SoundID='$soundfile_id' LIMIT 1", $connection);

#Build query
$query = "INSERT INTO FreqRanges (SoundID, ColID, freq_min, freq_max, Species, fft_size, UserID)
	VALUES ('$soundfile_id', '$ColID', '$f_min', '$f_max', '$Species', '$fft_size', '$UserID')";

#Execute query or die and display error message
$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));


#Make the new mark into a tag
	#remove spaces
	$mark_tag=str_replace(" ","", $mark_tag);
	
	#Check that it does not exist already for this sound
	$result=query_several("SELECT Tag FROM Tags WHERE SoundID='$soundfile_id' AND Tag='$Species'", $connection);
	$nrows = mysqli_num_rows($result);
	if ($nrows==0){
		$query_tags = "INSERT INTO Tags (SoundID, Tag) VALUES ('$soundfile_id', '$Species')";
		$result_tags = mysqli_query($connection, $query_tags)
			or die (mysqli_error($connection));
		}


echo "<p class=\"success\">Record inserted in database.";
?>

<p><a href="#" onClick="window.close();">Close window.</a>

</body>
</html>
