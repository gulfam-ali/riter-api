<?php
class Guestfeed extends Builder
{
    public $config;

    public function __construct($db) {
		$this->db = $db;

		global $config;
		$this->config = (object) $config;

		$this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->items;
  	}

    public function feed($cond = '')
    {
		
		$sql = "SELECT SQL_CALC_FOUND_ROWS p.*, u.avtar, u.username, u.first_name, u.last_name," 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.post_id=p.id) as likes," 
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.post_id=p.id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.post_id=p.id) as views "
			." FROM `pr_posts` p JOIN `pr_users` u ON p.user_id=u.id "
			." WHERE p.is_deleted<>1"
			." ORDER BY p.post_date DESC".$this->query_limit;
		
		$result = $this->custom($sql);
		$tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
        $records = mysqli_fetch_assoc($tot_records);

        if($result->num_rows >0)
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

}
