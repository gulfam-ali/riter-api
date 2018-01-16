<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, userId');
date_default_timezone_set('Asia/Kolkata');
?>
<?php

require_once("Rest.inc.php");
require_once(CLASS_PATH . "/builder.php");

class API extends REST {

	public $data = "";
	public $verb = "";
	public $className = "";
	public $task = "";
	public $url_elements = "";
	public $allow = true;

	private $dbRead = NULL;
	private $dbWrite = NULL;

	public function __construct(){
		parent::__construct(); 
		$this->verb = $_SERVER['REQUEST_METHOD'];
		if(isset($_REQUEST['requestUrl'])){
			$this->url_elements = explode('/', $_REQUEST['requestUrl']);
		}
		$this->dbConnect();
	}

	//Database connection
	private function dbConnect(){
		global $config;
		$config = (object) $config;
		require_once(CLASS_PATH . "/database.php");
		$dbConnection = new DbConnect;

		$this->dbRead = $dbConnection->connectDbRead(); // For read data
		$this->dbWrite = $dbConnection->connectDbWrite(); // For write data
		
		$this->check_permission($this->dbRead);
	}
	
	//Blocking Request if User not logged in
	public function check_permission($db){
		require_once(CLASS_PATH . "/check_permit.php");
		$permissionClass = new UserProceed;

		$result = $permissionClass->validate_token($db);
		$this->allow = $result;
	}


	//Public method for access api.
	//This method dynmically call the method based on the query string
	public function processApi(){
		global $config;
		if((isset($_GET['page'])) &&(isset($_GET['itemsPerPage'])) ){

			if(($_GET['page']) > 1){
				$offset = $_GET['itemsPerPage'] * ($_GET['page'] - 1) ;
			}else{
				$offset = 0 ;
			}
			$config->offset = $offset;
			$config->itemPerpage = $_GET['itemsPerPage'];
		}else{
			$config->offset = 0;
			$config->itemPerpage = 18446744;
		}

		if(isset($_REQUEST['requestUrl'])){
			if(isset($this->url_elements[0]) && trim($this->url_elements[0]) != ''){
				$this->className = $this->url_elements[0];
			}

			if(file_exists(CONTROLLER_PATH . '/' . strtolower($this->className) .'Controller.php')){
				
					//Check if user logged in or not
					if(!$this->allow)
					{
						$response['validate'] = 'false';
						$response['message'] = 'Token mismatch';
						return $this->response($this->json($response), 200);
					}
				
					require_once(CONTROLLER_PATH . '/' . strtolower($this->className) .'Controller.php');
					$className = $this->className . 'Controller';
					$obj = new $className($this->dbRead);

					if((int)method_exists($obj,'call') > 0){
						$rs = $obj->call($this->dbRead, $this->verb);
						
						if(is_bool($rs)){
							if($rs === true){
								return $this->response($this->json(array('validate' => 'true')), 200);
							} else{
								return $this->response($this->json(array('validate' => 'false')), 200);
							}
						} else{
							$result = array();
							if(is_array($rs)){
								$result = $rs;
							} else{
								while($row = mysqli_fetch_assoc($rs)){
									$result[] = $row;
								}
								$result = array('data'=>$result);
							}
						}
						// If success everythig is good send header as "OK" and return list of users in JSON format
						return $this->response($this->json($result), 200);
					} else{
						return $this->response('',404);
					}
			} else{
				return $this->response('',500);
			}
		} else{
			return $this->response('',403);
		}

	// If the method not exist with in this class, response would be "Page not found".
	}

	//Encode array into JSON
	private function json($data){
		if(is_array($data)){
			return json_encode($data);
		}
	}
}

// Initiiate Library
$api = new API;
$api->processApi();
?>
