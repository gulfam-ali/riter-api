<?php

class CheckPermit{

	private $userId;
	public $url_elements = "";
	private $allowedController = array( "menus", "pagination", "status", "profile", "users"); //These do not need permission
	
	public function __construct( ) {
		
		$headers = getallheaders();
		if(isset($headers['userId'])){
			$this->userId = $headers['userId']; 
		}
		
		if(isset($_REQUEST['requestUrl'])){
			$this->url_elements = explode('/', $_REQUEST['requestUrl']);
		}
	}
	public function have_child($obj, $controller, $task) {
		$arr =  (array)$obj;
		foreach($arr as $key=>$val)
		{
			$id = $val->id;
			$title = $val->controller;
			$title1 = $val->title;
			$view = $val->view;
			$add = $val->add;
			$del = $val->delete;
			$tot = $view + $add + $del;
			
			//echo "\n".$title." : ".$controller;
			if($title === $controller)
			{
				if($task === 'View' && $tot==0)
				{
					return 0;
				}
				else if($task === 'Add' && $add==0)
				{
				//echo "[Matched]";
					return 0;
				}
				else if($task === 'Del' && $del==0)
				{
					return 0;
				}
				else
				{
					return 1;
				}
			}
				
			if(array_key_exists('Child', $val))
			{	
				if($this->have_child($val->Child, $controller, $task)==1)
				{
					return 1;
				}
				else if($this->have_child($val->Child, $controller, $task)==0)
					return 0;
			}
		}
		return 2;
	}
	
	public function check_permission($db, $controller, $task) {
		if(in_array($controller, $this->allowedController))
		{
			return 1;
		}
		else if($task ==='Download')
		{
			return 1;
		}
		else if($controller === 'usersgroup' && isset($this->url_elements[1]) )
		{
			if($this->url_elements[1]==='gp')
				return 1;
		}
		global $config;
		$config = (object) $config;
		$sql = "select ug.Access_modules, (SELECT fp.functions FROM di_function_permit fp WHERE fp.group_id=ug.id) as functionPermit " 
			."from ".$config->dbprefix ."user_groups ug, ". $config->dbprefix ."users  us"
			." where us.id=".$this->userId." AND us.status IN ('1','0') AND ug.id= us.user_group"; 
		
		// Check database connection
		if( ($db instanceof MySQLi) == false) {
			return array(status => false, message => 'MySQL connection is invalid');
		}

		// Construct query
	 
		$links=mysqli_query($db,$sql);
		
		
		if(mysqli_num_rows($links)>0)
		{
			$row = mysqli_fetch_array($links,MYSQL_ASSOC);
			$access = json_decode($row['Access_modules']);
			//var_dump($access);
			foreach($access as $key=>$val)
			{
				$id = $val->id;
				$title = $val->controller;
				$title1 = $val->title;
				$view = $val->view;
				$add = $val->add;
				$del = $val->delete;
				$tot = $view + $add + $del;
				
				//echo "\n".$title." : ".$controller;
				if($title === $controller)
				{
					if($task === 'view' && $tot==0)
					{
						return 0;
					}
					else if($task ==='add' && $add==0)
					{
						return 0;
					}
					else if($task === 'del' && $del==0)
					{
						return 0;
					}
					else
					{
						return 1;
					}
				}
				if(array_key_exists('Child', $val))
				{	
					if($this->have_child($val->Child, $controller, $task)==1)
					{
						return 1;
					}
					else if($this->have_child($val->Child, $controller, $task)==0)
						return 0;
				}
			}
			
			return 1;
			
		}
		else{
			return 0;
		}
	}
}
?>
