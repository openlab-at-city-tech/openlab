<?php

class M_NextGen_Admin extends C_Base_Module {

	public $object;

	/**
	 * Defines the module
	 */
	public function define(
		$id = 'pope-module',
		$name = 'Pope Module',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = false
	) {
		parent::define(
			'photocrati-nextgen_admin',
			'NextGEN Administration',
			'Provides a framework for adding Administration pages',
			'3.7.0',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);

		\Imagely\NGG\Util\Installer::add_handler( $this->module_id, 'C_NextGen_Admin_Installer' );

		\Imagely\NGG\Settings\Settings::get_instance()->add_option_handler(
			'C_NextGen_Admin_Option_Handler',
			[
				'jquery_ui_theme_url',
			]
		);

		if ( is_multisite() ) {
			\Imagely\NGG\Settings\GlobalSettings::get_instance()->add_option_handler(
				'C_NextGen_Admin_Option_Handler',
				[
					'jquery_ui_theme_url',
				]
			);
		}
	}

	/**
	 * Register utilities necessary for this module (and the plugin)
	 */
	public function _register_utilities() {
		$this->get_registry()->add_utility( 'I_NextGen_Admin_Page', 'C_NextGen_Admin_Page_Controller' );
		$this->get_registry()->add_utility( 'I_Page_Manager', 'C_NextGen_Admin_Page_Manager' );
		$this->get_registry()->add_utility( 'I_Form', 'C_Form' );
	}

	/**
	 * Registers adapters required by this module
	 */
	public function _register_adapters() {
		$this->get_registry()->add_adapter( 'I_MVC_Controller', 'A_MVC_Validation' );
	}

	public function get_common_admin_css_handlers( $array ) {
		$array['nextgen_admin_css'] = $this->module_version;
		return $array;
	}

	public function get_common_admin_js_handlers( $array ) {
		$array['nextgen_admin_js'] = $this->module_version;
		return $array;
	}

