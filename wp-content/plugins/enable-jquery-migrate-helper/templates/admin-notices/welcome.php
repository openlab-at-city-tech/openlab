<?php
/**
 * Admin notice template for the plugins welcome message.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

?>
<div class="notice <?php echo ( 'no' !== get_option( '_jquery_migrate_downgrade_version', 'no' ) ? 'notice-error' : 'notice-warning is-dismissible' ); ?> jquery-migrate-dashboard-notice" data-notice-id="jquery-migrate-notice">
	<h2><?php _ex( 'jQuery Migrate Helper', 'Admin notice header', 'enable-jquery-migrate-helper' ); ?></h2>
	<p>
		<?php _e( 'Right now, you are using a plugin to support some old code in this website’s jQuery JavaScript library. That code is deprecated, meaning it is basically obsolete and is losing the support of your themes and plugins and WordPress itself.', 'enable-jquery-migrate-helper' ); ?>
		<br>
		<strong>
			<?php _e( 'This plugin, called Enable jQuery Migrate Helper, is not a fix. It is a temporary solution to give your plugin and theme authors time to update.', 'enable-jquery-migrate-helper' ); ?>
		</strong>
	</p>

	<p>
		<?php _e( 'If you get warnings, please check for updated versions of the plugin or theme that generated the warning. There will very likely be one you can install. When you have updated your plugins and themes, and there are no more warnings, please deactivate Enable jQuery Migrate Helper.', 'enable-jquery-migrate-helper' ); ?>
	</p>

	<?php if ( 'no' !== get_option( '_jquery_migrate_downgrade_version', 'no' ) ) : ?>
		<p>
			<strong>
                <?php _e( 'You are currently using a legacy version of jQuery. It has no support and doesn’t receive security updates. Please upgrade as soon as possible.', 'enable-jquery-migrate-helper' ); ?>
			</strong>
		</p>
	<?php endif; ?>

	<p>
		<a href="<?php echo esc_url( admin_url( 'tools.php?page=jqmh' ) ); ?>"><?php _e( 'Plugin settings', 'enable-jquery-migrate-helper' ); ?></a> | <a href="<?php echo esc_url( admin_url( 'tools.php?page=jqmh&tab=logs' ) ); ?>"><?php _e( 'Logged deprecations', 'enable-jquery-migrate-helper' ); ?></a>
	</p>

	<?php wp_nonce_field( 'jquery-migrate-notice', 'jquery-migrate-notice-nonce', false ); ?>
</div>
