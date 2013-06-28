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

#Sanitize
$type=filter_var($_POST["type"], FILTER_SANITIZE_NUMBER_INT);
$samplesize=filter_var($_POST["samplesize"], FILTER_SANITIZE_NUMBER_INT);
$samplename=filter_var($_POST["samplename"], FILTER_SANITIZE_STRING);
$samplenotes=filter_var($_POST["samplenotes"], FILTER_SANITIZE_STRING);
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$sample_percent=filter_var($_POST["sample_percent"], FILTER_SANITIZE_NUMBER_INT);

$time_limits=filter_var($_POST["time_limits"], FILTER_SANITIZE_NUMBER_INT);
$time_min=filter_var($_POST["time_min"], FILTER_SANITIZE_STRING);
$time_max=filter_var($_POST["time_max"], FILTER_SANITIZE_STRING);


$query1 = ("SELECT * FROM Samples WHERE SampleName='$samplename'");
$result1 = mysqli_query($connection, $query1)
	or die (mysqli_error($connection));
$nrows1 = mysqli_num_rows($result1);
if ($nrows1>0) {
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
	<html>
	<head>";

	require("include/get_css.php");

	echo "</head>
	<body>
	<div style=\"padding: 10px;\">
	<div class=\"error\">There is a sample set with that name. Please go back and try again.</div>
	</div>
	</body>
	</html>";
	die();
	}
	
if ($type==1) {
	$query = ("INSERT INTO Samples 
		(SampleName,SampleNotes) 
		VALUES ('$samplename','$samplenotes')");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$SampleID=mysqli_insert_id($connection);
	
	$query = ("INSERT INTO SampleMembers (SampleID, SoundID) SELECT '$SampleID', SoundID FROM Sounds ORDER BY RAND() LIMIT $samplesize");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
elseif ($type==2) {
	$query = ("INSERT INTO Samples 
		(SampleName,SampleNotes) 
		VALUES ('$samplename','$samplenotes')");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$SampleID=mysqli_insert_id($connection);

	if ($time_limits){
		$query = ("INSERT INTO SampleMembers (SampleID, SoundID) SELECT '$SampleID', SoundID FROM Sounds WHERE ColID='$ColID' AND (Time>='$time_min' AND Time<='$time_max') ORDER BY RAND() LIMIT $samplesize");
		}
	else {
		$query = ("INSERT INTO SampleMembers (SampleID, SoundID) SELECT '$SampleID', SoundID FROM Sounds WHERE ColID='$ColID' ORDER BY RAND() LIMIT $samplesize");
		}
	
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
	}
elseif ($type==3) {
	$query = "SELECT * from Collections";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		
		//How many sounds associated with that source
		$no_sounds_setsample=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE ColID='$ColID'", $connection);
		
		if ($no_sounds_setsample>0) {
			$query1 = ("INSERT INTO Samples (SampleName,SampleNotes) VALUES ( CONCAT('$CollectionName', ' Sample Set'),'$samplenotes')");
			$result1 = mysqli_query($connection, $query1)
				or die (mysqli_error($connection));
			$SampleID=mysqli_insert_id($connection);

			$no_samples=ceil(($sample_percent/100)*$no_sounds_setsample);
			$query2 = ("INSERT INTO SampleMembers (SampleID, SoundID) SELECT '$SampleID', SoundID FROM Sounds WHERE ColID='$ColID' ORDER BY RAND() LIMIT $no_samples");
			$result2 = mysqli_query($connection, $query2)
				or die (mysqli_error($connection));
			}
		}
		
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
		<html>
		<head>

		<title>Pumilio</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div style=\"padding: 10px;\">
		<div class=\"success\">This sample sets were created. Return to the <a href=\"./\">homepage of the application</a></div>
		</div>
		</body>
		</html>"; 
	die();
	}
elseif ($type==4) {
	$query = "SELECT * from Sites";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		
		//How many sounds associated with that source
		$no_sounds_setsample=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SiteID='$SiteID'", $connection);
		
		if ($no_sounds_setsample>0) {
			$query1 = ("INSERT INTO Samples (SampleName,SampleNotes) VALUES ( CONCAT('$SiteName', ' Sample Set'),'$samplenotes')");
			$result1 = mysqli_query($connection, $query1)
			or die (mysqli_error($connection));
			$SampleID=mysqli_insert_id($connection);
		
			$no_samples=ceil(($sample_percent/100)*$no_sounds_setsample);
			$query2 = ("INSERT INTO SampleMembers (SampleID, SoundID) SELECT '$SampleID', SoundID FROM Sounds WHERE SiteID='$SiteID' ORDER BY RAND() LIMIT $no_samples");
			$result2 = mysqli_query($connection, $query2)
				or die (mysqli_error($connection));
			}
		}
		
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
		<html>
		<head>

		<title>Pumilio</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div style=\"padding: 10px;\">
		<div class=\"success\">This sample sets were created. Return to the <a href=\"./\">homepage of the application</a></div>
		</div>
		</body>
		</html>"; 
	die();
	}
elseif ($type==5) {
	$query = ("INSERT INTO Samples 
		(SampleName,SampleNotes) 
		VALUES ('$samplename','$samplenotes')");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	
		echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
		<html>
		<head>

		<title>Pumilio</title>

		<!-- Blueprint css -->
		<link rel=\"stylesheet\" href=\"css/screen.css\" type=\"text/css\" media=\"screen, projection\">
		<link rel=\"stylesheet\" href=\"css/print.css\" type=\"text/css\" media=\"print\">	
		<!--[if IE]><link rel=\"stylesheet\" href=\"css/ie.css\" type=\"text/css\" media=\"screen, projection\"><![endif]-->
		</head>
		<body>
		<div style=\"padding: 10px;\">
		<div class=\"success\">The sample set was created. Return to the <a href=\"./\">homepage of the application</a></div>
		</div>
		</body>
		</html>"; 
	die();
	}
elseif ($type==6) {
	$query = ("INSERT INTO Samples (SampleName,SampleNotes) VALUES ('$samplename','$samplenotes')");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$SampleID=mysqli_insert_id($connection);

	$query = ("INSERT INTO SampleMembers (SampleID, SoundID) SELECT '$SampleID', SoundID FROM Sounds WHERE Time>='$time_min' AND Time<='$time_max' ORDER BY RAND()");
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	}
// Relocate
	header("Location: browse_sample.php?SampleID=$SampleID");
	die();
?>
