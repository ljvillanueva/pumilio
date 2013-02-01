<?php
$ColID=filter_var($_GET["ColID"], FILTER_SANITIZE_NUMBER_INT);
header("Location: edit_collection.php?ColID=$ColID");
die;
?>
