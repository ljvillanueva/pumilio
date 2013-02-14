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

require("check_admin.php");

$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);

	$query = ("SELECT SoundID FROM Sounds WHERE ColID='$ColID'");
	$result = mysqli_query($connection, $query)
	or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);

	for ($i=0;$i<$nrows;$i++) {
		$row = mysqli_fetch_array($result);
		extract($row);
		//How many sounds associated with that source
		$result1 = mysqli_query($connection, "DELETE FROM SoundsMarks WHERE SoundID='$SoundID'")
			or die (mysqli_error($connection));
		}

	// Relocate back to where you came from

		header("Location: ../admin.php?t=4");
		die;
?>
