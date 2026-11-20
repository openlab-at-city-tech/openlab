<?php
/**
 * Shows the galleries table's Privacy column once.
 *
 * @package Imagely\NGG\Migrations
 */

namespace Imagely\NGG\Migrations;

use Imagely\NGG\Settings\ManagerBase;

/**
 * The Privacy column used to be hidden and impossible to show, so a saved "off" is the old
 * default, not a choice. It is turned on once; later choices are kept.
 */
class ShowPrivacyColumn {

	/**
	 * Setting that records this has already run.
	 *
	 * @var string
	 */
	const SETTING = 'privacy_column_shown';

	/**
	 * Turns the column on in the saved column choice. Mutates the settings without saving them.
	 *
	 * @param ManagerBase $settings Settings to update.
	 * @return bool Whether this ran.
	 */
	public static function apply( ManagerBase $settings ) {
		if ( $settings->get( self::SETTING ) ) {
			return false;
		}

		$columns = $settings->get( 'admin_galleries_visible_columns', [] );

		if ( is_array( $columns ) && array_key_exists( 'is_private', $columns ) ) {
			$columns['is_private'] = true;
			$settings->set( 'admin_galleries_visible_columns', $columns );
		}

		$settings->set( self::SETTING, true );

		return true;
	}
}
