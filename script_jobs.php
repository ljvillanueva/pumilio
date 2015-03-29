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
			echo "<h3>Script jobs</h3>\n";
			echo "This option has been disabled until the next version.";
			/*	
			echo "<p>This option allows users to create a queue to run a script on sample sets. 
					The scripts should be in R or Python.
					Each script ran in a sample set is stored as a job.";
					
			echo "<hr noshade>
			<p>Jobs in the queue:";

			$query_jobs = "SELECT * from QueueJobs ORDER BY JobName";
			$result_jobs = mysqli_query($connection, $query_jobs)
				or die (mysqli_error($connection));
			$nrows_jobs = mysqli_num_rows($result_jobs);

			if ($nrows_jobs>0) {
				echo "<p>There are $nrows_jobs jobs in the system:<br>";
				for ($job=0;$job<$nrows_jobs;$job++) {
					$row_jobs = mysqli_fetch_array($result_jobs);
					extract($row_jobs);
					//How many sounds associated with that source
					$no_sounds_job=query_one("SELECT COUNT(*) FROM Queue WHERE JobID='$JobID'", $connection);
					$no_sounds_done=query_one("SELECT COUNT(*) FROM Queue WHERE JobID='$JobID' AND Status>1", $connection);
					$no_sounds_completed=query_one("SELECT COUNT(*) FROM Queue WHERE JobID='$JobID' AND Status='2'", $connection);
		
					$no_sounds_errors=query_one("SELECT COUNT(*) FROM Queue WHERE JobID='$JobID' AND Status='3'", $connection);
					$no_sounds_404=query_one("SELECT COUNT(*) FROM Queue WHERE JobID='$JobID' AND Status='5'", $connection);
					$no_sounds_anyerrors=$no_sounds_errors+$no_sounds_404;
		
					$percent_done=round(($no_sounds_done/$no_sounds_job)*100, 2);
		
					#Don't round up to 100%
					if ($percent_done==100) {
						if ($no_sounds_job!=$no_sounds_done)
							$percent_done=99.99;
						}
		
					$percent_error=round(($no_sounds_errors/$no_sounds_job)*100, 2);
					$percent_404=round(($no_sounds_404/$no_sounds_job)*100, 2);
		
					$this_username=query_one("SELECT UserName FROM Users WHERE UserID='$UserID' LIMIT 1", $connection);
		
					echo "<br><a href=\"managejobs.php?JobID=$JobID\" target=\"managejobs\" onclick=\"window.open('managejobs.php?JobID=$JobID', 'managejobs', 'width=600,height=800,status=yes,resizable=yes,scrollbars=yes')\">$JobName</a> - $no_sounds_job items in the job. $percent_done% completed. Created by $this_username.";
		
					if ($no_sounds_anyerrors>0) {
						echo "<br><em style=\"color: red; margin-left: 40px;\">There were errors in this job: ";
						if ($no_sounds_errors>0)
							{echo " $percent_error % had script errors";}
						if ($no_sounds_404>0)
							{echo " $percent_404 % could not be found";}
						echo "</em>";
						}
					}

				}
			else {
				echo "<p> &nbsp;&nbsp;There are no jobs.";
				}

			echo "<hr noshade>
			<p><strong>Create a job on samples</strong>";

			$query_sample = "SELECT * from Samples ORDER BY SampleName";
			$result_sample = mysqli_query($connection, $query_sample)
				or die (mysqli_error($connection));
			$nrows_sample = mysqli_num_rows($result_sample);

			if ($nrows_sample>0) {
				echo "<form method=\"POST\" action=\"include/createqueue.php\" target=\"q\" onsubmit=\"window.open('', 'q', 'width=700,height=400,status=yes,resizable=yes,scrollbars=yes')\">
				Name for the job: <input type=\"text\" name=\"jobname\" maxlength=\"20\" size=\"14\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>";
				echo "Sample set: <select name=\"SampleID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";
				for ($sa=0;$sa<$nrows_sample;$sa++) {
					$row_sample = mysqli_fetch_array($result_sample);
					extract($row_sample);
						//How many sounds associated with that source
						$no_sounds_sample=query_one("SELECT COUNT(*) as no_sounds FROM SampleMembers WHERE SampleID='$SampleID'", $connection);
					if ($no_sounds_sample>0)
						echo "<option value=\"$SampleID\">$SampleName - $no_sounds_sample sound files</option>\n";
					}

				echo "</select> <br>";


				$query_script = "SELECT ScriptID, ScriptName, ScriptVersion, Language from Scripts ORDER BY ScriptName, ScriptVersion";
				$result_script = mysqli_query($connection, $query_script)
				or die (mysqli_error($connection));
				$nrows_script = mysqli_num_rows($result_script);
				if ($nrows_script>0) {
					echo "Script: <select name=\"ScriptID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";
					for ($c=0;$c<$nrows_script;$c++) {
						$row_script = mysqli_fetch_array($result_script);
						extract($row_script);
			
						echo "<option value=\"$ScriptID\">$ScriptName ($Language) v. $ScriptVersion</option>\n";
						}
					echo "</select>";
					echo "<p><input type=submit value=\" Create Job \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";
				}
				else {
					echo "<p> &nbsp;&nbsp;There are no scripts stored in the database.<br>";
					}
				echo "</form>";
				}
			else {
				echo "<p> &nbsp;&nbsp;There are no sample sets in the database yet.";
				}

			echo "<hr noshade>";

			echo "
			<p><strong>Create a job for a site</strong>";

			$query_sample = "SELECT * from Sites ORDER BY SiteName";
			$result_sample = mysqli_query($connection, $query_sample)
				or die (mysqli_error($connection));
			$nrows_sample = mysqli_num_rows($result_sample);

			if ($nrows_sample>0) {
				echo "<form method=\"POST\" action=\"include/createqueue.php\" target=\"q\" onsubmit=\"window.open('', 'q', 'width=700,height=400,status=yes,resizable=yes,scrollbars=yes')\">
				Name for the job: <input type=\"text\" name=\"jobname\" maxlength=\"20\" size=\"14\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"><br>";
				echo "Site: <select name=\"SiteID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";
				for ($sa=0;$sa<$nrows_sample;$sa++) {
					$row_sample = mysqli_fetch_array($result_sample);
					extract($row_sample);
						//How many sounds associated with that source
						$no_sounds_site=query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE SiteID='$SiteID'", $connection);
					if ($no_sounds_site>0)
						echo "<option value=\"$SiteID\">$SiteName - $no_sounds_site sound files</option>\n";
					}

				echo "</select> <br>";


				$query_script = "SELECT ScriptID, ScriptName, ScriptVersion, Language from Scripts ORDER BY ScriptName, ScriptVersion";
				$result_script = mysqli_query($connection, $query_script)
				or die (mysqli_error($connection));
				$nrows_script = mysqli_num_rows($result_script);
				if ($nrows_script>0) {
					echo "Script: <select name=\"ScriptID\" class=\"ui-state-default ui-corner-all\" style=\"font-size:12px\">";
					for ($c=0;$c<$nrows_script;$c++) {
						$row_script = mysqli_fetch_array($result_script);
						extract($row_script);
			
						echo "<option value=\"$ScriptID\">$ScriptName ($Language) v. $ScriptVersion</option>\n";
						}
					echo "</select>";
					echo "<p>
					<input type=\"hidden\" name=\"type\" value=\"site\">
					<input type=submit value=\" Create Job \" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\"></form>";
				}
				else {
					echo "<p> &nbsp;&nbsp;There are no scripts stored in the database.<br>";
					}
				echo "</form>";
				}
			else {
				echo "<p> &nbsp;&nbsp;There are no sample sets in the database yet.";
				}

			echo "<hr noshade>
			<p><strong>Save a new script:</strong>
			<form method=\"POST\" action=\"include/addscript.php\" target=\"R\" onsubmit=\"window.open('', 'R', 'width=700,height=400,status=yes,resizable=yes,scrollbars=yes')\">
				Name of script: <input type=\"text\" name=\"scriptname\" size=\"30\" class=\"fg-button ui-state-default ui-corner-all\">
				<br>Script version: <input type=\"text\" name=\"scriptver\" size=\"10\" class=\"fg-button ui-state-default ui-corner-all\">
				<br>Language: <select name=\"Lang\" class=\"ui-state-default ui-corner-all\">
					<option>R</option>
					<option>Python</option>
				</select><br>
				<textarea name=\"script\" class=\"ui-corner-all\" style=\"width: 500px; height: 300px;\"></textarea>
				<p><input type=submit value=\" Save new script \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>\n";
			*/
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
