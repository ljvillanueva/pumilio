<?php

//Custom name of the app
if ($t==9) {
	if ($u==1) {
		echo "<div class=\"success\">The database was updated.</div>";
		}
	elseif ($u==2) {
		echo "<div class=\"error\">The Quality Flag could not be added. Please try again.</div>";
		}
	elseif ($u==3) {
		echo "<div class=\"notice\">The Quality Flag already exists in the database.</div>";
		}
	}

echo "<div style=\"margin-left: 10px;\">";

	echo "<p><strong><a href=\"qc.php\">Data extraction for quality control</a>
	<p><a href=\"qa.php\">Figures for quality control</a></strong><br><br>";
							
							
$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
$result_qf = mysqli_query($connection, $query_qf)
	or die (mysqli_error($connection));
$nrows_qf = mysqli_num_rows($result_qf);

echo "<p>
	<table border=\"0\">
	<tr>
		<td><strong>Quality Flag</strong></td><td>&nbsp;</td><td><strong>Meaning</strong></td><td>&nbsp;</td><td><strong>Delete (files that have it will be changed to 0)</strong></td>
	</tr>";

	for ($f=0;$f<$nrows_qf;$f++) {
		$row_qf = mysqli_fetch_array($result_qf);
		extract($row_qf);
		echo "	<tr>
		<td>$QualityFlagID</td><td>&nbsp;</td><td>$QualityFlag</td><td>&nbsp;</td><td>";
		if ($QualityFlagID=="0"){
			echo " (default) ";
			}
		else {
			echo "<a href=\"include/delqf.php?QualityFlagID=$QualityFlagID\"><img src=\"images/cross.png\"></a>";
			}
		echo "</td>
		</tr>";
		}

echo "</table>
</div>";

echo "<p><form action=\"include/addqf.php\" method=\"POST\" id=\"AddQF\">Add new Quality Flags:<br>
		Quality Flag Value:<br>
			<input name=\"QualityFlagID\" type=\"text\" maxlength=\"4\" size=\"4\" class=\"fg-button ui-state-default ui-corner-all formedge\"> (Integer or decimal value)<br>
		Quality Flag Meaning:<br>
			<input name=\"QualityFlag\" type=\"text\" maxlength=\"40\" size=\"40\" class=\"fg-button ui-state-default ui-corner-all formedge\"><br>
		<input type=\"submit\" value=\" Add quality flag \"  class=\"fg-button ui-state-default ui-corner-all\" style=\"font-size:12px\">
	</form><br><br>";

if ($u==4) {
	echo "<div class=\"success\">The database was updated.</div>";
	}

echo "Minimum Quality Flag to display to anonymous users: $default_qf
	<br>&nbsp;&nbsp;&nbsp;(useful to hide unchecked data to the public)";

echo "<p><form action=\"include/editqfdefault.php\" method=\"POST\" id=\"EditQFDef\">";

	$query_qf = "SELECT * from QualityFlags ORDER BY QualityFlagID";
	$result_qf = mysqli_query($connection, $query_qf)
		or die (mysqli_error($connection));
	$nrows_qf = mysqli_num_rows($result_qf);

	echo "<select name=\"defaultqf\" class=\"ui-state-default ui-corner-all formedge\">";

		for ($f=0;$f<$nrows_qf;$f++) {
			$row_qf = mysqli_fetch_array($result_qf);
			extract($row_qf);
			if ($QualityFlagID == $default_qf){
				echo "<option value=\"$QualityFlagID\" SELECTED>$QualityFlagID: $QualityFlag</option>\n";
				}
			else {
				echo "<option value=\"$QualityFlagID\">$QualityFlagID: $QualityFlag</option>\n";
				}
			}

	echo "</select><br>
	<input type=submit value=\" Change \" class=\"fg-button ui-state-default ui-corner-all\">
	<br>
	</form>";
	
?>