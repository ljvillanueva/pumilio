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

require("include/check_login.php");

$d=filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);
$t=filter_var($_GET["t"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>

<title>$app_custom_name - Edit my information</title>";

require("include/get_css.php");
?>

<?php
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
		$(\"#tabs0\").tabs({ selected: 2 });
		});";
	}
elseif ($t==1) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 1 });
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
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
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
			if ($d==1) {
				echo "<p><div class=\"success\">Changes were applied successfully</div>";
				}
			if ($d==3) {
				echo "<p><div class=\"success\">Changes were applied successfully. Please log in to apply the changes.</div>";
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

			?>
				<div id="tabs0">
					<ul>
						<li><a href="#tabs-1">Theme for application</a></li>
						<li><a href="#tabs-2">Edit my email address</a></li>
						<li><a href="#tabs-3">Change my password</a></li>
					</ul>
				<div id="tabs-1">

				<?php
				echo "<h3>User settings</h3>";
			
				echo "<form action=\"set_cookies.php\" method=\"post\">
					<input type=\"hidden\" name=\"cookie_to_set\" value=\"jquerycss\">
					Select a theme for the application:
					<select name=\"css\" class=\"ui-state-default ui-corner-all\">\n";

				if ($jquerycss=="" || $jquerycss=="cupertino") {
					$cupertino_s = "SELECTED";
					}
				elseif ($jquerycss=="blitzer") {
					$blitzer_s = "SELECTED";
					}
				elseif ($jquerycss=="start") {
					$start_s = "SELECTED";
					}
				elseif ($jquerycss=="humanity") {
					$humanity_s = "SELECTED";
					}
				elseif ($jquerycss=="lightness") {
					$lightness_s = "SELECTED";
					}
				elseif ($jquerycss=="overcast") {
					$overcast_s = "SELECTED";
					}
				elseif ($jquerycss=="peppergrinder") {
					$peppergrinder_s = "SELECTED";
					}
				elseif ($jquerycss=="smoothness") {
					$smoothness_s = "SELECTED";
					}
				elseif ($jquerycss=="sunny") {
					$sunny_s = "SELECTED";
					}
				elseif ($jquerycss=="hotsneaks") {
					$hotsneaks_s = "SELECTED";
					}
				elseif ($jquerycss=="excitebike") {
					$excitebike_s = "SELECTED";
					}
				elseif ($jquerycss=="southstreet") {
					$southstreet_s = "SELECTED";
					}
				elseif ($jquerycss=="blacktie") {
					$blacktie_s = "SELECTED";
					}

				echo "	<option value=\"blacktie\" $blacktie_s>Black Tie</option>
					<option value=\"blitzer\" $blitzer_s>Blitzer</option>
					<option value=\"cupertino\" $cupertino_s>Cupertino (default)</option>
					<option value=\"excitebike\" $excitebike_s>Excite Bike</option>
					<option value=\"hotsneaks\" $hotsneaks_s>Hot Sneaks</option>
					<option value=\"humanity\" $humanity_s>Humanity</option>
					<option value=\"lightness\" $lightness_s>Lightness</option>
					<option value=\"overcast\" $overcast_s>Overcast</option>
					<option value=\"peppergrinder\" $peppergrinder_s>Pepper-Grinder</option>
					<option value=\"smoothness\" $smoothness_s>Smoothness</option>
					<option value=\"southstreet\" $southstreet_s>South Street</option>
					<option value=\"start\" $start_s>Start</option>
					<option value=\"sunny\" $sunny_s>Sunny</option>\n";


			echo "</select> 
				<br><br>
				<input type=submit value=\" Change theme \" class=\"fg-button ui-state-default ui-corner-all\"></form>";

			echo "<br><br>";

			?>

				</div>
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
					if ($d==4) {
						echo "<p><div class=\"error\">The new passwords do not match, please try again.</div>";
						}

					if ($d==2) {
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
