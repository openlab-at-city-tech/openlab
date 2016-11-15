<?php

class an_gradebook_api{

	public function build_sorter($key) {
		return function ($a, $b) use ($key) {
			return strnatcmp($a[$key], $b[$key]);
		};
	}	

	public function an_gradebook_update_user($id,$first_name,$last_name){
		$user_id = wp_update_user( array ( 
			'ID' => $id, 
			'first_name' => $first_name, 
			'last_name' => $last_name ) ) ;
		$user = get_user_by('id',$user_id);		  
	    return array(
			'first_name' => $user -> first_name,
	    	'last_name' => $user -> last_name,
	    	'id' => $user_id
	    );
	}
	
	public function get_line_chart($uid, $gbid){
		global $wpdb;
		//need to check that user has access to this gradebook.		
		if (!is_user_logged_in()){	
			echo json_encode(array("status" => "Not Allowed."));
			die();
		} 					  	
		///$cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE uid = '. $uid .' AND gbid = '. $gbid, ARRAY_A);	
		$class_cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE gbid = '. $gbid, ARRAY_A);
		$cells = array_map( function($class_cell) use ($uid) {
			if($class_cell['uid'] == $uid){
				return $class_cell;
			}
		}, $class_cells);	
		$cells = array_filter($cells);
		$assignments = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE gbid = '. $gbid, ARRAY_A);

		$cells_points = array();
		$assignments_names = array();
		$assignment_averages = array();
				
		usort($cells, $this->build_sorter('assign_order'));
		usort($assignments, $this->build_sorter('assign_order'));		
	
		foreach($assignments as $assignment){
			$assignment_cells_points = array_map( function($class_cell) use ($assignment) {
				if($class_cell['amid'] == $assignment['id']){
					return floatval($class_cell['assign_points_earned']);
				}
			}, $class_cells);	
			$assignment_cells_points = array_filter($assignment_cells_points);		
			$total_points = array_sum($assignment_cells_points);	
			array_push($assignment_averages, number_format($total_points/ count($assignment_cells_points),2));		
		}
		
		$cells_points = array_map( function($cell){
			return floatval($cell['assign_points_earned']);
		}, $cells);
		
		$assignments_names = array_map( function($assignment){
			return $assignment['assign_name'];
		}, $assignments);
		
		return array(
			'datasets' => array(
				array(
					'label' => "Student Grades",
					'fillColor' => "rgba(220,220,220,0.2)",
					'strokeColor' => "rgba(220,220,220,1)",
					'pointColor' => "rgba(220,220,220,1)",
					'pointStrokeColor' => "#fff",
					'pointHighlightFill' => "#fff",
					'pointHighlightStroke' => "rgba(220,220,220,1)",			
					'data' => $cells_points
				),
        		array(
            		'label' => "Class Average",
            		'fillColor' => "rgba(151,187,205,0.2)",
            		'strokeColor' => "rgba(151,187,205,1)",
					'pointColor'=> "rgba(151,187,205,1)",
					'pointStrokeColor' => "#fff",
					'pointHighlightFill' => "#fff",
					'pointHighlightStroke' => "rgba(151,187,205,1)",
					'data' => $assignment_averages
				)				
			), 
			'labels' => $assignments_names
		);	
	}	
	
	public function get_pie_chart($amid){
		global $wpdb;
		//need to check that user has access to this assignment.
		if (!is_user_logged_in()){	
			echo json_encode(array("status" => "Not Allowed."));
			die();
		}  	
		$pie_chart_data = $wpdb->get_col('SELECT assign_points_earned FROM an_gradebook_cells WHERE amid = '. $amid);	
	
		function isA($n){ return ($n>=90 ? true : false); }
		function isB($n){ return ($n>=80 && $n<90 ? true : false); }
		function isC($n){ return ($n>=70 && $n<80 ? true : false); }
		function isD($n){ return ($n>=60 && $n<70 ? true : false); }
		function isF($n){ return ($n<60 ? true : false); }
	
		$is_A = count(array_filter( $pie_chart_data, 'isA'));
		$is_B = count(array_filter( $pie_chart_data, 'isB'));
		$is_C = count(array_filter( $pie_chart_data, 'isC'));
		$is_D = count(array_filter( $pie_chart_data, 'isD'));	
		$is_F = count(array_filter( $pie_chart_data, 'isF'));	
	
		return array(
			array('value' => $is_A, 'color' => '#F7464A', 'highlight' => '#FF5A5E', 'label' => 'A'),
			array('value' => $is_B, 'color' => '#46BFBD', 'highlight' => '#5AD3D1', 'label' => 'B'),
			array('value' => $is_C, 'color' => '#FDB45C', 'highlight' => '#FFC870', 'label' => 'C'),
			array('value' => $is_D, 'color' => '#949FB1', 'highlight' => '#A8B3C5', 'label' => 'D'),
			array('value' => $is_F, 'color' => '#4D5360', 'highlight' => '#616774', 'label' => 'F')
		);												
			
	}	
	
