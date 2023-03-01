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

	const PLUGIN_FILE = 'advanced-sidebar-menu/advanced-sidebar-menu.php';


	/**
	 * Actions
	 */
	protected function hook() {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
		add_action( 'advanced-sidebar-menu/widget/page/after-form', [ $this, 'widget_documentation' ], 99, 2 );
		add_action( 'advanced-sidebar-menu/widget/category/after-form', [ $this, 'widget_documentation' ], 99, 2 );
	}


	/**
	 * Display a link to a widget's documentation.
	 *
	 * @param array      $_      - Widget settings.
	 * @param \WP_Widget $widget - Widget class.
	 *
	 * @since 9.0.0
	 *
	 * @return void
	 */
	public function widget_documentation( $_, \WP_Widget $widget ) {
		?>
		<p class="advanced-sidebar-widget-documentation">
			<a
				href="<?php echo esc_url( $this->get_documentation_url( $widget->id_base ) ); ?>"
				target="_blank"
				rel="noopener noreferrer"
			>
				<?php esc_html_e( 'widget documentation', 'advanced-sidebar-menu' ); ?>
			</a>
		</p>
		<?php
	}


	/**
	 * Get the URL of a widget's documentation.
	 *
	 * @param string $widget_id - ID of the widget.
	 *
	 * @since 9.0.0
	 *
	 * @return string
	 */
	public function get_documentation_url( $widget_id ) {
		$url = Category::NAME === $widget_id ? 'https://onpointplugins.com/advanced-sidebar-menu/basic-usage/advanced-sidebar-menu-categories/' : 'https://onpointplugins.com/advanced-sidebar-menu/basic-usage/advanced-sidebar-menu-pages/';

		return apply_filters( 'advanced-sidebar-menu/widget-docs/url', $url, $widget_id );
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
		$comments = apply_filters( 'advanced-sidebar-menu/core/include-template-parts-comments', true, $file_name );
		if ( empty( $file ) ) {
			if ( $comments ) {
				?>
				<!-- advanced-sidebar-menu/core-template -->
				<?php
			}
			$file = ADVANCED_SIDEBAR_MENU_DIR . 'views/' . $file_name;
		} elseif ( $comments ) {
			?>
			<!-- advanced-sidebar-menu/template-override -->
			<?php
		}

		return apply_filters( 'advanced-sidebar-menu/core/get-template-part', $file, $file_name, $this );
	}
}
