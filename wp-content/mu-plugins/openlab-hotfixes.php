<?php

/**
 * Flush user cache after BP manually updates password.
 */
add_action( 'bp_core_activated_user', function( $user_id ) {
        wp_cache_delete( $user_id, 'users' );
} );

function cac_set_return_path_header( $phpmailer ) {
       $phpmailer->Sender = 'wordpress@openlab.citytech.cuny.edu';
       return $phpmailer;
}
add_action( 'phpmailer_init', 'cac_set_return_path_header' );

