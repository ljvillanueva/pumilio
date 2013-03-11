<?php

require("../config.php");
require("functions.php");
$SoundID = $_GET['SoundID'];

$img_query="SELECT ImageFile from SoundsImages WHERE SoundID=$SoundID";

$content = query_one($img_query, $connection);
header("Content-type: image/png");
echo $content[0];

?>
