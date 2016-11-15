<?php
class ANGB_GRADEBOOK{
	public function __construct(){
		add_action('wp_ajax_gradebook', array($this, 'gradebook'));											
	}

	public function gradebook(){
  		global $wpdb, $an_gradebook_api;
    	$wpdb->show_errors();  	   		  	
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
			case 'DELETE' : 
				echo json_encode(array("delete" => "deleting"));		
	  			break;
	  		case 'PUT' :
				echo json_encode(array("put" => "putting"));		
   				echo json_encode($courseDetails);	
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :	  			
				$gbid = $_GET['gbid'];			 
			   	echo json_encode($an_gradebook_api->angb_get_gradebook($gbid,null,null));
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