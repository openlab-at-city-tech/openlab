<?php
/**
 * CubePoints core functions
 */

/** Core function loaded */
function cp_ready(){
	return true;
}

/** Get current logged in user */
function cp_currentUser() {
	require_once(ABSPATH . WPINC . '/pluggable.php');
	global $current_user;
	get_currentuserinfo();
	return $current_user->ID;
}
 
 /** Get number of points */
function cp_getPoints($uid) {
	$points = get_user_meta($uid, 'cpoints', 1);
	if ($points == '') {
		return 0;
	} else {
		return $points;
	}
}

/** Update points */
function cp_updatePoints($uid, $points) {
	// no negative points 
	if ($points < 0) {
	  $points = 0;
	}
	update_user_meta($uid, 'cpoints', $points);
}

/** Alter points */
function cp_alterPoints($uid, $points) {
	cp_updatePoints($uid, cp_getPoints($uid) + $points);
}

/** Formats points with prefix and suffix */
function cp_formatPoints($points){
	if($points == 0) { $points = '0'; }
	return get_option('cp_prefix') . $points . get_option('cp_suffix');
}

/** Display points */
function cp_displayPoints($uid = 0, $return = 0, $format = 1) {
	if ($uid == 0) {
		if (!is_user_logged_in()) {
		  return false;
	  	}
	  	$uid = cp_currentUser();
	}
	
	if ($format == 1) {
		$fpoints = cp_formatPoints(cp_getPoints($uid));
	} else {
		$fpoints = cp_getPoints($uid);
	}
	
	if (!$return ) {
	  	echo $fpoints;
	} else {
	  	return $fpoints;
	}
}

/** Get points of all users into an array */
function cp_getAllPoints($amt=0,$filter_users=array(),$start=0){
	global $wpdb;
	if($amt>0){ $limit = ' LIMIT ' . $start.','.$amt; }
  $extraquery = '';
	if (count($filter_users)>0){
		$extraquery = ' WHERE '.$wpdb->base_prefix.'users.user_login != \'';
		$extraquery .= implode("' AND ".$wpdb->base_prefix."users.user_login != '",$filter_users);
		$extraquery .= '\' ';
	} 
	$array = $wpdb->get_results('SELECT '.$wpdb->base_prefix.'users.id, '.$wpdb->base_prefix.'users.user_login, '.$wpdb->base_prefix.'users.display_name, '.$wpdb->base_prefix.'usermeta.meta_value 
		FROM `'.$wpdb->base_prefix.'users` 
		LEFT JOIN `'.$wpdb->base_prefix.'usermeta` ON '.$wpdb->base_prefix.'users.id = '.$wpdb->base_prefix.'usermeta.user_id 
		AND '.$wpdb->base_prefix.'usermeta.meta_key=\''.'cpoints'.'\''.$extraquery.' 
		ORDER BY '.$wpdb->base_prefix.'usermeta.meta_value+0 DESC'
		. $limit . ';'
		,ARRAY_A);
		foreach($array as $x=>$y){
			$a[$x] = array( "id"=>$y['id'], "user"=>$y['user_login'], "display_name"=>$y['display_name'], "points"=>($y['meta_value']==0)?0:$y['meta_value'], "points_formatted"=>cp_formatPoints($y['meta_value']) );
		}
	return $a;
}

/** Adds transaction to logs database */
function cp_log($type, $uid, $points, $data){
	$userinfo = get_userdata($uid);
	if($userinfo->user_login==''){ return false; }
	if($points==0 && $type!='reset'){ return false; }
	global $wpdb;
	$wpdb->query("INSERT INTO `".CP_DB."` (`id`, `uid`, `type`, `data`, `points`, `timestamp`) 
				  VALUES (NULL, '".$uid."', '".$type."', '".$data."', '".$points."', ".time().");");
	do_action('cp_log',$type, $uid, $points, $data);
	return true;
}

/** Alter points and add to logs */
function cp_points($type, $uid, $points, $data){
	$points = apply_filters('cp_points',$points, $type, $uid, $data);
	cp_alterPoints($uid, $points);
	cp_log($type, $uid, $points, $data);
}

/** Set points and add to logs */
function cp_points_set($type, $uid, $points, $data){
	$points = apply_filters('cp_points_set',$points, $type, $uid, $data);
	$difference = $points - cp_getPoints($uid);
	cp_updatePoints($uid, $points);
	cp_log($type, $uid, $difference, $data);
}

/** Get total number of posts */
function cp_getPostCount($id){
	global $wpdb;
	return (int) $wpdb->get_var('SELECT count(id) FROM `'.$wpdb->base_prefix.'posts` where `post_type`=\'post\' and `post_status`=\'publish\' and `post_author`='.$id);
}

/** Get total number of comments */
function cp_getCommentCount($id){
	global $wpdb;
	return (int) $wpdb->get_var('SELECT count(comment_ID) FROM `'.$wpdb->base_prefix.'comments` where `user_id`='.$id);
}

/** Function to truncate a long string */
function cp_truncate($string, $length, $stopanywhere=false) {
	$string = str_replace('"','&quot;',strip_tags($string));
	
    //truncates a string to a certain char length, stopping on a word if not specified otherwise.
    if (strlen($string) > $length) {
        //limit hit!
        $string = substr($string,0,($length -3));
        if ($stopanywhere) {
            //stop anywhere
            $string .= '...';
        } else{
            //stop on a word.
            $string = substr($string,0,strrpos($string,' ')).'...';
        }
    }
    return $string;
}

/** Function to register modules */
function cp_module_register($module, $id, $version, $author, $author_url, $plugin_url, $description, $can_deactivate){
	if($module==''||$id==''||$version==''||$description==''){
		return false;
	}
	global $cp_module;
	$cp_module[] = array( "module"=>$module, "id"=>$id, "version"=>$version, "author"=>$author, "author_url"=>$author_url, "plugin_url"=>$plugin_url, "description"=>$description, "can_deactivate"=>$can_deactivate );
}

/** Function to check module activation status */
function cp_module_activated($id){
	if(get_option('cp_module_activation_'.$id)!=false){
		return true;
	}
	else{
		return false;
	}
}

/** Function to activate or deactivate module */
function cp_module_activation_set($id,$status){
	update_option('cp_module_activation_'.$id , $status);
}

/** Function to include all modules in the modules folder */
function cp_modules_include(){
	foreach (glob(ABSPATH.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__))."/modules/*.php") as $filename){
		require_once($filename);
	}
	foreach (glob(ABSPATH.PLUGINDIR.'/'.dirname(plugin_basename(__FILE__))."/modules/*/*.php") as $filename){
		require_once($filename);
	}
}

/** Function to cache module versions and run activation hook on module update */
function cp_modules_updateCheck(){
	global $cp_module;
	$module_ver_cache = unserialize(get_option('cp_moduleVersions'));
	$module_ver = array();
	foreach($cp_module as $mod){
		$module_ver[$mod['id']] = $mod['version'];
		// check for change in version and run module activation hook
		if(cp_module_activated($mod['id'])){
			if($module_ver_cache[$mod['id']] != $mod['version']){
				if(!did_action('cp_module_'.$mod['id'].'_activate')){
					do_action('cp_module_'.$mod['id'].'_activate');
				}
			}
		}
	}
	update_option('cp_moduleVersions', serialize($module_ver));
}


?>