<?php

namespace PDFEmbedder;

use PDFEmbedder\Helpers\Multisite;
use PDFEmbedder\Tasks\UsageTracking\SendUsageTask;

/**
 * Class Options.
 *
 * @since 4.7.0
 */
class Options {

	/**
	 * This key used to save/retrieve options from wp_options table.
	 *
	 * @since 4.7.0
	 */
	const KEY = 'pdfemb';

	/**
	 * Internal cached holder for the options.
	 *
	 * @since 4.7.0
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Default plugin options.
	 *
	 * @since 4.7.0
	 */
	public function get_defaults(): array {

		/**
		 * Filter the default plugin options.
		 *
		 * @since 4.7.0
		 *
		 * @param array $defaults The default options.
		 */
		return apply_filters(
			'pdfemb_options_defaults',
			[
				'pdfemb_width'        => 'max',
				'pdfemb_height'       => 'max',
				'pdfemb_toolbar'      => 'bottom',
				'pdfemb_toolbarfixed' => 'off',
				'poweredby'           => 'off', // Removed.
				'usagetracking'       => 'off',
			]
		);
	}

	/**
	 * Get the plugin settings.
	 *
	 * @since 4.7.0
	 */
	public function get(): array {

		if ( ! empty( $this->options ) ) {
			return $this->options;
		}

		if ( Multisite::is_network_activated() ) {
			$options = get_site_option( self::KEY, [] );
		} else {
			$options = get_option( self::KEY, [] );
		}

		// Inject default options into those that are saved in DB.
		foreach ( $this->get_defaults() as $k => $v ) {
			if ( ! isset( $options[ $k ] ) ) {
				$options[ $k ] = $v;
			}
		}

		/**
		 * Filter the plugin options.
		 *
		 * @since 4.7.0
		 *
		 * @param array $options The options.
		 */
		$this->options = apply_filters( 'pdfemb_options_get', $options );

		return $this->options;
	}

	/**
	 * Go through each option and sanitize and validate its value before saving into DB.
	 *
	 * @since 4.7.0
	 *
	 * @param array $input Plugin options to validate.
	 */
	public static function validate( array $input ): array { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$validated = [];

		$validated['pdfemb_width'] = isset( $input['pdfemb_width'] ) ? strtolower( trim( $input['pdfemb_width'] ) ) : 'max';

		if (
			! is_numeric( $validated['pdfemb_width'] ) &&
			$validated['pdfemb_width'] !== 'max' &&
			$validated['pdfemb_width'] !== 'auto'
		) {
			if ( function_exists( 'add_settings_error' ) ) {
				add_settings_error(
					'pdfemb_width',
					'widtherror',
					self::get_error_text( 'pdfemb_width|widtherror' ),
					'error'
				);
			}

			// Revert back to max as last resort, don't leave field blank.
			$validated['pdfemb_width'] = 'max';
		}

		$validated['pdfemb_height'] = isset( $input['pdfemb_height'] ) ? strtolower( trim( $input['pdfemb_height'] ) ) : 'max';

		if (
			! is_numeric( $validated['pdfemb_height'] ) &&
			$validated['pdfemb_height'] !== 'max' &&
			$validated['pdfemb_height'] !== 'auto'
		) {
			if ( function_exists( 'add_settings_error' ) ) {
				add_settings_error(
					'pdfemb_height',
					'heighterror',
					self::get_error_text( 'pdfemb_height|heighterror' ),
					'error'
				);
			}

			// Revert back to max as last resort, don't leave field blank.
			$validated['pdfemb_height'] = 'max';
		}

		if (
			isset( $input['pdfemb_toolbar'] ) &&
			in_array( $input['pdfemb_toolbar'], [ 'top', 'bottom', 'both', 'none' ], true )
		) {
			$validated['pdfemb_toolbar'] = $input['pdfemb_toolbar'];
		} else {
			$validated['pdfemb_toolbar'] = 'bottom';
		}

		if (
			isset( $input['pdfemb_toolbarfixed'] ) &&
			in_array( $input['pdfemb_toolbarfixed'], [ 'on', 'off' ], true )
		) {
			$validated['pdfemb_toolbarfixed'] = $input['pdfemb_toolbarfixed'];
		}

		$validated['pdfemb_version'] = PDFEMB_VERSION;

		// Always off, legacy, removed.
		$validated['poweredby'] = 'off';

		if (
			isset( $input['usagetracking'] ) &&
			in_array( $input['usagetracking'], [ 'on', 'off' ], true )
		) {
			$validated['usagetracking'] = $input['usagetracking'];
		} else {
			pdf_embedder()->tasks()->cancel( SendUsageTask::ACTION );
			$validated['usagetracking'] = 'off';
		}

		/**
		 * Filter the validated plugin options.
		 *
		 * @since 4.7.0
		 *
		 * @param array $validated Validated plugin options.
		 * @param array $input     Plugin options to validate.
		 */
		return apply_filters( 'pdfemb_options_validated', $validated, $input );
	}

	/**
	 * Get the error string for a given field error.
	 *
	 * @since 4.7.0
	 *
	 * @param string $error The field error to get the string for.
	 */
	public static function get_error_text( string $error ): string {

		$local_error_strings = [
			'pdfemb_width|widtherror'   => __( 'Width must be "max" or an integer (number of pixels). This setting is reset to "max".', 'pdf-embedder' ),
			'pdfemb_height|heighterror' => __( 'Height must be "max" or an integer (number of pixels). This setting is reset to "max".', 'pdf-embedder' ),
		];

		if ( isset( $local_error_strings[ $error ] ) ) {
			return $local_error_strings[ $error ];
		}

		return __( 'Unspecified error. Please review all the settings and try again.', 'pdf-embedder' );
	}

	/**
	 * Save the plugin options.
	 *
	 * @since 4.7.0
	 *
	 * @param array $options The options to save, they will be validated.
	 */
	public function save( array $options ) {

		$options = self::validate( $options );

		if ( Multisite::is_network_activated() ) {
			update_site_option( self::KEY, $options );
		} else {
			update_option( self::KEY, $options, 'no' );
		}

		$this->options = $options;
	}

	/**
	 * Validate whether the option value is truthy.
	 *
	 * @since 4.7.0
	 *
	 * @param mixed $value The option value to validate.
	 */
	public static function is_on( $value ): bool {

		return is_scalar( $value ) && ( $value === true || $value === 'on' || $value === '1' || $value === 'true' );
	}

	/**
	 * Prepend "pdfemb_" string to each key in the $atts array.
	 *
	 * @since 4.8.0
	 *
     * @param array $options Options to prefix.
	 */
	public static function prefix( array $options ): array {

		return (array) array_combine(
			array_map(
				static function ( $key ) {

					return 'pdfemb_' . $key;
				},
				array_keys( $options )
			),
			array_values( $options )
		);
	}

	/**
	 * Remove "pdfemb_" prefix from each key in the $options array.
	 *
	 * @since 4.8.0
	 *
	 * @param array $options Options to unprefix.
	 */
	public static function unprefix( $options ): array {

		return (array) array_combine(
			array_map(
				static function ( $key ) {

					return str_replace( 'pdfemb_', '', $key );
				},
				array_keys( $options )
			),
			array_values( $options )
		);
	}
}
