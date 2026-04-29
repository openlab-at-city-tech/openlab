<?php

use Imagely\NGG\Admin\AMNotifications as Notifications;

/**
 * Admin Section for NextGEN Gallery
 *
 * @package NextGEN Gallery
 * @author Alex Rabe
 *
 * @since 1.0.0
 */
class nggAdminPanel {

	// constructor.
	public function __construct() {

		// Buffer the output.
		add_action( 'admin_init', [ $this, 'start_buffer' ] );

		// Add the admin menu.
		add_action( 'admin_menu', [ $this, 'add_menu' ] );
		if ( self::show_legacy_settings() ) {
			add_action( 'admin_bar_menu', [ $this, 'admin_bar_menu' ], 99 );
		}
		add_action( 'network_admin_menu', [ $this, 'add_network_admin_menu' ] );

		// Add the script and style files.
		add_action( 'admin_print_scripts', [ $this, 'load_scripts' ] );
		add_action( 'admin_print_styles', [ $this, 'load_styles' ] );

		add_filter( 'current_screen', [ $this, 'edit_current_screen' ] );

		add_action( 'ngg_admin_enqueue_scripts', [ $this, 'enqueue_progress_bars' ] );
	}

	public function enqueue_progress_bars() {
		// Enqueue the new Gritter-based progress bars.
		wp_enqueue_style( 'ngg_progressbar' );
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_enqueue_script( 'ngg_progressbar' );
	}

	public function start_buffer() {

		// Notify of page event.
		//
		// Nonce verification here is not necessary: this is a general router to methods that may or may not have their
		// own authentication & nonce verification checks.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['page'] ) && ! empty( $_POST ) ) {

			$event = [
				'event' => str_replace(
					'-',
					'_',
					str_replace(
						'nggallery',
						'',
						sanitize_text_field( wp_unslash( $_REQUEST['page'] ) )
					)
				),
			];

			// Do we have a list of galleries that are being affected?
			if ( isset( $_REQUEST['doaction'] ) ) {
				$event['gallery_ids'] = sanitize_text_field( wp_unslash( $_REQUEST['doaction'] ) );
			} elseif ( isset( $_REQUEST['gid'] ) ) {
				// Do we have a particular gallery id?
				$event['gallery_id'] = sanitize_text_field( wp_unslash( $_REQUEST['gid'] ) );
			} elseif ( isset( $_REQUEST['act_album'] ) ) {
				// Do we have an album id?
				$event['album_id'] = sanitize_text_field( wp_unslash( $_REQUEST['act_album'] ) );
			}
		// phpcs:enable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended

			if ( strpos( $event['event'], '_' ) === 0 ) {
				$event['event'] = substr( $event['event'], 1 );
			}

			do_action( 'ngg_page_event', $event );
		}

