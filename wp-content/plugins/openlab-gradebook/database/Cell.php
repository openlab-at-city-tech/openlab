<?php
class gradebook_cell_API{
	public function __construct(){
		add_action('wp_ajax_cell', array($this, 'cell'));											
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
*   cell api
*
**************************/	
	

	public function cell(){
		global $wpdb, $oplb_gradebook_api;
   		$wpdb->show_errors();  		   		
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
			case 'DELETE' :  
	  			echo json_encode(array('delete'=>'deleting'));
	  			break;
	  		case 'PUT' :
	  			$params = json_decode(file_get_contents('php://input'),true);
	  			$gbid = $params['gbid'];  			
				if ( $oplb_gradebook_api -> oplb_gradebook_get_user_role($gbid)!='instructor'){	
					echo json_encode(array("status" => "Not Allowed."));
					die();
				} 		  					  			
   				$wpdb->update("{$wpdb->prefix}oplb_gradebook_cells", array( 'assign_order'=>$params['assign_order'], 'assign_points_earned' => $params['assign_points_earned']),
					array( 'uid' => $params['uid'], 'amid' => $params['amid'] )
   				);   
                                $assign_points_earned = $wpdb->get_row("SELECT assign_points_earned FROM {$wpdb->prefix}oplb_gradebook_cells WHERE uid = {$params['uid']} AND amid = {$params['amid']}" , ARRAY_A);
                                
                                $current_grade_average = $oplb_gradebook_api->oplb_gradebook_get_current_grade_average($params['uid'], $gbid);
                                
                                $data_back = array(
                                    'current_grade_average' => $current_grade_average,
                                    'assign_points_earned' => floatval($assign_points_earned['assign_points_earned']),
                                    'uid' => $params['uid'],
                                );
                                
   				echo json_encode($data_back);
   				die();	  			
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
				echo json_encode(array("post" => "posting"));		  		
				break;
	  	}
	  	die();
	}
}
?>