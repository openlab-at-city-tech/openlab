<?php

namespace Imagely\NGG\DisplayedGallery;

use Imagely\NGG\DataMappers\DisplayedGallery as DisplayedGalleryMapper;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\Display\DisplayManager;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Router;
use Imagely\NGG\Util\Transient;

class Renderer {

	protected static $instances = [];
	protected static $cache     = [];

	protected static $has_done_app_rewrite = false;

	/**
	 * @param bool|string $context
	 * @return Renderer
	 */
	public static function get_instance( $context = false ) {
		if ( ! isset( self::$instances[ $context ] ) ) {
			self::$instances[ $context ] = new Renderer();
		}
		return self::$instances[ $context ];
	}

	/**
	 * @param array $params
	 * @return DisplayedGallery
	 */
	public function params_to_displayed_gallery( $params ) {
		$hash = crc32( serialize( $params ) );
		if ( isset( self::$cache[ $hash ] ) ) {
			return self::$cache[ $hash ];
		}

		// Get the NextGEN settings to provide some defaults.
		$settings = Settings::get_instance();

		// Perform some conversions...
		if ( isset( $params['galleries'] ) ) {
			$params['gallery_ids'] = $params['galleries'];
			unset( $params['galleries'] );
		}
		if ( isset( $params['albums'] ) ) {
			$params['album_ids'] = $params['albums'];
			unset( $params['albums'] );
		}

		// Configure the arguments.
		$defaults = [
			'album_ids'       => [],
			'container_ids'   => [],
			'display'         => '',
			'display_type'    => '',
			'entity_ids'      => [],
			'exclusions'      => [],
			'gallery_ids'     => [],
			'id'              => null,
			'ids'             => null,
			'image_ids'       => [],
			'order_by'        => $settings->get( 'galSort' ),
			'order_direction' => $settings->get( 'galSortOrder' ),
			'returns'         => 'included',
			'slug'            => null,
			'sortorder'       => [],
			'source'          => '',
			'src'             => '',
			'tag_ids'         => [],
			'tagcloud'        => false,
		];

		$args = shortcode_atts( $defaults, $params, 'ngg' );

		// Are we loading a specific (legacy) displayed gallery that's persisted?
		$mapper = DisplayedGalleryMapper::get_instance();
		if ( ! is_null( $args['id'] ) ) {
			$displayed_gallery = $mapper->find( $args['id'], true );
			unset( $mapper );
		}

		// We're generating a new displayed gallery.
		else {
			// Galleries?
			if ( $args['gallery_ids'] ) {
				if ( $args['source'] != 'albums' and $args['source'] != 'album' ) {
					$args['source']        = 'galleries';
					$args['container_ids'] = $args['gallery_ids'];
					if ( $args['image_ids'] ) {
						$args['entity_ids'] = $args['image_ids'];
					}
				} elseif ( $args['source'] == 'albums' ) {
					$args['entity_ids'] = $args['gallery_ids'];
				}
				unset( $args['gallery_ids'] );
			}

			// Albums ?
			elseif ( $args['album_ids'] || $args['album_ids'] === '0' ) {
				$args['source']        = 'albums';
				$args['container_ids'] = $args['album_ids'];
				unset( $args['albums_ids'] );
			}

			// Tags ?
			elseif ( $args['tag_ids'] ) {
				$args['source']        = 'tags';
				$args['container_ids'] = $args['tag_ids'];
				unset( $args['tag_ids'] );
			}

			// Specific images selected.
			elseif ( $args['image_ids'] ) {
				$args['source']     = 'galleries';
				$args['entity_ids'] = $args['image_ids'];
				unset( $args['image_ids'] );
			}

			// Tagcloud support.
			elseif ( $args['tagcloud'] ) {
				$args['source'] = 'tags';
			}

			// Convert strings to arrays.
			if ( ! empty( $args['ids'] ) && ! is_array( $args['ids'] ) ) {
				$args['container_ids'] = preg_split( '/,|\|/', $args['ids'] );
				unset( $args['ids'] );
			}

			if ( ! is_array( $args['container_ids'] ) ) {
				$args['container_ids'] = preg_split( '/,|\|/', $args['container_ids'] );
			}

			if ( ! is_array( $args['exclusions'] ) ) {
				$args['exclusions'] = preg_split( '/,|\|/', $args['exclusions'] );
			}

			if ( ! is_array( $args['entity_ids'] ) ) {
				$args['entity_ids'] = preg_split( '/,|\|/', $args['entity_ids'] );
			}

			if ( ! is_array( $args['sortorder'] ) ) {
				$args['sortorder'] = preg_split( '/,|\|/', $args['sortorder'] );
			}

			// 'src' is used for legibility.
			if ( ! empty( $args['src'] ) && empty( $args['source'] ) ) {
				$args['source'] = $args['src'];
				unset( $args['src'] );
			}

			// 'display' is used for legibility.
			if ( ! empty( $args['display'] ) && empty( $args['display_type'] ) ) {
				$args['display_type'] = $args['display'];
				unset( $args['display'] );
			}

			// Get the display settings.
			foreach ( array_keys( $defaults ) as $key ) {
				unset( $params[ $key ] );
			}

			$args['display_settings'] = $params;

			// Create the displayed gallery.
			$displayed_gallery = new DisplayedGallery( (object) $args );
		}

		// Cache for reuse.
		self::$cache[ $hash ] = $displayed_gallery;

		return $displayed_gallery;
	}

