<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_admin = TRUE;
require("check_admin.php");

$op=filter_var($_GET["op"], FILTER_SANITIZE_NUMBER_INT);
$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
 require("get_css_include.php");
 require("get_jqueryui_include.php");
?>

<!-- JQuery Confirmation -->
<script type="text/javascript">
	$(function() {
		$("#dialog").dialog({
			autoOpen: false,
			bgiframe: true,
			resizable: false,
			draggable: false,
			height:140,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
		 buttons: {
		                "Delete files": function() {
		                    document.del1.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#del1').submit(function(){
		            $("p#dialog-email").html($("input#ColID").val());
		            $('#dialog').dialog('open');
		            return false;
		        });
		});

	</script>
<script type="text/javascript">
	$(function() {
		$("#dialog2").dialog({
			autoOpen: false,
			bgiframe: true,
			resizable: false,
			draggable: false,
			height:140,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
		 buttons: {
		                "Delete files": function() {
		                    document.del2.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#del2').submit(function(){
		            $("p#dialog-email").html($("input#ColID").val());
		            $('#dialog2').dialog('open');
		            return false;
		        });
		});

	</script>
<script type="text/javascript">
	$(function() {
		$("#dialog3").dialog({
			autoOpen: false,
			bgiframe: true,
			resizable: false,
			draggable: false,
			height:140,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
		 buttons: {
		                "Delete files": function() {
		                    document.del3.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#del3').submit(function(){
		            $("p#dialog-email").html($("input#ColID").val());
		            $('#dialog3').dialog('open');
		            return false;
		        });
		});

	</script>
	
<script type="text/javascript">
	$(function() {
		$("#dialog7").dialog({
			autoOpen: false,
			bgiframe: true,
			resizable: false,
			draggable: false,
			height:140,
			modal: true,
			overlay: {
				backgroundColor: '#000',
				opacity: 0.5
			},
		 buttons: {
		                "Delete files": function() {
		                    document.del7.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#del7').submit(function(){
		            $("p#dialog-email").html($("input#ColID").val());
		            $('#dialog7').dialog('open');
		            return false;
		        });
		});

	</script>
</head>
<body>

<div style="padding: 10px;">

	<?php
	if ($op == "1") {
			delTree('../sounds/images/' . $ColID . '/');
			delTree('../sounds/previewsounds/' . $ColID . '/');
			header("Location: delauxfiles.php?op=9");
			die();
		}
	elseif ($op == "2") {
			delTree('../sounds/images/' . $ColID . '/');
			header("Location: delauxfiles.php?op=9");
			die();
		}
	elseif ($op == "3") {
			delTree('../sounds/previewsounds/' . $ColID . '/');
			header("Location: delauxfiles.php?op=9");
			die();
		}
	elseif ($op == "7") {
		#Delete file div
		echo "<p><strong>Delete all the image files (spectrograms and waveforms) from the archive?</strong><br>";
		echo "<div id=\"dialog7\" title=\"Delete the images?\">
		<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The image files
			(spectrograms and waveforms) in the archive will be permanently deleted and cannot be 
			recovered. Are you sure?</p>
		</div>";
		echo "<form method=\"GET\" action=\"delauxfiles.php\" id=\"del7\" name=\"del7\">
		<input type=\"hidden\" name=\"op\" value=\"8\">
		<input type=submit value=\" Delete all images from system \" class=\"fg-button ui-state-default ui-corner-all\">
		</form>";
		die();
		}
	elseif ($op == "8") {
		delSubTree('../sounds/images/');
		echo "<p><div class=\"success\">The files were deleted successfully.<br>
			<a href=\"#\" onClick=\"window.close();\">Close window</a></div>";
			die();
		}
	elseif ($op == "9") {
		echo "<p><div class=\"success\">The files were deleted successfully.<br>
			<a href=\"#\" onClick=\"window.close();\">Close window</a></div>";
		}
	?>

	<p>This menu lets you delete the mp3 sound files or the spectrogram and waveform images. <strong>The original sound files are not
				deleted from this menu.</strong></p>
			
	<?php
	echo "<p><strong>Delete all the auxiliary files (mp3 and images) for a particular collection</strong>:";

		#Delete file div
		echo "<div id=\"dialog\" title=\"Delete the auxiliary files?\">
		<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The mp3 and image files for this collection will be permanently deleted and cannot be recovered. Are you sure?</p>
		</div>";

		echo "<form id=\"del1\" name=\"del1\" action=\"delauxfiles.php\" method=\"GET\">";
			$query = "SELECT * from Collections ORDER BY CollectionName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
		echo "<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">";

		for ($i=0;$i<$nrows;$i++){
			$row = mysqli_fetch_array($result);
			extract($row);
			echo "<option value=\"$ColID\">$CollectionName</option>\n";
			}

		echo "</select> 
		<input type=\"hidden\" name=\"op\" value=\"1\">
		<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\"></form>
		<hr noshade>";


	echo "<p><strong>Delete only the images (spectrograms and waveforms) for a particular collection</strong>:";

		#Delete file div
		echo "<div id=\"dialog2\" title=\"Delete the auxiliary files?\">
		<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The image files (spectrograms and waveforms) for this collection will be permanently deleted and cannot be recovered. Are you sure?</p>
		</div>";

		echo "<form id=\"del2\" name=\"del2\" action=\"delauxfiles.php\" method=\"GET\">";
		$query = "SELECT * from Collections ORDER BY CollectionName";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
		echo "<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">";

		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			echo "<option value=\"$ColID\">$CollectionName</option>\n";
			}

		echo "</select> 
		<input type=\"hidden\" name=\"op\" value=\"2\">
		<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\"></form>
		<hr noshade>";


		echo "<p><strong>Delete only the mp3 files for a particular collection</strong>:";

		#Delete file div
		echo "<div id=\"dialog3\" title=\"Delete the auxiliary files?\">
		<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The mp3 files for this collection will be permanently deleted and cannot be recovered. Are you sure?</p>
		</div>";

		echo "<form id=\"del3\" name=\"del3\" action=\"delauxfiles.php\" method=\"GET\">";
		$query = "SELECT * from Collections ORDER BY CollectionName";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
		echo "<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">";

		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			echo "<option value=\"$ColID\">$CollectionName</option>\n";
			}

		echo "</select> 
		<input type=\"hidden\" name=\"op\" value=\"3\">
		<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\"></form>
		<hr noshade>";
	?>

		<br><br>
<br><p><a href="#" onClick="window.close();">Cancel and close window</a>

</div>

</body>
</html>
