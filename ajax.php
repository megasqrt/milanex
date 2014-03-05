<?php
/**~2014 OpenEx.pw Developers. All Rights Reserved.~*
 *               https://openex.pw/
 *Licensed Under the MIT License : http://www.opensource.org/licenses/mit-license.php
 *
 *WARRANTY INFORMATION:
 *THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *THE SOFTWARE. 
 ***************************************/
include 'models/config.php';
include 'models/chat.config.php';
$id = addslashes(strip_tags($loggedInUser->user_id));
$username = addslashes(strip_tags($loggedInUser->display_username));
$do = addslashes(strip_tags($_GET['do']));

if($do === 'buyorder') {
	$coin_id = mysql_real_escape_string(strip_tags($_GET["coin"]));
	$SQL2 = mysql_query("SELECT * FROM Wallets WHERE `Id`='$coin_id'");
	$Currency_1a = mysql_result($SQL2, 0, "Acronymn");
	$Currency_1 = mysql_result($SQL2, 0, "Id");
	$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
	$name = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
	$fullname = mysql_real_escape_string(mysql_result($result, 0, "Name"));
	if(isset($_POST["price2"])) {
		//Don't touch unless authorized!
		if ($_POST["price2"] > 0.000000009 && $_POST["Amount2"] > 0.000000009) {
			$postedToken = filter_input(INPUT_POST, 'token2');
			if(!empty($postedToken)) {
				if(isTokenValid($postedToken)) {
					$PricePer = mysql_real_escape_string($_POST["price2"]);
					$Amount = mysql_real_escape_string($_POST["Amount2"]);
					//$Total = $Amount  file_get_contents("http://openex.pw/openex.pw/system/calculatefees2.php?P=" . $Amount);
					$Fees = $Amount * 0.005;
					$X = sprintf("%.8f",($Amount-$Fees)/$PricePer);
					$user_id = $loggedInUser->user_id; 
					if(TakeMoney($Amount,$id,$Currency_1) == true) {
						$New_Trade = new Trade();
						$New_Trade->trade_to = $name;
						$New_Trade->trade_from = $Currency_1a;
						$New_Trade->trade_amount = $X;
						$New_Trade->trade_value = $PricePer;
						$New_Trade->trade_owner = $id;
						$New_Trade->trade_type = $name;
						$New_Trade->trade_fees = $Fees;
						$New_Trade->trade_total = $Amount;
						$New_Trade->trade_type = $name;
						$New_Trade->standard = $X;
						//$New_Trade->GetEquivalentTrade();
						//$New_Trade->ExecuteTrade();
						$New_Trade->UpdateTrade();
						$message = "Trade Submitted.";
					}else{
						$error = true;
						$errors[] = "You cannot afford that!</br>";
					}
				}else{
					echo "invalid token";
					die();
				}
			}
		}else{
			$error = true;
			$errors[] = "Please fill in all the forms!!<br/>";
		}
	}
}

