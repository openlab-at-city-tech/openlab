<?php
//phpcs:disable WordPress.Security.NonceVerification.Recommended

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Menus\Menu_Abstract;
use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;

/**
 * Widget Debugging
 *
 * Passed an `asm_debug` URL parameter to print a JS
 * variable including information about a page's widgets.
 *
 * @author OnPoint Plugins
 *
 * @phpstan-import-type PAGE_SETTINGS from Page
 * @phpstan-import-type CATEGORY_SETTINGS from Category
 *
 * @see    content/plugins/advanced-sidebar-menu/js/src/debug.ts
 * @phpstan-type DEBUG_INFO array{
 *      basic: string,
 *      classicEditor?: bool,
 *      classicWidgets: bool,
 *      excludedCategories?: int[],
 *      excluded_pages?: int[],
 *      menus: array<string, array<string, mixed>>,
 *      php: string,
 *      pro: string|false,
 *      scriptDebug: bool,
 *      WordPress: string,
 *  }
 */
class Debug {
	use Singleton;

	/**
	 * @see DEBUG_PARAM in js/src/debug.ts
	 */
	public const DEBUG_PARAM   = 'asm_debug';
	public const SCRIPT_HANDLE = 'advanced-sidebar-menu/debug/js';


	/**
	 * Add actions and filters.
	 *
	 * @return void
	 */
	protected function hook() {
		if ( isset( $_GET[ static::DEBUG_PARAM ] ) ) {
			add_action( 'advanced-sidebar-menu/widget/before-render', [ $this, 'include_menu_in_debug_info' ], 1 );

			if ( \is_array( $_GET[ static::DEBUG_PARAM ] ) ) {
				add_filter( 'advanced-sidebar-menu/menus/widget-instance', [ $this, 'adjust_widget_settings' ], 100 );
			}
			add_action( 'wp_print_footer_scripts', [ $this, 'load_scripts' ], 1 );
		}
	}


	/**
	 * Adjust widget settings using the URL parameters.
	 *
	 * @phpstan-param PAGE_SETTINGS|CATEGORY_SETTINGS $instance
	 *
	 * @param array                                   $instance - Widget settings.
	 *
	 * @return array<string, mixed>
	 */
	public function adjust_widget_settings( array $instance ): array {
		if ( ! isset( $_GET[ self::DEBUG_PARAM ] ) ) {
			return $instance;
		}

		$overrides = Utils::instance()->array_map_recursive( function( $value ) {
			return sanitize_text_field( wp_unslash( $value ) );
		}, (array) $_GET[ static::DEBUG_PARAM ] ); //phpcs:ignore -- Input is sanitized.

		// Do not allow passing a non-public post type.
		if ( isset( $overrides['post_type'] ) ) {
			$type = get_post_type_object( $overrides['post_type'] );
			if ( null !== $type && ! $type->public ) {
				unset( $overrides['post_type'] );
			}
		}
		// Adjust global excluded categories.
		if ( isset( $overrides['excludedCategories'] ) ) {
			add_filter( 'advanced-sidebar-menu/meta/category-meta/excluded-term-ids', function() use ( $overrides ) {
				return $overrides['excludedCategories'];
			}, 100 );
		}
		// Adjust global excluded pages.
		if ( isset( $overrides['excluded_pages'] ) ) {
			add_filter( 'advanced-sidebar-menu/meta/page-meta/excluded-page-ids', function() use ( $overrides ) {
				return $overrides['excluded_pages'];
			}, 100 );
		}

		return wp_parse_args( $overrides, $instance );
	}


	/**
	 * Get information about the current site.
	 *
	 * 1. PHP version.
	 * 2. Plugin version.
	 * 3. WP version.
	 * 4. PRO version.
	 * 5. Script debug active.
	 * 6. Classic widgets enabled.
	 *
	 * @since 9.0.2
	 *
	 * @phpstan-return DEBUG_INFO
	 *
	 * @return array
	 */
	public function get_site_info(): array {
		if ( ! \function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$data = [
			'basic'          => ADVANCED_SIDEBAR_MENU_BASIC_VERSION,
			'classicWidgets' => is_plugin_active( 'classic-widgets/classic-widgets.php' ),
			'menus'          => [],
			'php'            => PHP_VERSION,
			'pro'            => false,
			'scriptDebug'    => Scripts::instance()->is_script_debug_enabled(),
			'WordPress'      => get_bloginfo( 'version' ),
		];

		$current_post = get_queried_object();
		if ( $current_post instanceof \WP_Post ) {
			$data['classicEditor'] = ! use_block_editor_for_post( $current_post );
		}

		if ( \defined( 'ADVANCED_SIDEBAR_MENU_PRO_VERSION' ) ) {
			$data['pro'] = ADVANCED_SIDEBAR_MENU_PRO_VERSION;
		}

		return $data;
	}


	/**
	 * Load the JS and the JS `asm_debug` variable into the footer.
	 *
	 * @return void
	 */
	public function load_scripts(): void {
		$file = Scripts::instance()->get_dist_file( Scripts::FILE_DEBUG, 'js' );
		wp_enqueue_script( self::SCRIPT_HANDLE, $file, [], ADVANCED_SIDEBAR_MENU_BASIC_VERSION, true );
		wp_localize_script( self::SCRIPT_HANDLE, self::DEBUG_PARAM, apply_filters( 'advanced-sidebar-menu/debug/print-instance', $this->get_site_info() ) );
	}


	/**
	 * Print the widget settings as a JS variable.
	 *
	 * @phpstan-param Menu_Abstract<PAGE_SETTINGS|CATEGORY_SETTINGS> $menu
	 *
	 * @param Menu_Abstract                                          $menu - Menu class.
	 *
	 * @return void
	 */
	public function include_menu_in_debug_info( Menu_Abstract $menu ): void {
		add_filter( 'advanced-sidebar-menu/debug/print-instance', function( array $data ) use ( $menu ) {
			$data['menus'][ $menu->args['widget_id'] ?? '' ] = $menu->instance;
			return $data;
		} );
	}
}
