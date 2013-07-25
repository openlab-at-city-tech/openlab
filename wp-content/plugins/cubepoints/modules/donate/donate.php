<?php

/** Donate Module */

cp_module_register(__('Donate', 'cp') , 'donate' , '1.2', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('This module allows your users to donate points to each other.', 'cp'), 1);

if(cp_module_activated('donate')){

	add_action( 'wp_ajax_cp_donate_search', 'cp_module_donate_ajax_search' );
	function cp_module_donate_ajax_search() {

		header( "Content-Type: application/json" );
		
		if( $_REQUEST['q']=='' ){
			$response = json_encode( array() );
			echo $response;
			exit;
		}
		
		global $wpdb;
		$users = $wpdb->get_results('SELECT ID, user_login, first_name, last_name, md5(user_email) as email_hash FROM `' . $wpdb->prefix . 'users`
										LEFT JOIN (
											SELECT
												user_id,
												meta_value as first_name
											FROM `' . $wpdb->prefix . 'usermeta`
											WHERE (	meta_key =  \'first_name\' )
										) AS A
										ON id = A.user_id
										LEFT JOIN (
											SELECT
												user_id,
												meta_value as last_name
											FROM `' . $wpdb->prefix . 'usermeta`
											WHERE (	meta_key =  \'last_name\' )
										) AS B
										ON id = B.user_id
										WHERE
											user_login  LIKE \''. $wpdb->escape($_REQUEST['q']) .'%\'
											OR CONCAT_WS(\' \', first_name, last_name) LIKE \''. $wpdb->escape($_REQUEST['q']) .'%\'
											OR CONCAT_WS(\' \', last_name, first_name) LIKE \''. $wpdb->escape($_REQUEST['q']) .'%\'
											OR CONCAT_WS(\' \', first_name, last_name) LIKE \'% '. $wpdb->escape($_REQUEST['q']) .'%\'
											OR CONCAT_WS(\' \', last_name, last_name) LIKE \'% '. $wpdb->escape($_REQUEST['q']) .'%\'
										ORDER BY CASE
											WHEN user_login  LIKE \''. $wpdb->escape($_REQUEST['q']) .'%\' THEN 1 ELSE 2 END 
										LIMIT 30');
		$response = array();
		
		foreach($users as $u){
			$response[] = array(
							'id' => $u->ID,
							'ul' => $u->user_login,
							'fn' => $u->first_name,
							'ln' => $u->last_name,
							'eh' => $u->email_hash
							);
		}
		$response = json_encode($response);
		echo $response;
		exit;
		
	}

	function cp_module_donate_scripts(){
	
		wp_register_script('impromptu',
		   CP_PATH . 'modules/donate/jquery-impromptu.4.0.min.js',
		   array('jquery'),
		   '4.0' );

		wp_register_style('cp_donate', CP_PATH . 'modules/donate/donate.css');

		wp_enqueue_script('impromptu');
		wp_enqueue_style('cp_donate');
		
	}

	add_action('init', 'cp_module_donate_scripts');
	
	function cp_module_donate_do(){
		$recipient = $_POST['recipient'];
		$points = $_POST['points'];
		$message = htmlentities(stripslashes($_POST['message']), ENT_QUOTES, 'UTF-8');
		$user =  get_user_by('id', $recipient);

		if(!is_user_logged_in()){
			$r['success'] = false;
			$r['message'] = __('You must be logged in to make a donation!', 'cp');
		}
		
		else if($recipient==''){
			$r['success'] = false;
			$r['message'] = __('Please enter the username of the recipient!', 'cp');
		}
		
		else if($user->ID==''){
			$r['success'] = false;
			$r['message'] = __('You have entered an invalid recipient!', 'cp');
		}
		
		else if($user->ID==cp_currentUser()){
			$r['success'] = false;
			$r['message'] = __('You cannot donate to yourself!', 'cp');
		}
		
		else if(!is_numeric($points)){
			$r['success'] = false;
			$r['message'] = __('You have entered an invalid number of points!', 'cp');
		}
		
		else if((int)$points<1){
			$r['success'] = false;
			$r['message'] = __('You have to donate at least one point!', 'cp');
		}
		
		else if((int)$points != (float) $points){
			$r['success'] = false;
			$r['message'] = __('You have entered an invalid number of points!', 'cp');
		}
		
		else if((int)$points>(int)cp_getPoints(cp_currentUser())){
			$r['success'] = false;
			$r['message'] = __('You do not have that many points to donate!', 'cp');
		}
		
		else if(strlen($message)>160){
			$r['success'] = false;
			$r['message'] = __('The message you have entered is too long!', 'cp');
		}
		
		else{
			$message = mb_convert_encoding($message, 'HTML-ENTITIES', 'UTF-8');
			$r['success'] = true;
			$r['message'] = __('Your donation is successful!', 'cp');
			cp_points('donate_from', $user->ID, $points, serialize(array("from"=>cp_currentUser(),"message"=>$message)));
			cp_points('donate_to', cp_currentUser(), -$points, serialize(array("to"=>$user->ID,"message"=>$message)));
			$r['pointsd'] = cp_displayPoints(0, 1, 1);
			$r['points'] = cp_displayPoints(0, 1, 0);
		}

		echo json_encode($r);
		die();
	}
	
	add_action('wp_ajax_cp_module_donate_do', 'cp_module_donate_do');
	add_action('wp_ajax_nopriv_cp_module_donate_do', 'cp_module_donate_do');
	
	// Handle JS
	function cp_module_donate_script(){
		wp_register_script('cp_donate_script', WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'donate.js', array('jquery'));
		wp_enqueue_script('cp_donate_script');
		wp_localize_script( 'cp_donate_script', 'cp_donate', array(
			'logged_in' =>  is_user_logged_in() ? '1' : '0',
			'ajax_url' =>  admin_url( 'admin-ajax.php' ),
			'title' => __('Points Transfer', 'cp'),
			'searchText' => __('Type a username and press Enter to search...', 'cp'),
			'searchingText' => __('Searching, please wait...', 'cp'),
			'searchPlaceholder' => __('Search...', 'cp'),
			'nothingFound' => __('Nothing Found', 'cp'),
			'amountToDonate' => __('Enter amount of points to transfer...', 'cp'),
			'donateAmountPlaceholder' => __('Amount to transfer...', 'cp'),
			'donateComment' => __('Leave feedback (to be displayed on recipient\'s profile)', 'cp'),
			'donateCommentPlaceholder' => __('Enter a message...', 'cp'),
			'notLoggedInText' => __('You must be logged in to make a transfer!', 'cp'),
			'somethingWentWrongText' => _('Oops, something went wrong! Please try again later.', 'cp')
		) );
	}

	add_action('init', 'cp_module_donate_script');
	
	function cp_module_donate_widget(){
		if(is_user_logged_in()){
			?>
				<li><a href="javascript:void(0);" onclick="cp_module_donate();"><?php _e('Donate', 'cp'); ?></a></li>
			<?php
		}
	}

	add_action('cp_pointsWidget', 'cp_module_donate_widget',1000);
	
	/** Donations log hook */
	add_action('cp_logs_description','cp_admin_logs_desc_donate', 10, 4);
	function cp_admin_logs_desc_donate($type,$uid,$points,$data){
		if($type!='donate_to'&&$type!='donate_from') { return; }
		$data = unserialize($data);
		$user = get_userdata($data['to']+$data['from']);
		if($type=='donate_to'){ 
			echo __('Donation to', 'cp') . ' "' . $user->user_login . '"';
		}
		if($type=='donate_from'){ 
			echo __('Donation from', 'cp') . ' "' . $user->user_login . '"';
		}
		if($data['message']!=''){
			echo ' <a href="javascript:void(0);" onclick="jQuery.prompt(\'MESSAGE: '.htmlspecialchars($data['message']).'\')">[' . __('Message', 'cp') . ']</a>';
		}
	}
	
}
?>