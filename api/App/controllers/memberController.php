<?php
require_once(CONTROLLER_PATH ."/controller.php");
class memberController extends controller{

	public $memberClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/member/member.php'));
		$this->memberClass = new Member($db);
		
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
				if(trim($requestUrl[1]) == 'follow')
				{
					return $this->memberClass->follow();
				}
				else if(trim($requestUrl[1]) == 'profile')
				{
					return $this->memberClass->profile();
				}
				else if(trim($requestUrl[1]) == 'stories')
				{
					return $this->memberClass->stories();
				}
			}else{
				return $this->memberClass->view();
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
