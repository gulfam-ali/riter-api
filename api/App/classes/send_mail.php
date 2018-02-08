<?php
class Mail
{
    public $host = 'server125.web-hosting.com';  // Specify main and backup SMTP servers
  	public $smtp_secure = "ssl";
  	public $smtp_auth = "true";
  	public $username = "info@wordsire.com";
  	public $password = '9672555281';
  	public $port = "465";
    public $from_email = "info@wordsire.com";
    public $from_name = "Wordsire";
    public $reply_email = "info@wordsire.com";
    public $reply_name = "Wordsire";
    public $html = true;
    public $logo_path = '';

    private $mail = NULL;

    public function __construct(){
        //require 'phpmailer/PHPMailerAutoload.php';
        require_once(LIBRARY_PATH . '/PHPMailer-master/class.phpmailer.php');
		    require_once(LIBRARY_PATH . '/PHPMailer-master/class.smtp.php');
        $this->mail = new PHPMailer;

        $logo_dir = getcwd();
		    $this->logo_path = $logo_dir."/media/images/logo.jpg";

	   }

  private function init_mailer($mail, $recipient, $recipient_name){
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->isSMTP();
    //$mail->SMTPDebug = 3;
    $mail->SMTPSecure = $this->smtp_secure;                            // Enable TLS encryption, `ssl` also accepted            // Set mailer to use SMTP
    $mail->Host = $this->host;
    $mail->SMTPAuth = $this->smtp_auth;                               // Enable SMTP authentication
    $mail->Username = $this->username;                 // SMTP username
    $mail->Password = $this->password;                           // SMTP password
    $mail->Port = $this->port;                                    // TCP port to connect to

    $mail->setFrom($this->from_email, $this->from_name);
    $mail->addAddress($recipient, $recipient_name);
    $mail->addReplyTo($this->reply_email, $this->reply_email);
    return $mail;
	}

  function send_mail($recipient, $recipient_name, $subject, $msg)
  {
    $mail = $this->init_mailer($this->mail, $recipient, $recipient_name);
	$mail->Subject = $subject;
	
    $mail->Body = '
	<!DOCTYPE html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Wordsire</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<link href="https://fonts.googleapis.com/css?family=Berkshire+Swash" rel="stylesheet">
		<style>
			p{
				font-family: Verdana, Geneva, sans-serif;
				font-family: 1.1em;
				color: #666;
			}
		</style>
    </head>
    <body >
		<div style="background: #fff; padding: 0em;">
		<table style="max-width: 600px; margin:auto;background: #fff; padding: 2em;border-radius: 0.2em;">
			<tbody>
				<tr>
					<td>
						<span style="color: #ee0000;font-weight: bold; border-radius: 0.15em; font-size: 2em;">
							<img src="https://wordsire.com/email_logo.png" alt="Wordsire" style="max-height: 40px;    margin-left: -0.5em; ">
						</span>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 1em; font-size: 1.2em; color: #666;">'
						.$msg
						.'<p style="color: #999;">
							Thank you for using Wordsire!<br>
							The Wordsire Team
						</p>
						
					</td>
				</tr>
			</tbody>
		</table>
		</div>
    </body>';
    
    $mail->AltBody = $msg;

    $mail->isHTML(true);
	
	/*
	try{
		$mail->send();
	}
	catch (phpmailerException $e) {
		echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
		echo $e->getMessage(); //Boring error messages from anything else!
	}
	*/
	
    if(!$mail->send()) {
        $mailSent = 0;
        //echo 'Message could not be sent.';
        //echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $mailSent = 1;

      //  echo 'Message has been sent';
    }
	
	return $mailSent;
  }

}
