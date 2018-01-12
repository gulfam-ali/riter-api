<?php
require_once(CONTROLLER_PATH ."/controller.php");
class contactsController extends controller{

	public $contactClass;
	public function __construct($db) {
		require_once((CLASS_PATH . '/contacts/contacts.php'));
		$this->contactClass = new Contacts($db);
	}

	public function call($db, $method){
		$requestUrl = array();
		if(isset($_REQUEST['requestUrl'])){
			$requestUrl = explode('/', $_REQUEST['requestUrl']);
		}

		switch($method) {
			case 'PUT':
			  break;

			case 'DELETE':
			  break;

			case 'GET':
        if(isset($requestUrl[2])){
          if(trim($requestUrl[1]) == 'get-contact-details')
          {
            $id = (int)trim($requestUrl[2]);
            return $this->contactClass->get_contact_details($id);
          }
        }
        else if(isset($requestUrl[1])){
          if(trim($requestUrl[1]) == 'get-contact-list')
          {
            return $this->contactClass->get_contact_list();
          }
        }
				break;

			case 'POST':
        if(isset($requestUrl[1])){
          if(trim($requestUrl[1]) == 'save-contact')
          {
            return $this->contactClass->save_contact();
          }
					else if(trim($requestUrl[1]) == 'delete-contact')
          {
            return $this->contactClass->delete_contact();
          }
					else if(trim($requestUrl[1]) == 'search-contact-list')
          {
            return $this->contactClass->search_contact_list();
          }
					else if(trim($requestUrl[1]) == 'search-contact-with-group')
          {
            return $this->contactClass->search_contact_with_group();
          }
        }
				break;

			default:
			  header('HTTP/1.1 405 Method Not Allowed');
			  header('Allow: GET, PUT, DELETE');
			  break;
		}
	}


}
?>
