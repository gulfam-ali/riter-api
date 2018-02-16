<?php
class Post extends Builder
{
    public $config;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;

		$this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->items;
  	}

    public function like()
    {
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$post_id = (int)$this->valid_input($data->post_id);
		
		$check = $this->select("likes", " post_id = ".$post_id." AND user_id = ".$user_id." ");
		
		if($check['validate']==='empty'){
			//Add Like Event
			$flag = $this->insert("likes", " post_id = ".$post_id.", user_id = ".$user_id);
			if($flag){
				return $this->insert("notifications", " user_id = '".$user_id."', event_id = '".EV_LIKE."', reference_type_id ='".REF_STORY."' , reference_id = '".$post_id."' " );
			}else{
				return false;
			}
		}else{
			//Remove Like Event
			$this->delete("notifications", " event_id = '".EV_LIKE."' AND reference_id = ".$post_id." AND user_id = ".$user_id);
			
			return $this->delete("likes", " post_id = ".$post_id." AND user_id = ".$user_id);
		}
    }
	
	public function bookmark()
    {
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$post_id = (int)$this->valid_input($data->post_id);
		
		$check = $this->select("bookmarks", " post_id = ".$post_id." AND user_id = ".$user_id." ");
		
		if($check['validate']==='empty'){
			//Add Like Event
			$flag = $this->insert("bookmarks", " user_id = ".$user_id.", post_id = ".$post_id);
			if($flag){
				return $this->insert("notifications", " user_id = '".$user_id."', event_id = '".EV_BOOKMARK."', reference_type_id ='".REF_STORY."' , reference_id = '".$post_id."' " );
			}else{
				return false;
			}
		}else{
			//Remove Like Event
			$this->delete("notifications", " event_id = '".EV_BOOKMARK."' AND reference_id = ".$post_id." AND user_id = ".$user_id);
			return $this->delete("bookmarks", " post_id = ".$post_id." AND user_id = ".$user_id." ");
		}
    }
	
	public function read()
    {
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$post_id = (int)$this->valid_input($data->post_id);
		
		$sql = "SELECT p.*, u.avtar, u.username, u.first_name, u.last_name," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id AND l.user_id = $user_id ) as liked," 
			." (SELECT COUNT(1) FROM pr_bookmarks b WHERE b.post_id=p.id AND b.user_id = $user_id ) as bookmarked,"
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id) as likes," 
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.post_id=p.id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.post_id=p.id) as views, "
			." (SELECT COUNT(1) FROM pr_followers f WHERE f.user_id = p.user_id AND f.follower_id = $user_id ) as follow "
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
			
			$comments = $this->read_comments($post_id, 0);
			
			$response['comments'] = $comments;
			
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
	
	public function comment()
    {
		$data = json_decode(file_get_contents("php://input"));
		
		$post_id = $this->valid_input($data->post_id);
		$comment = $this->valid_input($data->comment);
		$user_id = $this->valid_input($data->user_id);
		
		$insert_values = " user_id = '".$user_id."', post_id='".$post_id."', comment='".$comment."', comment_date = NOW() ";
		$flag = $this->insert("comments", $insert_values);
		
        if($flag)
        {
			$response['comment_id'] = $this->last_insert_id;
			$response['validate'] = 'true';
			
			$this->insert("notifications", " user_id = '".$user_id."', event_id = '".EV_COMMENT."', reference_type_id ='".REF_STORY."' , reference_id = '".$post_id."' " );
		}
		else{
			$response['validate'] = 'false';
		}

		return $response;
    }
	
	public function read_comments($post_id = 0, $offset = 0){
		
		if($post_id > 0)
		{
			$post_id 				 = $post_id;
			$offset	 = $offset;
		}
		else{
			
			$data = json_decode(file_get_contents("php://input"));
			
			$post_id 				= $this->valid_input($data->post_id);
			$offset 	= $this->valid_input($data->offset);
		}
		
		$sql = "SELECT c.*, u.avtar, u.username, u.first_name, u.last_name" 
			." FROM `pr_comments` c JOIN `pr_users` u ON c.user_id=u.id "
			." WHERE c.is_deleted<>1 AND c.post_id=".$post_id
			." ORDER BY c.comment_date DESC "
			." LIMIT $offset, 5";
		
		$result = $this->custom($sql);

        if($result->num_rows>0)
        {
			$response['comments_returned'] = $result->num_rows;

			while($row = mysqli_fetch_assoc($result))
			{
				$arr[] = $row;
			}
			$response['commentsArr'] = $arr;
			
		}else{
			$response['comments_returned'] = 0;
		}

		return $response;
	}
	
	public function loadcomments(){
		
		$data = json_decode(file_get_contents("php://input"));

		$post_id 	= $this->valid_input($data->post_id);
		$offset 	= $this->valid_input($data->offset);
		
		return $this->read_comments($post_id, $offset);
	}
	

}
