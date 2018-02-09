<?php

class UserProceed{

	private $userId;
	public $url_elements = array();
	public $builderClass;
	private $protectedController = array( "post", "profile", "user", "feed", "settings"); //These need permission
	public $config;
	
	public function __construct( ) {
		
		$headers = getallheaders();
		if(isset($headers['userId'])){
			$this->userId = $headers['userId']; 
		}
		
		if(isset($_REQUEST['requestUrl'])){
			$this->url_elements = explode('/', $_REQUEST['requestUrl']);
		}
	}
	
	public function validate_token($db){
		
		if( in_array($this->url_elements[0], $this->protectedController) )
		{
			require_once((CLASS_PATH . '/builder.php'));
			$this->builderClass = new Builder();
			$this->builderClass->db = $db;
			
			if($_SERVER["CONTENT_TYPE"] == 'application/json'){
				$data = json_decode(file_get_contents("php://input"));
				if( isset($data->user_id) && isset($data->token) )
				{
					$user_id = $this->builderClass->valid_input($data->user_id);
					$token = $this->builderClass->valid_input($data->token);
				}else{
					return false;
				}
			}else{
				$user_id = $this->builderClass->valid_input($_POST['user_id']);
				$token = $this->builderClass->valid_input($_POST['token']);
			}
			
			
			if( isset($user_id) && isset($token) )
			{
				$check = $this->builderClass->custom("sessions", " user_id=".$user_id." AND token='".$token."' ");
				
				if($check['validate'] == 'empty')
				{
					return false;
				}else{
					return true;
				}
			}else{
				return false;
			}
		}else{
			return true;
		}
	}
	
}
?>