	public function angb_get_gradebook($gbid, $role, $uid){
		global $current_user, $wpdb;
		if(!$uid){
			$uid = $current_user -> ID;
		}	
		if(!$role){
			$role = $wpdb->get_var('SELECT role FROM an_gradebook_users WHERE gbid = '. $gbid .' AND uid ='. $uid);
		}
		switch($role){
			case 'instructor' :
				$assignments = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE gbid = '. $gbid, ARRAY_A);
				foreach($assignments as &$assignment){
					$assignment['id'] = intval($assignment['id']);
					$assignment['gbid'] = intval($assignment['gbid']);    	
					$assignment['assign_order'] = intval($assignment['assign_order']);       	
				}	
				$cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE gbid = '. $gbid, ARRAY_A);
				foreach($assignments as &$assignment){
					$assignment['gbid'] = intval($assignment['gbid']);
				}		
				$students = $wpdb->get_results('SELECT uid FROM an_gradebook_users WHERE gbid = '. $gbid .' AND role = "student"', ARRAY_N);
				foreach($students as &$student_id){
					$student = get_userdata($student_id[0]);
					$student_id = array(
						'first_name' => $student->first_name, 
						'last_name' => $student->last_name, 
						'user_login' => $student->user_login,
						'id'=>intval($student->ID),
						'gbid' => intval($gbid)
						);
				}
				usort($cells, build_sorter('assign_order')); 
				foreach($cells as &$cell){
					$cell['amid'] = intval($cell['amid']);		
					$cell['uid'] = intval($cell['uid']);				
					$cell['assign_order'] = intval($cell['assign_order']);			
					$cell['assign_points_earned'] = floatval($cell['assign_points_earned']);		
					$cell['gbid'] = intval($cell['gbid']);	
					$cell['id'] = intval($cell['id']);
				}  	
				return array( "assignments" => $assignments,  
					"cells" => $cells,   			
					"students"=>$students,
					"role"=>"instructor"					
			   	);
			case 'student' :
				$assignments = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE assign_visibility = "Students" AND gbid = '. $gbid, ARRAY_A);
				$assignments2=$assignments;
				foreach($assignments as &$assignment){
					$assignment['id'] = intval($assignment['id']);
					$assignment['gbid'] = intval($assignment['gbid']);    	
					$assignment['assign_order'] = intval($assignment['assign_order']);       	
				}	   	
				$assignmentIDsformated ='';
				foreach($assignments as &$assignment){
					$assignmentIDsformated = $assignmentIDsformated. $assignment['id'] . ',';
				}
				$assignmentIDsformated = substr($assignmentIDsformated, 0, -1);
				$cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE amid IN ('.$assignmentIDsformated.') AND uid = '. $current_user->ID , ARRAY_A);    			
				foreach($cells as &$cell){
					$cell['gbid'] = intval($cell['gbid']);
				}		
				$student = get_userdata( $current_user->ID );
				$student = array(
						'first_name' => $student->first_name, 
						'last_name' => $student->last_name, 
						'user_login' => $student->user_login,
						'id'=>intval($student->ID),
						'gbid' => intval($gbid)
				);
				usort($cells, build_sorter('assign_order')); 
				foreach($cells as &$cell){
					$cell['amid'] = intval($cell['amid']);		
					$cell['uid'] = intval($cell['uid']);				
					$cell['assign_order'] = intval($cell['assign_order']);			
					$cell['assign_points_earned'] = floatval($cell['assign_points_earned']);		
					$cell['gbid'] = intval($cell['gbid']);	
					$cell['id'] = intval($cell['id']);
				}  
			   return array(
					"assignments"=>$assignments, 
					"cells" => $cells, 
					"students"=>array($student),
					"role"=>"student",
					"test"=>$assignments2
			   );		
			}
	}
	
	public function an_gradebook_get_user_role($gbid){
		global $wpdb, $current_user;
		$uid = $current_user -> ID;	
		$role = $wpdb->get_var('SELECT role FROM an_gradebook_users WHERE gbid = '. $gbid .' AND uid ='. $uid);	
		return $role;		
	}
	
	public function angb_is_gb_administrator(){
		global $current_user;  
		$x = $current_user->roles;
		$y = array_keys(get_option('an_gradebook_settings'),true);
		$z = array_intersect($x,$y);
		if( count($z) ){	
			return true;
		} else {
			return false;
		}
	}
	