	// Enqueues static resources that should be enqueued in the HEADER on a NextGEN Admin Page.
	public static function enqueue_common_admin_static_resources( $footer = false ) {
		$footer                = ( $footer === true );
		$script_handles_filter = $footer ? 'ngg_admin_footer_script_handles' : 'ngg_admin_script_handles';
		$style_handles_filter  = 'ngg_admin_style_handles';
		$enqueue_action        = $footer ? 'ngg_admin_footer_enqueue_scripts' : 'ngg_admin_enqueue_scripts';

		if ( $slug = C_NextGen_Admin_Page_Manager::is_requested() ) {
			$script_handles = apply_filters( $script_handles_filter, [], $slug );
			$style_handles  = apply_filters( $style_handles_filter, [], $slug );

			foreach ( $script_handles as $handle => $version ) {
				$hook = "ngg_enqueue_{$handle}_script";
				if ( has_action( $hook ) ) {
					do_action( $hook, $handle, $version );
				} else {
					wp_enqueue_script( $handle, '', [], $version );
				}
			}

			if ( ! $footer ) {
				foreach ( $style_handles as $handle => $version ) {
					$hook = "ngg_enqueue_{$handle}_style";
					if ( has_action( $hook ) ) {
						do_action( $hook, $handle, $version );
					} else {
						wp_enqueue_style( $handle, '', [], $version );
					}
				}
			}

			// Expose a means for other modules or third-party plugins to provide their own
			// enqueue calls.
			do_action( $enqueue_action, $slug );
		}

		// Have the toplevel "NextGEN Gallery" link to the Manage Galleries page.
		wp_add_inline_script(
			'common',
			"jQuery(function($){
            var parent = $('.toplevel_page_nextgen-gallery');
            var manageGalleryUrl = parent.find('a[href*=\"manage-gallery\"]').attr('href');
            parent.attr('href', manageGalleryUrl);
        })"
		);
	}

	// Enqueues static resources that should be enqueued in the FOOTER on a NextGEN Admin Page.
	public function enqueue_common_admin_footer_static_resources() {
		self::enqueue_common_admin_static_resources( true );
	}

	/**
	 * Hooks into the WordPress Framework
	 */
	public function _register_hooks() {
		// Register scripts.
		add_action( 'init', [ $this, 'register_scripts' ], 9 );

		// Elementor's editor.php runs `new \WP_Scripts()` which requires we register scripts on both init and this
		// action if we want our resources to be used with the page builder.
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_scripts' ] );

		// Enqueue common static resources for NGG admin pages.
		add_filter( 'ngg_admin_style_handles', [ $this, 'get_common_admin_css_handlers' ] );
		add_filter( 'ngg_admin_script_handles', [ $this, 'get_common_admin_js_handlers' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_common_admin_static_resources' ] );
		add_action( 'admin_footer_print_scripts', [ $this, 'enqueue_common_admin_footer_static_resources' ] );

		// Provides menu options for managing NextGEN Settings.
		add_action( 'admin_menu', [ $this, 'add_menu_pages' ], 999 );

		// Add ngg-admin body class to all admin pages for styling.
		add_filter( 'admin_body_class', [ $this, 'add_ngg_body_class' ] );

		// Custom post types.
		add_filter( 'admin_body_class', [ $this, 'add_ngg_post_type_class' ] );
		add_filter( 'screen_options_show_screen', [ $this, 'remove_post_type_screen_options' ] );
		add_action( 'all_admin_notices', [ $this, 'custom_post_type_markup_top' ], 11 );
		add_action( 'admin_footer', [ $this, 'custom_post_type_markup_bottom' ] );

		// Requirements need to be registered with the notification manager *before* it's serve_ajax_request().
		add_action(
			'after_setup_theme',
			function () {
				add_action( 'admin_init', [ \Imagely\NGG\Admin\RequirementsManager::get_instance(), 'create_notification' ], - 10 );
			}
		);

		// Define routes.
		add_action( 'ngg_routes', [ $this, 'define_routes' ] );

		// Provides admin notices.
		$notices = \Imagely\NGG\Admin\Notifications\Manager::get_instance();
		add_action( 'admin_footer', [ $notices, 'enqueue_scripts' ] );
		add_action( 'do_ngg_notices', [ $notices, 'render' ] );
		add_action( 'ngg_created_new_gallery', [ $this, 'set_review_notice_flag' ] );
		add_action( 'ngg_created_new_gallery', 'M_NextGEN_Admin::update_gallery_count_setting' );
		add_action( 'ngg_delete_gallery', 'M_NextGEN_Admin::update_gallery_count_setting' );

		if ( ! self::is_ngg_legacy_page() ) {
			add_action( 'all_admin_notices', 'M_NextGEN_Admin::emit_do_notices_action' );
		}

		if ( defined( 'PHP_VERSION_ID' ) ) {
			$php_id = PHP_VERSION_ID;
		} else {
			$version = explode( '.', PHP_VERSION );
			$php_id  = ( $version[0] * 10000 + $version[1] * 100 + $version[2] );
		}

		if ( $php_id < 50300 ) {
			$notices->add(
				'ngg_php52_deprecation',
				[ 'message' => __( 'PHP 5.2 will be deprecated in a future version of NextGEN. Please upgrade your PHP installation to 5.3 or above.', 'nggallery' ) ]
			);
		}

		// Add review notices.
		$review_notice_1 = new \Imagely\NGG\Admin\Notifications\Review(
			[
				'name'    => 'review_level_1',
				'range'   => [
					'min' => 3,
					'max' => 8,
				],
				'follows' => '',
			]
		);
		$review_notice_2 = new \Imagely\NGG\Admin\Notifications\Review(
			[
				'name'    => 'review_level_2',
				'range'   => [
					'min' => 10,
					'max' => 18,
				],
				'follows' => &$review_notice_1,
			]
		);
		$review_notice_3 = new \Imagely\NGG\Admin\Notifications\Review(
			[
				'name'    => 'review_level_3',
				'range'   => [
					'min' => 20,
					'max' => PHP_INT_MAX,
				],
				'follows' => &$review_notice_2,
			]
		);
		$notices->add( $review_notice_1->get_name(), $review_notice_1 );
		$notices->add( $review_notice_2->get_name(), $review_notice_2 );
		$notices->add( $review_notice_3->get_name(), $review_notice_3 );

		$notices->add( 'nextgen_first_run_notification', 'C_NextGen_First_Run_Notification_Wizard' );

		$notices->add( 'mailchimp_opt_in', 'C_Mailchimp_OptIn_Notice' );
	}

	/**
	 * Used to determine if the current request is for a NGG legacy page
	 */
	public static function is_ngg_legacy_page(): bool {
		// This method only inspects the URL and returns a bool. Nonce verification is done in the methods that
		// make use of this method.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return ( is_admin()
				&& isset( $_REQUEST['page'] )
				&& in_array(
					sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ),
					[
						'nggallery-manage-gallery',
						'nggallery-manage-album',
						'nggallery-tags',
						'manage-galleries',
					]
				)
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Emits the 'do_ngg_notices' action
	 * Used by the notification manager to render all notices
	 */
	static function emit_do_notices_action() {
		if ( ! did_action( 'do_ngg_notices' ) ) {
			do_action( 'do_ngg_notices' );
		}
	}

	/**
	 * We do not want to suddenly ask users for a review when they have upgraded. Instead we will wait for a new
	 * gallery to be created and then will we also consider displaying reviews if the gallery count is within range.
	 */
	public function set_review_notice_flag() {
		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		if ( ! $settings->get( 'gallery_created_after_reviews_introduced' ) ) {
			$settings->set( 'gallery_created_after_reviews_introduced', true );
		}
		$settings->save();
	}

	/**
	 * Review notifications are pegged to run only when the current gallery count is within a certain range. This
	 * updates the 'gallery_count' setting when galleries have been created or deleted.
	 */
	public static function update_gallery_count_setting() {
		$settings               = \Imagely\NGG\Settings\Settings::get_instance();
		$mapper                 = \Imagely\NGG\DataMappers\Gallery::get_instance();
		$original_cache_setting = $mapper->use_cache;
		$mapper->use_cache      = false;
		$gallery_count          = \Imagely\NGG\DataMappers\Gallery::get_instance()->count();
		$mapper->use_cache      = $original_cache_setting;
		$settings->set( 'gallery_count', $gallery_count );
		$settings->save();
		return $gallery_count;
	}

	public function define_routes( $router ) {
		// TODO: Why is this in the nextgen-admin module? Shouldn't it be in the other options module?
		$router->create_app( '/nextgen-settings' )
			->route( '/update_watermark_preview', 'I_Settings_Manager_Controller#watermark_update' );
	}

	public function register_scripts() {
		$router = \Imagely\NGG\Util\Router::get_instance();
		wp_register_script(
			'gritter',
			$router->get_static_url( 'photocrati-nextgen_admin#gritter/gritter.js' ),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION
		);
		wp_register_style(
			'gritter',
			$router->get_static_url( 'photocrati-nextgen_admin#gritter/css/gritter.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
		wp_register_script(
			'ngg_progressbar',
			$router->get_static_url( 'photocrati-nextgen_admin#ngg_progressbar.js' ),
			[ 'gritter' ],
			NGG_SCRIPT_VERSION
		);
		wp_register_style(
			'ngg_progressbar',
			$router->get_static_url( 'photocrati-nextgen_admin#ngg_progressbar.css' ),
			[ 'gritter' ],
			NGG_SCRIPT_VERSION
		);
		wp_register_style(
			'ngg_select2',
			$router->get_static_url( 'photocrati-nextgen_admin#select2-4.0.13/dist/css/select2.css' ),
			[],
			NGG_SCRIPT_VERSION
		);
		wp_register_script(
			'ngg_select2',
			$router->get_static_url( 'photocrati-nextgen_admin#select2-4.0.13/dist/js/select2.full.js' ),
			[],
			NGG_SCRIPT_VERSION
		);
		wp_register_script(
			'jquery.nextgen_radio_toggle',
			$router->get_static_url( 'photocrati-nextgen_admin#jquery.nextgen_radio_toggle.js' ),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION
		);
		wp_register_style(
			'ngg-jquery-ui',
			$router->get_static_url( 'photocrati-nextgen_admin#jquery-ui/jquery-ui-1.10.4.custom.css' ),
			[],
			'1.10.4'
		);

		wp_register_style(
			'nextgen_admin_css',
			$router->get_static_url( 'photocrati-nextgen_admin#nextgen_admin_page.css' ),
			[ 'wp-color-picker' ],
			NGG_SCRIPT_VERSION
		);

		wp_register_script(
			'nextgen_admin_js',
			$router->get_static_url( 'photocrati-nextgen_admin#nextgen_admin_page.js' ),
			[ 'wp-color-picker', 'jquery-ui-widget' ],
			NGG_SCRIPT_VERSION
		);

		// Style the parent menu icons for NextGEN-related pages.
		wp_add_inline_style(
			'wp-admin',
			'
            #adminmenu li.toplevel_page_nextgen-gallery img,
            #adminmenu li[class*=toplevel_page_nextgen-gallery] img,
            #adminmenu li[class*=toplevel_page_ngg] img {
                opacity: 1;
                max-width: 18px;
                padding-top: 7px;
            }
        '
		);
	}

	/**
	 * Adds a common body class to all admin pages
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public function add_ngg_body_class( $classes ) {
		return C_NextGen_Admin_Page_Manager::is_requested() ? "$classes ngg-admin" : $classes;
	}

	/**
	 * Adds a common body class to all NGG post types pages
	 *
	 * @param $classes
	 *
	 * @return string
	 */
	public function add_ngg_post_type_class( $classes ) {
		return C_NextGen_Admin_Page_Manager::is_requested_post_type() ? "$classes ngg-post-type" : $classes;
	}

	public function remove_post_type_screen_options( $show ) {
		return C_NextGen_Admin_Page_Manager::is_requested_post_type() ? false : $show;
	}

	/* Add common admin markup to top of custom post type pages */
	public function custom_post_type_markup_top() {
		global $title;
		if ( C_NextGen_Admin_Page_Manager::is_requested_post_type() && ! $this->is_ngg_post_type_with_template() ) {
			echo '<div id="ngg_page_content"><div class="ngg_page_content_header "><h3>' . $title . '</h3></div><div class="ngg_page_content_main">';
		}
	}

	/* Add common admin markup to bottom of custom post type pages */
	public function custom_post_type_markup_bottom() {
		if ( C_NextGen_Admin_Page_Manager::is_requested_post_type() && ! $this->is_ngg_post_type_with_template() ) {
			echo '</div></div>';
		}
	}

	/* Conditional returns true if is post type and uses custom template */
	public function is_ngg_post_type_with_template() {
		$url = $_SERVER['REQUEST_URI'];
		if ( C_NextGen_Admin_Page_Manager::is_requested_post_type() && strpos( $url, '&ngg_edit' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Determine if block editor is in use
	 */
	public static function is_block_editor(): bool {
		$is_block_editor = false;
		if ( function_exists( 'get_current_screen' ) ) {
			$current_screen = get_current_screen();
			if ( is_object( $current_screen ) ) {
				if ( method_exists( $current_screen, 'is_block_editor' ) ) {
					$is_block_editor = $current_screen->is_block_editor();
				}

				if ( ! $is_block_editor ) {
					if ( $current_screen->action == 'add' ) {
						$is_block_editor = function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( $current_screen->post_type );
					} else {
						$is_block_editor = function_exists( 'use_block_editor_for_post' ) && use_block_editor_for_post( null );
					}
				}

				if ( ! $is_block_editor ) {
					// This method only determines if the block editor or classic editor are being used. Nonce
					// verification is the responsibility of methods that invoke this method.
					//
					// phpcs:disable WordPress.Security.NonceVerification.Recommended
					if ( $current_screen->action == 'add' ) {
						$is_block_editor = ! isset( $_GET['classic-editor'] ) && function_exists( 'gutenberg_can_edit_post_type' ) && gutenberg_can_edit_post_type( $current_screen->post_type );
					} else {
						$is_block_editor = ! isset( $_GET['classic-editor'] ) && function_exists( 'gutenberg_can_edit_post' ) && gutenberg_can_edit_post( null );
					}
					// phpcs:enable WordPress.Security.NonceVerification.Recommended
				}
			}
		}

		if ( ! $is_block_editor ) {
			$is_block_editor = function_exists( 'is_gutenberg_page' ) && is_gutenberg_page();
		}

		return $is_block_editor;
	}

	/**
	 * Adds menu pages to manage NextGen Settings
	 *
	 * @uses action: admin_menu
	 */
	public function add_menu_pages() {
		C_NextGen_Admin_Page_Manager::get_instance()->setup();
	}

	public function get_type_list() {
		return [
			'A_MVC_Validation'                        => 'adapter.mvc_validation.php',
			'A_Nextgen_Settings_Routes'               => 'adapter.nextgen_settings_routes.php',
			'C_Admin_Notification_Manager'            => 'class.admin_notification_manager.php',
			'C_Form'                                  => 'class.form.php',
			'C_Mailchimp_OptIn_Notice'                => 'class.mailchimp_optin_notice.php',
			'C_NextGen_Admin_Page_Manager'            => 'class.nextgen_admin_page_manager.php',
			'C_NextGen_First_Run_Notification_Wizard' => 'class.nextgen_first_run_notification_wizard.php',
			'C_Nextgen_Admin_Installer'               => 'class.nextgen_admin_installer.php',
			'C_Nextgen_Admin_Page_Controller'         => 'class.nextgen_admin_page_controller.php',
		];
	}
}

class C_NextGen_Admin_Installer {

	public function install() {
		$settings = \Imagely\NGG\Settings\Settings::get_instance();

		// In version 0.2 of this module and earlier, the following values
		// were statically set rather than dynamically using a handler. Therefore, we need
		// to delete those static values.
		$module_name = 'photocrati-nextgen_admin';
		$modules     = get_option( 'pope_module_list', [] );
		if ( ! $modules ) {
			$modules = $settings->get( 'pope_module_list', [] );
		}

		$cleanup = false;
		foreach ( $modules as $module ) {
			if ( strpos( $module, $module_name ) !== false ) {
				// Leave $module as-is: inside version_compare() will warn about passing items by reference.
				$module = explode( '|', $module );
				$val    = array_pop( $module );
				if ( version_compare( $val, '0.3' ) == -1 ) {
					$cleanup = true;
				}
				break;
			}
		}

		if ( $cleanup ) {
			$keys = [
				'jquery_ui_theme',
				'jquery_ui_theme_version',
				'jquery_ui_theme_url',
			];
			foreach ( $keys as $key ) {
				$settings->delete( $key );
			}
		}
	}
}

class C_NextGen_Admin_Option_Handler {

	public function get_router() {
		return \Imagely\NGG\Util\Router::get_instance();
	}

	public function get( $key, $default = null ) {
		$retval = $default;

		if ( $key == 'jquery_ui_theme_url' ) {
			$retval = $this->get_router()->get_static_url( 'photocrati-nextgen_admin#jquery-ui/jquery-ui-1.10.4.custom.css' );
		}

		return $retval;
	}
}

new M_NextGen_Admin();
