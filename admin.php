<?php
session_start();

require("include/functions.php");

$config_file = 'config.php';

if (file_exists($config_file)) {
	require($config_file);
	}
else {
	header("Location: error.php?e=config");
	die();
	}

require("include/apply_config.php");
$force_admin = TRUE;
require("include/check_admin.php");


#DB
use \DByte\DB;
DB::$c = $pdo;


if (isset($_GET["u"])){
	$u = filter_var($_GET["u"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$u = "";
	}


if (isset($_GET["uu"])){
	$uu = filter_var($_GET["uu"], FILTER_SANITIZE_NUMBER_INT);
	}
else{
	$uu = "";
	}


if (!isset($_GET["tt"])){
	$tt = 0;
	}
else{
	$tt = $_GET["tt"];
	}

if (!isset($_GET["imgset"])){
	$imgset = 0;
	}
else{
	$imgset = $_GET["imgset"];
	}

echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
<title>$app_custom_name - Administration Area</title>";

#Get CSS
require("include/get_css3.php");
require("include/get_jqueryui.php");
?>

<!-- Validation Script -->
<script src="js/jquery.validate.js"></script>

<!-- Form validation from http://bassistance.de/jquery-plugins/jquery-plugin-validation/ -->

	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddUserForm").validate({
			rules: {
				UserName: {
					required: true
				},
				UserFullname: {
					required: true
				},
				UserEmail: {
					required: true,
					email: true
				},
				newpassword1: {
					required: true,
					minlength: 5
				},
				newpassword2: {
					required: true,
					minlength: 5,
					equalTo: "#newpassword1"
					}
			},
			messages: {
				UserName: "Please enter a username for this user",
				UserFullname: "Please enter the full name of this user",
				UserEmail: "Please enter a valid email of this user"
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

	<!-- #KML Add validation -->
	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddKML").validate({
			rules: {
				KmlName: {
					required: true
				},
				KmlURL: {
					required: true,
					url: true
				}
			},
			messages: {
				KmlName: "Please enter a name to display for this layer",
				KmlURL: "Please enter the full URL of this layer"
			}
			});
		});
	</script>
	<style type="text/css">
	#AddKML label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>
	
	
	<!-- #QF Add validation -->
	<script type="text/javascript">
	$().ready(function() {
		// validate signup form on keyup and submit
		$("#AddQF").validate({
			rules: {
				QualityFlagID: {
					required: true
				},
				QualityFlag: {
					required: true
				}
			},
			messages: {
				QualityFlagID: "Please enter a value",
				QualityFlag: "Please enter the meaning of this flag"
			}
			});
		});
	</script>
	<style type="text/css">
	#AddQF label.error {
		margin-left: 10px;
		width: auto;
		display: inline;
	}
	</style>

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
		                "Set as inactive": function() {
		                    document.delform.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#delform').submit(function(){
		            $("p#dialog-email").html($("input#UserID").val());
		            $('#dialog').dialog('open');
		            return false;
		        });
		});

	</script>


<!-- JQuery Confirmation -->
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
		                "Delete data": function() {
		                    document.delform2.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialog("close");
		                }
		            }
		        });

		        $('form#delform2').submit(function(){
		            $("p#dialog-email").html($("input#ColID").val());
		            $('#dialog2').dialog('open');
		            return false;
		        });
		});

	</script>


<script type="text/javascript">
	$(function() {
		$("#dialogdata").dialog({
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
		                "Delete data": function() {
		                    document.del_weatherdata.submit();
		                },
		                "Cancel": function() {
		                    $(this).dialogdata("close");
		                }
		            }
		        });

		        $('form#del_weatherdata').submit(function(){
		            $("p#dialog-email").html($("input#ColID").val());
		            $('#dialogdata').dialog('open');
		            return false;
		        });
		});

	</script>
	

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

	

<!-- Hide success messages -->
<script type="text/javascript">
$(function() {
    // setTimeout() function will be fired after page is loaded
    // it will wait for 5 sec. and then will fire
    // $("#successMessage").hide() function
    setTimeout(function() {
        $("#tt1").hide('blind', {}, 500)
    }, 5000);
});
</script>

<script type="text/javascript">
$(function() {
    // setTimeout() function will be fired after page is loaded
    // it will wait for 5 sec. and then will fire
    // $("#successMessage").hide() function
    setTimeout(function() {
        $("#tt2").hide('blind', {}, 500)
    }, 5000);
});
</script>


<!-- Tooltips-->
<script>
	$(function() {
		$( document ).tooltip();
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


			<h2>Administration Area</h2>
				<p>Use this page to set up users, permissions, and other general options for the application.</p>


				<?php
				#Check last version
				$last_ver=file("http://ljvillanueva.github.io/pumilio/cur_ver.txt", FILE_IGNORE_NEW_LINES);
				$last_ver=$last_ver[0];
				$last_ver_e=explode(".",$last_ver);
				#get this version
				require("include/version.php");
				$website_version_e=explode(".",$website_version);
				
				if (count($website_version_e) == 4){
					$w_dev = trim($website_version_e[3]);
					if ($w_dev == "dev"){
						echo "<div class=\"alert alert-danger\">
							<span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
							You are using a development version of Pumilio. This version may be more up to date
							than the current version, but may be unstable.<br>
							This version is recommended ONLY for development and for testing since there can be data corruption or loss.</div>";
						}
					}

				if ($last_ver_e[2]>$website_version_e[2] && $last_ver_e[1] == $website_version_e[1]) {
					$update_message = "<div class=\"alert alert-warning\" id=\"pumversion\">A new version of Pumilio is available ($last_ver) which includes bug fixes.<br>You are running version $website_version<br>Visit the <a href=\"http://pumilio.sourceforge.net\">project website</a> for more details.</div>";
					}
				elseif ($last_ver_e[1]>$website_version_e[1] && $last_ver_e[0] == $website_version_e[0]) {
					$update_message = "<div class=\"alert alert-warning\" id=\"pumversion\">A new version of Pumilio is available ($last_ver) which includes new or improved features.<br>You are running version $website_version<br>Visit the <a href=\"http://pumilio.sourceforge.net\">project website</a> for more details.</div>";
					}
				elseif ($last_ver_e[0]>$website_version_e[0]) {
					$update_message = "<div class=\"alert alert-warning\" id=\"pumversion\">A new major version of Pumilio is available ($last_ver) which includes major improvements.<br>You are running version $website_version<br>Visit the <a href=\"http://pumilio.sourceforge.net\">project website</a> for more details.</div>";
					}
				else {
					$update_message = "<div class=\"alert alert-success\" id=\"pumversion\">Pumilio is up to date, you are running version $website_version</div>";
					}

				echo $update_message;
				flush();




require("include/adminpumilio.php");



require("include/bottom.php");
?>

</body>
</html>
<?php
#Close session to release script from php session
	session_write_close();
	flush(); @ob_flush();
	#Delete old temp files
	delete_old('tmp/', 3);
?>