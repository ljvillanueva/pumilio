<?php
	
	#Search advanced
	
	echo "	<div class=\"panel panel-primary\">
				<div class=\"panel-heading\">
					<h3 class=\"panel-title\">Search by file properties</h3>
				</div>
				<div class=\"panel-body\">
				";

			echo "<form action=\"results.php\" method=\"GET\" class=\"form-inline\">
				<label for=\"filename\">Filename &nbsp;&nbsp;</label>
					<input type=\"text\" name=\"filename\" value=\"$filename\" id=\"filename\" class=\"form-control\" placeholder=\"Filename\"><br>


				<label for=\"Col_comparison\">Collection &nbsp;&nbsp;</label>

					<select name=\"Col_comparison\" id=\"Col_comparison\" class=\"form-control\">";
					if ($Col_comparison==2){
						echo "<option value=\"1\"> is </option>
						<option value=\"2\"SELECTED> is not </option>";}
					else{
						echo "<option value=\"1\" SELECTED> is </option>
						<option value=\"2\"> is not </option>";
					}
						
				echo "</select> &nbsp;

				<select name=\"Col\" id=\"Col\" class=\"form-control\">
					<option value=\"0\"></option>";
						
					#Get all dates
					$query_dates = "SELECT ColID, CollectionName FROM Collections ORDER BY CollectionName";
					$result_dates = query_several($query_dates, $connection);
					$nrows_dates = mysqli_num_rows($result_dates);

					if ($nrows_dates > 0) {
						for ($d = 0; $d < $nrows_dates; $d++)	{
							$row_dates = mysqli_fetch_array($result_dates);
							extract($row_dates);
							if ($Col == $ColID){
								echo "\n<option value=\"$ColID\" SELECTED>$CollectionName</option>";
							}
							else{
								echo "\n<option value=\"$ColID\">$CollectionName</option>";
							}

							}
						}
				echo "</select><br>";


			echo "
				<label for=\"startTime\">Time range &nbsp;&nbsp;</label>";
					if ($startTime!=""){
						echo "<input type=\"text\" name=\"startTime\" id=\"startTime\" value=\"$startTime\" class=\"form-control\">";
					}
					else{
						echo "<input type=\"text\" name=\"startTime\" id=\"startTime\" value=\"00:00\" class=\"form-control\">";
					}

				echo "to ";
				if ($endTime!=""){
						echo "<input type=\"text\" name=\"endTime\" id=\"endTime\" value=\"$endTime\" class=\"form-control\">";
					}
					else{
						echo "<input type=\"text\" name=\"endTime\" id=\"endTime\" value=\"23:59\" class=\"form-control\">";
					}

			echo "<br>
				<label for=\"startDate\">Date range &nbsp;&nbsp;</label>
					
					<input type=\"text\" id=\"startDate\" name=\"startDate\" value=\"$DateLow1\" class=\"form-control\" readonly> to 
					<input type=\"text\" id=\"endDate\" name=\"endDate\" value=\"$DateHigh1\" class=\"form-control\" readonly><br>
			

				<label for=\"Site_comparison\">Site &nbsp;&nbsp;</label>
					<select name=\"Site_comparison\" id=\"Site_comparison\" class=\"form-control\">
						<option value=\"1\" SELECTED> is </option>
						<option value=\"2\"> is not </option>
					</select> &nbsp;

				<select name=\"SiteID\" class=\"form-control\">
					<option value=\"0\"></option>";
						
					#Get all dates
					$query_sites = "SELECT SiteID, SiteName FROM Sites ORDER BY SiteName";
					$result_sites = query_several($query_sites, $connection);
					$nrows_sites = mysqli_num_rows($result_sites);

					if ($nrows_sites > 0) {
						for ($s = 0; $s < $nrows_sites; $s++) {
							$row_sites = mysqli_fetch_array($result_sites);
							extract($row_sites);

							$check_site = query_one("SELECT COUNT(*) FROM Sounds WHERE SiteID='$SiteID'", $connection);

							if ($check_site > 0){
								if ($Site == $SiteID){
									echo "\n<option value=\"$SiteID\" SELECTED>$SiteName</option>";
									}
								else{
									echo "\n<option value=\"$SiteID\">$SiteName</option>";
								}
								}
							}
						}
				echo "</select><br>
				
				<label for=\"Tag_comparison\">Tags &nbsp;&nbsp;</label>
				<select name=\"Tag_comparison\" class=\"form-control\">
					<option value=\"1\" SELECTED> include </option>
				</select> &nbsp;

				<select name=\"Tags\" class=\"form-control\">
					<option value=\"0\"></option>";
					
					#Get all dates
					$query_tags = "SELECT Tag FROM Tags GROUP BY Tag ORDER BY Tag";
					$result_tags = query_several($query_tags, $connection);
					$nrows_tags = mysqli_num_rows($result_tags);

					if ($nrows_tags > 0) {
						for ($t = 0; $t < $nrows_tags; $t++) {
							$row_tags = mysqli_fetch_array($result_tags);
							extract($row_tags);

							echo "\n<option value=\"$Tag\">$Tag</option>";
							}
						}
				echo "</select><br><br>
				
				<label for=\"startDuration\">Duration (secs) &nbsp;&nbsp;</label>
				<div style=\"margin: 10 10 10 10;\"><div id=\"durationslider\"></div></div><br>
							

					<input type=\"text\" id=\"startDuration\" name=\"startDuration\" value=\"$DurationLow\" class=\"form-control\" readonly style=\"margin-left:70px;\"> to 
					<input type=\"text\" id=\"endDuration\" name=\"endDuration\" value=\"$DurationHigh\" class=\"form-control\" readonly><br>

				
				<label for=\"Channels_comparison\">Channels &nbsp;&nbsp;</label>
				<select name=\"Channels_comparison\" class=\"form-control\">
					<option value=\"1\" SELECTED> is </option>
					<option value=\"2\"> is not </option>
				</select> &nbsp;
				
				<select name=\"Channels\" class=\"form-control\">
					<option value=\"0\" SELECTED></option>
					<option value=\"1\">1</option>
					<option value=\"2\">2</option>
				</select><br>
				

				<label for=\"SamplingRate_comparison\">Sampling Rate &nbsp;&nbsp;</label>
				
				<select name=\"SamplingRate_comparison\" class=\"form-control\">
					<option value=\"1\" SELECTED> is </option>
					<option value=\"2\"> is not </option>
				</select> &nbsp;

				<select name=\"SamplingRate\" class=\"form-control\">
					<option value=\"0\"></option>";
					
					#Get all dates
					$query_SamplingRate = "SELECT DISTINCT SamplingRate FROM Sounds WHERE SamplingRate IS NOT NULL ORDER BY SamplingRate";
					$result_SamplingRate = query_several($query_SamplingRate, $connection);
					$nrows_SamplingRate = mysqli_num_rows($result_SamplingRate);

					if ($nrows_SamplingRate > 0) {
						for ($d = 0; $d < $nrows_SamplingRate; $d++) {
							$row_SamplingRate = mysqli_fetch_array($result_SamplingRate);
							extract($row_SamplingRate);
							echo "\n<option value=\"$SamplingRate\">$SamplingRate</option>";
							}
						}
				echo "</select> Hz<br>

				<label for=\"Orderby\">Order by &nbsp;&nbsp;</label>

					<select name=\"Orderby\" class=\"form-control\">";
						/*<option value=\"Sounds.SoundID\" SELECTED>original order</option>*/
						echo "<option value=\"Time\">date and time</option>
						<option value=\"Duration\">duration</option>
					</select>

			 		<select name=\"Orderby_dir\" class=\"form-control\">
						<option value=\"ASC\" SELECTED>in ascending order</option>
						<option value=\"DESC\">in descending order</option>
					</select>

				<br><br><button type=\"submit\" class=\"btn btn-lg btn-primary btn-block\"> Search </button>
				</form>

				</div>
			</div>";
?>