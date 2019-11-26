<?php
/**
 * Course API
 */
class gradebook_course_API{
	public function __construct(){
		add_action('wp_ajax_course', array($this, 'course'));											
	}

	public function course(){
  		global $wpdb, $oplb_gradebook_api;
                
                $wpdb->show_errors();  	   		  	
		
                $params = $oplb_gradebook_api->oplb_gradebook_get_params();
                $id = $gbid = $params['gbid'];
                
                //user check - only instructors allowed, except for GET requests
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor' 
                        && $params['method'] !== 'GET' 
                        && $params['method'] !== 'POST') {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                //for POST requests, the course doesn't exist yet, so we have to do a more generic user check
                } else if ($params['method'] === 'POST'
                            && !$oplb_gradebook_api->oplb_is_gb_administrator()) {
                    echo json_encode(array("status" => "Not Allowed."));
                    die();
                } else if($params['method'] === 'GET'
                            && $oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor'
                            && $oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'student') {
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
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_courses",array('id'=>$id));
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_assignments",array('gbid'=>$gbid));
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells",array('gbid'=>$gbid));  
	  			$wpdb->delete("{$wpdb->prefix}oplb_gradebook_users",array('gbid'=>$gbid));  	  			
	  			echo json_encode(array('delete_course'=>'Success'));
	  			break;
	  		case 'PUT' :  					
   				$wpdb->update("{$wpdb->prefix}oplb_gradebook_courses", array( 
   					'name' => $params['name'], 'school' => $params['school'], 'semester' => $params['semester'], 
   					'year' => $params['year']),
					array('id' => $gbid)
				);  
                                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = %d", $gbid);
                                $courseDetails = $wpdb->get_row($query, ARRAY_A);
   				echo json_encode($courseDetails);
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
              case 'GET' :
                                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = %d", $gbid);                            
                                $courseDetails = $wpdb->get_row($query, ARRAY_A);

                                if(empty($courseDetails['gradebook_version'])){
                                    $tracker = $oplb_gradebook_api->version_tracker(OPENLAB_GRADEBOOK_VERSION, $courseDetails);

                                    if($tracker){
                                        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = %d", $gbid);                            
                                        $courseDetails = $wpdb->get_row($query, ARRAY_A);
                                        echo json_encode($courseDetails);
                                    } else {
                                        echo json_encode($courseDetails);
                                    }

                                } else {
                                    echo json_encode($courseDetails);
                                }

   						
				break;
	  		case 'POST' :		
                                $user = wp_get_current_user();
                            
                                //handle values that cannot be NULL
                                if(!$params['name']){
                                    $params['name'] = 'Needs Name';
                                }
                                
                                if(!$params['school']){
                                    $params['school'] = '';
                                }

                                if(!$params['semester']){
                                    $params['semester'] = '';
                                }

                                if(!$params['year']){
                                    $params['year'] = date('Y');
                                }
                            
				$wpdb->insert("{$wpdb->prefix}oplb_gradebook_courses", 
		    		array('name' => $params['name'], 
		    			'school' => $params['school'], 
		    			'semester' => $params['semester'], 
		    			'year' => $params['year']), 
					array('%s', '%s', '%s', '%d') 
				);
				$gbid = $wpdb -> insert_id;
                                
                                //before creating an instructor, see if an initial instructor was created, and use that instructor first
                                $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}oplb_gradebook_users WHERE gbid = %d AND role = %s", 0, 'instructor');
                                $init_instructor = $wpdb->get_results($query);
                                
                                if (!empty($init_instructor)) {
                                    
                                    $target_id = $init_instructor[0]->id;
                                    
                                    $wpdb->update("{$wpdb->prefix}oplb_gradebook_users", array(
                                        'gbid' => $gbid
                                            ), array(
                                        'id' => $target_id,
                                            ), array(
                                        '%d',
                                            ), array(
                                        '%d',
                                            )
                                    );
                                    
                                } else {

                                    $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
                                        'uid' => $user->ID,
                                        'gbid' => $gbid,
                                        'role' => 'instructor'), array(
                                        '%d',
                                        '%d',
                                        '%s')
                                    );
                                }

                                global $oplb_gradebook_api;
				$user = $oplb_gradebook_api -> oplb_gradebook_get_user($user->ID, $gbid);	
                                $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}oplb_gradebook_courses WHERE id = %d", $gbid);
				$course = $wpdb->get_row($query, ARRAY_A);
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