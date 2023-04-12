<?php

namespace Advanced_Sidebar_Menu;

use Advanced_Sidebar_Menu\Blocks\Block_Abstract;
use Advanced_Sidebar_Menu\Traits\Singleton;
use Advanced_Sidebar_Menu\Widget\Category;
use Advanced_Sidebar_Menu\Widget\Page;

/**
 * Scripts and styles.
 */
class Scripts {
	use Singleton;

	const ADMIN_SCRIPT = 'advanced-sidebar-menu-script';
	const ADMIN_STYLE  = 'advanced-sidebar-menu-style';

	const GUTENBERG_HANDLE     = 'advanced-sidebar-menu/gutenberg';
	const GUTENBERG_CSS_HANDLE = 'advanced-sidebar-menu/gutenberg-css';


	/**
	 * Add various scripts to the cue.
	 */
	public function hook() {
		add_action( 'init', [ $this, 'register_gutenberg_scripts' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );

		// Elementor support.
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'register_gutenberg_scripts' ] );
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
	 * Register Gutenberg block scripts.
	 *
	 * We register instead of enqueue so Gutenberg will load them
	 * within the iframes of areas such as FSE.
	 *
	 * The actual script/style loading is done via `register_block_type`
	 * using 'editor_script' and 'editor_style.
	 *
	 * @action init 10 0
	 *
	 * @notice Must be run before `get_block_editor_settings` is
	 *         called to allow styles to be included in the Site
	 *         Editor iframe.
	 *
	 * @see    Block_Abstract::register()
	 *
	 * @link   https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#wpdefinedasset
	 *
	 * @since  9.0.0
	 *
	 * @return void
	 */
	public function register_gutenberg_scripts() {
		$js_dir = apply_filters( 'advanced-sidebar-menu/js-dir', ADVANCED_SIDEBAR_MENU_URL . 'js/dist/' );
		$file = $this->is_script_debug_enabled() ? 'admin' : 'admin.min';

		wp_register_script( static::GUTENBERG_HANDLE, "{$js_dir}{$file}.js", [
			'jquery',
			'react',
			'react-dom',
			'wp-url',
		], ADVANCED_SIDEBAR_MENU_BASIC_VERSION, true );

		// Must register here because used as a dependency of the Gutenberg styles.
		wp_register_style( static::ADMIN_STYLE, trailingslashit( (string) ADVANCED_SIDEBAR_MENU_URL ) . 'resources/css/advanced-sidebar-menu.css', [], ADVANCED_SIDEBAR_MENU_BASIC_VERSION );

		if ( ! $this->is_webpack_enabled() ) {
			wp_register_style( static::GUTENBERG_CSS_HANDLE, "{$js_dir}{$file}.css", [
				static::ADMIN_STYLE,
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
	 * @action admin_enqueue_scripts 10 0
	 *
	 * @return void
	 */
	public function admin_scripts() {
		wp_enqueue_script(
			static::ADMIN_SCRIPT,
			trailingslashit( (string) ADVANCED_SIDEBAR_MENU_URL ) . 'resources/js/advanced-sidebar-menu.js',
			[ 'jquery' ],
			ADVANCED_SIDEBAR_MENU_BASIC_VERSION,
			false
		);

		wp_enqueue_style( static::ADMIN_STYLE );
	}


	/**
	 * Is SCRIPT_DEBUG enabled or passed via URL argument.
	 *
	 * @since 9.0.0
	 *
	 * @return bool
	 */
	public function is_script_debug_enabled() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return ( \defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || ! empty( $_GET['script-debug'] );
	}


	/**
	 * Are we currently developing locally with Webpack enabled?
	 *
	 * Provides a consistent interface for determining considerations
	 * when Webpack is enabled.
	 *
	 * @since 9.0.0.
	 *
	 * @return bool
	 */
	public function is_webpack_enabled() {
		return SCRIPT_DEBUG && has_filter( 'advanced-sidebar-menu/js-dir' );
	}


	/**
	 * Configuration passed from PHP to JavaScript.
	 *
	 * @return array
	 */
	public function js_config() {
		return apply_filters( 'advanced-sidebar-menu/scripts/js-config', [
			'categories'    => [
				'displayEach' => Category::get_display_each_options(),
			],
			'currentScreen' => is_admin() ? get_current_screen()->base : '',
			'docs'          => [
				'page'     => Core::instance()->get_documentation_url( Page::NAME ),
				'category' => Core::instance()->get_documentation_url( Category::NAME ),
			],
			'error'         => apply_filters( 'advanced-sidebar-menu/scripts/js-config/error', '' ),
			'features'      => Notice::instance()->get_features(),
			'isPostEdit'    => ! empty( $GLOBALS['pagenow'] ) && 'post.php' === $GLOBALS['pagenow'],
			'isPro'         => false,
			'isWidgets'     => ! empty( $GLOBALS['pagenow'] ) && 'widgets.php' === $GLOBALS['pagenow'],
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
			if ( typeof ( advanced_sidebar_menu ) !== 'undefined' ) {
				advanced_sidebar_menu.init();
			}
		</script>
		<?php
	}

}
