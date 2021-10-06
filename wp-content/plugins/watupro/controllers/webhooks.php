<?php
// Webhooks / Zapier management and integration
class WatuPROWebhooks {
	public static function manage() {
		global $wpdb;
		$action = empty($_GET['action']) ? 'list' : $_GET['action'];
		
		// select exams
		$exams = $wpdb->get_results("SELECT ID, name FROM ".WATUPRO_EXAMS." ORDER BY name");
		// select all non-category grades and match them to exams and relations
   	$grades = $wpdb->get_results("SELECT * FROM ".WATUPRO_GRADES." WHERE cat_id=0 ORDER BY gtitle");
   	foreach($exams as $cnt=>$exam) {
   	  	  $exam_grades = array();
   	  	  foreach($grades as $grade) {
   	  	  	if($grade->exam_id == $exam->ID) $exam_grades[] = $grade;
			  }
			  
			  $exams[$cnt]->grades = $exam_grades;
   	 }
		
		switch($action) {
			case 'add':
				if(!empty($_POST['ok']) and check_admin_referer('watupro_webhooks')) {
					$payload_config = self :: payload_config();
					$wpdb->query($wpdb->prepare("INSERT INTO ".WATUPRO_WEBHOOKS." SET exam_id=%d, grade_id=%d, hook_url = %s, payload_config=%s",
					intval($_POST['exam_id']), intval($_POST['grade_id']), esc_url_raw($_POST['hook_url']), serialize($payload_config) ));
					
					watupro_redirect("admin.php?page=watupro_webhooks");
				}
				
				if(@file_exists(get_stylesheet_directory().'/watupro/webhook.html.php')) require get_stylesheet_directory().'/watupro/webhook.html.php';
				else require WATUPRO_PATH."/views/webhook.html.php";
			break;
			
			case 'edit':
				if(!empty($_POST['test'])	and check_admin_referer('watupro_webhooks')) {
					list($data, $result) = self :: test($_GET['id']);
				}		
			
				if(!empty($_POST['ok']) and check_admin_referer('watupro_webhooks')) {
					$payload_config = self :: payload_config();
					$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_WEBHOOKS." SET exam_id=%d, grade_id=%d, hook_url = %s, payload_config=%s WHERE ID=%d",
					intval($_POST['exam_id']), intval($_POST['grade_id']), esc_url_raw($_POST['hook_url']), serialize($payload_config), intval($_GET['id'])) );
				}
				
