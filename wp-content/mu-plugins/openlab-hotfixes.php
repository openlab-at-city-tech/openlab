<?php

/**
 * Flush user cache after BP manually updates password.
 */
add_action( 'bp_core_activated_user', function( $user_id ) {
        wp_cache_delete( $user_id, 'users' );
} );
