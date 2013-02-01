<?php
/*
Save frequency range
*/

$current_address=$_SERVER["REQUEST_URI"];

	echo " <form style=\"float: inherit;\" method=\"POST\" action=\"tools/add_range/add.php\" target=\"add\" onsubmit=\"window.open('', 'add', 'width=450,height=300,status=yes,resizable=yes,scrollbars=auto')\">";
echo "	<input type=\"hidden\" id=\"y_2\" name=\"f_min\" value=\"\" />
	<input type=\"hidden\" id=\"y2_2\" name=\"f_max\" value=\"\" />
	<input src=\"images/database_add.png\" type=\"image\" onmouseover=\"Tip('Insert selection to database', FONTCOLOR, '#fff',BGCOLOR, '#4aa0e0', FADEIN, '400', FADEOUT, '400', ABOVE, 'true', CENTERMOUSE, 'true')\" onmouseout=\"UnTip()\">
	</form> &nbsp;";



echo "&nbsp;";

?>