				// select hook
				$hook = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_WEBHOOKS." WHERE ID=%d", intval($_GET['id'])));
				$payload_config = unserialize(stripslashes($hook->payload_config));
				
				// select grades for this hook's test
				$hook_grades = WTPGrade :: get_grades($hook->exam_id);
				
				if(@file_exists(get_stylesheet_directory().'/watupro/webhook.html.php')) require get_stylesheet_directory().'/watupro/webhook.html.php';
				else require WATUPRO_PATH."/views/webhook.html.php";
			break;
			
			case 'list':
			default: 
				if(!empty($_GET['delete']) and wp_verify_nonce($_GET['watupro_hook_nonce'], 'delete_hook')) {
					$wpdb->query($wpdb->prepare("DELETE FROM ".WATUPRO_WEBHOOKS." WHERE ID=%d", intval($_GET['id'])));
					watupro_redirect("admin.php?page=watupro_webhooks");
				}			
			
				// select hooks join grades
				$hooks = $wpdb->get_results("SELECT tH.ID as ID, tH.grade_id as grade_id, tH.hook_url as hook_url, 
					tE.name as quiz_name, tG.gtitle as grade
					FROM ".WATUPRO_WEBHOOKS." tH JOIN ".WATUPRO_EXAMS." tE ON tH.exam_id = tE.ID
					LEFT JOIN ".WATUPRO_GRADES." tG ON tH.grade_id = tG.ID
					ORDER BY tH.ID");		
					
				// depending if there are hooks, set the option
				update_option('watupro_webhooks', count($hooks));		
			
				if(@file_exists(get_stylesheet_directory().'/watupro/webhooks.html.php')) require get_stylesheet_directory().'/watupro/webhooks.html.php';
				else require WATUPRO_PATH."/views/webhooks.html.php";
			break;
		}
	} // end manage
	
	// called on submit_exam action, figures out whether any webhooks should be sent
	public static function dispatch($taking_id) {
		global $wpdb;
		
		// to avoid unnecessary queries this option is set to 1 only if there are webhooks in the system
		if(get_option('watupro_webhooks') <= 0) return false;
		
		$taking = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		
		$email = $taking->email;
		$name = $taking->name;
		if(( empty($email) or empty($name) ) and !empty($taking->user_id)) {
			$user = get_userdata($taking->user_id);
      	if(empty($email)) $email = $user->user_email;
      	if(empty($name)) $name = $user->display_name;
		}	
		// name still empty? Default to guest although that's not a great idea
		if(empty($name)) $name = 'Guest';
		
		// do not continue if email is empty
		if(empty($email)) return false;
		
		$hooks = $wpdb -> get_results($wpdb->prepare("SELECT * FROM ".WATUPRO_WEBHOOKS." 
			WHERE exam_id=%d AND (grade_id=0 OR grade_id=%d) ORDER BY ID", $taking->exam_id, $taking->grade_id));
			
		foreach($hooks as $hook) {
			// prepare data
			$config = unserialize(stripslashes($hook->payload_config));
			$data = [];
			
			foreach($config as $key => $setting) {				
				if(empty($setting) or !is_array($setting)) continue;
				
				// all keys are predefined. $setting['name'] is the customizable param name
				// $setting['value'] is empty and should come from $taking data except on the custom pre-filled keys
				switch($key) {
					case 'email':
						$data[$setting['name']] = $email;
					break;
					case 'name':
						$data[$setting['name']] = stripslashes($name);
					break;
					case 'field_phone':
						$data[$setting['name']] = stripslashes($taking->field_phone);
					break;
					case 'field_company':
						$data[$setting['name']] = stripslashes($taking->field_company);
					break;
					case 'custom_field1':
						$data[$setting['name']] = stripslashes($taking->custom_field1);
					break;
					case 'custom_field2':
						$data[$setting['name']] = stripslashes($taking->custom_field1);
					break;
					case 'custom_key1':
					case 'custom_key2':
					case 'custom_key3':
						$data[$setting['name']] = stripslashes($setting['value']);
					break;
				} // end switch
			} // end foreach config param
			
			self :: send($hook->hook_url, $data);			
			
		} // end foreach hook	
	} // end dispatch
	
	// send webhook
	public static function send($url, $data) {
		$args = array(
	        'headers' => array(
	            'Content-Type' => 'application/json',
	        ),
	        'body' => json_encode( $data )
	    );

	    //$return = wp_remote_post( $url, $args );
	    
	    // probably make includings headers optional?
	    $headers = ['Content-Type' => 'application/json',];
	    
	    $args = ['body' => $data];
	    $return = wp_remote_post( $url, $args);
		if(is_wp_error($return)) {
			$error_string = $return->get_error_message();
   		echo '<div id="message" class="error"><p>' . sprintf(__('Webhook error: %s', 'watupro'), $error_string) . '</p></div>';
   		return false;
		}
	   return true;
	} // end send

	// test a hook	
	public static function test($hook_id) {
		global $wpdb;
		
		$hook = $wpdb -> get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_WEBHOOKS." WHERE ID=%d", intval($hook_id)));
		$config = unserialize(stripslashes($hook->payload_config));
		$data = [];
		
		foreach($config as $key => $setting) {				
				if(empty($setting) or !is_array($setting)) continue;
				
				// all keys are predefined. $setting['name'] is the customizable param name
				// $setting['value'] is empty and should come from $taking data except on the custom pre-filled keys
				switch($key) {
					case 'email':
						$data[$setting['name']] = get_option('admin_email');
					break;
					case 'name':
						$data[$setting['name']] = "Test Name";
					break;
					case 'field_phone':
						$data[$setting['name']] = "555-555-555";
					break;
					case 'field_company':
						$data[$setting['name']] = "Test Company";
					break;
					case 'custom_field1':
						$data[$setting['name']] = "Custom field 1";
					break;
					case 'custom_field2':
						$data[$setting['name']] = "Custom field 2";
					break;
					case 'custom_key1':
					case 'custom_key2':
					case 'custom_key3':
						$data[$setting['name']] = stripslashes($setting['value']);
					break;
				} // end switch
			} // end foreach config param
			
			$args = array(

	        'headers' => array(
	            'Content-Type' => 'application/json',
	        ),
	        'body' => json_encode( $data )
	    );
			
		  $return = wp_remote_post( $hook->hook_url, $args );
		  
		  return [$data, $return];
	} // end test
	
	// helper to prepare the payload_config array
	private static function payload_config() {
		$payload_config = [];
		if(!empty($_POST['email_name']))	$payload_config['email'] = ['name' => sanitize_text_field($_POST['email_name'])];
		if(!empty($_POST['name_name']))	$payload_config['name'] = ['name' => sanitize_text_field($_POST['name_name'])];
		if(!empty($_POST['field_phone_name']))	$payload_config['field_phone'] = ['name' => sanitize_text_field($_POST['field_phone_name'])];
		if(!empty($_POST['field_company_name']))	$payload_config['field_company'] = ['name' => sanitize_text_field($_POST['field_company_name'])];
		if(!empty($_POST['custom_field1_name']))	$payload_config['custom_field1'] = ['name' => sanitize_text_field($_POST['custom_field1_name'])];
		if(!empty($_POST['custom_field2_name']))	$payload_config['custom_field2'] = ['name' => sanitize_text_field($_POST['custom_field2_name'])];
		if(!empty($_POST['custom_key1_name']))	$payload_config['custom_key1'] = ['name' => sanitize_text_field($_POST['custom_key1_name']), "value" => sanitize_text_field($_POST['custom_key1_value'])];
		if(!empty($_POST['custom_key2_name']))	$payload_config['custom_key2'] = ['name' => sanitize_text_field($_POST['custom_key2_name']), "value" => sanitize_text_field($_POST['custom_key2_value'])];
		if(!empty($_POST['custom_key3_name']))	$payload_config['custom_key3'] = ['name' => sanitize_text_field($_POST['custom_key3_name']), "value" => sanitize_text_field($_POST['custom_key_value'])];
		
		return $payload_config;
	} // end payload_config
}