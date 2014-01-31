<?php
session_start();
require("../config.php");
require("functions.php");
require("apply_config_include.php");

$cookie_to_test = $_COOKIE["usercookie"];
$cookie_to_testa = explode(".", $cookie_to_test);
$cookie_to_test1 = $cookie_to_testa['0'];
$cookie_to_test2 = $cookie_to_testa['1'];

#$cookie_to_test1=filter_var($_GET["cookie_to_test1"], FILTER_SANITIZE_NUMBER_INT);
#$cookie_to_test2=filter_var($_GET["cookie_to_test2"], FILTER_SANITIZE_STRING);

$query = "DELETE FROM Cookies WHERE user_id = '$cookie_to_test1' AND cookie = '$cookie_to_test2'";
$result = mysqli_query($connection, $query)
       or die (mysqli_error($connection));

setcookie("usercookie", "1", time()-3600, $app_dir);
setcookie("username", "1", time()-3600, $app_dir);

$where_to=filter_var($_GET["where_to"], FILTER_SANITIZE_URL);
$q=filter_var($_GET["q"], FILTER_SANITIZE_URL);

$q = str_replace("%", "&", $q);

$pos1 = strpos($where_to, "advancedsearch.php");
$pos2 = strpos($where_to, "admin.php");

if($pos1 != false) {
	header("Location: ../");
	die();
	}

if($pos2 != false) {
	header("Location: ../");
	die();
	}

header("Location: $where_to?$q");
die();

?>
