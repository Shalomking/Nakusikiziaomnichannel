<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MVC_Library_Mail
{
	public function send($vars, $email, $template, &$smarty)
	{
		$mail = new PHPMailer();

		try {
			$smarty->assign($vars);

			if(system_mail_function > 1):
				$mail->isSMTP();
				$mail->SMTPAuth = true;
				$mail->SMTPSecure = system_smtp_secure < 2 ? "tls" : "ssl";
				$mail->Host = system_smtp_host;
				$mail->Port = system_smtp_port; 
				$mail->Username = system_smtp_username;
				$mail->Password = system_smtp_password;
			endif;

			$mail->CharSet = "UTF-8";
			$mail->Encoding = "base64";

			$mail->Subject = mail_title($vars["data"]["subject"]);
		    $mail->setFrom(system_site_mail, $vars["title"]);
		    $mail->addAddress($email);
		    $mail->isHTML(true);  
		    $mail->msgHTML($smarty->fetch("{$template}"));

		    $mail->send();

		    $mail->clearAllRecipients();
		} catch(Exception $e){
			return false;
		}

		return true;
	}
}