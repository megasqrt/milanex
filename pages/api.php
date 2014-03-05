<?php
require_once ('system/csrfmagic/csrf-magic.php');
require_once("models/config.php");
$id = $loggedInUser->user_id;
if(!isUserLoggedIn()){
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=login">';
	die();
}

echo '<h1>API</h1>';
 /*check to see if user has an api key.*/
 
$api_select = mysql_query("SELECT * FROM Api_Keys WHERE `User_ID`='$id'");
if(mysql_num_rows($api_select) > 0) {
	$pkey = mysql_result($api_select, 0, "Public_Key");
	$akey = mysql_result($api_select, 0, "Authentication_Key");
	echo '<h3>Your Public Key is:</h3><br/>';
	echo $pkey;
	echo '<br/>';
	echo '<h3>Your Server Key is:</h3><br/>';
	echo $akey;
	echo '<br/>';	
	echo '<h3>API Reference and Examples</h3>';
	
	echo '<a href="ajax.php?do=getapireference">Download Reference(RTF Format)</a>';

}else{

	$topublic = generateKey($id); //public key

	$toprivate = generateKey($id); //private key

	$pub_check_no_collision = mysql_query("SELECT `Public_Key` FROM Api_Keys WHERE `Public_Key` = '$topublic'");

	$priv_check_no_collision = mysql_query("SELECT `Authentication_Key` FROM Api_Keys WHERE `Authentication_Key` = '$toprivate'");

	if(mysql_num_rows($pub_check_no_collision) > 0 ) {
		echo '<meta http-equiv="refresh" content="0; URL=index.php?page=api">';
	}else{
	
	$api_insert = mysql_query("INSERT INTO Api_Keys (`Public_Key`,`Authentication_Key`,`User_ID`) VALUES ('$topublic','$toprivate','$id')");
	echo '<meta http-equiv="refresh" content="0; URL=index.php?page=api">';
	}

}

?>