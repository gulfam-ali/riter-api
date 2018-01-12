<?php
class Register extends Builder
{
    public $config;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;
  	}

    public function register()
    {
		$data = json_decode(file_get_contents("php://input"));
		
        $first_name = $this->valid_input($data->firstName);
        $last_name = $this->valid_input($data->lastName);
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
}
