<?php

require_once("settings.php");
require_once("db/".$dbtype.".php");
$db = new $sql_db();
if(is_array($db->sql_connect($db_host, $db_user, $db_pass, $db_name, $db_port, false, false))) {
	die("Unable to connect to the database");
}
if(!isset($language)) $langauge = "en";
require_once("lang/".$langauge.".php");
require_once("jsonRPCClient.php");
require_once("class.user.php");
require_once("class.mail.php");
require_once("funcs.user.php");
require_once("funcs.general.php");
require_once("class.newuser.php");
include("class.wallet.php");

session_start();
if(isset($_SESSION["userCakeUser"]) && is_object($_SESSION["userCakeUser"])) {
	$loggedInUser = $_SESSION["userCakeUser"];
}

?>
