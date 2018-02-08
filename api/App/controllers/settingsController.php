<?php
require_once(CONTROLLER_PATH ."/controller.php");
class settingsController extends controller{

	public $settingsClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/settings/settings.php'));
		$this->settingsClass = new Settings($db);
		
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
				if(trim($requestUrl[1]) == 'change-email')
				{
					return $this->settingsClass->change_email();
				}
				else if(trim($requestUrl[1]) == 'change-password')
				{
					return $this->settingsClass->change_password();
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