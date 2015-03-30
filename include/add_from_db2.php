<?php

$dir=filter_var($_POST["dir"], FILTER_SANITIZE_URL);
$files_format=strtolower(filter_var($_POST["files_format"], FILTER_SANITIZE_STRING));
$ColID=filter_var($_POST["ColID"], FILTER_SANITIZE_NUMBER_INT);
$SiteID=filter_var($_POST["SiteID"], FILTER_SANITIZE_NUMBER_INT);
$SensorID=filter_var($_POST["SensorID"], FILTER_SANITIZE_NUMBER_INT);

if ($dir==""){
	die();
	}

if ($files_format==""){
	die();
	}

if (substr($dir, -1)!="/")
	$dir = $dir . "/";

$files_format_length=strlen($files_format);

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
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php

			echo "<h3>Add files from a database or spreadsheet</h3>";

			if (is_dir($dir)) {
				echo "<strong>The directory has these files:</strong><div style=\"width:450px; height: 200px; overflow:auto;\">";
				$handle = opendir($dir);
				$files_to_process = array();
				$files_to_process_counter=0;
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != ".." && substr($file, -$files_format_length)==$files_format) {
						echo "$file<br>\n";
						array_push($files_to_process, $file);
						$files_to_process_counter+=1;
						}
					}
				echo "</div><br><br>";
				closedir($handle);
				}
			else{
				echo "<div class=\"error\">Could not read directory. Please make sure that it exists and that the webserver can read it.</div><br><br>";
				}

			echo "<p>There are a total of <strong>$files_to_process_counter<strong> files to add.<br><br>
			<form action=\"add_from_db.php\" method=\"POST\" id=\"AddForm\">
				<input type=\"hidden\" name=\"step\" value=\"3\">
				<p>If the list above seems right, continue to the next step:<br>
				<input type=\"hidden\" name=\"dir\" value=\"$dir\">
				<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
				<input type=\"hidden\" name=\"SiteID\" value=\"$SiteID\">
				<input type=\"hidden\" name=\"SensorID\" value=\"$SensorID\">
				<input type=\"hidden\" name=\"files_format\" value=\"$files_format\">
				<input type=\"hidden\" name=\"files_to_process_counter\" value=\"$files_to_process_counter\">
				<input type=\"hidden\" name=\"files_to_process\" value=\"$files_to_process\">
				<input type=submit value=\" Select fields \" class=\"fg-button ui-state-default ui-corner-all\">
			</form>";
			?>

