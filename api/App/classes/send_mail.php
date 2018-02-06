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

  function send_mail($recipient, $recipient_name, $is_html, $subject, $msg, $altBody)
  {
    $mail = $this->init_mailer($this->mail, $recipient, $recipient_name);

    $header = '
	<!DOCTYPE html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Wordsire</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body >
		<div style="background: #fff; padding: 0em;">
		<table style="max-width: 600px; margin:auto;background: #fff; padding: 2em;border-radius: 0.2em;">
			<tbody>
				<tr>
					<td>
						<span style="color: #cc0000;font-weight: bold;border-radius: 0.15em;font-size: 1.6em;">
							wordsire
						</span>
					</td>
				</tr>
				<tr>
					<td style="padding-bottom: 1em;">
						<p>Dear Gulfam Ali</p>
						<p>Thank you for becoming the part of wordsire. Your account is created.</p>
						<p>Please activate your account by clicking this link below,</p>
						
						<p>Regards<br>
							The Wordsire Team
						</p>
						
					</td>
				</tr>
				<tr>
					<td>
						<p>The Wordsire Team</p>
					</td>
				</tr>
			</tbody>
		</table>
		</div>
    </body>
	
	';
	  
	  
	  
    $msg = '<div style="padding:1.5em 0.5em 1em 0.5em; font-size:1.1em; font-family: Helvetica;"> '.$msg.' </div>';
    $footer = '<div style="padding:0.2em; color: #666; background: #f0f0f0;"><p>Thanks<br>National Housing Bank Team</p></div> </div></div></body></html>';

    $mail->Subject = $subject;
    $mail->Body    = $header;
    $mail->AltBody = $altBody;

    $mail->isHTML($is_html);
	
	try{
		$mail->send();
	}
	catch (phpmailerException $e) {
		echo $e->errorMessage(); //Pretty error messages from PHPMailer
	} catch (Exception $e) {
		echo $e->getMessage(); //Boring error messages from anything else!
	}
	
	
    if(!$mail->send()) {
        $mailSent = 0;
        //echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        $mailSent = 1;

      //  echo 'Message has been sent';
    }
	var_dump($mail);
	die;
    return $mailSent;
  }

}
