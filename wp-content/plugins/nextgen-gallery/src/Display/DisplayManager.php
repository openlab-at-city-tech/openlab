<?php
/**
 * Display Manager for NextGEN Gallery
 *
 * @package NextGEN Gallery
 * @subpackage Display
 */

namespace Imagely\NGG\Display;

use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;

use Imagely\NGG\DataTypes\{DisplayType, DisplayedGallery};
use Imagely\NGG\DisplayType\{Controller, ControllerFactory};
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Router;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\REST\DataMappers\AddonsREST;

/**
 * Display Manager class for NextGEN Gallery
 *
 * Handles registration and rendering of gallery displays.
 */
class DisplayManager {

	/**
	 * Array of enqueued displayed gallery IDs
	 *
	 * @var array
	 */
	public static $enqueued_displayed_gallery_ids = [];

	/**
	 * Registers hooks for display manager
	 *
	 * @return void
	 */
	public static function register_hooks() {
		$self = new DisplayManager();

		Shortcodes::add( 'ngg', [ $self, 'display_images' ] );
		Shortcodes::add( 'ngg_images', [ $self, 'display_images' ] );
		Shortcodes::add( 'imagely', [ $self, 'display_imagely_shortcode' ] );

		add_filter( 'the_content', [ $self, '_render_related_images' ] );

		add_action( 'init', [ $self, 'register_resources' ], 12 );

		add_action( 'admin_bar_menu', [ $self, 'add_admin_bar_menu' ], 100 );

		add_action( 'wp_print_styles', [ $self, 'fix_nextgen_custom_css_order' ], PHP_INT_MAX - 1 );

		add_action( 'wp_enqueue_scripts', [ $self, 'enqueue_frontend_resources' ] );
		add_action( 'wp_enqueue_scripts', [ $self, 'maybe_enqueue_ga4_tracking' ], 20 );
	}

	/**
	 * Enqueues frontend resources for the current page
	 *
	 * @return void
	 */
	public function enqueue_frontend_resources() {
		if ( ( defined( 'NGG_SKIP_LOAD_SCRIPTS' ) && NGG_SKIP_LOAD_SCRIPTS ) || $this->is_rest_request() ) {
			return;
		}

		// Find our content and process it.
		global $wp_query;

		// It's possible for the posts attribute to be empty or unset.
		if ( ! isset( $wp_query->posts ) || ! is_array( $wp_query->posts ) ) {
			return;
		}

		$posts = $wp_query->posts;
		foreach ( $posts as $post ) {
			if ( empty( $post->post_content ) ) {
				continue;
			}

			self::enqueue_frontend_resources_for_content( $post->post_content );
		}
	}

