<?php
class gradebook_assignment_API{
	public function __construct(){
		add_action('wp_ajax_assignment', array($this, 'assignment'));											
	}
	
	public function assignment(){
		global $wpdb, $an_gradebook_api;
   		$wpdb->show_errors();  			
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
			case 'DELETE' :  
				parse_str($_SERVER['QUERY_STRING'],$params);	
				$id = $params['id'];
				$gbid = $wpdb->get_var('SELECT gbid FROM an_gradebook_assignments WHERE id = '.$id);
				if ( $an_gradebook_api -> an_gradebook_get_user_role($gbid)!='instructor'){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 								
 				$wpdb->delete('an_gradebook_cells', array('amid'=> $id));
 				$wpdb->delete('an_gradebook_assignments', array('id'=> $id)); 	
 				echo json_encode(array('id'=> $id));   						
	  			break;
	  		case 'PUT' :
	  			$params = json_decode(file_get_contents('php://input'),true);	
				$gbid = $params['gbid'];
				if ( $an_gradebook_api -> an_gradebook_get_user_role($gbid)!='instructor'){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 	  			
   				$wpdb->update('an_gradebook_assignments', array( 'assign_name' => $params['assign_name'], 'assign_date' => $params['assign_date'],
   					'assign_due' => $params['assign_due'], 'assign_order'=>$params['assign_order'], 'assign_category' => $params['assign_category'], 
   					'assign_visibility' => $params['assign_visibility_options']), 
   					array('id' => $params['id'] )
   				);   
   				$wpdb->update('an_gradebook_cells', array( 'assign_order' => $params['assign_order']), array('amid' => $params['id'] )
   				);     				
   				$assignment = $wpdb->get_row('SELECT * FROM an_gradebook_assignments WHERE id = '. $params['id'] , ARRAY_A);
   				$assignment['id'] = intval($assignment['id']);   				
   				$assignment['gbid'] = intval($assignment['gbid']);  
   				$assignment['assign_order'] = intval($assignment['assign_order']);    				  				
   				echo json_encode($assignment);
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :
				echo json_encode(array("get" => "getting"));	
				break;
	  		case 'POST' :	
				$params = json_decode(file_get_contents('php://input'),true);	
				$gbid = $params['gbid'];
				if ( $an_gradebook_api -> an_gradebook_get_user_role($gbid)!='instructor'){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 					  		
    			$assignOrders = $wpdb->get_col('SELECT assign_order FROM an_gradebook_assignments WHERE gbid = '. $params['gbid']);    
    			if(!$assignOrders){
    				$assignOrders = array(0);
    			}
    			$assignOrder = max($assignOrders)+1;
				$wpdb->insert('an_gradebook_assignments', array( 
					'assign_name' => $params['assign_name'],
					'assign_date' => $params['assign_date'],					
					'assign_due' => $params['assign_due'],					
					'assign_category' => $params['assign_category'],						
					'assign_visibility' => $params['assign_visibility_options'],						
					'gbid' => $params['gbid'],
					'assign_order'=> $assignOrder
				), array( '%s','%s','%s','%s','%s','%d','%d') 
				);
				$assignID = $wpdb->insert_id;
			    $studentIDs = $wpdb->get_results('SELECT uid FROM an_gradebook_users WHERE gbid = '. $params['gbid'] . ' AND role = "student"', ARRAY_N);
			    foreach($studentIDs as $value){
					$wpdb->insert('an_gradebook_cells', array( 
						'amid' => $assignID,
						'uid' => $value[0],
						'gbid' => $params['gbid'],
						'assign_order' => $assignOrder,
						'assign_points_earned' => 0
					), array( '%d','%d','%d','%d') 
					);
				}
				$assignment = $wpdb->get_row("SELECT * FROM an_gradebook_assignments WHERE id = $assignID", ARRAY_A);
				$assignment['assign_order'] = intval($assignment['assign_order']);					
				$assignment['gbid'] = intval($assignment['gbid']);	
				$assignment['id'] = intval($assignment['id']);
		
				$cells = $wpdb->get_results("SELECT * FROM an_gradebook_cells WHERE amid = $assignID", ARRAY_A);
				foreach($cells as &$cell){
					$cell['amid'] = intval($cell['amid']);		
					$cell['uid'] = intval($cell['uid']);				
					$cell['assign_order'] = intval($cell['assign_order']);			
					$cell['assign_points_earned'] = intval($cell['assign_points_earned']);		
					$cell['gbid'] = intval($cell['gbid']);	
					$cell['id'] = intval($cell['id']);
				} 		
				$data = array('assignment'=>$assignment,'cells'=>$cells);
				echo json_encode($data);				
				break;
   			}	  						  						
	  	die();
	}
}
?>