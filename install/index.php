<?php
#New install

if (isset($_POST['server'])){
	#Form submitted, so do checks and install

	$server=filter_var($_POST["server"], FILTER_SANITIZE_STRING);
	$dbuser=filter_var($_POST["dbuser"], FILTER_SANITIZE_STRING);
	$dbpass=filter_var($_POST["dbpass"], FILTER_SANITIZE_STRING);
	$db=filter_var($_POST["db"], FILTER_SANITIZE_STRING);

	$UserName=filter_var($_POST["UserName"], FILTER_SANITIZE_STRING);
	$UserFullname=filter_var($_POST["UserFullname"], FILTER_SANITIZE_STRING);
	$UserEmail=filter_var($_POST["UserEmail"], FILTER_SANITIZE_STRING);
	$newpassword1=filter_var($_POST["newpassword1"], FILTER_SANITIZE_STRING);
	$newpassword2=filter_var($_POST["newpassword2"], FILTER_SANITIZE_STRING);


	#Try to connect to the db
	$connection = @mysqli_connect($server, $dbuser, $dbpass, $db);

	#If could not connect, redirect
	if (!$connection) {
		$e = "Could not connect to the database. Please check your settings and try again.";
		}
	else{
		#connected, create tables and insert user
		//Empty database
		$result_check = mysqli_query($connection, "DROP TABLE IF EXISTS Cookies, Equipment, PumilioSettings, Sites, Sounds, SoundsImages, SoundsMarks, Sources, Tags, Users, WeatherData, WeatherSites, Samples, SampleMembers, ProcessLog, Queue, QueueJobs, Scripts, SitesPhotos, Kml, Tokens, PumilioLog, QualityFlags, CheckAuxfiles, FilesToAdd, FilesToAddMembers, SoundsStatsResults, SoundsStats")
			or die (mysqli_error($connection));

		$all_query = file_get_contents("pumilio.sql");
		mysqli_multi_query($connection, $all_query);
		mysqli_close($connection);
		sleep(5);

		#encrypt user password
		$enc_password = md5($newpassword1);
		$query = ("INSERT INTO Users 
					(UserName,UserFullname,UserEmail,UserRole,UserPassword) 
					VALUES ('$UserName', '$UserFullname', '$UserEmail', 'admin', '$enc_password')");
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		
		#Write config file
			$configFile = '../config.php';
			$fh = fopen($configFile, 'w') or die("Can't write the configuration file $configFile. Please check that the permissions of the directory.");

			fwrite($fh, "<?php" . PHP_EOL);
			fwrite($fh, "\$host = \"" . $server . "\";" . PHP_EOL);
			fwrite($fh, "\$user = \"" . $dbuser . "\";" . PHP_EOL);
			fwrite($fh, "\$password = \"" . $dbpass . "\";" . PHP_EOL);
			fwrite($fh, "\$database = \"" . $db . "\";" . PHP_EOL);
			fwrite($fh, "?>");
			fclose($fh);

		die();
		}

}
else{
	$server = "localhost";
	$port = 3306;
	}

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>

<title>Pumilio - Installation</title>";
?>



<!-- Bootstrap core CSS -->
<link href="../libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap theme -->
<link href="../libs/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

<link href="../libs/bootstrap/css/sticky-footer-navbar.css" rel="stylesheet">

<!-- JQuery -->
<link type="text/css" href="../libs/jquery-ui/jquery-ui.min.css" rel="stylesheet">

<!-- font-awesome -->
<link rel="stylesheet" href="../libs/font-awesome/css/font-awesome.min.css">


<!-- Scripts for JQuery -->
	<script type="text/javascript" src="../libs/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="../libs/jquery-ui/jquery-ui.min.js"></script>

	<link rel="stylesheet" href="../css/custom.css" type="text/css" media="screen, projection">

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
				},
				server: {
					required: true
				},
				dbuser: {
					required: true
				},
				dbpass: {
					required: true
				},
				db: {
					required: true
				}
			}});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
		color: red;
	}
	</style>

</head>
<body>

