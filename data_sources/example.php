<?php
#Sample script to get data from a database with the same SoundID.
# Uncomment below and edit accordingly.

/*
#Connect to a database and display other data associated with this SoundID
$db_host="";
$db_user="";
$db_password="";
$db_database = "";

$conn = @mysqli_connect($db_host, $db_user, $db_password, $db_database);

#If could not connect, display error
if (!$conn) {
	echo "<div class=\"error\">Could not connect to database.</div>";
	die();
	}

$data_query = "SELECT field FROM table WHERE SoundID = '$SoundID'";
$data_result = mysqli_query($conn, $data_query)
	or die (mysqli_error($conn));
$data_nrows = mysqli_num_rows($data_result);

for ($d=0;$d<$data_nrows;$d++)
	{
	$this_row = mysqli_fetch_array($data_result);
	extract($this_row);
	
	}
*/
?>
