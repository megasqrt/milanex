<?php
header("Content-type: text/plain");
$price = $_GET["P"];
echo sprintf("%.8f", $price);
?>