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
		
		
		$check_exist = $this->select( "users", " email='".$email."' " );
		if($check_exist['validate']==='empty'){
			
			$response['validate'] = 'false';
			$response['message'] = "This email is not registered";
		}
		else{
			$user_id = $check_exist['data'][0]['id'];
			$first_name = $check_exist['data'][0]['first_name'];
			$last_name = $check_exist['data'][0]['last_name'];
			$full_name = $first_name.' '.$last_name;
			$code = mt_rand(21212, 98989);
			
			$msg = '
			<p><b>Hi '.$first_name.',</b></p>'
			.'<p>To change your account password, enter the following reset code in the box provided in your browser:</p>'
			.'<p style="font-size: 2em; color: #888; font-weight: bold;">'.$code.'</p>'
			.'<p>This reset code will expire in 1 hour, so be sure to use it right away.</p>';
	
			$status = $this->mail->send_mail($email, $full_name, 'Password Reset Code', $msg);
			
			if($status){
				$insert_values = " user_id = '".$user_id."', reset_code='".$code."' ";
				$flag = $this->insert("password_reset", $insert_values);
				
				if($flag){
					$response['validate'] = 'true';
					$response['message'] = " Password reset code successfully sent to your email address.";
				}else{
					$response['validate'] = 'false';
					$response['message'] = " Something went wrong. Please try again later.";
				}
			}
			else{
				$response['validate'] = 'false';
				$response['message'] = " Something went wrong. Please try again later.";
			}
		}
		
		return $response;
    }
}
