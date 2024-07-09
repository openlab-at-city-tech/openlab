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
 * @phpstan-type DEBUG_INFO array{
 *      basic: string,
 *      classicWidgets: bool,
 *      excludedCategories?: int[],
 *      excluded_pages?: int[],
 *      php: string,
 *      pro: string|false,
 *      scriptDebug: bool,
 *      WordPress: string,
 *      pro?: string
 *  }
 */
class Debug {
	use Singleton;

	public const DEBUG_PARAM = 'asm_debug';


	/**
	 * Add actions and filters.
	 *
	 * @return void
	 */
	protected function hook() {
		if ( isset( $_GET[ static::DEBUG_PARAM ] ) ) {
			add_action( 'advanced-sidebar-menu/widget/before-render', [ $this, 'print_instance' ], 1, 2 );

			if ( \is_array( $_GET[ static::DEBUG_PARAM ] ) ) {
				add_filter( 'advanced-sidebar-menu/menus/widget-instance', [ $this, 'adjust_widget_settings' ], 100 );
			}
		}
	}


	/**
	 * Adjust widget settings using the URL parameters.
	 *
	 * @param array $instance - Widget settings.
	 *
	 * @return array
	 */
	public function adjust_widget_settings( array $instance ) {
		if ( ! isset( $_GET[ static::DEBUG_PARAM ] ) ) {
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
	 * @phpstan-return array{
	 *     basic: string,
	 *     classicWidgets: bool,
	 *     php: string,
	 *     pro: string|false,
	 *     scriptDebug: bool,
	 *     WordPress: string,
	 *     pro?: string
	 * }
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
	 * Print the widget settings as a JS variable.
	 *
	 * @param Menu_Abstract<array<string,string>> $menu   - Menu class.
	 * @param Page|Category                       $widget - Widget class.
	 *
	 * @return void
	 */
	public function print_instance( $menu, $widget ) {
		$data = apply_filters( 'advanced-sidebar-menu/debug/print-instance', $this->get_site_info(), $menu, $widget );
		?>
		<script name="<?php echo esc_attr( static::DEBUG_PARAM ); ?>">
			window.asm_debug = window.asm_debug || <?php echo wp_json_encode( $data ); ?>;
			<?php
			echo esc_attr( static::DEBUG_PARAM );
			?>
			[ '<?php echo esc_js( $menu->args['widget_id'] ?? '' ); ?>' ] = <?php echo wp_json_encode( $menu->instance ); ?>;
		</script>
		<?php
	}
}
