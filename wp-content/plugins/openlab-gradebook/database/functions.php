<?php

function build_sorter($key) {
	return function ($a, $b) use ($key) {
		return strnatcmp($a[$key], $b[$key]);
	};
}	
	
function gradebook_check_user_role( $role, $user_id = null ) {
	if ( is_numeric( $user_id ) ){
		$user = get_userdata( $user_id );
	}
    else{
      	$user = wp_get_current_user();
 	}
    if ( empty( $user ) ){
		return false;
	}
 	return in_array( $role, (array) $user->roles );
}

?>