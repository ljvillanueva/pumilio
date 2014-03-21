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

$force_loggedin = TRUE;
require("include/check_login.php");

if (isset($_GET["d"])){
	$d=filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$d=0;
	}
	
if (isset($_GET["t"])){
	$t=filter_var($_GET["t"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$t=0;
	}
	
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Edit my information</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->
	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#EditUserForm").validate({
			rules: {
				UserEmail: {
					required: true,
					email: true
				}
			},
			messages: {
				UserEmail: "Please enter a valid email address"
			}
			});
		});
	</script>


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

<!-- JQuery Tabs -->
<script type="text/javascript">

<?php
if ($t==2) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 1 });
		});";
	}
elseif ($t==1) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 0 });
		});";
	}
else {
	echo "$(function() {
		$(\"#tabs0\").tabs();
		});";
	}
	
?>

	</script>
	
<?php
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			<h3>Edit my information</h3>
			<p>Use this page to edit your email address or password.

			<?php
			if ($login_wordpress == TRUE){
				$path_parts = pathinfo($wordpress_require);
				$path_dir = $path_parts['dirname'];
				echo "<div class=\"notice\">Your account is managed by Wordpress, change your information <a href=\"$path_dir/wp-admin/profile.php\">there</a>.</div>
				<br>
				</div>
					<div class=\"span-24 last\">";
					require("include/bottom.php");
				echo "</div>
					</div>
				</body>
				</html>";
				die();
				}
			else{
				if ($d == 1) {
					echo "<p><div class=\"success\">Changes were applied successfully</div>";
					}
				if ($d == 3) {
					echo "<p><div class=\"success\">Your password was changed.</div>";
					echo "<br>
					</div>
					<div class=\"span-24 last\">";
	
					require("include/bottom.php");
	
					echo "</div>
					</div>
					</body></html>";
					die();
					}

				$result = mysqli_query($connection, "SELECT * FROM Users WHERE UserName='$username' LIMIT 1")
					or die (mysqli_error($connection));
				$row = mysqli_fetch_array($result);
				if (mysqli_num_rows($result) == 1) {
					extract($row);
					}
				else {
					die("<div class=\"error\">Your account could not be found or you are not logged in.</div>");
					}
				}
			?>
				<div id="tabs0">
					<ul>
						<li><a href="#tabs-2">Edit my email address</a></li>
						<li><a href="#tabs-3">Change my password</a></li>
					</ul>

				<div id="tabs-2">
					<h3>Edit your email address</h3>
					<form action="include/edit_user.php" method="POST" id="EditUserForm">
					<input type="hidden" name="ac" value="selfedit">
					Please enter your email address:<br>
					<?php
						echo "<input type=\"text\" name=\"UserEmail\" maxlength=\"100\" size=\"60\" class=\"fg-button ui-state-default ui-corner-all formedge\" value=\"$UserEmail\"><br>";
					?>

					<input type=submit value=" Edit email address " class="fg-button ui-state-default ui-corner-all">
					</form>
				</div>
				<div id="tabs-3">
					<h3>Change your password</h3>
					<?php
					if ($d == 4) {
						echo "<p><div class=\"error\">The new passwords do not match, please try again.</div>";
						}

					if ($d == 2) {
						echo "<p><div class=\"error\">The current password does not match, please try again.</div>";
						}
					?>

					<form action="include/edit_user.php" method="POST" id="EditPassForm">
					<input type="hidden" name="ac" value="editpassword">
					Please enter your current password:<br>
					<input type="password" name="curpassword" id="curpassword" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
					Please enter a new password:<br>
					<input type="password" name="newpassword1" id="newpassword1" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
					Please retype the new password:<br>
					<input type="password" name="newpassword2" id="newpassword2" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
					<input type=submit value=" Edit my password " class="fg-button ui-state-default ui-corner-all">
					</form>

				</div>

		<br>
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>
		</div>
	</div>

</body>
</html>