		ob_start();
	}   // integrate the menu.
	public function add_menu() {
		$notifications = new Notifications();

		// Notification count HTML to append to the menu.
		$nav_append_count = '';
		if ( absint( $notifications->get_count() ) > 0 ) {
			$nav_append_count = "<span class='ngg-menu-notification-indicator update-plugins'>" . absint( $notifications->get_count() ) . '</span>';
		}

		$show_old_settings = self::show_legacy_settings();
		$name              = $show_old_settings ? NGGFOLDER : '';

		if ( $show_old_settings ) {
			add_menu_page(
				__( 'NextGEN Gallery', 'nggallery' ),
				_n( 'NextGEN Gallery', 'NextGen Galleries', 1, 'nggallery' ) . $nav_append_count,
    // phpcs:ignore WordPress.WP.Capabilities.Unknown
				'NextGEN Gallery overview',
				NGGFOLDER,
				[ $this, 'show_menu' ],
				path_join( NGGALLERY_URLPATH, 'admin/images/imagely_icon.png' ),
				11
			);
		}

		// Legacy pages - hidden from menu but accessible via direct URL.
		// Using empty string '' as parent creates hidden pages without PHP 8.1+ deprecation warnings.
  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		add_submenu_page( $name, __( 'NextGEN Gallery Overview', 'nggallery' ), __( 'Overview', 'nggallery' ), 'NextGEN Gallery overview', NGGFOLDER, [ $this, 'show_menu' ] );
  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		add_submenu_page( $name, __( 'Manage Galleries', 'nggallery' ), __( 'Manage Galleries', 'nggallery' ), 'NextGEN Manage gallery', 'nggallery-manage-gallery', [ $this, 'show_menu' ] );
  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		add_submenu_page( $name, _n( 'Manage Albums', 'Albums', 1, 'nggallery' ), _n( 'Manage Albums', 'Manage Albums', 1, 'nggallery' ), 'NextGEN Edit album', 'nggallery-manage-album', [ $this, 'show_menu' ] );
  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		add_submenu_page( $name, __( 'Manage Tags', 'nggallery' ), __( 'Manage Tags', 'nggallery' ), 'NextGEN Manage tags', 'nggallery-tags', [ $this, 'show_menu' ] );

		// Set page title for hidden pages to avoid strip_tags() deprecation warning.
		add_action( 'admin_head', [ $this, 'set_legacy_page_titles' ] );

		// register the column fields.
		$this->register_columns();
	}

	// integrate the network menu.
	public function add_network_admin_menu() {

  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		add_menu_page( _n( 'Gallery', 'Galleries', 1, 'nggallery' ), _n( 'Gallery', 'Galleries', 1, 'nggallery' ), 'nggallery-wpmu', NGGFOLDER, [ &$this, 'show_network_settings' ], path_join( NGGALLERY_URLPATH, 'admin/images/imagely_icon.png' ) );
  // phpcs:ignore WordPress.WP.Capabilities.Unknown
		add_submenu_page( NGGFOLDER, __( 'Network settings', 'nggallery' ), __( 'Network settings', 'nggallery' ), 'nggallery-wpmu', NGGFOLDER, [ &$this, 'show_network_settings' ] );
	}

	/**
	 * Adding NextGEN Gallery to the Admin bar
	 *
	 * @since 1.9.0
	 *
	 * @return void
	 */
	public function admin_bar_menu() {
		// If the current user can't write posts, this is all of no use, so let's not output an admin menu.
		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( ! current_user_can( 'NextGEN Gallery overview' ) ) {
			return;
		}

		global $wp_admin_bar;

		$wp_admin_bar->add_menu(
			[
				'id'    => 'ngg-menu',
				'title' => __( 'Gallery', 'nggallery' ),
				'href'  => admin_url( 'admin.php?page=' . NGGFOLDER ),
			]
		);
		$wp_admin_bar->add_menu(
			[
				'parent' => 'ngg-menu',
				'id'     => 'ngg-menu-overview',
				'title'  => __( 'Overview', 'nggallery' ),
				'href'   => admin_url( 'admin.php?page=' . NGGFOLDER ),
			]
		);
		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( current_user_can( 'NextGEN Upload images' ) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'ngg-menu',
					'id'     => 'ngg-menu-add-gallery',
					'title'  => __( 'Add Gallery / Images', 'nggallery' ),
					'href'   => admin_url( 'admin.php?page=ngg_addgallery' ),
				]
			);
		}
		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( current_user_can( 'NextGEN Manage gallery' ) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'ngg-menu',
					'id'     => 'ngg-menu-manage-gallery',
					'title'  => __( 'Manage Galleries', 'nggallery' ),
					'href'   => admin_url( 'admin.php?page=nggallery-manage-gallery' ),
				]
			);
		}
		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( current_user_can( 'NextGEN Edit album' ) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'ngg-menu',
					'id'     => 'ngg-menu-manage-album',
					'title'  => _n( 'Manage Albums', 'Manage Albums', 1, 'nggallery' ),
					'href'   => admin_url( 'admin.php?page=nggallery-manage-album' ),
				]
			);
		}
		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( current_user_can( 'NextGEN Manage tags' ) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'ngg-menu',
					'id'     => 'ngg-menu-tags',
					'title'  => __( 'Manage Tags', 'nggallery' ),
					'href'   => admin_url( 'admin.php?page=nggallery-tags' ),
				]
			);
		}
	}

	// show the network page.
	public function show_network_settings() {
		include_once __DIR__ . '/wpmu.php';
		nggallery_wpmu_setup();
	}

	// load the script for the defined page and load only this code.
	public function show_menu() {
		global $ngg;

		// Set installation date.
		if ( empty( $ngg->options['installDate'] ) ) {
			$ngg->options['installDate'] = time();
			update_option( 'ngg_options', $ngg->options );
		}

		echo '<div id="ngg_page_content">';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for routing
		switch ( isset( $_GET['page'] ) ? $_GET['page'] : '' ) {
			case 'nggallery-manage-gallery':
				include_once __DIR__ . '/functions.php'; // admin functions.
				include_once __DIR__ . '/manage.php';    // nggallery_admin_manage_gallery.
				// Initate the Manage Gallery page.
				$ngg->manage_page = new nggManageGallery();
				// Render the output now, because you cannot access a object during the constructor is not finished.
				$ngg->manage_page->controller();
				break;
			case 'nggallery-manage-album':
				include_once __DIR__ . '/album.php';     // nggallery_admin_manage_album.
				$ngg->manage_album = new nggManageAlbum();
				$ngg->manage_album->controller();
				break;
			case 'nggallery-tags':
				include_once __DIR__ . '/tags.php';      // nggallery_admin_tags.
				break;
			case 'nggallery':
			default:
				include_once __DIR__ . '/overview.php';  // nggallery_admin_overview.
				nggallery_admin_overview();
				break;
		}
		echo '</div>';
	}

	public function load_scripts() {
		global $wp_version;

		// no need to go on if it's not a plugin page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for routing
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}

		// used to retrieve the uri of some module resources.
		$router = \Imagely\NGG\Util\Router::get_instance();

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_register_script( 'ngg-ajax', NGGALLERY_URLPATH . 'admin/js/ngg.ajax.js', [ 'jquery' ], NGG_SCRIPT_VERSION );
		wp_localize_script(
			'ngg-ajax',
			'nggAjaxSetup',
			[
				'url'        => admin_url( 'admin-ajax.php' ),
				'action'     => 'ngg_ajax_operation',
				'operation'  => '',
				'nonce'      => wp_create_nonce( 'ngg-ajax' ),
				'ids'        => '',
				'permission' => __( 'You do not have the correct permission', 'nggallery' ),
				'error'      => __( 'Unexpected Error', 'nggallery' ),
				'failure'    => __( 'A failure occurred', 'nggallery' ),
			]
		);
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_register_script( 'ngg-progressbar', NGGALLERY_URLPATH . 'admin/js/ngg.progressbar.js', [ 'jquery' ], NGG_SCRIPT_VERSION );

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_enqueue_script( 'wp-color-picker' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for routing
		switch ( $_GET['page'] ) {
			case NGGFOLDER:
				// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
				wp_enqueue_script(
					'ngg_overview',
					\Imagely\NGG\Display\StaticAssets::get_url(
						'Legacy/overview.js',
						'photocrati-nextgen-legacy#overview.js'
					),
					[ 'jquery' ],
					NGG_SCRIPT_VERSION
				);
				break;
			case 'nggallery-manage-gallery':
				wp_enqueue_script( 'postbox' );
				wp_enqueue_script( 'ngg-ajax' );
				wp_enqueue_script( 'ngg-progressbar' );
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
				wp_register_script(
					'shutter',
					\Imagely\NGG\Display\StaticAssets::get_url( 'Lightbox/shutter/shutter.js', 'photocrati-lightbox#shutter/shutter.js' ),
					[],
					NGG_SCRIPT_VERSION
				);
				wp_localize_script(
					'shutter',
					'shutterSettings',
					[
						'msgLoading' => __( 'L O A D I N G', 'nggallery' ),
						'msgClose'   => __( 'Click to Close', 'nggallery' ),
						'imageCount' => '1',
					]
				);
				wp_enqueue_script( 'shutter' );

				// Thickbox is used to display images being managed.
				wp_dequeue_script( 'thickbox' );
				wp_enqueue_style( 'thickbox' );
				\Imagely\NGG\Display\LightboxManager::get_instance()->enqueue( 'thickbox' );

				break;
			case 'nggallery-manage-album':
				wp_enqueue_script( 'jquery-ui-dialog' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'ngg_select2' );
				wp_enqueue_style( 'ngg_select2' );
				break;
		}
	}


	public function enqueue_jquery_ui_theme() {
		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		wp_enqueue_style(
			$settings->get( 'jquery_ui_theme' ),
			$settings->get( 'jquery_ui_theme_url' ),
			[],
			$settings->get( 'jquery_ui_theme_version' )
		);
	}

	public function load_styles() {
		global $ngg;

		wp_register_style( 'nggadmin', NGGALLERY_URLPATH . 'admin/css/nggadmin.css', [], NGG_SCRIPT_VERSION, 'screen' );
		wp_register_style( 'ngg-jqueryui', NGGALLERY_URLPATH . 'admin/css/jquery.ui.css', [], NGG_SCRIPT_VERSION, 'screen' );

		// no need to go on if it's not a plugin page.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for routing
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}

		// used to retrieve the uri of some module resources.
		$router = \Imagely\NGG\Util\Router::get_instance();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for routing
		switch ( $_GET['page'] ) {
			case NGGFOLDER:
				wp_add_inline_style(
					'nggadmin',
					file_get_contents(
						\Imagely\NGG\Display\StaticAssets::get_abspath(
							'Legacy/overview.css',
							'photocrati-nextgen-legacy#overview.css'
						)
					)
				);
				// Fall through to enqueue nggadmin.
			case 'nggallery-about':
				wp_enqueue_style( 'nggadmin' );
				break;
			case 'nggallery-manage-gallery':
				// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
				wp_enqueue_script( 'jquery-ui-tooltip' );
				// Fall through to enqueue theme and nggadmin.
			case 'nggallery-roles':
			case 'nggallery-manage-album':
				$this->enqueue_jquery_ui_theme();
				wp_enqueue_style( 'nggadmin' );
				break;
			case 'nggallery-tags':
				wp_enqueue_style( 'nggtags', NGGALLERY_URLPATH . 'admin/css/tags-admin.css', [], NGG_SCRIPT_VERSION, 'screen' );
				break;
		}
	}

	/**
	 * We need to manipulate the current_screen name so that we can show the correct column screen options
	 *
	 * @since 1.8.0
	 * @param object $screen
	 * @return object $screen
	 */
	public function edit_current_screen( $screen ) {

		if ( is_string( $screen ) ) {
			$screen = convert_to_screen( $screen );
		}

		// menu title is localized, so we need to change the toplevel name.
		$i18n = strtolower( _n( 'Gallery', 'Galleries', 1, 'nggallery' ) );

		// Nonce verification is not necessary here: we are inspecting the URL and manually setting attributes, not
		// accepting any user input here.
		//
		// phpcs:disable WordPress.Security.NonceVerification.Missing,WordPress.Security.NonceVerification.Recommended
		switch ( $screen->id ) {
			case "{$i18n}_page_nggallery-manage-gallery":
				// we would like to have screen option only at the manage images / gallery page.
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only GET parameter for routing
				if ( ( isset( $_GET['mode'] ) && 'edit' === $_GET['mode'] ) || isset( $_POST['backToGallery'] ) ) {
					$screen->id   = 'nggallery-manage-images';
					$screen->base = $screen->id;
				} else {
					$screen->id   = 'nggallery-manage-gallery';
					$screen->base = $screen->id;
				}
				break;
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( strpos( $screen->id, 'ngg' ) !== false ||
				strpos( $screen->id, 'nextgen' ) !== false ||
				strpos( $screen->id, 'ngg' ) === 0 ) {
			$screen->ngg = true; }

		return $screen;
	}

	/**
	 * We need to register the columns at a very early point
	 *
	 * @return void
	 */
	public function register_columns() {
		include_once __DIR__ . '/manage-images.php';

		$wp_list_table = new _NGG_Images_List_Table( 'nggallery-manage-images' );

		include_once __DIR__ . '/manage-galleries.php';

		$wp_list_table = new _NGG_Galleries_List_Table( 'nggallery-manage-gallery' );
	}

	/**
	 * Set page titles for legacy hidden pages to avoid deprecation warnings.
	 *
	 * @since 3.0.0
	 * @return void
	 */
	public function set_legacy_page_titles() {
		global $title;

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );

		// Set title based on page.
		$page_titles = [
			NGGFOLDER                  => __( 'NextGEN Gallery Overview', 'nggallery' ),
			'nggallery-manage-gallery' => __( 'Manage Galleries', 'nggallery' ),
			'nggallery-manage-album'   => _n( 'Manage Albums', 'Manage Albums', 1, 'nggallery' ),
			'nggallery-tags'           => __( 'Manage Tags', 'nggallery' ),
		];

		if ( isset( $page_titles[ $page ] ) ) {
			$title = $page_titles[ $page ];
		}
	}

	public static function show_legacy_settings() {
		// Get from main settings array instead of separate option
		$settings = \Imagely\NGG\Settings\Settings::get_instance();
		return filter_var( $settings->get( 'ngg_show_old_settings', false ), FILTER_VALIDATE_BOOLEAN );
	}
}
