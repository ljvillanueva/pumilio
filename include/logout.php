<?php
/*session_start();
require("../config.php");
require("functions.php");
require("apply_config_include.php");*/

$cookie_to_test = $_COOKIE["usercookie"];
$cookie_to_testa = explode(".", $cookie_to_test);
$cookie_to_test1 = $cookie_to_testa['0'];
$cookie_to_test2 = $cookie_to_testa['1'];


$query = "DELETE FROM Cookies WHERE user_id = '$cookie_to_test1' AND cookie = '$cookie_to_test2'";
$result = mysqli_query($connection, $query)
       or die (mysqli_error($connection));

setcookie("usercookie", "1", time()-3600, $app_dir);
setcookie("username", "1", time()-3600, $app_dir);

header("Location: ./");
die();
?>
