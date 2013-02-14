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

require("include/check_admin.php");

#Sanitize inputs
$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
$d=filter_var($_GET["d"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>

<title>$app_custom_name - Edit Collection Info</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>


<script type="text/javascript">
	$(function() {
		$("#tabs0").tabs();
	});
	</script>

	<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#EditForm").validate({
			rules: {
				CollectionName: {
					required: true
				}
			},
			messages: {
				SoundName: "Please enter the name of the file"
			}
			});
		});
	</script>
	<style type="text/css">
	#fileForm label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>

<?php
if ($use_googleanalytics)
	{echo $googleanalytics_code;}
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

			<?php

			$query = "SELECT * FROM Collections WHERE ColID='$ColID'";

			$result=query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
			if ($nrows==1) {
				$row = mysqli_fetch_array($result);
				extract($row);
					
				echo "<div class=\"span-24 last\">			
					<h3>Edit collection information</h3>";

				if ($d==1) {
					echo "<p><div class=\"success\">Collection was updated successfully. Return to <a href=\"db_browse.php?ColID=$ColID\">browsing</a></div>";
					}


				echo "<p>
				<form action=\"edit_collection2.php\" method=\"POST\" id=\"EditForm\">
					<input name=\"ColID\" type=\"hidden\" value=\"$ColID\">

				Name of the collection: <br><input name=\"CollectionName\" type=\"text\" maxlength=\"160\" size=\"60\" value=\"$CollectionName\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				Author: <br><input name=\"Author\" type=\"text\" size=\"40\" value=\"$Author\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				Full citation: <br><input name=\"CollectionFullCitation\" type=\"text\" maxlength=\"250\" size=\"80\" value=\"$CollectionFullCitation\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				URL: <br><input name=\"MiscURL\" type=\"text\" size=\"40\" value=\"$MiscURL\" maxlength=\"250\" id=\"MiscURL\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				Notes: <br><input name=\"Notes\" type=\"text\" size=\"60\" value=\"$Notes\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
				Type of Collection: <br><select name=\"FilesSource\" class=\"ui-state-default ui-corner-all formedge\">\n";

				if ($FilesSource=="Field Recording")
					echo "<option SELECTED>Field Recording</option>";
				else
					echo "<option>Field Recording</option>";
				
				if ($FilesSource=="Book with CD")
					echo "<option SELECTED>Book with CD</option>";
				else
					echo "<option>Book with CD</option>";

				if ($FilesSource=="Automated Audio Logger")
					echo "<option SELECTED>Automated Audio Logger</option>";
				else
					echo "<option>Automated Audio Logger</option>";

				if ($FilesSource=="Audio CD")
					echo "<option SELECTED>Audio CD</option>";
				else
					echo "<option>Audio CD</option>";

				if ($FilesSource=="CD-ROM")
					echo "<option SELECTED>CD-ROM</option>";
				else
					echo "<option>CD-ROM</option>";
				
				
				if ($FilesSource=="DVD")
					echo "<option SELECTED>DVD</option>";
				else
					echo "<option>DVD</option>";
				
				
				if ($FilesSource=="Tape")
					echo "<option SELECTED>Tape</option>";
				else
					echo "<option>Tape</option>";
				
				
				if ($FilesSource=="Internet")
					echo "<option SELECTED>Internet</option>";
				else
					echo "<option>Internet</option>";
				
				
				if ($FilesSource=="Donation")
					echo "<option SELECTED>Donation</option>";
				else
					echo "<option>Donation</option>";
				
				
				if ($FilesSource=="Other")
					echo "<option SELECTED>Other</option>";
				else
					echo "<option>Other</option>";
				
				echo "</select><br>";

				echo "
				<input type=\"submit\" value=\" Edit Collection \"  class=\"fg-button ui-state-default ui-corner-all\">
				</form>
			
				<br>
				<form action=\"db_browse.php\" method=\"GET\">
					<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
					<input type=submit value=\" Cancel \" class=\"fg-button ui-state-default ui-corner-all\">
				</form>
				<br>
				<hr noshade>
				</div>";

				$no_sounds = query_one("SELECT COUNT(*) as no_sounds FROM Sounds WHERE ColID='$ColID' AND Sounds.SoundStatus!='9'", $connection);
				echo "<div class=\"span-24 last\">
					<a name=\"anchor2\"></a>		
					<h3>Add or edit metadata for all the files in this collection</h3>\n";

				if ($d==2) {
					echo "<p><div class=\"success\">Metadata was updated successfully. 
					Return to <a href=\"db_browse.php?ColID=$ColID\">browsing</a></div>";}

				echo "<p><form action=\"edit_collection3.php\" method=\"POST\">
					<input name=\"ColID\" type=\"hidden\" value=\"$ColID\">";

				$query_e = "SELECT SensorID as SensorID_q, Recorder from Sensors ORDER BY Recorder";
				$result_e = mysqli_query($connection, $query_e)
					or die (mysqli_error($connection));
				$nrows_e = mysqli_num_rows($result_e);
				echo "Sensor used: <br><select name=\"SensorID\" class=\"ui-state-default ui-corner-all formedge\">";
					echo "<option value=\"\"> </option>\n";

				for ($e=0;$e<$nrows_e;$e++){
					$row_e = mysqli_fetch_array($result_e);
					extract($row_e);

					if ($SensorID_q==$SensorID)
						echo "<option value=\"$SensorID_q\" SELECTED>$Recorder</option>\n";
					else
						echo "<option value=\"$SensorID_q\">$Recorder</option>\n";
					}
				echo "</select> <small><a href=\"admin.php?t=4\">Add sensors to the list</a></small><br>";

				echo "
				Notes: <br><input name=\"Notes\" type=\"text\" maxlength=\"250\" size=\"60\" value=\"$Notes\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>

				<input type=\"submit\" value=\" Add metadata of the files \"  class=\"fg-button ui-state-default ui-corner-all\">
				</form>
				
				<div class=\"notice\">
					<img src=\"images/error.png\"> This will replace the fields in the $no_sounds files for this collection.
				</div>
			
				<br>
				<form action=\"db_browse.php\" method=\"GET\">
					<input type=\"hidden\" name=\"ColID\" value=\"$ColID\">
					<input type=submit value=\" Cancel \" class=\"fg-button ui-state-default ui-corner-all\">
					</form>
				</div>";

				}

			?>

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
