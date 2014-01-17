<?php
require_once("models/config.php");
if (isWithdrawalDisabled()) {
	echo 'Withdrawals are currently disabled see our <a href="https://twitter.com/_OpenEx_">Twitter Account</a> for details.';
}else{
	if(!isUserLoggedIn()) {
		echo 'invalid request : not logged in';
	}else{
		$account = $loggedInUser->display_username;
		$user_id = $loggedInUser->user_id;
		$ip = getIP();
		$idtw = mysql_real_escape_string(strip_tags($_GET["id"]));
		$init = mysql_query("SELECT * FROM Wallets where `Id`='$idtw'");
		if(mysql_num_rows($init) > 0) {
			$coinfull = mysql_result($init, 0, "Name");
			$coin = mysql_result($init, 0, "Acronymn");
			if($idtw == 78) { $minwithdrawal = .00000001; }
			elseif($idtw == 1) { $minwithdrawal = .001; }
			elseif($idtw == 86) { $minwithdrawal = 1; }
			elseif($idtw == 91) { $minwithdrawal = 1;}
			else { $minwithdrawal = .001; }
			$todo1 = mysql_query("SELECT * FROM balances WHERE `Wallet_ID`='$idtw' AND `User_Id`='$user_id'");
			$total = mysql_result($todo1, 0, "Amount");
			$token = getToken($user_id,$ip);
		?>
		<link rel="stylesheet" type="text/css" href="assets/css/register.css" />
		<script>
			function fillwithdraw() {
				$("#Amount").val(<?php echo sprintf("%.8f",$total); ?>);
			}
		</script>
		<h3 color="red"> we have limited supply of BLC, 42, and SKC. if you keep getting rejected withdraw</br>
		withdraw a smaller amount. we will have to owe you the difference.</br> we apologize in advance. </br>
		this was separate than the hack, it was due to the double coin on cancel bug the site had a few days ago</br>
		</h3>
		<hr class="five">
		<h2>Withdraw <?php echo $coinfull; ?></h2>
		<hr class="five">
		<h3>Available balance: <span class="balance" onclick="fillwithdraw();" style="cursor:pointer;"><u><?php echo  sprintf("%.8f",$total); ?></u></span></h3>
		<hr class="five">
		<h4 style="color: red;">
		Minimum withdrawal :<?php echo sprintf("%.8f",$minwithdrawal); ?></br>
		</h4>
		<hr class="five">
		<table border="0" cellpadding="0" cellspacing="0" id="withdrawform">
			<form name='withdraw' action='index.php?page=withdraw&id=<?php echo $idtw; ?>' method="POST" autocomplete="off">
				<input type="hidden" name="token" value="<?php echo $token;?>"/>
				<tr>
					<td><input id="Amount" name="amount" type="text" placeholder="Amount(.5% fee applies)" autocomplete="off" class="field" /></td>
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
				$password = mysql_real_escape_string(trim($_POST["password"]));
				$entered_pass = generateHash($password,$userdetails["Password"]);
				$error = false;
				$postedToken = filter_input(INPUT_POST, 'token');
				if(!empty($postedToken)){
					if(isTokenValid($postedToken)){
						if($entered_pass != $userdetails["Password"]){
							$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
							$error = true;
						}
						if(!is_numeric($_POST["amount"])) {
							$errors[] = lang("N_A_N");	
							$error = true;
						}
						if($_POST["amount"] < $minwithdrawal) {
							$errors[] = "The minimum allotted withdrawal for ".$coinfull." is ".$minwithdrawal." coins";	
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
							$todo2 = mysql_query("SELECT * FROM balances WHERE `Wallet_ID`='$idtw' AND `User_Id`='$user_id'");
							$total2 = mysql_result($todo2, 0, "Amount");
							if($total != $total2){
								echo '<meta http-equiv="refresh" content="0; URL=index.php?page=withdraw&id='.$idtw.'">';
								die();
							}
							$newamt = $total2 - $amount;
							$todo2 = mysql_query("UPDATE balances SET `Amount`='$newamt' WHERE `Wallet_ID`='$idtw' AND `User_Id`='$user_id'");
							$todo3 = mysql_query("INSERT INTO Withdraw_Requests (`Amount`,`Address`,`User_Id`,`Wallet_Id`,`Account`,`CoinAcronymn`) VALUES ('$amountf','$to','$from','$idtw','$account','$coin')");
							echo '<h3>you now have a pending withdrawal</h3><br/>';
						}else{
							echo '<ul class="nobullets">';
							foreach($errors as $key => $value) {
								echo '<li>'.$value.'</li>';
							}
							echo '</ul>';
						}	
					}
				  }else{
					echo "invalid token";
					die();
				  }
				}
				
		}else{
			echo 'invalid request : coin does not exist';
		}
	}
}
?>