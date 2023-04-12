<?php

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
 */
class Debug {
	use Singleton;

	const DEBUG_PARAM = 'asm_debug';


	/**
	 * Add actions and filters.
	 *
	 * @return void
	 */
	protected function hook() {
		if ( ! empty( $_GET[ self::DEBUG_PARAM ] ) ) { //phpcs:ignore
			add_action( 'advanced-sidebar-menu/widget/before-render', [ $this, 'print_instance' ], 1, 2 );

			if ( \is_array( $_GET[ self::DEBUG_PARAM ] ) ) { //phpcs:ignore
				add_filter( 'advanced-sidebar-menu/menus/widget-instance', [ $this, 'adjust_widget_settings' ] );
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
		if ( empty( $_GET[ self::DEBUG_PARAM ] ) ) { //phpcs:ignore
			return $instance;
		}

		$overrides = Utils::instance()->array_map_recursive( 'sanitize_text_field', (array) $_GET[ self::DEBUG_PARAM ] ); //phpcs:ignore

		// Do not allow passing a non-public post type.
		if ( isset( $overrides['post_type'] ) ) {
			$type = get_post_type_object( $overrides['post_type'] );
			if ( $type && ! $type->public ) {
				unset( $overrides['post_type'] );
			}
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
	 *
	 * @since 9.0.2
	 *
	 * @return array
	 */
	public function get_site_info() {
		$data = [
			'basic'       => ADVANCED_SIDEBAR_MENU_BASIC_VERSION,
			'php'         => PHP_VERSION,
			'pro'         => false,
			'scriptDebug' => Scripts::instance()->is_script_debug_enabled(),
			'wordpress'   => get_bloginfo( 'version' ),
		];
		if ( defined( 'ADVANCED_SIDEBAR_MENU_PRO_VERSION' ) ) {
			$data['pro'] = ADVANCED_SIDEBAR_MENU_PRO_VERSION;
		}

		return $data;
	}


	/**
	 * Print the widget settings as a JS variable.
	 *
	 * @param Menu_Abstract $menu   - Menu class.
	 * @param Page|Category $widget - Widget class.
	 *
	 * @return void
	 */
	public function print_instance( $menu, $widget ) {
		$data = apply_filters( 'advanced-sidebar-menu/debug/print-instance', $this->get_site_info(), $menu, $widget );
		?>
		<script name="<?php echo esc_attr( static::DEBUG_PARAM ); ?>">
			if ( 'undefined' === typeof ( <?php echo esc_attr( static::DEBUG_PARAM ); ?> ) ) {
				var <?php echo esc_attr( static::DEBUG_PARAM ); ?> = <?php echo wp_json_encode( $data ); ?>;
			}
				<?php echo esc_attr( static::DEBUG_PARAM ); ?>[ '<?php echo esc_js( $menu->args['widget_id'] ); ?>' ] = <?php echo wp_json_encode( $menu->instance ); ?>;
		</script>
		<?php
	}
}
