<?php
require_once(CONTROLLER_PATH ."/controller.php");
class registerController extends controller{

	public $contactClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/users/register.php'));
		$this->registerClass = new Register($db);
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
			  if(trim($requestUrl[0]) == 'register')
			  {
				return $this->registerClass->register();
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