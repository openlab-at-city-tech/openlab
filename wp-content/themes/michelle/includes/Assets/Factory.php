<?php
/**
 * Assets factory class.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.5
 */

namespace WebManDesign\Michelle\Assets;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Factory {

	/**
	 * Contains an array of script handles registered by theme.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of style handles registered by theme.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private static $styles = array();

	/**
	 * Register or enqueue asset default args.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return  array
	 */
	public static function set_asset_args( array $args = array() ): array {

		// Variables

			/**
			 * Filters args for theme asset registration.
			 *
			 * The arguments array comes from wp_register_style() and wp_register_script() functions.
			 *
			 * @example
			 *   array(
			 *     'handle'    => '',
			 *     'src'       => '',
			 *     'deps'      => array(),
			 *     'ver'       => MICHELLE_THEME_VERSION,
			 *     'media'     => 'screen', // Stylesheet only.
			 *     'in_footer' => true, // Script only.
			 *     'add_data'  => array(), // wp_style_add_data() or wp_script_add_data()
			 *     'localize'  => array(), // wp_localize_script()
			 *     'inline'    => array(), // wp_add_inline_script() or wp_add_inline_style()
			 *   )
			 *
			 * @since  1.0.0
			 *
			 * @param  array $args
			 */
			$args = (array) apply_filters( 'michelle/assets/set_asset_args', $args );


		// Output

			return wp_parse_args( $args, array(
				'handle'    => '',
				'src'       => '',
				'deps'      => array(),
				'ver'       => 'v' . MICHELLE_THEME_VERSION,
				'media'     => 'screen', // Stylesheet only.
				'in_footer' => true, // Script only.
				'add_data'  => array(), // wp_style_add_data() or wp_script_add_data()
				'localize'  => array(), // wp_localize_script()
				'inline'    => array(), // wp_add_inline_script() or wp_add_inline_style()
			) );

	} // /process_asset

	/**
	 * Enqueue style.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return  void
	 */
	public static function style_enqueue( array $args = array() ) {

		// Variables

			$args = self::set_asset_args( $args );


		// Requirements check

			if ( empty( $args['handle'] ) ) {
				return;
			}


		// Processing

			if ( ! in_array( $args['handle'], self::$styles, true ) ) {
				self::style_register( $args );
			}
			wp_enqueue_style( $args['handle'] );

			foreach ( (array) $args['inline'] as $context => $data ) {
				$context = ( is_string( $context ) ) ? ( $context ) : ( '' );
				wp_add_inline_style(
					$args['handle'],
					self::esc_css( (string) $data, $context )
				);
			}

	} // /style_enqueue

	/**
	 * Register style.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return  void
	 */
	public static function style_register( array $args = array() ) {

		// Requirements check

			if ( wp_style_is( $args['handle'], 'registered' ) ) {
				return;
			}


		// Variables

			self::$styles[] = $args['handle'];

			$args = self::set_asset_args( $args );


		// Processing

			wp_register_style(
				$args['handle'],
				$args['src'],
				$args['deps'],
				$args['ver'],
				$args['media']
			);

			foreach ( (array) $args['add_data'] as $key => $value ) {
				wp_style_add_data( $args['handle'], $key, $value );
			}

	} // /style_register

	/**
	 * Enqueue script.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return  void
	 */
	public static function script_enqueue( array $args = array() ) {

		// Variables

			$args = self::set_asset_args( $args );


		// Requirements check

			if ( empty( $args['handle'] ) ) {
				return;
			}


		// Processing

			if ( ! in_array( $args['handle'], self::$scripts, true ) ) {
				self::script_register( $args );
			}
			wp_enqueue_script( $args['handle'] );

			foreach ( (array) $args['localize'] as $object_name => $l10n ) {
				wp_localize_script( $args['handle'], $object_name, $l10n );
			}

			foreach ( (array) $args['inline'] as $data ) {
				wp_add_inline_script(
					$args['handle'],
					(string) $data
				);
			}

	} // /script_enqueue

