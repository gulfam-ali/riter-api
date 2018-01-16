<?php
class Post extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->itemPerpage;
  	}

    public function like()
    {
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$post_id = (int)$this->valid_input($data->post_id);
		
		$check = $this->select("likes", " post_id = ".$post_id." AND user_id = ".$user_id." ");
		
		if($check['validate']==='empty'){
			//Add Like Event
			return $this->insert("likes", " post_id = ".$post_id.", user_id = ".$user_id);
		}else{
			//Remove Like Event
			return $this->delete("likes", " post_id = ".$post_id." AND user_id = ".$user_id);
		}
    }
	
	public function read()
    {
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$post_id = (int)$this->valid_input($data->post_id);
		
		$sql = "SELECT p.*, u.first_name, u.last_name," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id AND l.user_id = $user_id ) as liked," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id) as likes," 
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.post_id=p.id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.post_id=p.id) as views "
			." FROM `pr_posts` p JOIN `pr_users` u ON p.user_id=u.id "
			." WHERE p.is_deleted<>1 AND p.id=".$post_id;
		
		$result = $this->custom($sql);

        if($result->num_rows>0)
        {
			//Inserting View
			$insert_values = " user_id = '".$user_id."', post_id='".$post_id."' ";
			$this->insert("views", $insert_values);
			
			$response['validate'] = 'true';

			while($row = mysqli_fetch_assoc($result))
			{
				$arr[] = $row;
			}
			$response['data'] = $arr;
		}else{
			$response['validate'] = 'empty';
		}

		return $response;
    }
	
	public function write()
    {
		$data = json_decode(file_get_contents("php://input"));
		
		$title = $this->valid_input($data->title);
		$body = $this->valid_input($data->body);
		$user_id = $this->valid_input($data->user_id);
		
		$insert_values = " user_id = '".$user_id."', title='".$title."', body='".$body."', post_date=NOW() ";
		$flag = $this->insert("posts", $insert_values);
		
        if($flag)
        {
			$response['post_id'] = $this->last_insert_id;
			$response['validate'] = 'true';
		}else{
			$response['validate'] = 'false';
		}

		return $response;
    }

}
