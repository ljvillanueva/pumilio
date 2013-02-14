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

if ($login_wordpress == TRUE){
	if (is_user_logged_in()==TRUE){
		if (!is_super_admin()) {
			header("Location: error.php?e=admin");
			die();
			}
		}
	}
else {
	#Check if user can edit files (i.e. has admin privileges)
	if (!sessionAuthenticate($connection)) {
		header("Location: error.php?e=admin");
		die();
		}
	
	$username = $_COOKIE["username"];

	if (!is_user_admin2($username, $connection)) {
		die("You are not an admin.");
		}
	}

$u=filter_var($_GET["u"], FILTER_SANITIZE_NUMBER_INT);

echo "
<html>
<head>

<title>$app_custom_name - Administration Area</title>";

#Get CSS
require("include/get_css.php");
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

	
	
<!-- JQuery Tabs -->
<script type="text/javascript">

<?php
						
if ($_GET["t"]==9) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 8 });
		});";
	}
elseif ($_GET["t"]==8) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 7 });
		});";
	}
elseif ($_GET["t"]==7) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 6 });
		});";
	}
elseif ($_GET["t"]==6) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 5 });
		});";
	}
elseif ($_GET["t"]==5) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 4 });
		});";
	}
elseif ($_GET["t"]==4) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 3 });
		});";
	}
elseif ($_GET["t"]==3) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 2 });
		});";
	}
elseif ($_GET["t"]==2) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 1 });
		});";
	}
elseif ($_GET["t"]==1) {
	echo "$(function() {
		$(\"#tabs0\").tabs({ selected: 0 });
		});";
	}
else
	{echo "$(function() {
		$(\"#tabs0\").tabs();
		});";
	}
	
?>

</script>

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

