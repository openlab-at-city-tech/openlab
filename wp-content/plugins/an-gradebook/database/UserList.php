<?php
class ANGB_USER_LIST{
	public function __construct(){
		add_action('wp_ajax_angb_user_list', array($this, 'angb_user_list'));											
	}

	public function angb_user_list(){
		global $wpdb;
   		$wpdb->show_errors();  			
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
			case 'DELETE' :  
				echo json_encode(array("delete" => "deleting"));	
 				die();			
	  		case 'PUT' :
				echo json_encode(array("put" => "putting"));	   				  				
  				die();
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :
	  			$search = $_GET['search'];	 		 
				$args = array(			
					'search' => '*'. $search .'*'	
				);				
				$results = get_users( $args );								
				echo json_encode($results);
				die();
	  		case 'POST' :		
				echo json_encode(array("post" => "posting"));				   			  						  		
	  			die();
	  	}
	  	die();
	}
}
?>