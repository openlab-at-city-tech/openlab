<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Blocks\Block_Abstract;
use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;

/**
 * Scripts and styles.
 *
 * @phpstan-type JS_CONFIG array{
 *     categories: array{
 *          displayEach: array<string, string>
 *     },
 *     currentScreen: string,
 *     docs: array{
 *          page: string,
 *          category: string
 *     },
 *     error: string,
 *     features: array<string, bool>,
 *     isPostEdit: bool,
 *     isPro: bool,
 *     isWidgets: bool,
 *     pages: array{
 *          orderBy: array<string, string>
 *     },
 *     siteInfo: array{
 *          basic: string,
 *          classicWidgets: bool,
 *          php: string,
 *          pro: string|false,
 *          scriptDebug: bool,
 *          WordPress: string,
 *          pro?: string
 *     },
 *     support: string
 * }
 */
class Scripts {
	use Singleton;

	public const ADMIN_SCRIPT = 'advanced-sidebar-menu-script';
	public const ADMIN_STYLE  = 'advanced-sidebar-menu-style';

	public const GUTENBERG_HANDLE     = 'advanced-sidebar-menu/gutenberg';
	public const GUTENBERG_CSS_HANDLE = 'advanced-sidebar-menu/gutenberg-css';

	public const FILE_BLOCK_EDITOR = 'advanced-sidebar-menu-block-editor';
	public const FILE_ADMIN        = 'advanced-sidebar-menu-admin';
	public const FILE_DEBUG        = 'advanced-sidebar-menu-debug';


