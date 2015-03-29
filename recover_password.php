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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Recover Password</title>";

require("include/get_css.php");


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
?>

</head>
<body>

	<!--Blueprint container-->
	<div class="container">
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">

		<H4>Recover password</H4>
		<?php
			if ($app_allow_email==FALSE){
				echo "This server is not configured to send emails. To recover your password, please request it from the administrator: $app_admin_email.
				</div>
				<div class=\"span-24 last\">
					&nbsp;
				</div>
				</body>
				</html>";
				die();
				}
		?>
		<form method="post" action="recover_password2.php">
			<p>Enter your email address:<br><br>
			<input name="sumitted_email" type="text" size="30" class="fg-button ui-state-default ui-corner-all"><br><br>
		        <input type="submit" value=" Submit " class="fg-button ui-state-default ui-corner-all">
		</form>

		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
</body>
</html>
