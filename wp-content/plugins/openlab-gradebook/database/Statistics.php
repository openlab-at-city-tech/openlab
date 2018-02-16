<?php
class OPLB_STATISTICS{
	public function __construct(){
		add_action('wp_ajax_oplb_statistics', array($this, 'statistics'));											
	}
	
	public function statistics(){
		global $wpdb, $oplb_gradebook_api;
   		$wpdb->show_errors();  			
		
                $params = $oplb_gradebook_api->oplb_gradebook_get_params();
                $gbid = $params['gbid'];

                //user check - only instructors allowed in
                if ($oplb_gradebook_api->oplb_gradebook_get_user_role_by_gbid($gbid) !== 'instructor'
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
				echo json_encode(array('delete'=>'deleting'));
 				die();			
	  			break;
	  		case 'PUT' :
				echo json_encode(array('put'=>'putting'));
 				die();	
				break;
	  		case 'UPDATE' :
				echo json_encode(array("update" => "updating"));				
				break;
	  		case 'PATCH' :
				echo json_encode(array("patch" => "patching"));				
				break;
	  		case 'GET' :
				if($params['chart_type'] == 'line_chart'){
					$result = $oplb_gradebook_api -> get_line_chart($params['uid'],$params['gbid']);
				} else {
					$result = $oplb_gradebook_api -> get_pie_chart($params['amid']);				
				}
				echo json_encode($result);
				die();	
				break;
	  		case 'POST' :	
				echo json_encode(array('post'=>'posting'));
 				die();							  		
				break;	  	
		}
	die();
	}
}
?>