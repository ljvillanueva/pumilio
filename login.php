<?php
session_start();

require("include/functions.php");
require("config.php");
require("include/apply_config.php");


if(isset($_GET['logout'])){
	$logout = filter_var($_GET["logout"], FILTER_SANITIZE_STRING);
	if ($logout == "TRUE"){
		require("include/logout.php");
		die();
	}
}


if (!isset($no_login)){
	$no_login = FALSE;
	}
if ($no_login == TRUE) {
	die();
	}


if(isset($_POST['submitted'])){
	$submitted = filter_var($_POST['submitted'], FILTER_SANITIZE_STRING);
}
else{
	$submitted = FALSE;
}



if ($submitted){

	$username = strtolower(filter_var($_POST["inputUsername"], FILTER_SANITIZE_STRING));
	$password = filter_var($_POST["inputPassword"], FILTER_SANITIZE_STRING);

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
			header("Location: ./");
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
			header("Location: ./");
			die();
			}
		else {
			// The authentication failed
			$where_to = str_replace("&e=1","",$where_to);
			$pos1 = stripos($where_to, "?");
			header("Location: login.php?e=1");
			die();
			}
		}

}
else{

	#require("include/apply_config.php");
	require("include/check_admin.php");

	#If user is not logged in, add check for QF
	if ($pumilio_loggedin == FALSE) {
		$qf_check = "AND `Sounds`.`QualityFlagID`>=$default_qf";
		}
	else {
		$qf_check = "";
		}

	echo "<!DOCTYPE html>
	<html lang=\"en\">
	<head>
	<title>$app_custom_name</title>";

	require("include/get_css3.php");
	require("include/get_jqueryui.php");


	if ($use_googleanalytics) {
		echo $googleanalytics_code;
		}


	#Execute custom code for head, if set
	if (is_file("$absolute_dir/customhead.php")) {
			include("customhead.php");
		}
		
	echo "<!-- Custom styles for this template -->
	    <link href=\"libs/bootstrap/css/signin.css\" rel=\"stylesheet\">";

	echo "</head>\n";

	echo "<body>";

	?>

		<!--Blueprint container-->
		<div class="container">
			<?php
				require("include/topbar.php");
			
			if(isset($_GET['e'])){
				echo "<div class=\"alert alert-danger\" role=\"alert\">Invalid username or password, try again or <a href=\"recover_password.php\">recover password</a></div>";
				}

			?>
			
	      <form class="form-signin" action="login.php" method="POST" class="form-inline">
	        <h2 class="form-signin-heading">Please sign in</h2>
	        <input type="hidden" name="submitted" value="TRUE">
	        <label for="inputUsername" class="sr-only">Username</label>
	        <input type="text" id="inputUsername" name="inputUsername" class="form-control" placeholder="Username" required autofocus>
	        <label for="inputPassword" class="sr-only">Password</label>
	        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
	        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
	      </form>

	   <br><br><br><br><br><br><br>
			
	<?php
	require("include/bottom.php");
	
	echo "</body>
	</html>";


	if ($use_leaflet == TRUE){
		require("include/leaflet2.php");
	}

	#Close session to release script from php session
		session_write_close();
		flush(); @ob_flush();
		#Delete old temp files
		delete_old('tmp/', 30);


}
?>