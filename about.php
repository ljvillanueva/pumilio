<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: $app_url/error.php?e=config");
    die();
}

require("include/apply_config.php");
#require("include/check_admin.php");


#DB
use \DByte\DB;
DB::$c = $pdo;


echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name - About</title>";

require("include/get_css3.php");
require("include/get_jqueryui.php");



if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}


#Execute custom code for head, if set
if (is_file("$absolute_dir/customhead.php")) {
		include("customhead.php");
	}
	
	
echo "</head>\n
	<body>";

?>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		


	echo "<h2>About $app_custom_name</h2>";


	echo "<h3>Contact the administrators of this site:</h3>";

	
		$rows = DB::fetch("SELECT * FROM `Users` WHERE `UserRole`='admin' AND `UserActive`='1' ORDER BY `UserName`", array(TRUE));

		echo "<ul>\n";

		foreach($rows as $row){
			echo "<li>" . $row->UserFullname . ": <a href=\"mailto:" . $row->UserEmail . "\">" . $row->UserEmail . "</a></li>\n";
			}

		echo "</ul>\n";


		require("include/version.php");

		echo "<h3>Powered by Pumilio</h3>
			<p><strong>Pumilio v. $website_version</strong> - A sound archive manager and visualization web application. <a href=\"http://ljvillanueva.github.io/pumilio\">http://ljvillanueva.github.io/pumilio</a>
			<br>Copyright (&copy;) $copyright_years Luis J. Villanueva-Rivera (ljvillanueva@coquipr.com)</p>";
?>

	
	
	    

	<p><strong>Citation</strong>: Villanueva-Rivera, Luis J., and Bryan C. Pijanowski. 2012. Pumilio: A Web-Based Management System for Ecological Recordings. Bulletin of the Ecological Society of America 93: 71-81. doi: 10.1890/0012-9623-93.1.71</p>

	<pre>
	    This program is free software: you can redistribute it and/or modify
	    it under the terms of the GNU General Public License as published by
	    the Free Software Foundation, either version 3 of the License, or
	    (at your option) any later version.
	 
	    This program is distributed in the hope that it will be useful,
	    but WITHOUT ANY WARRANTY; without even the implied warranty of
	    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	    GNU General Public License for more details.

	    You should have received a copy of the GNU General Public License
	    along with this program.  If not, see <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.
	</pre>

	<p><a href="gplv3.php"><img src="images/gplv3-88x31.png" border="0"> Click here for the full GPLv3 License text</a>

	<hr noshade>

	<p>This application uses several other open source tools and programs, including:

	<ul>
		<li> <a href="http://getbootstrap.com/" target="_blank">Bootstrap Framework</a></li>
		<li> <a href="http://leafletjs.com/" target="_blank">Leaflet</a></li>
		<li> <a href="http://sox.sourceforge.net/" target="_blank">SoX</a></li>
		<li> wav2png script by <a href="http://www.freesound.org" target="_blank">Freesound</a></li>
		<li> <a href="http://www.longtailvideo.com/players/jw-flv-player/" target="_blank">JW Player</a></li>
		<li> <a href="http://www.jplayer.org" target="_blank">JPlayer</a></li>
		<li> <a href="http://www.flotcharts.org" target="_blank">Flot</a></li>
		<li> <a href="http://www.ar.media.kyoto-u.ac.jp/members/david/softwares/audiolab/sphinx/index.html" target="_blank">Audiolab Python module</a></li>
		<li> <a href="http://www.jquery.com" target="_blank">JQuery Javascript library</a></li>
		<li> <a href="http://jqueryui.com" target="_blank">JQuery UI</a></li>
		<li> <a href="http://deepliquid.com/content/Jcrop.html" target="_blank">JCrop image cropping plugin</a></li>
		<li> <a href="http://github.com/Xeoncross/DByte">DByte database layer</a></li>
		<li> <a href="http://www.plupload.com" target="_blank">Plupload</a></li>
		<!-- <li> <a href="http://mobiledetect.net" target="_blank">Mobile Detect</a></li> -->
		<li> <a href="http://maps.google.com" target="_blank">Google Maps</a></li>
		<!-- <li> <a href="http://www.google.com/webfonts" target="_blank">Google Web Fonts</a></li> -->
		<!-- <li> <a href="http://www.andrewdavidson.com/articles/spinning-wait-icons/" target="_blank">Ajax wait icons by Andrew B. Davidson</a></li> -->
		<li> <a href="http://everaldo.com/crystal/?action=downloads" target="_blank">Crystal Project icons</a></li>
		<li> <a href="http://www.famfamfam.com/lab/icons/silk/" target="_blank">Silk icons</a></li>
	</ul>

	<p>The website for this app is hosted at GitHub: <a href="http://ljvillanueva.github.io/pumilio">http://ljvillanueva.github.io/pumilio</a>

<?php
require("include/bottom.php");
?>


</body>
</html>
