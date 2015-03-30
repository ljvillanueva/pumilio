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

$sumitted_email=filter_var($_POST["sumitted_email"], FILTER_SANITIZE_EMAIL);

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

			$query = ("SELECT UserName, UserEmail,UserPassword FROM Users WHERE UserEmail='$sumitted_email'");
			$result = mysqli_query($connection, $query)
				or die ("Could not execute query. Please try again later.");

			$nrows = mysqli_num_rows($result);

			if ($nrows==1) {
				$row = mysqli_fetch_array($result);
				extract($row);

				if ($app_custom_name=="")
					$app_custom_name="Pumilio";

				$to = "<$UserEmail>";
				$subject = "Password recovery";
				$headers = "From: <$app_admin_email>";
				$mess = "\nPassword reminder for $app_custom_name:\n\n Your username is: $UserName\n Your password is: $UserPassword\n";

				if (!mail($to, $subject, $mess, $headers)){
					echo "This system failed to send the email. Please consult with the administrator to reset/recover your password. The administrator email is: $app_admin_email";
					}
				else {
					echo "A reminder email has been sent. Check your email in a couple of minutes for your username and password. If you can not find the reminder in your inbox, check your spam or junk folder or contact your administrator.";
					}
				}
			else {
				echo "That email does not exists in the system. Please check for errors and try again.";
				}

		?>

		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
</body>
</html>
