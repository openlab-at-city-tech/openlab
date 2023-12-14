<?php

namespace Imagely\NGG\DisplayTypes\Albums;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DisplayType\Controller as ParentController;
use Imagely\NGG\DynamicThumbnails\Manager as ThumbnailsManager;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\{DisplayManager, LightboxManager, View, ViewElement, StaticAssets};
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\Util\Router;

class SharedController extends ParentController {

	public $albums = [];

	public static $alternate_displayed_galleries = [];

	public $breadcrumb_cache = [];
	public $entities         = [];

	public $template        = '';
	public $legacy_template = '';

	// When viewing a child gallery the album controller's add_description_to_legacy_templates() method will be
	// called for the gallery and then again for the root album; we only want to run once.
	public static $_description_added_once = false;

	public function add_breadcrumbs_and_descriptions( ViewElement $root_element, DisplayedGallery $displayed_gallery ): ViewElement {
		$ds = $displayed_gallery->display_settings;

		// Enable album breadcrumbs.
		if ( $this->are_breadcrumbs_enabled( $ds ) && ( $original_entities = $this->get_original_album_entities( $ds ) ) ) {
			if ( ! empty( $ds['original_album_id'] ) ) {
				$ids = $ds['original_album_id'];
			} else {
				$ids = $displayed_gallery->container_ids;
			}

			$breadcrumbs = $this->generate_breadcrumb( $ids, $original_entities );
			foreach ( $root_element->find( 'nextgen_gallery.gallery_container', true ) as $container ) {
				$container->insert( $breadcrumbs );
			}
		}

		// Enable album descriptions.
		if ( $this->are_descriptions_enabled( $ds ) ) {
			$description = $this->generate_description( $displayed_gallery );

			foreach ( $root_element->find( 'nextgen_gallery.gallery_container', true ) as $container ) {
				// Determine where (to be compatible with breadcrumbs) in the container to insert.
				$pos = 0;
				foreach ( $container->_list as $ndx => $item ) {
					if ( is_string( $item ) ) {
						$pos = $ndx;
					} else {
						break;
					}
				}

				$container->insert( $description, $pos );
			}
		}

		return $root_element;
	}

	public function add_breadcrumbs_to_legacy_templates( $html, $displayed_gallery ) {
		$original_album_entities = [];
		if ( isset( $displayed_gallery->display_settings['original_album_entities'] ) ) {
			$original_album_entities = $displayed_gallery->display_settings['original_album_entities'];
		} elseif ( isset( $displayed_gallery->display_settings['original_settings'] ) && isset( $displayed_gallery->display_settings['original_settings']['original_album_entities'] ) ) {
			$original_album_entities = $displayed_gallery->display_settings['original_settings']['original_album_entities'];
		}

		$breadcrumbs = $this->render_legacy_template_breadcrumbs(
			$displayed_gallery,
			$original_album_entities,
			$displayed_gallery->container_ids
		);

		if ( ! empty( $breadcrumbs ) ) {
			$html = $breadcrumbs . $html;
		}

		return $html;
	}

	public function add_description_to_legacy_templates( $html, $displayed_gallery ) {
		$description = $this->render_legacy_template_description( $displayed_gallery );

		if ( ! empty( $description ) ) {
			$html = $description . $html;
		}

		return $html;
	}

	public function are_breadcrumbs_enabled( $display_settings ): bool{
		$retval = false;

		if ( isset( $display_settings['enable_breadcrumbs'] ) && $display_settings['enable_breadcrumbs'] ) {
			$retval = true;
		} elseif ( isset( $display_settings['original_settings'] ) && $this->are_breadcrumbs_enabled( $display_settings['original_settings'] ) ) {
			$retval = true;
		}

		return $retval;
	}

	/**
	 * Determine if we need to append the JS to the current template.
	 */
	public function are_child_entities_enabled( array $display_settings ): bool {
		$retval = false;

		if ( empty( $display_settings['open_gallery_in_lightbox'] ) ) {
			$display_settings['open_gallery_in_lightbox'] = 0;
		}

		if ( $display_settings['open_gallery_in_lightbox'] == 1 ) {
			$retval = true;
		}

		return $retval;
	}