	/**
	 * Register script.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $args
	 *
	 * @return  void
	 */
	public static function script_register( array $args = array() ) {

		// Requirements check

			if ( wp_script_is( $args['handle'], 'registered' ) ) {
				return;
			}


		// Variables

			self::$scripts[] = $args['handle'];

			$args = self::set_asset_args( $args );


		// Processing

			wp_register_script(
				$args['handle'],
				$args['src'],
				$args['deps'],
				$args['ver'],
				$args['in_footer']
			);

			foreach ( (array) $args['add_data'] as $key => $value ) {
				wp_script_add_data( $args['handle'], $key, $value );
			}

	} // /script_register

	/**
	 * Prints stylesheet link tags directly.
	 *
	 * This should be used for stylesheets that aren't global and thus should only be loaded
	 * if the HTML markup they are responsible for is actually present. Template parts should
	 * use this method when the related markup requires a specific stylesheet to be loaded.
	 * If preloading stylesheets is disabled, this method will not do anything.
	 *
	 * If the `<link>` tag for a given stylesheet has already been printed, it will be skipped.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  string ...$handles  One or more stylesheet handles.
	 *
	 * @return  void
	 */
	public static function print_styles( string ...$handles ) {

		// Requirements check

			// If preloading styles is disabled (and thus they have already been enqueued), return early.
			if ( self::is_preloading_styles_disabled() ) {
				return;
			}


		// Variables

			$css_files = Styles::get_css_files();
			$handles   = array_filter(
				$handles,
				function( $handle ) use ( $css_files ) {
					return empty( $css_files[ $handle ]['global'] );
				}
			);


		// Processing

			if ( ! empty( $handles ) ) {
				wp_print_styles( $handles );
			}

	} // /print_styles

	/**
	 * Whether not to preload stylesheets and inject link tags directly into page content.
	 *
	 * Using this technique generally improves performance, however may not be preferred
	 * under certain circumstances. For example, since AMP will include all style rules
	 * directly in the head, it must not be used in that context.
	 *
	 * @since  1.3.0
	 *
	 * @return  bool
	 */
	public static function is_preloading_styles_disabled(): bool {

		// Output

			/**
			 * Filters whether not to preload stylesheets and inject their link tags within the page content.
			 *
			 * @since  1.3.0
			 *
			 * @param  bool $disabled  By default, returns false.
			 */
			return (bool) apply_filters( 'michelle/assets/is_preloading_styles_disabled', false );

	} // /is_preloading_styles_disabled

	/**
	 * Check whether we should disable JavaScript output.
	 *
	 * @since    1.3.0
	 * @version  1.3.5
	 *
	 * @return  bool
	 */
	public static function is_js_disabled(): bool {

		// Output

			/**
			 * Filters whether to disable outputting JavaScript into HTML.
			 *
			 * @since    1.3.0
			 * @version  1.3.5
			 *
			 * @param  bool $disabled  By default, returns false.
			 */
			return (bool) apply_filters( 'michelle/assets/is_js_disabled', false );

	} // /is_js_disabled

	/**
	 * Escape CSS code.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $css
	 * @param  string $context  Optional CSS code identification for better filtering.
	 *
	 * @return  string
	 */
	public static function esc_css( string $css = '', string $context = '' ): string {

		// Output

			/**
			 * Escapes inline CSS code.
			 *
			 * @since  1.0.0
			 *
			 * @param  string $css    CSS code.
			 * @param  string $context  Optional context.
			 */
			return (string) apply_filters( 'michelle/assets/esc_css', (string) $css, (string) $context );

	} // /esc_css

	/**
	 * Strip script off certain characters.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $script
	 *
	 * @return  string
	 */
	public static function strip( string $script = '' ): string {

		// Output

			return preg_replace( '/\s+/', ' ', $script );

	} // /strip

}
