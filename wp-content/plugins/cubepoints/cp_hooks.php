<?php
/**
 * CubePoints plugin hooks and filters
 */

 /** Misc logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_misc', 10, 4);
function cp_admin_logs_desc_misc($type,$uid,$points,$data){
	if($type!='misc') { return; }
	echo $data;
}

 /** Add Points logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_addpoints', 10, 4);
function cp_admin_logs_desc_addpoints($type,$uid,$points,$data){
	if($type!='addpoints') { return; }
	echo $data;
}
 
/** Comments hook */
add_action('comment_post', 'cp_newComment', 10 ,2);
function cp_newComment($cid, $status) {
	$cdata = get_comment($cid);
	if($status == 1){
		do_action('cp_comment_add', $cid);
		cp_points('comment', cp_currentUser(), apply_filters('cp_comment_points',get_option('cp_comment_points')), $cid);
	}
}

/** Comment approved hook */
add_action('comment_unapproved_to_approved', 'cp_commentApprove', 10, 1);
add_action('comment_trash_to_approved', 'cp_commentApprove', 10, 1);
add_action('comment_spam_to_approved', 'cp_commentApprove', 10, 1);
function cp_commentApprove($cdata){
	do_action('cp_comment_add', $cdata->comment_ID);
	cp_points('comment', $cdata->user_id, apply_filters('cp_comment_points',get_option('cp_comment_points')), $cdata->comment_ID);
}

/** Comment unapproved hook */
add_action('comment_approved_to_unapproved', 'cp_commentUnapprove', 10, 1);
add_action('comment_approved_to_trash', 'cp_commentUnapprove', 10, 1);
add_action('comment_approved_to_spam', 'cp_commentUnapprove', 10, 1);
function cp_commentUnapprove($cdata){
	// check if points were indeed awarded for this comment
	global $wpdb;
	if($wpdb->get_var('SELECT COUNT(*) FROM ' . CP_DB . ' WHERE type = \'comment\' AND data = ' . $cdata->comment_ID)==0){
		return;
	}
	do_action('cp_comment_remove', $cdata->comment_ID);
	cp_points('comment_remove', $cdata->user_id, apply_filters('cp_del_comment_points',-get_option('cp_del_comment_points')), $cdata->comment_ID);
}

/** Comments logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_comment', 10, 4);
function cp_admin_logs_desc_comment($type,$uid,$points,$data){
	if($type!='comment') { return; }
	$cdata = get_comment($data);
	if($cdata==null){ echo '<span title="'.__('Comment removed', 'cp').'...">'.__('Comment', 'cp').'</span>'; return; }
	$pid = $cdata->comment_post_ID;
	$pdata = get_post($pid);
	$ptitle = $pdata->post_title;
	$url = get_permalink( $pid ) . '#comment-' . $data;
	$detail = __('Comment', 'cp').': '.cp_truncate(strip_tags($cdata->comment_content), 100, false);
	echo '<span title="'.$detail.'">'.__('Comment on', 'cp').' &quot;<a href="'.$url.'">'.$ptitle.'</a>&quot;</span>';
}

/** Comments removal logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_comment_remove', 10, 4);
function cp_admin_logs_desc_comment_remove($type,$uid,$points,$data){
	if($type!='comment_remove') { return; }
	_e('Comment Deletion', 'cp');
}

/** Post hook */
add_action('publish_post', 'cp_newPost');
function cp_newPost($pid) {
	$post = get_post($pid);
	$uid = $post->post_author;
	global $wpdb;
	$count = (int) $wpdb->get_var("select count(id) from `".CP_DB."` where `type`='post' and `data`=". $pid);
	if($count==0){
		cp_points('post', $uid, apply_filters('cp_post_points',get_option('cp_post_points')), $pid);
	}
}

/** Post logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_post', 10, 4);
function cp_admin_logs_desc_post($type,$uid,$points,$data){
	if($type!='post') { return; }
	$post = get_post($data);
	echo __('Post on', 'cp') . ' "<a href="'.get_permalink( $post ).'">' . $post->post_title . '</a>"';
}

/** User registration hook */
add_action('user_register', 'cp_newUser');
function cp_newUser($uid) {
	cp_points('register', $uid, apply_filters('cp_reg_points',get_option('cp_reg_points')), $uid);
}

