<?php
class Mail
{

    public $host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
  	public $smtp_secure = "tls";
  	public $smtp_auth = "true";
  	public $username = "digitalindiacware@gmail.com";
  	public $password = 'Q!W@E#R$';
  	public $port = "587";
    public $from_email = "gulfam@computerware.in";
    public $from_name = "NHB SMS Portal";
    public $reply_email = "gulfam@computerware.in";
    public $reply_name = "NHB SMS Portal";
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

    $header = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
     <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>NHB SMS Portal</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    </head>
    <body style="text-align:center; font-size: 1.2em;">
    <div>
    <div tyle="background: #fff; border:0.1pt solid #ddd; margin: 0.5em 1em; border: 0.1pt solid #e2e2e2;">
      <div style="padding:0.4em; background: #7a8ba7;font-size: 2.5em;">
        <div style="padding:0em 0.2em 0 0.2em;"><img style="width: 260px;" src="https://nhb.org.in/wp-content/themes/slider/images/logo.png" ></div>
      </div>';
    $msg = '<div style="padding:1.5em 0.5em 1em 0.5em; font-size:1.1em; font-family: Helvetica;"> '.$msg.' </div>';
    $footer = '<div style="padding:0.2em; color: #666; background: #f0f0f0;"><p>Thanks<br>National Housing Bank Team</p></div> </div></div></body></html>';

    $mail->Subject = $subject;
    $mail->Body    = $header.$msg.$footer;
    $mail->AltBody = $altBody;

    $mail->isHTML($is_html);

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
