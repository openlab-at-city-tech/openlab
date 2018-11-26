<?php
/**
 * Admin code to save our options from the "Settings > Writing" page.
 *
 * @package cac-creative-commons
 */

if ( isset( $_POST['cac-cc-nonce'] ) ) {
	if ( ! wp_verify_nonce( $_POST['cac-cc-nonce'], 'cac-cc-license' ) ) {
		return;
	}

	// Update license.
	update_option( 'cac_cc_default', strip_tags( $_POST['cac-cc-license'] ) );

	// Update size.
	update_option( 'cac_cc_logo_size', strip_tags( $_POST['cac-cc-default-size'] ) );
}