/** User registration logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_register', 10, 4);
function cp_admin_logs_desc_register($type,$uid,$points,$data){
	if($type!='register') { return; }
	_e('Registration', 'cp');
}

/** Admin manage logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_admin', 10, 4);
function cp_admin_logs_desc_admin($type,$uid,$points,$data){
	if($type!='admin') { return; }
	$user = get_userdata($data);
	echo __('Points adjusted by ', 'cp') . ' "' .  $user->user_login . '"';
}

/** Remote site logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_remote', 10, 4);
function cp_admin_logs_desc_remote($type,$uid,$points,$data){
	if($type!='remote') { return; }
	list($name,$url) = explode('^', $data);
	echo __('Points earned from ') . ' "<a href="'.$url.'">' . $name . '</a>"';
}

/** Custom logs hook */
add_action('cp_logs_description','cp_admin_logs_desc_custom', 10, 4);
function cp_admin_logs_desc_custom($type,$uid,$points,$data){
	if($type!='custom') { return; }
	echo $data;
}

/** Display top users in page */
add_shortcode('cubepoints_top','cp_shortcode_top');
function cp_shortcode_top( $atts ){
	$num = (int) $atts['num'];
	if($num<1){$num=1;}
	$top = cp_getAllPoints($num,get_option('cp_topfilter'));
	if($atts['class']!=''){$class = ' class="'.$atts['class'].'"';}
	if($atts['style']!=''){$style = ' style="'.$atts['style'].'"';}
	switch ($atts['display']) {
    case 'custom':
        if($atts['custom'] == null) {
			$atts['custom'] = '%user% (%points%)';
		}
		foreach($top as $x=>$i) {
			$text = apply_filters('cp_displayUserInfo',$atts['custom'],$i,$x+1);
			$c .= $text;
		}
        break;
    case 'ol':
        $c = '<ol'.$class.$style.'>';
        if($atts['custom'] == null) {
			$atts['custom'] = '<li>%user% (%points%)</li>';
		}
		foreach($top as $x=>$i) {
			$text = apply_filters('cp_displayUserInfo',$atts['custom'],$i,$x+1);
			$c .= $text;
		}
		$c .= '</ol>';
        break;
    case 'table':
        $c = '<table'.$class.$style.'>';
        if($atts['custom'] == null) {
			$atts['custom'] = '<tr><td>%user%</td><td>%points%</td></tr>';
		}
		foreach($top as $x=>$i) {
			$text = apply_filters('cp_displayUserInfo',$atts['custom'],$i,$x+1);
			$c .= $text;
		}
		$c .= '</table>';
        break;
    default;
        $c = '<ul'.$class.$style.'>';
        if($atts['custom'] == null) {
			$atts['custom'] = '<li>%user% (%points%)</li>';
		}
		foreach($top as $x=>$i) {
			$text = apply_filters('cp_displayUserInfo',$atts['custom'],$i,$x+1);
			$c .= $text;
		}
		$c .= '</ul>';
        break;
}
	return $c;
}

/** Display points info in page */
add_shortcode('cubepoints','cp_shortcode_user');
function cp_shortcode_user( $atts ){
	if($atts['user']!=''){
		$u=get_userdatabylogin($atts['user']);
		$uid = $u->ID;
		if($uid==''){ return ''; }
		return cp_displayPoints($uid, 1, $atts['format']);
	}
	else{
		$uid = cp_currentUser();
		if($uid==''){ return $atts['not_logged_in']; }
		return cp_displayPoints($uid, 1, (bool)$atts['format']);
	}
	return $c;
}

/** Format displays of users */
add_filter('cp_displayUserInfo', 'cp_displayUserInfo', 10 , 3);
function cp_displayUserInfo($string,$y,$place) {
	$user = get_userdata($y['id']);
	$string = str_replace('%points%', $y['points_formatted'], $string);
	$string = str_replace('%npoints%', $y['points'], $string);
	$string = str_replace('%user%', $y['user'], $string);
	$string = str_replace('%username%', $y['display_name'], $string);
	$string = str_replace('%userid%', $y['id'], $string);
	$string = str_replace('%place%', $place, $string);
	$string = str_replace('%emailhash%', md5(strtolower($user->user_email)), $string);
	return $string;
}

