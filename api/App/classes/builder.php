<?php
class Builder
{
  protected $query = array(
    "operation"=>"",
    "fields"=>" * ",
    "table"=>"",
    "join1"=>"",
    "join2"=>"",
    "join3"=>"",
    "insert_values"=>"",
    "update_values"=>"",
    "where"=>"",
    "limit"=>"",
    "order"=>""
  );
  protected $sql_query = '';
  public $table;
  public $table_prefix = "pr_";
  public $key;
  public $db;
  public $last_insert_id;

  public function __construct()
  {

  }

  protected function build_query()
  {
    $this->sql_query  = $this->query['operation'];

      switch ($this->query['operation']) {
        case 'SELECT':
          $this->sql_query.= " SQL_CALC_FOUND_ROWS ".$this->query['fields']
                          ." FROM ".$this->table_prefix.$this->query['table']
                          .$this->query['join1']
                          .$this->query['join2']
                          .$this->query['join3']
                          .$this->query['where']
                          .$this->query['limit']
                          .$this->query['order'];
          break;

        case 'UPDATE':
          $this->sql_query .= " ".$this->table_prefix.$this->query['table']
                          .$this->query['update_values']
                          .$this->query['where'];
          break;
        case 'INSERT INTO':
          $this->sql_query .= " ".$this->table_prefix.$this->query['table']
                          .$this->query['insert_values'];
          break;

        case 'DELETE':
          $this->sql_query.= " FROM ".$this->table_prefix.$this->query['table']
                          .$this->query['where'];
          break;
        default: break;
      }
  }

  protected function execute_query()
  {
    $this->build_query();
    //echo $this->sql_query;
    $result = mysqli_query($this->db,$this->sql_query);

    if($this->query['operation']== 'SELECT')
    {
      $tot_records = mysqli_query($this->db, "SELECT FOUND_ROWS() as rows");
      $tot_records = mysqli_fetch_assoc($tot_records);
    }

    if($result)
    {
      if($this->query['operation'] == 'SELECT')
      {
        $records = $result->num_rows;
        if($records>0)
        {
          $response['total_records'] = $tot_records['rows'];
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
    $this->last_insert_id = mysqli_insert_id($this->db);
    return $result;
  }

  public function custom($query)
  {
      return mysqli_query($this->db, $query);
  }

  public function select($table, $where='', $fields=' * ', $join1='', $join2='', $join3='')
  {
      $this->query['operation'] = "SELECT" ;
      $this->query['table'] = $table;
      $this->query['fields'] = $fields;
      $this->query['join1'] = $join1;
      $this->query['join2'] = $join2;
      $this->query['join3'] = $join3;
      if($where!=='')
        $this->query['where'] = " WHERE ".$where;

      global $config;
  		$config = (object) $config;
      $this->query['limit'] = " LIMIT ".$config->offset.",". $config->items;
      return $this->execute_query();
  }

  public function insert($table, $insert_values, $where='')
  {
    $this->query['table'] = $table;
    if($where==='')
    {
      $this->query['operation'] = "INSERT INTO" ;
      $this->query['insert_values'] = " SET ".$insert_values;
    }
    else{
      $this->query['operation'] = "UPDATE" ;
      $this->query['where'] = " WHERE ".$where;
      $this->query['update_values'] = " SET ".$insert_values;
    }

    return $this->execute_query();
  }

  public function update($table, $update_values, $where='')
  {
    if($where=='')
    {
      $this->query['operation'] = "UPDATE" ;
    }
    else{
      $this->query['operation'] = "UPDATE" ;
      $this->query['where'] = " WHERE ".$where;
    }

    $this->query['table'] = $table;
    $this->query['update_values'] = " SET ".$update_values;
    return $this->execute_query();
  }

  public function delete($table, $where='')
  {
      $this->query['operation'] = "DELETE" ;
      $this->query['table'] = $table;
      if($where!=='')
        $this->query['where'] = " WHERE ".$where;
      return $this->execute_query();
  }


	public function valid_input($data) {
		$data = trim($data);
		$data = addslashes($data);
		//$data = htmlspecialchars($data);
		return $data;
	}

	public function encryptIt( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
		return( $qEncoded );
	}

	public function decryptIt( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
		return( $qDecoded );
	}
	
	public function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;
		if ($range < 1) return $min; // not so random...
		$log = ceil(log($range, 2));
		$bytes = (int) ($log / 8) + 1; // length in bytes
		$bits = (int) $log + 1; // length in bits
		$filter = (int) (1 << $bits) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ($rnd > $range);
		return $min + $rnd;
	}

	public function getToken($length)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
		$codeAlphabet.= "0123456789";
		$max = strlen($codeAlphabet); // edited

		for ($i=0; $i < $length; $i++) {
			$token .= $codeAlphabet[$this->crypto_rand_secure(0, $max-1)];
		}

		return $token;
	}

}

 ?>
