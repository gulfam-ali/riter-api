<?php
class User extends Builder
{

	public $config;

    public function __construct($db) {
      $this->db = $db;

      global $config;
      $this->config = (object) $config;
  	}

	public function save_password()
	{
		$data = json_decode(file_get_contents("php://input"));
        $currentPassword = $this->valid_input($data->currentPassword);
		$newPassword = $this->encryptIt( $this->valid_input($data->newPassword) );

		$user_id = $_COOKIE['userid'];

		$result = $this->select("users", " id = ".$user_id);
		if($result['validate']=='true')
		{
			if($result['total_records']>0)
			{
				$checkPass = $this->decryptIt($result['data'][0]['password']);

				if($checkPass != $currentPassword)
				{
					$response['validate'] = 'false';
					$response['message'] = 'Incorrect Current Password';
					return $response;
				}else{
					$flag = $this->insert("users", " password = '".$newPassword."' ", " id = ".$user_id);
					if($flag)
					{
						$response['validate'] = 'true';
						$response['message'] = 'Password changed successfully';
					}else{
						$response['validate'] = 'false';
						$response['message'] = 'Password cannot be changed. Please try again later';
					}

					return $response;
				}
			}
		}
	
	}
	
	function encryptIt( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
		return( $qEncoded );
	}

	function decryptIt( $q ) {
		$cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
		$qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
		return( $qDecoded );
	}

}
?>
