<?php
/**
 * Page builder component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Tool;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize\Mod;
use WebManDesign\Michelle\Entry;
use WebManDesign\Michelle\Header\Body_Class;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Page_Builder implements Component_Interface {

	/**
	 * Check if any commonly used page builder is active.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     boolean
	 */
	private static $is_page_builder_plugin_active = false;

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Variables

			self::$is_page_builder_plugin_active = (

				/**
				 * Beaver Builder
				 * @link  https://wordpress.org/plugins/beaver-builder-lite-version/
				 */
				class_exists( 'FLBuilder' )

				/**
				 * Elementor
				 * @link  https://wordpress.org/plugins/elementor/
				 */
				|| class_exists( 'Elementor\Plugin' )

				/**
				 * Page Builder by SiteOrigin
				 * @link  https://wordpress.org/plugins/siteorigin-panels/
				 */
				|| class_exists( 'SiteOrigin_Panels' )

				/**
				 * Divi Builder
				 * @link  https://www.elegantthemes.com/documentation/developers/divi-module/advanced-field-types-for-module-settings/
				 */
				|| class_exists( 'ET_Builder_Module' )

				/**
				 * WPBakery Page Builder
				 * @link  https://wpbakery.com/
				 */
				|| class_exists( 'Vc_Manager' )

				/**
				 * Visual Composer Website Builder
				 * @link  https://wordpress.org/plugins/visualcomposer/
				 */
				|| class_exists( 'VcvEnv' )

			);


		// Processing

			// Filters

				add_filter( 'body_class', __CLASS__ . '::body_class', 99 );

				add_filter( 'theme_templates', __CLASS__ . '::remove_page_template' );

				add_filter( 'michelle/customize/options/get', __CLASS__ . '::options' );

				add_filter( 'michelle/content/show_primary_title', __CLASS__ . '::return_false_when_enabled', 20 );

				add_filter( 'michelle/tool/page_builder/is_enabled', __CLASS__ . '::automatic_check' );

				add_filter( 'pre/michelle/entry/media/display', __CLASS__ . '::return_empty_string_when_enabled', 100 );

	} // /init

	/**
	 * Theme options.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $options
	 *
	 * @return  array
	 */
	public static function options( array $options ): array {

		// Variables

			$is_no_padding_default = class_exists( 'Vc_Manager' ) || class_exists( 'SiteOrigin_Panels' );


		// Processing

			$options[ 940 . 'page_builders' ] = array(
				'id'             => 'page_builders',
				'type'           => 'section',
				'create_section' => esc_html_x( 'Page builders', 'Customizer section title.', 'michelle' ),
				'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
			);

			$options[ 940 . 'page_builders' . 100 ] = array(
				'type'        => 'checkbox',
				'id'          => 'page_builder_template',
				'label'       => esc_html__( 'Enable "Page builder" page/post template', 'michelle' ),
				'description' => esc_html__( 'Use this page/post template when building a content with your page builder plugin.', 'michelle' ) . ' ' . esc_html__( 'You can tweak the desired layout of this template below.', 'michelle' ) . '<br>(<a href="https://support.webmandesign.eu/page-template/" target="_blank"  rel="noopener noreferrer">' . esc_html__( 'Open a page/post template instructions in new window &rarr;', 'michelle' ) . '</a>)',
				'default'     => self::$is_page_builder_plugin_active,
			);

			$options[ 940 . 'page_builders' . 110 ] = array(
				'type'        => 'radio',
				'id'          => 'page_builder_content_layout',
				'label'       => esc_html__( 'Page builder layout', 'michelle' ),
				'description' => esc_html__( 'Tweaks content area layout when using "Page builder" template.', 'michelle' ) . ' ' . esc_html__( 'As every page builder plugin works differently, set this according to your needs.', 'michelle' ),
				'default'     => ( $is_no_padding_default ) ? ( 'no-padding' ) : ( 'full-width' ),
				'choices'     => array(
					'full-width' => esc_html__( 'Full width content area, no padding', 'michelle' ),
					'no-padding' => esc_html__( 'Keep content area width, just remove padding', 'michelle' ),
				),
			);


		// Output

			return $options;

	} // /options

	/**
	 * HTML body classes.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $classes
	 *
	 * @return  array
	 */
	public static function body_class( array $classes ): array {

		// Processing

			// Any page builder layout.
			if (
				is_singular()
				&& self::is_enabled( $classes )
			) {
				$content_layout = Mod::get( 'page_builder_content_layout' );

				if ( 'full-width' === $content_layout ) {
					$classes[] = 'has-content-layout-no-padding';
					$classes[] = 'has-content-layout-full-width';
				} elseif ( 'no-padding' === $content_layout ) {
					$classes[] = 'has-content-layout-no-padding';
				}
			}


		// Output

			return array_unique( $classes );

	} // /body_class

	/**
	 * Is page builder template body class used?
	 *
	 * @param  mixed $body_classes  Optional forced array of body classes when using the method within `body_class` hook.
	 *
	 * @since  1.0.0
	 *
	 * @return  bool
	 */
	public static function is_enabled( $body_classes = array() ): bool {

		// Variables

			$check_body_class = stripos( implode( ' ', Body_Class::get_body_class( $body_classes ) ), 'template-page-builder' );


		// Output

			/**
			 * Filters whether we should apply page builder template.
			 *
			 * @since  1.0.0
			 *
			 * @param  bool $check_body_class  By default it checks for a specific body class name portion.
			 */
			return (bool) apply_filters( 'michelle/tool/page_builder/is_enabled', (bool) $check_body_class );

	} // /is_enabled

	/**
	 * Remove "Page builder" page/post template [page-builder(-*).php].
	 *
	 * @since  1.0.0
	 *
	 * @param  array $post_templates  Array of page templates. Keys are filenames, values are translated names.
	 *
	 * @return  array
	 */
	public static function remove_page_template( array $post_templates ): array {

		// Processing

			if ( ! Mod::get( 'page_builder_template' ) ) {
				foreach ( $post_templates as $file => $name ) {
					if ( 0 === stripos( $file, 'templates/page-builder' ) ) {
						unset( $post_templates[ $file ] );
					}
				}
			}


		// Output

			return $post_templates;

	} // /remove_page_template

	/**
	 * If page builder is enabled, return `false`.
	 *
	 * @since  1.0.0
	 *
	 * @param  bool $show
	 *
	 * @return  bool
	 */
	public static function return_false_when_enabled( bool $show ): bool {

		// Processing

			if ( self::is_enabled() ) {
				$show = false;
			}


		// Output

			return (bool) $show;

	} // /return_false_when_enabled

	/**
	 * If page builder is enabled, return empty string.
	 *
	 * Useful for `pre` filter hooks.
	 *
	 * @since  1.0.0
	 *
	 * @param  mixed $pre
	 *
	 * @return  mixed  Original pre value or empty string.
	 */
	public static function return_empty_string_when_enabled( $pre ) {

		// Processing

			if ( self::is_enabled() ) {
				return '';
			}


		// Output

			return $pre;

	} // /return_empty_string_when_enabled

	/**
	 * Enable page builder layout automatically for selected page builder plugins.
	 *
	 * Works with:
	 * - Beaver Builder
	 * - Elementor
	 *
	 * Does not work with:
	 * - WPBakery Page Builder because there is no way to check if the page was built
	 *   with this page builder except checking for shortcodes in the page content...
	 *
	 * @since  1.0.0
	 *
	 * @param  bool $is_enabled
	 *
	 * @return  bool
	 */
	public static function automatic_check( bool $is_enabled ): bool {

		// Variables

			$beaver_builder = is_callable( 'FLBuilderModel::is_builder_enabled' ) && \FLBuilderModel::is_builder_enabled();
			$elementor = is_callable( 'Elementor\Plugin::instance' ) && \Elementor\Plugin::instance()->db->is_built_with_elementor( get_the_ID() );


		// Processing

			if (
				is_singular()
				&& (
					$beaver_builder
					|| $elementor
				)
			) {
				return true;
			}


		// Output

			return (bool) $is_enabled;

	} // /automatic_check

}
