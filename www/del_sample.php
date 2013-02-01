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

#Check if user can edit files (i.e. has admin privileges)
	if (!sessionAuthenticate($connection)) {
		die();
		}

	$username = $_COOKIE["username"];

	if (!is_user_admin($username, $connection)) {
		die();
		}

$SampleID=filter_var($_POST["SampleID"], FILTER_SANITIZE_NUMBER_INT);

echo "	<html>
	<head>
<title>$app_custom_name - Delete sample</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
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

		<?php
			$query = "DELETE FROM SampleMembers WHERE SampleID='$SampleID'";
			$result=query_several($query, $connection);	
			$query = "DELETE FROM Samples WHERE SampleID='$SampleID' LIMIT 1";
			$result=query_several($query, $connection);	

			echo "<p><div class=\"success\">The sample was deleted.</div>
			<p><a href=\"./\">Return to the homepage</a>";
				
		?>

		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>

	</div>


</body>
</html>
