<?php
/**
 * Helper class for font settings.
 *
 * @package     Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Font info class for System and Google fonts.
 */
if ( ! class_exists( 'Astra_Fonts_Data' ) ) {

	/**
	 * Fonts Data
	 */
	final class Astra_Fonts_Data {
		/**
		 * Localize Fonts
		 *
		 * @param bool $skip_google_fonts Whether to skip Google Fonts loading for initial load optimization.
		 */
		public static function js( $skip_google_fonts = true ) {

			$system = wp_json_encode( Astra_Font_Families::get_system_fonts() );
			$custom = wp_json_encode( Astra_Font_Families::get_custom_fonts() );

			/** @psalm-suppress UndefinedVariable */
			if ( $skip_google_fonts ) {
				$custom = $custom ? $custom : '{}';
				/** @psalm-suppress RedundantConditionGivenDocblockType */
				if ( ! empty( $custom ) && '{}' !== $custom ) {
					return 'var AstFontFamilies = { system: ' . ( $system ?: '{}' ) . ', custom: ' . $custom . ', google: {}, googleLoaded: false };';
				}
				return 'var AstFontFamilies = { system: ' . ( $system ?: '{}' ) . ', google: {}, googleLoaded: false };';
			}

			$google = wp_json_encode( Astra_Font_Families::get_google_fonts() );
			$custom = $custom ? $custom : '{}';
			$google = $google ? $google : '{}';
			$system = $system ? $system : '{}';

			/** @psalm-suppress RedundantConditionGivenDocblockType */
			if ( ! empty( $custom ) && '{}' !== $custom ) {
				return 'var AstFontFamilies = { system: ' . $system . ', custom: ' . $custom . ', google: ' . $google . ', googleLoaded: true };';
			}

			return 'var AstFontFamilies = { system: ' . $system . ', google: ' . $google . ', googleLoaded: true };';
		}
	}

}
