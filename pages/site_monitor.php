<?php
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
		<input type="submit" name="serverload" value="Show Server Load" class="blues" id="submit" />
	</form>	
</div>
<hr class="five" />
<div class="mysqlload">
	<form action="" name="mysqlload" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="mysqlload" value="Show SQL Status" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="users">
	<form action="" name="userstats" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="userstats" value="Show User Statistics" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="coinstatus">
	<form action="" name="coinstatus" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="coinstatus" value="Check Coin Stats" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="optsql">
	<form action="" name="optimizesql" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="optimizesql" value="Optimize SQL" class="blues" id="submit" />
	</form>
</div>
<hr class="five" />
<div class="missingp">
	<form action="" name="missingpeople" method="POST" onsubmit="document.getElementById('#submit').disabled = 1;">
		<input type="submit" name="missingpeople" value="Missing Accounts" class="blues" id="submit" />
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
		<table id="page">
			<center>
				<tr>
					<th>Name</th>
					<th>Balance</th>
					<th>Blocks</th>
					<th>Connections</th>
					<th>errors</th>
				</tr>
				';

				$result = mysql_query("SELECT * FROM `Wallets` WHERE `disabled`='0' ORDER BY `Wallets`.`Acronymn` ASC");
				
				while($row = mysql_fetch_array($result))
				{
				$id = $row["Id"];
				$pw = $row["Wallet_Password"];
				$usr = $row["Wallet_Username"];
				$wallet = new Wallet($id);
				$id_check = $loggedInUser->user_id;
				$requestkey = md5( hash('sha512', $id_check.$id.$usr.$pw));
				$info = $wallet->GetInfo($id,$pw,$usr,$requestkey,$id_check);

				echo "<tr>";
				echo "<td>" . $row['Acronymn'] . "</td>";
				foreach($info as $key => $value) {
					if($key == "balance"){
						echo '<td>'.round($value, 8).'</td>';
					}
					elseif($key == "blocks"){
						echo '<td>'.$value.'</td>';
					}
					elseif($key == "connections"){
						echo '<td>'.$value.'</td>';
					}
					elseif($key == "errors"){
						echo '<td>'.$value.'</td>';
					}else{
						
					}
				}
				echo "</tr>";
				
				}
			
		echo'</center></table>';
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

if(isset($_POST["missingpeople"])) {
	echo '<h1>Missing People</h1>';
	$sql = mysql_query("SELECT * FROM deposits");
	for($i=0;$i<mysql_num_rows($sql);$i++)
	{
		$Account = mysql_result($sql,$i,"Account");
		$transaction_id = mysql_result($sql,$i,"Transaction_Id");
		$amount = mysql_result($sql,$i,"Amount");
		$coin = mysql_result($sql,$i,"Coin");
		$sql2 = @mysql_query("SELECT * FROM userCake_Users WHERE `Username_Clean`='$Account'");
		$ac = @mysql_result($sql2,0,"Username_Clean");
		if($ac != "")
		{
		}else{
			echo "Account: $Account Transaction: $transaction_id : $amount : $coin<br/> ";
		}
	}

}
?>
</div>
