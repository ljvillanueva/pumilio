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
$force_loggedin = TRUE;
require("include/check_login.php");
		
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Sample the archive</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

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

		<?php
			echo "<h3>Sample the archive</h3>\n";
					
			echo "<p>This option allows users to create a sample set from the archive for further 
				analysis. This sample is stored and can be accessed using a sample set name.
				You can design a random sample set from either the whole archive or from particular collections:</p>
			<hr noshade>
			<p>Sample sets in the system:";

			#Check for deleted files
			$query_sample = "SELECT * from Samples ORDER BY SampleName";
			$result_sample = mysqli_query($connection, $query_sample)
				or die (mysqli_error($connection));
			$nrows_sample = mysqli_num_rows($result_sample);

			if ($nrows_sample > 0) {
				for ($sa = 0; $sa < $nrows_sample; $sa++) {
					$row_sample = mysqli_fetch_array($result_sample);
					extract($row_sample);
					$no_sounds_sample=query_one("SELECT COUNT(*) FROM SampleMembers WHERE SampleID='$SampleID'", $connection);
					if ($no_sounds_sample > 0){
						$query_sample_check = "SELECT SoundID from SampleMembers WHERE SampleID='$SampleID'";
						$result_sample_check = mysqli_query($connection, $query_sample_check)
							or die (mysqli_error($connection));
						$nrows_sample_check = mysqli_num_rows($result_sample_check);
						if ($nrows_sample_check > 0){
							for ($ch = 0; $ch < $nrows_sample_check; $ch++) {
								$row_check = mysqli_fetch_array($result_sample_check);
								extract($row_check);
								$check_this_sound=query_one("SELECT COUNT(*) FROM Sounds WHERE SoundID='$SoundID'", $connection);
								if ($check_this_sound == 0){
									query_one("DELETE FROM SampleMembers WHERE SampleID='$SampleID' AND SoundID='$SoundID'", $connection);
									}
								}
							}
		
						}
					}
				}
			#End check			

			$query_sample = "SELECT * from Samples ORDER BY SampleName";
			$result_sample = mysqli_query($connection, $query_sample)
				or die (mysqli_error($connection));
			$nrows_sample = mysqli_num_rows($result_sample);

			if ($nrows_sample > 0) {
				echo "<br><form action=\"browse_sample.php\" method=\"GET\"><strong>Browse the sample sets in the archive:</strong><br>";
				echo "<select name=\"SampleID\" class=\"ui-state-default ui-corner-all\">";
				for ($sa = 0; $sa < $nrows_sample; $sa++) {
					$row_sample = mysqli_fetch_array($result_sample);
					extract($row_sample);
						//How many sounds associated with that source
						$no_sounds_sample = query_one("SELECT COUNT(*) as no_sounds FROM SampleMembers WHERE SampleID='$SampleID'", $connection);
					if ($no_sounds_sample > 0){
						echo "<option value=\"$SampleID\">$SampleName - $no_sounds_sample sound files</option>\n";
						}
					else{
						echo "<option value=\"$SampleID\">$SampleName - empty sample set</option>\n";
						}
					}

				echo "</select> <br>
				<input type=submit value=\" Browse sample set \" class=\"fg-button ui-state-default ui-corner-all\"></form><br>";
				}
			else {
				echo "<p> &nbsp;&nbsp;<em>There are no sample sets in the database.</em><br><hr noshade>";
				}

			#Sample from whole archive
			$no_sounds = query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE Sounds.SoundStatus!='9'", $connection);
			if ($no_sounds > 0) {
				echo "<p>
				<strong>Sample set from the whole archive:</strong>
				<form action=\"add_sample.php\" method=\"POST\" id=\"AddSample1\">
			
					<input type=\"hidden\" name=\"type\" value=\"1\">
					<br>Size of the sample:
					<input type=\"text\" name=\"samplesize\" maxlength=\"10\" size=\"6\" class=\"fg-button ui-state-default ui-corner-all\"> max: $no_sounds
					<br>Name this sample set:
					<input type=\"text\" name=\"samplename\" maxlength=\"80\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
					<br>Notes about this set:
					<input type=\"text\" name=\"samplenotes\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
					<br><input type=submit value=\" Create random sample set \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>
				<br>
		
				<hr noshade>";
	
				#Sample from a collection
				echo "<p><strong>Sample set from a particular collection:</strong><br>
					<form action=\"add_sample.php\" method=\"POST\" id=\"AddSample2\">
					<input type=\"hidden\" name=\"type\" value=\"2\">";
		
				$query_setsample = "SELECT * from Collections ORDER BY CollectionName";
				$result_setsample = mysqli_query($connection, $query_setsample)
					or die (mysqli_error($connection));
				$nrows_setsample = mysqli_num_rows($result_setsample);
				#from http://codepunk.hardwar.org.uk/ajs23.htm
				echo "<select name=\"ColID\" id=\"ColID\" class=\"ui-state-default ui-corner-all\" onchange=\"this.form['samplename'].value=this.form['ColID'].options[this.form['ColID'].options.selectedIndex].text;\">";
				for ($ss = 0; $ss < $nrows_setsample; $ss++) {
					$row_setsample = mysqli_fetch_array($result_setsample);
					extract($row_setsample);
					//How many sounds associated with that source
					$no_sounds_setsample = query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE ColID='$ColID'  AND Sounds.SoundStatus!='9'", $connection);
					if ($no_sounds_setsample > 0){
						echo "<option value=\"$ColID\" text=\"$CollectionName\">$CollectionName - $no_sounds_setsample sound files</option>\n";
						}
					}

				echo "</select>
				<br>Size of the sample:
				<input type=\"text\" name=\"samplesize\" maxlength=\"10\" size=\"6\" class=\"fg-button ui-state-default ui-corner-all\"> max: number of sounds in that collection
				<br><input type=\"checkbox\" name=\"time_limits\" value=\"1\" class=\"fg-button ui-state-default ui-corner-all\" />Select only between: 
					<input type=\"text\" name=\"time_min\" maxlength=\"10\" size=\"6\" class=\"fg-button ui-state-default ui-corner-all\"> and <input type=\"text\" name=\"time_max\" maxlength=\"10\" size=\"6\" class=\"fg-button ui-state-default ui-corner-all\"> (hh:mm:ss) [optional]
				<br>Name this sample set:
				<input type=\"text\" name=\"samplename\" id=\"samplename\" maxlength=\"80\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br>Notes about this set:
				<input type=\"text\" name=\"samplenotes\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br><input type=submit value=\" Create random sample set \" class=\"fg-button ui-state-default ui-corner-all\">
				</form><br>
				";
		
				#Sample all collections
				echo "<hr noshade>
				<p><strong>Sample all collections:</strong>
	
				<form action=\"add_sample.php\" method=\"POST\" id=\"AddSample3\">
				<input type=\"hidden\" name=\"type\" value=\"3\">
				<p>A set will be created for each collection in the archive with the name of the collection.
				<br>Size of the sample:
				<select name=\"sample_percent\" id=\"sample_percent\" class=\"ui-state-default ui-corner-all\">
					<option></option>";
			
				for ($p = 5; $p < 96; $p = $p + 5) {
					echo "<option value=\"$p\">$p %</option>\n";
					}
		
				echo "</select>
				<br>Notes about this set:
				<input type=\"text\" name=\"samplenotes\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br><input type=submit value=\" Create sample sets \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
	
				#Sample all sites
				echo "<hr noshade>
				<p><strong>Sample all sites:</strong>
	
				<form action=\"add_sample.php\" method=\"POST\" id=\"AddSample4\">
				<input type=\"hidden\" name=\"type\" value=\"4\">
				<p>A set will be created for each site in the archive with the name of the site.
				<br>Size of the sample:
				<select name=\"sample_percent\" id=\"sample_percent\" class=\"ui-state-default ui-corner-all\">
					<option></option>";
			
				for ($p = 5; $p < 96; $p = $p + 5) {
					echo "<option value=\"$p\">$p %</option>\n";
					}
		
				echo "</select>
				<br>Notes about this set:
				<input type=\"text\" name=\"samplenotes\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br><input type=submit value=\" Create sample sets \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";


				#Sample from whole archive by time
				echo "<hr noshade>
				<p><strong>Sample set from the whole archive:</strong><br>
					<form action=\"add_sample.php\" method=\"POST\" id=\"AddSample2\">
					<input type=\"hidden\" name=\"type\" value=\"6\">

				<br>Select all the sounds in the archive between:
				<br><input type=\"text\" name=\"time_min\" maxlength=\"10\" size=\"6\" value=\"00:00:00\" class=\"fg-button ui-state-default ui-corner-all\"> and 
					<input type=\"text\" name=\"time_max\" maxlength=\"10\" size=\"6\" value=\"23:59:59\" class=\"fg-button ui-state-default ui-corner-all\"> (hh:mm:ss)
				<br>Name this sample set:
				<input type=\"text\" name=\"samplename\" id=\"samplename\" maxlength=\"80\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br>Notes about this set:
				<input type=\"text\" name=\"samplenotes\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br><input type=submit value=\" Create random sample set \" class=\"fg-button ui-state-default ui-corner-all\">
				</form><br>
				";

				#Empty set
				echo "<hr noshade>
				<p><strong>Create an empty sample set:</strong>
	
				<form action=\"add_sample.php\" method=\"POST\" id=\"AddSample5\">
				<input type=\"hidden\" name=\"type\" value=\"5\">
				<p>An empty sample set will be created. You can add sounds individually.
				<br>Name this sample set:
				<input type=\"text\" name=\"samplename\" id=\"samplename\" maxlength=\"80\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br>Notes about this set:
				<input type=\"text\" name=\"samplenotes\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br><input type=submit value=\" Create sample set \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>";
				}
			else {
				echo "<p> &nbsp;&nbsp;There are no sounds in the database yet.";
				}
					
			?>
			
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