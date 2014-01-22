<center><?php//error_reporting(E_ALL);//ini_set('display_errors', 1);require_once ('system/csrfmagic/csrf-magic.php');$user_id = addslashes(strip_tags($loggedInUser->user_id));$account = addslashes(strip_tags($loggedInUser->display_username));if(!isUserLoggedIn()) {	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';}if(isUserAdmin($id)) {	$sql = @mysql_query("SELECT SUM(Amount) as `Amount`  FROM balances WHERE User_ID='-12' AND `Wallet_ID` = '1'");	echo'<h3>Welcome Admin the current fee earnings are: '.mysql_result($sql,0,"Amount").' BTC</h3>';}echo '<h2>Welcome to your account page <b>'.$account.'</b></h2><hr class="five"/>';echo"<ul class='nobullets' style='display:inline-block;'>	<li width: 200px !important; lineheight: 35px;' class='blues'><h3><a style='text-decoration: none;' href='index.php?page=newticket'>Get Support</a></h3></li>	<li width: 200px !important; lineheight: 35px;' class='blues'><h3><a style='text-decoration: none;' href='index.php?page=fchk'>Missing Deposit</a></h3></li>	<li width: 200px !important; lineheight: 35px;' class='blues'><h3><a style='text-decoration: none;' href='index.php?page=account_history'>Account History</a></h3></li></ul><hr class='five' />";?><hr class="five" /><a id="refresh" href="index.php?page=account">Click Here to Refresh.</a><hr class="five" /><?phpecho'<div id="page">
<table id="page">
<tr>
	<th>Currency</th><th>Available</th><th>Pending</th><th>Deposit</th><th>Withdraw</th>
</tr>';
$user_id =  $loggedInUser->user_id;
$sql = mysql_query("SELECT * FROM Wallets WHERE `disabled`='0' ORDER BY `Name` ASC");
while ($row = mysql_fetch_assoc($sql)) {
	$coin = $row["Id"];
	$result = @mysql_query("SELECT SUM(Amount) as `Amount`  FROM balances WHERE User_ID='$user_id' AND `Wallet_ID` = '$coin'");
	if($result == NULL) {
		$amount = 0;
		$pending = 0;
	}else{
		$amount = @mysql_result($result,0,"Amount");		$account = $loggedInUser->display_username;	}
	$account = $loggedInUser->display_username;	$acronymn = $row["Acronymn"];	$sql_pending = mysql_query("SELECT * FROM deposits WHERE `Paid`='0' AND `Account`='$account' AND `Coin`='$acronymn'");	$nums = mysql_num_rows($sql_pending);	$pending = 0;	$market_id = $row["Id"];	for($iz = 0;$iz<$nums; $iz++) {		$pending = $pending + @mysql_result($sql_pending,$iz,"Amount");	}	echo'
	<tr>
		<td><a href="index.php?page=trade&market='.$market_id.'">'.$row["Name"].'</a></td><td class="b1">'.sprintf("%.8f",$amount).'</td>		<td class="b1">'.$pending.'</td>		<td><a href="index.php?page=deposit&id='.$row["Id"].'">Deposit</a></td>		<td><a href="index.php?page=withdraw&id='.$row["Id"].'">Withdraw</a></td>
	</tr>';
}
?>
</table></div>
</center>

