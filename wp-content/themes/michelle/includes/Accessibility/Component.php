<?php
/**
 * Accessibility component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.5.0
 */

namespace WebManDesign\Michelle\Accessibility;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Assets;
use WebManDesign\Michelle\Header;
use WebManDesign\Michelle\Entry;
use WP_Post;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Component implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.5.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'wp_enqueue_scripts', __CLASS__ . '::enqueue', MICHELLE_ENQUEUE_PRIORITY );

				add_action( 'tha_body_top', __CLASS__ . '::anchor_top_of_page', -10 );

				add_action( 'tha_body_top',     __CLASS__ . '::skip_links_body', 20 );
				add_action( 'tha_entry_bottom', __CLASS__ . '::skip_links_entry', 999 );

			// Filters

				add_filter( 'walker_nav_menu_start_el', __CLASS__ . '::nav_menu_item_label', 20, 4 );

	} // /init

	/**
	 * Enqueue assets.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function enqueue() {

		// Requirements check

			if ( Assets\Factory::is_js_disabled() ) {
				return;
			}


		// Processing

			// Navigation scripts.
			if ( Header\Component::is_enabled() ) {
				Assets\Factory::script_enqueue( array(
					'handle'    => 'a11y-menu',
					'src'       => get_theme_file_uri( 'vendor/a11y-menu/a11y-menu.dist.min.js' ),
					'in_footer' => false,
					'add_data'  => array(
						'async'    => true,
						'precache' => true,
					),
					'localize'  => array(
						'a11yMenuConfig' => array(
							'mode'              => array( 'esc', 'button' ),
							'menu_selector'     => '.toggle-sub-menus',
							'button_attributes' => array(
								'class'      => 'button-toggle-sub-menu',
								'aria-label' => array(
									/* translators: %s: menu item label. */
									'collapse' => esc_html__( 'Collapse menu: %s', 'michelle' ),
									/* translators: %s: menu item label. */
									'expand'   => esc_html__( 'Expand menu: %s', 'michelle' ),
								),
							),
						),
					),
				) );
			}

	} // /enqueue

	/**
	 * Anchor for top of the page.
	 *
	 * Should be the first element on the page, before the skip links.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function anchor_top_of_page() {

		// Output

			echo '<a name="top"></a>' . PHP_EOL.PHP_EOL;

	} // /anchor_top_of_page

	/**
	 * Skip link generator.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $id     Link target element ID.
	 * @param  string $text   Link text.
	 * @param  string $class  Additional link CSS classes.
	 * @param  string $html   Output html, use "%s" for actual link output.
	 *
	 * @return  string
	 */
	public static function link_skip_to( string $id = 'content', string $text = '', string $class = '', string $html = '%s' ): string {

		// Pre

			/**
			 * Bypass filter for Content::link_skip_to().
			 *
			 * Returning a non-false value will short-circuit the method,
			 * returning the passed value instead.
			 *
			 * @since  1.0.0
			 *
			 * @param  mixed  $pre    Default: false. If not false, method returns this value.
			 * @param  string $id     Link target element ID.
			 * @param  string $text   Link text.
			 * @param  string $class  Additional link CSS classes.
			 * @param  string $html   Output html, use "%s" for actual link output.
			 */
			$pre = apply_filters( 'pre/michelle/accessibility/link_skip_to', false, $id, $text, $class, $html );

			if ( false !== $pre ) {
				return $pre;
			}


		// Processing

			if ( empty( $text ) ) {
				$text = __( 'Skip to main content', 'michelle' );
			}


		// Output

			return sprintf(
				(string) $html,
				'<a class="' . esc_attr( trim( 'skip-link screen-reader-text ' . $class ) ) . '" href="#' . esc_attr( trim( $id ) ) . '">' . esc_html( $text ) . '</a>'
			);

	} // /link_skip_to

	/**
	 * Skip links: Body top.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function skip_links_body() {

		// Output

			get_template_part( 'templates/parts/accessibility/menu', 'skip-links' );

	} // /skip_links_body

	/**
	 * Skip links: Entry bottom.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function skip_links_entry() {

		// Requirements check

			if ( ! Entry\Component::is_singular() ) {
				return;
			}


		// Output

			echo
				'<div class="entry-skip-links">'
				. self::link_skip_to( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'site-navigation',
					esc_html__( 'Skip back to main navigation', 'michelle' )
				)
				. '</div>';

	} // /skip_links_entry

	/**
	 * Menu item modification: item label.
	 *
	 * Primary menu only.
	 * This is for `a11y-menu` script improved accessibility.
	 *
	 * @since  1.2.0
	 *
	 * @param  string  $item_output Menu item output HTML (without closing `</li>`).
	 * @param  WP_Post $item        The current menu item.
	 * @param  int     $depth       Depth of menu item. Used for padding. Since WordPress 4.1.
	 * @param  object  $args        An object of wp_nav_menu() arguments.
	 *
	 * @return  string
	 */
	public static function nav_menu_item_label( string $item_output, $item, int $depth, $args ): string {

		// Requirements check

			if ( ! $item instanceof WP_Post ) {
				return $item_output;
			}


		// Processing

			if (
				'primary' == $args->theme_location
				&& in_array( 'menu-item-has-children', $item->classes )
			) {
				// From https://developer.wordpress.org/reference/classes/walker_nav_menu/start_el/.
				$title = apply_filters( 'the_title', $item->title, $item->ID );
				$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

				// Unfortunately, there is no way of filtering menu item `<li>` tag, so we have to use
				// the actual menu item `<a>` tag for this.
				return str_replace(
					'<a ',
					'<a data-submenu-label="' . esc_attr( wp_strip_all_tags( $title ) ) . '" ',
					$item_output
				);
			}


		// Output

			return $item_output;

	} // /nav_menu_item_label

}
