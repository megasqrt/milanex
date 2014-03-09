<?php
function mysqli_result($result,$row,$field=0) {
    if ($result===false) return false;
    if ($row>=mysqli_num_rows($result)) return false;
    if (is_string($field) && !(strpos($field,".")===false)) {
        $t_field=explode(".",$field);
        $field=-1;
        $t_fields=mysqli_fetch_fields($result);
        for ($id=0;$id<mysqli_num_fields($result);$id++) {
            if ($t_fields[$id]->table==$t_field[0] && $t_fields[$id]->name==$t_field[1]) {
                $field=$id;
                break;
            }
        }
        if ($field==-1) return false;
    }
    mysqli_data_seek($result,$row);
    $line=mysqli_fetch_array($result);
    return isset($line[$field])?$line[$field]:false;
}
function GetChartData($id)
{
	$idc = intval($id);
	$sql1 = @mysql_query("SELECT `Data`,`LastUpdated` FROM GraphCache WHERE `Id`='$idc' LIMIT 1");
	$LU = @mysql_result($sql1,0,"LastUpdated") or 0;
	$D = @mysql_result($sql1,0,"Data");
	if((time() - $LU) > 600)
	{
		$TempTable = array();
		$sql2 = @mysql_query("SELECT * FROM Trade_History WHERE `Market_Id`='$idc'");
		$NString = "";
		while($row=@mysql_fetch_array($sql2))
		{
			$TDateT = date("m/d/Y h",$row["Timestamp"]);
			$TDate = date_timestamp_get(DateTime::createFromFormat("m/d/Y h",$TDateT));
			//print($TDate . "\n");
			$TEMPS = $TempTable["" . $TDate];
			if($TEMPS == null)
			{
				$TempTable[$TDate] = array(
				"High"=> 0,
				"Low"=> 999999999,
				"OpenTime"=> ($TDate + 3700),
				"Open"=> 0,"CloseTime"=> 0,
				"Close" => 0,
				"Volume" => 0,
				"TimeStamp" => $TDate);
			}
			if($row["Price"] > $TempTable[$TDate]["High"])
			{
				$TempTable[$TDate]["High"] = $row["Price"];
			}
			if($row["Price"] < $TempTable[$TDate]["Low"])
			{
				$TempTable[$TDate]["Low"] = $row["Price"];
			}
			if($row["Timestamp"] < $TempTable[$TDate]["OpenTime"])
			{
				$TempTable[$TDate]["OpenTime"] = $row["Timestamp"];
				$TempTable[$TDate]["Open"] = $row["Price"];
			}
			if($row["Timestamp"] > $TempTable[$TDate]["CloseTime"])
			{
				$TempTable[$TDate]["CloseTime"] = $row["Timestamp"];
				$TempTable[$TDate]["Close"] = $row["Price"];
			}
			$TempTable[$TDate]["Volume"] = $TempTable[$TDate]["Volume"] + $row["Quantity"];
		}
		if(ksort($TempTable) == true)
		{
			$in = 0;
			foreach($TempTable as $key => $value)
			{	
				if($in == 0)
				{
					$in = 1;

					$NString = $NString . "[['" . date("Y-m-d h:00:00",$value["TimeStamp"])."'],". sprintf("%0.8f",$value["Open"]) . "," . sprintf("%0.8f",$value["Low"]) . "," . sprintf("%0.8f",$value["High"]) . "," . sprintf("%0.8f",$value["Close"]) . "," . sprintf("%0.8f",$value["Volume"]) . "]";
				}else{
					$NString = $NString . ",[['" . date("Y-m-d h:00:00",$value["TimeStamp"])."'],". sprintf("%0.8f",$value["Open"]) . "," . sprintf("%0.8f",$value["Low"]) . "," . sprintf("%0.8f",$value["High"]) . "," . sprintf("%0.8f",$value["Close"]) . "," . sprintf("%0.8f",$value["Volume"]) . "]";
				}

			}
			mysql_query("DELETE FROM GraphCache WHERE `Id`='$idc'");
			mysql_query("INSERT INTO GraphCache (`Id`,`Data`,`LastUpdated`) VALUES ('$idc','" . mysql_real_escape_string($NString) ."','". time() ."')");
		}
		return $NString;
	}else{
		return $D;
	}
}
function accountMenu() {
	echo
	'
	<ul id="accountmenu">
	<li><a href="index.php?page=account"><i class="fa fa-usd"><font class="mhead">Balances</font></i></a></li>
	<li><a href="index.php?page=open_orders_all"><i class="fa fa-caret-square-o-right"><font class="mhead">Open Orders</font></i></a></li>
	<li><a href="index.php?page=account_history" title="account history"><i class="fa fa-book"><font class="mhead">History</font></i></a></li>
	<li><a href="index.php?page=support" title="support system"><i class="fa fa-warning"><font class="mhead">Support</font></i></a></li>
	<li><a href="index.php?page=preferences" title="account preferences"><i class="fa fa-cogs"><font class="mhead">Settings</font></i></a></li>
	<li><a href="index.php?page=api" title="API"><i class="fa fa-code"><font class="mhead">API</font></i></a></li>
	</ul>
	<hr class="five" />
	<hr class="five" />
	';
}

