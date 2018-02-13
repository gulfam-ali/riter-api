<?php
class Profile extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->items;
  	}

    public function view(){
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->member_id);
		$my_id = (int)$this->valid_input($data->user_id);
		
		$sql = "SELECT u.id, u.avtar, u.first_name, u.last_name, u.reader_points, u.writer_points, u.registered_date, u.active, " 
			." (SELECT COUNT(1) FROM pr_likes l WHERE l.user_id = $user_id ) as likes," 
			." (SELECT COUNT(1) FROM pr_bookmarks b WHERE b.user_id = $user_id ) as bookmarks,"
			." (SELECT COUNT(1) FROM pr_comments c WHERE c.user_id = $user_id) as comments, "
			." (SELECT COUNT(1) FROM pr_views v WHERE v.user_id = $user_id) as views, "
			." (SELECT COUNT(1) FROM pr_posts p WHERE p.user_id = $user_id) as posts, "
			." (SELECT COUNT(1) FROM pr_followers f WHERE f.user_id = $user_id AND f.follower_id = $my_id ) as follow "
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
	
	public function change_avtar(){
		
		$user_id  = (int)$this->valid_input($_POST['user_id']);
		
		$avtar_name = round(microtime(true) * 1000).$user_id;
		
		$allowed =  array('gif', 'png' ,'jpg', 'jpeg');
		
		$filename = $_FILES['avtar']['name'];
		
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if(in_array($ext,$allowed) ) {
			
			$newfilename = $avtar_name.'.'.$ext;
			move_uploaded_file($_FILES['avtar']['tmp_name'], ROOT_URL."/media/images/dp/".$newfilename);
			
			require_once((CLASS_PATH . '/profile/thumbnail.php'));
			
			$flag = $this->update("users", " avtar = '".$newfilename."' ", " id=".$user_id );
			
			if($flag)
			{
				$response['validate'] = 'true';
				$response['avtar'] = $newfilename;
				$response['message'] = 'Profile picture have been changed successfully';
			}else{
				$response['validate'] = 'false';
				$response['message'] = 'Profile picture cannot be changed. Please try again later.';
			}
			
		}else{
			$response['validate'] = 'empty';
			$response['message'] = 'Only JPG/PNG images are allowed.';
		}

		
		return $response;
		
	}
	
	public function new_notifs(){
		
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);

		$sql = "SELECT N.id, N.user_id, U.avtar, U.first_name, U.last_name, N.reference_id, NE.name as event, N.reference_name, N.notification_date, N.seen FROM pr_notifications N"
			." JOIN pr_notification_events NE ON N.event_id = NE.id"
			." JOIN pr_users U ON N.user_id = U.id"
			." WHERE N.user_id<> ".$user_id." AND N.receiver_id = ".$user_id." AND N.seen=0 AND N.notification_date > timestampadd(day, -7, now()) "
			." GROUP BY NE.name, N.reference_id "
			." UNION "
			." SELECT N.id, N.user_id, U.avtar, U.first_name, U.last_name, N.reference_id, NE.name as event, PO.title as reference_name, N.notification_date, N.seen FROM pr_notifications N"
			." JOIN pr_notification_events NE ON N.event_id = NE.id"
			." JOIN pr_notification_reference_type NR ON N.reference_type_id = NR.id"
			." JOIN pr_posts PO ON N.reference_id = PO.id"
			." JOIN pr_users U ON N.user_id = U.id"
			." WHERE N.user_id<> ".$user_id." AND PO.user_id = ".$user_id." AND N.seen=0 AND N.notification_date > timestampadd(day, -7, now()) "
			." GROUP BY NE.name, N.reference_id ";
		
		$result = $this->custom($sql);

        if($result)
        {
			$response['validate'] = 'true';
			$response['unseen_count'] = $result->num_rows;
			
		}else{
			$response['validate'] = 'false';
		}

		return $response;
		
	}
	
	public function notifications(){
		
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);

		$sql = "SELECT COUNT(1) as personCount, N.id, N.user_id, U.avtar, U.first_name, U.last_name, N.reference_id, NE.name as event, N.reference_name, N.notification_date, N.seen FROM pr_notifications N"
			." JOIN pr_notification_events NE ON N.event_id = NE.id"
			." JOIN pr_users U ON N.user_id = U.id"
			." WHERE N.user_id<> ".$user_id." AND N.receiver_id = ".$user_id." AND N.notification_date > timestampadd(day, -7, now()) "
			." GROUP BY NE.name, N.reference_id "
			." UNION "
			." SELECT COUNT(1) as personCount, N.id, N.user_id, U.avtar, U.first_name, U.last_name, N.reference_id, NE.name as event, PO.title as reference_name, N.notification_date, N.seen FROM pr_notifications N"
			." JOIN pr_notification_events NE ON N.event_id = NE.id"
			." JOIN pr_notification_reference_type NR ON N.reference_type_id = NR.id"
			." JOIN pr_posts PO ON N.reference_id = PO.id"
			." JOIN pr_users U ON N.user_id = U.id"
			." WHERE N.user_id<> ".$user_id." AND PO.user_id = ".$user_id." AND N.notification_date > timestampadd(day, -7, now()) "
			." GROUP BY NE.name, N.reference_id "
			." ORDER BY notification_date DESC ";
		
		$result = $this->custom($sql);

        if($result->num_rows>0)
        {
			$response['validate'] = 'true';
			$new = 0;
			while($row = mysqli_fetch_assoc($result))
			{
				$arr[] = $row;
				if($row['seen']==0)
					$new++;
			}
			$response['data'] = $arr;
			$response['unseen_count'] = $new;
			
		}else{
			$response['validate'] = 'empty';
		}

		return $response;
		
	}
	
	public function read_notifs(){
		
		$data = json_decode(file_get_contents("php://input"));
		$user_id = (int)$this->valid_input($data->user_id);

		$sql = " UPDATE pr_notifications N SET seen = 1 WHERE "
			." receiver_id = $user_id OR ( N.reference_type_id = 1 AND N.reference_id IN ( SELECT id FROM pr_posts P WHERE P.user_id =  $user_id ) ) ";
		
		$result = $this->custom($sql);

        if($result)
        {
			return $this->notifications();
			
		}else{
			$response['validate'] = 'false';
		}

		return $response;
		
	}
}
