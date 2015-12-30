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


#DB
use \DByte\DB;
DB::$c = $pdo;


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
	
echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name - Edit my information</title>";

require("include/get_css3.php");
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
		width: 500px;
		display: inline;
	}
	</style>

	
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

		<h2>Edit my information</h2>
			<p>Use this page to edit your email address or password.


		

			<?php
			if ($login_wordpress == TRUE){
				$path_parts = pathinfo($wordpress_require);
				$path_dir = $path_parts['dirname'];
				echo "<div class=\"notice\">Your account is managed by Wordpress, change your information <a href=\"$path_dir/wp-admin/profile.php\">there</a>.</div>
				<br>";
					require("include/bottom.php");
				echo "
					</div>
				</body>
				</html>";
				die();
				}
			else{
				if ($d == 1) {
					echo "<p><div class=\"alert alert-success\">Changes were applied successfully</div>";
					}
				if ($d == 3) {
					echo "<p><div class=\"alert alert-success\">Your password was changed.</div>";
					echo "<br>";
	
					require("include/bottom.php");
	
					echo "
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
					die("<div class=\"alert alert-danger\">Your account could not be found or you are not logged in.</div>");
					}
				}
			?>
			
		
<div class="row">
	<div class="col-lg-8">
	
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Edit your email address</h3>
		</div>
		<div class="panel-body">
		 	
				<form action="include/edit_user.php" method="POST" id="EditUserForm">
					<input type="hidden" name="ac" value="selfedit">
					<p>Please enter your email address: 

					<?php
						echo "<input type=\"text\" name=\"UserEmail\" maxlength=\"60\" class=\"form-control\" value=\"$UserEmail\"><br>";
					?>

					<button type="submit" class="btn btn-primary"> Edit email address </button>
				</form>
		</div>
	</div>

	</div>
	<div class="col-lg-4">&nbsp;</div>
</div>
		



<div class="row">
	<div class="col-lg-4">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Change your password</h3>
		</div>
		<div class="panel-body">
		 	
				<?php
				if ($d == 4) {
					echo "
					<div class=\"alert alert-warning\" role=\"alert\">The new passwords do not match, please try again.</div>";
					}

				if ($d == 2) {
					echo "<div class=\"alert alert-warning\" role=\"alert\">The current password does not match, please try again.</div>";
					}
			?>

			<form action="include/edit_user.php" method="POST" id="EditPassForm">

				<input type="hidden" name="ac" value="editpassword" class="form-control">
				<div class="form-group">
					<p>Please enter your current password:</p>
					<input type="password" name="curpassword" id="curpassword" maxlength="20" class="form-control">
				</div>
				
				<div class="form-group">
					<p>Please enter a new password:</p>
					<input type="password" name="newpassword1" id="newpassword1" maxlength="20" class="form-control">
				</div>

				<div class="form-group">
					<p>Please retype the new password:</p>
					<input type="password" name="newpassword2" id="newpassword2" maxlength="20" class="form-control">
				</div>

				<button type="submit" class="btn btn-primary"> Edit my password </button>
			</form>
		</div>
	</div>
	</div>
	<div class="col-lg-8">&nbsp;</div>
</div>




<?php
require("include/bottom.php");
?>

</body>
</html>