	public function an_gradebook_get_user($id,$gbid){
	  	global $wpdb;
	  	$user = $wpdb->get_row('SELECT * FROM an_gradebook_users WHERE uid = '. $id .' AND gbid ='. $gbid, ARRAY_A);
	  	$user_data = get_user_by('id',$id);
	  	$user_data -> ID;
		$user['id'] = intval($user['id']);
		$user['gbid'] = intval($user['gbid']);					
		$user['uid'] = intval($user['uid']);		
		$user['first_name'] = $user_data->first_name;
		$user['last_name'] = $user_data->last_name;		
		$user['user_login'] = $user_data->user_login;				 			
		return $user;		
	}
	
	public function an_gradebook_create_user($id, $gbid, $first_name,$last_name,$user_login){
			global $wpdb;
			//$gbid is being passed as string, should be int.
			if(!$user_login){ 		   
					$counter = intval($wpdb -> get_var('SELECT MAX(id) FROM wp_users'))+1;
					$result = wp_insert_user(array(
						'user_login' => strtolower($first_name[0].$last_name.$counter),
						'first_name' => $first_name,
						'last_name'  => $last_name,							
						'user_pass'  => 'password'
					));
					if(is_wp_error($result) ){
						echo $result->get_error_message();
						die();
					}		
					$user_id = $result;								
					$wpdb->update($wpdb->users, array('user_login' => strtolower($first_name[0].$last_name).$user_id), 
						array('ID'=> $user_id)
		    		);	
			    	$assignments = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE gbid = '. $gbid, ARRAY_A);
			    	foreach( $assignments as $assignment){
			       		$wpdb->insert('an_gradebook_cells', array(	
			       			'gbid'=> $gbid, 'amid'=> $assignment['id'], 
			       			'uid' => $result, 'assign_order' => $assignment['assign_order']
			       		));
			   		};
					$student = get_user_by('id',$user_id);
					$wpdb->insert('an_gradebook_users', array('uid' => $student->ID,'gbid' => $gbid, 'role' => 'student'));
					$cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE uid = '. $result, ARRAY_A);	
					usort($cells, build_sorter('assign_order'));
					foreach($cells as &$cell){
						$cell['amid'] = intval($cell['amid']);		
						$cell['uid'] = intval($cell['uid']);				
						$cell['assign_order'] = intval($cell['assign_order']);			
						$cell['assign_points_earned'] = intval($cell['assign_points_earned']);		
						$cell['gbid'] = intval($cell['gbid']);	
						$cell['id'] = intval($cell['id']);
					} 			
					return array(
						'student'=> array(
			      			'first_name' => $student -> first_name,
    						'last_name' => $student -> last_name,
							'user_login' => $student -> user_login,    							
				  			'gbid' => intval($gbid),
			     			'id' => intval($result)
			     		),
			      		'cells' => $cells
					);
				}else {
					$user = get_user_by('login',$user_login);
					if($user){
						$result = $wpdb->insert('an_gradebook_users', array('uid' => $user->ID,
							'gbid' => $gbid), 
							array('%d','%d') 
						);
    					$assignments = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE gbid = '. $gbid, ARRAY_A);
			    		foreach( $assignments as $assignment){
		       			$wpdb->insert('an_gradebook_cells', array('gbid'=> $gbid, 
		       					'amid'=> $assignment['id'], 
		          				'uid' => $user->ID, 
		          				'assign_order' => $assignment['assign_order']));
    					};    			
						$role = $wpdb->get_results('SELECT * FROM an_gradebook_users WHERE uid = '. $user->ID . ' AND gbid = '. $gbid, ARRAY_A);												
						
						$cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE uid = '. $user->ID .' AND gbid = '. $gbid, ARRAY_A);										
						usort($cells, build_sorter('assign_order'));
						foreach($cells as &$cell){
							$cell['amid'] = intval($cell['amid']);		
							$cell['uid'] = intval($cell['uid']);				
							$cell['assign_order'] = intval($cell['assign_order']);			
							$cell['assign_points_earned'] = intval($cell['assign_points_earned']);		
							$cell['gbid'] = intval($cell['gbid']);	
							$cell['id'] = intval($cell['id']);
						} 				
						echo json_encode(array('student'=>array(
							'first_name' => $user -> first_name,
							'last_name' => $user -> last_name,
							'user_login' => $user -> user_login,
		    	  			'gbid' => intval($gbid),
	    	  				'id' => $user -> ID,
	  						'role' => $role[0]['role']),	    	  				
	  						'cells' => $cells
		      			));
						die();			
					}
				} 		
	}
}
?>