<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\Settings\Settings;

/**
 * WP-CLI Notifications management commands for NextGEN Gallery.
 *
 * Provides notification-related WP-CLI commands for NextGEN Gallery.
 */
class Notifications {

	/**
	 * Clear all dismissed notifications handled by C_Admin_Notification_Manager
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Associative arguments.
	 * @synopsis
	 */
	public function clear_dismissed( $args, $assoc_args ) {
		$settings = Settings::get_instance();
		$settings->set( 'dismissed_notifications', [] );
		$settings->set( 'gallery_created_after_reviews_introduced', false );
		$settings->save();
	}
}
