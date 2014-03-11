<style type="text/css">#chat_toggle {	visibility: hidden;	display: none;}</style><?php
require_once ('system/csrfmagic/csrf-magic.php');
require_once("models/config.php");
if (!isset($_SESSION["Login_Attempts"])){$_SESSION["Login_Attempts"]=0;}
if(isUserLoggedIn()) {	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=account">';	die(); }if(isLoginDisabled()) {    echo "Logins are currently disabled.";}else{
	if(!empty($_POST)) {		$agent = browsercheck();
		if($_SESSION["Login_Attempts"] > 2)		{			$account = mysql_real_escape_string(strip_tags($loggedInUser->display_username));			$uagent = mysql_real_escape_string(getuseragent()); //get user agent			$ip = mysql_real_escape_string(getIP()); //get user ip			$account = mysql_real_escape_string("Guest/Not Logged In");			$date = mysql_real_escape_string(gettime());			$sql = @mysql_query("INSERT INTO access_violations (username, ip, user_agent, time) VALUES ('$account', '$ip', '$uagent', '$date');");			$captcha = md5($_POST["captcha"]);						if ($captcha != $_SESSION['captcha'])			{				$errors[] = lang("CAPTCHA_FAIL");			}					}		if($_SESSION["Login_Attempts"] > 3) {						$ip_address = mysql_real_escape_string(getIP());						$date2 = mysql_real_escape_string(gettime());						mysql_query("INSERT INTO bantables_ip (ip, date) VALUES ( '$ip_address', '$date2');");			}
			$errors = array();
			$username = trim($_POST["username"]);
			$password = trim($_POST["password"]);
			if($username == "") {
				$errors[] = lang("ACCOUNT_SPECIFY_USERNAME");
			}
			if($password == "") {
				$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
			}
			if(count($errors) == 0)
			{

				if(!usernameExists($username))
				{
					$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
				}
				else
				{
					$userdetails = fetchUserDetails($username);
				
					if($userdetails["Banned"]==1)
					{
						$errors[] = "<p class='notify-red'>This account is banned!</p>";
					}
					else
					{
						$entered_pass = generateHash($password,$userdetails["Password"]);

						if($entered_pass != $userdetails["Password"])
						{
							$errors[] = lang("ACCOUNT_USER_OR_PASS_INVALID");
						}
						else
						{
							$loggedInUser = new loggedInUser();
							$loggedInUser->email = $userdetails["Email"];
							$loggedInUser->user_id = $userdetails["User_ID"];
							$loggedInUser->hash_pw = $userdetails["Password"];
							$loggedInUser->display_username = $userdetails["Username"];
							$loggedInUser->clean_username = $userdetails["Username_Clean"];		
							$loggedInUser->updateLastSignIn();						
							$_SESSION["userCakeUser"] = $loggedInUser;														if($agent == 'fox') {								echo '<meta http-equiv="refresh" content="0; URL=index.php?page=account">';								die();							}else{								header("location: index.php?page=account");								die();							}
						}
					}
				}
			}			if(count($errors) > 0) {				if(!isset($_SESSION["Login_Attempts"]))				{					$_SESSION["Login_Attempts"] = 1;				}else{					$_SESSION["Login_Attempts"]++;				}				errorBlock($errors); 		} 
	}
?> 
<html lang="en"><head><link rel="stylesheet" type="text/css" href="assets/css/register.css" /></head>
<div id="login-holder">
	<div id="logo-login">		<h1>Login</h1>
	</div>

	<div class="clear"></div>
	
	<div id="loginbox">
	

	<div id="login-inner">
	<form method="POST" action="index.php?page=login" autocomplete="off" onsubmit="document.getElementById('#loginbutton').disabled = 1;">
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td><input name="username" type="text"  class="field stdsize" placeholder="username" /></td>			<br/>
		</tr>		
		<tr>
			<td><input type="password" name="password" placeholder="password" class="field stdsize" /></td>			<br/>
		</tr>
		<tr>
			<td><input type="checkbox" style="float: left; margin-right: 0.4em;" id="login-check" name="remember"/><label for="remember" style=" font-size: 18px; text-align: left; display: inline;">Remember me</label></td>
			<hr class='five'>			<br/>			<br/>		</tr>
		<tr><?php	if($_SESSION["Login_Attempts"] > 2)	{		echo 		'		<tr>			<td>				<center><img src="pages/docs/captcha.php" class="captcha stdsize"></center>			</td>		</tr>		<tr>			<td>				<input name="captcha" type="text" placeholder="Enter Security Code" class="field stdsize">			</td>		</tr>		';	}?>
			<td><input type="submit" id="loginbutton" class="blues stdsize" onclick="this.disabled=true;this.value='Logging In...';this.form.submit();<?php if($agent == 'fox') { ?>$('#login-holder').html('');<?php } ?>"/></td>
		</tr>
		<tr>
			<th></th>
			
		</tr>
		</table>	</div>

	<div class="clear"></div>
 </div>

 

	<div id="forgotbox">
		<div id="forgotbox-text"><a href="index.php?page=reset">Help! I forgot my password</a></div>
		
	</div>
</div></html><?php}?>
