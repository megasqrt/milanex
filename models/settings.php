<?php
	//Application Db
	$dbtype = "mysql"; 
	$db_host = "localhost";
	$db_user = "username";
	$db_pass = "password";
	$db_name = "database";
	$db_port = "";
	$db_table_prefix = "userCake_";
	$langauge = "en";

	

	//Generic website variables
	$websiteName = "OpenEx";
	$websiteUrl = "http://openex.pw/"; //including trailing slash
	$emailActivation = false;
	$resend_activation_threshold = 24;
	$emailAddress = "no-reply@server.openex.pw";
	$emailDate = date("l \\t\h\e jS");
	$mail_templates_dir = "models/mail-templates/";
	$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
	$default_replace = array($websiteName,$websiteUrl,$emailDate);
	$debug_mode = true;
	//---------------------------------------------------------------------------

?>
