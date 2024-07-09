<?php

namespace Imagely\NGG\IGW;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataMappers\DisplayedGallery as DisplayedGalleryMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;

use Imagely\NGG\Admin\FormManager;
use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\Display\StaticAssets;
use Imagely\NGG\Display\StaticPopeAssets;
use Imagely\NGG\Display\View;
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\DisplayedGallery\SourceManager;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Router;
use Imagely\NGG\Util\Security;
use Imagely\NGG\Util\URL;

class Controller {

	public static $_instances = [];

	protected $displayed_gallery;

	/** @var \C_NextGen_Admin_Page_Controller|null $parent */
	protected $parent = null;

	protected $attach_to_post_scripts = [];
	protected $attach_to_post_styles  = [];

	public function __construct() {
		$this->parent = \C_NextGen_Admin_Page_Controller::get_instance();
		$this->_load_displayed_gallery();

		if ( ! has_action( 'wp_print_scripts', [ $this, 'filter_scripts' ] ) ) {
			add_action( 'wp_print_scripts', [ $this, 'filter_scripts' ] );
		}

		if ( ! has_action( 'wp_print_scripts', [ $this, 'filter_styles' ] ) ) {
			add_action( 'wp_print_scripts', [ $this, 'filter_styles' ] );
		}
	}

	static function get_instance( string $context = 'all' ): Controller {
		if ( ! isset( self::$_instances[ $context ] ) ) {
			self::$_instances[ $context ] = new Controller( $context );
		}
		return self::$_instances[ $context ];
	}

	/**
	 * Necessary for compatibility with Pro.
	 *
	 * @TODO Remove when use of this method has been removed from Pro
	 */
	public static function has_method(): bool {
		return false;
	}

