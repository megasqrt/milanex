<?php
/*
	UserCake Version: 1.4
	http://usercake.com
	Developed by: Adam Davis
*/
require_once("PHPMailerAutoload.php");
class userCakeMail {
	//UserCake uses a text based system with hooks to replace various strs in txt email templates
	public $contents = NULL;
	//Function used for replacing hooks in our templates
	public function newTemplateMsg($template,$additionalHooks)
	{
		global $mail_templates_dir,$debug_mode;
		$this->contents = file_get_contents($mail_templates_dir.$template);
		//Check to see we can access the file / it has some contents
		if(!$this->contents || empty($this->contents))
		{
			if($debug_mode)
			{
				if(!$this->contents)
				{ 
					echo lang("MAIL_TEMPLATE_DIRECTORY_ERROR",array(getenv("DOCUMENT_ROOT")));
					die(); 
				}
				else if(empty($this->contents))
				{
					echo lang("MAIL_TEMPLATE_FILE_EMPTY"); 
					die();
				}
			}
			return false;
		}
		else
		{
			//Replace default hooks
			$this->contents = replaceDefaultHook($this->contents);

			//Replace defined / custom hooks
			$this->contents = str_replace($additionalHooks["searchStrs"],$additionalHooks["subjectStrs"],$this->contents);

			//Try and find the #INC-FOOTER hook
			if(strpos($this->contents,"#INC-FOOTER#") !== FALSE)
			{
				$footer = file_get_contents($mail_templates_dir."email-footer.txt");
				if($footer && !empty($footer)) $this->contents .= replaceDefaultHook($footer); 
				$this->contents = str_replace("#INC-FOOTER#","",$this->contents);
			}
			return true;
		}
	}
	public function sendMailold($email,$subject,$msg = NULL)
	{
		global $websiteName,$emailAddress;

		$header = "MIME-Version: 1.0\r\n";
		$header .= "Content-type: text/plain; charset=iso-8859-1\r\n";
		$header .= "From: ". $websiteName . " <" . $emailAddress . ">\r\n";
		//Check to see if we sending a template email.

		if($msg == NULL)
			$msg = $this->contents; 
		$message = $msg;
		$message = wordwrap($message, 70);
		return mail($email,$subject,$message,$header);
	}

        public function sendMail($email,$subject,$msg = NULL){
                global $websiteName,$emailHost,$emailPort,$emailAddress,$emailPassword,$emailReply;

                $mail = new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPDebug;
                $mail->Host = $emailHost;
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = "ssl";
                $mail->Port = $emailPort;
                $mail->Username = $emailAddress;
                $mail->Password = $emailPassword;

                $mail->AddReplyTo($emailReply, $websiteName);
                $mail->AddAddress($email);//snd lily
                $mail->SetFrom($emailAddress, $websiteName);

                $mail->IsHTML(true);
                $mail->Subject = $subject;
//print_r($mail);
                if($msg == NULL)
                         $msg = $this->contents;
                $message = $msg;
                $message = wordwrap($message, 70);
                $mail->MsgHTML($message);
                if(!$mail->Send()) {
                       echo 'Verification mail not sent' . $mail->ErrorInfo;
                       return false;
                } else {
                       return true;
                }
        }
}
?>
