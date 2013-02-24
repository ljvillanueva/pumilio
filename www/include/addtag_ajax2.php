<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config.php");

$force_loggedin = TRUE;
require("include/check_login.php");

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$i=filter_var($_GET["this_i"], FILTER_SANITIZE_NUMBER_INT);

$newtag=explode(" ",$_GET["newtag"]);
$where_to=filter_var($_GET["where_to"], FILTER_SANITIZE_URL);

foreach($newtag as $newitem){ 
	$newitem1=filter_var($newitem, FILTER_SANITIZE_STRING);
	
	#Check that it does not exist already for this sound
	$result=query_several("SELECT Tag FROM Tags WHERE SoundID='$SoundID' AND Tag='$newitem1'", $connection);
	$nrows = mysqli_num_rows($result);
	if ($nrows==0){			
		$query_tags = "INSERT INTO Tags (SoundID, Tag) VALUES ('$SoundID', '$newitem1')";
		$result_tags = mysqli_query($connection, $query_tags)
			or die (mysqli_error($connection));
		}
	}

echo "<html>
<head>";
?>


<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type="text/javascript" src="js/jquery.form.js"></script> 

<?php
	echo "
	
	<script type=\"text/javascript\">
	$(document).ready(function() { 
	    var options = { 
	        target:        '#tagspace$i',   // target element(s) to be updated with server response 
	        // beforeSubmit:  showRequest,  // pre-submit callback 
	        // success:       showResponse,  // post-submit callback 
	 	clearForm: true,
	 	resetForm: true
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#addtags$i').ajaxForm(options); 
	}); 
	</script>
	
	";

?>

</head>
<body>
	<!-- Scripts for Javascript tooltip from http://www.walterzorn.com/tooltip/tooltip_e.htm -->
	<script type="text/javascript" src="include/wz_tooltip/wz_tooltip.js"></script>
<?php
// Relocate back to the first page of the application
	echo "<form method=\"get\" action=\"include/addtag_ajax2.php\" id=\"addtags$i\">";
	require("../include/managetagsp.php");
		echo "<p>Add tags:<br>
			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
			<input type=\"hidden\" name=\"this_i\" value=\"$i\">
			<input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:10px\">
			<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\" onmouseover=\"Tip('Add new tag', FONTCOLOR, '#fff',BGCOLOR, '#4aa0e0', FADEIN, '400', FADEOUT, '400', ABOVE, 'true', CENTERMOUSE, 'true')\" onmouseout=\"UnTip()\" >
			<em>Separate tags with a space</em></form><br>";

?>
</body>
</html>
