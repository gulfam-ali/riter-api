<?php
class controller extends REST {

	public function __construct(){

	}

	public function validate_token(){
		/* Validate token value in session */
		$headers = getallheaders();

		session_start();
		// if(!isset($_SESSION['token']) || !isset($headers['token']) || $headers['token'] != $_SESSION['token']){
		// 	return false;
		// }
	}

}
?>
