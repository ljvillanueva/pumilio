<?php
session_start();

require("functions.php");
require("../config.php");
require("apply_config_include.php");

$force_loggedin = TRUE;
require("check_login.php");

$SoundID=filter_var($_GET["SoundID"], FILTER_SANITIZE_NUMBER_INT);
$this_i=filter_var($_GET["this_i"], FILTER_SANITIZE_NUMBER_INT);
$newtag=explode(" ",$_GET["newtag"]);
$where_to=filter_var($_GET["where_to"], FILTER_SANITIZE_URL);
$where_toq=filter_var($_GET["where_toq"], FILTER_SANITIZE_URL);

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

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>";

echo "<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type=\"text/javascript\" src=\"../js/jquery.form.js\"></script>\n";

for ($ajax=0;$ajax<10;$ajax++){
	echo "	
	<script type=\"text/javascript\">
	$(document).ready(function() { 
	    var options = { 
	        target:        '#tagspace$ajax',   // target element(s) to be updated with server response 
	        // beforeSubmit:  showRequest,  // pre-submit callback 
	        // success:       showResponse,  // post-submit callback 
	 	clearForm: true,
	 	resetForm: true
	    }; 
	 
	    // bind form using 'ajaxForm' 
	    $('#addtags$ajax').ajaxForm(options); 
	}); 
	</script>\n";
	}

?>

<!--jquery form-->
<!-- http://jquery.malsup.com/form/ -->
<script type="text/javascript" src="../js/jquery.form.js"></script> 
<script type="text/javascript">
$(document).ready(function() { 
    var options = { 
        target:        '#tagspace',   // target element(s) to be updated with server response 
        // beforeSubmit:  showRequest,  // pre-submit callback 
        // success:       showResponse,  // post-submit callback 
 	clearForm: true,
 	resetForm: true
        // other available options: 
        //url:       url         // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
 
    // bind form using 'ajaxForm' 
    $('#addtags').ajaxForm(options); 
}); 
</script>

</head>
<body>

<?php
// Relocate back to the first page of the application
	echo "<div id=\"tagspace$this_i\">\n";
	echo "<form method=\"get\" action=\"include/addtag_ajax.php\" id=\"addtags$this_i\">";
		require("../include/managetags.php");
		echo "Add tags:
			<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\">
			<input type=\"hidden\" name=\"goto\" value=\"p\">
			<br><input type=\"text\" size=\"16\" name=\"newtag\" id=\"newtag\" class=\"fg-button ui-state-default ui-corner-all\">
			<INPUT TYPE=\"image\" src=\"images/tag_blue_add.png\" BORDER=\"0\" alt=\"Add new tag\" title=\"Add new tag\" >
			<em>Separate tags with a space</em>
		</form><br>
		</div>";
?>
</body>
</html>
