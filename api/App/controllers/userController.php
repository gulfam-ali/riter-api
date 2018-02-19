<?php
require_once(CONTROLLER_PATH ."/controller.php");
class userController extends controller{

	public $userClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/users/users.php'));
		$this->userClass = new User($db);
		
	}

	public function call($db, $method){
		
		$requestUrl = array();
		if(isset($_REQUEST['requestUrl'])){
			$requestUrl = explode('/', $_REQUEST['requestUrl']);
		}
		
		switch($method) {
			case 'PUT':
			  break;

			case 'DELETE':
			  break;

			case 'GET':
			break;

			case 'POST':
			if(isset($requestUrl[1])){
				if(trim($requestUrl[1]) == 'login')
				{
					return $this->userClass->login();
				}
				else if(trim($requestUrl[1]) == 'logout')
				{
					return $this->userClass->logout();
				}
				else if(trim($requestUrl[1]) == 'register')
				{
					return $this->userClass->register();
				}
				else if(trim($requestUrl[1]) == 'reset-password')
				{
					return $this->userClass->reset_password();
				}
				else if(trim($requestUrl[1]) == 'reset-code')
				{
					return $this->userClass->reset_code();
				}
				else if(trim($requestUrl[1]) == 'check-verify')
				{
					return $this->userClass->check_verify();
				}
				else if(trim($requestUrl[1]) == 'send-verify-code')
				{
					return $this->userClass->verification_code();
				}
				else if(trim($requestUrl[1]) == 'verify-email')
				{
					return $this->userClass->verify_email();
				}
				else{
					return 404;
				}
			}else{
				return 0;
			}
			break;

			default:
			  header('HTTP/1.1 405 Method Not Allowed');
			  header('Allow: GET, PUT, DELETE');
			  break;
		}
	}


}
?>
