<?php

require("functions.php");
require("../config.php");
require("apply_config_include.php");

require("check_admin.php");

$ac=filter_var($_POST["ac"], FILTER_SANITIZE_STRING);
$UserID=filter_var($_POST["UserID"], FILTER_SANITIZE_NUMBER_INT);

if ($ac=="delete") {
	if ($pumilio_admin==FALSE){
		die();
		}

		$query_file = "DELETE FROM Users WHERE UserID='$UserID'";
		$result_file = mysqli_query($connection, $query_file)
			or die (mysqli_error($connection));
		// Relocate back to where you came from

		header("Location: ../admin.php?t=3&u=3");
		die();
	}
elseif ($ac=="remadmin") {
	if ($pumilio_admin==FALSE){
		die();
		}
		
		$query_file = "UPDATE Users SET UserRole='user' WHERE UserID='$UserID'";
		$result_file = mysqli_query($connection, $query_file)
			or die (mysqli_error($connection));
		// Relocate back to where you came from

		header("Location: ../admin.php?t=3&u=3");
		die();
	}
elseif ($ac=="makeadmin") {
	if ($pumilio_admin==FALSE){
		die();
		}

		$query_file = "UPDATE Users SET UserRole='admin' WHERE UserID='$UserID'";
		$result_file = mysqli_query($connection, $query_file)
			or die (mysqli_error($connection));
		// Relocate back to where you came from

		header("Location: ../admin.php?t=3&u=3");
		die();
	}
elseif ($ac=="inactive") {
	if ($pumilio_admin==FALSE){
		die();
		}

		$query_file = "UPDATE Users SET UserActive='0' WHERE UserID='$UserID'";
		$result_file = mysqli_query($connection, $query_file)
			or die (mysqli_error($connection));
		// Relocate back to where you came from

		header("Location: ../admin.php?t=3&u=4");
		die();
	}
elseif ($ac=="activate") {
	if ($pumilio_admin==FALSE){
		die();
		}

		$query_file = "UPDATE Users SET UserActive='1' WHERE UserID='$UserID'";
		$result_file = mysqli_query($connection, $query_file)
			or die (mysqli_error($connection));
		// Relocate back to where you came from

		header("Location: ../admin.php?t=3&u=4");
		die();
	}
elseif ($ac=="selfedit") {
		$UserEmail=filter_var($_POST["UserEmail"], FILTER_SANITIZE_EMAIL);

		$query_file = "UPDATE Users SET UserEmail='$UserEmail' WHERE UserName='$username' LIMIT 1";
		$result_file = mysqli_query($connection, $query_file)
			or die (mysqli_error($connection));
		// Relocate back to where you came from

		header("Location: ../edit_myinfo.php?d=1&t=1");
		die();
	}
elseif ($ac=="editpassword") {
	$curpassword=filter_var($_POST["curpassword"], FILTER_SANITIZE_STRING);
	$newpassword1=filter_var($_POST["newpassword1"], FILTER_SANITIZE_STRING);
	$newpassword2=filter_var($_POST["newpassword2"], FILTER_SANITIZE_STRING);

	if ($newpassword1!=$newpassword2) {
		header("Location: ../edit_myinfo.php?t=2&d=4");
		die();
		}

	$enc_password = md5($curpassword);

	$query = "SELECT * FROM Users WHERE UserName = '$username' AND UserPassword = '$enc_password' AND UserActive='1'";
	// Execute the query
	$result = mysqli_query($connection, $query)
	or die ("Could not execute query. Please try again later.");
	// exactly one row? then we have found the user
	if (mysqli_num_rows($result) != 1) {
		header("Location: ../edit_myinfo.php?t=2&d=2");
		die();
		}

	$newpassword1_enc = md5($newpassword1);

	$query_file = "UPDATE Users SET UserPassword='$newpassword1_enc' WHERE UserName='$username' LIMIT 1";
	$result_file = mysqli_query($connection, $query_file)
		or die (mysqli_error($connection));


	$cookie_to_test = $_COOKIE["usercookie"];
	$cookie_to_testa = explode(".", $cookie_to_test);
	$cookie_to_test1 = $cookie_to_testa['0'];
	$cookie_to_test2 = $cookie_to_testa['1'];

	#$cookie_to_test1=filter_var($_GET["cookie_to_test1"], FILTER_SANITIZE_NUMBER_INT);
	#$cookie_to_test2=filter_var($_GET["cookie_to_test2"], FILTER_SANITIZE_STRING);
	/*
	$query = "DELETE FROM Cookies WHERE user_id = '$cookie_to_test1' AND cookie = '$cookie_to_test2'";
	$result = mysqli_query($connection, $query)
	       or die (mysqli_error($connection));

	setcookie("usercookie", "1", time()-3600, "/");
	setcookie("username", "1", time()-3600, "/");
	*/

	// Relocate back to where you came from
	header("Location: ../edit_myinfo.php?d=3");
	die();
	}
elseif ($ac=="editpasswordadmin") {
	$curpassword=filter_var($_POST["curpassword"], FILTER_SANITIZE_STRING);
	$newpassword1=filter_var($_POST["newpassword1"], FILTER_SANITIZE_STRING);
	$newpassword2=filter_var($_POST["newpassword2"], FILTER_SANITIZE_STRING);

	if ($newpassword1!=$newpassword2) {
		header("Location: edit_user_password.php?UserID=$UserID&e=2");
		die();
		}

	$enc_password = md5($curpassword);

	$query = "SELECT * FROM Users WHERE UserName = '$username' AND UserPassword = '$enc_password' AND UserActive='1'";
	// Execute the query
	$result = mysqli_query($connection, $query)
	or die ("Could not execute query. Please try again later.");
	// exactly one row? then we have found the user
	if (mysqli_num_rows($result) != 1) {
		header("Location: edit_user_password.php?UserID=$UserID&e=1");
		die();
		}

	$newpassword1_enc = md5($newpassword1);

	$query_file = "UPDATE Users SET UserPassword='$newpassword1_enc' WHERE UserID='$UserID' LIMIT 1";
	$result_file = mysqli_query($connection, $query_file)
		or die (mysqli_error($connection));

	$query = "DELETE FROM Cookies WHERE user_id = '$UserID'";
	$result = mysqli_query($connection, $query)
	       or die (mysqli_error($connection));

	// Relocate back to where you came from
	header("Location: edit_user_password.php?e=3");
	die();
	}
elseif ($ac=="resetpassword") {
	die("This option is not used anymore. Please go back.");
	}

?>
