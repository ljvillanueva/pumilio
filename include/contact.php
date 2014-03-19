<?php
session_start();

require("functions.php");

$config_file = '../config.php';

if (file_exists($config_file)) {
	require("../config.php");
} else {
	header("Location: ../error.php?e=config");
	die();
	}

require("apply_config_include.php");

?>

<html>
<head>
<title>Pumilio - Contact</title>

<!-- JQuery -->
<link type="text/css" href="../css/jqueryui/jquery-ui-1.10.4.custom.min.css" rel="stylesheet">
	
<link type="text/css" href="../js/jquery/jquery.custom.css" rel="stylesheet">
<link type="text/css" href="../js/jquery/jquery.css.custom.css" rel="stylesheet">

<!-- Blueprint -->
<link rel="stylesheet" href="../css/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print">	
<!--[if IE]><link rel="stylesheet" href="../css/ie.css" type="text/css" media="screen, projection"><![endif]-->
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Ubuntu">

<!-- Scripts for JQuery -->
	<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
	<script type="text/javascript" src="../js/jquery-ui-1.10.4.custom.min.js"></script>
	<script type="text/javascript" src="../js/jquery.fg-button.js"></script>

<link rel="stylesheet" href="../css/custom.css" type="text/css" media="screen, projection">


</head>
<body>

<div style="padding: 10px;">

<h4>Contact the administrators of this site:</h4>

	<?php
	
		$query = "SELECT * from Users WHERE UserRole='admin' AND UserActive='1' ORDER BY UserName";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);

		echo "<ul>\n";

		for ($i = 0; $i < $nrows; $i++) {
			$row = mysqli_fetch_array($result);
			extract($row);

			echo "<li>$UserFullname: <a href=\"mailto:$UserEmail\">$UserEmail</a></li>\n";
			}

		echo "</ul>\n";
	?>

<p><a href="#" onClick="window.close();">Close window</a>

</div>
</body>
</html>
