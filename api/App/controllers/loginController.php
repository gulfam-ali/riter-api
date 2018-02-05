<?php
require_once(CONTROLLER_PATH ."/controller.php");
class loginController extends controller{

	public $loginClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/users/login.php'));
		$this->loginClass = new Login($db);
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
			if(isset($requestUrl[0])){
			  if(trim($requestUrl[0]) == 'login')
			  {
				return $this->loginClass->login();
			  }
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