<?php
require_once("models/config.php");
if (isWithdrawalDisabled()) {
	echo 'Withdrawals are currently disabled.';
	
}else{
	if(!isUserLoggedIn()) {
		echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
		 
	}
	
	$account = $loggedInUser->display_username;
	$user_id = $loggedInUser->user_id;
	if(!$account) {
		die("invalid account");
	}
		$idtw = mysql_real_escape_string(strip_tags($_GET["id"]));
		$init = @mysql_query("SELECT * FROM Wallets where `Id`='$idtw'");
		if(!$init) {
		echo 'problem with query '.mysql_error();
		}
		$coinfull = mysql_result($init, 0, "Name");
		$coinname = mysql_result($init, 0, "Acronymn");
		if($idtw == 78) {
			$minwithdrawal = .0000002;
		}
		elseif($idtw == 1) {
			$minwithdrawal = .005;
		}
		elseif($idtw == 86) {
			$minwithdrawal = 50;
		}
		elseif($idtw == 91) {
			$minwithdrawal = 50;
		}else{ 
			$minwithdrawal = .01;
		}
		$todo1 = mysql_query("SELECT * FROM balances WHERE `Coin`='$coinname' AND `User_Id`='$user_id'");
		$total = mysql_result($todo1, 0, "Amount");
?>
<link rel="stylesheet" type="text/css" href="assets/css/register.css" />
<h2>Withdraw <?php echo $coinfull; ?></h2>
<hr class="five">
<h3>Available balance: <a class="balance" onclick=""><?php echo  sprintf("%.8f",$total); ?></a></h3>
<hr class="five">
<h4 style="color: red;">Minimum withdrawal for this coin type is <?php echo sprintf("%.8f",$minwithdrawal); ?> Coins.</h4>
<hr class="five">
<table border="0" cellpadding="0" cellspacing="0" id="withdrawform">
	<form name='withdraw' id='withdraw' action='index.php?page=withdraw&id=<?php echo $idtw; ?>' method="POST" autocomplete="off">
		<tr>
			<td><input name="amount" type="text" placeholder="Amount(.5% fee applies)" autocomplete="off" class="field" /></td>
		</tr>
		<tr>
			<td><input name="recipient" type="text" placeholder="Receiving Address" autocomplete="off" class="field" /></td>
		</tr>
		<tr>
			<td><input type="password" name="password" placeholder="Password" autocomplete="off" class="field" /></td>
		</tr>
		<tr>
			<td valign="top"><center><input type="submit" value="withdraw" name="withdraw" class="blues" /></center></td>
		</tr>
	</form>
</table>
<?php
	
	if(isset($_POST["withdraw"]))  {
		$errors = array();
		$userdetails = fetchUserDetails($account);
		$password = trim($_POST["password"]);
		$entered_pass = generateHash($password,$userdetails["Password"]);
		$error = false;
		if($entered_pass != $userdetails["Password"]){
			$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
			$error = true;
		}
		if(!is_numeric($_POST["amount"])) {
			$errors[] = lang("N_A_N");	
			$error = true;
		}
		elseif($_POST["amount"] < $minwithdrawal) {
			$errors[] = lang("FAIL_MINIMUM");	
			$error = true;
		}
		if(($_POST["amount"] == NULL)) {
			$errors[] = lang("INVALID_AMOUNT");
            $error = true;			
		}
		if($_POST["amount"] > $total) {
			$errors[] = lang("INS_FUNDS");	
			$error = true;
		}
		if($error == false){
		
			$to = mysql_real_escape_string(strip_tags($_POST["recipient"]));
			$from = mysql_real_escape_string(strip_tags($user_id));
			$amount = mysql_real_escape_string($_POST["amount"]);
			$minusfee = .9998;
			$amountf = $amount * $minusfee;
			$newamt = $total - $amount;
			$todo2 = mysql_query("UPDATE balances SET `Amount`='$newamt' WHERE `Coin`='$coinname' AND `User_Id`='$user_id'");
			//$todo2a = mysql_query("");
			$todo3 = mysql_query("INSERT INTO Withdraw_Requests (`Amount`,`Address`,`User_Id`,`Wallet_Id`,`Account`,`CoinAcronymn`) VALUES ('$amountf','$to','$from','$idtw','$account','$coinname')");
			?>
			<script type="text/javascript">
			$('#withdrawalform').html('');
			</script>
			<?php
			$todo4 = mysql_query("SELECT * FROM Withdraw_Requests Where `User_Id='$from' and `Wallet_Id`='$idtw'");
			echo '<h3>you now have a pending withdrawal</h3><br/>';
			echo '<table id="page">';
			while($row = mysql_fetch_assoc($todo4)) {
			?>
			<tr>
				<td>
					<?php echo $row["Amount"]; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $row["Address"]; ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo $row["CoinAcronymn"]; ?>
				</td>
			</tr>
			<?php
			
			}
			echo '</table>';
			}
			else
			{
				echo '<ul class="nobullets">';
				foreach($errors as $key => $value) {
				
				echo '<li>'.$value.'</li>';
				
				}
				echo '</ul>';
			}
		}	
	}

?>
