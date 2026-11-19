<?php

namespace Imagely\NGG\Admin\Notifications;

use Imagely\NGG\Util\Security;

/**
 * Watermark-at-upload failure notification.
 *
 * Automatic watermarking runs inside the upload request, which still succeeds when the
 * watermark image or font cannot be loaded (refused by #1084, unreachable URL, not a PNG),
 * so the image is published without one. The upload records the miss here and every
 * NextGEN settings admin sees this notice until they dismiss it, or for a week (#1104).
 * Nothing clears it automatically: no settings save or later upload can tell that the
 * images already published got their watermark, and a new miss shows it again.
 */
class WatermarkUploadFailed {

	/**
	 * Transient holding the time of the last miss. One key, expires on its own.
	 */
	const TRANSIENT = 'ngg_watermark_upload_failed';

	/**
	 * Records that an upload could not be watermarked, and shows the notice again to
	 * anyone who dismissed an earlier one: this is a new miss.
	 */
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

	/**
	 * Clears the recorded miss.
	 */
	public static function clear() {
		delete_transient( self::TRANSIENT );
	}

	/**
	 * Checks if the notification should be rendered.
	 *
	 * @return bool Whether a miss is recorded and the user can fix the watermark settings.
	 */
	public function is_renderable() {
		return false !== get_transient( self::TRANSIENT ) && Security::is_allowed( 'NextGEN Change options' );
	}

	/**
	 * Checks if the notification is dismissable.
	 *
	 * @return bool True: the admin acknowledges it; the next miss shows it again.
	 */
	public function is_dismissable() {
		return true;
	}

	/**
	 * Gets the CSS class for the notice.
	 *
	 * @return string CSS class.
	 */
	public function get_css_class() {
		return 'error';
	}

	/**
	 * Renders the notification content.
	 *
	 * @return string The notification message.
	 */
	public function render() {
		return esc_html__( 'NextGEN Gallery: recently uploaded images were not watermarked because the watermark image or font in Other Options → Watermarks could not be loaded. Fix the watermark settings, then use Set Watermark on those images.', 'nggallery' );
	}
}
