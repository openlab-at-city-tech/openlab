<?php

class AN_GradeBookAPI{
	public function __construct(){							
		add_action('wp_ajax_an_gradebook_get_settings', array($this, 'an_gradebook_get_settings'));			
		add_action('wp_ajax_an_gradebook_set_settings', array($this, 'an_gradebook_set_settings'));	
		add_action('wp_ajax_get_csv', array($this, 'get_csv'));	
		add_action('wp_ajax_get_pie_chart', array($this, 'get_pie_chart'));	
		add_action('wp_ajax_get_line_chart', array($this, 'get_line_chart'));													
		add_action('wp_ajax_get_gradebook_config', array($this, 'get_gradebook_config'));																									
		add_action('wp_ajax_get_student', array($this, 'get_student'));									
	}
	
	public function an_gradebook_get_settings(){
		wp_cache_delete ( 'alloptions', 'options' );	
		$settings = get_option('an_gradebook_settings');
		echo json_encode(array('gradebook_administrators'=>$settings));
		die();
	}
	
	public function an_gradebook_set_settings(){
		$params = json_decode(file_get_contents('php://input'),true);
		unset($params['action']);
		$params['administrator']=true;	
		wp_cache_delete ( 'alloptions', 'options' );		
		$didupdateQ = update_option('an_gradebook_settings', $params);
		wp_cache_delete ( 'alloptions', 'options' );		
		echo json_encode(array('gradebook_administrators'=>get_option('an_gradebook_settings')));
		die();
	}	
	
