<?php
session_start();

ignore_user_abort(true);
set_time_limit(0);

#Calculate time to complete
$Time0=strtotime("now");

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
    require("config.php");
} else {
    header("Location: error.php?e=config");
    die();
}

require("include/apply_config.php");

$force_loggedin = TRUE;
require("include/check_login.php");

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$files_format=strtolower(filter_var($_POST["files_format"], FILTER_SANITIZE_STRING));
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$fields=filter_var($_POST["fields"], FILTER_SANITIZE_STRING);
$fields_to_use_counter=filter_var($_POST["fields_to_use_counter"], FILTER_SANITIZE_NUMBER_INT);
$files_to_process=filter_var($_POST["files_to_process"], FILTER_SANITIZE_STRING);
$files_to_process_counter=filter_var($_POST["files_to_process_counter"], FILTER_SANITIZE_NUMBER_INT);

$commadata=$_POST["commadata"];

if ($dir=="") {
	die();
	}

if ($files_format=="") {
	die();
	}

$files_format_length=strlen($files_format);

echo "
<html>
<head>

<title>$app_custom_name - Add from a database or spreadsheet</title>";

require("include/get_css.php");
?>

<?php
	require("include/get_jqueryui.php");
?>

<?php
if ($use_googleanalytics) {
	echo $googleanalytics_code;
	}
?>

</head>
<body>

	<!-- Scripts for Javascript tooltip from http://www.walterzorn.com/tooltip/tooltip_e.htm -->
	<script type="text/javascript" src="include/wz_tooltip/wz_tooltip.js"></script>

	<!--Blueprint container-->
	<div class="container">
		<?php
			require("include/topbar.php");
		?>
		<div class="span-24 last">
			<hr noshade>
		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php

			echo "<h3>Add files from a database or spreadsheet</h3>";

			$commadata_file=$commadata;
			$commadata=explode("\n", $commadata);
			$commadata_count=count($commadata);

			if ($commadata_count!=$files_to_process_counter) {
				echo "<div class=\"error\">The number of lines ($commadata_count) does not match the number of files to import ($files_to_process_counter). Please go back and try again.</div><br><br>";
				die();
				}


			$file_errors=0;
			
			for ($i=0;$i<$commadata_count;$i++) {
				$this_row=$commadata[$i];
				$this_row1=explode("|" , $this_row);
				$j=$i+1;
				#Check that the number of fields match
				$this_row_counter=count($this_row1);
				if ($this_row_counter!=$fields_to_use_counter) {
					echo "<div class=\"error\">The number of fields ($this_row_counter) in line $j does not match the number of fields to import ($fields_to_use_counter). Please go back and try again.</div><br><br>";
					die();
					}

				#Check that the files are ok
				$this_file=filter_var($this_row1[0], FILTER_SANITIZE_STRING);
				exec('include/soundcheck.py ' . $dir . '/' . $this_file, $lastline, $retval);
				if ($retval!=0) {
					echo "<div class=\"error\">The file " . $this_file . " could not be read or it is not an audio file. Please go back and try again.</div>";
					$file_errors+=1;
					}
				}

				if ($file_errors>0) {
					echo "<br><br><div class=\"error\">There were $file_errors errors, fix them before importing can be done.</div>";
					die();
					}


			#ALL CHECKS OK
			
			echo "</div>
			<div class=\"span-24 last\">&nbsp;
			<p>Please wait, importing...</p>
				<iframe src=\"progressbar.php?per=1\" width=\"100%\" height=\"30\" frameborder=\"0\" id=\"progress_bar\" scrolling=\"no\"></iframe>&nbsp;
			<div id=\"progress_counter\"><strong>0 of $commadata_count files imported</strong></div>
			</div>
			<div class=\"span-24 last\">";
			
			
			require("include/addtodb.php");
			#require("include/update_sites.php");

			echo "<br><div class=\"success\">$success_counter files were addedd successfully to the database.</div>
				<p><a href=\"db_browse.php?ColID=$ColID\">Browse this collection</a>.";

			?>

		</div>
		<div class="span-24 last">
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			?>

		</div>
	</div>

</body>
</html>

