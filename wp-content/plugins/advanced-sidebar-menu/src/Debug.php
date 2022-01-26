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

			if ( is_array( $_GET[ self::DEBUG_PARAM ] ) ) { //phpcs:ignore
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
		$overrides = array_map( 'sanitize_text_field', (array) $_GET[ self::DEBUG_PARAM ] ); //phpcs:ignore

		return wp_parse_args( $overrides, $instance );
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
		$data = [
			'version' => ADVANCED_SIDEBAR_BASIC_VERSION,
		];
		if ( defined( 'ADVANCED_SIDEBAR_MENU_PRO_VERSION' ) ) {
			$data['pro_version'] = ADVANCED_SIDEBAR_MENU_PRO_VERSION;
		}
		$data = apply_filters( 'advanced-sidebar-menu/debug/print-instance', $data, $menu, $widget );
		?>
		<script class="<?php echo esc_attr( self::DEBUG_PARAM ); ?>">
			if ( 'undefined' === typeof ( <?php echo esc_attr( self::DEBUG_PARAM ); ?> ) ) {
				var <?php echo esc_attr( self::DEBUG_PARAM ); ?> = <?php echo wp_json_encode( $data ); ?>;
			}
				<?php echo esc_attr( self::DEBUG_PARAM ); ?>[ '<?php echo esc_js( $widget->id ); ?>' ] = <?php echo wp_json_encode( $menu->instance ); ?>;
		</script>
		<?php
	}
}