function getToken($id,$ip){
	$token = hash('sha256', $id.$ip.time());
	if(!isset($_SESSION['tokens'])){
	$_SESSION['tokens'] = array($token => 1);
	}
	else{
	$_SESSION['tokens'][$token] = 1;
	}
	return $token;
}
function isTokenValid($token){
	if(!empty($_SESSION['tokens'][$token])){
	unset($_SESSION['tokens'][$token]);
	return true;
	}
	return false;
}

function browsercheck() {
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
		$agent = 'chrome';
	}
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE) {
		$agent = 'safari';
	}
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
		$agent = 'fox';
	}else{
		$agent = 'unk';
	}
	return $agent;
}

function mobile_listen() 
{
	$useragent=$_SERVER['HTTP_USER_AGENT'];
	if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
	{
		echo '<meta http-equiv="refresh" content="0; URL=https://milanex.mobi">';
	} 
}

function memcache_init()
{
	include("ratelimiter.php");

	//memcached listener 
	$memcache_obj = new Memcache; 
	$memcache_obj->addServer('memcache_host', 11211);

	$rateLimiter = new RateLimiter(new Memcache(), $_SERVER["REMOTE_ADDR"]);
	try {
		$rateLimiter->limitRequestsInMinutes(15, 1);
	} catch (RateExceededException $e) {
		header("HTTP/1.0 529 Too Many Requests");
		exit;
	}
}

function getuseragent()
{
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') == TRUE) {
		$u_agent = "Internet Explorer";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') == TRUE) {
		$u_agent = "Google Chrome";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') == TRUE) {
		$u_agent = "Opera Mini";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') == TRUE) {
		$u_agent = "Opera";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox/25.0') == TRUE) {
		$u_agent = "Mozilla Firefox";
	}
	elseif(strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') == TRUE) {
		$u_agent = "Safari";
	}
	else { 
		$u_agent = "Unknown/Other";
	}	
	return $u_agent;
}

function gettime()
{
	{
	$tmvari = date("F j, Y, g:i a");
	}
	return $tmvari;
}

function load_monit_init()
{
	$load = sys_getloadavg();
	$sleep=5;
	$maxload=2;
	if ($load[0] > $maxload) {
		   sleep($sleep);
		echo "Busy server - sleep $sleep seconds<br>";
	}
}
function TakeMoney($amount, $user, $currency, $fs=false)
{
	$sell = @mysql_query("SELECT * FROM balances WHERE `User_ID`='$user' AND `Wallet_ID`='$currency'");
	$id   = @mysql_result($sell, 0, "id");
	$old = @mysql_result($sell, 0, "Amount");

	if($old >  0.000000009 && $amount >  0.000000009 && $old >= $amount) {
		$new = sprintf("%0.8f", $old) - sprintf("%0.8f", $amount);
		$new0 = sprintf("%0.8f", $new);

		if($new0 >= 0){
			$take = mysql_query("UPDATE balances SET `Amount` = '$new0' WHERE `User_ID` = '$user' AND `Wallet_ID` = '$currency';");

			if($take != null) {
				return true;
			}else{
				mysql_error($take);
			}

		}else{
			return false;
		}
	}
}

function AddMoney($amount, $user, $currency)
{
	$user_id = 0;
	if(!is_numeric($user))
	{
		$user_sql = mysql_query("SELECT * FROM userCake_Users WHERE `Username_Clean`='$user'");
		$user_id = mysql_result($user_sql,0,"User_ID");
	}
	else
	{
		$user_id = $user;
	}
	$acr = mysql_query("SELECT * FROM Wallets WHERE `Acronymn`='$currency' OR `Id`='$currency'");
	$acr_id = mysql_result($acr,0,"Id");
	$acr_acronymn = mysql_result($acr,0,"Acronymn");
    $sell = mysql_query("SELECT * FROM balances WHERE `User_ID`='$user_id' AND `Wallet_ID`='$acr_id'");
    $id   = mysql_result($sell, 0, "id") or 0;
    if ($id < 1) {
        mysql_query("INSERT INTO balances (`User_ID`,`Amount`,`Coin`,`Pending`,`Wallet_ID`) VALUES ('$user_id','$amount','$acr_acronymn','0','$acr_id');");
    } else {
        $old = mysql_result($sell, 0, "Amount");
        $new = $old + $amount;
		if($user_id != -12)
		{
			$new = sprintf("%.8f", $new);
		}
		else
		{
			$new = sprintf("%.15f",$new);
		}
		mysql_query("UPDATE balances SET `Amount` = '$new' WHERE `id`='$id'");
    }
}