/** Formatting tables */
add_filter('cp_displayTable', 'cp_displayTable');
function cp_displayTable($string) {
	$string = '<tr><td>'.$string;
	$string = str_replace('%d%', '</td><td>', $string);
	$string .= '</td></tr>';
	return $string;
}

/** Hook to process admin manage ajax post request to update points */
add_action( 'wp_ajax_cp_manage_form_submit', 'cp_manage_form_submit' );
function cp_manage_form_submit() {

	header( "Content-Type: application/json" );
	
	if( ! current_user_can('manage_options')){
		$response = json_encode( array( 'error' => __('You do not have sufficient permission to manage points!', 'cp') ) );
		echo $response;
		exit;
	}
	
	if($_POST['points']!='' && $_POST['user_id']!=''){
		$points = (int) $_POST['points'];
		$uid = (int) $_POST['user_id'];
		$user = get_userdata($uid);
		if($user->ID==NULL){
			$response = json_encode( array( 'error' => __('User does not exist!', 'cp') ) );
			echo $response;
			exit;
		}
		if($points<0){$points = 0;}
		cp_points_set('admin', $uid, $points, cp_currentUser());
	}
	else{
		$response = json_encode( array( 'error' => __('Invalid request!', 'cp') ) );
		echo $response;
		exit;
	}
	
	$response = json_encode( array( 'error' => 'ok' ,
									'points' => cp_displayPoints($uid, 1, 0) ,
									'points_formatted' => cp_displayPoints($uid, 1, 1) ,
									'username' => $user->user_login ,
									'user_id' => $user->ID 
								   ) );
	echo $response;
	exit;
	
}

/** Hook for add-points autocomplete user suggestion */
add_action( 'wp_ajax_cp_add_points_user_suggest', 'cp_add_points_user_suggest' );
function cp_add_points_user_suggest() {

	header( "Content-Type: application/json" );
	
	if( ! current_user_can('manage_options') || $_REQUEST['q']=='' ){
		$response = json_encode( array() );
		echo $response;
		exit;
	}
	
	global $wpdb;
	$users = $wpdb->get_results('SELECT * from `' . $wpdb->prefix . 'users` WHERE `user_login` LIKE \''.$_REQUEST['q'].'%\' LIMIT 10', ARRAY_A);
	
	$response = array();
	
	foreach($users as $user){
		$response[] = implode("|", array($user['user_login'], $user['ID'], $user['display_name'], $user['user_email'], md5(trim(strtolower($user['user_email'])))));
	}
	$response = json_encode( implode("\n", $response) );
	echo $response;
	exit;
	
}

/** Hook for add-points user query */
add_action( 'wp_ajax_cp_add_points_user_query', 'cp_add_points_user_query' );
function cp_add_points_user_query() {

	header( "Content-Type: application/json" );
	
	if( ! current_user_can('manage_options') || $_REQUEST['q']=='' ){
		$response = json_encode( array() );
		echo $response;
		exit;
	}
	
	global $wpdb;
	$user = $wpdb->get_row('SELECT * from `' . $wpdb->prefix . 'users` WHERE `user_login` LIKE \''.$wpdb->prepare(trim($_REQUEST['q'])).'\' LIMIT 1', ARRAY_A);
	if($user['ID'] == null){
		$response = json_encode( array() );
		echo $response;
		exit;
	}
	$response = json_encode( array(
							'id' => $user['ID'],
							'user_login' => $user['user_login'],
							'display_name' => $user['display_name'],
							'email' => $user['user_email'],
							'points' => cp_getPoints($user['ID']),
							'hash' => md5(trim(strtolower($user['user_email'])))
							));
	echo $response;
	exit;
	
}

/** Hook for add-points user update */
add_action( 'wp_ajax_cp_add_points_user_update', 'cp_add_points_user_update' );
function cp_add_points_user_update() {

	header( "Content-Type: application/json" );
	
	if( ! current_user_can('manage_options') || $_POST['id']=='' || $_POST['points']=='' || $_POST['description']=='' ){
		$response = json_encode( array( 'status' => 'failed' ) );
		echo $response;
		exit;
	}
	
	cp_points('addpoints', (int)$_POST['id'], (int)$_POST['points'], htmlentities($_POST['description']));
	$response = json_encode( array(
							'status' => 'ok',
							'newpoints' => cp_getPoints((int)$_POST['id'])
							));
	echo $response;
	exit;
	
}

?>