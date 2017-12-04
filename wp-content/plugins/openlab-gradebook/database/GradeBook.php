<?php
class OPLB_GRADEBOOK{
	public function __construct(){
		add_action('wp_ajax_gradebook', array($this, 'gradebook'));											
	}

	public function gradebook(){
  		global $wpdb, $oplb_gradebook_api;
                $wpdb->show_errors();  	   		  	
		
                $params = $oplb_gradebook_api->oplb_gradebook_get_params();
                $gbid = $params['gbid'];
                
                //user check - only allow GET requests
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) === 'instructor'
                        || $oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) === 'student') {
                    
                        if ($params['method'] !== 'GET') {
                            echo json_encode(array("status" => "Not Allowed."));
                            die();
                        }
                } else {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                }
                
                //nonce check
                if (!wp_verify_nonce($params['nonce'], 'oplb_gradebook')) {
                    echo json_encode(array("status" => "Authentication error."));
                    die();
                }
                
		switch ($params['method']){
			case 'DELETE' : 
				echo json_encode(array("delete" => "deleting"));		
	  			break;
	  		case 'PUT' :
				echo json_encode(array("put" => "putting"));		
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :	  	
			   	echo json_encode($oplb_gradebook_api->oplb_get_gradebook($gbid,null,null));
				break;
	  		case 'POST' :
				echo json_encode(array("post" => "posting"));		
				die();					
				break;
	  	}
	  	die();
	}
}	
?>