<?php
header("Content-type: text/plain");
$price = $_GET["P"] + $_GET["X"];
$price2 = 0;//($price*0.002);
echo sprintf("%2.8f", $price2);
?>
