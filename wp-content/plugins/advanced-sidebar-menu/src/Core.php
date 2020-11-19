<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;

/**
 * Core functionality for Advanced Sidebar Menu Plugin
 *
 * @author OnPoint Plugins
 * @since  7.0.0
 */
class Core {
	use Singleton;

	/**
	 * Actions
	 */
	protected function hook() {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
	}


	/**
	 * Register the page and category widgets.
	 *
	 * @return void
	 */
	public function register_widgets() {
		register_widget( Page::class );
		register_widget( Category::class );
	}


	/**
	 * Retrieve a template file from either the theme's 'advanced-sidebar-menu' directory
	 * or this plugin's view folder if one does not exist.
	 *
	 * @param string $file_name - Name of template file without the PHP extension.
	 *
	 * @since 6.0.0
	 *
	 * @return string
	 */
	public function get_template_part( $file_name ) {
		$file = locate_template( 'advanced-sidebar-menu/' . $file_name );
		if ( empty( $file ) ) {
			?>
			<!-- advanced-sidebar-menu/core-template -->
			<?php
			$file = ADVANCED_SIDEBAR_DIR . 'views/' . $file_name;
		} else {
			?>
			<!-- advanced-sidebar-menu/template-override -->
			<?php
		}

		return apply_filters( 'advanced-sidebar-menu/core/get-template-part', $file, $file_name, $this );
	}
}
