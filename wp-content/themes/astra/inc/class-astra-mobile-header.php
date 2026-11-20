<?php
/**
 * Astra Loop
 *
 * @package Astra
 * @since 1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Astra_Mobile_Header' ) ) {

	/**
	 * Astra_Mobile_Header
	 *
	 * @since 1.4.0
	 */
	class Astra_Mobile_Header {
		/**
		 * Instance
		 *
		 * @since 1.4.0
		 *
		 * @var object Class object.
		 */
		private static $instance;

		/**
		 * Initiator
		 *
		 * @since 1.4.0
		 *
		 * @return object initialized object of class.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @since 1.4.0
		 */
		public function __construct() {
			add_action( 'astra_header', array( $this, 'mobile_header_markup' ), 5 );
			add_action( 'astra_customizer_save', array( $this, 'generate_mobile_header_logo' ) );
			add_action( 'body_class', array( $this, 'add_body_class' ) );
			add_filter( 'astra_main_menu_toggle_classes', array( $this, 'menu_toggle_classes' ) );
			add_filter( 'walker_nav_menu_start_el', array( $this, 'toggle_button' ), 20, 4 );
			add_filter( 'astra_walker_nav_menu_start_el', array( $this, 'toggle_button' ), 10, 4 );
		}

		/**
		 * Add submenu toggle button used for mobile devices.
		 *
		 * @since 1.6.9
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 *
		 * @return String Menu item's starting markup.
		 */
		public function toggle_button( $item_output, $item, $depth, $args ) {

			$menu_locations = array( 'primary', 'above_header_menu', 'secondary_menu', 'below_header_menu', 'mobile_menu' );

			for ( $index = 3; $index <= Astra_Builder_Helper::$component_limit; $index++ ) {
				array_push( $menu_locations, 'menu_' . $index );
			}

			// Add toggle button if menu is from Astra.
			if ( true === is_object( $args ) ) {
				if ( isset( $args->theme_location ) && in_array( $args->theme_location, $menu_locations ) ) {
					if ( isset( $item->classes ) && in_array( 'menu-item-has-children', $item->classes ) ) {
						$item_output = $this->menu_arrow_button_markup( $item_output, $item );
					}
				}
			} else {
				if ( isset( $item->post_parent ) && 0 === $item->post_parent ) {
					$item_output = $this->menu_arrow_button_markup( $item_output, $item );
				}
			}

			return $item_output;
		}

		/**
		 * Get Menu Arrow Button Mark up
		 *
		 * @param string  $item_output The menu item's starting HTML output.
		 * @param WP_Post $item        Menu item data object.
		 *
		 * @since 1.7.2
		 * @return string Menu item arrow button markup.
		 */
		public function menu_arrow_button_markup( $item_output, $item ) {
			$item_output  = apply_filters( 'astra_toggle_button_markup', $item_output, $item );
			$item_output .= '<button ' . astra_attr(
				'ast-menu-toggle',
				array(
					'aria-expanded' => 'false',
					'aria-label'    => astra_get_submenu_toggle_label( isset( $item->title ) ? $item->title : '' ),
				),
				$item
			) . '>' . Astra_Icons::get_icons( 'arrow' ) . '</button>';

			return $item_output;
		}

		/**
		 * Header Cart Icon Class
		 *
		 * @param array $classes Default argument array.
		 *
		 * @since 1.4.0
		 * @return array;
		 */
		public function menu_toggle_classes( $classes ) {
			return ' ast-mobile-menu-buttons-' . astra_get_option( 'mobile-header-toggle-btn-style' ) . ' ';
		}

		/**
		 * Mobile Header Markup
		 *
		 * @return void
		 */
		public function mobile_header_markup() {
			$mobile_header_logo = astra_get_option( 'mobile-header-logo' );
			$different_logo     = astra_get_option( 'different-mobile-logo' );

			if ( '' !== $mobile_header_logo && '1' == $different_logo ) {
				add_filter( 'astra_has_custom_logo', '__return_true' );
				add_filter( 'get_custom_logo', array( $this, 'astra_mobile_header_custom_logo' ), 10, 2 );
				add_filter( 'astra_is_logo_attachment', array( $this, 'add_mobile_logo_svg_class' ), 10, 2 );
			}
		}

		/**
		 * Replace logo with Mobile Header logo.
		 *
		 * @param sting $html Size name.
		 * @param int   $blog_id Icon.
		 * @since 1.4.0
		 * @return string html markup of logo.
		 */
		public function astra_mobile_header_custom_logo( $html, $blog_id ) {

			$mobile_header_logo = astra_get_option( 'mobile-header-logo' );

			$custom_logo_id = attachment_url_to_postid( $mobile_header_logo );

			$size = 'ast-mobile-header-logo-size';

			if ( is_customize_preview() ) {
				$size = 'full';
			}

			$logo = sprintf(
				'<a href="%1$s" class="custom-mobile-logo-link" rel="home" itemprop="url">%2$s</a>',
				esc_url( home_url( '/' ) ),
				wp_get_attachment_image(
					$custom_logo_id,
					$size,
					false,
					array(
						'class' => 'ast-mobile-header-logo',
					)
				)
			);

			return $html . $logo;
		}

		/**
		 * Generate the mobile header logo image size.
		 *
		 * The mobile logo markup requests the `ast-mobile-header-logo-size` image size,
		 * but a separate mobile logo attachment bypasses the desktop logo resize machinery
		 * which is keyed to the `custom_logo` theme mod. Generate the right-sized variant
		 * here so WordPress does not fall back to the full-size image on the frontend.
		 *
		 * The variant is generated additively via image_make_intermediate_size() instead
		 * of regenerating the full attachment metadata, so sizes generated for the same
		 * attachment by the desktop or transparent header logo machinery are preserved.
		 *
		 * @since 4.13.9
		 * @return void
		 */
		public function generate_mobile_header_logo() {

			/**
			 * Filter to disable Astra logo image resizing.
			 *
			 * Shared with the desktop logo resize machinery in Astra_Customizer::customize_save().
			 * Returning false skips generating the `ast-mobile-header-logo-size` variant, leaving
			 * the mobile logo to render the full-size image.
			 *
			 * Example usage:
			 *     add_filter( 'astra_resize_logo', '__return_false' );
			 *
			 * @since 1.6.9
			 * @param bool $resize_logo Whether to generate resized logo image variants. Default true.
			 */
			if ( ! apply_filters( 'astra_resize_logo', true ) ) {
				return;
			}

			$mobile_header_logo = astra_get_option( 'mobile-header-logo' );
			$different_logo     = astra_get_option( 'different-mobile-logo' );

			if ( '' === $mobile_header_logo || '1' != $different_logo ) {
				return;
			}

			$logo_width = astra_get_option( 'ast-header-responsive-logo-width' );

			if ( ! is_array( $logo_width ) ) {
				return;
			}

			// Consider only the device width values — the option array may also carry
			// SVG height keys such as `desktop-svg-height`.
			$logo_widths = array();
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( isset( $logo_width[ $device ] ) ) {
					$logo_widths[] = absint( $logo_width[ $device ] );
				}
			}
			$logo_widths = array_filter( $logo_widths );

			if ( empty( $logo_widths ) ) {
				return;
			}

			$max_width      = max( $logo_widths );
			$mobile_logo_id = attachment_url_to_postid( $mobile_header_logo );

			if ( ! $mobile_logo_id ) {
				return;
			}

			$metadata = wp_get_attachment_metadata( $mobile_logo_id );

			// Bail for SVGs / non-image attachments, or when the original is already small enough.
			if ( ! is_array( $metadata ) || empty( $metadata['width'] ) || $metadata['width'] <= $max_width ) {
				return;
			}

			// Bail when the variant is already generated at the current width.
			if ( isset( $metadata['sizes']['ast-mobile-header-logo-size']['width'] ) && $max_width === (int) $metadata['sizes']['ast-mobile-header-logo-size']['width'] ) {
				return;
			}

			$fullsizepath = get_attached_file( $mobile_logo_id );

			if ( ! $fullsizepath || ! file_exists( $fullsizepath ) ) {
				return;
			}

			if ( ! function_exists( 'image_make_intermediate_size' ) ) {
				/** @psalm-suppress UndefinedConstant */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				require_once ABSPATH . 'wp-admin/includes/image.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound
			}

			$resized = image_make_intermediate_size( $fullsizepath, $max_width, 0, false );

			if ( $resized ) {
				$metadata['sizes']['ast-mobile-header-logo-size'] = $resized;
				wp_update_attachment_metadata( $mobile_logo_id, $metadata );
			}
		}

		/**
		 * Add svg class to mobile logo.
		 *
		 * @param bool  $is_logo_attachment is attachment is logo image?.
		 * @param array $attachment attachment data.
		 * @since 2.1.0
		 * @return bool return if attachment is mobile logo image.
		 */
		public function add_mobile_logo_svg_class( $is_logo_attachment, $attachment ) {

			$mobile_header_logo = astra_get_option( 'mobile-header-logo' );
			$custom_logo_id     = attachment_url_to_postid( $mobile_header_logo );

			if ( $custom_logo_id === $attachment->ID ) {
				return true;
			}

			return $is_logo_attachment;
		}

		/**
		 * Add Body Classes
		 *
		 * @param array $classes Body Class Array.
		 * @return array
		 */
		public function add_body_class( $classes ) {

			/**
			 * Add class for header width
			 */
			$header_content_layout = astra_get_option( 'different-mobile-logo' );

			if ( '0' == $header_content_layout ) {
				$classes[] = 'ast-mobile-inherit-site-logo';
			}

			return $classes;
		}

	}

	/**
	 * Initialize class object with 'get_instance()' method
	 */
	Astra_Mobile_Header::get_instance();

}