function GetPosts($thread)
{
    $sql = mysql_query("SELECT * FROM TicketReplies WHERE `ticket_id` = '$thread'");
    $num = @mysql_num_rows($sql);
    $x   = 0;
    for ($i = 0; $i < $num; $i++) {
        $x = $x + 1;
    }
    return $x;
    
}



function GetUser($owner)
{
    
    $sql = mysql_query("SELECT * FROM userCake_Users WHERE `User_ID`=$owner");
    
    return mysql_result($sql, 0, "Username_Clean");
    
}


function sanitize($str)
{
    
    return strtolower(strip_tags(trim(($str))));
    
}



function isValidEmail($email)
{
    
    return preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", trim($email));
    
}



function minMaxRange($min, $max, $what)
{
    
    if (strlen(trim($what)) < $min)
        return true;
    
    else if (strlen(trim($what)) > $max)
        return true;
    
    else
        return false;
    
}



//@ Thanks to - http://phpsec.org

function generateHash($plainText, $salt = null)
{
    
    if ($salt === null) {
        
        $salt = substr(md5(uniqid(rand(), true)), 0, 25);
        
    }
    
    else {
        
        $salt = substr($salt, 0, 25);
        
    }
    
    
    
    return $salt . hash('sha512', ($salt . $plainText));;
    
}



function replaceDefaultHook($str)
{
    
    global $default_hooks, $default_replace;
    
    
    
    return (str_replace($default_hooks, $default_replace, $str));
    
}



function getUniqueCode($length = "")
{
    
    $code = md5(uniqid(rand(), true));
    
    if ($length != "")
        return substr($code, 0, $length);
    
    else
        return $code;
    
}



function errorBlock($errors)
{
    
    if (!count($errors) > 0) {
        
        return false;
        
    }
    
    else {
        
		echo '<center><ul class="nobullets">';
        
        foreach ($errors as $error) {
            
            echo '<li style="color: red;">' . $error . '</li>';
            
        }
        echo '</ul></center>';
        
    }
    
}

function successBlock($successes)
{
    
    if (!count($successes) > 0) {
        
        return false;
        
    }
    
    else {
        
		echo '<center><ul class="nobullets">';
        
        foreach ($successes as $success) {
            
            echo '<li style="color: green;">' . $success . '</li>';
            
        }
        echo '</ul></center>';
        
    }
    
}



function lang($key, $markers = NULL)
{
    
    global $lang;
    
    
    
    if ($markers == NULL) {
        $str = $lang[$key];
    }else {
        
        //Replace any dyamic markers
        
        $str = $lang[$key];
        
        
        
        $iteration = 1;
        
        
        
        foreach ($markers as $marker) {
            
            $str = str_replace("%m" . $iteration . "%", $marker, $str);
            
            
            
            $iteration++;
            
        }
        
    }
    
    
    
    //Ensure we have something to return
    
    if ($str == "") {
        
        return ("No language key found");
        
    }else {
        
        return $str;
        
    }
    
}



function destorySession($name)
{
    
    if (isset($_SESSION[$name])) {
        
        $_SESSION[$name] = NULL;
        
        
        
        unset($_SESSION[$name]);
        
    }
    
}

function getIP()
{
    foreach (array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ) as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
}

function isTORnode() {
	$ipvart = getIP(); //this is the users ip address we are testing
	$filename = "models/torlist.csv"; //the list of tor nodes
	$data = file_get_contents($filename); //get the list
	if(strpos($data,$ipvart) != false)
	return true;
	else
	return false;
}

function isIPbanned() {
	$ipvars = mysql_real_escape_string(getIP());
	$sqlxyzr = mysql_query("SELECT * FROM bantables_ip WHERE `ip`='$ipvars'");
	if (mysql_num_rows($sqlxyzr) > 0) {
		return true;
	}else{
		return false;
	}
}

function strip_tags_recursive( $str, $allowable_tags = NULL )
{
    if ( is_array( $str ) )
    {
        $str = array_map( 'strip_tags_recursive', $str, array_fill( 0, count( $str ), $allowable_tags ) );
    }
    else
    {
        $str = strip_tags( $str, $allowable_tags );
    }
    return $str;
} 

/*begin configuration functions*/

