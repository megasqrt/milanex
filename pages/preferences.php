<?php
require_once ('system/csrfmagic/csrf-magic.php');
$id = $loggedInUser->user_id;
$account = $loggedInUser->display_username;

if(!isUserLoggedIn()){
echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
}
//print_r(fetchUserDetails($account), false);
?>
<h1>Account Settings</h1>
<table id="page">
<tr>
	<th>Username</th>
	<th>Email</th>
</tr>
<tr>
	<td><?php echo $account; ?></td>
	<td>hidden for your protection</td>
</tr>
</table>
<hr><br/>
<h2>Options</h2><br/>
<div>
<link rel="stylesheet" type="text/css" href="assets/css/register.css" />
	<form name="changePass" action="index.php?page=preferences" method="post" onsubmit="document.getElementById('#Update_PW').disabled = 1;">
		<table>
			<tr>
				<td><center><h3>Change Password</h3></center></td>
			</tr>
			<tr>
				<td><input type="password" name="password" placeholder="Existing Password" class="field stdsize" /></td>
			</tr>
			
			<tr>
				<td><input type="password" name="passwordc" placeholder="New Password" class="field stdsize" /></td>
			</tr>
			
			<tr>
				<td><input type="password" name="passwordcheck" placeholder="NewPassword(Repeat)" class="field stdsize" /></td>
			</tr>
			
			<tr>
				<td><input type="submit" name="changePass"value="Update Password" id="Update_PW" class="blues stdsize" /></td>
		   </tr>
		</table>       
	</form>
<?php
if(isset($_POST["changePass"])) {
	if(!empty($_POST)) {
		if($_SESSION["Pass_Attempts"] > 2)
		{
			$account = mysql_real_escape_string(strip_tags($loggedInUser->display_username));
			$uagent = mysql_real_escape_string(getuseragent()); //get user agent
			$ip = mysql_real_escape_string(getIP()); //get user ip
			$date = mysql_real_escape_string(gettime());
			$sql = @mysql_query("INSERT INTO access_violations (username, ip, user_agent, time) VALUES ('$account', '$ip', '$uagent', '$date');");
			$captcha = md5($_POST["captcha"]);
			
			if ($captcha != $_SESSION['captcha'])
			{
				$errors[] = lang("CAPTCHA_FAIL");
			}
			
		}
		if($_SESSION["Pass_Attempts"] > 2)
		{
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
		if($_SESSION["Pass_Attempts"] > 3) {
			$ip_address = mysql_real_escape_string(getIP());
			$date2 = mysql_real_escape_string(gettime());
			mysql_query("INSERT INTO bantables_ip (ip, date) VALUES ( '$ip_address', '$date2');");	
		}
		$errors = array();
		$password = $_POST["password"];
		$password_new = $_POST["passwordc"];
		$password_confirm = $_POST["passwordcheck"];
		if(trim($password) == "") {
			$errors[] = lang("ACCOUNT_SPECIFY_PASSWORD");
		}
		else if(trim($password_new) == "") {
			$errors[] = lang("ACCOUNT_SPECIFY_NEW_PASSWORD");
		}
		else if(minMaxRange(8,50,$password_new)) {	
			$errors[] = lang("ACCOUNT_NEW_PASSWORD_LENGTH",array(8,50));
		}
		else if($password_new != $password_confirm) {
			$errors[] = lang("ACCOUNT_PASS_MISMATCH");
		}
		if(count($errors) == 0) {
			$entered_pass = generateHash($password,$loggedInUser->hash_pw);
			$entered_pass_new = generateHash($password_new,$loggedInUser->hash_pw);
			if($entered_pass != $loggedInUser->hash_pw) {
				$errors[] = lang("ACCOUNT_PASSWORD_INVALID");
			}
			else if($entered_pass_new == $loggedInUser->hash_pw) {
				$errors[] = lang("NOTHING_TO_UPDATE");
			}else{
				$loggedInUser->updatePassword($password_new);
			}
		}
		if(count($errors) > 0) {
			if(!isset($_SESSION["Pass_Attempts"]))
			{
				$_SESSION["Pass_Attempts"] = 1;
			}else{
				$_SESSION["Pass_Attempts"]++;
			}
			errorBlock($errors); 
		}else{ 
			echo lang("ACCOUNT_DETAILS_UPDATED");  
		} 
	}
}
?>
</div><br/>
