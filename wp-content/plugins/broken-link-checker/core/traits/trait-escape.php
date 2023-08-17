<?php
/**
 * Wrapper class for escaping output.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\Core\Traits
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\Core\Traits;

// Abort if called directly.
defined( 'WPINC' ) || die;

/**
 * Class Sanitize
 *
 * @package WPMUDEV_BLC\Core\Traits
 */
trait Escape {
	/**
	 * Escape an array with fixed format.
	 *
	 * @since 2.0.0
	 *
	 * @param array $options The options to escape.
	 * @param string $schema String
	 *
	 * @return array Returns the array with escaped values.
	 */
	protected function escape_array_fixed( array $options = array(), string $schema = 'html' ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		foreach ( $options as $key => $value ) {
			if ( is_array( $value ) ) {
				$options[ $key ] = $this->escape_array_fixed( $value, $schema );
			} else {
				$options[ $key ] = $this->escape_single( $value, $schema );
			}

		}

		return $options;
	}

	/**
	 * Escape an array.
	 *
	 * @since 2.0.0
	 *
	 * @param array $options The options to escape.
	 * @param array $schema An array containing key and format pairs. Array( 'key_1' => 'html', 'key_2' => 'url )
	 *
	 * @return array Returns the array with escaped values.
	 */
	protected function escape_array( array $options = array(), array $schema = array() ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		foreach ( $options as $key => $value ) {
			if ( isset( $schema[ $key ] ) && is_string( $value ) ) {
				$options[ $key ] = self::escape_single( $value, $schema[ $key ] );
			}
		}

		return $options;
	}

	/**
	 * Sanitize an array.
	 *
	 * @since 2.0.0
	 *
	 * @param string $option The option to sanitize.
	 * @param string $schema The schema to follow escaping method. Accepted values: html, attr, url, url_raw, textarea
	 *
	 * @return string Returns the sanitized string.
	 */
	protected function escape_single( $option = '', string $schema = 'html' ) {
		if ( ! $option ) {
			return $option;
		}

		switch( $schema ) {
			case 'html' :
				$option = esc_html( $option );
				break;
			case 'attr' :
				$option = esc_attr( $option );
				break;
			case 'url' :
				$option = esc_url( $option );
				break;
			case 'url_raw' :
				$option = esc_url_raw( $option );
				break;
			case 'textarea' :
				$option = esc_textarea( $option );
				break;
		}

		return $option;
	}
}