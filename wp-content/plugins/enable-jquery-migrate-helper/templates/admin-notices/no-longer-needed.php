<?php
/**
 * Admin notice template encouraging removal of this plugin when it may no longer be needed.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

?>
<div class="notice notice-warning is-dismissible jquery-migrate-dashboard-notice" data-notice-id="jquery-migrate-no-deprecations-notice">
	<h2><?php _ex( 'jQuery Migrate Helper', 'Admin notice header', 'enable-jquery-migrate-helper' ); ?></h2>

	<p>
		<?php _e( 'No deprecations have been logged on this site in a while, you may no longer need this plugin.', 'enable-jquery-migrate-helper' ); ?>
	</p>

	<p>
		<?php _e( 'Please note that not all jQuery deprecation notices are recorded in the log. Deprecation notices that occur on public pages (front end) are recorded. Notices from the admin pages (back end) are only recorded if the immediate deprecation notices in the admin bar are turned off. Also keep in mind that under WordPress 5.6 logging of deprecation notices are only logged if logging is specifically enabled by the user.', 'enable-jquery-migrate-helper' ); ?>
	</p>

	<p>
		<?php _e( 'This means you should still check that things work as expected after you have disabled the plugin, and if you know there have been warnings in the admin pages, you may still need to reach out to the authors of the affected plugin or theme.', 'enable-jquery-migrate-helper' ); ?>
	</p>

	<?php if ( ! is_wp_version_compatible( '5.5.1' ) ) : ?>

		<p>
			<strong>
				<?php _e( 'You are using a WordPress version prior to 5.5.1. This plugin also helps with a bug found in WordPress 5.5.0. Please update WordPress to version 5.5.1, or later, before deactivating this plugin.', 'enable-jquery-migrate-helper' ); ?>
			</strong>
		</p>

	<?php endif; ?>

	<?php wp_nonce_field( 'jquery-migrate-no-deprecations-notice', 'jquery-migrate-no-deprecations-notice-nonce', false ); ?>
</div>
