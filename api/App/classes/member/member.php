<?php
class Member extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->items;
  	}

    public function profile(){
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$username = $this->valid_input($data->username);
		
		$sql = "SELECT u.id, u.avtar, u.username, u.first_name, u.last_name, u.tagline, u.bio, u.reader_points, u.writer_points, u.registered_date, "
			." (SELECT COUNT(1) FROM pr_posts p WHERE p.user_id = u.id) as posts, "
			." (SELECT COUNT(1) FROM pr_followers f WHERE f.user_id = u.id) as followers, "
			." (SELECT COUNT(1) FROM pr_followers f WHERE f.user_id = u.id AND f.follower_id = $user_id ) as follow "
			." FROM `pr_users` u "
			." WHERE u.username='".$username."' AND u.active=1 ";
		
		$result = $this->custom($sql);

        if($result && $result->num_rows>0)
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
	
	public function stories()
    {
		
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$username = $this->valid_input($data->username);
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.*, u.avtar, u.first_name, u.last_name," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id AND l.user_id = $user_id ) as liked," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id) as likes," 
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.post_id=p.id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.post_id=p.id) as views "
			." FROM `pr_posts` p JOIN `pr_users` u ON p.user_id=u.id "
			." WHERE u.username='".$username."' AND p.is_deleted<>1 "
			." ORDER BY p.post_date DESC".$this->query_limit;
		
		$result = $this->custom($sql);
		$tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
        $records = mysqli_fetch_assoc($tot_records);

        if($result && $result->num_rows >0)
        {
			$response['total_records'] = $records['rows'];
			$response['validate'] = 'true';
			$arr = array();
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
	
	public function follow()
    {
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		$member_id = (int)$this->valid_input($data->member_id);
		
		$check = $this->select("followers", " user_id = ".$member_id." AND follower_id = ".$user_id." ");
		
		if($check['validate']==='empty'){
			//Add Like Event
			$flag = $this->insert("followers", " user_id = ".$member_id.", follower_id = ".$user_id);
			if($flag){
				return $this->insert("notifications", " user_id = ".$user_id.", receiver_id = ".$member_id.", event_id = '".EV_FOLLOW."' ");
			}else{
				return false;
			}
		}else{
			//Remove Like Event
			$this->delete("notifications", " receiver_id = ".$member_id." AND user_id = ".$user_id." AND event_id='".EV_FOLLOW."' ");
			return $this->delete("followers", " user_id = ".$member_id." AND follower_id = ".$user_id);
		}
    }
}