	/**
	 * Most content will come from the WP query / global $posts but it's also sometimes necessary to enqueue resources
	 * based on the results of an output filter
	 *
	 * @param string $content The content to scan for shortcodes.
	 */
	public static function enqueue_frontend_resources_for_content( $content = '' ) {
		$manager             = Shortcodes::get_instance();
		$pattern             = $manager->get_shortcode_regex();
		$ngg_shortcodes      = $manager->get_shortcodes();
		$ngg_shortcodes_keys = array_keys( $ngg_shortcodes );

		// Determine which shortcodes to look for; 'ngg' is the default, but there are legacy aliases.
		preg_match_all( $pattern, $content, $matches, PREG_SET_ORDER );

		foreach ( $matches as $shortcode ) {
			$this_shortcode_name = $shortcode[2];
			if ( ! in_array( $this_shortcode_name, $ngg_shortcodes_keys, true ) ) {
				continue;
			}

			$params = shortcode_parse_atts( trim( $shortcode[0], '[]' ) );
			if ( in_array( $params[0], $ngg_shortcodes_keys, true ) ) { // Don't pass 0 => 'ngg' as a parameter, it's just part of the shortcode itself.
				unset( $params[0] );
			}

			// Special handling for the imagely shortcode
			if ( 'imagely' === $this_shortcode_name ) {
				$params = self::convert_imagely_params_for_enqueuing( $params );
				if ( ! $params ) {
					continue; // Skip if gallery not found or no ID provided
				}
			}

			// And do the enqueueing process.
			$renderer = Renderer::get_instance();

			// This is necessary for legacy shortcode compatibility.
			if ( is_callable( $ngg_shortcodes[ $this_shortcode_name ]['transformer'] ) ) {
				$params = call_user_func( $ngg_shortcodes[ $this_shortcode_name ]['transformer'], $params );
			}

			$displayed_gallery = $renderer->params_to_displayed_gallery( $params );

			// TODO: Fix Pro so this is unnecessary and then remove the following check.
			if ( 1 === did_action( 'wp_enqueue_scripts' )
			&& ! ResourceManager::addons_version_check()
			&& in_array( $displayed_gallery->display_type, [ 'photocrati-nextgen_pro_horizontal_filmstrip', 'photocrati-nextgen_pro_slideshow' ], true ) ) {
				continue;
			}

			if ( ControllerFactory::has_controller( $displayed_gallery->display_type ) ) {
				$controller = ControllerFactory::get_controller( $displayed_gallery->display_type );
			}

			if ( ! isset( $controller ) && class_exists( '\C_Display_Type_Controller' ) ) {
				$controller = new \C_Display_Type_Controller( $displayed_gallery->display_type );
			}

			if ( ! $displayed_gallery || empty( $params ) || ! isset( $controller ) ) {
				continue;
			}

			self::enqueue_frontend_resources_for_displayed_gallery( $displayed_gallery, $controller );

			// Prevent $controller from persisting through this loop.
			unset( $controller );
		}
	}

	/**
	 * Converts imagely shortcode parameters to standard NGG parameters for resource enqueuing
	 *
	 * @param array $params The imagely shortcode parameters
	 * @return array|false The converted parameters or false if gallery/album not found
	 */
	private static function convert_imagely_params_for_enqueuing( $params ) {
		// Check if this is a gallery or album shortcode
		$gallery_id = isset( $params['id'] ) ? intval( $params['id'] ) : 0;
		$album_id   = isset( $params['album'] ) ? intval( $params['album'] ) : 0;

		if ( ! $gallery_id && ! $album_id ) {
			return false;
		}

		$display_params = [];

		if ( $gallery_id ) {
			// Handle gallery shortcode
			$gallery_mapper = GalleryMapper::get_instance();
			$gallery        = $gallery_mapper->find( $gallery_id );

			if ( ! $gallery ) {
				return false;
			}

			// Set the display type
			if ( ! empty( $gallery->display_type ) ) {
				$display_params['display_type'] = $gallery->display_type;
			}

			// Merge in the display type settings
			if ( ! empty( $gallery->display_type_settings ) && is_array( $gallery->display_type_settings ) ) {
				// Get settings for the current display type
				$current_display_type = ! empty( $gallery->display_type ) ? $gallery->display_type : 'photocrati-nextgen_basic_thumbnails';
				if ( isset( $gallery->display_type_settings[ $current_display_type ] ) ) {
					$display_params = array_merge( $display_params, $gallery->display_type_settings[ $current_display_type ] );
				}
			}

			// Ensure ecommerce is enabled when set on the gallery record
			if ( ! empty( $gallery->is_ecommerce_enabled ) ) {
				$display_params['is_ecommerce_enabled'] = 1;
			}

			// Set the gallery source
			$display_params['source']        = 'galleries';
			$display_params['container_ids'] = $gallery_id;

			// Remove the 'id' parameter as it's not needed for rendering
			unset( $params['id'] );
		} elseif ( $album_id ) {
			// Handle album shortcode
			$album_mapper = AlbumMapper::get_instance();
			$album        = $album_mapper->find( $album_id );

			if ( ! $album ) {
				return false;
			}

			// photocrati-nextgen_basic_thumbnails was an incorrect column default in the Album data mapper.
			// Treat it as absent and fall back to the compact album display type.
			$stored_display_type = ! empty( $album->display_type ) ? $album->display_type : '';
			if ( empty( $stored_display_type ) || NGG_BASIC_THUMBNAILS === $stored_display_type ) {
				$display_params['display_type'] = NGG_BASIC_COMPACT_ALBUM;
			} else {
				$display_params['display_type'] = $stored_display_type;
			}

			// Merge in the display type settings if they exist
			if ( ! empty( $album->display_type_settings ) && is_array( $album->display_type_settings ) ) {
				// Get settings for the current display type
				$current_display_type = $display_params['display_type'];
				if ( isset( $album->display_type_settings[ $current_display_type ] ) ) {
					$display_params = array_merge( $display_params, $album->display_type_settings[ $current_display_type ] );
				}
			}

			// Set the album source
			$display_params['source']        = 'albums';
			$display_params['container_ids'] = $album_id;

			// Remove the 'album' parameter as it's not needed for rendering
			unset( $params['album'] );
		}

		// Allow shortcode parameters to override gallery/album settings
		$display_params = array_merge( $display_params, $params );

		return $display_params;
	}

