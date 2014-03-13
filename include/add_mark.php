<?php
/*
Insert region to database
*/

$current_address=$_SERVER["REQUEST_URI"];

$add_id=mt_rand(1,100000);


	echo " <form style=\"float: inherit;\" method=\"POST\" action=\"include/add_mark1.php\" target=\"add$add_id\" onsubmit=\"window.open('', 'add$add_id', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\" id=\"addform\">";
echo "<input type=\"hidden\" id=\"x_2\" name=\"t_min\" value=\"\" />
	<input type=\"hidden\" id=\"x2_2\" name=\"t_max\" value=\"\" />
	<input type=\"hidden\" id=\"y_2\" name=\"f_min\" value=\"\" />
	<input type=\"hidden\" id=\"y2_2\" name=\"f_max\" value=\"\" />
	<input type=\"hidden\" name=\"ch\" value=\"$ch\" />
	<input type=\"hidden\" name=\"SoundID\" value=\"$SoundID\" />
	<a href=\"#\" title=\"Save selected region to the database\" onclick=\"$('#addform').submit();\">Save selection to database</a>
	<input src=\"images/database_add.png\" type=\"image\" title=\"Save selected region to the database\"> 
	</form> &nbsp;";


$current_address1=explode("pumilio.php?", $current_address);
$current_address2=explode("&", $current_address1[1]);
#$link="pumilio.php?tool=add_to_db.php";
$link="pumilio.php?";

for ($b=0;$b<count($current_address2);$b++){
	if (($current_address2[$b]!="showmarks=1") && ($current_address2[$b]!="tool=add_to_db.php")){
		$link=$link . "&" . $current_address2[$b];
		}
	}

$str = 'This is still a test.';
if ($link[strlen($link)-1]=="&"){
	$link_marks=$link . "showmarks=1";
	}
else {
	$link_marks=$link . "&showmarks=1";
	}


if (isset($_GET["showmarks"])){
	$showmarks = $_GET["showmarks"];
	}
else{
	$showmarks = FALSE;
	}

if ($showmarks) {
	echo "<a href=\"$link\" title=\"Hide marked regions\">Hide marked regions<img src=\"images/database.png\"></a> &nbsp;";
	}
else {
	echo "<a href=\"$link_marks\" title=\"Show marked regions\">Show marked regions<img src=\"images/database_refresh.png\"></a> &nbsp;";
	}

#Window to manage marks
if ($showmarks){
	echo "[<a href=\"managemarks.php?Token=$Token\" target=\"managemarks$add_id\" onclick=\"window.open('managemarks.php?SoundID=$SoundID', 'managemarks$add_id', 'width=600,height=500,status=yes,resizable=yes,scrollbars=auto')\" title=\"Open window to manage the marked regions\">Manage marks</a>]";
	}
						
echo "&nbsp;";

?>
