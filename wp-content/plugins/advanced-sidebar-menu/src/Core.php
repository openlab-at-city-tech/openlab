<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;

/**
 * Core functionality for Advanced Sidebar Menu Plugin
 *
 * @author OnPoint Plugins
 */
class Core {
	use Singleton;

	const PLUGIN_FILE = 'advanced-sidebar-menu/advanced-sidebar-menu.php';


	/**
	 * Actions
	 */
	protected function hook() {
		add_action( 'widgets_init', [ $this, 'register_widgets' ] );
		add_action( 'advanced-sidebar-menu/widget/category/before-form', [ $this, 'transform_notice' ], 1 );
		add_action( 'advanced-sidebar-menu/widget/page/before-form', [ $this, 'transform_notice' ], 1 );
		add_action( 'advanced-sidebar-menu/widget/navigation-menu/before-form', [ $this, 'transform_notice' ], 1 );
		add_action( 'advanced-sidebar-menu/widget/page/after-form', [ $this, 'widget_documentation' ], 99, 2 );
		add_action( 'advanced-sidebar-menu/widget/category/after-form', [ $this, 'widget_documentation' ], 99, 2 );
		add_filter( 'plugin_action_links_' . static::PLUGIN_FILE, [ $this, 'plugin_action_links' ] );
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
	 * If no widget is specified, return the landing page for the "Usage"
	 * documentation.
	 *
	 * @since 9.0.0
	 *
	 * @param ?string $widget_id - ID of the widget.
	 *
	 * @return string
	 */
	public function get_documentation_url( $widget_id = null ) {
		if ( null === $widget_id ) {
			$url = 'https://onpointplugins.com/advanced-sidebar-menu/basic-usage/';
		} else {
			$url = Category::NAME === $widget_id ? 'https://onpointplugins.com/advanced-sidebar-menu/basic-usage/advanced-sidebar-menu-categories/' : 'https://onpointplugins.com/advanced-sidebar-menu/basic-usage/advanced-sidebar-menu-pages/';
		}

		return apply_filters( 'advanced-sidebar-menu/widget-docs/url', $url, $widget_id );
	}


	/**
	 * Add a link to the plugin's documentation to the plugin's row on the
	 * plugins page.
	 *
	 * @param array $actions - Array of actions and their link.
	 *
	 * @return array
	 */
	public function plugin_action_links( array $actions ): array {
		$actions['documentation'] = sprintf( '<a href="%1$s%2$s" target="_blank">%3$s</a>',
			$this->get_documentation_url(), '?utm_source=wp-plugins&utm_campaign=documentation&utm_medium=wp-dash', __( 'Documentation', 'advanced-sidebar-menu' ) );

		return $actions;
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


	/**
	 * Display a dismissible notice above widget forms to inform
	 * users the widget may be transformed into a block.
	 *
	 * Notice is rendered via React.
	 *
	 * @since 9.2.0
	 *
	 * @see   js/src/modules/widgets.tsx
	 *
	 * @internal
	 *
	 * @return void
	 */
	public function transform_notice() {
		?>
		<!-- <?php echo esc_html( __METHOD__ ); ?> -->
		<span data-js="advanced-sidebar-menu/transform-notice"></span>
		<?php
	}
}
