<?php
class Contacts extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;

      $this->query_limit = " LIMIT ".$this->config->offset.",". $this->config->itemPerpage;
  	}

    public function get_contact_list($cond = '')
    {
      $where = " status IN (1, 2)";
      if($cond!=='')
        $where = $cond;

      $sql = " SELECT  SQL_CALC_FOUND_ROWS  id, AES_DECRYPT(first_name, '".$this->config->salt."') as first_name, 
                AES_DECRYPT(last_name, '".$this->config->salt."') as last_name, 
                AES_DECRYPT(phone, '".$this->config->salt."') as phone , 
                AES_DECRYPT(email, '".$this->config->salt."') as email 
                FROM ".$this->config->dbprefix."contacts WHERE ".$where.$this->query_limit;
     
      $result = $this->custom($sql);

      $tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
      $records = mysqli_fetch_assoc($tot_records);

      if($records['rows']>0)
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

    public function search_contact_list()
    {
      $data = json_decode(file_get_contents("php://input"));
      $keyword = $this->valid_input($data->keyword);
      $where = '';
      if($keyword!==0)
      {
        $where = "  ( CONCAT( AES_DECRYPT( first_name, '".$this->config->salt."') , ' ', AES_DECRYPT( last_name, '".$this->config->salt."') )  LIKE '%".$keyword."%' OR AES_DECRYPT( first_name, '".$this->config->salt."') LIKE '%".$keyword."%' OR AES_DECRYPT( last_name, '".$this->config->salt."') LIKE '%".$keyword."%' OR AES_DECRYPT(phone, '".$this->config->salt."') LIKE '%".$keyword."%' OR AES_DECRYPT(email, '".$this->config->salt."') LIKE '%".$keyword."%')  AND  status IN (1, 2) ";
      }

      return $this->get_contact_list($where);
    }

    public function search_contact_with_group()
    {
      

      $data = json_decode(file_get_contents("php://input"));
      $group_id = $this->valid_input($data->group_id);
      $keyword = $this->valid_input($data->keyword);
      $sql = " SELECT  c.id, AES_DECRYPT(c.first_name, '".$this->config->salt."') as first_name, 
                AES_DECRYPT(c.last_name, '".$this->config->salt."') as last_name, 
                AES_DECRYPT(c.phone, '".$this->config->salt."') as phone , 
                AES_DECRYPT(c.email, '".$this->config->salt."') as email, 
                (SELECT count(1) FROM ".$this->config->dbprefix."group_contacts gc WHERE gc.contact_id=c.id AND gc.group_id=".$group_id." AND gc.status IN (1, 2) ) as present FROM ".$this->config->dbprefix."contacts c WHERE ( CONCAT( AES_DECRYPT( c.first_name, '".$this->config->salt."') , ' ', AES_DECRYPT( c.last_name, '".$this->config->salt."') )  LIKE '%".$keyword."%' OR  AES_DECRYPT( c.first_name, '".$this->config->salt."') LIKE '%".$keyword."%' OR AES_DECRYPT( c.last_name, '".$this->config->salt."')  LIKE '%".$keyword."%' OR AES_DECRYPT(c.phone, '".$this->config->salt."') LIKE '%".$keyword."%' OR AES_DECRYPT(c.email, '".$this->config->salt."') LIKE '%".$keyword."%') AND c.status IN (1, 2) LIMIT 0, 10 ";

      if($keyword!==0 && $group_id>0)
      {
        $result = $this->custom($sql);
        $tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
        $records = mysqli_num_rows($tot_records);

        if($records>0)
        {
          $response['total_records'] = $records;
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

    public function get_contact_details($id)
    {
      //return $this->select("contacts", "id=".$id." AND status=1 ");
      $contact_details = $this->get_contact_list("id=".$id." AND status IN (1, 2)  ");
      //$contact_details = $this->select("contacts", "id=".$id." AND status IN (1, 2)  ");
      $group_contacts = $this->select("group_contacts", "contact_id=".$id." AND status IN (1, 2)  ");

      if($group_contacts['validate']!=='empty')
      {
        $groupArr = array();
        foreach ($group_contacts['data'] as $key) {
          array_push($groupArr, $key['group_id']);
        }
        $group_ids = implode(', ', $groupArr);
        $group_details = $this->select("groups", " id IN (".$group_ids.") AND status IN (1, 2) ");
        if(isset($group_details['data']))
          $contact_details['data'][0]['groups'] = $group_details['data'];
        else
          $contact_details['data'][0]['groups'] = '';
      }else{
          $contact_details['data'][0]['groups'] = '';
      }

      return $contact_details;
    }

    public function save_contact()
    {
        $data = json_decode(file_get_contents("php://input"));
        $contact_id = $this->valid_input($data->id);
		    $first_name = $this->valid_input($data->first_name);
        $last_name = $this->valid_input($data->last_name);
        $email = $this->valid_input($data->email);
        $phone = $this->valid_input($data->phone);
        $group_ids = $this->valid_input($data->groups);


        $insert = " first_name = AES_ENCRYPT('$first_name', '".$this->config->salt."'), last_name = AES_ENCRYPT('$last_name', '".$this->config->salt."'), email = AES_ENCRYPT('$email', '".$this->config->salt."'), phone= AES_ENCRYPT('$phone', '".$this->config->salt."') ";

        if($contact_id==0)
        {

          $where = " AES_DECRYPT(phone, '".$this->config->salt."') LIKE '%".$phone."%' AND status IN (1, 2) ";
          $check_exist = $this->select("contacts", $where);

          if($check_exist['validate']==='empty'){
            $flag = $this->insert("contacts", $insert);
            $contact_id = $this->last_insert_id;
          }else{
            $response['validate'] = 'exist';
            return $response;
          }
        }else{
          $where = " id = ".$contact_id;
          $flag = $this->insert("contacts", $insert, $where);
        }


        $response['validate'] = '';
        if( $flag )
        {
          if($group_ids==='')
            $group_ids = 0;
          $result = $this->add_group_contacts($contact_id, $group_ids);
          if($result)
          {
            $response['validate'] = "true";
          }
          else{
            $response['validate'] = "partial";
          }

          return $response;

        }else{
          return $flag;
        }
    }

    public function delete_contact()
    {
      $data = json_decode(file_get_contents("php://input"));
      $contact_ids = $this->valid_input($data->contact_id);

      $update = " status=3 ";
      $where = " id IN ($contact_ids) ";

      return $this->update("contacts", $update, $where);
    }

    public function add_group_contacts($contact_id, $group_ids)
    {
      $this->update("group_contacts", ' status = 3' , ' contact_id='.$contact_id);

      $flag = 1;
      if($group_ids==0)
        return $flag;

      $group_id_arr = explode(',', $group_ids);

      foreach($group_id_arr as $group_id)
      {
        $where = " group_id = $group_id AND contact_id= $contact_id ";
        $check_exist = $this->select("group_contacts", $where);

        if($check_exist['validate']==='empty'){
          $insert = " group_id='$group_id', contact_id='$contact_id' ";
          //echo $insert;
          $flag = ($this->insert("group_contacts", $insert))?$flag*1:$flag*0;
        }else{
          $insert = " status = 1 ";
          $where = " group_id='$group_id' AND contact_id='$contact_id' ";
          $flag = ($this->insert("group_contacts", $insert, $where))?$flag*1:$flag*0;
        }

      }
      return $flag;
    }
}
