<center>
<?php
/**~2014 milancoin.org Developers. All Rights Reserved.~*
 *               http://www.milancoin.org/milanex/
 *Licensed Under the MIT License : http://www.opensource.org/licenses/mit-license.php
 *    +++++++++++++++++++++++
 *    +WARRANTY INFORMATION:+
 *    +++++++++++++++++++++++
 *THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *THE SOFTWARE. 
 ***************************************/
require_once ('system/csrfmagic/csrf-magic.php');
/***\/
 */	
	if(!isUserLoggedIn()) {
		echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';
	}
/***\/
 */     $user_id = addslashes(strip_tags($loggedInUser->user_id));
    $account = addslashes(strip_tags($loggedInUser->display_username));
/***\/
 */
	if(isUserAdmin($user_id)) {
		$sql = @mysql_query("SELECT SUM(Amount) as `Amount`  FROM balances WHERE User_ID='-12' AND `Wallet_ID` = '1'");
		echo'<h3>Welcome Admin the current fee earnings are: '.mysql_result($sql,0,"Amount").' MLC</h3>';
	}
	echo '<h2>Welcome to your account page <b>'.$account.'</b></h2><hr class="five"/>';
?>
<div class="tabber">
	<div class="tabbertab" title="Balances">
		<?php
		/***\/
		 */	
			echo
			'
			<div id="page" style="width: 95%;">
			<table id="page">
			<tr>
				<th>Currency</th><th>Available</th><th>Pending</th><th>Deposit</th><th>Withdraw</th>
			</tr>';

			$user_id =  $loggedInUser->user_id;
			$sql = mysql_query("SELECT * FROM Wallets WHERE `disabled`='0' ORDER BY `Id` ASC");
			$g = 0;
			while ($row = mysql_fetch_assoc($sql)) {
					$g++;
					if($g & 1) {
						$color = "lightgray";
					} else {
						$color = "darkgray";
					}
					$coin = $row["Id"];
					$amount = 0;
					$account = $loggedInUser->display_username;
					$acronymn = $row["Acronymn"];
					$sql_pending = mysql_query("SELECT * FROM deposits WHERE `Paid`='0' AND `Account`='$account' AND `Coin`='$acronymn'");
					$nums = mysql_num_rows($sql_pending);
					$pending = 0;
					$market_id = $row["Id"];
					for($iz = 0;$iz<$nums; $iz++) {
						$pending = $pending + @mysql_result($sql_pending,$iz,"Amount");
					}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                if (isset($_GET['fchk'])){
                        if ($_GET['fchk']==$acronymn){
                                $id = $row["Id"];
                                $wallet = new Wallet($id);
                                $listtrans = $wallet->Client->listtransactions($account, 5);
                                foreach($listtrans as $key => $trans) {
                                        //print_r($trans);
                                        //$address=$trans['address'];
                                        //if ($address !== $address){break;}
                                        $tid = $trans['txid'];
                                        $acronymn=$acronymn;
                                        $account = $trans["account"];
                                        $category = $trans["category"];
                                        $confirms = $trans["confirmations"];
                                        $amount = $trans["amount"];
                                        $sql2 = @mysql_query("SELECT * FROM deposits WHERE `Transaction_Id`='$tid' AND `Coin`='$acronymn'");
                                        $id2 = @mysql_result($sql2,0,"id");
                                        $paid = @mysql_result($sql2,0,"Paid");
                                        if($id2 != NULL) {
                                        //echo $amount;
                                                if($paid == 0)
                                                {
                                                        if($category == "receive" && $confirms > 5 && $account != "")
                                                        {
                                                                mysql_query("UPDATE deposits SET `Paid`='1' WHERE `id`='$id2'");
                                                                AddMoney($amount, $account, $acronymn);
                                                                echo $amount." ".$acronymn." was credited to your account: ".$account;
                                                        }else{
                                                                echo $amount." ".$acronymn." This Deposit is unconfirmed. Current confirmations:" . $confirms .". Required : 6.";
                                                        }
                                                }else{
                                                        echo $amount." ".$acronymn." was already credited to your account: ".$account."<br>";
                                                }
                                        }else{
                                                if($category == "receive" && $account != "") {
                                                        if($confirms > 5) {
                                                                mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$acronymn','1','$account');");
                                                                AddMoney($amount, $account, $acronymn);
                                                                echo $amount." ".$acronymn." was credited to your account";
                                                        }else{
                                                                mysql_query("INSERT INTO  deposits (`Transaction_Id`,`Amount`,`Coin`,`Paid`,`Account`) VALUES ('$tid','$amount','$acronymn','0','$account');");
                                                                echo "This Deposit is unconfirmed. Current confirmations:" . $confirms .". Required : 6.";
                                                        }
                                                }else{
                                                        echo "transaction is not a deposit or account is invalid.";
                                                }
                                        }
                                }
                        }
                }
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                                        $result = @mysql_query("SELECT SUM(Amount) as `Amount`  FROM balances WHERE User_ID='$user_id' AND `Wallet_ID` = '$coin'");
                                        if($result == NULL) {
                                                $amount = 0;
                                                $pending = 0;
                                        }else{
                                                $amount = @mysql_result($result,0,"Amount");
                                                $account = $loggedInUser->display_username;
                                        }
					echo'
					<tr class="'.$color.'">
						<td><a href="index.php?page=trade&market='.$market_id.'">'.$row["Name"].'</a></td><td class="b1">'.sprintf("%.8f",$amount).'</td>
						<td class="b1">'.$pending.'</td>
						<td><a href="index.php?page=deposit&id='.$row["Id"].'">Deposit</a>&nbsp;&nbsp;<a href="index.php?page=account&fchk='.$row["Acronymn"].'">click to flush</a></td>
						<td><a href="index.php?page=withdraw&id='.$row["Id"].'">Withdraw</a></td>
					</tr>';
			}
		?>
		</table>
		</div>
	</div>
	
	<div class="tabbertab" title="Open Orders">
	<?php include("open_orders_all.php"); ?>
	</div>
	
	<div class="tabbertab" title="History">
	<?php include("account_history.php"); ?>
	</div>
	
	<div class="tabbertab" title="Settings">
	<?php include("preferences.php"); ?>
	</div>
	
	<div class="tabbertab" title="Support">
	<!--support-->
	<?php
	if(isUserAdmin($user_id) === true)
	{
	echo "<h2>Welcome Admin</h2>";
	$sql2 = mysql_query("SELECT * FROM Tickets WHERE `opened`='1'");
	}
	if(isUserMod($user_id) === true)
	{
	echo "<h2>Welcome Moderator</h2>";
	$sql2 = mysql_query("SELECT * FROM Tickets WHERE `opened`='1'");
	}
	if(isUserNormal($user_id) === true){
		echo "<h2>How may I help you today, <b>".$account."</b> ?</h2>";
		echo "
		<ul class='nobullets'>
			<li style='width: 200px !important; lineheight: 35px;' class='blues'><h3><a href='index.php?page=newticket'>Get Support</a></h3></li>
		</ul>
		</br>";
		$sql2 = mysql_query("SELECT * FROM Tickets WHERE `user_id`='$id'");
	}

	$num = mysql_num_rows($sql2);
	?>
		<div id="page">
			<form action="">
			<table id="page">
			<tr>
				<th><a id="toggle-all" ></a> </th>
				<th><a href="">Ticket Subject</a>	</th>
				<th><a href="">Posted</a></th>			
				<th><a href="">Answers</a></th>
				<th><a href="">Status</a>
			</tr>
			<?php
			for($i = 0;$i<$num;$i++)
			{
			$subject = mysql_result($sql2,$i,"subject");
			$posted = mysql_result($sql2,$i,"posted");
			$id = mysql_result($sql2,$i,"id");
			$answers = GetPosts($id);
			$opened = mysql_result($sql2,$i,"opened");
			if($opened == 1)
			{
			$open = "<b>Open</b>";
			}
			else
			{
			$open = "<b>Closed</b>";
			}
			?>
							<tr>
								<td><input  type="checkbox"/></td>
								<td><a href="index.php?page=viewticket&id=<?php echo $id; ?>"><?php echo $subject;?></a></td>
								<td><?php echo $posted;?></td>
			<td><?php echo $answers;?></td>
			<td><?php echo $open; ?></td>
							</tr>
			<?php
			}
			?>
			</table>
			</form>
		</div>
	</div>

	<div class="tabbertab" title="API Info">
	<?php
	$api_select = mysql_query("SELECT * FROM Api_Keys WHERE `User_ID`='$id'");
	if(mysql_num_rows($api_select) > 0) {
		$pkey = mysql_result($api_select, 0, "Public_Key");
		$akey = mysql_result($api_select, 0, "Authentication_Key");
		echo '<h3>Your Public Key is:</h3><br/>';
		echo '<input onclick="ClipBoard();" style="width: 70%;" type="text" id="pubkey" class="field stdsize" readonly value="'.$pkey.'" />';
		echo '<br/>';
		echo '<h3>Your Server Key is:</h3><br/>';
		echo '<input onclick="ClipBoard();" style="width: 70%;" type="text" id="pubkey" class="field stdsize" readonly value="'.$akey.'" /?';
		echo '<br/>';	
		echo '<h3>API Reference and Examples</h3>';

		echo '<a href="ajax.php?do=getapireference">Download Reference(RTF Format)</a>';

	}else{

		$topublic = generateKey($id); //public key

		$toprivate = generateKey($id); //private key

		$pub_check_no_collision = mysql_query("SELECT `Public_Key` FROM Api_Keys WHERE `Public_Key` = '$topublic'");

		$priv_check_no_collision = mysql_query("SELECT `Authentication_Key` FROM Api_Keys WHERE `Authentication_Key` = '$toprivate'");

		if(mysql_num_rows($pub_check_no_collision) > 0 ) {
			echo '<meta http-equiv="refresh" content="0; URL=index.php?page=account">';
		}else{

		$api_insert = mysql_query("INSERT INTO Api_Keys (`Public_Key`,`Authentication_Key`,`User_ID`) VALUES ('$topublic','$toprivate','$id')");
		echo '<meta http-equiv="refresh" content="0; URL=index.php?page=account">';
		}

	}
	?>
	</div>
</div>
</center>

