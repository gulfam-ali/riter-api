<?php
class Download extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;
  	}

    public function group_contacts($group_id)
    {
      global $config;
       $config = (object) $config;
      if($group_id<1)
      {
        echo '{"validate" : "Invalid Data Provided"}'; die;
      }

      $group_details = $this->select("groups", "id=".$group_id." AND status IN (1, 2)  ");
      if($group_details['validate'] == 'empty')
      {
        echo '{"validate" : "Group does not exist"}'; die;
      }
      $group_name = $group_details['data'][0]['name'];
      $sql = "SELECT contact_id FROM ".$config->dbprefix."group_contacts WHERE group_id=".$group_id." AND status IN (1, 2)  ";
      $group_contacts_res = $this->custom($sql);
      if($group_contacts_res->num_rows>0)
      {
        $group_contacts['validate'] = 'true';
        while($row = mysqli_fetch_assoc($group_contacts_res))
        {
          $arr[] = $row;
        }
        $group_contacts['data'] = $arr;
      }else{
        echo '{"validate" : "No contacts found for this group"}'; die;
      }

      if($group_contacts['validate']!=='empty' && $group_details['validate']!=='empty' )
      {
        $contactArr = array();
        foreach ($group_contacts['data'] as $key) {
          array_push($contactArr, $key['contact_id']);
        }
        $contact_ids = implode(', ', $contactArr);

        $sql = " SELECT AES_DECRYPT(first_name, '".$this->config->salt."') as first_name, 
                  AES_DECRYPT(last_name, '".$this->config->salt."') as last_name, 
                  AES_DECRYPT(email, '".$this->config->salt."') as email, 
                  AES_DECRYPT(phone, '".$this->config->salt."') as phone 
                  FROM ".$config->dbprefix."contacts 
                  WHERE  id IN (".$contact_ids.") AND status IN (1, 2) ";
        $contact_details = $this->custom($sql);

        if($contact_details->num_rows>0)
        {
			$data = "First Name, Last Name, Email, Phone\n";
            while($row = mysqli_fetch_assoc($contact_details))
            {
                $data .= $row['first_name'].",".$row['last_name'].",".$row['email'].",".$row['phone']."\n";
            }
        }
		header('Content-Type: application/csv');
		header("Content-Disposition: attachement; filename='".$group_name." Contacts.csv");
		echo $data; exit();
      }
      echo '{"validate" : "No contacts found for this group"}'; die;
	}

	public function sample_contacts()
	{
		$data = "First Name, Last Name, Email, Phone";

		header('Content-Type: application/csv');
		header('Content-Disposition: attachement; filename="Sample Contacts.csv"');
		echo $data; exit();
	}

}
