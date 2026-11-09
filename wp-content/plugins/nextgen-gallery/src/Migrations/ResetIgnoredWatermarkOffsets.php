<?php
/**
 * Clears the watermark offsets that older versions silently ignored.
 *
 * @package Imagely\NGG\Migrations
 */

namespace Imagely\NGG\Migrations;

use Imagely\NGG\Settings\ManagerBase;

/**
 * Clears the watermark offsets that older versions silently ignored.
 *
 * The watermark renderer applied the X offset only to the left and right positions and the
 * Y offset only to the top and bottom ones, so every centred axis discarded its offset. Now
 * that both axes honour their offset, a site that never changed the shipped values would see
 * its watermark move. Clearing those axes keeps the rendered output where it already was.
 */
class ResetIgnoredWatermarkOffsets {

	/**
	 * Setting that records this has already run.
	 *
	 * It lives in the settings blob rather than its own option so that it is written by the
	 * same save as the offsets it describes, leaving no state to fall out of step.
	 *
	 * @var string
	 */
	const SETTING = 'watermark_offsets_reset';

	/**
	 * X offset shipped while the centred axes discarded it.
	 *
	 * @var int
	 */
	const LEGACY_X_DEFAULT = 15;

	/**
	 * Y counterpart of self::LEGACY_X_DEFAULT.
	 *
	 * @var int
	 */
	const LEGACY_Y_DEFAULT = 5;

	/**
	 * Clears the offsets on the axes that used to be ignored.
	 *
	 * Mutates the settings without saving them. The caller saves, so a failed write leaves
	 * neither the cleared offsets nor the marker behind and the upgrade is simply retried.
	 *
	 * @param ManagerBase $settings Settings to update.
	 * @return bool Whether anything was changed.
	 */
	public static function apply( ManagerBase $settings ) {
		if ( $settings->get( self::SETTING ) ) {
			return false;
		}

		$position = (string) $settings->get( 'wmPos', '' );

		// Only touch an axis that was ignored, and only while it still holds the value that
		// was shipped, so an offset the user chose starts working rather than being reset.
		if ( 0 === strpos( $position, 'mid' ) && self::LEGACY_Y_DEFAULT === (int) $settings->get( 'wmYpos' ) ) {
			$settings->set( 'wmYpos', 0 );
		}

		if ( 'Center' === substr( $position, 3 ) && self::LEGACY_X_DEFAULT === (int) $settings->get( 'wmXpos' ) ) {
			$settings->set( 'wmXpos', 0 );
		}

		$settings->set( self::SETTING, 1 );

		return true;
	}
}
