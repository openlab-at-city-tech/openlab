<?php
/**
 * Admin notice template showing live deprecation notices.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

?>
<div class="notice notice-error is-dismissible jquery-migrate-dashboard-notice jquery-migrate-deprecation-notice hidden" data-notice-id="jquery-migrate-deprecation-list">
	<h2><?php _ex( 'jQuery Migrate Helper', 'Admin notice header', 'enable-jquery-migrate-helper' ); ?> &mdash; <?php _ex( 'Warnings encountered', 'enable-jquery-migrate-helper' ); ?></h2>
	<p><?php _e( 'This page generated the following warnings:', 'enable-jquery-migrate-helper' ); ?></p>

	<ol class="jquery-migrate-deprecation-list"></ol>

	<p>
		<?php _e( 'Please make sure you are using the latest version of all of your plugins, and your theme. If that is the case, then you may want to ask the developers of the code mentioned in your warnings to update it.', 'enable-jquery-migrate-helper' ); ?>
	</p>

	<?php wp_nonce_field( 'jquery-migrate-deprecation-list', 'jquery-migrate-deprecation-list-nonce', false ); ?>
</div>
