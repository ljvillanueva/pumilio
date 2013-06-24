<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Change user password</title>";

#Get CSS
	require("get_css_include.php");
	require("get_jqueryui_include.php");
?>

<script src="../js/jquery.validate.js"></script>

<script type="text/javascript">
$().ready(function() {
	// validate signup form on keyup and submit
	$("#EditPassForm").validate({
		rules: {
			curpassword: {
				required: true
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
			curpassword: "Please enter your current password",
			newpassword1: {required: "Please enter a new password of at least 5 characters",
					minlength: "Your password must be at least 5 characters long"},
			newpassword2: {required: "Please enter a new password of at least 5 characters",
					minlength: "Your password must be at least 5 characters long",
					equalTo: "Please enter the same password as above"}
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

<div style="padding: 10px;">

<?php

$e = $_GET["e"];

if ($e==1) {
	echo "<p><div class=\"error\">Your password was incorrect, please try again.</div>";
	}
elseif ($e==2) {
	echo "<p><div class=\"error\">The new passwords do not match, please try again.</div>";
	}
elseif ($e==3) {
	echo "<p><div class=\"success\">The user has a new password.</div>
		<br><p><a href=\"#\" onClick=\"window.close();\">Close window</a>
		</div>
		</body>
		</html>";
	die();
	}
	

?>
<h3>Change a user password</h3>
<p><form action="edit_user.php" method="POST" id="EditPassForm">
	<input type="hidden" name="ac" value="editpasswordadmin">
	Please enter your current password:<br>
	<input type="password" name="curpassword" id="curpassword" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
	Please enter a new password for the user 
	<?php
	
		$UserID=filter_var($_GET["UserID"], FILTER_SANITIZE_NUMBER_INT);
		$username=query_one("SELECT UserName FROM Users WHERE UserID='$UserID'", $connection);
		echo "$username 
		<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">";
	?>
	:<br>
	<input type="password" name="newpassword1" id="newpassword1" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
	Please retype the new password:<br>
	<input type="password" name="newpassword2" id="newpassword2" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
	<input type=submit value=" Edit user password " class="fg-button ui-state-default ui-corner-all">
</form>

<br><p><a href="#" onClick="window.close();">Close window</a>

</div>

</body>
</html>
