<?php
require_once(CONTROLLER_PATH ."/controller.php");
class profileController extends controller{

	public $postClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/profile/profile.php'));
		$this->profileClass = new Profile($db);
		
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
				if(trim($requestUrl[1]) == 'like')
				{
					return $this->postClass->like();
				}
			}else{
				return $this->profileClass->view();
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
