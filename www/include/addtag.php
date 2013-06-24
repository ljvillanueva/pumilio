<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);

$newtag=explode(" ",$_GET["newtag"]);
$where_to=filter_var($_GET["where_to"], FILTER_SANITIZE_URL);

foreach($newtag as $newitem){ 
	$newitem1=filter_var($newitem, FILTER_SANITIZE_STRING);
	
	#Check that it does not exist already for this sound
	$result=query_several("SELECT Tag FROM Tags WHERE SoundID='$SoundID' AND Tag='$newitem1'", $connection);
	$nrows = mysqli_num_rows($result);
	if ($nrows==0){			
		$query_tags = "INSERT INTO Tags (SoundID, Tag) VALUES ('$SoundID', '$newitem1')";
		$result_tags = mysqli_query($connection, $query_tags)
			or die (mysqli_error($connection));
		}
	}

// Relocate back to the first page of the application
	if ($_GET["goto"]=="p")	{
		header("Location: ../pumilio.php");
		die();
		}
	elseif ($_GET["goto"]=="o"){
		header("Location: $where_to");
		die();
		}
	else{
		header("Location: ../db_filedetails.php?SoundID=$SoundID");
		die;
		}
?>
