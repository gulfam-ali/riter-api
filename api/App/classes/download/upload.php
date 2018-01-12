<?php
class Upload extends Builder
{
    public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;
  	}

    public function upload_contacts1()
    {
      $flag =1;
      require_once LIBRARY_PATH.'/PHPExcel/IOFactory.php';
      $object = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']);
      //var_dump($_FILES['file']['tmp_name']);
      $sheet = $object->getSheet(0);
      $highestRow   = $sheet->getHighestRow();
       $highestColumn = $sheet->getHighestColumn();
      for ($row = 2; $row <= $highestRow; $row++) {
        $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                        null, true, false);

        //Prints out data in each row.
        /*
        $name = $rowData[0][0];

        $nameArr = explode(' ', $name); // Exploding to remove extra whitespaces
        $nameArr = array_filter($nameArr); //Removing whitespaces
        $nameArr = implode(' ', $nameArr); //Imlopding to serialize the index keys
        $nameArr = explode(' ', $nameArr); //Again exploding to split first_name and last_name

        $length = sizeof($nameArr);
        $last_name = $nameArr[$length-1];
        array_pop($nameArr);
        $first_name = implode(' ', $nameArr);
        */

        $first_name = $rowData[0][0];
        $last_name  = $rowData[0][1];
        $email      = $rowData[0][2];
        $phone      = $rowData[0][3];

        $insert = " first_name = AES_ENCRYPT('".$first_name."', '".$this->config->salt."') , 
                  last_name= AES_ENCRYPT('".$last_name."', '".$this->config->salt."'), 
                  email= AES_ENCRYPT('".$email."', '".$this->config->salt."'), 
                  phone= AES_ENCRYPT('".$phone."', '".$this->config->salt."'), 
                  status=1 ";

        $where = " AES_DECRYPT(phone, '".$this->config->salt."') LIKE '%".$phone."%' AND status IN (1, 2) ";

        $check_exist = $this->select("contacts", $where);

        if($check_exist['validate']==='empty'){
          $flag = ($this->insert("contacts", $insert))?$flag*1:$flag*0;
        }else{
          $flag = ($this->insert("contacts", $insert, $where))?$flag*1:$flag*0;
        }

      }

      if($flag)
      {
        $response['validate'] = "true";
      }
      else{
        $response['validate'] = "partial";
      }

      return $response;

    }
	
	public function upload_contacts()
    {
		$flag =1;
		$handle = fopen($_FILES['file']['tmp_name'],'r');
		
		if(!$handle){
			$response['validate'] = "false";
			return $response;
		}else{
			//Read the file as csv
			$i = 1;
			while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
				
				if($i>1)
				{
					$first_name = $data[0];
					$last_name  = $data[1];
					$email      = $data[2];
					$phone      = $data[3];
					
					$insert = " first_name = AES_ENCRYPT('".$first_name."', '".$this->config->salt."') , 
                  last_name= AES_ENCRYPT('".$last_name."', '".$this->config->salt."'), 
                  email= AES_ENCRYPT('".$email."', '".$this->config->salt."'), 
                  phone= AES_ENCRYPT('".$phone."', '".$this->config->salt."'), 
                  status=1 ";

          $where = " AES_DECRYPT(phone, '".$this->config->salt."') LIKE '%".$phone."%' AND status IN (1, 2) ";

					$check_exist = $this->select("contacts", $where);

					if($check_exist['validate']==='empty'){
					  $flag = ($this->insert("contacts", $insert))?$flag*1:$flag*0;
					}else{
					  $flag = ($this->insert("contacts", $insert, $where))?$flag*1:$flag*0;
					}
				}
				
				$i++;
			}
		}
		
	  
	  

		if($flag)
		{
		$response['validate'] = "true";
		}
		else{
		$response['validate'] = "partial";
		}

		return $response;

    }

}
