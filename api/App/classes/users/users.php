<?php
class User extends Builder
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
	
	public function login()
    {
		$data = json_decode(file_get_contents("php://input"));
		
        $email = $this->valid_input($data->email);
        $password = $data->password;
		
		$check_exist = $this->select( "users", " email='".$email."' " );

		if($check_exist['validate']==='empty'){
			
			$response['validate'] = 'false';
			$response['message'] = "This email is not registered";
			return $response;
		}
		else{
			if(password_verify($password, $check_exist['data'][0]['password'])) {
				$user_id = $check_exist['data'][0]['id'];
				$username = $check_exist['data'][0]['username'];
				$first_name = $check_exist['data'][0]['first_name'];
				$last_name = $check_exist['data'][0]['last_name'];
				$email = $check_exist['data'][0]['email'];
				$avtar = $check_exist['data'][0]['avtar'];
				
				$user_token = $this->getToken(32);
				
				$insert_values = " user_id = '".$user_id."', token='".$user_token."' ";
				$flag = $this->insert("sessions", $insert_values);
				
				if($flag){
					$response['user_id'] = $user_id;
					$response['username'] = $username;
					$response['first_name'] = $first_name;
					$response['last_name'] = $last_name;
					$response['email'] = $email;
					$response['avtar'] = $avtar;
					
					$response['user_token'] = $user_token;
					$response['validate'] = 'true';
					$response['message'] = 'Login Success';
				}else{
					$response['validate'] = 'false';
					$response['message'] = 'Server is not responding at the moment. Please try again later.';
				}
				
			} 
			else {
				$response['validate'] = 'false';
				$response['message'] = 'Incorrect Password.';
				
			}
			
			
			
			return $response;
		}
    }
	
	public function logout()
    {
		$data = json_decode(file_get_contents("php://input"));
		
        $user_id = $this->valid_input($data->user_id);
		$token = $this->valid_input($data->token);
		
		$flag = $this->update("sessions", " is_logout = 1", " user_id=".$user_id." AND token = '".$token."'" );
		$response['validate'] = 'true';
		return $response;
    }
	
	public function register()
    {
		$data = json_decode(file_get_contents("php://input"));
		
        $first_name = ucwords($this->valid_input($data->firstName));
        $last_name = ucwords($this->valid_input($data->lastName));
        $email = $this->valid_input($data->email);
        $password = $data->password;
        $cpassword = $data->confirmPassword;
		
		if($password !== $cpassword)
		{
			$response['validate'] = 'password';
			$response['message'] = "Password does not match";
			return $response;
		}
		$check_exist = $this->select("users", " email='".$email."' ");

		if($check_exist['validate']==='empty'){
			
			$options = [
				'cost' => 12,
			];
			$password = password_hash($password, PASSWORD_BCRYPT, $options);
			
			$insert_values = " first_name = '".$first_name."', last_name='".$last_name."', email='".$email."', password='".$password."' ";
			$flag = $this->insert("users", $insert_values);
			
			if($flag)
			{
				
				$msg = "
				<p><b>Hi ".$first_name.",</b></p>"
				.'<p>First off, I’d like to extend a warm welcome. I recognize that your time is valuable and I’m seriously flattered that you join us.</p>'
				."<p>Wordsire is a one who loves to play around with words and masters it thereby showing her creativity in her writings. A good writer starts with a good reading. I hope you enjoy this greatful journey with us!</p>"
				."<p>In the meantime, I’d love to hear from you about what you’re interested in reading or writing about. So long as you reply to this email, I promise I will too.</p>"
				."<p>If you need anything, please feel free to give me a shout at support@wordsire.com.</p>"
				."<p>Again, welcome!</p>";
		
				$status = $this->mail->send_mail($email, $first_name, 'Welcome Aboard', $msg);
				
				$response['validate'] = 'true';
				$response['message'] = 'Registration Success';
			}else{
				$response['validate'] = 'false';
				$response['message'] = 'Registration Failed';
			}
			
			return $response;
		}
		else{
			$response['validate'] = 'exist';
			$response['message'] = "This email is already registered.";
			return $response;
		}
    }
	
	public function reset_code()
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

	public function reset_password()
	{
		$data = json_decode(file_get_contents("php://input"));
		
		$email = $this->valid_input($data->email);
		$code = $this->valid_input($data->code);
		$password = $this->valid_input($data->password);
		
		$sql= 	" SELECT U.id FROM pr_users U "
				." JOIN pr_password_reset PR ON U.id=PR.user_id "
				." WHERE U.email='".$email."' AND PR.reset_code='".$code."' ";

		$check_exist = $this->custom($sql);
		if($check_exist->num_rows>0){
			$userData = mysqli_fetch_object($check_exist);
			$user_id = $userData->id;
			
			$options = ['cost' => 12];
			$password = password_hash($password, PASSWORD_BCRYPT, $options);
			
			$flag = $this->update("users", " password = '".$password."' ", " id=".$user_id );
			
			if($flag){
				$response['validate'] = "true";
				$response['message'] = "Your password has been reset successfully! Login to your account now with your new password.";
			}else{
				$response['validate'] = "false";
				$response['message'] = "Unable to connect to server. Please try again later.";
			}
		}else{
			$response['validate'] = "false";
			$response['message'] = " Incorrect email or reset code. Please check your email for reset code.";
		}
		
		
		return $response;
	
	}

}
?>