	/**
	 * Enqueues frontend resources for alternative displayed gallery.
	 *
	 * @param DisplayedGallery $displayed_gallery The displayed gallery.
	 * @param Controller       $controller The controller instance.
	 */
	public static function enqueue_frontend_resources_for_alternate_displayed_gallery( $displayed_gallery, $controller ) {
		// Allow basic thumbnails "use imagebrowser effect" feature to seamlessly change between display types as well
		// as for album display types to show galleries.
		$alternate_displayed_gallery = $controller->get_alternative_displayed_gallery( $displayed_gallery );
		if ( $displayed_gallery === $alternate_displayed_gallery ) {
			return;
		}

		$alternate_controller = ControllerFactory::get_controller( $alternate_displayed_gallery->display_type );

		if ( ! $alternate_controller && class_exists( 'C_Display_Type_Controller', false ) ) {
			$alternate_controller = \C_Display_Type_Controller::get_instance( $alternate_displayed_gallery->display_type );
		}
		self::enqueue_frontend_resources_for_displayed_gallery( $alternate_displayed_gallery, $alternate_controller );
	}

	/**
	 * Enqueues frontend resources for a displayed gallery
	 *
	 * @param DisplayedGallery $displayed_gallery The displayed gallery object.
	 * @param Controller       $controller The controller instance.
	 * @return void
	 */
	public static function enqueue_frontend_resources_for_displayed_gallery( $displayed_gallery, $controller ) {
		if ( is_null( $displayed_gallery->id() ) ) {
			$displayed_gallery->id( md5( wp_json_encode( $displayed_gallery->get_entity() ) ) );
		}

		self::$enqueued_displayed_gallery_ids[] = $displayed_gallery->id();

		$controller->enqueue_frontend_resources( $displayed_gallery );

		if ( method_exists( $controller, 'get_alternative_displayed_gallery' ) ) {
			self::enqueue_frontend_resources_for_alternate_displayed_gallery( $displayed_gallery, $controller );
		}
	}

	/**
	 * Checks if this is a REST request
	 *
	 * @return bool
	 */
	public function is_rest_request(): bool {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Only checking for string presence
		return defined( 'REST_REQUEST' ) || false !== strpos( $_SERVER['REQUEST_URI'], 'wp-json' );
	}

	/**
	 * This moves the NextGen custom CSS to the last of the queue
	 *
	 * @return void
	 */
	public function fix_nextgen_custom_css_order() {
		global $wp_styles;
		if ( in_array( 'nggallery', $wp_styles->queue, true ) ) {
			foreach ( $wp_styles->queue as $ndx => $style ) {
				if ( 'nggallery' === $style ) {
					unset( $wp_styles->queue[ $ndx ] );
					$wp_styles->queue[] = 'nggallery';
					break;
				}
			}
		}
	}

