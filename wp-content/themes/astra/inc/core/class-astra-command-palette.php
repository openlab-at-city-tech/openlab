<?php
/**
 * Astra Command Palette Integration
 *
 * Integrates Astra customizer panels with WordPress Command Palette.
 *
 * @package     Astra
 * @link        https://wpastra.com/
 * @since       Astra 4.12.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Astra Command Palette Integration
 */
if ( ! class_exists( 'Astra_Command_Palette' ) ) {

	/**
	 * Astra Command Palette Integration Class
	 */
	class Astra_Command_Palette {
		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_command_palette_script' ) );
			add_action( 'admin_bar_menu', array( $this, 'add_search_icon_to_admin_bar' ), 999 );
		}

		/**
		 * Add search icon to admin bar
		 *
		 * @since 4.12.2
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
		 * @return void
		 */
		public function add_search_icon_to_admin_bar( $wp_admin_bar ) {

			/**
			 * Filter to enable/disable the search icon in admin bar.
			 *
			 * @since 4.12.3
			 * @param bool $show_search_icon Whether to show the search icon. Default true.
			 */
			if ( ! apply_filters( 'astra_show_admin_bar_search_icon', true ) ) {
				return;
			}

			if ( ! is_admin() ) {
				return;
			}

			/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			global $wp_version;
			if ( version_compare( $wp_version, '6.3', '<' ) ) {
				return;
			}

			// WP 7.0+ ships a built-in command palette, so Astra's search icon is not needed.
			// Use 6.9.99 so that 7.0-rc and 7.0-beta pre-release versions are also excluded.
			if ( version_compare( $wp_version, '6.9.99', '>' ) ) {
				return;
			}

			$search_icon = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M15 11H15.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M11 11H11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M7 11H7.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"/>
			</svg>';

			// Detect OS for keyboard shortcut display.
			$is_mac     = false;
			$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
			if ( strpos( $user_agent, 'Mac' ) !== false || strpos( $user_agent, 'iPhone' ) !== false || strpos( $user_agent, 'iPad' ) !== false ) {
				$is_mac = true;
			}
			$shortcut_key = $is_mac ? '⌘K' : 'Ctrl+K';

			$title = '<span class="astra-search-icon">' . $search_icon . '</span>'
				. '<span class="astra-search-tooltip">' . esc_html__( 'Search everything: from site settings to pages and design tools', 'astra' ) . ' (' . esc_html( $shortcut_key ) . ')</span>';

			$wp_admin_bar->add_node(
				array(
					'id'     => 'astra-command-palette-search',
					'title'  => $title,
					'href'   => '#',
					'meta'   => array(
						'class' => 'astra-command-palette-trigger',
					),
					'parent' => 'top-secondary',
				)
			);
		}

		/**
		 * Enqueue Command Palette integration script
		 *
		 * @since 4.12.0
		 * @return void
		 */
		public function enqueue_command_palette_script() {

			/** @psalm-suppress InvalidGlobal */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			global $wp_customize, $wp_version;

			// Skip if WP version is less than 6.3, as Command Palette is not available.
			if ( version_compare( $wp_version, '6.3', '<' ) ) {
				return;
			}

			// Skip in customizer for WP < 7.0 — WP 7.0+ ships a built-in command palette.
			// Use 6.9.99 so that 7.0-rc and 7.0-beta pre-release versions are included.
			if ( $wp_customize && version_compare( $wp_version, '6.9.99', '<' ) ) {
				return;
			}

			/** @psalm-suppress RedundantCondition */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$dir_name = SCRIPT_DEBUG ? 'unminified' : 'minified';
			/** @psalm-suppress RedundantCondition */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$file_prefix = SCRIPT_DEBUG ? '' : '.min';

			$script_path = ASTRA_THEME_DIR . 'assets/js/' . $dir_name . '/command-palette' . $file_prefix . '.js';

			if ( ! file_exists( $script_path ) ) {
				return;
			}

			// Enqueue command palette CSS.
			wp_enqueue_style(
				'astra-command-palette',
				ASTRA_THEME_URI . 'assets/css/minified/command-palette.min.css',
				array(),
				ASTRA_THEME_VERSION
			);
			wp_style_add_data( 'astra-command-palette', 'rtl', 'replace' );

			/** @psalm-suppress InvalidArgument */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			wp_enqueue_script(
				'astra-command-palette',
				ASTRA_THEME_URI . 'assets/js/' . $dir_name . '/command-palette' . $file_prefix . '.js',
				array( 'wp-data', 'wp-element', 'wp-commands', 'wp-dom-ready' ),
				ASTRA_THEME_VERSION,
				true
			);

			wp_localize_script(
				'astra-command-palette',
				'astraCommandPalette',
				array(
					'customizerUrl' => admin_url( 'customize.php' ),
					'panels'        => $this->get_customizer_panels(),
					'iconUrl'       => ASTRA_THEME_URI . 'inc/assets/images/astra-logo.svg',
				)
			);
		}

		/**
		 * Get customizer panels configuration
		 *
		 * @since 4.12.0
		 * @return array List of customizer panels.
		 */
		private function get_customizer_panels() {
			$panels = array(
				array(
					'name'        => 'astra/customizer-global',
					'label'       => __( 'Customizer: Global', 'astra' ),
					'searchLabel' => __( 'Customizer, Global, Container, Layout, Colors, Color, Background, Base Colors, Typography, Font, Fonts, Headings, Text, Buttons, Button, Style, Base Typography', 'astra' ),
					'type'        => 'panel',
					'id'          => 'panel-global',
				),
				array(
					'name'        => 'astra/customizer-header',
					'label'       => __( 'Customizer: Header', 'astra' ),
					'searchLabel' => __( 'Customizer, Header, Site Identity, Logo, Site Title, Tagline, Primary Header, Primary Menu, Menu, Navigation, Above Header, Below Header, Mobile Header, Mobile Menu, Search, Button, Social, Account, Cart, Widget, HTML, Off Canvas, Mobile Trigger', 'astra' ),
					'type'        => 'panel',
					'id'          => 'panel-header-builder-group',
				),
				array(
					'name'        => 'astra/customizer-footer',
					'label'       => __( 'Customizer: Footer', 'astra' ),
					'searchLabel' => __( 'Customizer, Footer, Footer Widgets, Footer Bar, Copyright, Primary Footer, Above Footer, Below Footer, Menu, Social, HTML, Widget', 'astra' ),
					'type'        => 'panel',
					'id'          => 'panel-footer-builder-group',
				),
				array(
					'name'        => 'astra/customizer-blog',
					'label'       => __( 'Customizer: Blog', 'astra' ),
					'searchLabel' => __( 'Customizer, Blog, Archive, Single Post, Post, Single Page, Page, Content, Excerpt, Featured Image, Meta, Author, Date, Categories, Tags, Comments', 'astra' ),
					'type'        => 'section',
					'id'          => 'section-blog-group',
				),
				array(
					'name'        => 'astra/customizer-general',
					'label'       => __( 'Customizer: General', 'astra' ),
					'searchLabel' => __( 'Customizer, General, Sidebar, Accessibility, Skip to Content, Block Editor, Gutenberg, Misc, Scroll To Top, Back to Top', 'astra' ),
					'type'        => 'section',
					'id'          => 'section-general-group',
				),
			);

			// Add WooCommerce panel only if WooCommerce is active.
			if ( class_exists( 'WooCommerce' ) ) {
				$panels[] = array(
					'name'        => 'astra/customizer-woocommerce',
					'label'       => __( 'Customizer: WooCommerce', 'astra' ),
					'searchLabel' => __( 'Customizer, WooCommerce, Shop, Store, Products, Single Product, Cart, Checkout, Account, My Account, Orders, Sidebar', 'astra' ),
					'type'        => 'panel',
					'id'          => 'woocommerce',
				);
			}

			return $panels;
		}
	}

	new Astra_Command_Palette();
}
