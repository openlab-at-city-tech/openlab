<?php

namespace Imagely\NGG\Admin\Notifications;

use Imagely\NGG\Util\Security;

/**
 * Reports cached copies that could not be deleted after an image's full-size file was rewritten
 * (the upload watermark, Set watermark).
 *
 * Those copies were cloned from the previous file, so they can show an image without its
 * watermark until they are removed (#1236). The usual cause is a gallery cache folder the web
 * server cannot write to. Shown until an admin dismisses it; the next failure shows it again.
 */
class CachedCopiesNotDeleted {

	const TRANSIENT = 'ngg_cached_copies_not_deleted';

	public static function record() {
		set_transient( self::TRANSIENT, time(), WEEK_IN_SECONDS );

		$settings  = \Imagely\NGG\Settings\Settings::get_instance();
		$dismissed = $settings->get( 'dismissed_notifications', [] );
		if ( is_array( $dismissed ) && isset( $dismissed[ self::TRANSIENT ] ) ) {
			unset( $dismissed[ self::TRANSIENT ] );
			$settings->set( 'dismissed_notifications', $dismissed );
			$settings->save();
		}
	}

	public static function clear() {
		delete_transient( self::TRANSIENT );
	}

	public function is_renderable() {
		return false !== get_transient( self::TRANSIENT ) && Security::is_allowed( 'NextGEN Change options' );
	}

	public function is_dismissable() {
		return true;
	}

	public function get_css_class() {
		return 'error';
	}

	public function render() {
		return esc_html__( 'NextGEN Gallery: older cached copies of recently watermarked images could not be deleted, so some visitors may still see those images without the watermark. Check that the web server can write to the galleries\' cache folders, then use Settings → Reset Options → Clear Image Cache.', 'nggallery' );
	}
}