<!--Blueprint container-->
<div class="container">
	<div class="row">
		<div class="col-lg-2">&nbsp;</div>
		<div class="col-lg-8">
		<br>
		<h2>New Installation of Pumilio</h2>

		<?php
		
		if (isset($e)){
			echo "<div class=\"alert alert-danger\">$e</div>";
			}


		if (isset($_GET['success'])){
			$success=filter_var($_GET["success"], FILTER_SANITIZE_NUMBER_INT);
			if ($success == 1){
				echo "<div class=\"alert alert-success\">The database for Pumilio has been installed. Go to the <a href=\"../\">main page of the application</a>. Please delete the folder 'install'.</div></body></html>";
				die();
				}
			}

		if (file_exists('../config.php')) {
			echo "<div class=\"alert alert-danger\">The config file exists already. To re-install Pumilio, please delete the file config.php</div></body></html>";
			die();
			}


		?>

		
		<!-- Software check -->


			<h3>Software Check</h3>

			<?php

			// Test if the required programs are installed.
			// SoX
				unset($out, $retval);
				exec('sox --version', $soxout, $soxretval);
				$soxout=explode("v",$soxout[0]);
				$soxout=$soxout[1];
				if ($soxretval!=0) {
					echo "<div class=\"alert alert-danger\"><strong>SoX is not installed</strong>. 
						Please install by using this command: sudo apt-get install sox libsox*</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>Sox is installed</strong>.</div>";
					}

			// Test if the required programs are installed.
			// SoX
				unset($out, $retval);
				exec('soxi', $out, $retval);
				if ($retval!=0) {
					if ($soxretval==0) {
						echo "<div class=\"alert alert-danger\"><strong>The soxi utility from SoX is not installed</strong>. The version of SoX installed is $soxout, you need at least version 14.3.0. Download the latest version from http://sox.sourceforge.net/</div>";
						}
					else {
						echo "<div class=\"alert alert-danger\"><strong>The soxi utility from SoX is not installed</strong>. Download the latest version of SoX from http://sox.sourceforge.net/</div>";
						}
					}



			if ($AudioPreviewFormat=="ogg"){
				// ogg encoder
				unset($out, $retval);
				exec('dir2ogg --version', $out, $retval);
				if ($retval!=0) {
					echo "<div class=\"alert alert-danger\"><strong>The OGG encoder is not installed</strong>. If ogg encoding is desired, please install by using this command: sudo apt-get install dir2ogg</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>The OGG encoder is installed</strong>.</div>";
					}
				}
			elseif ($player_format=="mp3"){
				// LAME
				unset($out, $retval);
				exec('lame --version', $out, $retval);
				if ($retval!=0) {
					echo "<div class=\"alert alert-danger\"><strong>The LAME MP3 encoder is not installed</strong>. If mp3 encoding is desired, please install by using this command: sudo apt-get install lame liblame*</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>LAME MP3 encoder is installed</strong>.</div>";
					}
				}




			// FLAC
				unset($out, $retval);
				exec('flac --version', $out, $retval);
				if ($retval!=0) {
					echo "<div class=\"alert alert-danger\"><strong>FLAC is not installed</strong>. If flac support is desired, please install by using this command: sudo apt-get install flac</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>FLAC is installed</strong>.</div>";
					}


			// Imagemagick
				unset($out, $retval);
				exec('convert --version', $out, $retval);
				if ($retval>1) {
					echo "<div class=\"alert alert-danger\"><strong>Imagemagick is not installed</strong>. Please install by using this command: sudo apt-get install imagemagick</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>Imagemagick is installed</strong>.</div>";
					}



			// audiolab
				unset($out, $retval);
				exec($absolute_dir . '../include/check_audiolab.py', $out, $retval);
				if ($retval!=0) {
					echo "<div class=\"alert alert-danger\"><strong>The Python module 'audiolab' is not installed</strong>. Please visit <a href=\"http://www.scipy.org/scipy/scikits/wiki/AudioLab\" target=_blank>http://www.scipy.org/scipy/scikits/wiki/AudioLab</a></div>";
					$audiolab=1;
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>The audiolab Python module is installed</strong>.</div>";
					}



			// svt.py script
				unset($out, $retval);
				exec($absolute_dir . '../include/svt.py -v', $out, $retval);
				if ($retval!=0) {
					echo "<div class=\"alert alert-danger\"><strong>The svt script is not installed or can not run</strong>.";
					if ($audiolab==1)
						echo " Please install audiolab first.";
					echo "</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>The svt script is installed</strong>.</div>";
					}


			//Check for tmp folder
				unset($out, $retval);
				#$tmpperms=substr(decoct(fileperms("$absolute_dir/tmp/")),2);
				#if ($tmpperms!=777)
				if (!is_dir("../tmp/") || !is_writable("../tmp/")) {
					echo "<div class=\"alert alert-danger\"><strong>The server can not write to the temporary folder, tmp/, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>Temporary directory permissions are correct</strong>.</div>";
					}

			//Check for sounds folder
				unset($out, $retval);
				#$tmpperms=substr(decoct(fileperms("$absolute_dir/sounds/")),2);
				#if ($tmpperms!=777)
				if (!is_dir("../sounds/") || !is_writable("../sounds/")) {
					echo "<div class=\"alert alert-danger\"><strong>The server can not write to the archive folder, sounds/, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
					}
				elseif (!is_dir("../sounds/images") || !is_writable("../sounds/images")) {
					echo "<div class=\"alert alert-danger\"><strong>The server can not write to the archive folder, sounds/images, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
					}
				elseif (!is_dir("../sounds/previewsounds") || !is_writable("../sounds/previewsounds")) {
					echo "<div class=\"alert alert-danger\"><strong>The server can not write to the archive folder, sounds/previewsounds, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
					}
				elseif (!is_dir("../sounds/sounds") || !is_writable("../sounds/sounds")) {
					echo "<div class=\"alert alert-danger\"><strong>The server can not write to the archive folder, sounds/sounds, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>Sound archive directory permissions are correct</strong>.</div>";
					}

			//Check for sitepictures folder
				/*unset($out, $retval);
				#$tmpperms=substr(decoct(fileperms("$absolute_dir/sitephotos/")),2);
				#if ($tmpperms!=777)
				if (!is_dir("../sitephotos/") || !is_writable("../sitephotos/")) {
					echo "<div class=\"alert alert-danger\"><strong>The server can not write to the site photographs folder, sitephotos/, some features will not work.</strong> Please set the webserver as the owner of the directory or change the permissions to read and write.</div>";
					}
				else {
					echo "<div class=\"alert alert-success\"><strong>Site photograph directory permissions are correct</strong>.</div>";
					}*/

			?>






		<h3>Database settings</h3>

		<form action="index.php" method="POST" id="AddUserForm">

			<div class="form-group">
				<label for="server">MySQL Server (default is: localhost)</label>
				<input type="text" name="server" id="server" maxlength="20" class="form-control" value=<?php echo "\"$server\""; ?>>
			</div>

			<div class="form-group">
				<label for="db">MySQL Database</label>
				<input type="text" name="db" id="db" maxlength="20" class="form-control" value=<?php echo "\"$db\""; ?>>
			</div>

			<div class="form-group">
				<label for="dbuser">MySQL User</label>
				<input type="text" name="dbuser" id="dbuser" maxlength="20" class="form-control" value=<?php echo "\"$dbuser\""; ?>>
			</div>

			<div class="form-group">
				<label for="dbpass">MySQL Password</label>
				<input type="password" name="dbpass" id="dbpass" maxlength="20" class="form-control" value=<?php echo "\"$dbpass\""; ?>>
			</div>

			<h3>Create a user for Pumilio. This user will have administration rights.</h3>

			<div class="form-group">
				<label for="UserName">UserName</label>
				<input type="text" name="UserName" id="UserName" maxlength="20" class="form-control" value=<?php echo "\"$UserName\""; ?>>
			</div>

			<div class="form-group">
				<label for="UserFullname">Full name of the user</label>
				<input type="text" name="UserFullname" id="UserFullname" class="form-control" value=<?php echo "\"$UserFullname\""; ?>>
			</div>

			<div class="form-group">
				<label for="UserEmail">User email address</label>
				<input type="text" name="UserEmail" id="UserEmail" class="form-control" value=<?php echo "\"$UserEmail\""; ?>>
			</div>

			<div class="form-group">
				<label for="newpassword1">User password</label>
				<input type="password" name="newpassword1" id="newpassword1" maxlength="20" class="form-control">
			</div>

			<div class="form-group">
				<label for="newpassword2">Please retype the password</label>
				<input type="password" name="newpassword2" id="newpassword2" maxlength="20" class="form-control">
			</div>

			<button type="submit" class="btn btn-lg btn-primary btn-block"> Continue </button>

		</form>
		<div class="col-lg-2">&nbsp;</div>
	</div>
</div>

</body>
</html>
