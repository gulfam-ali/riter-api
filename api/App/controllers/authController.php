<?php
require_once(CONTROLLER_PATH ."/controller.php");
class authController extends controller{

	private $username = "";
	private $password = "";
	public $data = "";
	public $classPath = "";
	public $verb = "";
	public $className = "";
	public $mobile_no = "";


	public function __construct() {

	}

	public function call($db, $method){
		$requestUrl = array();
		if(isset($_REQUEST['requestUrl'])){
			$requestUrl = explode('/', $_REQUEST['requestUrl']);
		}
		//Get function name according to request type
		switch($method) {
			case 'PUT':
			  break;

			case 'DELETE':
			  break;

			case 'GET':
				$validate_token = $this->validate_token();
				if($validate_token === false){
					// return $this->response(json_encode(array("status"=>"500", "status_message"=>"invalid token")), 500);
				}
				$user_type = 0;
				if(isset($requestUrl[0])){
					if(isset($requestUrl[1]) && trim($requestUrl[1]) != ''){
						if(trim(strtolower($requestUrl[1])) == 'logout'){
							return $this->logoutUser();
						}
					}
				}
				else{
					return array();
				}
				break;

			case 'POST':
				/* Password recovery */
				if(isset($requestUrl[0])){
					if(isset($requestUrl[1]) && trim($requestUrl[1]) == 'recoverPassword'){
						$data = json_decode(file_get_contents("php://input"));
						$this->username = $data->username;
						if(isset($this->username) &&  trim($this->username) != ''){
							$this->username = $this->username;
							return $this->recoverUserDetail($db, $this->username);
						}
					}
				}

				/* Login User */
				if(isset($requestUrl[1]) && trim($requestUrl[1]) != ''){
					if(trim($requestUrl[1]) == 'auth'){
						$data = json_decode(file_get_contents("php://input"));
						$this->username = $data->username;
						$this->password = $data->password;
						return $this->authUser($db, $this->username, $this->password);
					}
				}
					/* if all conditions fail */
					return $this->response(json_encode(array("invalid request" => "404")), 404);
				break;

			default:
			  header('HTTP/1.1 405 Method Not Allowed');
			  header('Allow: GET, PUT, DELETE');
			  break;
		}
	}

	function authUser($db, $username = '', $password = ''){
		require_once((CLASS_PATH . '/admin/users.php'));
		$userClass = new Users;
		return $userClass->authUser($db, $username, $password);
	}

	function recoverUserDetail($db, $username = ''){
		require_once((CLASS_PATH . '/admin/users.php'));
		$userClass = new Users;
		return $userClass->recoverUserDetail($db, $username);
	}

	function logoutUser(){
		session_destroy();
		return true;
	}

}
?>
