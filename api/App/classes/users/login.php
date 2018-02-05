<?php
class Login extends Builder
{
    public $config;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;
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
				$first_name = $check_exist['data'][0]['first_name'];
				$last_name = $check_exist['data'][0]['last_name'];
				$avtar = $check_exist['data'][0]['avtar'];
				
				$user_token = $this->getToken(32);
				
				$insert_values = " user_id = '".$user_id."', token='".$user_token."' ";
				$flag = $this->insert("sessions", $insert_values);
				
				if($flag){
					$response['user_id'] = $user_id;
					$response['first_name'] = $first_name;
					$response['last_name'] = $last_name;
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
}
