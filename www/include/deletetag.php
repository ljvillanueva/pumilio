<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$TagID=filter_var($_GET["TagID"], FILTER_SANITIZE_NUMBER_INT);
$where_to=filter_var($_GET["where_to"], FILTER_SANITIZE_URL);

$query_tags = "DELETE FROM Tags WHERE TagID='$TagID'";
$result_tags = mysqli_query($connection, $query_tags)
	or die (mysqli_error($connection));

// Relocate back to the first page of the application
	if ($_GET["goto"]=="db")	{
		header("Location: ../db_filedetails.php?SoundID=$SoundID");
		die();
		}
	elseif ($_GET["goto"]=="o")	{
		header("Location: $where_to");
		die();
		}
	else {
		header("Location: ../db_filedetails.php?SoundID=$SoundID");
		die;
		}
?>
