<?php
	/*
		UserCake Langauge File.
		Language: English.
		Author: Adam Davis
		http://adamdavis.co.uk
	*/
	/*
		%m1% - Dymamic markers which are replaced at run time by the relevant index.
	*/
	$lang = array();
	//Account
	$lang = array_merge($lang,array(
		"ACCOUNT_SPECIFY_USERNAME" 				=> "请输用户名",
		"ACCOUNT_SPECIFY_PASSWORD" 				=> "请输用户密码",
		"ACCOUNT_SPECIFY_EMAIL"					=> "请输电子邮箱",
		"ACCOUNT_INVALID_EMAIL"					=> "错误的邮箱地址",
		"ACCOUNT_INVALID_USERNAME"				=> "错误的用户名",
		"ACCOUNT_USER_OR_EMAIL_INVALID"			=> "用户名或邮箱地址输入错误",
		"ACCOUNT_USER_OR_PASS_INVALID"			=> "用户名或密码输入错误",
		"ACCOUNT_ALREADY_ACTIVE"				=> "你的帐号已经激活",
		"ACCOUNT_INACTIVE"						=> "你的帐号等待激活，请查收邮件，并点击激活。",
		"ACCOUNT_USER_CHAR_LIMIT"				=> "帐号长度介于 %m1% ~ %m2% 之间",
		"ACCOUNT_PASS_CHAR_LIMIT"				=> "密码长度介于 %m1% ~ %m2% 之间",
		"ACCOUNT_PASS_MISMATCH"					=> "两次输入的密码要相同",
		"ACCOUNT_USERNAME_IN_USE"				=> "用户名 %m1% 已经存在，请换一个",
		"ACCOUNT_EMAIL_IN_USE"					=> "邮箱 %m1% 已经存在，请换一个",
		"ACCOUNT_LINK_ALREADY_SENT"				=> "An activation email has already been sent to this email address in the last %m1% hour(s)",
		"ACCOUNT_NEW_ACTIVATION_SENT"			=> "We have emailed you a new activation link, please check your email",
		"ACCOUNT_NOW_ACTIVE"					=> "Your account is now active",
		"ACCOUNT_SPECIFY_NEW_PASSWORD"			=> "请输入你的新密码",	
		"ACCOUNT_NEW_PASSWORD_LENGTH"			=> "新密码长度介于 %m1% ~ %m2% 之间",	
		"ACCOUNT_PASSWORD_INVALID"				=> "原始密码不匹配，请重新输入",	
		"ACCOUNT_EMAIL_TAKEN"					=> "邮箱地址已经被其它用户所使用",
		"ACCOUNT_DETAILS_UPDATED"				=> "更新帐户的详细资料",
		"ACTIVATION_MESSAGE"					=> "You will need first activate your account before you can login, follow the below link to activate your account. \n\n
													%m1%activate-account.php?token=%m2%",							
		"ACCOUNT_REGISTRATION_COMPLETE_TYPE1"	=> "你已经注册成功，现在可以登陆 <a href=\"login.php\">here</a>.",
		"ACCOUNT_REGISTRATION_COMPLETE_TYPE2"	=> "You have successfully registered. You will soon receive an activation email. 
													You must activate your account before logging in.",
	));
	//Forgot Password
	$lang = array_merge($lang,array(
		"FORGOTPASS_INVALID_TOKEN"				=> "错误的验证码",
		"FORGOTPASS_NEW_PASS_EMAIL"				=> "我们已经通过电子邮件发送您的新密码",
		"FORGOTPASS_REQUEST_CANNED"				=> "密码找回已经被你取消",
		"FORGOTPASS_REQUEST_EXISTS"				=> "本帐号已经发送了一次密码找回申请邮件",
		"FORGOTPASS_REQUEST_SUCCESS"			=> "我们已经通过电子邮件发送您如何重新访问您的帐户的说明",
	));
	//Miscellaneous
	$lang = array_merge($lang,array(
		"CAPTCHA_FAIL"							=> "失败的验证码",
		"FAIL_MINIMUM"							=> "最小提现金额为 0.01BTC or 1 MLC",
		"INVALID_AMOUNT"						=> "没有输入数量",
		"N_A_N"									=> "输入的不是数值!",
		"INS_FUNDS"								=> "资金不足",
		"CONFIRM"								=> "确认",
		"DENY"									=> "取消",
		"SUCCESS"								=> "成功",
		"ERROR"									=> "失败",
		"NOTHING_TO_UPDATE"						=> "无更新内容",
		"SQL_ERROR"								=> "数据库出错",
		"MAIL_ERROR"							=> "邮件发送失败，请联系管理员：support@milancoin.com",
		"MAIL_TEMPLATE_BUILD_ERROR"				=> "新建邮件出错",
		"MAIL_TEMPLATE_DIRECTORY_ERROR"			=> "Unable to open mail-templates directory. Perhaps try setting the mail directory to %m1%",
		"MAIL_TEMPLATE_FILE_EMPTY"				=> "Template file is empty... nothing to send",
		"FEATURE_DISABLED"						=> "此功能目前已关闭",
	));
?>
