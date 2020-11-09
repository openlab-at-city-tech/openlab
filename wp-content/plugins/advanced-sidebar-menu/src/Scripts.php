<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;

/**
 * Scripts and styles.
 *
 * @author Mat Lipe
 * @since  7.7.0
 */
class Scripts {
	use Singleton;

	/**
	 * Add various scripts to the cue.
	 */
	public function hook() {
		add_action( 'admin_print_scripts', [ $this, 'admin_scripts' ] );
		// Elementor support.
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'admin_scripts' ] );
		// UGH! Beaver Builder hack.
		if ( isset( $_GET['fl_builder'] ) ) { // phpcs:ignore
			add_action( 'wp_enqueue_scripts', [ $this, 'admin_scripts' ] );
		}

		add_action( 'advanced-sidebar-menu/widget/category/after-form', [ $this, 'init_widget_js' ], 1000 );
		add_action( 'advanced-sidebar-menu/widget/page/after-form', [ $this, 'init_widget_js' ], 1000 );
		add_action( 'advanced-sidebar-menu/widget/navigation-menu/after-form', [ $this, 'init_widget_js' ], 1000 );
	}


	/**
	 * Add js and css to the admin and in specific cases the front-end.
	 *
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_script(
			'advanced-sidebar-menu-script',
			\trailingslashit( ADVANCED_SIDEBAR_MENU_URL ) . 'resources/js/advanced-sidebar-menu.js',
			[ 'jquery' ],
			ADVANCED_SIDEBAR_BASIC_VERSION,
			false
		);

		wp_enqueue_style(
			'advanced-sidebar-menu-style',
			\trailingslashit( ADVANCED_SIDEBAR_MENU_URL ) . 'resources/css/advanced-sidebar-menu.css',
			[],
			ADVANCED_SIDEBAR_BASIC_VERSION
		);
	}


	/**
	 * Trigger any JS needed by the widgets.
	 * This is outputted into the markup for each widget, so it may be
	 * trigger whether the widget is loaded on the front-end by
	 * page builders or the backend by standard WordPress or
	 * really anywhere.
	 *
	 * @return void
	 */
	public function init_widget_js() {
		if ( WP_DEBUG ) {
			?>
			<!-- <?php echo __FILE__; ?>-->
			<?php
		}
		?>
		<script>
			if ( typeof ( advanced_sidebar_menu ) !== 'undefined' ) {
				advanced_sidebar_menu.init();
			}
		</script>
		<?php
	}

}
