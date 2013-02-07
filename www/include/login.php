<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

function authenticateUser($connection, $username, $password) {
	// Test the username and password parameters
	if (!isset($username) || !isset($password))
		return false;

	#Use plain text passwords
	$enc_password = md5($password);

	// Formulate the SQL find the user
	$query = "SELECT * FROM Users WHERE UserName = '$username' AND UserPassword = '$enc_password' AND UserActive='1'";

	// Execute the query
	$result = mysqli_query($connection, $query)
		or die ("Could not execute query. Please try again later.");

	// exactly one row? then we have found the user
	if (mysqli_num_rows($result) != 1)
		return false;
	else
		return true;
	}
	
function WordpressAuthenticateUser() {
	// Test the username and password parameters
	if (!file_exists($wordpress_require)) {
		return false;
		}
	else {
		require_once('../wp-blog-header.php');

		if (is_user_logged_in()==TRUE){
			return true;
			}
		else{
			return false;
			}
		}
	}

$username=filter_var($_POST["username"], FILTER_SANITIZE_STRING);
$password=filter_var($_POST["password"], FILTER_SANITIZE_STRING);
$where_to=filter_var($_POST["where_to"], FILTER_SANITIZE_URL);

$username = strtolower($username);

// Authenticate the user
if ($login_wordpress == TRUE){
	if (WordpressAuthenticateUser()	) {
		wp_get_current_user();

		$user_id = $current_user->ID;
		$username = $current_user->user_login;

		$rand_cookie = rand(1, 99999);
		$enc_cookie = md5($rand_cookie);
		$usercookie = $user_id . "." . $enc_cookie;
		setcookie("usercookie", $usercookie, time()+(3600*24*30), $app_dir);
		setcookie("username", $username, time()+(3600*24*30), $app_dir);

		// Relocate back to where you came from
		header("Location: $where_to");
		die();
		}
	}
else {
	if (authenticateUser($connection, $username, $password)) {
		// Record each time the user logs in
		$IP = $_SERVER["REMOTE_ADDR"];
		$query = ("SELECT UserID as user_id FROM Users WHERE UserName = '$username'");
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$row = mysqli_fetch_array($result,MYSQL_ASSOC);
		extract($row);

		//Set cookie and session
		$rand_cookie = rand(1, 99999);
		$enc_cookie = md5($rand_cookie);
		$usercookie = $user_id . "." . $enc_cookie;
		setcookie("usercookie", $usercookie, time()+(3600*24*30), $app_dir);
		setcookie("username", $username, time()+(3600*24*30), $app_dir);

		//Insert cookie to database
		$remote_host = $_SERVER['REMOTE_ADDR'];
		$query = "INSERT INTO Cookies (`user_id` ,`cookie`, `hostname`, `TimeStamp`) VALUES 
				('$user_id', '$enc_cookie', '$remote_host', NOW())";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));

		// Relocate back to where you came from
		if (stristr($where_to, 'edit_myinfo.php?d=3')) {
			header("Location: ../");
			}
		else {
			header("Location: $where_to");
			}
		die();
		}
	else {
		// The authentication failed
		$where_to = str_replace("&e=1","",$where_to);
		$pos1 = stripos($where_to, "?");
		if ($pos1 === false) {
			header("Location: $where_to?&e=1");
			die;
			}
		else {
			header("Location: $where_to&e=1");
			die;
			}
		}
	}

?>
