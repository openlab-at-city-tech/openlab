<?php
class ANGB_COURSE_LIST{
	public function __construct(){
		add_action('wp_ajax_course_list', array($this, 'course_list'));											
	}

	public function course_list(){
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
   					'name' => $params['name'], 
   					'school' => $params['school'], 
   					'semester' => $params['semester'], 
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
  				$user_id = wp_get_current_user()->ID;
				$sql = '( SELECT gbid FROM an_gradebook_users WHERE uid = '. $user_id. ')';
  				$courses = $wpdb -> get_results("SELECT * FROM an_gradebook_courses WHERE id IN ". $sql, ARRAY_A);
				foreach($courses as &$course){
					$course['id'] = intval($course['id']);				
					$course['year'] = intval($course['year']);			
				}  		
  				echo json_encode(array('course_list' => $courses));
				break;
	  		case 'POST' :
				$params = json_decode(file_get_contents('php://input'),true);	
				$user = wp_get_current_user();
				if ( !$an_gradebook_api->angb_is_gb_administrator() ){	
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