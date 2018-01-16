<?php
class Feed extends Builder
{
    public $config;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;

		$this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->itemPerpage;
  	}

    public function feed($cond = '')
    {
		
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.*, u.first_name, u.last_name," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id AND l.user_id = $user_id ) as liked," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id) as likes," 
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.post_id=p.id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.post_id=p.id) as views "
			." FROM `pr_posts` p JOIN `pr_users` u ON p.user_id=u.id "
			." WHERE p.is_deleted<>1"
			." ORDER BY p.post_date DESC".$this->query_limit;
		
		$result = $this->custom($sql);
		$tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
        $records = mysqli_fetch_assoc($tot_records);

        if($records>0)
        {
			$response['total_records'] = $records['rows'];
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
	
	public function mystories()
    {
		
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.*, u.first_name, u.last_name," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id AND l.user_id = $user_id ) as liked," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id) as likes," 
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.post_id=p.id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.post_id=p.id) as views "
			." FROM `pr_posts` p JOIN `pr_users` u ON p.user_id=u.id "
			." WHERE p.user_id=$user_id AND p.is_deleted<>1 "
			." ORDER BY p.post_date DESC".$this->query_limit;
		
		$result = $this->custom($sql);
		$tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
        $records = mysqli_fetch_assoc($tot_records);

        if($records>0)
        {
			$response['total_records'] = $records['rows'];
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
