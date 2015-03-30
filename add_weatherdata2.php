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
$force_admin = TRUE;
require("include/check_admin.php");

$WeatherSiteID=filter_var($_POST["WeatherSiteID"], FILTER_SANITIZE_NUMBER_INT);

$commadata=$_POST["commadata"];

echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
<html>
<head>
<title>$app_custom_name - Add weather data</title>";

require("include/get_css.php");
require("include/get_jqueryui.php");

if ($use_googleanalytics){
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
			&nbsp;
		</div>
		<div class="span-24 last">
			<?php

			echo "<h3>Add weather data</h3>";

			$commadata=explode("\n", $commadata);
			$commadata_count=count($commadata);

			$file_errors=0;

			for ($i=0;$i<$commadata_count;$i++){
				$this_row=$commadata[$i];
				$this_row1=explode("," , $this_row);
				$j=$i+1;
				#Check that the number of fields match
				$this_row_counter=count($this_row1);
				if ($this_row_counter!=10){
					echo "<div class=\"error\">The number of fields ($this_row_counter) in line $j does not match the number of fields to import (10). Empty fields can be designated as \"NULL\". Please go back and try again.</div><br><br>";
					die();
					}
				}

			### All checks passed
			$success_counter=0;
			for ($k=0;$k<$commadata_count;$k++){
				$this_row=$commadata[$k];
				$this_row=filter_var($this_row, FILTER_SANITIZE_STRING);
				
				$this_row_imploded="";
				$this_row_exploded=explode(",", $this_row);
				$this_file=$this_row_exploded[0];
					for ($t=0;$t<1;$t++){
						$this_value=trim($this_row_exploded[$t]);
						if ($this_value=="NULL"||$this_value==""){
							$this_row_imploded="NULL";
							}
						else{
							$this_row_imploded="'$this_value'";
							}
						}
					for ($t=1;$t<count($this_row_exploded);$t++){
						$this_value=trim($this_row_exploded[$t]);
						if ($this_value=="NULL"||$this_value==""){
							$this_row_imploded=$this_row_imploded . ",NULL";
							}
						else{
							$this_row_imploded=$this_row_imploded . ",'$this_value'";
							}
						}

				#Insert to MySQL
				$query_to_insert="INSERT INTO WeatherData (WeatherSiteID, WeatherDate, WeatherTime, Temperature, Precipitation, RelativeHumidity, DewPoint, WindSpeed, WindDirection, LightIntensity, BarometricPressure) VALUES ('$WeatherSiteID',$this_row_imploded);";
				$result = mysqli_query($connection, $query_to_insert)
					or die (mysqli_error($connection));
				$success_counter+=1;
				}

			echo "<br><div class=\"success\">$success_counter data points were addedd successfully to the database.</div>
				<p><a href=\"admin.php?t=6\">Return to the admin page</a>.";
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
