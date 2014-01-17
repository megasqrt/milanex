<center><?php$user_id =  $loggedInUser->user_id;$account = $loggedInUser->display_username;if(!isUserLoggedIn()) {	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';}if(isUserAdmin($id)) {	$sql = @mysql_query("SELECT SUM(Amount) as `Amount`  FROM balances WHERE User_ID='-12' AND `Wallet_ID` = '1'");	echo'<h3>Welcome Admin the current fee earnings are: '.mysql_result($sql,0,"Amount").' BTC</h3>';}echo '<h2>Welcome to your account page <b>'.$account.'</b></h2><h4>**to view a list of your history, click <a href="index.php?page=account_history">here.</a></h4><h3>Force Deposit Check: </h3><form method="POST" action="index.php?Page=account">	<input type="text" class="fieldsmall" name="Transaction_Id" placeholder="Transaction Id"/>	<input type="text" class="fieldsmall" name="Coin" placeholder="Coin (Example: BTC)"/>	<input type="submit" class="miniblues"/></form><hr class="five"><div id="page">
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
}if($_POST["Transaction_Id"] != NULL && $_POST["Coin"] != NULL) {	$tid = mysql_real_escape_string(trim($_POST["Transaction_Id"]));	$coin = mysql_real_escape_string(trim($_POST["Coin"]));	$sql = mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$coin'");	$id = @mysql_result($sql,0,"Id");	$bitcoin = new Wallet($id);	$sql2 = @mysql_query("SELECT * FROM deposits WHERE `Transaction_Id`='$tid' AND `Coin`='$coin'");	$id2 = @mysql_result($sql2,0,"id");	$paid = @mysql_result($sql2,0,"Paid");	$trans = $bitcoin->Client->gettransaction($tid);	if(isset($trans)) {		$account = $trans["details"][0]["account"];		$category = $trans["details"][0]["category"];		$confirms = $trans["confirmations"];		$amount = $trans["amount"];		if($id2 != NULL) {			if($paid == 0)			{				if($category == "receive" && $confirms > 5 && $account != "")				{					mysql_query("UPDATE deposits SET `Paid`='1' WHERE `id`='$id2'");					AddMoney($amount, $account, $coin);				}			}else{				echo "This transaction has already been deposited";			}		}else{			if($category == "receive" && $account != "") {				if($confirms > 5) {					mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$coin','1','$account');");					AddMoney($amount, $account, $coin);				}else{					mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$coin','0','$account');");					echo "Not enough confirms Current:" . $confirms;				}			}		}	}	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=account">';}
?>
</table></div>
</center>

