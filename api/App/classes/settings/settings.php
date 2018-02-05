<?php
class Settings extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->items;
  	}

    public function change_email()
	{
		
		$data = json_decode(file_get_contents("php://input"));
		
		$user_id = (int)$this->valid_input($data->user_id);
        $email = $this->valid_input($data->email);
        $password = $data->password;
		
		$check_exist = $this->select( "users", " id='".$user_id."' " );

		if($check_exist['validate']!='empty'){
			
			if(password_verify($password, $check_exist['data'][0]['password'])) {
				$update_values = " email = '".$email."', email_verified = 0 ";
				$flag = $this->update("users", $update_values, " id='".$user_id."' ");
				
				if($flag){
					$response['validate'] = 'true';
					$response['message'] = 'Primary email changed successfully';
				}else{
					$response['validate'] = 'false';
					$response['message'] = 'Email cannot be changed at this moment. Please try again later. If this issue persists, then contact our support team at support@wordsire.com';
				}
			}else{
				$response['validate'] = 'false';
				$response['message'] = 'Incorrect Password';
			}
		}else{
			$response['validate'] = 'false';
			$response['message'] = 'Email cannot be changed at this moment. Please try again later. If this issue persists, then contact our support team at support@wordsire.com';
		}
		
		return $response;
		
	}
}