//https
function forceSSL() {
	if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] !== "on") {
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
		exit();
	}
}

//registration
function isRegistrationDisabled() {
	$sqlxzfk = mysql_query("SELECT * FROM  `config` WHERE  `name` = 'registration' LIMIT 1");
	while($row = mysql_fetch_assoc($sqlxzfk)) {
		if($row['setting'] == 1){
			return true;
		}else{
			return false;
		}
	}
}
function display_Reg_message() {
	$registration_message = "Registrations are currently disabled.</br>However you can login with a test username if you like.</br><h4>Test Users</h4><h5>format: user | pass </h5>";
	$testnamepair1 ="<h6>testuser | password</h6>";
	$testnamepair2 ="<h6>testmod | password</h6>";
	echo $registration_message;
	echo "<pre>";
	echo $testnamepair1;
	echo $testnamepair4;
	echo $testnamepair2;
	echo $testnamepair3;
	echo "</pre>";
}
//login
function isLoginDisabled() {
	$sqlxzfk = mysql_query("SELECT * FROM `config` WHERE `name` = 'login' LIMIT 1");
	while($row = mysql_fetch_assoc($sqlxzfk)) {
		if($row['setting'] == 1){
			return true;
		}else{
			return false;
		}
	}
}
//deposit
function isDepositDisabled() {
	$sqlxzfkn = mysql_query("SELECT * FROM `config` WHERE `name` = 'deposit' LIMIT 1");
	while($row = mysql_fetch_assoc($sqlxzfkn)) {
		if($row['setting'] == 1){
			return true;
		}else{
			return false;
		}
	}
}
//withdraw
function isWithdrawalDisabled() {
	$sqlxzfkg = mysql_query("SELECT * FROM `config` WHERE `name` = 'withdrawal' LIMIT 1");
	while($row = mysql_fetch_assoc($sqlxzfkg)) {
		if($row['setting'] == 1){
			return true;
		}else{
			return false;
		}
	}
}

//maintenance
function isMaintenanceDisabled() {
	$sqlxzfkgg = mysql_query("SELECT * FROM `config` WHERE `name` = 'maintenance' LIMIT 1");
	while($row = mysql_fetch_assoc($sqlxzfkgg)) {
		if($row['setting'] == 1){
			return false;
		}else{
			return true;
		}
	}
}
function isAdminOnline() {
	$sqlxzfkgdd = mysql_query("SELECT * FROM `config` WHERE `name` = 'admin' LIMIT 1");
	while($row = mysql_fetch_assoc($sqlxzfkgdd)) {
		if($row['setting'] == 1){
			return false;
		}else{
			return true;
		}
	}
}
function generateKey($id) {
$api_select = mysql_query("SELECT * FROM userCake_Users WHERE `User_Id`='$id'");
while($row = mysql_fetch_assoc($api_select)) {
		$user = $row["Username"];
		$pass = $row["Password"];
		$length = 128;
		$time = date("F j, Y, g:i a");
		$salt1 = $time . hash('sha512', (sha1 .$time));
		$salt2 = substr(md5(uniqid(rand(), true)), 0, 25);
		$salt3 = substr(md5(uniqid(rand(), true)), 0, 25);
		$salt4 = hash('sha256', (md5 .$time));
		$user_hash = hash('sha512', ($salt2 . $user . $salt1));
		$pass_hash = hash('sha512', ($salt1 . $pass . $salt2));
		$keyhash_a = hash('sha512', ($user_hash . $salt3));
		$keyhash_b = hash('sha512', ($pass_hash . $salt4));
		$hash_a = str_split($keyhash_a);
		$hash_b = str_split($keyhash_b);
		foreach($hash_a as $key => $value) {
			$hashed_a[] = $salt2 . hash('sha512', ($salt3 . $value)) . $salt1 . hash('sha256', ($salt4 . $key));
		}
		foreach($hash_a as $key => $value) {
			$hashed_b[] = $salt2 . hash('sha512', ($salt3 . $value)) . $salt1 . hash('sha256', ($salt4 . $key));
		}
		$hash_merge = array_merge($hashed_b, $hashed_a);
		$from_merge = implode($hash_merge);
		$exploded_2 = str_split($from_merge);
		$key_hash_last = implode($exploded_2);
		$key0 = str_shuffle($key_hash_last);
		$key1 = str_split($key0);
		$key2 = array_unique($key1);
		$key3 = implode($key2);
		$key4 = str_shuffle($key3);
		$key5 = str_shuffle($key4);
		$api_key0 = str_shuffle($key3.$key4.$key5.$key2);
		$keyf = mysql_real_escape_string($api_key0);
	}

	return $keyf;
}
?>
