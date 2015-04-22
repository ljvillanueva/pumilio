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

if (!isset($_GET["t"])){
	$t = 0;
	}
else{
	$t = $_GET["t"];
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

<script type="text/javascript">
$(function() {
    // setTimeout() function will be fired after page is loaded
    // it will wait for 5 sec. and then will fire
    // $("#successMessage").hide() function
    setTimeout(function() {
        $("#pumversion").hide('blind', {}, 500)
    }, 5000);
});
</script>

<script type="text/javascript">
$(function() {
    // setTimeout() function will be fired after page is loaded
    // it will wait for 5 sec. and then will fire
    // $("#successMessage").hide() function
    setTimeout(function() {
        $("#pumversiondev").hide('blind', {}, 500)
    }, 8000);
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
						echo "<div class=\"alert alert-danger\" id=\"pumversiondev\">
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

		?>

	<div class="row">
		<div class="col-lg-11">

		<?php
			require("include/adminpumilio.php");

			require("include/kml.php");
		?>

		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Manage users</h3>
		</div>
        <div class="panel-body">
	

			<?php
			if ($u==1) {
				echo "<p><div class=\"success\">User was added successfully</div>";
				}
			if ($u==2) {
				echo "<p><div class=\"error\">That username is already in use, please use another.</div>";
				}
			
			echo "<form action=\"$app_url/include/add_user.php\" method=\"POST\" id=\"AddUserForm\">";
			?>
			<p>Username: <br><input type="text" name="UserName" maxlength="20" class="form-control"><br>
			Full name of the user: <br><input type="text" name="UserFullname" maxlength="100" class="form-control"><br>
			User email address: <br><input type="text" name="UserEmail" maxlength="100" class="form-control"><br>
			User password:<br><input type="password" name="newpassword1" id="newpassword1" maxlength="20" class="form-control" /><br>
			Please retype the password:<br><input type="password" name="newpassword2" id="newpassword2" maxlength="20" class="form-control" /><br>
			User role:<br><select name="UserRole" class="form-control">
				<option value="user">Regular user</option>
				<option value="admin">Administrator</option>
			</select><br>
			<button type="submit" class="btn btn-primary"> Add user </button>
			</form><br><br>
			
			<hr noshade>
			<h4>Manage users</h4>
			<?php

			if ($u==3) {
				echo "<p><div class=\"success\">Change was made successfully</div>";
				}

			$no_users = DB::column('SELECT COUNT(*) FROM `Users` WHERE `UserActive` LIKE 1');
			
			$query = "SELECT * from Users WHERE UserActive='1' ORDER BY UserName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);

			echo "<p>This system has $no_users users:
				<table border=\"0\">";

			for ($i=0; $i<$nrows; $i++) {
				$row = mysqli_fetch_array($result);
				extract($row);
				
				echo "<tr>
					<td><strong>Name</strong></td><td>&nbsp;</td><td><strong>Username</strong></td><td>&nbsp;</td><td><strong>Role</strong></td><td>&nbsp;</td><td><strong>Change password</strong></td>
					</tr><tr>";
				
				echo "<td><form action=\"include/edit_user.php\" method=\"POST\">$UserFullname</td><td>&nbsp;</td><td>$UserName</td><td>&nbsp;</td><td>";
				if ($UserRole == "admin") {
					#$other_admins=query_one("SELECT COUNT(*) FROM Users WHERE UserRole='admin' AND UserID!='$UserID'", $connection);
					$other_admins = DB::column('SELECT COUNT(*) FROM `Users` WHERE  `UserRole`=`admin` AND `UserID`!= ?', $UserID);
					if ($other_admins > 0 && $UserName != $username) {
						echo "<input type=\"hidden\" name=\"ac\" value=\"remadmin\">
						<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
						<input type=submit value=\" Remove from administrators \"></form>";
						}
					else {
						echo "[Administrator]</form>";
						}
					}
				else {
					echo "<input type=\"hidden\" name=\"ac\" value=\"makeadmin\">
					<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
					<input type=submit value=\" Make administrator \"></form>";
					}

				echo "</td><td>&nbsp;</td><td>";
				
				if ($UserName == $username){
					echo "<a href=\"edit_myinfo.php?t=2\" title=\"Edit my information or change password\">Change my password</a>";
					}
				else{
					echo "<form method=\"GET\" action=\"include/edit_user_password.php\" target=\"editpassword\" onsubmit=\"window.open('', 'editpassword', 'width=450,height=400,status=yes,resizable=yes,scrollbars=yes')\">
						<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
						<button type=\"submit\" class=\"btn btn-primary\"> Edit user password </button>
					</form>
					</td></tr>";
					}
				}
			echo "</table>";
			?>
				</ul>
				
			<hr noshade>
			<h4>Set users as inactive</h4>
			<?php

			if ($u==4) {
				echo "<p><div class=\"success\">User was set as inactive successfully</div>";
				}

			#Delete div
			echo "<div id=\"dialog\" title=\"Set user as inactive?\">
			<p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>The user will be set as inactive immediately and will not be able to log in. Are you sure?</p></div>";

			$query = "SELECT * from Users WHERE UserName!='$username' AND UserActive='1' ORDER BY UserName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			if ($nrows==0) {
				echo "There are no other users and you can not set yourself as inactive.";
				}
			else {
				echo "
				<form action=\"include/edit_user.php\" method=\"POST\" id=\"delform\" name=\"delform\">
				<input type=\"hidden\" name=\"ac\" value=\"inactive\">
				<select name=\"UserID\">";

				for ($j=0; $j<$nrows; $j++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					echo "<option value=\"$UserID\">$UserFullname ($UserName)</option>";
					}
				echo "</select>";

				echo " &nbsp;&nbsp;<button type=\"submit\" class=\"btn btn-primary\"> Set user as inactive </button>
				</form>";
				}

			$query = "SELECT * from Users WHERE UserName!='$username' AND UserActive='0' ORDER BY UserName";
			$result = mysqli_query($connection, $query)
				or die (mysqli_error($connection));
			$nrows = mysqli_num_rows($result);
			if ($nrows==0) {
				#echo "There are no other users and you can not set yourself as inactive.";
				}
			else {
				echo "
				<form action=\"include/edit_user.php\" method=\"POST\">
				<input type=\"hidden\" name=\"ac\" value=\"activate\">
				<select name=\"UserID\" class=\"form-control\">";

				for ($j=0; $j<$nrows; $j++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					echo "<option value=\"$UserID\">$UserFullname ($UserName)</option>";
					}
				echo "</select>";

				echo " &nbsp;&nbsp;
				<button type=\"submit\" class=\"btn btn-primary\"> Reset user as active </button>
				</form>";
				}
			?>
			</p>
			</div>
			</div>



			
		
			<?php
				require("include/sensors.php");
			?>
			




		<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title">Export sound files</h3>
		</div>
        <div class="panel-body">
				
				<?php
				#Window to get a menu to export files
				# keeping it separate helps to avoid having to get the dir sizes without need
				echo "<p><form method=\"GET\" action=\"include/exportsounds.php\" target=\"disk\" onsubmit=\"window.open('', 'disk', 'width=650,height=600,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Open selection window </button>
					</form>";
				?>
			</div>
		</div>


			

		
					<?php
						require("include/qf.php");
					?>
			





			<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">Maintenance</h3>
			</div>
	        <div class="panel-body">

				<?php
					echo "<p>Execute maintenance tasks:";

					echo "<p><form method=\"GET\" action=\"admin_generate.php\">
					<button type=\"submit\" class=\"btn btn-primary\"> Generate mp3 and image files </button>
					</form> <br>";
					
					echo "<p><form method=\"GET\" action=\"include/emptytmp.php\" target=\"tmp\" onsubmit=\"window.open('', 'tmp', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Cleanup temp folder </button>
					</form> <br>";
					
					/*
					echo "<p><form method=\"GET\" action=\"include/systemlog.php\" target=\"systemlog\" onsubmit=\"window.open('', 'systemlog', 'width=850,height=620,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" System log \"></form><br><hr noshade>";
					*/

					#Check database values
					echo "<p><form method=\"GET\" action=\"include/checkdb.php\" target=\"checkdb\" onsubmit=\"window.open('', 'checkdb', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Check database for missing data and optimize tables </button>
					</form>  <br>";
					
					#Window to get disk used
					echo "<p><form method=\"GET\" action=\"include/diskused.php\" target=\"disk\" onsubmit=\"window.open('', 'disk', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Check disk usage </button>
					</form> <br>";

					#Delete mp3 or images
					echo "<p><form method=\"GET\" action=\"include/delauxfiles.php\" target=\"delauxfiles\" onsubmit=\"window.open('', 'delauxfiles', 'width=450,height=700,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Delete mp3 and/or images from system </button>
					</form> <br>";

					#Delete collection
					echo "<p><form method=\"GET\" action=\"include/delcol.php\" target=\"delcol\" onsubmit=\"window.open('', 'delcol', 'width=550,height=400,status=yes,resizable=yes,scrollbars=auto')\">
					<button type=\"submit\" class=\"btn btn-primary\"> Delete a collection and all the files </button>

					</form>  <br>";


				?>
			</div></div>

	</div>
	<div class="col-lg-1">&nbsp;</div></div>

<?php
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