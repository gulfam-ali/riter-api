<?php
class Logout extends Builder
{
    public $config;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;
  	}

    public function logout()
    {
		$data = json_decode(file_get_contents("php://input"));
		
        $user_id = $this->valid_input($data->user_id);
		$token = $this->valid_input($data->token);
		
		$flag = $this->update("sessions", " is_logout = 1", " user_id=".$user_id.", token = '".$token."'" );
		$response['validate'] = 'true';
		return $response;
		}
    }
}
