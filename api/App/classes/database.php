<?php
class DbConnect{
	
	public function __construct() {
	
	}
	
	function connectDbRead(){
		global $config;
		$config = (object) $config;
		$dbConnections = (object) $config->db;
		
		$dbRead = mysqli_connect($dbConnections->dbRead['host'], $dbConnections->dbRead['username'],$dbConnections->dbRead['password'],$dbConnections->dbRead['dbname']);
		return $dbRead;
	}
	
	function connectDbWrite(){
		global $config;
		$config = (object) $config;
		$dbConnections = (object) $config->db;
		
		$dbWrite = mysqli_connect($dbConnections->dbWrite['host'], $dbConnections->dbWrite['username'],$dbConnections->dbWrite['password'],$dbConnections->dbWrite['dbname']);
		return $dbWrite;
	}
	
	function executeQuery($type = 'Write', $query){
		
	}
	
}
?>