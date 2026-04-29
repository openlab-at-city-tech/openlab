<?php
/**
 * Abilities Helper
 *
 * Common utility functions for Astra abilities.
 *
 * @package Astra
 * @subpackage Abilities
 * @since 4.12.6
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Astra_Abilities_Helper
 */
class Astra_Abilities_Helper {
	/**
	 * Sanitize responsive typography array.
	 *
	 * @param array $typo_value Responsive typography array.
	 * @return array Sanitized typography array.
	 */
	public static function sanitize_responsive_typo( $typo_value ) {
		$sanitized_value = array();

		$allowed_keys = array( 'desktop', 'tablet', 'mobile', 'desktop-unit', 'tablet-unit', 'mobile-unit' );

		foreach ( $allowed_keys as $key ) {
			if ( isset( $typo_value[ $key ] ) ) {
				$sanitized_value[ $key ] = sanitize_text_field( $typo_value[ $key ] );
			}
		}

		return $sanitized_value;
	}

	/**
	 * Update font extras (line_height, text_transform, letter_spacing).
	 *
	 * @param array  $args       Input arguments containing font extras.
	 * @param string $option_key The option key to update.
	 * @return void
	 */
	public static function update_font_extras( $args, $option_key ) {
		if ( ! isset( $args['line_height'] ) && ! isset( $args['text_transform'] ) && ! isset( $args['letter_spacing'] ) ) {
			return;
		}

		$font_extras = astra_get_option( $option_key, array() );

		if ( isset( $args['line_height'] ) ) {
			$font_extras['line-height'] = sanitize_text_field( $args['line_height'] );
		}

		if ( isset( $args['text_transform'] ) ) {
			$font_extras['text-transform'] = sanitize_text_field( $args['text_transform'] );
		}

		if ( isset( $args['letter_spacing'] ) ) {
			$font_extras['letter-spacing'] = sanitize_text_field( $args['letter_spacing'] );
		}

		astra_update_option( $option_key, $font_extras );
	}
}
