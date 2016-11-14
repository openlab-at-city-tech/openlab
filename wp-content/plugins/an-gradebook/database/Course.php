<?php
class gradebook_course_API{
	public function __construct(){
		add_action('wp_ajax_course', array($this, 'course'));											
	}
	
/*********************************
* Use the following template to extend api
*
*	public function name_of_api(){
*		global $wpdb;
*   	$wpdb->show_errors();  		
*		if (!gradebook_check_user_role('administrator')){	
*			echo json_encode(array("status" => "Not Allowed."));
*			die();
*		}   		
*		switch ($_SERVER['REQUEST_METHOD']){
*			case 'DELETE' :  
*	  			echo json_encode(array('delete'=>'deleting'));
*	  			break;
*	  		case 'PUT' :
*	  			echo json_encode(array('put'=>'putting'));
*				break;
*	  		case 'UPDATE' :
*				echo json_encode(array("update" => "updating"));				
*				break;
*	  		case 'PATCH' :
*				echo json_encode(array("patch" => "patching"));				
*				break;
*	  		case 'GET' :
*				echo json_encode(array("get" => "getting"));	
*				break;
*	  		case 'POST' :				
*				echo json_encode(array("post" => "posting"));		  		
*				break;
*	  	}
*	  	die();
*	}
*********************************/


/*************************
*
*   course api
*
**************************/

	public function course(){
  		global $wpdb, $an_gradebook_api;
    	$wpdb->show_errors();  	   		  	
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
			case 'DELETE' : 
				$id = $_REQUEST['id'];
				$gbid = $id;
				if ( $an_gradebook_api -> an_gradebook_get_user_role($gbid)!='instructor'){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 						
	  			$wpdb->delete('an_gradebook_courses',array('id'=>$id));
	  			$wpdb->delete('an_gradebook_assignments',array('gbid'=>$gbid));
	  			$wpdb->delete('an_gradebook_cells',array('gbid'=>$gbid));  
	  			$wpdb->delete('an_gradebook_users',array('gbid'=>$gbid));  	  			
	  			echo json_encode(array('delete_course'=>'Success'));
	  			break;
	  		case 'PUT' :
				$params = json_decode(file_get_contents('php://input'),true);
				if ( $an_gradebook_api -> an_gradebook_get_user_role($params['id'])!='instructor'){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 	  					
   				$wpdb->update('an_gradebook_courses', array( 
   					'name' => $params['name'], 'school' => $params['school'], 'semester' => $params['semester'], 
   					'year' => $params['year']),
					array('id' => $params['id'])
				);   
   				$courseDetails = $wpdb->get_row('SELECT * FROM an_gradebook_courses WHERE id = '. $params['id'] , ARRAY_A);
   				echo json_encode($courseDetails);	
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :	  		
   				$courseDetails = $wpdb->get_row('SELECT * FROM an_gradebook_courses WHERE id = '. $_GET['id'] , ARRAY_A);	
   				echo json_encode($courseDetails);   				
				break;
	  		case 'POST' :
				$params = json_decode(file_get_contents('php://input'),true);	
				$user = wp_get_current_user();
				if ( gradebook_check_user_role('subscriber')){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 						  		
				$wpdb->insert('an_gradebook_courses', 
		    		array('name' => $params['name'], 
		    			'school' => $params['school'], 
		    			'semester' => $params['semester'], 
		    			'year' => $params['year']), 
					array('%s', '%s', '%s', '%d') 
				);
				$gbid = $wpdb -> insert_id;
    			$wpdb->insert('an_gradebook_users', 
		    		array('uid' => $user->ID,'gbid' => $gbid, 'role' => 'instructor'), 
					array('%d', '%d', '%s') 
				);	
				global $an_gradebook_api;
				$user = $an_gradebook_api -> an_gradebook_get_user($user->ID, $gbid);			
				$course = $wpdb->get_row("SELECT * FROM an_gradebook_courses WHERE id = $gbid", ARRAY_A);
				$course['id']=intval($course['id']);
				$course['year']=intval($course['year']);				
				echo json_encode(array('course'=>$course, 'user'=>$user));
				die();					
				break;
	  	}
	  	die();
	}
}	
?>