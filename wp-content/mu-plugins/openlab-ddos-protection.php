<?php

/**
 * Some rudimentary protection against simple bots
 */

/**
 * Add a security cookie, for .htaccess protection POST requests
 *
 * This cookie is detected when sending POST requests to wp-login.php or 
 * wp-comments-post.php. If not found, user is redirected to the 
 * unauthorized page. 
 */
function openlab_set_visitor_cookie() {
        if ( ! isset( $_COOKIE['openlab-visitor'] ) ) {
		global $current_blog;
                $cookie_domain = $current_blog->domain;
                $cookie_key = 'openlab-visitor';

                setcookie( $cookie_key, '1', time() + 60*60*24*365*100, '/', $cookie_domain );
        }
}
add_action( 'init', 'openlab_set_visitor_cookie', 0 );
