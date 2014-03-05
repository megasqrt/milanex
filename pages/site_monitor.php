<?php
/**~2014 MilanEx.pw Developers. All Rights Reserved.~*
 *               https://www.milancoin.org/milanex/
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
require_once("models/config.php");
require_once ('system/csrfmagic/csrf-magic.php');
if(!isUserLoggedIn()) {
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die();
}
if(!isUserAdmin($id)) {
	echo '<meta http-equiv="refresh" content="0; URL=access_denied.php">';
	die();
}
?>
<script type="text/javascript" src="assets/js/jquery.min.js"></script>
<h1>Site Monitor</h1>
<div class="serverload">
	<form action="" name="serverload" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="serverload" value="Show Server Load" class="blues stdsize" id="submit" />
	</form>	
</div>
<hr class="five" />
<div class="mysqlload">
	<form action="" name="mysqlload" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="mysqlload" value="Show SQL Status" class="blues stdsize" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="users">
	<form action="" name="userstats" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="userstats" value="Show User Statistics" class="blues stdsize" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="coinstatus">
	<form action="" name="coinstatus" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="coinstatus" value="Check Coin Stats" class="blues stdsize" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="optsql">
	<form action="" name="optimizesql" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="optimizesql" value="Optimize SQL" class="blues stdsize" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="earnings">
	<form action="" name="revenue" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="revenue" value="Site Revenue" class="blues stdsize" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="results" id="result">
<?php
if (isset($_POST["serverload"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
	echo'<b class="title-point">Server Load(main):</b></br>
		<table id="page">';
	
		$var_load = sys_getloadavg();
		
		foreach($var_load as $key => $value) {
			echo "<tr><td>".$key." : ".$value."</tr><td>";
		}
	echo'</table>';			

}

if (isset($_POST["mysqlload"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
		echo'<b class="title-point">MySQL Load:</b></br>
		<table id="page">';
			$mysqlload = explode("  ", mysql_stat());
				foreach ($mysqlload as $key => $value){
					echo "<tr><td>".$key." : ".$value."<td></tr>";
				}
		echo'</table>';

}

if (isset($_POST["userstats"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
	echo '
	<b class="title-point">Users Online</b></br>
		<table id="page">';
			
			$getusers = mysql_query("SELECT * FROM usersactive WHERE `id`=1");
			$getloggedin = mysql_query("SELECT COUNT(*) AS `count` FROM `userCake_Users` WHERE LastTimeSeen > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
			$loggedin = mysql_result($getloggedin, 0, "count");
			$total = mysql_result($getusers, 0, "total_users");
			$upda = mysql_result($getusers, 0, "last_update");
				echo "
				<tr>
					<td>Logged In : ".$loggedin."<td>
				</tr>
				<tr>
					<td>Total Users: ".$total."<td>
				</tr>
				<tr>
					<td>Last Updated : ".$upda."<td>
				</tr>
				
			
			
		</table>";	
}

if (isset($_POST["coinstatus"])) {
	echo '<h2>Result:</h2><br/>';
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php
	echo'<b class="title-point">Coin Status:</b></br>
		<div id="page">
		<table id="page">
			<center>
				<tr>
					<th>Name</th>
					<th>Balance</th>
					<th>Blocks</th>
					<th>Connections</th>
				</tr>
				';
				$result = mysql_query("SELECT * FROM `Wallets` WHERE `disabled`='0' ORDER BY `Wallets`.`Acronymn` ASC");
				$num_rows = mysql_num_rows($result);
				//new method
				for($i=0; $i < $num_rows; $i++) {
					if($i & 1) {
						$color = "lightgray";
					} else {
						$color = "darkgray";
					}
					$id         = mysql_result($result,$i,"Id");
					$pw         = mysql_result($result,$i,"Wallet_Password");
					$usr        = mysql_result($result,$i,"Wallet_Username");
					$acro       = mysql_result($result,$i,"Acronymn");
					$wallet     = new Wallet($id);
					$id_check   = $loggedInUser->user_id;
					$requestkey = md5( hash('sha512', $id_check.$id.$usr.$pw));
					$info       = $wallet->GetInfo($id,$pw,$usr,$requestkey,$id_check);
					$balance    = round($info["balance"], 8);
					$blocks     = $info["blocks"];
					$connects   = $info["connections"];
					echo
					'
					<tr class='.$color.'>
					<td>'.$acro.'</td>
					<td>'.$balance.'</td>
					<td>'.$blocks.'</td>
					<td>'.$connects.'</td>
					</tr>
					';
				}			
		echo'</center></table></div>';
}
if(isset($_POST["optimizesql"])) {
	mysql_query("OPTIMIZE TABLE TicketReplies");
	mysql_query("OPTIMIZE TABLE Trade_History");
	mysql_query("OPTIMIZE TABLE Wallets");
	mysql_query("OPTIMIZE TABLE Withdraw_History");
	mysql_query("OPTIMIZE TABLE Withdraw_Requests");
	mysql_query("OPTIMIZE TABLE access_violations");
	mysql_query("OPTIMIZE TABLE balances");
	mysql_query("OPTIMIZE TABLE bantables_ip");
	mysql_query("OPTIMIZE TABLE config");
	mysql_query("OPTIMIZE TABLE deposits");
	mysql_query("OPTIMIZE TABLE messages");
	mysql_query("OPTIMIZE TABLE trades");
	mysql_query("OPTIMIZE TABLE userCake_Groups");
	mysql_query("OPTIMIZE TABLE userCake_Users");
	mysql_query("OPTIMIZE TABLE usersactive");
echo "done : database was optimized";
}

if(isset($_POST["revenue"])) {
?>
		<a href class="miniblues" onclick="$('#result').html('');" height="30" width="200"/>Click Here To Close</a>
<?php

	$getearnings = @mysql_query("SELECT SUM(Amount) as `Amount`  FROM balances WHERE User_ID='-12' AND `Wallet_ID` = '1'");
	$earnings = mysql_result($getearnings,0,"Amount");
	$earned = array();
	$earned["staff"]   = $earnings * 0.6;
	$earned["shares"] = $earnings * 0.2;
	$earned["site"]   = $earnings * 0.2;
	echo'<b class="title-point">Site Revenue:</b></br>
	<table id="page">';
	echo '<tr><td>Total</td><td>'.$earnings.'</td></tr>';
	foreach($earned as $key => $value) {
	
		echo '<tr><td>'.$key.'</td><td>'.$value.'</td></tr>';
	
	}
	echo '</table>';
}
?>
</div>
