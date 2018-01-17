<?php
require_once(CONTROLLER_PATH ."/controller.php");
class guestfeedController extends controller{

	public $feedClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/guestfeed/guestfeed.php'));
		$this->feedClass = new Guestfeed($db);
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
			if(isset($requestUrl[0])){
				return $this->feedClass->feed();
			}
			break;

			case 'POST':
			break;

			default:
			  header('HTTP/1.1 405 Method Not Allowed');
			  header('Allow: GET, PUT, DELETE');
			  break;
		}
	}


}
?>
