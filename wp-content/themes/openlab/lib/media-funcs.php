<?php
/*
 * Media-oriented functionality
 */

//custom default avatar
function openlab_add_default_member_avatar( $url = false ) {
	return get_stylesheet_directory_uri().'/images/default-avatar.jpg';
}
add_filter( 'bp_core_mysteryman_src', 'openlab_add_default_member_avatar' );