	/**
	 * Displays a "displayed gallery" instance
	 *
	 * Alias Properties:
	 * gallery_ids/album_ids/tag_ids == container_ids
	 * image_ids/gallery_ids         == entity_ids
	 *
	 * Default Behavior:
	 * - if order_by and order_direction are missing, the default settings
	 *   are used from the "Other Options" page. The exception to this is
	 *   when entity_ids are selected, in which the order is custom unless
	 *   specified.
	 *
	 * How to use:
	 *
	 * To retrieve images from gallery 1 & 3, but exclude images 4 & 6:
	 * [ngg gallery_ids="1,3" exclusions="4,6" display_type="photocrati-nextgen_basic_thumbnails"]
	 *
	 * To retrieve images 1 & 2 from gallery 1:
	 * [ngg gallery_ids="1" image_ids="1,2" display_type="photocrati-nextgen_basic_thumbnails"]
	 *
	 * To retrieve images matching tags "landscapes" and "wedding shoots":
	 * [ngg tag_ids="landscapes,wedding shoots" display_type="photocrati-nextgen_basic_thumbnails"]
	 *
	 * To retrieve galleries from albums 1 & #, but exclude sub-album 1:
	 * [ngg album_ids="1,2" exclusions="a1" display_type="photocrati-nextgen_basic_compact_album"]
	 *
	 * To retrieve galleries from albums 1 & 2, but exclude gallery 1:
	 * [ngg album_ids="1,2" exclusions="1" display_type="photocrati-nextgen_basic_compact_album"]
	 *
	 * To retrieve image 2, 3, and 5 - independent of what container is used
	 * [ngg image_ids="2,3,5" display_type="photocrati-nextgen_basic_thumbnails"]
	 *
	 * To retrieve galleries 3 & 5, custom sorted, in album view
	 * [ngg source="albums" gallery_ids="3,5" display_type="photocrati-nextgen_basic_compact_album"]
	 *
	 * To retrieve recent images, sorted by alt/title text
	 * [ngg source="recent" order_by="alttext" display_type="photocrati-nextgen_basic_thumbnails"]
	 *
	 * To retrieve random image
	 * [ngg source="random" display_type="photocrati-nextgen_basic_thumbnails"]
	 *
	 * To retrieve a single image
	 * [ngg image_ids='8' display_type='photocrati-nextgen_basic_singlepic']
	 *
	 * To retrieve a tag cloud
	 * [ngg tagcloud=yes display_type='photocrati-nextgen_basic_tagcloud']
	 *
	 * @param array|DisplayedGallery $params_or_dg
	 * @param null|string            $inner_content (optional)
	 * @return string
	 */
	public function display_images( $params_or_dg, $inner_content = null ) {
		// Convert the array of parameters into a displayed gallery.
		if ( is_array( $params_or_dg ) ) {
			$params            = $params_or_dg;
			$displayed_gallery = $this->params_to_displayed_gallery( $params );
		} elseif ( is_object( $params_or_dg ) && get_class( $params_or_dg ) === 'Imagely\NGG\DataTypes\DisplayedGallery' ) {
			// We've already been given a displayed gallery.
			$displayed_gallery = $params_or_dg;
		} else {
			// Something has gone wrong; the request cannot be rendered.
			$displayed_gallery = null;
		}

		// Validate the displayed gallery.
		if ( $displayed_gallery && $displayed_gallery->validation() ) {
			$retval = $this->render( $displayed_gallery, true );
		} elseif ( \C_NextGEN_Bootstrap::$debug ) {
				$retval = __( 'We cannot display this gallery', 'nggallery' ) . $this->debug_msg( $displayed_gallery->validation() ) . $this->debug_msg( $displayed_gallery->get_entity() );
		} else {
			$retval = __( 'We cannot display this gallery', 'nggallery' );
		}

		return $retval;
	}

