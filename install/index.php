<?php

require("../include/functions.php");

$config_file = '../config.php';

if (file_exists($config_file)) {
    require("../config.php");
} else {
    header("Location: ../error.php?e=config");
    die();
}

require("apply_config.php");

if(isset($_GET["e"])){
	$e=filter_var($_GET["e"], FILTER_SANITIZE_NUMBER_INT);
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>Pumilio - Installation</title>";
?>
<!-- Blueprint css -->
	<link rel="stylesheet" href="../css/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="../css/print.css" type="text/css" media="print">	
	<!--[if IE]><link rel="stylesheet" href="../css/ie.css" type="text/css" media="screen, projection"><![endif]-->


<!-- Scripts for JQuery -->
	<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../js/jquery.fg-button.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.10.4.custom.min.js"></script>
	<link type="text/css" href="../css/jqueryui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet" />
	<script src="../js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddUserForm").validate({
			rules: {
				UserName: {
					required: true
				},
				UserFullname: {
					required: true
				},
				UserEmail: {
					required: true,
					email: true
				},
				newpassword1: {
					required: true,
					minlength: 5
				},
				newpassword2: {
					required: true,
					minlength: 5,
					equalTo: "#newpassword1"
				}
			},
			messages: {
				UserName: "Please enter a username for this user",
				UserFullname: "Please enter the full name of this user",
				UserEmail: "Please enter a valid email of this user"
			}
			});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>

</head>
<body>

<!--Blueprint container-->
<div class="container">
	<div class="span-24 last">
		<br>
		<h3>New Installation</h3>

		<?php
		
		if ($e == 1){
			echo "<div class=\"error\">The passwords didn't match. Please try again.</div>";
			}
			
		if ($e == 0){
			$no_tables=query_one("SELECT COUNT(DISTINCT `table_name`) FROM `information_schema`.`columns` WHERE `table_schema` = '$database'", $connection);

			if ($no_tables!=0) {
				$no_users = query_one("SELECT COUNT(*) FROM Users", $connection);
				if ($no_users!=0) {
					die("<div class=\"success\">Installation was successful, go the <a href=\"../\">the homepage</a> and log in. For added security, delete the <em>install</em> and <em>upgrade</em> folder.</div>");
					}
				}
			}
		?>

		<p><form method="GET" action="../include/soft_check.php" target="softcheck" onsubmit="window.open('', 'softcheck', 'width=600,height=700,status=yes,resizable=yes,scrollbars=yes')">
			<input type=submit value=" Software and permissions check " class="fg-button ui-state-default ui-corner-all">
		</form>

		<p>Use this page to set up an administrative user. You can add more users from the administration
			menu, once that user is logged in.

		<form action="add_admin_user.php" method="POST" id="AddUserForm">
			<p>UserName: <input type="text" name="UserName" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all"><br>
			Full name of the user: <input type="text" name="UserFullname" maxlength="100" size="60" class="fg-button ui-state-default ui-corner-all"><br>
			User email address: <input type="text" name="UserEmail" maxlength="100" size="60" class="fg-button ui-state-default ui-corner-all"><br>
			User password: <input type="password" name="newpassword1" id="newpassword1" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all"><br>
			Please retype the password:<input type="password" name="newpassword2" id="newpassword2" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all"><br>
			<input type=submit value=" Add user " class="fg-button ui-state-default ui-corner-all">
			<div class="notice">Make sure that the database listed in the configuration file,  
			<?php
				echo "<b>$database</b>, ";
			?>
				is not being used, its content will be deleted!</div>
		</form>
		</div>

	</div>

</body>
</html>
