<?php
require_once("models/config.php");
require_once('system/csrfmagic/csrf-magic.php');

	if(isset($_GET["confirm"])) {
		$crt       = @mysql_real_escape_string($_GET["confirm"]);
		$sql       = @mysql_query("SELECT * FROM Withdraw_Requests WHERE `Confirmation` = '$crt' AND `User_ID`='$id'");
		$crt2      = @mysql_result($sql,0,"Confirmation");
		$id2       = @mysql_result($sql,0,"Id");
		$confirmed = @mysql_result($sql,0,"Confirmed");
		if($confirmed != null) {
			if($confirmed == "0") {
				mysql_query("UPDATE `Withdraw_Requests` SET `Confirmed`='1' WHERE `id`='$id2'");
				echo "Your withdraw has been confirmed; however, an admin must still process this before your withdraw is processed.";
			}else{
				echo "This Withdraw Request Has Already Been Confirmed Or Denied";
			}
		}else{
			echo "Invalid Confirmation Key";
		}
	}
	if(isset($_GET["deny"])) {
		$crt = @mysql_real_escape_string($_GET["deny"]);
		$sql = @mysql_query("SELECT * FROM Withdraw_Requests WHERE `Confirmation` = '$crt' AND `User_ID`='$id'");
		$crt2 = @mysql_result($sql,0,"Confirmation");
		$id2 = @mysql_result($sql,0,"id");
		$confirmed = @mysql_result($sql,0,"Confirmed");
		if($confirmed != null) {
			if($confirmed == "0") {
				mysql_query("UPDATE `Withdraw_Requests` SET `Confirmed`='-1' WHERE `id`='$id2'");
				echo "Your withdraw has been canceled; however, an admin must still process this before your funds are received back";
			}else{
				echo "This Withdraw Request Has Already Been Confirmed Or Denied";
			}
		}else{
			echo "Invalid Confirmation Key";
		}
	}
	function generateRandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, strlen($characters) - 1)];
		}
		return $randomString;
	}
	if (isWithdrawalDisabled()) {
		echo 'Withdrawals are currently disabled see our <a href="https://twitter.com/milancoin">Twitter Account</a> for details.';
	}else{
		if(!isUserLoggedIn()) {
			echo 'invalid request : not logged in';
		}else{
			$account = $loggedInUser->display_username;
			$user_id = $loggedInUser->user_id;
			$ip      = getIP();
			$idtw    = mysql_real_escape_string(strip_tags((int)$_GET["id"]));
			$init = mysql_query("SELECT * FROM Wallets where `Id`='$idtw'");
			if(mysql_num_rows($init) > 0) {
				$maxwithdrawal = 10000000;
				$coinfull = mysql_result($init, 0, "Name");
				$coin = mysql_result($init, 0, "Acronymn");
				$feecost = mysql_result($init, 0,"Fee");
				$minwithdrawal = mysql_result($init, 0,"Minimum_Withdrawal");
				$total = $loggedInUser->getbalance($idtw);
				$token = getToken($user_id,$ip);
				?>
				<link rel="stylesheet" type="text/css" href="assets/css/register.css" />
				<script>
					function fillwithdraw() {
						$("#Amount").val(<?php echo sprintf("%.8f",$total); ?>);
					}
				</script>
				<h4 style="color: red;">Notice: Gmail users may not receive confirmation email. While we are investigating the problem,.</br> 
				in the meantime please email ceo@milancoin.org with your account name and subject "I confirm my withdrawal".</br>
				Make sure you send the email from the email registered to your account so we can verify your identity. </br>
				Thaks, MilanCoin.org
				</h4>
				<hr class="five">
				<h2>Withdraw <?php echo $coinfull; ?></h2>
				<hr class="five">
				<h3>Available balance: <span class="balance" onclick="fillwithdraw();" style="cursor:pointer;"><u><?php echo  sprintf("%.8f",$total); ?></u></span></h3>
				<hr class="five">
				<h4 style="color: red;">
				Minimum withdrawal(Min + Fee) :<?php echo sprintf("%.8f",$minwithdrawal + $feecost); ?></br>
				</h4>
				<hr class="five">
				<table border="0" cellpadding="0" cellspacing="0" id="withdrawform">
					<form name='withdraw' action='index.php?page=withdraw&id=<?php echo $idtw; ?>' method="POST" autocomplete="off">
						<input type="hidden" name="token" value="<?php echo $token;?>"/>
						<tr>
							<td><input id="Amount" name="amount" type="text" placeholder="Amount(.5% fee applies)" value="" class="field" /></td>
						</tr>
						<tr>
							<td><input name="recipient" type="text" placeholder="Receiving Address" value="" class="field" /></td>
						</tr>
						<tr>
							<td><input type="password" name="password" placeholder="Password" value="" class="field" /></td>
						</tr>
						<tr>
							<td valign="top"><center><input type="submit" value="withdraw" name="withdraw" class="blues" /></center></td>
						</tr>
					</form>
				</table>
				<?php
			
				if(isset($_POST["withdraw"]))  {
					if($_SESSION["Withdraw_Attempts"] > 2) {
						$account = mysql_real_escape_string(strip_tags($loggedInUser->display_username));
						$uagent = mysql_real_escape_string(getuseragent()); //get user agent
						$ip = mysql_real_escape_string(getIP()); //get user ip
						$date = mysql_real_escape_string(gettime());
						$sql = @mysql_query("INSERT INTO access_violations (username, ip, user_agent, time) VALUES ('$account', '$ip', '$uagent', '$date');");
						$captcha = md5($_POST["captcha"]);
						
						if ($captcha != $_SESSION['captcha']) {
							$errors[] = lang("CAPTCHA_FAIL");
						}
					}
					if($_SESSION["Withdraw_Attempts"] > 2) {
						echo 
						'
						<tr>
							<td>
								<center><img src="pages/docs/captcha.php" class="captcha"></center>
							</td>
						</tr>
						<tr>
							<td>
								<input name="captcha" type="text" placeholder="Enter Security Code" class="field">
							</td>
						</tr>
						';
					}
					if($_SESSION["Withdraw_Attempts"] > 3) {
						$ip_address = mysql_real_escape_string(getIP());
						$date2 = mysql_real_escape_string(gettime());
						mysql_query("INSERT INTO bantables_ip (ip, date) VALUES ( '$ip_address', '$date2');");	
					}
					$errors = array();
					$successes = array();
					$userdetails = fetchUserDetails($account);
					$password = trim($_POST["password"]);
					$entered_pass = generateHash($password,$userdetails["Password"]);
					$error = false;
					$success = false;
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
							if($_POST["amount"] <= $minwithdrawal + $feecost) {
								$errors[] = "The minimum allotted withdrawal for ".$coinfull." is ".sprintf("%.8f",$minwithdrawal + $feecost)." coins";	
								$error = true;
							}
							if($_POST["amount"] > $maxwithdrawal) {
								$errors[] = "The maximum allotted withdrawal for ".$coinfull." is ".$maxwithdrawal." coins";	
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
								$success = true;
								$to = mysql_real_escape_string(strip_tags($_POST["recipient"]));
								$from = mysql_real_escape_string(strip_tags($user_id));
								$amount = mysql_real_escape_string($_POST["amount"]);
								$amountf = $amount - $feecost;
								$total2 = $loggedInUser->getbalance($idtw);
								if($total != $total2){
									echo '<meta http-equiv="refresh" content="0; URL=index.php?page=withdraw&id='.$idtw.'">';
									die();
								}
								$newamt = $total2 - $amount;
								$ckey = generateRandomString(30);
								$userdetails = fetchUserDetails($account);
								$mail = new userCakeMail();
								$confirm_url = lang("CONFIRM")."\nhttp://www.milancoin.org/milanex/index.php?page=withdraw&confirm=".$ckey;
								$deny_url = ("DENY")."\nhttp://www.milancoin.org/milanex/index.php?page=withdraw&deny=".$ckey;
								$hooks = array(
									"searchStrs" => array("#CONFIRM-URL#","#DENY-URL#","#USERNAME#","#AMOUNT#","#COIN#"),
									"subjectStrs" => array($confirm_url,$deny_url,$userdetails["Username"],$amountf,$coin)
								);
								if(!$mail->newTemplateMsg("confirm-withdrawl-request.txt",$hooks))
								{
									$errors[] = lang("MAIL_TEMPLATE_BUILD_ERROR");
								}else{
									if(!$mail->sendMail($userdetails["Email"],"Confirm Your Withdrawal"))
									{
										die("Unable to send verifaction email! Please check your email on your account settings and try again!");
									}else{
										echo "A confirmation email has been sent to you!";
									}
								}
								$todo2 = mysql_query("UPDATE balances SET `Amount`='$newamt' WHERE `Wallet_ID`='$idtw' AND `User_Id`='$user_id'");
								$todo3 = mysql_query("INSERT INTO Withdraw_Requests (`Amount`,`Address`,`User_Id`,`Wallet_Id`,`Account`,`CoinAcronymn`,`Confirmation`) VALUES ('$amountf','$to','$from','$idtw','$account','$coin','$ckey')");
								$successes[] = 'you now have a pending withdrawal';
								successBlock($successes);
							}else{
								if(!isset($_SESSION["Withdraw_Attempts"]))
								{
									$_SESSION["Withdraw_Attempts"] = 1;
								}else{
									$_SESSION["Withdraw_Attempts"]++;
								}
								errorBlock($errors);
							}	
							}
						}else{
							echo "invalid token";
							die();
						}
					}	
				
			}
		}
	}
			
?>
