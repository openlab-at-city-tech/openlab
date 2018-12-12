<?php
/**
 * Frontend integration to the /blogs/create/ BuddyPress page.
 *
 * @package cac-creative-commons
 */

require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/frontend.php';

// Enqueue chooser assets.
add_action( 'wp_enqueue_scripts', function() {
	require_once CAC_CC_DIR . '/includes/admin-functions.php';
	cac_cc_register_scripts();
} );

add_action( 'signup_blogform', function() {
	cac_cc_get_template_part( 'blog-create' );
}, 9 );

/**
 * Save license routine for a WP site.
 *
 * @since 0.1.0
 */
function cac_cc_blog_save( $blog_id ) {
	if ( ! isset( $_POST['cac-cc-nonce'] ) ) {
		return;
	}

	if ( ! wp_verify_nonce( $_POST['cac-cc-nonce'], 'cac-cc-license' ) ) {
		return;
	}

	// Update license.
	update_blog_option( $blog_id, 'cac_cc_default', strip_tags( $_POST['cac-cc-license'] ) );
}
add_action( 'wpmu_new_blog', 'cac_cc_blog_save' );