	public function get_csv(){
		global $wpdb, $an_gradebook_api; 
		$gbid = $_GET['id'];						
		if (!is_user_logged_in() || $an_gradebook_api -> an_gradebook_get_user_role($gbid)!='instructor'){	
			echo json_encode(array("status" => "Not Allowed."));
			die();
		} 			
		$course = $wpdb->get_row('SELECT * FROM an_gradebook_courses WHERE id = '. $gbid, ARRAY_A);		
		$assignments = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE gbid = '. $gbid, ARRAY_A);		
	    foreach($assignments as &$assignment){
    		$assignment['id'] = intval($assignment['id']);
    		$assignment['gbid'] = intval($assignment['gbid']);    	
	    	$assignment['assign_order'] = intval($assignment['assign_order']);       	
    	}	
    	usort($assignments, build_sorter('assign_order'));     	
    	
		$column_headers_assignment_names = array();

		foreach($assignments as &$assignment){
    		array_push($column_headers_assignment_names, $assignment['assign_name']);
    	}
	    $column_headers = array_merge(
	    	array('firstname','lastname','user_login','id','gbid'),
	    	$column_headers_assignment_names
	    );	
	    $cells= array();	    	
		$cells = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE gbid = '. $gbid, ARRAY_A);
    	foreach($cells as &$cell){
	    	$cell['gbid'] = intval($cell['gbid']);	    	
    	}		
		$students = $wpdb->get_results('SELECT uid FROM an_gradebook_users WHERE gbid = '. $gbid, ARRAY_N);
   		foreach($students as &$value){
	        $studentData = get_userdata($value[0]);
    	    $value = array(
	          	'firstname' => $studentData->first_name, 
    	      	'lastname' => $studentData->last_name, 
    	      	'user_login' => $studentData->user_login,
        	  	'id'=>intval($studentData->ID),
          		'gbid' => intval($gbid)
	          	);
	    }	    	
		foreach($cells as &$cell){
			$cell['amid'] = intval($cell['amid']);		
			$cell['uid'] = intval($cell['uid']);				
			$cell['assign_order'] = intval($cell['assign_order']);			
			$cell['assign_points_earned'] = floatval($cell['assign_points_earned']);		
			$cell['gbid'] = intval($cell['gbid']);	
			$cell['id'] = intval($cell['id']);
		} 
		usort($cells, build_sorter('assign_order')); 		
		$student_records = array(); 
		foreach($students as &$row){
			$records_for_student = array_filter($cells,function($k) use ($row) {
					return $k['uid']==$row['id'];
				});
			$scores_for_student = array_map(function($k){ return $k['assign_points_earned'];}, $records_for_student);		
			$student_record = array_merge($row, $scores_for_student);
			array_push($student_records,$student_record);
		}	
		header('Content-Type: text/csv; charset=utf-8');
		$filename = str_replace(" ", "_", $course['name'].'_'.$gbid);
		header('Content-Disposition: attachment; filename='.$filename.'.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		fputcsv($output, $column_headers);	
		foreach($student_records as &$row){
			fputcsv($output, $row);			
		}	
		fclose($output);	
		die();		
	}
	
	public function get_pie_chart(){
		global $wpdb;
		//need to check that user has access to this assignment.
		if (!is_user_logged_in()){	
			echo json_encode(array("status" => "Not Allowed."));
			die();
		}  	
		$pie_chart_data = $wpdb->get_col('SELECT assign_points_earned FROM an_gradebook_cells WHERE amid = '. $_GET['amid']);	
	
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
	
		$output = array(
			"grades" => array($is_A,$is_B,$is_C,$is_D,$is_F)
		);

		echo json_encode($output);
		die();
	}
	
	public function get_line_chart(){
		global $wpdb;
		//need to check that user has access to this gradebook.		
		$uid = get_current_user_id();
		if (!is_user_logged_in()){	
			echo json_encode(array("status" => "Not Allowed."));
			die();
		} 					  	
		$line_chart_data1 = $wpdb->get_results('SELECT * FROM an_gradebook_cells WHERE uid = '. $_GET['uid'] .' AND gbid = '. $_GET['gbid'],ARRAY_A);	
		$line_chart_data2 = $wpdb->get_results('SELECT * FROM an_gradebook_assignments WHERE gbid = '. $_GET['gbid'],ARRAY_A);
	
		foreach($line_chart_data1 as &$line_chart_value1){
			$line_chart_value1['assign_order']= intval($line_chart_value1['assign_order']);		
			$line_chart_value1['assign_points_earned'] = intval($line_chart_value1['assign_points_earned']);
			foreach($line_chart_data2 as $line_chart_value2){
				if($line_chart_value2['id'] == $line_chart_value1['amid']){
					$all_homework_scores = $wpdb->get_col('SELECT assign_points_earned FROM an_gradebook_cells WHERE amid = '. $line_chart_value2['id']);
					$class_average = array_sum($all_homework_scores)/count($all_homework_scores);
										
					$line_chart_value1=array_merge($line_chart_value1, array('assign_name'=>$line_chart_value2['assign_name'], 'class_average' =>$class_average));
				}
			}
		} 	
		$result = array(array("Assignment", "Student Score", "Class Average"));
		foreach($line_chart_data1 as $line_chart_value3){
			array_push($result, array($line_chart_value3['assign_name'],$line_chart_value3['assign_points_earned'],$line_chart_value3['class_average']));
		}		
				
		
		echo json_encode($result);	
		die();
	}	
	
	public function get_gradebook_config(){
		if (!is_user_logged_in()){	
			echo json_encode(array("status" => "Not Allowed."));
			die();
		}  	
  		global $wpdb;		
  		$user_id = wp_get_current_user()->ID;
  		$wp_role = get_userdata($user_id)->roles;
	  	$user_courses = $wpdb->get_results('SELECT * FROM an_gradebook_users WHERE uid = '. $user_id, ARRAY_A);
		foreach($user_courses as &$user_course){
 			$user_data = get_userdata($user_course['uid']);			
			$user_course['first_name']= $user_data->first_name;
			$user_course['last_name']= $user_data->last_name;
			$user_course['user_login']= $user_data->user_login;
			$user_course['id'] = intval($user_course['id']);
			$user_course['gbid'] = intval($user_course['gbid']);					
			$user_course['uid'] = intval($user_course['uid']);					
		}   
		$sql = '( SELECT gbid FROM an_gradebook_users WHERE uid = '. $user_id. ')';
  		$courses = $wpdb -> get_results("SELECT * FROM an_gradebook_courses WHERE id IN ". $sql, ARRAY_A);
		foreach($courses as &$course){
			$course['id'] = intval($course['id']);				
			$course['year'] = intval($course['year']);			
		}  		
		$administrators = get_option('an_gradebook_settings');
  		echo json_encode(array('administrators' => $administrators, 'courses' => $courses, 'roles'=>$user_courses, 'wp_role'=>$wp_role[0]));
  		die();
	}
}
?>