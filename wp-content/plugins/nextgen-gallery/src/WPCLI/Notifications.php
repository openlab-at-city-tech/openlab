<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\Settings\Settings;

class Notifications {

	/**
	 * Clear all dismissed notifications handled by C_Admin_Notification_Manager
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @synopsis
	 */
	public function clear_dismissed( $args, $assoc_args ) {
		$settings = Settings::get_instance();
		$settings->set( 'dismissed_notifications', [] );
		$settings->set( 'gallery_created_after_reviews_introduced', false );
		$settings->save();
	}
}