	public function are_descriptions_enabled( $display_settings ): bool {
		$retval = false;

		if ( isset( $display_settings['enable_descriptions'] ) && $display_settings['enable_descriptions'] ) {
			$retval = true;
		} elseif ( isset( $display_settings['original_settings'] ) && $this->are_descriptions_enabled( $display_settings['original_settings'] ) ) {
			$retval = true;
		}

		return $retval;
	}

	public function enqueue_frontend_resources( $displayed_gallery ) {
		parent::enqueue_frontend_resources( $displayed_gallery );
		$this->enqueue_pagination_resources();

		\wp_enqueue_style(
			'nextgen_basic_album_style',
			StaticAssets::get_url( 'Albums/nextgen_basic_album.css', 'photocrati-nextgen_basic_album#nextgen_basic_album.css' ),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_enqueue_script(
			'nextgen_basic_album_script',
			StaticAssets::get_url( 'Albums/init.js', 'photocrati-nextgen_basic_album#init.js' ),
			[],
			NGG_SCRIPT_VERSION
		);

		\wp_enqueue_script( 'shave.js' );

		$ds = $displayed_gallery->display_settings;

		if ( ( ! empty( $ds['enable_breadcrumbs'] ) ) || ( ! empty( $ds['original_settings']['enable_breadcrumbs'] ) ) ) {
			\wp_enqueue_style(
				'nextgen_basic_album_breadcrumbs_style',
				StaticAssets::get_url( 'Albums/breadcrumbs.css', 'photocrati-nextgen_basic_album#breadcrumbs.css' ),
				[],
				NGG_SCRIPT_VERSION
			);
		}
	}

	public function find_gallery_parent( $gallery_id, $sortorder ) {
		$map   = AlbumMapper::get_instance();
		$found = [];

		foreach ( $sortorder as $order ) {
			if ( strpos( $order, 'a' ) === 0 ) {
				$album_id = ltrim( $order, 'a' );
				if ( empty( $this->breadcrumb_cache[ $order ] ) ) {
					$album                            = $map->find( $album_id );
					$this->breadcrumb_cache[ $order ] = $album;
					if ( in_array( $gallery_id, $album->sortorder ) ) {
						$found[] = $album;
						break;
					} else {
						$found = $this->find_gallery_parent( $gallery_id, $album->sortorder );
						if ( $found ) {
							$found[] = $album;
							break;
						}
					}
				}
			}
		}

		return $found;
	}

	public function generate_breadcrumb( $gallery_id, $entities ) {
		$found  = [];
		$router = Router::get_instance();
		$app    = $router->get_routed_app();

		if ( is_array( $gallery_id ) ) {
			$gallery_id = array_shift( $gallery_id );
		}
		if ( is_array( $gallery_id ) ) {
			$gallery_id = $gallery_id[0];
		}

		foreach ( $entities as $ndx => $entity ) {
			$tmpid                            = ( isset( $entity->albumdesc ) ? 'a' : '' ) . $entity->{$entity->id_field};
			$this->breadcrumb_cache[ $tmpid ] = $entity;
			if ( isset( $entity->albumdesc ) && in_array( $gallery_id, $entity->sortorder ) ) {
				$found[] = $entity;
				break;
			}
		}

		if ( empty( $found ) ) {
			foreach ( $entities as $entity ) {

				if ( ! empty( $entity->sortorder ) ) {
					$found = $this->find_gallery_parent( $gallery_id, $entity->sortorder );
				}

				if ( ! empty( $found ) ) {
					$found[] = $entity;
					break;
				}
			}
		}

		$found = array_reverse( $found );

		if ( strpos( $gallery_id, 'a' ) === 0 ) {
			$album_found = false;
			foreach ( $found as $found_item ) {
				if ( $found_item->{$found_item->id_field} == $gallery_id ) {
					$album_found = true;
				}
			}
			if ( ! $album_found ) {
				$album_id                              = ltrim( $gallery_id, 'a' );
				$album                                 = AlbumMapper::get_instance()->find( $album_id );
				$found[]                               = $album;
				$this->breadcrumb_cache[ $gallery_id ] = $album;
			}
		} else {
			$gallery_found = false;
			foreach ( $entities as $entity ) {
				if ( isset( $entity->is_gallery ) && $entity->is_gallery && $gallery_id == $entity->{$entity->id_field} ) {
					$gallery_found = true;
					$found[]       = $entity;
					break;
				}
			}
			if ( ! $gallery_found ) {
				$gallery = GalleryMapper::get_instance()->find( $gallery_id );
				if ( $gallery != null ) {
					$found[] = $gallery;
					$this->breadcrumb_cache[ $gallery->{$gallery->id_field} ] = $gallery;
				}
			}
		}

		$crumbs = [];
		if ( ! empty( $found ) ) {
			$end = end( $found );
			reset( $found );
			foreach ( $found as $found_item ) {
				$type   = isset( $found_item->albumdesc ) ? 'album' : 'gallery';
				$id     = ( $type == 'album' ? 'a' : '' ) . $found_item->{$found_item->id_field};
				$entity = $this->breadcrumb_cache[ $id ];
				$link   = null;

				if ( $type == 'album' ) {
					$name = $entity->name;
					if ( $entity->pageid > 0 ) {
						$link = @get_page_link( $entity->pageid );
					}
					if ( empty( $link ) && $found_item !== $end ) {
						$link = $app->get_routed_url();
						$link = $app->strip_param_segments( $link );
						$link = $app->set_parameter_value( 'album', $entity->slug, null, false, $link );
					}
				} else {
					$name = $entity->title;
				}

				$crumbs[] = [
					'type' => $type,
					'name' => $name,
					'url'  => $link,
				];
			}
		}

		// free this memory immediately.
		$this->breadcrumb_cache = [];

		$view = new View(
			'Albums/breadcrumbs',
			[
				'breadcrumbs' => $crumbs,
				'divisor'     => apply_filters( 'ngg_breadcrumb_separator', ' &raquo; ' ),
			],
			'photocrati-nextgen_basic_album#breadcrumbs'
		);

		return $view->render( true );
	}

	public function generate_description( $displayed_gallery ) {
		if ( self::$_description_added_once ) {
			return '';
		}

		self::$_description_added_once = true;
		$description                   = $this->get_description( $displayed_gallery );

		$view = new View(
			'Albums/descriptions',
			[
				'description' => $description,
			],
			'photocrati-nextgen_basic_album#descriptions'
		);

		return $view->render( true );
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @return DisplayedGallery
	 */
	public function get_alternate_displayed_gallery( $displayed_gallery ) {
		// Prevent recursive checks for further alternates causing additional modifications to the settings array.
		$id = $displayed_gallery->id();
		if ( ! empty( self::$alternate_displayed_galleries[ $id ] ) ) {
			return self::$alternate_displayed_galleries[ $id ];
		}

		$router = Router::get_instance();

		// Without this line the param() method will always return NULL when in wp_enqueue_scripts.
		$renderer = Renderer::get_instance( 'inner' );
		$renderer->do_app_rewrites( $displayed_gallery );

		$display_settings = $displayed_gallery->display_settings;
		$gallery          = $router->get_parameter( 'gallery' );

		if ( $gallery && strpos( $gallery, 'nggpage--' ) !== 0 ) {
			$result = GalleryMapper::get_instance()->get_by_slug( $gallery );

			if ( $result ) {
				$gallery = $result->{$result->id_field};
			}

			$parent_albums = $displayed_gallery->get_albums();

			$gallery_params = [
				'source'                  => 'galleries',
				'container_ids'           => [ $gallery ],
				'display_type'            => $display_settings['gallery_display_type'],
				'original_display_type'   => $displayed_gallery->display_type,
				'original_settings'       => $display_settings,
				'original_album_entities' => $parent_albums,
			];

			if ( ! empty( $display_settings['gallery_display_template'] ) ) {
				$gallery_params['template'] = $display_settings['gallery_display_template'];
			}

			$displayed_gallery = $renderer->params_to_displayed_gallery( $gallery_params );
			if ( is_null( $displayed_gallery->id() ) ) {
				$displayed_gallery->id( md5( json_encode( $displayed_gallery->get_entity() ) ) );
			}
			self::$alternate_displayed_galleries[ $id ] = $displayed_gallery;
		}

		return $displayed_gallery;
	}

	public function get_description( $displayed_gallery ) {
		$description = '';

		// Important: do not array_shift() $displayed_gallery->container_ids as it will affect breadcrumbs.
		$container_ids = $displayed_gallery->container_ids;

		if ( $displayed_gallery->source == 'galleries' ) {
			$gallery_id = array_shift( $container_ids );
			$gallery    = GalleryMapper::get_instance()->find( $gallery_id );
			if ( $gallery && ! empty( $gallery->galdesc ) ) {
				$description = $gallery->galdesc;
			}
		} elseif ( $displayed_gallery->source == 'albums' ) {
			$album_id = array_shift( $container_ids );
			$album    = AlbumMapper::get_instance()->find( $album_id );
			if ( $album && ! empty( $album->albumdesc ) ) {
				$description = $album->albumdesc;
			}
		}

		return $description;
	}

	public function get_order() {
		return NGG_DISPLAY_PRIORITY_BASE + NGG_DISPLAY_PRIORITY_STEP;
	}

	public function get_original_album_entities( $display_settings ) {
		$retval = [];

		if ( isset( $display_settings['original_album_entities'] ) ) {
			$retval = $display_settings['original_album_entities'];
		} elseif ( isset( $display_settings['original_settings'] ) && $this->get_original_album_entities( $display_settings['original_settings'] ) ) {
			$retval = $this->get_original_album_entities( $display_settings['original_settings'] );
		}

		return $retval;
	}

	/**
	 * Gets the parent album for the entity being displayed
	 *
	 * @param int $entity_id
	 * @return null|object Album object
	 */
	public function get_parent_album_for( $entity_id ) {
		$retval = null;

		foreach ( $this->albums as $album ) {
			if ( in_array( $entity_id, $album->sortorder ) ) {
				$retval = $album;
				break;
			}
		}

		return $retval;
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @param bool             $return
	 * @return string
	 */
	public function index_action( $displayed_gallery, $return = false ) {
		$router           = Router::get_instance();
		$display_settings = $displayed_gallery->display_settings;

		// We need to fetch the selected album containers. We need to do this, because once we fetch the included
		// entities, we need to iterate over each entity and assign it a parent_id, which is the album that it belongs
		// to. We need to do this because the link to the gallery, is not /nggallery/gallery--id,
		// but /nggallery/album--id/gallery--id.

		// Are we to display a gallery? Ensure our 'gallery' isn't just a paginated album view.
		$gallery = $router->get_parameter( 'gallery' );
		if ( $gallery && strpos( $gallery, 'nggpage--' ) !== 0 ) {
			// Basic albums only support one per post.
			if ( isset( $GLOBALS['nggShowGallery'] ) ) {
				return '';
			}

			$GLOBALS['nggShowGallery'] = true;

			$alternate_displayed_gallery = $this->get_alternate_displayed_gallery( $displayed_gallery );
			if ( $alternate_displayed_gallery !== $displayed_gallery ) {
				$renderer = Renderer::get_instance( 'inner' );

				// For legacy templates we just generate the description & breadcrumb string and prepend it to the generated
				// display of the gallery. For modern templates we attach a filter to the display renderer just for this
				// one particular gallery, that seeks out the container element and injects the breadcrumbs there.
				\add_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_description_to_legacy_templates' ], 8, 2 );
				\add_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_breadcrumbs_to_legacy_templates' ], 9, 2 );
				\add_filter( 'ngg_display_type_rendering_object', [ $this, 'add_breadcrumbs_and_descriptions' ], 10, 2 );

				$output = $renderer->display_images( $alternate_displayed_gallery, $return );

				\remove_filter( 'ngg_display_type_rendering_object', [ $this, 'add_breadcrumbs_and_descriptions' ], 10 );
				\remove_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_description_to_legacy_templates' ], 8 );
				\remove_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_breadcrumbs_to_legacy_templates' ], 9 );

				return $output;
			}
		}

		// If we're viewing a sub-album, then we use that album as a container instead.
		elseif ( ( $album = $router->get_parameter( 'album' ) ) ) {
			// Are we to display a sub-album?
			$result    = AlbumMapper::get_instance()->get_by_slug( $album );
			$album_sub = $result ? $result->{$result->id_field} : null;
			if ( $album_sub != null ) {
				$album = $album_sub;
			}

			$displayed_gallery->entity_ids    = [];
			$displayed_gallery->sortorder     = [];
			$displayed_gallery->container_ids = ( $album === '0' or $album === 'all' ) ? [] : [ $album ];

			$displayed_gallery->display_settings['original_album_id']       = 'a' . $album_sub;
			$displayed_gallery->display_settings['original_album_entities'] = $displayed_gallery->get_albums();
		}

		// Get the albums
		// TODO: This should probably be moved to the elseif block above.
		$this->albums = $displayed_gallery->get_albums();

		// None of the above: Display the main album. Get the settings required for display.
		$current_page = (int) $router->get_parameter( 'page', $displayed_gallery->id(), 1 );
		$offset       = $display_settings['galleries_per_page'] * ( $current_page - 1 );
		$entities     = $displayed_gallery->get_included_entities( $display_settings['galleries_per_page'], $offset );

		// If there are entities to be displayed.
		if ( $entities ) {
			$pagination_result = $this->create_pagination(
				$current_page,
				$displayed_gallery->get_entity_count(),
				$display_settings['galleries_per_page'],
				urldecode( $router->get_parameter( 'ajax_pagination_referrer' ) ?: '' )
			);
			$display_settings['entities']          = $entities;
			$display_settings['pagination']        = $pagination_result['output'];
			$display_settings['displayed_gallery'] = $displayed_gallery;
			$display_settings                      = $this->prepare_legacy_album_params( $displayed_gallery->get_entity(), $display_settings );

			if ( ! empty( $display_settings['template'] ) && $display_settings['template'] != 'default' ) {
				// Add additional parameters.
				$router->get_routed_app()->remove_parameter( 'ajax_pagination_referrer' );
				$display_settings['current_page']    = $current_page;
				$display_settings['pagination_prev'] = $pagination_result['prev'];
				$display_settings['pagination_next'] = $pagination_result['next'];

				$breadcrumbs = $this->render_legacy_template_breadcrumbs( $displayed_gallery, $entities );
				$description = $this->render_legacy_template_description( $displayed_gallery );

				// If enabled enqueue the child entities as JSON for lightboxes to read.
				$retval = $this->legacy_render( $display_settings['template'], $display_settings, $return, 'album' );

				if ( ! empty( $description ) ) {
					$retval = $description . $retval;
				}

				if ( ! empty( $breadcrumbs ) ) {
					$retval = $breadcrumbs . $retval;
				}

				return $retval;
			} else {
				$params = $display_settings;
				$params = $this->prepare_display_parameters( $displayed_gallery, $params );

				$view = new View( $this->template, $params, $this->legacy_template );

				// Rather than messing with filters and return values, this method just directly calls add_breadcrumbs_and_descriptions().
				$view_element = $view->render_object();
				$view_element = $this->add_breadcrumbs_and_descriptions( $view_element, $displayed_gallery );
				$content      = $view->rasterize_object( $view_element );

				if ( ! $return ) {
					echo $content;
				}

				return $content;
			}
		} else {
			$view = new View(
				'GalleryDisplay/NoImagesFound',
				[],
				'photocrati-nextgen_gallery_display#no_images_found'
			);
			return $view->render( $return );
		}
	}

	public function is_basic_album( $displayed_gallery ): bool {
		return in_array( $displayed_gallery->display_type, [ NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM ] );
	}

	/**
	 * Creates a displayed gallery of a gallery belonging to an album. Shared by index_action() and cache_action() to
	 * allow lightboxes to open album children directly.
	 *
	 * This also adds some script data to generated child entities, so that they can be viewed by the Pro Lightbox.
	 *
	 * @param $gallery
	 * @param $display_settings
	 * @return object
	 */
	public function make_child_displayed_gallery( $gallery, $display_settings ) {
		$gallery->displayed_gallery                    = new DisplayedGallery();
		$gallery->displayed_gallery->container_ids     = [ $gallery->{$gallery->id_field} ];
		$gallery->displayed_gallery->display_settings  = $display_settings;
		$gallery->displayed_gallery->returns           = 'included';
		$gallery->displayed_gallery->source            = 'galleries';
		$gallery->displayed_gallery->images_list_count = $gallery->displayed_gallery->get_entity_count();
		$gallery->displayed_gallery->is_album_gallery  = true;
		$gallery->displayed_gallery->to_transient();

		\add_action( 'ngg_display_type_controller_enqueue_frontend_resources', function ( $displayed_gallery ) use ( $gallery ) {
			$displayed_gallery = $gallery->displayed_gallery;

			DisplayManager::add_script_data(
				'ngg_common',
				'galleries.gallery_' . $displayed_gallery->id(),
				(array) $displayed_gallery->get_entity(),
				false
			);
			DisplayManager::add_script_data(
				'ngg_common',
				'galleries.gallery_' . $displayed_gallery->id() . '.wordpress_page_root',
				get_permalink(),
				false
			);

			do_action( 'ngg_albums_enqueue_child_entity_data', $displayed_gallery );
		} );

		return $gallery;
	}

	/**
	 * @param DisplayedGallery $displayed_gallery
	 * @param array            $params
	 * @return array
	 */
	public function prepare_legacy_album_params( $displayed_gallery, $params ) {
		$image_gen    = ThumbnailsManager::get_instance();
		$image_mapper = ImageMapper::get_instance();
		$router       = Router::get_instance();
		$storage      = StorageManager::get_instance();

		$app = $router->get_routed_app();

		if ( empty( $displayed_gallery->display_settings['override_thumbnail_settings'] ) ) {
			// legacy templates expect these dimensions.
			$image_gen_params = [
				'width'  => 91,
				'height' => 68,
				'crop'   => true,
			];
		} else {
			// use settings requested by user.
			$image_gen_params = [
				'width'     => $displayed_gallery->display_settings['thumbnail_width'],
				'height'    => $displayed_gallery->display_settings['thumbnail_height'],
				'quality'   => isset( $displayed_gallery->display_settings['thumbnail_quality'] ) ? $displayed_gallery->display_settings['thumbnail_quality'] : 100,
				'crop'      => isset( $displayed_gallery->display_settings['thumbnail_crop'] ) ? $displayed_gallery->display_settings['thumbnail_crop'] : null,
				'watermark' => isset( $displayed_gallery->display_settings['thumbnail_watermark'] ) ? $displayed_gallery->display_settings['thumbnail_watermark'] : null,
			];
		}

		// so user templates can know how big the images are expected to be.
		$params['image_gen_params'] = $image_gen_params;

		// Transform entities.
		$params['galleries'] = $params['entities'];
		unset( $params['entities'] );

		foreach ( $params['galleries'] as &$gallery ) {

			// Get the preview image url.
			$gallery->previewurl = '';
			if ( $gallery->previewpic && $gallery->previewpic > 0 ) {
				if ( ( $image = $image_mapper->find( intval( $gallery->previewpic ) ) ) ) {
					$gallery->previewpic_image         = $image;
					$gallery->previewpic_fullsized_url = $storage->get_image_url( $image );

					$gallery->previewurl  = $storage->get_image_url( $image, $image_gen->get_size_name( $image_gen_params ) );
					$gallery->previewname = $gallery->name;
				} else {
					$gallery->no_previewpic = true;
				}
			}

			// Get the page link. If the entity is an album, then the url will
			// look like /nggallery/album--slug.
			$id_field = $gallery->id_field;
			if ( $gallery->is_album ) {
				if ( $gallery->pageid > 0 ) {
					$gallery->pagelink = @get_page_link( $gallery->pageid );
				} else {
					$pagelink          = $app->get_routed_url( true );
					$pagelink          = $app->remove_parameter( 'album', null, $pagelink );
					$pagelink          = $app->remove_parameter( 'gallery', null, $pagelink );
					$pagelink          = $app->remove_parameter( 'nggpage', null, $pagelink );
					$pagelink          = $app->set_parameter( 'album', $gallery->slug, null, false, $pagelink );
					$gallery->pagelink = $pagelink;
				}
			}

			// Otherwise, if it's a gallery then it will look like
			// /nggallery/album--slug/gallery--slug.
			else {

				if ( $gallery->pageid > 0 ) {
					$gallery->pagelink = @get_page_link( $gallery->pageid );
				}

				if ( empty( $gallery->pagelink ) ) {
					$pagelink     = $app->get_routed_url();
					$parent_album = $this->get_parent_album_for( $gallery->$id_field );
					if ( $parent_album ) {
						$pagelink = $app->remove_parameter( 'album', null, $pagelink );
						$pagelink = $app->remove_parameter( 'gallery', null, $pagelink );
						$pagelink = $app->remove_parameter( 'nggpage', null, $pagelink );
						$pagelink = $app->set_parameter(
							'album',
							$parent_album->slug,
							null,
							false,
							$pagelink
						);
					}
					// Legacy compat: use an album slug of 'all' if we're missing a container_id.
					elseif ( $displayed_gallery->container_ids === [ '0' ]
						|| $displayed_gallery->container_ids === [ '' ] ) {
						$pagelink = $app->set_parameter( 'album', 'all', null, false, $pagelink );
					} else {
						$pagelink = $app->remove_parameter( 'nggpage', null, $pagelink );
						$pagelink = $app->set_parameter( 'album', 'album', null, false, $pagelink );
					}
					$gallery->pagelink = $app->set_parameter(
						'gallery',
						$gallery->slug,
						null,
						false,
						$pagelink
					);
				}
			}

			// Mark the child type.
			$gallery->entity_type = isset( $gallery->is_gallery ) && intval( $gallery->is_gallery ) ? 'gallery' : 'album';

			// If this setting is on we need to inject an effect code.
			if ( ! empty( $displayed_gallery->display_settings['open_gallery_in_lightbox'] ) && $gallery->entity_type == 'gallery' ) {
				$gallery  = $this->make_child_displayed_gallery( $gallery, $displayed_gallery->display_settings );
				$lightbox = LightboxManager::get_instance()->get_selected();
				if ( $lightbox->is_supported( $displayed_gallery ) ) {
					$gallery->displayed_gallery->effect_code = $this->get_effect_code( $gallery->displayed_gallery );
				}
			}

			// Let plugins modify the gallery.
			$gallery = \apply_filters( 'ngg_album_galleryobject', $gallery );
		}

		/*
		Register each gallery belonging to the album that has just been rendered, so that when the MVC controller
		 * system 'catches up' and runs $this->render_object() that method knows what galleries to inline as JS. */
		if ( $this->is_basic_album( $displayed_gallery ) ) {
			$id = $displayed_gallery->ID();
			foreach ( $params['galleries'] as &$gallery ) {
				if ( $gallery->is_album ) {
					continue;
				}
				$this->entities[ $id ][] = $gallery;
			}
		}

		$params['album']  = reset( $this->albums );
		$params['albums'] = $this->albums;

		// Clean up.
		unset( $storage );
		unset( $image_mapper );
		unset( $image_gen );
		unset( $image_gen_params );

		return $params;
	}

	public function render_legacy_template_breadcrumbs( $displayed_gallery, $entities, $gallery_id = false ) {
		$ds = $displayed_gallery->display_settings;

		if ( ! empty( $entities ) && ! empty( $ds['template'] ) && $this->are_breadcrumbs_enabled( $ds ) ) {
			if ( $gallery_id ) {
				if ( is_array( $gallery_id ) ) {
					$ids = $gallery_id;
				} else {
					$ids = [ $gallery_id ];
				}
			} elseif ( ! empty( $ds['original_album_id'] ) ) {
				$ids = $ds['original_album_id'];
			} else {
				$ids = $displayed_gallery->container_ids;
			}

			if ( ! empty( $ds['original_album_entities'] ) ) {
				$breadcrumb_entities = $ds['original_album_entities'];
			} else {
				$breadcrumb_entities = $entities;
			}

			return $this->generate_breadcrumb(
				$ids,
				$breadcrumb_entities
			);
		} else {
			return '';
		}
	}

	public function render_legacy_template_description( $displayed_gallery ) {
		if ( ! empty( $displayed_gallery->display_settings['template'] )
			&& $this->are_descriptions_enabled( $displayed_gallery->display_settings ) ) {
			return $this->generate_description( $displayed_gallery );
		} else {
			return '';
		}
	}
}