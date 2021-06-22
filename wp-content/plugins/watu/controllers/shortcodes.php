<?php
class WatuShortcodes {
   // displays data from user profile of the currently logged user
   static function userinfo($atts) {
   	global $user_ID;
   	
   	// let's allow user ID to be passed or taken from certificate
   	if(!empty($atts['user_id'])) {
   		if(is_numeric($atts['user_id'])) $user_id = $atts['user_id'];
   	}		
   	
   	if(empty($user_id) and !is_user_logged_in()) return @$atts[1];
   	if(empty($user_id)) $user_id = $user_ID;	
   		
   	$field = $atts[0];
   		
   	$user = get_userdata($user_id);
   	
   	if(isset($user->data->$field) and !empty($user->data->$field)) return $user->data->$field;
   	if(isset($user->data->$field) and empty($user->data->$field)) return @$atts[1];
   	
   	// not set? must be in meta then
   	$metas = get_user_meta($user_id);
   	if(count($metas) and is_array($metas)) {
   		foreach($metas as $key => $meta) {
   			if($key == $field and !empty($meta[0])) return $meta[0];
   			if($key == $field and empty($meta[0])) return @$atts[1];
   		}
   	}
   	
   	// nothing found, return the default if any
   	return @$atts[1];
   }
}