	public function _load_displayed_gallery() {
		$mapper = DisplayedGalleryMapper::get_instance();

		// Fetch the displayed gallery by ID.
		if ( ( $id = $this->parent->param( 'id' ) ) ) {
			$this->displayed_gallery = $mapper->find( $id );
		} elseif ( isset( $_REQUEST['shortcode'] )
					&& isset( $_REQUEST['nonce'] )
					&& \wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ), 'ngg_attach_to_post_iframe' ) ) {
			// Fetch the displayed gallery by shortcode.
			$shortcode = base64_decode( $_REQUEST['shortcode'] );

			// $shortcode lacks the opening and closing brackets but still begins with 'ngg ' or 'ngg_images ' which are not parameters.
			$params = preg_replace( '/^(ngg|ngg_images) /i', '', $shortcode, 1 );
			$params = stripslashes( $params );
			$params = str_replace( [ '[', ']' ], [ '&#91;', '&#93;' ], $params );
			$params = shortcode_parse_atts( $params );

			$this->displayed_gallery = Renderer::get_instance()->params_to_displayed_gallery( $params );
		}

		// If all else fails, then create fresh with a new displayed gallery.
		if ( empty( $this->displayed_gallery ) ) {
			$this->displayed_gallery = $mapper->create();
		}
	}

	/**
	 * Gets all dependencies for a particular resource that has been registered using wp_register_style/wp_register_script
	 *
	 * @param $handle
	 * @param $type
	 *
	 * @return array
	 */
	public function get_resource_dependencies( $handle, $type ) {
		$retval = [];

		$wp_resources = $GLOBALS[ $type ];

		if ( ( $index = array_search( $handle, $wp_resources->registered ) ) !== false ) {
			$registered_script = $wp_resources->registered[ $index ];
			if ( $registered_script->deps ) {
				foreach ( $registered_script->deps as $dep ) {
					$retval[] = $dep;
					$retval   = array_merge( $retval, $this->get_script_dependencies( $handle ) );
				}
			}
		}

		return $retval;
	}

	public function get_script_dependencies( $handle ) {
		return $this->get_resource_dependencies( $handle, 'wp_scripts' );
	}

	public function get_style_dependencies( $handle ) {
		return $this->get_resource_dependencies( $handle, 'wp_styles' );
	}

	public function get_ngg_provided_resources( $type ) {
		$wp_resources = $GLOBALS[ $type ];

		$retval = [];

		foreach ( $wp_resources->queue as $handle ) {
			$script = $wp_resources->registered[ $handle ];

			if ( strpos( $script->src, plugin_dir_url( NGG_PLUGIN_BASENAME ) ) !== false ) {
				$retval[] = $handle;
			}

			if ( defined( 'NGG_PRO_PLUGIN_BASENAME' ) && strpos( $script->src, plugin_dir_url( NGG_PRO_PLUGIN_BASENAME ) ) !== false ) {
				$retval[] = $handle;
			}

			if ( defined( 'NGG_PLUS_PLUGIN_BASENAME' ) && strpos( $script->src, plugin_dir_url( NGG_PLUS_PLUGIN_BASENAME ) ) !== false ) {
				$retval[] = $handle;
			}
		}

		return array_unique( $retval );
	}

	public function get_ngg_provided_scripts() {
		return $this->get_ngg_provided_resources( 'wp_scripts' );
	}

	public function get_ngg_provided_styles() {
		return $this->get_ngg_provided_resources( 'wp_styles' );
	}

	public function get_igw_allowed_scripts() {
		$retval = [];

		foreach ( $this->get_ngg_provided_scripts() as $handle ) {
			$retval[] = $handle;
			$retval   = array_merge( $retval, $this->get_script_dependencies( $handle ) );
		}

		foreach ( $this->get_display_type_scripts() as $handle ) {
			$retval[] = $handle;
			$retval   = array_merge( $retval, $this->get_script_dependencies( $handle ) );
		}

		foreach ( $this->attach_to_post_scripts as $handle ) {
			$retval[] = $handle;
			$retval   = array_merge( $retval, $this->get_script_dependencies( $handle ) );
		}

		return array_unique( apply_filters( 'ngg_igw_approved_scripts', $retval ) );
	}

	public function get_display_type_scripts() {
		global $wp_scripts;

		$wp_scripts->old_queue = $wp_scripts->queue;
		$wp_scripts->queue     = [];

		$mapper = DisplayTypeMapper::get_instance();
		foreach ( $mapper->find_all() as $display_type ) {
			$form = \C_Form::get_instance( $display_type->name );
			$form->enqueue_static_resources();
		}

		$retval            = $wp_scripts->queue;
		$wp_scripts->queue = $wp_scripts->old_queue;
		unset( $wp_scripts->old_queue );

		return $retval;
	}

	public function get_display_type_styles() {
		global $wp_styles;

		$wp_styles->old_queue = $wp_styles->queue;
		$wp_styles->queue     = [];

		$mapper = DisplayTypeMapper::get_instance();
		foreach ( $mapper->find_all() as $display_type ) {
			$form = \C_Form::get_instance( $display_type->name );
			$form->enqueue_static_resources();
		}

		$retval           = $wp_styles->queue;
		$wp_styles->queue = $wp_styles->old_queue;
		unset( $wp_styles->old_queue );

		return $retval;
	}

	public function get_igw_allowed_styles() {
		$retval = [];

		foreach ( $this->get_ngg_provided_styles() as $handle ) {
			$retval[] = $handle;
			$retval   = array_merge( $retval, $this->get_style_dependencies( $handle ) );
		}

		foreach ( $this->get_display_type_styles() as $handle ) {
			$retval[] = $handle;
			$retval   = array_merge( $retval, $this->get_style_dependencies( $handle ) );
		}

		foreach ( $this->attach_to_post_styles as $handle ) {
			$retval[] = $handle;
			$retval   = array_merge( $retval, $this->get_style_dependencies( $handle ) );
		}

		return array_unique( apply_filters( 'ngg_igw_approved_styles', $retval ) );
	}

	public function filter_scripts() {
		global $wp_scripts;

		$new_queue     = [];
		$current_queue = $wp_scripts->queue;
		$approved      = $this->get_igw_allowed_scripts();

		foreach ( $current_queue as $handle ) {
			if ( in_array( $handle, $approved ) ) {
				$new_queue[] = $handle;
			}
		}

		$wp_scripts->queue = $new_queue;
	}

	public function filter_styles() {
		global $wp_styles;

		$new_queue     = [];
		$current_queue = $wp_styles->queue;
		$approved      = $this->get_igw_allowed_styles();

		foreach ( $current_queue as $handle ) {
			if ( in_array( $handle, $approved ) ) {
				$new_queue[] = $handle;
			}
		}

		$wp_styles->queue = $new_queue;
	}

	/**
	 * Necessary for compatibility with Pro.
	 *
	 * @TODO Remove when use of this method has been removed from Pro
	 * @return false
	 */
	public function mark_script( $handle ) {
		return false;
	}

	public function enqueue_display_tab_js() {
		// Enqueue backbone.js library, required by the Attach to Post display tab.
		wp_enqueue_script( 'backbone' ); // provided by WP.

		// Enqueue the backbone app for the display tab. Get all entities used by the display tab.
		$context             = 'attach_to_post';
		$gallery_mapper      = GalleryMapper::get_instance();
		$album_mapper        = AlbumMapper::get_instance();
		$image_mapper        = ImageMapper::get_instance();
		$display_type_mapper = DisplayTypeMapper::get_instance();
		$sources             = SourceManager::get_instance();
		$router              = Router::get_instance();
		$settings            = Settings::get_instance();

		// Get the nextgen tags.
		global $wpdb;
		$tags           = $wpdb->get_results(
			"SELECT DISTINCT name AS 'id', name FROM {$wpdb->terms}
                        WHERE term_id IN (
                                SELECT term_id FROM {$wpdb->term_taxonomy}
                                WHERE taxonomy = 'ngg_tag'
                        )"
		);
		$all_tags       = new \stdClass();
		$all_tags->name = 'All';
		$all_tags->id   = 'All';
		array_unshift( $tags, $all_tags );

		$display_types = [];

		$display_type_mapper->flush_query_cache();
		$all_display_types = $display_type_mapper->find_all();

		if ( \C_NextGEN_Bootstrap::get_pro_api_version() >= 4.0 ) {
			foreach ( $all_display_types as $display_type ) {

				$available = ControllerFactory::has_controller( $display_type->name );

				if ( ! \apply_filters( 'ngg_atp_show_display_type', $available, $display_type ) ) {
					continue;
				}

				if ( ! ControllerFactory::has_controller( $display_type->name ) ) {
					continue;
				}

				$controller = ControllerFactory::get_controller( $display_type->name );

				if ( $controller->is_hidden_from_igw() ) {
					continue;
				}

				$display_type->preview_image_url = $controller->get_preview_image_url();

				$display_types[] = $display_type;
			}
		} else {
			foreach ( $all_display_types as $display_type ) {

				if ( ( isset( $display_type->hidden_from_igw ) && $display_type->hidden_from_igw ) || ( isset( $display_type->hidden_from_ui ) && $display_type->hidden_from_ui ) ) {
					continue;
				}

				$available = ControllerFactory::has_controller( $display_type->name );

				if ( ! $available && class_exists( '\C_Component_Registry' ) ) {
					$available = \C_Component_Registry::get_instance()->is_module_loaded( $display_type->name );
					if ( ! $available
						&& defined( 'NGG_PRO_ALBUMS' )
						&& in_array( $display_type->name, [ \NGG_PRO_LIST_ALBUM, \NGG_PRO_GRID_ALBUM ] ) ) {
						$available = true;
					}
				}

				if ( ! \apply_filters( 'ngg_atp_show_display_type', $available, $display_type ) ) {
					continue;
				}

				if ( ControllerFactory::has_controller( $display_type->name ) ) {
					$controller                      = ControllerFactory::get_controller( $display_type->name );
					$display_type->preview_image_url = $controller->get_preview_image_url();
				} else {
					$display_type->preview_image_url = StaticPopeAssets::get_url( $display_type->preview_image_relpath );
				}

				$display_types[] = $display_type;
			}
		}

		usort( $display_types, [ $this, '_display_type_list_sort' ] );

		\wp_enqueue_script(
			'ngg_display_tab',
			StaticAssets::get_url( 'AttachToPost/display_tab.js', 'photocrati-attach_to_post#display_tab.js' ),
			[ 'jquery', 'backbone', 'photocrati_ajax' ],
			NGG_SCRIPT_VERSION
		);

		\wp_localize_script(
			'ngg_display_tab',
			'igw_data',
			[
				'displayed_gallery_preview_url' => $router->get_url( '/' . NGG_ATTACH_TO_POST_SLUG . '/preview', false ),
				'displayed_gallery'             => $this->displayed_gallery->get_entity(),
				'sources'                       => $sources->get_all(),
				'gallery_primary_key'           => $gallery_mapper->get_primary_key_column(),
				'galleries'                     => $gallery_mapper->find_all(),
				'albums'                        => $album_mapper->find_all(),
				'tags'                          => $tags,
				'display_types'                 => $display_types,
				'nonce'                         => wp_create_nonce( 'wp_rest' ),
				'image_primary_key'             => $image_mapper->get_primary_key_column(),
				'display_type_priority_base'    => NGG_DISPLAY_PRIORITY_BASE,
				'display_type_priority_step'    => NGG_DISPLAY_PRIORITY_STEP,
				// Nonce verification has already been performed by the methods that invoke this method.
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				'shortcode_ref'                 => isset( $_REQUEST['ref'] ) ? floatval( $_REQUEST['ref'] ) : null,
				'shortcode_defaults'            => [
					'order_by'             => $settings->get( 'galSort' ),
					'order_direction'      => $settings->get( 'galSortDir' ),
					'returns'              => 'included',
					'maximum_entity_count' => $settings->get( 'maximum_entity_count' ),
				],
				'shortcode_attr_replacements'   => [
					'source'        => 'src',
					'container_ids' => 'ids',
					'display_type'  => 'display',
				],
				'i18n'                          => [
					'sources'          => \__( 'Are you inserting a Gallery (default), an Album, or images based on Tags?', 'nggallery' ),
					'optional'         => \__( '(optional)', 'nggallery' ),
					'slug_tooltip'     => \__( 'Sets an SEO-friendly name to this gallery for URLs. Currently only in use by the Pro Lightbox', 'nggallery' ),
					'slug_label'       => \__( 'Slug', 'nggallery' ),
					'no_entities'      => \__( 'No entities to display for this source', 'nggallery' ),
					'exclude_question' => \__( 'Exclude?', 'nggallery' ),
					'select_gallery'   => \__( 'Select a Gallery', 'nggallery' ),
					'galleries'        => \__( 'Select one or more galleries (click in box to see available galleries).', 'nggallery' ),
					'albums'           => \__( 'Select one album (click in box to see available albums).', 'nggallery' ),

				],
			]
		);
	}

	public function start_resource_monitoring() {
		global $wp_scripts, $wp_styles;

		$this->attach_to_post_scripts  = [];
		$this->attach_to_post_styles   = [];
		$wp_styles->before_monitoring  = $wp_styles->queue;
		$wp_scripts->before_monitoring = $wp_styles->queue;
	}

	public function stop_resource_monitoring() {
		global $wp_scripts, $wp_styles;
		$this->attach_to_post_scripts = array_diff( $wp_scripts->queue, $wp_scripts->before_monitoring );
		$this->attach_to_post_styles  = array_diff( $wp_styles->queue, $wp_styles->before_monitoring );
	}

	public function enqueue_backend_resources() {
		$this->start_resource_monitoring();

		// Enqueue frame event publishing.
		\wp_enqueue_script( 'frame_event_publisher' );

		\wp_enqueue_script(
			'ngg_tabs',
			StaticAssets::get_url( 'AttachToPost/ngg_tabs.js', 'photocrati-attach_to_post#ngg_tabs.js' ),
			[ 'jquery-ui-tabs', 'jquery-ui-sortable', 'jquery-ui-tooltip', 'jquery-ui-accordion' ],
			NGG_SCRIPT_VERSION
		);

		\wp_enqueue_style( 'buttons' );

		// Ensure select2.
		\wp_enqueue_style( 'ngg_select2' );
		\wp_enqueue_script( 'ngg_select2' );

		// Ensure that the Photocrati AJAX library is loaded.
		\wp_enqueue_script( 'photocrati_ajax' );

		// Enqueue logic for the Attach to Post interface as a whole.
		\wp_enqueue_script(
			'ngg_attach_to_post_js',
			StaticAssets::get_url( 'AttachToPost/attach_to_post.js', 'photocrati-attach_to_post#attach_to_post.js' ),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_enqueue_style(
			'ngg_attach_to_post',
			StaticAssets::get_url( 'AttachToPost/attach_to_post.css', 'photocrati-attach_to_post#attach_to_post.css' ),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_dequeue_script( 'debug-bar-js' );
		\wp_dequeue_style( 'debug-bar-css' );

		$this->enqueue_display_tab_js();

		if ( ! \M_Marketing::is_plus_or_pro_enabled() ) {
			$marketing = new Marketing();
			$marketing->enqueue_display_tab_js();
		}

		\do_action( 'ngg_igw_enqueue_scripts' );
		\do_action( 'ngg_igw_enqueue_styles' );

		$this->stop_resource_monitoring();
	}

	/**
	 * Renders the interface
	 *
	 * @param bool $return
	 * @return string
	 */
	public function index_action( $return = true ) {
		$parent = \C_NextGen_Admin_Page_Controller::get_instance();
		$parent->enqueue_backend_resources();
		$parent->do_not_cache();

		$this->enqueue_backend_resources();

		\wp_enqueue_style( 'nggadmin', NGGALLERY_URLPATH . 'admin/css/nggadmin.css', [], NGG_SCRIPT_VERSION, 'screen' );
		\do_action( 'admin_enqueue_scripts' );

		// If Elementor is also active a fatal error is generated due to this method not existing.
		if ( ! function_exists( 'wp_print_media_templates' ) ) {
			require_once ABSPATH . WPINC . '/media-template.php';
		}

		$view = new View(
			'AttachToPost/attach_to_post',
			[
				'page_title' => $this->_get_page_title(),
				'tabs'       => $this->_get_main_tabs(),
				'logo'       => StaticPopeAssets::get_url( 'photocrati-nextgen_admin#imagely_icon.png' ),
			],
			'photocrati-attach_to_post#attach_to_post'
		);

		return $view->render( $return );
	}

	/**
	 * Returns the page title of the Attach to Post interface
	 *
	 * @return string
	 */
	public function _get_page_title() {
		return \__( 'NextGEN Gallery - Attach To Post', 'nggallery' );
	}

	/**
	 * Returns the main tabs displayed on the Attach to Post interface
	 *
	 * @return array
	 */
	public function _get_main_tabs() {
		$retval = [];

		if ( Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			$retval['displayed_tab'] = [
				'content' => $this->_render_display_tab(),
				'title'   => \__( 'Insert Into Page', 'nggallery' ),
			];
		}

		if ( Security::is_allowed( 'NextGEN Upload images' ) ) {
			$retval['create_tab'] = [
				'content' => $this->_render_create_tab(),
				'title'   => \__( 'Upload Images', 'nggallery' ),
			];
		}

		if ( Security::is_allowed( 'NextGEN Manage others gallery' ) && Security::is_allowed( 'NextGEN Manage gallery' ) ) {
			$retval['galleries_tab'] = [
				'content' => $this->_render_galleries_tab(),
				'title'   => \__( 'Manage Galleries', 'nggallery' ),
			];
		}

		if ( Security::is_allowed( 'NextGEN Edit album' ) ) {
			$retval['albums_tab'] = [
				'content' => $this->_render_albums_tab(),
				'title'   => \__( 'Manage Albums', 'nggallery' ),
			];
		}

		return apply_filters( 'ngg_attach_to_post_main_tabs', $retval );
	}

	/**
	 * Renders a NextGen Gallery page in an iframe, suited for the attach to post
	 * interface
	 *
	 * @param string   $page
	 * @param null|int $tab_id (optional)
	 * @return string
	 */
	public function _render_ngg_page_in_frame( $page, $tab_id = null ) {
		$frame_url = \admin_url( "/admin.php?page={$page}&attach_to_post" );
		$frame_url = Router::esc_url( $frame_url );

		if ( $tab_id ) {
			$tab_id = " id='ngg-iframe-{$tab_id}'";
		}

		return "<iframe name='{$page}' frameBorder='0'{$tab_id} class='ngg-attach-to-post ngg-iframe-page-{$page}' scrolling='yes' src='{$frame_url}'></iframe>";
	}

	/**
	 * Renders the display tab for adjusting how images/galleries will be displayed
	 *
	 * @return string
	 */
	public function _render_display_tab() {
		$view = new View(
			'AttachToPost/display_tab',
			[
				'messages'          => [],
				'displayed_gallery' => $this->displayed_gallery,
				'tabs'              => $this->_get_display_tabs(),
			],
			'photocrati-attach_to_post#display_tab'
		);
		return $view->render( true );
	}

	/**
	 * Renders the tab used primarily for Gallery and Image creation
	 *
	 * @return string
	 */
	public function _render_create_tab() {
		return $this->_render_ngg_page_in_frame( 'ngg_addgallery', 'create_tab' );
	}

	/**
	 * Renders the tab used for Managing Galleries
	 *
	 * @return string
	 */
	public function _render_galleries_tab() {
		return $this->_render_ngg_page_in_frame( 'nggallery-manage-gallery', 'galleries_tab' );
	}

	/**
	 * Renders the tab used for Managing Albums.
	 */
	public function _render_albums_tab() {
		return $this->_render_ngg_page_in_frame( 'nggallery-manage-album', 'albums_tab' );
	}

	public function _display_type_list_sort( $type_1, $type_2 ) {
		$order_1 = $type_1->view_order;
		$order_2 = $type_2->view_order;

		if ( $order_1 == null ) {
			$order_1 = NGG_DISPLAY_PRIORITY_BASE;
		}

		if ( $order_2 == null ) {
			$order_2 = NGG_DISPLAY_PRIORITY_BASE;
		}

		if ( $order_1 > $order_2 ) {
			return 1;
		}

		if ( $order_1 < $order_2 ) {
			return -1;
		}

		return 0;
	}

	/**
	 * Gets a list of tabs to render for the "Display" tab
	 */
	public function _get_display_tabs() {
		// The ATP requires more memmory than some applications, somewhere around 60MB.
		// Because it's such an important feature of NextGEN Gallery, we temporarily disable
		// any memory limits.
		if ( ! extension_loaded( 'suhosin' ) ) {
			@ini_set( 'memory_limit', -1 );
		}

		return [
			'choose_display_tab'   => $this->_render_choose_display_tab(),
			'display_settings_tab' => $this->_render_display_settings_tab(),
			'preview_tab'          => $this->_render_preview_tab(),
		];
	}

	/**
	 * Renders the accordion tab, "What would you like to display?"
	 */
	public function _render_choose_display_tab() {
		return [
			'id'      => 'choose_display',
			'title'   => \__( 'Choose Display', 'nggallery' ),
			'content' => $this->_render_display_source_tab_contents() . $this->_render_display_type_tab_contents(),
		];
	}

	/**
	 * Renders the contents of the source tab
	 *
	 * @return string
	 */
	public function _render_display_source_tab_contents() {
		$view = new View(
			'AttachToPost/display_tab_source',
			[
				'i18n' => [],
			],
			'photocrati-attach_to_post#display_tab_source'
		);
		return $view->render( true );
	}

	/**
	 * Renders the contents of the display type tab
	 */
	public function _render_display_type_tab_contents() {
		$view = new View(
			'AttachToPost/display_tab_type',
			[],
			'photocrati-attach_to_post#display_tab_type'
		);
		return $view->render( true );
	}

	/**
	 * Renders the display settings tab for the Attach to Post interface
	 *
	 * @return array
	 */
	public function _render_display_settings_tab() {
		return [
			'id'      => 'display_settings_tab',
			'title'   => \__( 'Customize Display Settings', 'nggallery' ),
			'content' => $this->_render_display_settings_contents(),
		];
	}

	/**
	 * If editing an existing displayed gallery, retrieves the name
	 * of the display type
	 *
	 * @return string
	 */
	public function _get_selected_display_type_name() {
		$retval = '';

		if ( $this->displayed_gallery ) {
			$retval = $this->displayed_gallery->display_type;
		}

		return $retval;
	}

	/**
	 * Is the displayed gallery that's being edited using the specified display
	 * type?
	 *
	 * @param string $name  name of the display type
	 * @return bool
	 */
	public function is_displayed_gallery_using_display_type( $name ) {
		$retval = false;

		if ( $this->displayed_gallery ) {
			$retval = $this->displayed_gallery->display_type == $name;
		}

		return $retval;
	}

	/**
	 * Renders the contents of the display settings tab
	 *
	 * @return string
	 */
	public function _render_display_settings_contents() {
		$retval = [];

		// Get all display setting forms.
		$form_manager = FormManager::get_instance();
		$forms        = $form_manager->get_forms( NGG_DISPLAY_SETTINGS_SLUG, true );

		// Display each form.
		foreach ( $forms as $form ) {

			// Enqueue the form's static resources.
			$form->enqueue_static_resources();

			// Determine which classes to use for the form's "class" attribute.
			$model = $form->get_model();

			if ( null === $model ) {
				continue;
			}

			$current   = $this->is_displayed_gallery_using_display_type( $model->name );
			$css_class = $current ? 'display_settings_form' : 'display_settings_form hidden';
			$defaults  = $model->settings;

			// If this form is used to provide the display settings for the current
			// displayed gallery, then we need to override the forms settings
			// with the displayed gallery settings.
			if ( $current ) {
				$settings = $this->parent->array_merge_assoc(
					$model->settings,
					$this->displayed_gallery->display_settings,
					true
				);

				$model->settings = $settings;
			}

			// Output the display settings form.
			$view     = new View(
				'AttachToPost/display_settings_form',
				[
					'settings'          => $form->render(),
					'display_type_name' => $model->name,
					'css_class'         => $css_class,
					'defaults'          => $defaults,
				],
				'photocrati-attach_to_post#display_settings_form'
			);
			$retval[] = $view->render( true );
		}

		// In addition, we'll render a form that will be displayed when no display type has been selected in the
		// Attach to Post interface. Render the default "no display type selected" view.
		$css_class = $this->_get_selected_display_type_name() ? 'display_settings_form hidden' : 'display_settings_form';

		$view = new View(
			'AttachToPost/no_display_type_selected',
			[
				'no_display_type_selected' => \__( 'No display type selected', 'nggallery' ),
				'css_class'                => $css_class,
			],
			'photocrati-attach_to_post#no_display_type_selected'
		);

		$retval[] = $view->render( true );

		// Return all display setting forms.
		return implode( "\n", $retval );
	}

	/**
	 * Renders the tab used to preview included images
	 *
	 * @return array
	 */
	public function _render_preview_tab() {
		return [
			'id'      => 'preview_tab',
			'title'   => \__( 'Sort or Exclude Images', 'nggallery' ),
			'content' => $this->_render_preview_tab_contents(),
		];
	}

	/**
	 * Renders the contents of the "Preview" tab.
	 *
	 * @return string
	 */
	public function _render_preview_tab_contents() {
		$view = new View(
			'AttachToPost/preview_tab',
			[],
			'photocrati-attach_to_post#preview_tab'
		);

		return $view->render( true );
	}
}
