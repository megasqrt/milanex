<?php
header("Content-type: text/plain");
$price = $_GET["P"];
$price2 = $price;// - ($price*0.002);
echo sprintf("%.8f", $price2);
?>