<?php
if ($use_googleanalytics) {
	echo $googleanalytics_code;
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
			<h3>Administration Area</h3>
			<p>Use this page to set up users, permissions, and other general options for the application.

			<p>

			<?php
			#Check last version
			$last_ver=file("http://pumilio.sourceforge.net/cur_ver.txt", FILE_IGNORE_NEW_LINES);
			$last_ver=$last_ver[0];
			$last_ver_e=explode(".",$last_ver);
			#get this version
			require("include/version.php");
			$website_version_e=explode(".",$website_version);
			
			$w_dev = trim($website_version_e[3]);
			if ($w_dev == "dev"){
				echo "<div class=\"error\" id=\"pumversiondev\"><strong><img src=\"images/exclamation.png\"> You are using a development version of Pumilio. This version may be more up to date
					than the current version, but may be unstable.<br>
					This version is recommended ONLY for development and for testing since there can be data corruption or loss.</strong></div>";
				}

			if ($last_ver_e[2]>$website_version_e[2] && $last_ver_e[1] == $website_version_e[1]) {
				$update_message = "<div class=\"notice\" id=\"pumversion\"><strong>A new version of Pumilio is available ($last_ver) which includes bug fixes.<br>You are running version $website_version<br>Visit the <a href=\"http://pumilio.sourceforge.net\">project website</a> for more details.</strong></div>";
				}
			elseif ($last_ver_e[1]>$website_version_e[1] && $last_ver_e[0] == $website_version_e[0]) {
				$update_message = "<div class=\"notice\" id=\"pumversion\"><strong>A new version of Pumilio is available ($last_ver) which includes new or improved features.<br>You are running version $website_version<br>Visit the <a href=\"http://pumilio.sourceforge.net\">project website</a> for more details.</strong></div>";
				}
			elseif ($last_ver_e[0]>$website_version_e[0]) {
				$update_message = "<div class=\"notice\" id=\"pumversion\"><strong>A new major version of Pumilio is available ($last_ver) which includes major improvements.<br>You are running version $website_version<br>Visit the <a href=\"http://pumilio.sourceforge.net\">project website</a> for more details.</strong></div>";
				}
			else {
				$update_message = "<div class=\"success\" id=\"pumversion\"><strong>Pumilio is up to date, you are running version $website_version</strong></div>";
				}

			echo $update_message;
			flush();
		?>

			<div id="tabs0">
				<ul>
					<li><a href="#tabs-1">Settings</a></li>
					<li><a href="#tabs-2">KML</a></li>
					<li><a href="#tabs-3">Users</a></li>
					<li><a href="#tabs-4">Sensors</a></li>
					<li><a href="#tabs-5">Export sound files</a></li>
					<li><a href="#tabs-6">Weather data</a></li>
					<li><a href="#tabs-7">Maintenance</a></li>
					<li><a href="#tabs-8">System log</a></li>
					<li><a href="#tabs-9">Quality Control</a></li>
				</ul>
			<div id="tabs-1">
				<h4>Settings</h4>
				<?php
					require("include/adminpumilio.php");
				?>
			</div>
			<div id="tabs-2">
				<h4>KML</h4>
				<?php
					require("include/kml.php");
				?>
			</div>
			<div id="tabs-3">
			
				<h4>Users</h4>
				<P><strong>Add users</strong>:
				<?php
				if ($u==1) {
					echo "<p><div class=\"success\">User was added successfully</div>";
					}
				if ($u==2) {
					echo "<p><div class=\"error\">That username is already in use, please use another.</div>";
					}
				
				echo "<form action=\"$app_url/include/add_user.php\" method=\"POST\" id=\"AddUserForm\">";
				?>
				<p>Username: <br><input type="text" name="UserName" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge"><br>
				Full name of the user: <br><input type="text" name="UserFullname" maxlength="100" size="60" class="fg-button ui-state-default ui-corner-all formedge"><br>
				User email address: <br><input type="text" name="UserEmail" maxlength="100" size="60" class="fg-button ui-state-default ui-corner-all formedge"><br>
				User password:<br><input type="password" name="newpassword1" id="newpassword1" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge" /><br>
				Please retype the password:<br><input type="password" name="newpassword2" id="newpassword2" maxlength="20" size="20" class="fg-button ui-state-default ui-corner-all formedge" /><br>
				User role:<br><select name="UserRole" class="ui-state-default ui-corner-all formedge">
					<option value="user">Regular user</option>
					<option value="admin">Administrator</option>
				</select><br>
				<input type=submit value=" Add user " class="fg-button ui-state-default ui-corner-all" />
				</form><br><br>
				
				<hr noshade>
				<h4>Manage users</h4>
				<?php

				if ($u==3) {
					echo "<p><div class=\"success\">Change was made successfully</div>";
					}

				$query = "SELECT * from Users WHERE UserActive='1' ORDER BY UserName";
				$result = mysqli_query($connection, $query)
					or die (mysqli_error($connection));
				$nrows = mysqli_num_rows($result);

				echo "<p>This system has $nrows users:
					<table border=\"0\">";

				for ($i=0;$i<$nrows;$i++) {
					$row = mysqli_fetch_array($result);
					extract($row);
					
					echo "<tr>
						<td><strong>Name</strong></td><td>&nbsp;</td><td><strong>Username</strong></td><td>&nbsp;</td><td><strong>Role</strong></td><td>&nbsp;</td><td><strong>Change password</strong></td>
						</tr><tr>";
					
					echo "<td><form action=\"include/edit_user.php\" method=\"POST\">$UserFullname</td><td>&nbsp;</td><td>$UserName</td><td>&nbsp;</td><td>";
					if ($UserRole=="admin") {
						$other_admins=query_one("SELECT COUNT(*) FROM Users WHERE UserRole='admin' AND UserID!='$UserID'", $connection);
						if ($other_admins>0 && $UserName!=$username) {
							echo "<input type=\"hidden\" name=\"ac\" value=\"remadmin\">
							<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
							<input type=submit value=\" Remove from administrators \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
							}
						else {
							echo "[Administrator]</form>";
							}
						}
					else {
						echo "<input type=\"hidden\" name=\"ac\" value=\"makeadmin\">
						<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
						<input type=submit value=\" Make administrator \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
						}

					echo "</td><td>&nbsp;</td><td>";
					
					if ($UserName == $username){
						echo "<a href=\"edit_myinfo.php?t=2\" title=\"Edit my information or change password\">Change my password</a>";
						}
					else{
						echo "<form method=\"GET\" action=\"include/edit_user_password.php\" target=\"editpassword\" onsubmit=\"window.open('', 'editpassword', 'width=450,height=400,status=yes,resizable=yes,scrollbars=yes')\">
							<input type=\"hidden\" name=\"UserID\" value=\"$UserID\">
							<input type=submit value=\" Edit user password \" class=\"fg-button ui-state-default ui-corner-all\">
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
					<select name=\"UserID\" class=\"ui-state-default ui-corner-all\">";

					for ($j=0;$j<$nrows;$j++) {
						$row = mysqli_fetch_array($result);
						extract($row);
						echo "<option value=\"$UserID\">$UserFullname ($UserName)</option>";
						}
					echo "</select>";

					echo " &nbsp;&nbsp;<input type=submit value=\" Set user as inactive \" class=\"fg-button ui-state-default ui-corner-all\">
					</form>";
					}
			?>
					
					
			</div>
			<div id="tabs-4">
				<h4>Sensors</h4>
				<?php
					require("include/sensors.php");
				?>
			</div>
			<div id="tabs-5">
				<h4>Export sound files</h4>
				
				
				<?php
				#Window to get a menu to export files
				# keeping it separate helps to avoid having to get the dir sizes without need
				echo "<p><form method=\"GET\" action=\"include/exportsounds.php\" target=\"disk\" onsubmit=\"window.open('', 'disk', 'width=650,height=600,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Open selection window \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
				?>
				
			</div>
			
			<div id="tabs-6">
				<h4>Weather data</h4>
				<?php
					require("include/weather_sites.php");
				?>
			</div>
			<div id="tabs-7">
				<h4>Maintenance</h4>
				<?php
					echo "<p>Execute maintenance tasks:";

					echo "<p><form method=\"GET\" action=\"admin_generate.php\">
					<input type=submit value=\" Generate mp3 and image files \" class=\"fg-button ui-state-default ui-corner-all\"></form> <br><hr noshade>";

					#Check database values
					echo "<p><form method=\"GET\" action=\"include/checkdb.php\" target=\"checkdb\" onsubmit=\"window.open('', 'checkdb', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Check database for missing data and optimize tables \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
					
					#Delete orphan files
					#FIX CANCEL BUG
					#Fix new organizational search
					/*					
					echo "<p><form method=\"GET\" action=\"include/orphanedfiles.php\" target=\"orphans\" onsubmit=\"window.open('', 'orphans', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Delete orphaned and temporary files \" class=\"fg-button ui-state-default ui-corner-all\"></form>";
					*/

					#Window to get disk used
					echo "<p><form method=\"GET\" action=\"include/diskused.php\" target=\"disk\" onsubmit=\"window.open('', 'disk', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Check disk usage \" class=\"fg-button ui-state-default ui-corner-all\"></form><br><hr noshade>";

					#Delete mp3 or images
					echo "<p><form method=\"GET\" action=\"include/delauxfiles.php\" target=\"delauxfiles\" onsubmit=\"window.open('', 'delauxfiles', 'width=450,height=700,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Delete mp3 and/or images from system \" class=\"fg-button ui-state-default ui-corner-all\"></form><br><hr noshade>";

					#Delete collection
					echo "<p><form method=\"GET\" action=\"include/delcol.php\" target=\"delcol\" onsubmit=\"window.open('', 'delcol', 'width=550,height=400,status=yes,resizable=yes,scrollbars=auto')\">
					<input type=submit value=\" Delete a collection and all the files \" class=\"fg-button ui-state-default ui-corner-all\"></form>";

					
					echo "</ul>";
				?>
			</div>
			
			
			<div id="tabs-8">
				<h4>System log</h4>
					<?php
						require("include/systemlog.php");
					?>
			</div>
			
			<div id="tabs-9">
				<h4>Quality control</h4>
					<?php
						require("include/qf.php");
					?>
			</div>
			
		<br>
		</div>
		<div class="span-24 last">
			<?php
			require("include/bottom.php");
			flush(); @ob_flush();
			#Delete old temp files
			delete_old('tmp/', 3);
			
			?>

		</div>
	</div>

</body>
</html>
