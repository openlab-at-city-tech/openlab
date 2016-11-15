<?php

class ANGB_USER{
	public function __construct(){
		add_action('wp_ajax_angb_user', array($this, 'angb_user'));											
	}

	public function angb_user(){
  		global $wpdb;
    	$wpdb->show_errors();  		 		
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
			case 'DELETE' :  
				parse_str($_SERVER['QUERY_STRING'],$params);				
				$delete_options = $params['delete_options'];
				$x = $params['id'];
				$y = $params['gbid'];	
				switch($delete_options){
					case 'gradebook':
						$results1 = $wpdb->delete('an_gradebook_users',array('uid'=>$x, 'gbid'=>$y));
						$results2 = $wpdb->delete('an_gradebook_cells',array('uid'=>$x, 'gbid'=>$y));			
						break;
					case 'all_gradebooks':
						echo json_encode('student deleted from all gradebooks');						
						$results1 = $wpdb->delete('an_gradebook_users',array('uid'=>$x));
						$results2 = $wpdb->delete('an_gradebook_cells',array('uid'=>$x));		
						break;
					case 'database':
						$results1 = $wpdb->delete('an_gradebook_users',array('uid'=>$x));
						$results2 = $wpdb->delete('an_gradebook_cells',array('uid'=>$x));				
						require_once(ABSPATH.'wp-admin/includes/user.php' );
						wp_delete_user($x);	
						die();												
						break;
				} 	  					  			
	  			break;
	  		case 'PUT' :
	  			global $an_gradebook_api;
				$params = json_decode(file_get_contents('php://input'),true);	
				$ID = $params['id'];
				$first_name = $params['first_name'];
				$last_name = $params['last_name'];				
				$results = $an_gradebook_api->an_gradebook_update_user($ID,$first_name,$last_name);	  		
				echo json_encode($results);				
   				die();	  		
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :
	  			global $an_gradebook_api;	  		
	  			//This is not called anywhere... do we need it?
	  			$id = wp_get_current_user()->ID;
	  			$gbid = $_GET['gbid'];
	  			$results = $an_gradebook_api->an_gradebook_get_current_user($id,$gbid);
				echo json_encode($results);
				break;
	  		case 'POST' :			
				$params = json_decode(file_get_contents('php://input'),true);	
				$first_name = $params['first_name'];
				$last_name = $params['last_name'];
				$id = null;
				$gbid = $params['gbid'];
				$user_login = $params['id-exists'];
				global $an_gradebook_api;
				$results = $an_gradebook_api->an_gradebook_create_user($id,$gbid,$first_name,$last_name,$user_login);		  		
				echo json_encode($results);
				break;
			}
			die();
			}	
}
?>