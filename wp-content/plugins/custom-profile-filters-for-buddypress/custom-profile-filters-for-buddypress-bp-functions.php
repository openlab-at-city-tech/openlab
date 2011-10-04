<?php


function cpfb_add_brackets( $field_value ) {

	if ( strpos( $field_value, '[' ) | strpos( $field_value, ']' ) ) { // If there is a bracket in a field, this block overrides the auto-linking
		$field_value = strip_tags( $field_value );
		while ( strpos( $field_value, ']') ) {
			$open_delin_pos = strpos( $field_value, '[' );
			$close_delin_pos = strpos( $field_value, ']' );
			$field_value =  substr($field_value, 0, $open_delin_pos) . '<a href="' . site_url( BP_MEMBERS_SLUG ) . '/?s=' . substr($field_value, $open_delin_pos+1, $close_delin_pos - $open_delin_pos - 1) . '">' . substr($field_value, $open_delin_pos+1, $close_delin_pos - $open_delin_pos - 1) . '</a>' . substr($field_value, $close_delin_pos+1);
		}
	}
	
	return $field_value;
}
add_filter( 'bp_get_the_profile_field_value', 'cpfb_add_brackets', 999, 1 );



function cpfb_add_social_networking_links( $field_value ) {
	global $bp, $social_networking_fields;

	$bp_this_field_name = bp_get_the_profile_field_name();
	
	if ( isset ( $social_networking_fields[$bp_this_field_name] ) ) {
		$sp = strpos ( $field_value, $social_networking_fields[$bp_this_field_name] );
		if ( $sp === false ) {
			$url = str_replace( '***', strip_tags( $field_value ), $social_networking_fields[$bp_this_field_name] );
			$field_value = '<a href="http://' . $url . '">' . $field_value . '</a>';
			}
		return $field_value;
	}
	
	return $field_value;
}
add_filter( 'bp_get_the_profile_field_value', 'cpfb_add_social_networking_links', 1 );



function cpfb_unlink_fields( $field_value ) {
	global $no_link_fields;
	
	$bp_this_field_name = bp_get_the_profile_field_name();
	
	if ( in_array( $bp_this_field_name, $no_link_fields ) )
		$field_value = strip_tags( $field_value );
	
	return $field_value;
		
}
add_filter( 'bp_get_the_profile_field_value', 'cpfb_unlink_fields', 998, 1 );
?>