	/**
	 * Enqueues FontAwesome library
	 *
	 * @return void
	 */
	public static function enqueue_fontawesome() {
		// The official plugin is active, we don't need to do anything outside the wp-admin.
		if ( defined( 'FONT_AWESOME_OFFICIAL_LOADED' ) && ! is_admin() ) {
			return;
		}

		$settings = Settings::get_instance();
		if ( $settings->get( 'disable_fontawesome' ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_register_script(
			'fontawesome_v4_shim',
			StaticAssets::get_url( 'FontAwesome/js/v4-shims.min.js' ),
			[],
			'5.3.1'
		);
		if ( ! wp_script_is( 'fontawesome', 'registered' ) ) {
			add_filter(
				'script_loader_tag',
				[ '\Imagely\NGG\Display\DisplayManager', 'fix_fontawesome_script_tag' ],
				10,
				2
			);
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
			wp_enqueue_script(
				'fontawesome',
				StaticAssets::get_url( 'FontAwesome/js/all.min.js' ),
				[ 'fontawesome_v4_shim' ],
				'5.3.1'
			);
		}

		if ( ! wp_style_is( 'fontawesome', 'registered' ) ) {
			wp_enqueue_style(
				'fontawesome_v4_shim_style',
				StaticAssets::get_url( 'FontAwesome/css/v4-shims.min.css' ),
				[],
				NGG_SCRIPT_VERSION
			);
			wp_enqueue_style(
				'fontawesome',
				StaticAssets::get_url( 'FontAwesome/css/all.min.css' ),
				[],
				NGG_SCRIPT_VERSION
			);
		}       wp_enqueue_script( 'fontawesome_v4_shim' );
		wp_enqueue_script( 'fontawesome' );
	}

	/**
	 * WP doesn't allow an easy way to set the defer, crossorign, or integrity attributes on our <script>
	 *
	 * @param string $tag The script tag HTML.
	 * @param string $handle The script handle.
	 * @return string Modified script tag.
	 */
	public static function fix_fontawesome_script_tag( $tag, $handle ) {
		if ( 'fontawesome' !== $handle ) {
			return $tag;
		}

		return str_replace( ' src', ' defer crossorigin="anonymous" data-auto-replace-svg="false" data-keep-original-source="false" data-search-pseudo-elements src', $tag );
	}

	/**
	 * Renders related images based on slug list
	 *
	 * @param array       $sluglist List of slugs.
	 * @param int|null    $max_images Maximum number of images.
	 * @param string|null $type Display type.
	 * @return string
	 */
	public static function _render_related_string( $sluglist = [], $max_images = null, $type = null ) {
		$settings = Settings::get_instance();
		if ( is_null( $type ) ) {
			$type = $settings->get( 'appendType' );
		}
		if ( is_null( $max_images ) ) {
			$max_images = $settings->get( 'maxImages' );
		}

		if ( ! $sluglist ) {
			switch ( $type ) {
				case 'tags':
					if ( function_exists( 'get_the_tags' ) ) {
						$taglist = \get_the_tags();
						if ( is_array( $taglist ) ) {
							foreach ( $taglist as $tag ) {
								$sluglist[] = $tag->slug;
							}
						}
					}
					break;
				case 'category':
					$catlist = \get_the_category();
					if ( is_array( $catlist ) ) {
						foreach ( $catlist as $cat ) {
							$sluglist[] = $cat->category_nicename;
						}
					}
					break;
			}
		}

		$taglist = implode( ',', $sluglist );

		if ( 'uncategorized' === $taglist || '' === $taglist ) {
			return '';
		}

		$renderer = Renderer::get_instance();
		$view     = new View( '', [], '' );
		$retval   = $renderer->display_images(
			[
				'source'                  => 'tags',
				'container_ids'           => $taglist,
				'display_type'            => NGG_BASIC_THUMBNAILS,
				'images_per_page'         => $max_images,
				'maximum_entity_count'    => $max_images,
				'template'                => $view->find_template_abspath( 'GalleryDisplay/Related', 'photocrati-nextgen_gallery_display#related' ),
				'show_all_in_lightbox'    => false,
				'show_slideshow_link'     => false,
				'disable_pagination'      => true,
				'display_no_images_error' => false,
			]
		);

		if ( $retval ) {
			\wp_enqueue_style( 'nextgen_gallery_related_images' );
		}

		return \apply_filters( 'ngg_show_related_gallery_content', $retval, $taglist );
	}

	/**
	 * Renders related images in post content
	 *
	 * @param string $content The post content.
	 * @return string
	 */
	public function _render_related_images( $content ) {
		$settings = Settings::get_instance();

		if ( $settings->get( 'activateTags' ) ) {
			$related = self::_render_related_string();

			if ( null !== $related ) {
				$heading  = $settings->get( 'relatedHeading' );
				$content .= $heading . $related;
			}
		}

		return $content;
	}

	/**
	 * Adds menu item to the admin bar
	 *
	 * @return void
	 */
	public function add_admin_bar_menu() {
		global $wp_admin_bar;

		// phpcs:ignore WordPress.WP.Capabilities.Unknown
		if ( \current_user_can( 'NextGEN Change options' ) ) {
			$wp_admin_bar->add_menu(
				[
					'parent' => 'ngg-menu',
					'id'     => 'ngg-menu-display_settings',
					'title'  => __( 'Gallery Settings', 'nggallery' ),
					'href'   => admin_url( 'admin.php?page=ngg_display_settings' ),
				]
			);
		}
	}

	/**
	 * Registers our static settings resources so the ATP module can find them later
	 *
	 * @return void
	 */
	public function register_resources() {
		// Register custom post types for compatibility.
		$types = [
			'displayed_gallery'  => 'NextGEN Gallery - Displayed Gallery',
			'display_type'       => 'NextGEN Gallery - Display Type',
			'gal_display_source' => 'NextGEN Gallery - Displayed Gallery Source',
		];

		foreach ( $types as $type => $label ) {
			\register_post_type(
				$type,
				[
					'label'               => $label,
					'publicly_queryable'  => false,
					'exclude_from_search' => true,
				]
			);
		}

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		\wp_register_script(
			'shave.js',
			StaticAssets::get_url(
				'GalleryDisplay/shave.js',
				'photocrati-nextgen_gallery_display#shave.js'
			),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_register_style(
			'nextgen_gallery_related_images',
			StaticAssets::get_url(
				'GalleryDisplay/nextgen_gallery_related_images.css',
				'photocrati-nextgen_gallery_display#nextgen_gallery_related_images.css'
			),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_register_script(
			'ngg_common',
			StaticAssets::get_url(
				'GalleryDisplay/common.js',
				'photocrati-nextgen_gallery_display#common.js'
			),
			[ 'jquery', 'photocrati_ajax' ],
			NGG_SCRIPT_VERSION,
			true
		);

		\wp_register_style(
			'ngg_trigger_buttons',
			StaticAssets::get_url(
				'GalleryDisplay/trigger_buttons.css',
				'photocrati-nextgen_gallery_display#trigger_buttons.css'
			),
			[],
			NGG_SCRIPT_VERSION
		);

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		\wp_register_style(
			'ngg_video_play_overlay',
			StaticAssets::get_url(
				'GalleryDisplay/video_play_overlay.css',
				'photocrati-nextgen_gallery_display#video_play_overlay.css'
			),
			[],
			NGG_SCRIPT_VERSION
		);

		// Video: lightbox styles for multi-platform video support.
		\wp_register_style(
			'ngg_video_lightbox',
			StaticAssets::get_url(
				'Lightbox/nextgen_video.css',
				'photocrati-lightbox#nextgen_video.css'
			),
			[],
			NGG_SCRIPT_VERSION
		);

		// TikTok: video player and link handler script (needed for link override functionality).
		\wp_register_script(
			'ngg_tiktok_video',
			StaticAssets::get_url(
				'Lightbox/ngg-tiktok-video.js',
				'photocrati-lightbox#ngg-tiktok-video.js'
			),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION,
			true
		);

		// Video: helper script for multi-platform video support (YouTube, Vimeo, Dailymotion, Twitch, VideoPress, Wistia, Local).
		\wp_register_script(
			'ngg_video_helper',
			StaticAssets::get_url(
				'Lightbox/nextgen_video_helper.js',
				'photocrati-lightbox#nextgen_video_helper.js'
			),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION,
			true
		);

		\wp_register_script(
			'ngg_waitforimages',
			StaticAssets::get_url(
				'GalleryDisplay/jquery.waitforimages-2.4.0-modded.js',
				'photocrati-nextgen_gallery_display#jquery.waitforimages-2.4.0-modded.js'
			),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION,
			true
		);

		$settings = Settings::get_instance();
		$router   = Router::get_instance();

		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		\wp_register_script(
			'photocrati_ajax',
			StaticAssets::get_url( 'Legacy/ajax.min.js', 'photocrati-ajax#ajax.min.js' ),
			[ 'jquery' ],
			NGG_SCRIPT_VERSION
		);

		$vars = [
			'url'             => $settings->get( 'ajax_url' ),
			'rest_url'        => get_rest_url(),
			'wp_home_url'     => $router->get_base_url( 'home' ),
			'wp_site_url'     => $router->get_base_url( 'site' ),
			'wp_root_url'     => $router->get_base_url( 'root' ),
			'wp_plugins_url'  => $router->get_base_url( 'plugins' ),
			'wp_content_url'  => $router->get_base_url( 'content' ),
			'wp_includes_url' => includes_url(),
			'ngg_param_slug'  => $settings->get( 'router_param_slug', 'nggallery' ),
			'rest_nonce'      => wp_create_nonce( 'wp_rest' ),
		];

		\wp_localize_script( 'photocrati_ajax', 'photocrati_ajax', $vars );

		// GA4 tracking - loads when addon enabled and measurement ID set.
		\wp_register_script(
			'ngg_ga4_tracking',
			StaticAssets::get_url( 'Integrations/nextgen-ga4-tracking.js' ),
			[],
			NGG_SCRIPT_VERSION,
			true
		);
	}

	/**
	 * Enqueue GA4 tracking script when Pro/Plus/Starter is active, addon is enabled, and Measurement ID is configured.
	 * Assumes gtag.js is already on the page (MonsterInsights, Site Kit, or manual install).
	 */
	public function maybe_enqueue_ga4_tracking() {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}
		if ( ( defined( 'NGG_SKIP_LOAD_SCRIPTS' ) && NGG_SKIP_LOAD_SCRIPTS ) || $this->is_rest_request() ) {
			return;
		}
		// Google Analytics is a Pro feature - only load when Pro/Plus/Starter is active.
		if ( 'lite' === \Imagely\NGG\Admin\App::get_pro_type_installed() ) {
			return;
		}
		if ( ! AddonsREST::is_addon_enabled( 'google_analytics' ) ) {
			return;
		}

		$settings         = Settings::get_instance();
		$measurement_id   = $settings->get( 'ga4_measurement_id', '' );
		$measurement_id   = is_string( $measurement_id ) ? trim( $measurement_id ) : '';
		if ( empty( $measurement_id ) ) {
			return;
		}

		$track = [
			'gallery_view'   => (bool) $settings->get( 'ga4_track_gallery_views', false ),
			'image_click'    => (bool) $settings->get( 'ga4_track_image_clicks', false ),
			'image_download' => (bool) $settings->get( 'ga4_track_image_downloads', false ),
			'add_to_cart'    => (bool) $settings->get( 'ga4_track_add_to_cart', false ),
			'purchase'       => (bool) $settings->get( 'ga4_track_purchases', false ),
		];

		wp_enqueue_script( 'ngg_ga4_tracking' );
		wp_localize_script(
			'ngg_ga4_tracking',
			'ngg_ga4_config',
			[
				'measurement_id' => $measurement_id,
				'track'          => $track,
			]
		);
	}

	/**
	 * Provides the [ngg] and [ngg_images] shortcodes
	 *
	 * @param array  $params Display parameters.
	 * @param string $inner_content Inner shortcode content.
	 * @return string
	 */
	public function display_images( $params, $inner_content = null ) {
		$renderer = Renderer::get_instance();
		return $renderer->display_images( $params, $inner_content );
	}

	/**
	 * Gets a list of directories in which display type template might be stored
	 *
	 * @param DisplayType|string $display_type The display type object or name.
	 * @return array
	 */
	public static function get_display_type_view_dirs( $display_type ) {
		if ( is_string( $display_type ) ) {
			$display_type = DisplayTypeMapper::get_instance()->find_by_name( $display_type );
		}

		// Create an array of directories to scan.
		$dirs = [];

		if ( ControllerFactory::has_controller( $display_type->name ) ) {
			$controller      = ControllerFactory::get_controller( $display_type->name );
			$dirs['default'] = $controller->get_template_directory_abspath();
		}

		if ( empty( $dirs['default'] ) ) {
			// In case the module path cannot be determined, we must intervene here if the resulting string is just '/templates'
			// which is a place where template files should not be stored.
			$directory = \C_NextGEN_Bootstrap::get_legacy_module_directory( $display_type->name ) . DIRECTORY_SEPARATOR . 'templates';
			if ( '/templates' !== $directory ) {
				$dirs['default'] = $directory;
			}
		}

		$dirs['custom'] = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'ngg' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $display_type->name . DIRECTORY_SEPARATOR . 'templates';

		/* Apply filters so third party devs can add directories for their templates */
		$dirs = \apply_filters( 'ngg_display_type_template_dirs', $dirs, $display_type );
		$dirs = \apply_filters( 'ngg_' . $display_type->name . '_template_dirs', $dirs, $display_type );
		foreach ( $display_type->aliases as $alias ) {
			$dirs = \apply_filters( "ngg_{$alias}_template_dirs", $dirs, $display_type );
		}

		return $dirs;
	}

	/**
	 * Adds data to the DOM which is then accessible by a script
	 *
	 * @param string $handle The script handle.
	 * @param string $object_name The object name.
	 * @param mixed  $object_value The object value.
	 * @param bool   $define Whether to use 'var' or not.
	 * @param bool   $override Whether to override existing data.
	 * @return bool
	 */
	public static function add_script_data( $handle, $object_name, $object_value, $define = true, $override = false ) {
		global $wp_scripts;

		$retval = false;

		// wp_localize_script allows you to add data to the DOM, associated with a particular script. You can even
		// call wp_localize_script multiple times to add multiple objects to the DOM. However, there are a few problems
		// with wp_localize_script:
		// - If you call it with the same object_name more than once, you're overwriting the first call.
		// - You cannot namespace your objects due to the "var" keyword always being used.
		//
		// To circumvent the above issues, we're going to use the WP_Scripts object to work around the above issues.

		// Has the script been registered or enqueued?
		if ( isset( $wp_scripts->registered[ $handle ] ) ) {
			// Get the associated data with this script.
			$script = &$wp_scripts->registered[ $handle ];
			$data   = isset( $script->extra['data'] ) ? $script->extra['data'] : '';

			// Construct the addition.
			$addition = $define ? "\nvar {$object_name} = " . wp_json_encode( $object_value ) . ';' : "\n{$object_name} = " . wp_json_encode( $object_value ) . ';';

			// Add the addition.
			if ( $override ) {
				$data  .= $addition;
				$retval = true;
			} elseif ( strpos( $data, $object_name ) === false ) {
				$data  .= $addition;
				$retval = true;
			}

			$script->extra['data'] = $data;

			unset( $script );
		}

		return $retval;
	}

	/**
	 * Provides the [ngg_gallery] shortcode that loads display settings from the gallery
	 *
	 * @param array  $params Shortcode parameters, expects 'id' parameter
	 * @param string $inner_content Inner content of the shortcode
	 * @return string Rendered gallery output
	 */
	public function display_gallery_by_id( $params, $inner_content = null ) {
		// Default parameters
		$params = shortcode_atts(
			[
				'id' => 0,
			],
			$params
		);

		$gallery_id = intval( $params['id'] );

		if ( ! $gallery_id ) {
			return '<!-- NGG Gallery Error: No gallery ID provided -->';
		}

		// Load the gallery from the database
		$gallery_mapper = GalleryMapper::get_instance();
		$gallery        = $gallery_mapper->find( $gallery_id );

		if ( ! $gallery ) {
			return '<!-- NGG Gallery Error: Gallery not found -->';
		}

		// If the gallery uses an external source addon that is not enabled, don't render.
		if ( ! AddonsREST::can_render_gallery( $gallery ) ) {
			return '';
		}

		// Start with the gallery's display type settings
		$display_params = [];

		// Set the display type
		if ( ! empty( $gallery->display_type ) ) {
			$display_params['display_type'] = $gallery->display_type;
		}

		// Merge in the display type settings
		if ( ! empty( $gallery->display_type_settings ) && is_array( $gallery->display_type_settings ) ) {
			// Get settings for the current display type
			$current_display_type = ! empty( $gallery->display_type ) ? $gallery->display_type : 'photocrati-nextgen_basic_thumbnails';
			if ( isset( $gallery->display_type_settings[ $current_display_type ] ) ) {
				$display_params = array_merge( $display_params, $gallery->display_type_settings[ $current_display_type ] );
			}
		}

		// Ensure ecommerce is enabled when set on the gallery record
		if ( ! empty( $gallery->is_ecommerce_enabled ) ) {
			$display_params['is_ecommerce_enabled'] = 1;
		}

		// Set the gallery source
		$display_params['source']        = 'galleries';
		$display_params['container_ids'] = $gallery_id;

		// Allow shortcode parameters to override gallery settings
		$display_params = array_merge( $display_params, $params );

		// Remove the 'id' parameter as it's not needed for rendering
		unset( $display_params['id'] );

		// Use the standard renderer to display the gallery
		$renderer = Renderer::get_instance();
		return $renderer->display_images( $display_params, $inner_content );
	}

	/**
	 * Handles the imagely shortcode for both galleries and albums
	 *
	 * @param array  $params        Shortcode parameters
	 * @param string $inner_content Inner content (unused)
	 * @return string Rendered output
	 */
	public function display_imagely_shortcode( $params, $inner_content = null ) {
		// Check if this is a gallery or album shortcode
		if ( isset( $params['album'] ) ) {
			return $this->display_album_by_id( $params, $inner_content );
		} else {
			return $this->display_gallery_by_id( $params, $inner_content );
		}
	}

	/**
	 * Displays an album using the imagely shortcode
	 *
	 * @param array  $params        Shortcode parameters
	 * @param string $inner_content Inner content (unused)
	 * @return string Rendered album output
	 */
	public function display_album_by_id( $params, $inner_content = null ) {
		// Default parameters
		$params = shortcode_atts(
			[
				'album' => 0,
			],
			$params
		);

		$album_id = intval( $params['album'] );

		if ( ! $album_id ) {
			return '<!-- NGG Album Error: No album ID provided -->';
		}

		// Load the album from the database
		$album_mapper = AlbumMapper::get_instance();
		$album        = $album_mapper->find( $album_id );

		if ( ! $album ) {
			return '<!-- NGG Album Error: Album not found -->';
		}

		// Start with the album's display type settings
		$display_params = [];

		// photocrati-nextgen_basic_thumbnails was an incorrect column default in the Album data mapper.
		// Treat it as absent and fall back to the compact album display type.
		$stored_display_type = ! empty( $album->display_type ) ? $album->display_type : '';
		if ( empty( $stored_display_type ) || NGG_BASIC_THUMBNAILS === $stored_display_type ) {
			$display_params['display_type'] = NGG_BASIC_COMPACT_ALBUM;
		} else {
			$display_params['display_type'] = $stored_display_type;
		}

		// Merge in the display type settings if they exist
		if ( ! empty( $album->display_type_settings ) && is_array( $album->display_type_settings ) ) {
			// Get settings for the current display type
			$current_display_type = $display_params['display_type'];
			if ( isset( $album->display_type_settings[ $current_display_type ] ) ) {
				$display_params = array_merge( $display_params, $album->display_type_settings[ $current_display_type ] );
			}
		}

		// Set the album source
		$display_params['source']        = 'albums';
		$display_params['container_ids'] = $album_id;

		// Allow shortcode parameters to override album settings
		$display_params = array_merge( $display_params, $params );

		// Remove the 'album' parameter as it's not needed for rendering
		unset( $display_params['album'] );

		// Use the standard renderer to display the album
		$renderer = Renderer::get_instance();
		return $renderer->display_images( $display_params, $inner_content );
	}
}
