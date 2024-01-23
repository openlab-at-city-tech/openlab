<?php
/**
 * Menu component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.5.0
 */

namespace WebManDesign\Michelle\Menu;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize\Mod;
use WebManDesign\Michelle\Header;
use WP_Post;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::register' );

				add_action( 'tha_header_top', __CLASS__ . '::primary', 20 );

			// Filters

				add_filter( 'nav_menu_css_class', __CLASS__ . '::nav_menu_item_class', 10, 4 );

	} // /init

	/**
	 * Register custom menus.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function register() {

		// Processing

			register_nav_menus( array(
				'primary' => esc_html_x( 'Primary', 'Navigational menu location', 'michelle' ),
			) );

	} // /register

	/**
	 * Primary navigation.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function primary() {

		// Output

			get_template_part( 'templates/parts/menu/menu', 'primary' );

	} // /primary

	/**
	 * Get menu arguments: Primary.
	 *
	 * @since    1.0.0
	 * @version  1.5.0
	 *
	 * @param  bool $mobile_nav  Is mobile navigation enabled?
	 * @param  bool $fallback    Return arguments to set a `wp_page_menu()` fallback?
	 *
	 * @return  array
	 */
	public static function get_menu_args_primary( bool $mobile_nav = true, bool $fallback = false ): array {

		// Variables

			$args = array(
				'container'    => '',
				'menu_id'      => 'menu-primary',
				'menu_class'   => 'menu menu-primary toggle-sub-menus',
				'item_spacing' => 'preserve', // Required for `wp_page_menu()`, which differs from `wp_nav_menu()`.
			);
			// -> produces `<ul id="menu-primary" class="menu menu-primary toggle-sub-menus">...</ul>`

			// Accessibility helper: last accessible focusable element.
			$a11y_link = ( Mod::get( 'navigation_mobile' ) ) ? ( '<li class="menu-toggle-skip-link-container"><a href="#menu-toggle" class="menu-toggle-skip-link">' . esc_html__( 'Skip to menu toggle button', 'michelle' ) . '</a></li>' ) : ( '' );


		// Processing

			if ( ! $fallback ) {
			// For `wp_nav_menu()`
				$args['theme_location'] = 'primary';
				$args['depth']          = 4;
				$args['fallback_cb']    = __CLASS__ . '::wp_page_menu';
				$args['items_wrap']     = '<ul id="%1$s" class="%2$s">%3$s' . $a11y_link . '</ul>';
			} else {
			// For `wp_page_menu()`
				$args['before'] = '<ul id="menu-primary" class="menu menu-primary toggle-sub-menus menu-fallback">';
				$args['after']  = $a11y_link . '</ul>';
			}


		// Output

			return $args;

	} // /get_menu_args_primary

	/**
	 * wp_page_menu() with menu classes and primary menu args.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function wp_page_menu() {

		// Variables

			$output = wp_page_menu( array( 'echo' => false ) + (array) self::get_menu_args_primary(
				Mod::get( 'navigation_mobile' ),
				'fallback'
			) );


		// Output

			echo str_replace( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'current_page_item',
					'current_page_parent',
					'current_page_ancestor',
					'page_item_has_children',
					'page_item',
					'class="children"',
					"class='children'",
				),
				array(
					'current_page_item current-menu-item',
					'current_page_parent current-menu-parent',
					'current_page_ancestor current-menu-ancestor',
					'page_item_has_children menu-item-has-children',
					'page_item menu-item',
					'class="children sub-menu"',
					"class='children sub-menu'",
				),
				$output
			);

	} // /wp_page_menu

	/**
	 * Navigation item classes.
	 *
	 * @since    1.0.0
	 * @version  1.3.7
	 *
	 * @param  array   $classes The CSS classes that are applied to the menu item's `<li>` element.
	 * @param  WP_Post $item    The current menu item.
	 * @param  object  $args    An object of wp_nav_menu() arguments.
	 * @param  int     $depth   Depth of menu item. Used for padding. Since WordPress 4.1.
	 *
	 * @return  array
	 */
	public static function nav_menu_item_class( array $classes, $item, $args, int $depth = 0 ): array {

		// Requirements check

			if ( ! $item instanceof WP_Post ) {
				return $classes;
			}


		// Processing

			if ( 'primary' === $args->theme_location ) {

				// Depth indication class.
				$classes[] = 'menu-item-is-depth-' . absint( $depth );
			}

			// Fixing blog page ancestry for posts.
			if (
				array_search( 'current_page_ancestor', $classes )
				|| array_search( 'current_page_parent', $classes )
			) {
				$classes[] = 'current-menu-ancestor';
			}


		// Output

			return $classes;

	} // /nav_menu_item_class

}
