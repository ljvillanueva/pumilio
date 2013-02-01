<?php

require("../config.php");
require("functions.php");
$SoundID = $_GET['SoundID'];

$img_query="SELECT ImageFile from SoundsImages WHERE SoundID=$SoundID";

#$img=mysqli_query($img_query, $connection);
#$row = mysqli_fetch_array($img);
$content = query_one($img_query, $connection);
header("Content-type: image/png");
echo $content[0];

?>
