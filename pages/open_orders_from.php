<?php
include("../models/config.php");
$id     = mysql_real_escape_string($_GET["market"]);
$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
$name   = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
$feecost= mysql_real_escape_string(mysql_result($result, 0, "Fee"));
$type = $name;
?>
<div class="top">
<center>Sell Orders <p style="font-size: 15px; display: inline;">(Click To Fill)</p></center>
</div>
<div class="box" id="page">
<table id="page"style="width: 100%;">
<thead>
<tr>
    <th style="width: 33%;">Price</th>
    <th style="width: 33%;">Quantity</th>
	<th style="width: 33%;">Total</th>
</tr>
<?php
$sql = mysql_query("SELECT * FROM trades WHERE `Type`='$type' ORDER BY `Value` ASC");
$previous = "";
$amount = 0;
$g = 0;
while ($row = mysql_fetch_assoc($sql)) {
$g++;
if($g & 1) {
	$color = "lightgray";
} else {
	$color = "darkgray";
}
if($row["To"] == "MLC") { 
if(sprintf("%.8f",$row["Value"]) != $previous)
{
$value = $row["Value"];
$amount = 0;
$sqls = mysql_query("SELECT * FROM trades WHERE `Value`='$value' AND `From`='$type' AND `Type`='$type'");
$num = mysql_num_rows($sqls);
while ($rows = mysql_fetch_assoc($sqls))
{
	$amount += $rows["Amount"];
}
if ($amount >=1) { $amount2 = round($amount);}else{$amount2 = $amount;}

if($amount < .001 && $row["Value"] < 1){
}else{
$totala=$amount * $value * (1 - $feecost);
$totalb=$amount * $value * (1 + $feecost);
?>
<tr style="cursor: pointer;" title="Click To Fill Order Form" class="<?php echo $color; ?>" onclick="document.getElementById('Amount2').value = '<?php echo $totalb; ?>'; document.getElementById('price2').value = '<?php echo $value; ?>'; calculateFees2(this);">
	<td style="width: 33%;"><?php echo $row["Value"];?></td>
    <td style="width: 33%;"><?php echo $amount2;?></td>
	<td style="width: 33%;"><?php echo $totala;?></td>
</tr>
<?php
}
$previous = sprintf("%.8f",$row["Value"]);
}
//$amount = 0;
}
}
?>
</thead>
</table>
</div>
