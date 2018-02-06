<?php
class RecoverPassword extends Builder
{
    public $config;
	public $mail;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;
		
		require_once((CLASS_PATH . '/send_mail.php'));
		$this->mail = new Mail();
  	}

    public function recover()
    {
		$data = json_decode(file_get_contents("php://input"));
        $email = $this->valid_input($data->email);
		
		$status = $this->mail->send_mail($email, 'Gulfam Ali', true, 'Testing Mail', 'Hello....', 'Alternating text body.');
		echo $status; die;
		$check_exist = $this->select( "users", " email='".$email."' " );

		if($check_exist['validate']==='empty'){
			
			$response['validate'] = 'false';
			$response['message'] = "This email is not registered";
			return $response;
		}
		else{
			
		}
		
		return $status;
    }
}
