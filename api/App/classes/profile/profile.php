<?php
class Profile extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->itemPerpage;
  	}

    public function view(){
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		
		$sql = "SELECT u.id, u.avtar, u.first_name, u.last_name, u.reader_points, u.writer_points, u.registered_date, u.active, " 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.user_id = $user_id ) as likes," 
			." (SELECT COUNT(1) FROM pr_bookmarks b WHERE b.user_id = $user_id ) as bookmarks,"
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.user_id = $user_id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.user_id = $user_id) as views, "
			." (SELECT COUNT(1) FROM pr_posts p WHERE p.user_id = $user_id) as posts "
			." FROM `pr_users` u "
			." WHERE u.id=$user_id AND u.active=1 ";
		
		$result = $this->custom($sql);

        if($result->num_rows>0)
        {
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
}