if($do === 'sellorder') {
	$coin_id = mysql_real_escape_string(strip_tags($_GET["coin"]));
	$SQL2 = mysql_query("SELECT * FROM Wallets WHERE `Id`='$coin_id'");
	$Currency_1a = mysql_result($SQL2, 0, "Acronymn");
	$Currency_1 = mysql_result($SQL2, 0, "Id");
	$result = mysql_query("SELECT * FROM Wallets WHERE `Id`='$id'");
	$name = mysql_real_escape_string(mysql_result($result, 0, "Acronymn"));
	$fullname = mysql_real_escape_string(mysql_result($result, 0, "Name"));
	if(isset($_POST["Amount"])) {
	//Don't touch unless authorized!
		if ($_POST["price1"] > 0.000000009 && $_POST["Amount"] > 0.000000009) 
		{
			$postedToken = filter_input(INPUT_POST, 'token1');
			if(!empty($postedToken)){
				if(isTokenValid($postedToken)){
					$PricePer = mysql_real_escape_string($_POST["price1"]);
					$Amount = mysql_real_escape_string($_POST["Amount"]);
					$user_id = $loggedInUser->user_id; 
					if(TakeMoney($Amount,$id,$coinid) == true)
					{	
						$New_Trade = new Trade();
						$New_Trade->trade_to = $Currency_1a;
						$New_Trade->trade_from = $name;
						$New_Trade->trade_amount = $Amount;
						$New_Trade->trade_value = $PricePer;
						$New_Trade->trade_owner = $id;
						$New_Trade->trade_type = $name;
						$New_Trade->trade_fees = 0;
						$New_Trade->trade_total = $Amount;
						$New_Trade->trade_type = $name;
						$New_Trade->standard = $Amount;
						//$New_Trade->GetEquivalentTrade();
						//$New_Trade->ExecuteTrade();
						$New_Trade->UpdateTrade();
						echo '<meta http-equiv="refresh" content="0; URL=index.php?page=trade&market='.$id.'">';
					}
					else
					{
						$error = true;
						$errors[] = "You cannot afford that!</br>";
					}
					
				}else{
					$error = true;
					$errors[] = "Invalid Token.";
				}
			}	
		}else{
			$error = true;
			$errors[] = "Please fill in all the forms!!<br/>";
			
		}
	}
}
if($do === 'load'){
	$db->query("SELECT * FROM (SELECT * FROM messages WHERE `hidden`='0' ORDER BY `id` DESC LIMIT 100) as last100 ORDER BY id");
	$data = $db->GET();
	foreach($data as $key => $value) {
		if(!isUserMod($id) and !isUserAdmin($id)) {
			$color = htmlentities($value['color']);
			$user = htmlentities($value['username']);
			$msg = htmlentities($value['message']);
			echo "<li id='msg_row'><b id='u_name_chat' style='color: ".$color.";'>".$user."</b>: ".$msg."</li>";
		}else{
			$color = htmlentities($value['color']);
			$user = htmlentities($value['username']);
			$msg = htmlentities($value['message']);
			$todelete = $db->real_escape_string($value['id']);
			echo "<li id='msg_row'><b id='u_name_chat' style='color: ".$color.";'>".$user."</b>: ".$msg."<a color='blue' href='#' rel=".$todelete." class='delete' onClick='deleteChat(this);'>delete</a></li>";
		}
	}
	?>
	<script>
		<?php 
		if(isUserMod($id) || isUserAdmin($id)) 
		{
		?>
			function deleteChat(t) {
				console.log("Clicked delete");
				var toDEL = $(t).parent();
				var id = $(t).attr('rel');
				console.log(id);
				
				$.post('ajax.php?do=delete', {id: id})
					.done(function(data) {
						$(toDEL).hide();
					});
			}
		<?php
		}
		?>
	</script>
	<?php
}
elseif($do === 'post'){
	if (isUserCBanned($id)) {

	die();
	
	}else{

		if(isUserAdmin($id)) 
		{
			$color = "#0404B4";
		}
		else if (isUserMod($id))
		{
			$color = "#B43104";
		} 
		else 
		{
			$color = "#000000";
		}
		$color_ = $db->real_escape_string(htmlentities(($color)));
		$user = $db->real_escape_string(htmlentities(($username))); 
		$message = $db->real_escape_string(strip_tags(($_POST['message']), '<a>'));
		$timestamp = $db->real_escape_string(gettime());
		/*
		if(isUserMod($id) || isUserAdmin($id)) {
			if (strpos($message, '/') !== FALSE) {
				/*here we'll add the code to ban users from areas of the site
				 *based on commands /sb <siteban> /cb <chatban> /ub <unban>
				 *format is simple command + user, eg /sb testuser would siteban testuser
				 */
				 /*
				$cmd = explode($message, '/' ' ');
				$message = print_r($cmd, false);
			}else{
				
			}
		}
		*/
		if($color_ == null){
			die("no username color");
		}
		if($user == null){
			die("not logged in");
		}
		if($message == null){
			die("no message entered");
		}
		if($timestamp == null){
			die("no timestamp");
		}
		$db->Query("INSERT INTO messages (color, username, message, timestamp) VALUES ('$color_','$user','$message','$timestamp')");
	}	

}
elseif($do === 'delete'){
	if(isUserMod($id) || isUserAdmin($id)) {
		$idz = $db->real_escape_string(strip_tags($_POST['id']));
		if($idz == null) {
			die("invalid request");
		}
		$query = $db->Query("UPDATE messages SET `hidden`='1' WHERE `id`='$idz'");
		$result = $db->GET($query);
		print($result);
	}else{
		die("user is not admin or moderator");
	}
}
elseif($do === 'getapireference') {
	$file = 'downloads/API-Reference.rtf';
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($file));
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		ob_clean();
		flush();
		readfile($file);
		header('Location: index.php?page=api');
		die();
	}else{
		die("file not found");
	}
} 
elseif($do === "trade_balances") {

	$coin 	 = mysql_real_escape_string(strip_tags($_GET["coin"]));
	$balance = $loggedInUser->getbalance($coin);

	?><u><?php echo(sprintf("%.8f",$balance)); ?></u><?php
}else{
	die("invalid operation");
}