	public function debug_msg( $msg, $print_r = false ) {
		$retval = '';

		if ( \C_NextGEN_Bootstrap::$debug ) {
			ob_start();
			if ( $print_r ) {
				echo '<pre>';
				print_r( $msg );
				echo '</pre>';
			} else {
				var_dump( $msg );
			}

			$retval = ob_get_clean();
		}

		return $retval;
	}

	/**
	 * @return bool
	 */
	public function is_rest_request() {
		return defined( 'REST_REQUEST' ) || strpos( $_SERVER['REQUEST_URI'], 'wp-json' ) !== false;
	}

	/**
	 * Renders a displayed gallery on the frontend
	 *
	 * @param DisplayedGallery $displayed_gallery
	 * @param bool             $return
	 * @return string
	 */
	public function render( $displayed_gallery, $return = false ) {
		// Simply throwing our rendered gallery into a feed will most likely not work correctly.
		// The MediaRSS option in NextGEN is available as an alternative.
		if ( ! Settings::get_instance()->get( 'galleries_in_feeds' ) && is_feed() ) {
			return sprintf(
				__( ' [<a href="%1$s">See image gallery at %2$s</a>] ', 'nggallery' ),
				esc_url( apply_filters( 'the_permalink_rss', get_permalink() ) ),
				$_SERVER['SERVER_NAME']
			);
		}

		$retval = '';

		if ( is_null( $displayed_gallery->id() ) ) {
			$displayed_gallery->id( md5( json_encode( $displayed_gallery->get_entity() ) ) );
		}

		$this->do_app_rewrites( $displayed_gallery );

		$controller = ControllerFactory::get_controller( $displayed_gallery->display_type );
		if ( ! $controller && class_exists( 'C_Component_Registry' ) && class_exists( 'C_Display_Type_Controller' ) ) {
			$controller = \C_Component_Registry::get_instance()->get_utility( 'I_Display_Type_Controller', $displayed_gallery->display_type );
		}

		if ( ! $controller ) {
			return $retval;
		}

		if ( method_exists( $controller, 'get_alternative_displayed_gallery' ) ) {
			$alt_displayed_gallery = $controller->get_alternative_displayed_gallery( $displayed_gallery );
			if ( $alt_displayed_gallery !== $displayed_gallery ) {
				$controller        = ControllerFactory::get_controller( $alt_displayed_gallery->display_type );
				$displayed_gallery = $alt_displayed_gallery;
			}
		}

		// Get routing info.
		$router = Router::get_instance();
		$app    = $router->get_routed_app();
		$url    = $router->get_url( $router->get_request_uri(), true );
		$lookup = true;

		// Should we check the cache?
		if ( is_array( $displayed_gallery->container_ids ) && in_array( 'All', $displayed_gallery->container_ids ) ) {
			$lookup = false;
		} elseif ( $displayed_gallery->source == 'albums' && ( $app->get_parameter( 'gallery' ) ) or $app->get_parameter( 'album' ) ) {
			$lookup = false;
		} elseif ( in_array( $displayed_gallery->source, [ 'random', 'random_images' ] ) ) {
			$lookup = false;
		} elseif ( $app->get_parameter( 'show' ) ) {
			$lookup = false;
		} elseif ( $controller->is_cachable() === false ) {
			$lookup = false;
		} elseif ( ! NGG_RENDERING_CACHE_ENABLED ) {
			$lookup = false;
		}

		// Just in case M_Gallery_Display could not find this displayed gallery during wp_enqueue_scripts (most likely
		// because this displayed gallery was created through do_shortcode) we'll enqueue it now. This may potentially
		// cause issues with displays adding their JS or CSS after the <body> has began or finished.
		if ( ( ! defined( 'NGG_SKIP_LOAD_SCRIPTS' ) || ! NGG_SKIP_LOAD_SCRIPTS )
		&& ! $this->is_rest_request()
		&& ! in_array( $displayed_gallery->id(), DisplayManager::$enqueued_displayed_gallery_ids ) ) {
			DisplayManager::$enqueued_displayed_gallery_ids[] = $displayed_gallery->id();
			$controller->enqueue_frontend_resources( $displayed_gallery );
		}

		// Try cache lookup, if we're to do so.
		$key  = null;
		$html = false;
		if ( $lookup ) {
			// The display type may need to output some things even when serving from the cache.
			if ( method_exists( $controller, 'cache_action' ) ) {
				$retval = $controller->cache_action( $displayed_gallery );
			}

			// Output debug message.
			$retval .= $this->debug_msg( 'Lookup!' );

			// Some settings affect display types.
			$settings   = Settings::get_instance();
			$key_params = apply_filters(
				'ngg_displayed_gallery_cache_params',
				[
					$displayed_gallery->get_entity(),
					$url,
					$settings->get( 'activateTags' ),
					$settings->get( 'appendType' ),
					$settings->get( 'maxImages' ),
					$settings->get( 'thumbEffect' ),
					$settings->get( 'thumbCode' ),
					$settings->get( 'galSort' ),
					$settings->get( 'galSortDir' ),
					NGG_PLUGIN_VERSION,
				]
			);

			// Any displayed gallery links on the home page will need to be regenerated if the permalink structure changes.
			if ( is_home() or is_front_page() ) {
				$key_params[] = get_option( 'permalink_structure' );
			}

			// Try getting the rendered HTML from the cache.
			$key  = Transient::create_key( 'displayed_gallery_rendering', $key_params );
			$html = Transient::fetch( $key, false );
		} else {
			$retval .= $this->debug_msg( 'Not looking up in cache as per rules' );
		}

		// TODO: This is hack. We need to figure out a more uniform way of detecting dynamic image urls.
		if ( strpos( $html, Settings::get_instance()->get( 'dynamic_thumbnail_slug' ) . '/' ) !== false ) {
			$html = false; // forces the cache to be re-generated.
		}

		// Output debug messages.
		if ( $html ) {
			$retval .= $this->debug_msg( 'HIT!' );
		} else {
			$retval .= $this->debug_msg( 'MISS!' );
		}

		// If a cached version doesn't exist, then create the cache.
		if ( ! $html ) {
			$retval .= $this->debug_msg( 'Rendering displayed gallery' );

			$html = apply_filters(
				'ngg_displayed_gallery_rendering',
				$controller->index_action( $displayed_gallery, true ),
				$displayed_gallery
			);

			if ( $key != null ) {
				Transient::update( $key, $html, NGG_RENDERING_CACHE_TTL );
			}
		}

		$retval .= $html;

		if ( ! $return ) {
			echo $retval;
		}

		return $retval;
	}

