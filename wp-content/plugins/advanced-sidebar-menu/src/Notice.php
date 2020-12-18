<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page as Widget_Page;

/**
 * Various notice handling for the admin and widgets.
 *
 * @author OnPoint Plugins
 * @since  8.1.0
 */
class Notice {
	use Singleton;

	/**
	 * Actions and filters.
	 */
	public function hook() {
		add_action( 'advanced-sidebar-menu/widget/page/before-columns', [ $this, 'preview' ], 1, 2 );
		add_action( 'advanced-sidebar-menu/widget/category/before-columns', [ $this, 'preview' ], 1, 2 );
		add_action( 'advanced-sidebar-menu/widget/category/right-column', [ $this, 'info_panel' ], 1, 2 );
		add_action( 'advanced-sidebar-menu/widget/page/right-column', [ $this, 'info_panel' ], 1, 2 );

		if ( $this->is_conflicting_pro_version() ) {
			add_action( 'all_admin_notices', [ $this, 'pro_version_warning' ] );
		}
	}


	/**
	 * Is PRO active but an unsupported version?
	 *
	 * @return bool
	 */
	public function is_conflicting_pro_version() {
		return defined( 'ADVANCED_SIDEBAR_MENU_PRO_VERSION' ) && version_compare( ADVANCED_SIDEBAR_MENU_REQUIRED_PRO_VERSION, ADVANCED_SIDEBAR_MENU_PRO_VERSION, '>' );
	}


	/**
	 * Display a warning if we don't have the required PRO version installed.
	 *
	 * @param bool $no_banner - Display as "message" banner.
	 */
	public function pro_version_warning( $no_banner = false ) {
		?>
		<div class="<?php echo true === $no_banner ? '' : 'error'; ?>">
			<p>
				<?php
				/* translators: Link to PRO plugin {%1$s}[<a href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/">]{%2$s}[</a>] */
				printf( esc_html_x( 'Advanced Sidebar Menu requires %1$sAdvanced Sidebar Menu PRO%2$s version %3$s+. Please update or deactivate the PRO version.', '{<a>}{</a>}', 'advanced-sidebar-menu' ), '<a target="_blank" rel="noreferrer noopener" href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/">', '</a>', esc_attr( ADVANCED_SIDEBAR_MENU_REQUIRED_PRO_VERSION ) );
				?>
			</p>
		</div>
		<?php
	}


	/**
	 * Notify widget users about the PRO options
	 *
	 * @param array      $instance - widget instance.
	 * @param \WP_Widget $widget   - widget class.
	 *
	 * @return void
	 */
	public function info_panel( array $instance, \WP_Widget $widget ) {
		if ( $this->is_conflicting_pro_version() ) {
			?>
			<div class="advanced-sidebar-menu-column-box" style="border-color: red">
				<?php static::instance()->pro_version_warning( true ); ?>
			</div>
			<?php
		}
		if ( defined( 'ADVANCED_SIDEBAR_MENU_PRO_VERSION' ) ) {
			return;
		}

		?>
		<div class="advanced-sidebar-menu-column-box">
			<h3 style="margin: 8px 0 0 3px;">
				<a
					href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/"
					style="text-decoration: none; color: inherit;">
					<?php esc_html_e( 'Advanced Sidebar Menu PRO', 'advanced-sidebar-menu' ); ?>
				</a>
			</h3>
			<ol style="list-style: disc;">
				<li><?php esc_html_e( 'Styling options including borders, bullets, colors, backgrounds, size, and font weight.', 'advanced-sidebar-menu' ); ?></li>
				<li><?php esc_html_e( 'Accordion menus.', 'advanced-sidebar-menu' ); ?></li>
				<li><?php esc_html_e( 'Support for custom navigation menus from Appearance -> Menus.', 'advanced-sidebar-menu' ); ?></li>
				<?php
				if ( Widget_Page::NAME === $widget->id_base ) {
					?>
					<li><?php esc_html_e( 'Select and display custom post types.', 'advanced-sidebar-menu' ); ?></li>
					<?php
				} else {
					?>
					<li><?php esc_html_e( 'Select and display custom taxonomies.', 'advanced-sidebar-menu' ); ?></li>
					<?php
				}
				?>
				<li><?php esc_html_e( 'Priority support with access to members only support area.', 'advanced-sidebar-menu' ); ?></li>
				<li>
					<a
						href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/"
						target="_blank"
						style="text-decoration: none;">
						<?php esc_html_e( 'So much more...', 'advanced-sidebar-menu' ); ?>
					</a>
				</li>
			</ol>
			<a
				class="button-primary"
				style="width:100%; text-align: center; margin: 15px 0 15px 0;"
				href="https://onpointplugins.com/product/advanced-sidebar-menu-pro/?trigger_buy_now=1"
				target="_blank">
				<?php esc_html_e( 'Upgrade', 'advanced-sidebar-menu' ); ?>
			</a>
			<div
				data-js="advanced-sidebar-menu/pro/preview/trigger"
				data-target="advanced-sidebar-menu/pro/preview/<?php echo esc_attr( $widget->id ); ?>"
				class="advanced-sidebar-desktop-only">
				<?php
				if ( Widget_Page::NAME === $widget->id_base ) {
					$margin = '20px';
				} else {
					$margin = '11px';
				}
				?>
				<button
					class="button-secondary"
					style="width:100%; text-align: center; margin: 0 0 <?php echo esc_attr( $margin ); ?> 0;">
					<?php esc_html_e( 'Preview', 'advanced-sidebar-menu' ); ?>
				</button>
			</div>
		</div>
		<?php
	}


	/**
	 * Display a preview image which covers the widget when the "Preview"
	 * button is clicked.
	 *
	 * @param array      $instance - Widgets settings.
	 * @param \WP_Widget $widget   - Widget class.
	 */
	public function preview( array $instance, \WP_Widget $widget ) {
		$src = 'pages-widget-min.png?version=' . ADVANCED_SIDEBAR_BASIC_VERSION;
		if ( Category::NAME === $widget->id_base ) {
			$src = 'category-widget-min.png?version=' . ADVANCED_SIDEBAR_BASIC_VERSION;
		}
		?>
		<div
			data-js="advanced-sidebar-menu/pro/preview/<?php echo esc_attr( $widget->id ); ?>"
			class="advanced-sidebar-desktop-only advanced-sidebar-menu-full-width advanced-sidebar-menu-preview-wrap">
			<div class="dashicons dashicons-no-alt advanced-sidebar-menu-close-icon"></div>
			<img
				data-js="advanced-sidebar-menu/pro/preview/image"
				class="advanced-sidebar-menu-preview-image"
				src="https://onpointplugins.com/plugins/assets/shared/<?php echo esc_attr( $src ); ?>"
				srcset="https://onpointplugins.com/plugins/assets/shared/<?php echo esc_attr( str_replace( '-min.png', '-1x-min.png', $src ) ); ?> 1x, https://onpointplugins.com/plugins/assets/shared/<?php echo esc_attr( $src ); ?> 2x"
				alt="PRO version widget options" />
		</div>
		<?php
	}

}
