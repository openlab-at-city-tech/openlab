<?php
class ANGB_STATISTICS{
	public function __construct(){
		add_action('wp_ajax_angb_statistics', array($this, 'statistics'));											
	}
	
	public function statistics(){
		global $wpdb, $an_gradebook_api;
   		$wpdb->show_errors();  			
		$method = (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
		switch ($method){
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
				$params = $_GET;
				if($params['chart_type'] == 'line_chart'){
					$result = $an_gradebook_api -> get_line_chart($params['uid'],$params['gbid']);
				} else {
					$result = $an_gradebook_api -> get_pie_chart($params['amid']);				
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