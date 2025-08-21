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
	 * This key used to save/retrieve options from the wp_options table.
	 *
	 * @since 4.7.0
	 */
	public const KEY = 'pdfemb';

	/**
	 * Default options for the Lite version.
	 *
	 * @since 4.9.0
	 */
	public const LITE_DEFAULTS = [
		'pdfemb_width'        => 'max',
		'pdfemb_height'       => 'max',
		'pdfemb_toolbar'      => 'bottom',
		'pdfemb_toolbarfixed' => 'off',
		'usagetracking'       => 'off',
	];

	/**
	 * In which context the saving process is performed.
	 * Right now this is a section of the page.
	 *
	 * @since 4.9.0
	 *
	 * @var string
	 */
	public $saving_context = '';

	/**
	 * Internal cached holder for the options.
	 *
	 * @since 4.7.0
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Default plugin options, hydrated by Lite and Premium plans.
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
		return apply_filters( 'pdfemb_options_defaults', self::LITE_DEFAULTS );
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

		// Get raw options from DB.
		$options = $this->get_from_db();

		// Inject default options into those that are saved in DB.
		foreach ( $this->get_defaults() as $k => $v ) {
			if ( ! isset( $options[ $k ] ) ) {
				$options[ $k ] = $v;
			}
		}

		/**
		 * Filter the plugin options to allow programmatic modification.
		 *
		 * @since 4.7.0
		 *
		 * @param array $options The options.
		 */
		$this->options = apply_filters( 'pdfemb_options_get', $options );

		return $this->options;
	}

	/**
	 * Save the plugin options.
	 *
	 * @since 4.7.0
	 * @since 4.9.0 Added a $context parameter.
	 *
	 * @param array  $input   The options to save, they will be validated.
	 * @param string $context In which context the saving process is performed.
	 */
	public function save( array $input, string $context = '' ) {

		if ( empty( $context ) ) {
			return;
		}

		$this->saving_context = $context;

		$this->options = self::validate( $input );

		if ( Multisite::is_network_activated() ) {
			update_site_option( self::KEY, $this->options );
		} else {
			update_option( self::KEY, $this->options, false );
		}
	}

	/**
	 * Check if the option exists in the database.
	 *
	 * @since 4.9.2
	 *
	 * @param string $key The key to check.
	 */
	public function exist( string $key = '' ): bool {

		$options = $this->get_from_db();

		if ( empty( $key ) ) {
			return ! empty( $options );
		}

		return array_key_exists( $key, $options );
	}

	/**
	 * Go through each option and sanitize and validate its value before saving into DB.
	 *
	 * @since 4.7.0
	 *
	 * @param array $input Plugin options to validate.
	 */
	public static function validate( array $input ): array { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		/**
		 * Filter the validated plugin options.
		 * The value will be hydrated by each plan individually.
		 *
		 * @since 4.7.0
		 *
		 * @param array $validated Already validated portion of plugin options, to be saved into DB.
		 * @param array $input     Plugin options to validate.
		 */
		return apply_filters( 'pdfemb_options_validated', [], $input );
	}

	/**
	 * Clean the provided array of options from the specific set of defaults.
	 *
	 * @since 4.9.0
	 *
	 * @param array $input    The options to clean.
	 * @param array $defaults The default options to compare against.
	 */
	public function clean_options_from_defaults( array $input = [], array $defaults = [] ): array {

		if ( empty( $input ) ) {
			$input = $this->get_from_db();
		}

		if ( empty( $defaults ) ) {
			$defaults = self::LITE_DEFAULTS;
		}

		return array_diff_key( $input, $defaults );
	}

	/**
	 * Validate the Premium options.
	 * The validation is done in steps according to each plan priority.
	 *
	 * @since 4.9.0
	 *
	 * @param array $validated Validated options.
	 * @param array $input     Original options coming from the request.
     */
	public function validate_options( array $validated, array $input ): array {

		/**
		 * Just return the data from DB when we are validating settings elsewhere.
		 * Lite settings shouldn't be re-validated in this case, as they are absent from $input.
		 */
		if ( $this->saving_context !== 'settings' ) {
			return $this->get_from_db();
		}

		$validated = $this->clean_options_from_defaults();

		$validated['pdfemb_width'] = isset( $input['pdfemb_width'] ) ? strtolower( trim( $input['pdfemb_width'] ) ) : self::LITE_DEFAULTS['pdfemb_width'];

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
			$validated['pdfemb_width'] = self::LITE_DEFAULTS['pdfemb_width'];
		}

		$validated['pdfemb_height'] = isset( $input['pdfemb_height'] ) ? strtolower( trim( $input['pdfemb_height'] ) ) : self::LITE_DEFAULTS['pdfemb_height'];

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
			$validated['pdfemb_height'] = self::LITE_DEFAULTS['pdfemb_height'];
		}

		if (
			isset( $input['pdfemb_toolbar'] ) &&
			in_array( $input['pdfemb_toolbar'], [ 'top', 'bottom', 'both', 'none' ], true )
		) {
			$validated['pdfemb_toolbar'] = $input['pdfemb_toolbar'];
		} else {
			$validated['pdfemb_toolbar'] = self::LITE_DEFAULTS['pdfemb_toolbar'];
		}

		if (
			isset( $input['pdfemb_toolbarfixed'] ) &&
			in_array( $input['pdfemb_toolbarfixed'], [ 'on', 'off' ], true )
		) {
			$validated['pdfemb_toolbarfixed'] = $input['pdfemb_toolbarfixed'];
		} else {
			$validated['pdfemb_toolbarfixed'] = self::LITE_DEFAULTS['pdfemb_toolbarfixed'];
		}

		if (
			isset( $input['usagetracking'] ) &&
			in_array( $input['usagetracking'], [ 'on', 'off' ], true )
		) {
			$validated['usagetracking'] = $input['usagetracking'];
		} else {
			pdf_embedder()->tasks()->cancel( SendUsageTask::ACTION );
			$validated['usagetracking'] = self::LITE_DEFAULTS['usagetracking'];
		}

		return $validated;
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

	/**
	 * Get the options from the database, without default values.
	 *
	 * @since 4.9.0
	 */
	private function get_from_db(): array {

		if ( Multisite::is_network_activated() ) {
			$options = get_site_option( self::KEY, [] );
		} else {
			$options = get_option( self::KEY, [] );
		}

		return $options;
	}
}