	/**
	 * Add various scripts to the queue.
	 */
	public function hook(): void {
		add_action( 'init', [ $this, 'register_gutenberg_scripts' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'use_development_version_of_react' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 11 );

		// Elementor support.
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'register_gutenberg_scripts' ] );
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'admin_scripts' ] );

		// Beaver Builder support.
		add_action( 'fl_builder_ui_enqueue_scripts', [ $this, 'admin_scripts' ] );

		add_action( 'advanced-sidebar-menu/widget/category/after-form', [ $this, 'init_widget_js' ], 1000 );
		add_action( 'advanced-sidebar-menu/widget/page/after-form', [ $this, 'init_widget_js' ], 1000 );
		add_action( 'advanced-sidebar-menu/widget/navigation-menu/after-form', [ $this, 'init_widget_js' ], 1000 );
	}


	/**
	 * Register Gutenberg block scripts.
	 *
	 * We register instead of enqueue so Gutenberg will load them
	 * within the iframes of areas such as FSE.
	 *
	 * The actual script/style loading is done via `register_block_type`
	 * using 'editor_script' and 'editor_style'.
	 *
	 * @action init 10 0
	 *
	 * @notice Must be run before `get_block_editor_settings` is
	 *         called to allow styles to be included in the Site
	 *         Editor iframe.
	 *
	 * @since  9.0.0
	 *
	 * @see    Block_Abstract::register()
	 *
	 * @link   https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#wpdefinedasset
	 *
	 * @return void
	 */
	public function register_gutenberg_scripts() {
		wp_register_script( static::GUTENBERG_HANDLE, $this->get_dist_file( self::FILE_BLOCK_EDITOR, 'js' ), [
			'jquery',
			'lodash',
			'react',
			'react-dom',
			'wp-block-editor',
			'wp-blocks',
			'wp-components',
			'wp-data',
			'wp-hooks',
			'wp-html-entities',
			'wp-i18n',
			'wp-server-side-render',
			'wp-url',
		], ADVANCED_SIDEBAR_MENU_BASIC_VERSION, true );

		if ( ! $this->is_webpack_enabled() ) {
			wp_register_style( static::GUTENBERG_CSS_HANDLE, $this->get_dist_file( self::FILE_BLOCK_EDITOR, 'css' ), [
				'dashicons',
			], ADVANCED_SIDEBAR_MENU_BASIC_VERSION );
		}

		wp_set_script_translations( static::GUTENBERG_HANDLE, 'advanced-sidebar-menu', ADVANCED_SIDEBAR_MENU_DIR . 'languages' );

		/**
		 * Load separately because `$this->js_config()` is heavy, and
		 * the block scripts must be registered before we have
		 * access to `wp_should_load_block_editor_scripts_and_styles`.
		 */
		add_action( 'enqueue_block_editor_assets', function() {
			wp_localize_script( self::GUTENBERG_HANDLE, 'ADVANCED_SIDEBAR_MENU', $this->js_config() );
		}, 1 );
	}


	/**
	 * Add JS and CSS to the admin and in specific cases the front-end.
	 *
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_script( static::ADMIN_SCRIPT, $this->get_dist_file( self::FILE_ADMIN, 'js' ), [
			'jquery',
		], ADVANCED_SIDEBAR_MENU_BASIC_VERSION, false );
		if ( ! $this->is_webpack_enabled() ) {
			wp_enqueue_style( static::ADMIN_STYLE, $this->get_dist_file( self::FILE_ADMIN, 'css' ), [], ADVANCED_SIDEBAR_MENU_BASIC_VERSION );
		}
		/**
		 * Fire action when admin scripts are being loaded.
		 * Simply loading additional scripts such as "runtime" in any context
		 * the admin scripts are used.
		 *
		 * @since 9.3.1
		 *
		 * @param Scripts $scripts - The Scripts instance.
		 */
		do_action( 'advanced-sidebar-menu/scripts/admin-scripts', $this );
	}


	/**
	 * Is SCRIPT_DEBUG enabled or passed via URL argument.
	 *
	 * @since 9.0.0
	 *
	 * @return bool
	 */
	public function is_script_debug_enabled() {
		if ( SCRIPT_DEBUG ) {
			return true;
		}
		//phpcs:ignore -- Not using nonce because users enter manually in url.
		if ( isset( $_GET['script-debug'] ) && 'true' === $_GET['script-debug'] ) {
			return true;
		}
		return false;
	}


	/**
	 * Are we currently developing locally with Webpack enabled?
	 *
	 * Provides a consistent interface for determining considerations
	 * when Webpack is enabled.
	 *
	 * @since 9.0.0
	 *
	 * @return bool
	 */
	public function is_webpack_enabled() {
		return SCRIPT_DEBUG && file_exists( ADVANCED_SIDEBAR_MENU_DIR . '/js/dist/.running' );
	}


	/**
	 * Use the development version of React to improve our ErrorBoundary data.
	 *
	 * Provides more useful debugging information.
	 *
	 * - Do not change if we are already on SCRIPT_DEBUG.
	 * - Do not change if our custom `$_GET['script-debug']` is not available.
	 * - Only available in the context of Gutenberg.
	 *
	 * @since 9.0.10
	 *
	 * @return void
	 */
	public function use_development_version_of_react() {
		if ( SCRIPT_DEBUG || ! $this->is_script_debug_enabled() ) {
			return;
		}

		$script = wp_scripts()->query( 'react', 'scripts' );
		if ( ! \is_bool( $script ) && is_a( $script, \_WP_Dependency::class ) ) {
			$script->src = \str_replace( wp_scripts_get_suffix(), '', (string) $script->src );
		}
		$script = wp_scripts()->query( 'react-dom', 'scripts' );
		if ( ! \is_bool( $script ) && is_a( $script, \_WP_Dependency::class ) ) {
			$script->src = \str_replace( wp_scripts_get_suffix(), '', (string) $script->src );
		}
	}


	/**
	 * Configuration passed from PHP to JavaScript.
	 *
	 * @return JS_CONFIG
	 */
	public function js_config(): array {
		return apply_filters( 'advanced-sidebar-menu/scripts/js-config', [
			'categories'    => [
				'displayEach' => Category::get_display_each_options(),
			],
			'currentScreen' => is_admin() && \function_exists( 'get_current_screen' ) ? get_current_screen()->base ?? '' : '',
			'docs'          => [
				'page'     => Core::instance()->get_documentation_url( Page::NAME ),
				'category' => Core::instance()->get_documentation_url( Category::NAME ),
			],
			'error'         => apply_filters( 'advanced-sidebar-menu/scripts/js-config/error', '' ),
			'features'      => Notice::instance()->get_features(),
			'isPostEdit'    => isset( $GLOBALS['pagenow'] ) && 'post.php' === $GLOBALS['pagenow'],
			'isPro'         => false,
			'isWidgets'     => isset( $GLOBALS['pagenow'] ) && 'widgets.php' === $GLOBALS['pagenow'],
			'pages'         => [
				'orderBy' => Page::get_order_by_options(),
			],
			'siteInfo'      => Debug::instance()->get_site_info(),
			'support'       => 'https://wordpress.org/support/plugin/advanced-sidebar-menu/#new-topic-0',
		] );
	}


	/**
	 * Trigger any JS needed by the widgets.
	 * This is outputted into the markup for each widget, so it may be
	 * trigger whether the widget is loaded on the front-end by
	 * page builders or the backend by standard WordPress or
	 * really anywhere.
	 *
	 * @notice Does not work in Gutenberg as widget's markup is loaded
	 *         via the REST API and React.
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
			if ( typeof ( window.advancedSidebarMenuAdmin ) !== 'undefined' ) {
				window.advancedSidebarMenuAdmin.init();
			}
		</script>
		<?php
	}


	/**
	 * Translate a file slug to its location based on the current context.
	 *
	 * @since    9.2.2
	 *
	 * @phpstan-param self::FILE_* $file_slug
	 * @phpstan-param 'js'|'css'   $extension
	 *
	 * @param string               $file_slug - The file slug.
	 * @param string               $extension - The file extension.
	 *
	 * @return string
	 */
	public function get_dist_file( string $file_slug, string $extension ): string {
		$js_dir = apply_filters( 'advanced-sidebar-menu/js-dir', ADVANCED_SIDEBAR_MENU_URL . 'js/dist/' );
		$file_slug = $this->is_script_debug_enabled() ? $file_slug : "{$file_slug}.min";
		return "{$js_dir}{$file_slug}.{$extension}";
	}
}