	public function do_app_rewrites( $displayed_gallery ) {
		if ( self::$has_done_app_rewrite ) {
			return;
		}
		self::$has_done_app_rewrite = true;

		$do_rewrites = false;
		$app         = null;

		// Get display types.
		$original_display_type = isset( $displayed_gallery->display_settings['original_display_type'] ) ? $displayed_gallery->display_settings['original_display_type'] : '';
		$display_type          = $displayed_gallery->display_type;

		$router = Router::get_instance();

		// If we're viewing an album, rewrite the urls.
		$regex = '/photocrati-nextgen_basic_\\w+_album/';
		if ( preg_match( $regex, $display_type ) ) {
			$do_rewrites = true;

			$app  = $router->get_routed_app();
			$slug = '/' . Settings::get_instance()->get( 'router_param_slug', 'nggallery' );

			$app->rewrite( "{*}{$slug}/page/{\\d}{*}", "{1}{$slug}/nggpage--{2}{3}", false, true );
			$app->rewrite( "{*}{$slug}/pid--{*}", "{1}{$slug}/pid--{2}", false, true ); // avoid conflicts with imagebrowser.
			$app->rewrite( "{*}{$slug}/{\\w}/{\\w}/{\\w}{*}", "{1}{$slug}/album--{2}/gallery--{3}/{4}{5}", false, true );
			$app->rewrite( "{*}{$slug}/{\\w}/{\\w}", "{1}{$slug}/album--{2}/gallery--{3}", false, true );
		} elseif ( preg_match( $regex, $original_display_type ) ) {
			$do_rewrites = true;

			// Get router.
			$app  = $router->get_routed_app();
			$slug = '/' . Settings::get_instance()->get( 'router_param_slug', 'nggallery' );

			$app->rewrite( "{*}{$slug}/album--{\\w}", "{1}{$slug}/{2}" );
			$app->rewrite( "{*}{$slug}/album--{\\w}/gallery--{\\w}", "{1}{$slug}/{2}/{3}" );
			$app->rewrite( "{*}{$slug}/album--{\\w}/gallery--{\\w}/{*}", "{1}{$slug}/{2}/{3}/{4}" );
		}

		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$pro_album_types = [
				'photocrati-nextgen_pro_albums',
				'photocrati-nextgen_pro_grid_album',
				'photocrati-nextgen_pro_list_album',
			];

			if ( in_array( $displayed_gallery->display_type, $pro_album_types ) ) {
				$do_rewrites = true;

				$app  = $router->get_routed_app();
				$slug = '/' . Settings::get_instance()->get( 'router_param_slug' );

				// ensure to pass $stop=TRUE to $app->rewrite() on parameters that may be shared with other display types.
				$app->rewrite( '{*}' . $slug . '/page/{\d}{*}', '{1}' . $slug . '/nggpage--{2}{3}', false, true );
				$app->rewrite( '{*}' . $slug . '/page--{*}', '{1}' . $slug . '/nggpage--{2}', false, true );
				$app->rewrite( '{*}' . $slug . '/{\w}', '{1}' . $slug . '/album--{2}' );
				$app->rewrite( '{*}' . $slug . '/{\w}/{\w}', '{1}' . $slug . '/album--{2}/gallery--{3}' );
				$app->rewrite( '{*}' . $slug . '/{\w}/{\w}/{\w}{*}', '{1}' . $slug . '/album--{2}/gallery--{3}/{4}{5}' );
			} elseif ( in_array( $original_display_type, $pro_album_types ) ) {
				$do_rewrites = true;

				$app  = $router->get_routed_app();
				$slug = '/' . Settings::get_instance()->get( 'router_param_slug' );

				$app->rewrite( "{*}{$slug}/album--{\\w}", "{1}{$slug}/{2}" );
				$app->rewrite( "{*}{$slug}/album--{\\w}/gallery--{\\w}", "{1}{$slug}/{2}/{3}" );
				$app->rewrite( "{*}{$slug}/album--{\\w}/gallery--{\\w}/{*}", "{1}{$slug}/{2}/{3}/{4}" );
			}
		}

		do_action( 'ngg_do_app_rewrites', $displayed_gallery );

		// Perform rewrites.
		if ( $do_rewrites && $app ) {
			$app->do_rewrites();
		}
	}
}
