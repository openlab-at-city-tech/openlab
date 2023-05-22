<?php
/**
 * Wrapper class for sanitizing input.
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
use function is_bool;
use function is_numeric;
use function strip_tags;

defined( 'WPINC' ) || die;

/**
 * Class Sanitize
 *
 * @package WPMUDEV_BLC\Core\Traits
 */
trait Sanitize {
	public function sanitize_bool( $input = null ) {
		return filter_var( $input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ? $input : rest_sanitize_boolean( $input );
	}

	/**
	 * Sanitize an array.
	 *
	 * @param array $options The options to sanitize.
	 *
	 * @since 1.0.0
	 * @return array Returns the sanitized array.
	 */
	protected function sanitize_array( array $options = array() ) {
		if ( ! is_array( $options ) ) {
			return $this->sanitize_single( $options );
		}

		$sanitized_options = array();

		foreach ( $options as $key => $value ) {
			$sanitized_options[ sanitize_key( $key ) ] = is_array( $value ) ? $this->sanitize_array( $value ) : $this->sanitize_single( $value );
		}

		return $sanitized_options;
	}

	/**
	 * Sanitize an array.
	 *
	 * @param string|int|bool|float $input The option to sanitize.
	 *
	 * @since 2.0.0
	 * @return string|int|bool|float Returns the sanitized value.
	 */
	protected function sanitize_single( $input = '' ) {
		if ( ! \is_null( $input ) && ! \is_array( $input ) && ! \is_object( $input ) ) {
			if ( $this->has_email_format( $input ) ) {
				$input = filter_var( $input, FILTER_SANITIZE_EMAIL );
			} elseif ( preg_match( '/\R/', $input ) ) {
				$input = sanitize_textarea_field( $input );
			} elseif ( wp_strip_all_tags( $input ) !== $input ) {
				$input = wp_kses_post( $input );
			} elseif ( ! is_numeric( $input ) && ! is_bool( $input ) ) {
				$input = sanitize_text_field( $input );
			}
		}

		return $input;
	}

	/**
	 * Checks the format of input if it looks like an email. It doesn't validate against forbidden characters.
	 *
	 * @param string $input The email address.
	 *
	 * @return bool
	 */
	protected function has_email_format( $input ) {
		return ( preg_match( '/(@.*@)|(\.\.)|(@\.)|(\.@)|(^\.)/', $input ) || ! preg_match( '/^.+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/', $input ) ) ? false : true;
	}

	/**
	 * Sanitize by scheme or without a given scheme.
	 *
	 * @param $input
	 * @param $scheme
	 *
	 * @return mixed|string
	 */
	protected function sanitize( $input = '', $scheme = '' ) {
		if ( empty( $input ) ) {
			return $input;
		}

		/**
		 * Filters the accepted schemes that are supported by default.
		 * Key is the scheme and value is the callback.
		 */
		$schemes = apply_filters(
			'wpmudev_blc_sanitize_default_accepted_schemes',
			array(
				'string'   => 'sanitize_text_field',
				'url'      => 'sanitize_url',
				'email'    => 'sanitize_email',
				'textarea' => 'sanitize_textarea_field',
				'html'     => 'wp_kses_post',
				//'bool'     => array( $this, 'sanitize_bool' ),
				'bool'     => 'boolval',
				//'bool'     => 'rest_sanitize_boolean',
			),
			$input,
			$scheme
		);

		if ( empty( $scheme ) || ! in_array( $scheme, array_keys( $schemes ) ) ) {
			return $this->sanitize_single( $input );
		}

		$callback = $schemes[ $scheme ];

		return $callback( $input );
	}
}
