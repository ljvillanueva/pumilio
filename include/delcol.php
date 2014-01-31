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

<title>$app_custom_name - Delete Collections</title>";

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

</head>
<body>

<div style="padding: 10px;">

	<?php
	if ($op == "1") {
		$query = "SELECT SoundID from Sounds WHERE ColID='$ColID' AND SoundStatus!='9'";
		$result = mysqli_query($connection, $query)
			or die (mysqli_error($connection));
		$nrows = mysqli_num_rows($result);
	
		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			
			query_one("DELETE FROM SoundsImages WHERE SoundID='$SoundID'", $connection);
			query_one("DELETE FROM Tags WHERE SoundID='$SoundID'", $connection);
			}

		delTree('../sounds/images/' . $ColID . '/');
		delTree('../sounds/previewsounds/' . $ColID . '/');
		delTree('../sounds/sounds/' . $ColID . '/');
		query_one("UPDATE Sounds SET SoundStatus='9' WHERE ColID='$ColID'", $connection);
		#query_one("DELETE FROM Collections WHERE ColID='$ColID'", $connection);

		header("Location: delcol.php?op=9");
		die();
		}
	elseif ($op == "9") {
		echo "<p><div class=\"success\">The files were deleted successfully.<br>
			<a href=\"#\" onClick=\"window.close();\">Close window</a></div>";
		}
	?>

	<h3>Delete the files from a collection</h3>

	<p>This menu lets you delete a whole collection from the archive. 
	<br><strong>The original sound files will be deleted from the disk. This can not be undone.</strong></p>
			
	<?php
	
	#Delete file div
	echo "<div id=\"dialog\" title=\"Delete the files?\">
	<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The files for this collection will be permanently deleted and cannot be recovered. Are you sure?</p>
	</div>";

	echo "<form id=\"del1\" name=\"del1\" action=\"delcol.php\" method=\"GET\">";
	$query = "SELECT * from Collections ORDER BY CollectionName";
	$result = mysqli_query($connection, $query)
		or die (mysqli_error($connection));
	$nrows = mysqli_num_rows($result);
	echo "<select name=\"ColID\" class=\"ui-state-default ui-corner-all\">";

		for ($i=0;$i<$nrows;$i++) {
			$row = mysqli_fetch_array($result);
			extract($row);
			
			$this_no_sounds=query_one("SELECT COUNT(*) as this_no_sounds FROM Sounds WHERE ColID='$ColID' AND SoundStatus!='9'", $connection);
			if ($this_no_sounds>0) {
				echo "<option value=\"$ColID\">$CollectionName - $this_no_sounds sound files</option>\n";
				}
			#else {
			#	echo "<option value=\"$ColID\">$CollectionName</option>\n";
			#	}
			}

		echo "</select> 
		<input type=\"hidden\" name=\"op\" value=\"1\">
		<input type=submit value=\" Delete \" class=\"fg-button ui-state-default ui-corner-all\">
	</form>
	<hr noshade>";

	?>

	<br><br>
<br><p><a href="#" onClick="window.close();">Cancel and close window</a>

</div>

</body>
</html>
