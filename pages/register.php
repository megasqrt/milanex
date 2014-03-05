<?phprequire_once ('system/csrfmagic/csrf-magic.php');require_once("models/config.php");if(isUserLoggedIn()) {	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=account">';	die(); }if(isRegistrationDisabled()){    display_Reg_message();}else{	
if(!empty($_POST))
{
		$errors = array();
		$email = trim($_POST["email"]);
		$username = trim($_POST["username"]);
		$password = trim($_POST["password"]);
		$confirm_pass = trim($_POST["passwordc"]);
		$captcha = md5($_POST["captcha"]);					if ($captcha != $_SESSION['captcha'])		{			$errors[] = lang("CAPTCHA_FAIL");		}		if (is_numeric($username)) {			$errors[] = "Username contains no Alphanumeric charachters";		}		if ($username == "MilanExMasterAccount") {			$errors[] = "Username Unavailable";		}		$gmail = "gmail";		if (strpos($email, $gmail)) {			$errors[] = "Gmail addresses arent supported. please sign up with a different address.";		}
		if(minMaxRange(5,25,$username))
		{
			$errors[] = lang("ACCOUNT_USER_CHAR_LIMIT",array(5,25));
		}
		if(minMaxRange(8,50,$password) && minMaxRange(8,50,$confirm_pass))
		{
			$errors[] = lang("ACCOUNT_PASS_CHAR_LIMIT",array(8,50));
		}
		else if($password != $confirm_pass)
		{
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}
		if(!isValidEmail($email))
		{
			$errors[] = lang("ACCOUNT_INVALID_EMAIL");
		}
		//End data validation
		if(count($errors) == 0)
		{	
				//Construct a user object
				$user = new User($username,$password,$email);
				
				//Checking this flag tells us whether there were any errors such as possible data duplication occured
				if(!$user->status)
				{
					if($user->username_taken) $errors[] = lang("ACCOUNT_USERNAME_IN_USE",array($username));
					if($user->email_taken) 	  $errors[] = lang("ACCOUNT_EMAIL_IN_USE",array($email));		
				}
				else
				{										
					//Attempt to add the user to the database, carry out finishing  tasks like emailing the user (if required)					$errors[] = 'Successfully registered! Returning you to the login form!';
					if(!$user->userCakeAddUser())
					{					
					}					$getcountusers = @mysql_query("SELECT COUNT(*) as count FROM userCake_Users");					$count = mysql_result($getcountusers, 0, "count");					$register = mysql_query("UPDATE usersactive SET `total_users`='$count' WHERE `id`='1' ");					$time = mysql_real_escape_string(gettime());					$update = mysql_query("UPDATE usersactive SET `last_update`='$time' WHERE `id`='1' ");					sleep(1);					echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';				}
		}
	}
?> <link rel="stylesheet" type="text/css" href="assets/css/register.css" /><script type="text/javascript">	function passwordStrength(password)	{		var desc = new Array();				desc[0] = "Too Short";		desc[1] = "Weak";		desc[2] = "Terrible";		desc[3] = "Better";		desc[4] = "Good";		desc[5] = "Strong";		desc[6] = "Secure";		desc[7] = "Legendary";		var score   = 0;				if (password.length > 7) score++;		if (password.match(/\d+/)) score++;		if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/))  score++;		if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/))  score++;		if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) score++;		if (password.length > 13) score++;		if (password.length > 20 && password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) score++;						document.getElementById("passwordDescription").innerHTML = desc[score];		document.getElementById("passwordStrength").className = "strength" + score;	}</script><h1>Register</h1><b>By signing up, you agree to the <a href="index.php?page=tos"><u>Terms Of Service</u></a></b><center><?php
if (isset($message)){if ($message != ""){echo $message;}}
if (isset($errors)){errorBlock($errors);} 
?></center>
<div id="login-holder">
	<div id="loginbox">
		<center>
		</center>
		<div id="login-inner"><form method="POST" action="">
<table border="0" cellpadding="0" cellspacing="0">		<tr>		<td>			<input type="text" name="email" placeholder="Email" class="field stdsize"/>		</td>	</tr>
	<tr>
		<td>
			<input name="username" type="text" class="field stdsize" placeholder="Desired Username"/>
		</td>
	</tr>
	<tr>
		<td>
			<input type="password" id="password1" name="password" class="field stdsize" placeholder="Password" onkeyup="passwordStrength(this.value)"/>
		</td>
	</tr>	</br>	</br>	<tr>		<td>							<div id="passwordDescription">Password strength: not entered</div>								<div class="strength stdsize">					<div id="passwordStrength" class="strength0"></div>				</div>					</td>	</tr>		<tr>		<td>			<input type="password" id="password2" name="passwordc" class="field stdsize" placeholder="Repeat Password"/>		</td>	</tr>	<tr>		<td>			<center><img src="pages/docs/captcha.php" class="captcha stdsize"></center>		</td>	</tr>	<tr>		<td>			<input name="captcha" type="text" placeholder="Enter Security Code" class="field stdsize">		</td>	</tr>
	<tr>
		<td>
			<input type="submit" class="blues stdsize" onclick="this.disabled=true;this.value='Registering...';this.form.submit();"/>
		</td>
	</tr>
</table></form>
</div>
</div>
</div>
</body>
</html><?php}?>
