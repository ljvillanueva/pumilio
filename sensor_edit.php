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

$force_loggedin = TRUE;
require("include/check_login.php");

#Sanitize inputs
$SensorID=filter_var($_GET["SensorID"], FILTER_SANITIZE_NUMBER_INT);
if (isset($_GET["u"])){
	$u=filter_var($_GET["u"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$u = 0;
	}

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>

<title>$app_custom_name - Edit Sensor</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");
?>

<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddSensors").validate({
			rules: {
				Recorder: {
					required: true
				},
				Microphone: {
					required: true
				}
			},
			messages: {
				Recorder: "Please enter the name/model of the recorder",
				Microphone: "Please enter the name/model of the microphone"
			}
			});
		});
	</script>
	<style type="text/css">
	#AddSensors label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>
	
	
	
<script type="text/javascript">
	$(function() {
		$("#sensordel2").dialog({
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
		                "Delete sensor": function() {
		                    document.sensordel1.submit();
		                },
		                "Cancel": function() {
		                    $( this ).dialog( "close" );
		                }
		            }
		        });

		        $('form#sensordel1').submit(function(){
		            $('#sensordel2').dialog('open');
		            return false;
		        });
		});

	</script>

<?php
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
			$query = "SELECT * FROM Sensors WHERE SensorID='$SensorID'";

			$result=query_several($query, $connection);
			$nrows = mysqli_num_rows($result);
			if ($nrows==0) {
				echo "<div class=\"error\"><img src=\"images/exclamation.png\"> There was an error. That sensor ID could not be found. Please go back and try again.</div>";
				}
			else {
				$row = mysqli_fetch_array($result);
				extract($row);
					
				echo "<h3>Edit sensor to the database</h3>";
				
				if ($u==1) {
					echo "<p><div class=\"success\">Sensor data was updated successfully.</div>";
					}				
				
				echo "<form action=\"include/sensor_edit.php\" method=\"POST\" id=\"AddSensors\">
					<input name=\"SensorID\" type=\"hidden\" value=\"$SensorID\">
					<p>Sensor ID: $SensorID
					<p>Recorder: <input type=\"text\" name=\"Recorder\" maxlength=\"100\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" value=\"$Recorder\"><br>
					Microphone: <input type=\"text\" name=\"Microphone\" maxlength=\"80\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" value=\"$Microphone\"><br>
					Notes of the sensor: <input type=\"text\" name=\"Notes\" maxlength=\"255\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all\" value=\"$Notes\"><br>
					<input type=submit value=\" Edit sensor \" class=\"fg-button ui-state-default ui-corner-all\"></form>";

				#Delete div
				echo "<div id=\"sensordel2\" title=\"Delete sensor?\">
				<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>This sensor will be deleted, this action can not be undone! Are you sure?</p></div>";
									
				echo "<form action=\"sensor_del.php\" method=\"GET\" id=\"sensordel1\" name=\"sensordel1\">
					<input name=\"SensorID\" type=\"hidden\" value=\"$SensorID\">
					<input type=submit value=\" Delete sensor \" class=\"fg-button ui-state-default ui-corner-all\">
					</form>";
					
				}

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
