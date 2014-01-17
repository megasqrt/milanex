<?php
	//Application Db
	$dbtype = "mysql"; 
	$db_host = "";
	$db_user = "";
	$db_pass = "";
	$db_name = "";
	$db_port = "";
	$db_table_prefix = "userCake_";
	$langauge = "en";

	

	//Generic website variables
	$websiteName = "OpenEx";
	$websiteUrl = ""; //including trailing slash
	$emailActivation = false;
	$resend_activation_threshold = 24;
	$emailAddress = "";
	$emailDate = date("l \\t\h\e jS");
	$mail_templates_dir = "models/mail-templates/";
	$default_hooks = array("#WEBSITENAME#","#WEBSITEURL#","#DATE#");
	$default_replace = array($websiteName,$websiteUrl,$emailDate);
	$debug_mode = true;
	//---------------------------------------------------------------------------

?>
