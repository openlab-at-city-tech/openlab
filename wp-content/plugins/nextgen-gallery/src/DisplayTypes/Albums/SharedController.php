<?php
/**
 * SharedController is extended by other Album controllers and is responsible for all actual processing.
 *
 * @package NextGEN Gallery
 */

namespace Imagely\NGG\DisplayTypes\Albums;

use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\DataStorage\Manager as StorageManager;
use Imagely\NGG\DisplayType\Controller as ParentController;
use Imagely\NGG\DynamicThumbnails\Manager as ThumbnailsManager;

use Imagely\NGG\DataTypes\DisplayedGallery;
use Imagely\NGG\Display\{DisplayManager, LightboxManager, View, ViewElement, StaticAssets};
use Imagely\NGG\DisplayedGallery\Renderer;
use Imagely\NGG\Util\Router;

/**
 * SharedController definition.
 */
class SharedController extends ParentController {

	/**
	 * Stored display settings that must never be merged into a child gallery's params.
	 *
	 * The three view keys select a template file and reach the include in legacy_render(); the
	 * album sets the child template itself further down. is_ecommerce_enabled is a gallery-level
	 * column rather than a per-display-type setting and is applied from the gallery entity.
	 *
	 * The remaining keys are Renderer::params_to_displayed_gallery()'s structural vocabulary: they
	 * choose *which* entities render rather than how they look, so they are denied unconditionally
	 * even when a display type declares them (a display type's stored settings row is not a
	 * trustworthy allow-list on its own - see filter_child_display_settings()). Without this a
	 * stored `id` would substitute a persisted displayed gallery for the album's child, and
	 * `gallery_ids` / `container_ids` would retarget it at unrelated galleries. Note the lightbox
	 * path merges with array_merge(), so it cannot rely on the permalink path's `+=` precedence.
	 *
	 * `order_by` / `order_direction` are deliberately NOT denied: they only sort the child's own
	 * entities, and display types (e.g. Pro Search) declare them as genuine settings.
	 *
	 * @var string[]
	 */
	const RESERVED_CHILD_SETTINGS = [
		'template',
		'display_view',
		'display_type_view',
		'is_ecommerce_enabled',
		// Renderer::params_to_displayed_gallery() structural params.
		'album_ids',
		'container_ids',
		'display',
		'display_type',
		'entity_ids',
		'exclusions',
		'gallery_ids',
		'id',
		'ids',
		'image_ids',
		'returns',
		'slug',
		'sortorder',
		'source',
		'src',
		'tag_ids',
		'tagcloud',
	];

	/**
	 * Thumbnail geometry keys, the only settings carried across display types (#787).
	 *
	 * A gallery's saved override lives under the display type the admin UI was showing, which for
	 * a Pro Grid/List album child is not the type the album forces on it. These keys mean the same
	 * thing in every display type that declares them, so they are safe to carry over; nothing else
	 * is (a `number_of_columns` or `images_per_page` saved for basic thumbnails is not a statement
	 * about how the forced type should paginate). They are also carried as one group, never
	 * key-by-key, so the `override_thumbnail_settings` gate and the dimensions it gates can never
	 * come from two different buckets.
	 *
	 * @var string[]
	 */
	const CROSS_TYPE_CHILD_SETTINGS = [
		'override_thumbnail_settings',
		'thumbnail_width',
		'thumbnail_height',
		'thumbnail_crop',
		'thumbnail_quality',
		'thumbnail_watermark',
	];

	/**
	 * Settings used in arithmetic; cast before use.
	 *
	 * @var string[]
	 */
	const NUMERIC_SETTINGS = [ 'galleries_per_page', 'thumbnail_width', 'thumbnail_height' ];

	/**
	 * Cache of albums to be displayed.
	 *
	 * @var array
	 */
	public $albums = [];

	/**
	 * Cache of alternate displayed galleries to render.
	 *
	 * @var array
	 */
	public static $alternate_displayed_galleries = [];

	/**
	 * Cache of rendered HTML strings.
	 *
	 * @var array
	 */
	public $breadcrumb_cache = [];

	/**
	 * Cache of album children from the database.
	 *
	 * @var array
	 */
	public $entities = [];

	/**
	 * Path to a legacy template to use when rendering.
	 *
	 * @var string
	 */
	public $legacy_template = '';

	/**
	 * Path to the template to use when rendering.
	 *
	 * @var string
	 */
	public $template = '';

	/**
	 * Cache of settings to be used when rendering displayed galleries.
	 *
	 * @var array
	 */
	public static $display_settings = [];

	/**
	 * When viewing a child gallery the album controller's add_description_to_legacy_templates() method will be
	 * called for the gallery and then again for the root album; we only want to run once.
	 *
	 * @var bool Has the description HTML been added or not.
	 */
	public static $_description_added_once = false;

	/**
	 * Adds rendered breadcrumbs and descriptions to a ViewElement.
	 *
	 * @param ViewElement      $root_element A ViewElement object.
	 * @param DisplayedGallery $displayed_gallery A DisplayedGallery object.
	 *
	 * @return ViewElement
	 */
	public function add_breadcrumbs_and_descriptions( ViewElement $root_element, DisplayedGallery $displayed_gallery ): ViewElement {
		$ds = $displayed_gallery->display_settings;

		// Enable album breadcrumbs.
		$original_entities = $this->get_original_album_entities( $ds );
		if ( $this->are_breadcrumbs_enabled( $ds ) && ! empty( $original_entities ) ) {
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
				if ( ! empty( $container->_list ) ) {
					foreach ( $container->_list as $ndx => $item ) {
						if ( is_string( $item ) ) {
							$pos = $ndx;
						} else {
							break;
						}
					}
				}

				$container->insert( $description, $pos );
			}
		}

		return $root_element;
	}

	/**
	 * Prepends generated breadcrumb HTML to the $html parameter.
	 *
	 * @param string           $html HTML string.
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return string
	 */
	public function add_breadcrumbs_to_legacy_templates( string $html, DisplayedGallery $displayed_gallery ): string {
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
			return $breadcrumbs . $html;
		} else {
			return $html;
		}
	}

	/**
	 * Prepends generated description HTML to the $html parameter.
	 *
	 * @param string           $html HTML string.
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return string
	 */
	public function add_description_to_legacy_templates( string $html, DisplayedGallery $displayed_gallery ): string {
		$description = $this->render_legacy_template_description( $displayed_gallery );

		if ( ! empty( $description ) ) {
			return $description . $html;
		} else {
			return $html;
		}
	}

	/**
	 * Determines whether breadcrumbs are enabled.
	 *
	 * @param array $display_settings Array of display type settings.
	 *
	 * @return bool
	 */
	public function are_breadcrumbs_enabled( array $display_settings ): bool {
		if ( isset( $display_settings['enable_breadcrumbs'] ) && $display_settings['enable_breadcrumbs'] ) {
			return true;
		} elseif ( isset( $display_settings['original_settings'] ) && $this->are_breadcrumbs_enabled( $display_settings['original_settings'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Determines whether descriptions are enabled.
	 *
	 * @param array $display_settings Array of display type settings.
	 *
	 * @return bool
	 */
	public function are_descriptions_enabled( array $display_settings ): bool {
		if ( isset( $display_settings['enable_descriptions'] ) && $display_settings['enable_descriptions'] ) {
			return true;
		} elseif ( isset( $display_settings['original_settings'] ) && $this->are_descriptions_enabled( $display_settings['original_settings'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Enqueues the frontend resources needed for this displayed gallery.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 */
	public function enqueue_frontend_resources( $displayed_gallery ) {
		$this->cast_numeric_settings( $displayed_gallery, self::NUMERIC_SETTINGS );

		// Necessary for breadcrumbs and URL routing.
		$renderer = Renderer::get_instance( 'inner' );
		$renderer->do_app_rewrites( $displayed_gallery );

		// This MUST come before the parent::enqueue_frontend_resources() so that this method can register an action
		// that will be triggered by the parent method.
		$this->prepare_display_settings(
			$displayed_gallery->get_entity(),
			$displayed_gallery->display_settings
		);

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
			NGG_SCRIPT_VERSION,
			false
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

	/**
	 * Finds the parent album of a gallery, returning the ancestor chain.
	 *
	 * Accepts a gallery id (int) or an album key ( 'a{id}' ) and deliberately does not
	 * normalise it to int: casting 'a5' to 0 was the depth-2 breadcrumb bug. (Separate from
	 * album_id_in_sortorder(), an album-id-only containment check that does normalise.)
	 *
	 * @param int|string $gallery_id Gallery ID (int) or album key ( 'a{id}' ).
	 * @param array      $sortorder  Array of children belonging to an album.
	 * @param array      $visited    Album keys already walked; guards against cycles.
	 *
	 * @return array
	 */
	public function find_gallery_parent( $gallery_id, array $sortorder, array &$visited = [] ): array {
		$map   = AlbumMapper::get_instance();
		$found = [];

		foreach ( $sortorder as $order ) {
			if ( strpos( $order, 'a' ) !== 0 ) {
				continue;
			}

			// Cycle guard.
			if ( in_array( $order, $visited, true ) ) {
				continue;
			}
			$visited[] = $order;

			$album_id = ltrim( $order, 'a' );

			// Cache only the DB lookup; the sortorder is traversed even on a cache hit, and
			// array_key_exists() memoises a missing album as null instead of re-querying it.
			if ( ! array_key_exists( $order, $this->breadcrumb_cache ) ) {
				$this->breadcrumb_cache[ $order ] = $map->find( $album_id );
			}
			$album = $this->breadcrumb_cache[ $order ];

			if ( ! $album ) {
				// Album row is gone; skip it and keep rendering the rest of the trail.
				continue;
			}

			// Using strict comparison here breaks the breadcrumb generation.
			//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( is_array( $album->sortorder ) && in_array( $gallery_id, $album->sortorder ) ) {
				$found[] = $album;
				break;
			} elseif ( is_array( $album->sortorder ) ) {
				$found = $this->find_gallery_parent( $gallery_id, $album->sortorder, $visited );
				if ( $found ) {
					$found[] = $album;
					break;
				}
			}
		}

		return $found;
	}

	/**
	 * Determines whether $album_id belongs to the hierarchy of any album in $albums.
	 *
	 * Prevents multiple album shortcodes on the same page from all reacting to the
	 * same ?album= URL parameter. A shortcode should only navigate into a sub-album
	 * that actually lives within its own container album hierarchy
	 * (see awesomemotive/nextgen-gallery-pro#627).
	 *
	 * @param int         $album_id Numeric album ID requested via the URL.
	 * @param AlbumMapper $mapper   Album data mapper.
	 * @param array       $albums   The shortcode's own container album entities.
	 *
	 * @return bool
	 */
	protected function is_album_in_hierarchy( int $album_id, $mapper, array $albums ): bool {
		$visited = [];
		foreach ( $albums as $album ) {
			if ( $this->album_id_in_sortorder( $album_id, $album, $mapper, $visited ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Recursively checks whether 'a{$album_id}' appears in $album->sortorder or in
	 * any nested sub-album's sortorder. Album IDs already walked are tracked in
	 * $visited so cyclic or self-referential album trees cannot cause infinite
	 * recursion, without imposing an arbitrary nesting-depth limit.
	 *
	 * @param int         $album_id Numeric album ID to look for.
	 * @param object      $album    Album entity exposing a sortorder array.
	 * @param AlbumMapper $mapper   Album data mapper.
	 * @param array       $visited  Album IDs already visited (passed by reference).
	 *
	 * @return bool
	 */
	protected function album_id_in_sortorder( int $album_id, $album, $mapper, array &$visited ): bool {
		if ( empty( $album->sortorder ) || ! is_array( $album->sortorder ) ) {
			return false;
		}
		if ( in_array( 'a' . $album_id, $album->sortorder, true ) ) {
			return true;
		}
		foreach ( $album->sortorder as $item ) {
			if ( is_string( $item ) && strpos( $item, 'a' ) === 0 && is_numeric( substr( $item, 1 ) ) ) {
				$sub_id = (int) substr( $item, 1 );
				if ( isset( $visited[ $sub_id ] ) ) {
					continue;
				}
				$visited[ $sub_id ] = true;
				$sub_album          = $mapper->find( $sub_id );
				if ( $sub_album && $this->album_id_in_sortorder( $album_id, $sub_album, $mapper, $visited ) ) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Determines whether $gallery_id is one of the galleries this album shortcode renders.
	 *
	 * This is the authorization decision for the ?gallery= drill-down parameter. The parameter
	 * names an arbitrary gallery id or slug, is read on a public render path, and its value
	 * becomes the child DisplayedGallery's container_ids - which every consumer of
	 * 'ngg_displayed_gallery_construct' then reads. Without this check an anonymous GET renders
	 * any gallery on the site, including ones deliberately never placed on a public page
	 * (see awesomemotive/nextgen-gallery-pro#811).
	 *
	 * The basis is what the shortcode actually renders as tiles, not the raw album sortorder,
	 * because the two differ in both directions:
	 *
	 * - `[album]` and `[album id=0]` arrive here with container_ids of [''] / ['0'], which
	 *   get_entities() treats as "every gallery on the site" (DataTypes/DisplayedGallery.php)
	 *   while get_albums() returns no albums at all for them. Deriving containment from
	 *   get_albums() would refuse every drill-down on those shortcodes even though every
	 *   gallery is on the page.
	 * - `exclusions` and `entity_ids` narrow the rendered set below the sortorder. Those are
	 *   how an owner takes a gallery back off a published album without restructuring it, so
	 *   sortorder membership is exactly the wrong authority there.
	 *
	 * A sub-album tile is followed into, since clicking it is a legitimate route to the
	 * galleries beneath it; its own children are not filtered by this shortcode's exclusions.
	 *
	 * @param int              $gallery_id        Numeric gallery ID requested via the URL.
	 * @param DisplayedGallery $displayed_gallery The album displayed gallery being rendered.
	 *
	 * @return bool
	 */
	protected function is_gallery_in_displayed_albums( int $gallery_id, DisplayedGallery $displayed_gallery ): bool {
		if ( $gallery_id < 1 ) {
			return false;
		}

		// Only the two real sentinels mean "every gallery on the site" - those shortcodes render
		// every gallery as its own tile, so any existing gallery is legitimately reachable and
		// the check short-circuits to a simple existence test. An empty container_ids
		// does NOT: get_entities() nests its sentinel test inside `if ( $container_ids )`, so a
		// falsy value means the container query contributes nothing, and the tiles come from
		// entity_ids instead - which is exactly what `[ngg source="albums" gallery_ids="3,5"]`
		// produces (Renderer::params_to_displayed_gallery() routes gallery_ids into entity_ids
		// and leaves container_ids empty for an albums source). Treating that as "all galleries"
		// would authorize every gallery on the site for those shortcodes, which is the #811 hole
		// itself. Falling through to the entity walk authorizes exactly the rendered tiles.
		// ...and only when the shortcode does not narrow that set afterwards. The renderer
		// expands the sentinel to every gallery and THEN subtracts exclusions and intersects
		// entity_ids (DataTypes/DisplayedGallery.php: entity_ids fast path, sentinel expansion,
		// exclusions), so `[ngg src="albums" ids="0" exclusions="7"]` shows everything except
		// gallery 7. Short-circuiting on the sentinel alone authorized gallery 7 anyway, because
		// it returns before $excluded is built below and never reads entity_ids. When either is
		// present the walk below is the authority instead: it reads get_included_entities(),
		// which has already had both applied.
		$container_ids = (array) $displayed_gallery->container_ids;
		if ( ( [ '0' ] === $container_ids || [ '' ] === $container_ids )
			&& empty( $displayed_gallery->exclusions )
			&& empty( $displayed_gallery->entity_ids ) ) {
			return (bool) GalleryMapper::get_instance()->find( $gallery_id );
		}

		$mapper  = AlbumMapper::get_instance();
		$visited = [];

		// This shortcode's exclusions survive a drill-down into a sub-album: index_action()'s
		// ?album= branch resets entity_ids, sortorder and container_ids but deliberately leaves
		// exclusions alone, so a gallery excluded here is not rendered in the sub-album view
		// either - and must not be reachable through it.
		$excluded = array_map( 'intval', (array) $displayed_gallery->exclusions );

		foreach ( (array) $displayed_gallery->get_included_entities() as $entity ) {
			if ( ! is_object( $entity ) || empty( $entity->id_field ) ) {
				continue;
			}

			$is_gallery = isset( $entity->is_gallery ) && intval( $entity->is_gallery );
			$entity_id  = (int) $entity->{$entity->id_field};

			if ( $is_gallery ) {
				if ( $entity_id === $gallery_id ) {
					return true;
				}
				continue;
			}

			if ( $this->gallery_id_in_sortorder( $gallery_id, $entity, $mapper, $visited, $excluded ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Recursively checks whether $gallery_id appears in $album->sortorder or in any nested
	 * sub-album's sortorder.
	 *
	 * Sub-album ids already walked are tracked in $visited so cyclic or self-referential album
	 * trees cannot cause infinite recursion, without imposing a nesting-depth limit - the same
	 * guard album_id_in_sortorder() uses.
	 *
	 * sortorder holds its entries as strings, so gallery ids are compared after an integer cast
	 * rather than with in_array( ..., true ): a strict match against the stored '12' would never
	 * fire. Entries beginning with 'a' are sub-albums and are recursed into, never compared.
	 *
	 * Sub-album rows are read through $this->breadcrumb_cache, the same per-request cache the
	 * breadcrumb walk uses, so an album appearing in several trees is fetched once per request.
	 *
	 * @param int         $gallery_id Numeric gallery ID to look for.
	 * @param object      $album      Album entity exposing a sortorder array.
	 * @param AlbumMapper $mapper     Album data mapper.
	 * @param array       $visited    Sub-album IDs already visited (passed by reference).
	 * @param array       $excluded   Gallery IDs this shortcode excludes at any depth.
	 *
	 * @return bool
	 */
	protected function gallery_id_in_sortorder( int $gallery_id, $album, $mapper, array &$visited, array $excluded = [] ): bool {
		if ( empty( $album->sortorder ) || ! is_array( $album->sortorder ) ) {
			return false;
		}

		// An excluded gallery is not rendered by this shortcode at any depth, so no amount of
		// sortorder membership makes it reachable.
		if ( in_array( $gallery_id, $excluded, true ) ) {
			return false;
		}

		foreach ( $album->sortorder as $item ) {
			if ( is_string( $item ) && strpos( $item, 'a' ) === 0 ) {
				continue;
			}
			if ( is_numeric( $item ) && (int) $item === $gallery_id ) {
				return true;
			}
		}

		foreach ( $album->sortorder as $item ) {
			if ( ! is_string( $item ) || strpos( $item, 'a' ) !== 0 || ! is_numeric( substr( $item, 1 ) ) ) {
				continue;
			}
			$sub_id = (int) substr( $item, 1 );
			if ( isset( $visited[ $sub_id ] ) ) {
				continue;
			}
			$visited[ $sub_id ] = true;

			$cache_key = 'a' . $sub_id;
			if ( ! array_key_exists( $cache_key, $this->breadcrumb_cache ) ) {
				$this->breadcrumb_cache[ $cache_key ] = $mapper->find( $sub_id );
			}
			$sub_album = $this->breadcrumb_cache[ $cache_key ];

			if ( $sub_album && $this->gallery_id_in_sortorder( $gallery_id, $sub_album, $mapper, $visited, $excluded ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Generates breadcrumb HTML.
	 *
	 * @param array|int $gallery_id Gallery ID.
	 * @param array     $entities Array of album children.
	 *
	 * @return string|null
	 */
	public function generate_breadcrumb( $gallery_id, array $entities ) {
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
			// Skip null entities
			if ( ! $entity || ! isset( $entity->id_field ) ) {
				continue;
			}
			$tmpid                            = ( isset( $entity->albumdesc ) ? 'a' : '' ) . $entity->{$entity->id_field};
			$this->breadcrumb_cache[ $tmpid ] = $entity;
			// Using strict comparison here breaks the breadcrumb generation.
			//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
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
				if ( $found_item->{$found_item->id_field} === $gallery_id ) {
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
				if ( isset( $entity->is_gallery ) && $entity->is_gallery && $gallery_id === $entity->{$entity->id_field} ) {
					$gallery_found = true;
					$found[]       = $entity;
					break;
				}
			}
			if ( ! $gallery_found ) {
				$gallery = GalleryMapper::get_instance()->find( $gallery_id );
				if ( null !== $gallery ) {
					$found[] = $gallery;
					$this->breadcrumb_cache[ $gallery->{$gallery->id_field} ] = $gallery;
				}
			}
		}

		$crumbs = [];
		if ( ! empty( $found ) ) {
			$end = end( $found );
			reset( $found );
			foreach ( $found as $ndx => $found_item ) {
				// Skip null or invalid items
				if ( ! $found_item || ! isset( $found_item->id_field ) || empty( $found_item->id_field ) ) {
					continue;
				}
				$type = isset( $found_item->albumdesc ) ? 'album' : 'gallery';
				$id   = ( 'album' === $type ? 'a' : '' ) . $found_item->{$found_item->id_field};

				// Skip if entity not found in cache
				if ( ! isset( $this->breadcrumb_cache[ $id ] ) ) {
					continue;
				}
				$entity = $this->breadcrumb_cache[ $id ];
				$link   = null;

				if ( 'album' === $type ) {
					$name = isset( $entity->name ) ? $entity->name : '';
					if ( isset( $entity->pageid ) && $entity->pageid > 0 ) {
						$page = get_post( $entity->pageid );
						if ( $page && isset( $page->ID ) ) {
							$link = get_page_link( $page->ID );
						}
					}
					if ( empty( $link ) && $found_item !== $end ) {
						$link = $app->get_routed_url();
						$link = $app->strip_param_segments( $link );
						// Do not include the album in the URL when linking to the root element.
						if ( 0 !== $ndx ) {
							$link = $app->set_parameter_value( 'album', $entity->slug, null, false, $link );
						}
					}
				} else {
					$name = isset( $entity->title ) ? $entity->title : '';
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

	/**
	 * Generates description HTML.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return string|null
	 */
	public function generate_description( DisplayedGallery $displayed_gallery ) {
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
	 * Returns the correct DisplayedGallery to render under the current circumstances.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return DisplayedGallery
	 */
	public function get_alternate_displayed_gallery( DisplayedGallery $displayed_gallery ): DisplayedGallery {
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

			// Honour ?gallery= only for a gallery this shortcode actually renders, mirroring the
			// containment the ?album= branch of index_action() already applies. The parameter is
			// read on a public render path and its value lands in the child's container_ids, so
			// without this an anonymous GET renders any gallery on the site - proofing and
			// paid-download galleries included - and every consumer of
			// 'ngg_displayed_gallery_construct' receives the attacker's id
			// (awesomemotive/nextgen-gallery-pro#811).
			//
			// An unresolved slug leaves $gallery non-numeric and casts to 0, which the helper
			// refuses, so a bogus slug can no longer become a container id either. The cast is
			// also what the child is built from below: authorizing (int) $gallery and then
			// rendering the raw string would let '7abc' be approved as 7 and rendered as '7abc'.
			$gallery = (int) $gallery;

			if ( ! $this->is_gallery_in_displayed_albums( $gallery, $displayed_gallery ) ) {
				// Memoise the refusal as well as the success below. This method mutates router
				// state through do_app_rewrites() and runs a slug lookup and get_albums() before
				// reaching here, and index_action() now calls it before claiming the
				// one-gallery-per-post slot - so without this, every shortcode that refuses
				// repeats all of that on each call. Storing the album itself keeps the identity
				// comparison callers use ('alternate !== original' means "refused") intact.
				self::$alternate_displayed_galleries[ $id ] = $displayed_gallery;

				return $displayed_gallery;
			}

			$gallery_params = [
				'source'                  => 'galleries',
				'container_ids'           => [ $gallery ],
				'display_type'            => $display_settings['gallery_display_type'],
				'original_display_type'   => $displayed_gallery->display_type,
				'original_settings'       => $display_settings,
				'original_album_entities' => $parent_albums,
			];

			// Apply the gallery's own saved display settings for the child display type,
			// matching how the gallery renders outside an album (#787). += keeps the structural
			// keys set above; only settings the display type declares come through the filter.
			$gallery_entity  = $result ? $result : ( is_numeric( $gallery ) ? GalleryMapper::get_instance()->find( $gallery ) : null );
			$gallery_params += $this->saved_child_settings( $gallery_entity, $display_settings['gallery_display_type'] );

			// Deliberately no ngg_triggers_display compensation here, unlike the lightbox path below:
			// on this path the child resolves ngg_triggers_display exactly as the standalone render
			// does, so forcing 'always' for eCommerce galleries would make the album permalink
			// disagree with [imagely id=X] - reintroducing the very inconsistency #787 removes. A
			// saved 'never' is the owner's choice and is honored on both. Making eCommerce outrank
			// that choice is a product decision for both paths at once, not part of #787.
			if ( $result && ! empty( $result->is_ecommerce_enabled ) ) {
				$gallery_params['is_ecommerce_enabled']                      = 1;
				$gallery_params['original_settings']['is_ecommerce_enabled'] = 1;
			}

			if ( ! empty( $display_settings['gallery_display_template'] ) ) {
				$gallery_params['template'] = $display_settings['gallery_display_template'];
			}

			$displayed_gallery = $renderer->params_to_displayed_gallery( $gallery_params );
			if ( is_null( $displayed_gallery->id() ) ) {
				$displayed_gallery->id( md5( wp_json_encode( $displayed_gallery->get_entity() ) ) );
			}
			self::$alternate_displayed_galleries[ $id ] = $displayed_gallery;
		}

		return $displayed_gallery;
	}

	/**
	 * Filters a gallery's stored display settings down to what may safely be merged into the
	 * params of a child gallery rendered inside an album.
	 *
	 * The display_type_settings store is free-form: GalleryREST::sanitize_display_type_settings()
	 * casts values but whitelists no keys. Merged unfiltered, a stored key could become a
	 * structural argument in Renderer::params_to_displayed_gallery() or select a template file.
	 * Only keys the child display type actually declares are kept, minus RESERVED_CHILD_SETTINGS.
	 *
	 * Two known limits of this filter, both deliberate:
	 *
	 * 1. $display_type->settings is not a code-defined allow-list. DisplayType::set_defaults()
	 *    merges the controller defaults *under* the persisted row, and DisplayTypeREST::
	 *    update_display_type() persists arbitrary keys (cap: NextGEN Change style), so a declared
	 *    key can be anything an administrator stored. That is why the structural vocabulary is
	 *    denied unconditionally in RESERVED_CHILD_SETTINGS rather than merely being absent from
	 *    the allow-list. Conversely the set can be under-populated when the display type has no
	 *    controller, in which case the merge is a no-op and the child renders as it did before
	 *    #787 - degraded, never wrong.
	 * 2. Settings a display type uses but never declares (e.g. Pro's animate_* keys, written per
	 *    display type by the adminApp) are dropped, so an album child falls back to the global
	 *    animation settings. Widening the filter to admit undeclared keys is a separate change
	 *    (it needs its own deny-list rework, since undeclared keys are exactly where the
	 *    structural ones hide); #787 is about thumbnail geometry, which every relevant display
	 *    type declares. Do not "fix" this by unioning the raw blob.
	 *
	 * @param array  $saved_settings Stored settings for the child display type.
	 * @param string $child_type     Name of the child display type.
	 *
	 * @return array
	 */
	protected function filter_child_display_settings( array $saved_settings, string $child_type ): array {
		$declared = $this->child_display_type_settings( $child_type );

		if ( ! $declared ) {
			return [];
		}

		// $display_type->settings is the stored row with the controller's declared defaults merged
		// under it (DisplayType mapper set_defaults()), which is what lets this allow-list cover Pro
		// and addon types without knowing them. It is not authoritative in either direction, hence
		// the unconditional deny-list on top - see the docblock above.
		$allowed = array_diff_key( $declared, array_flip( self::RESERVED_CHILD_SETTINGS ) );

		return array_intersect_key( $saved_settings, $allowed );
	}

	/**
	 * Returns the settings a display type declares, i.e. the stored DisplayType row with the
	 * controller's defaults merged under it. [] when the display type is unknown.
	 *
	 * @param string $child_type Name of the display type.
	 *
	 * @return array
	 */
	private function child_display_type_settings( string $child_type ): array {
		$display_type = DisplayTypeMapper::get_instance()->find_by_name( $child_type );

		if ( ! $display_type || empty( $display_type->settings ) || ! is_array( $display_type->settings ) ) {
			return [];
		}

		return $display_type->settings;
	}

	/**
	 * Returns a gallery entity's saved, filtered display settings for a child display type,
	 * ready to merge into an album child's params. [] when the gallery has none. Shared by the
	 * album permalink and lightbox paths so the guard-and-filter lives in one place (#787).
	 *
	 * @param object|null $gallery_entity The child gallery entity, or null.
	 * @param string      $child_type     Name of the child display type.
	 *
	 * @return array
	 */
	protected function saved_child_settings( $gallery_entity, string $child_type ): array {
		if ( ! $gallery_entity || empty( $gallery_entity->display_type_settings ) || ! is_array( $gallery_entity->display_type_settings ) ) {
			return [];
		}

		$stored = $gallery_entity->display_type_settings;

		// Settings the gallery saved under the display type the album forces on it. This bucket is
		// an exact match for what the child renders, so it is carried whole - the same slice the
		// standalone render path applies, which is the parity #787 asks for.
		$out = ! empty( $stored[ $child_type ] ) && is_array( $stored[ $child_type ] )
			? $this->filter_child_display_settings( $stored[ $child_type ], $child_type )
			: [];

		$geometry = $this->cross_type_child_settings( $stored, $child_type, $out, $gallery_entity->display_type ?? '' );

		if ( ! $geometry ) {
			return $out;
		}

		// Take the geometry from one bucket only: drop the forced bucket's geometry keys entirely
		// rather than letting the ones the other bucket happens not to set survive underneath.
		// Anything dropped falls back to the child display type's declared default in
		// DisplayedGallery::merge_display_settings(), never to another layout's saved value.
		return array_merge( array_diff_key( $out, array_flip( self::CROSS_TYPE_CHILD_SETTINGS ) ), $geometry );
	}

	/**
	 * Returns the thumbnail geometry a gallery saved under its OWN display type, when that is
	 * where the override lives and the child render would otherwise miss it (#787, paid path).
	 *
	 * Pro Grid/List albums force gallery_display_type = pro_thumbnail_grid on their children, but
	 * the admin UI saves a gallery's override under the gallery's own display type (basic_thumbnails
	 * by default), so the forced type's bucket is empty and the child fell back to the global
	 * thumbnail size. Only CROSS_TYPE_CHILD_SETTINGS crosses the type boundary, and only as a whole
	 * group taken from a single bucket:
	 *
	 * - Nothing is carried unless the own bucket's `override_thumbnail_settings` is truthy. That
	 *   flag is what gates dynamic thumbnails in every consumer (Thumbnails, Pro ThumbnailGrid,
	 *   ...), so a gallery whose size the owner never overrode carries nothing and the child keeps
	 *   the forced type's configured geometry. This is also why a bucket that merely *mentions*
	 *   these keys - a UI snapshot, a stale value behind an unchecked box - cannot quietly switch
	 *   the child to on-disk thumbnails.
	 * - Nothing is carried when the forced type's own bucket already declares an override; that is
	 *   a deliberate, type-specific choice and outranks a value saved for another type.
	 * - The group replaces the forced bucket's geometry rather than filling gaps in it. Buckets are
	 *   routinely sparse (NormalizeDisplayTypeSettings::strip_defaults() deletes default-equal
	 *   keys), so filling gaps key-by-key could take a gate from one bucket and a dimension from
	 *   another and render a size the owner never chose anywhere.
	 *
	 * Everything else stays per-type on purpose: `images_per_page`, `number_of_columns` and the
	 * like mean different things to different layouts, and carrying them over would let a
	 * gallery's basic-thumbnails settings override the site's configured Pro Grid layout.
	 *
	 * @param array  $stored     The gallery's whole display_type_settings store.
	 * @param string $child_type Name of the display type the album forces on the child.
	 * @param array  $forced     Already-filtered settings from the forced type's own bucket.
	 * @param string $own_type   Name of the gallery's own display type.
	 *
	 * @return array Geometry to overlay on $forced, or [].
	 */
	private function cross_type_child_settings( array $stored, string $child_type, array $forced, string $own_type ): array {
		if ( ! empty( $forced['override_thumbnail_settings'] ) ) {
			return [];
		}

		if ( ! $own_type || $own_type === $child_type || empty( $stored[ $own_type ] ) || ! is_array( $stored[ $own_type ] ) ) {
			return [];
		}

		// Still allow-listed against the forced child type: a key it does not declare is not a
		// setting it can render, whichever bucket the value came from.
		$own = $this->filter_child_display_settings( $stored[ $own_type ], $child_type );

		if ( empty( $own['override_thumbnail_settings'] ) ) {
			return [];
		}

		$geometry = array_intersect_key( $own, array_flip( self::CROSS_TYPE_CHILD_SETTINGS ) );
		$declared = $this->child_display_type_settings( $child_type );

		// Complete the group from the forced type's own declared values. Saved buckets are sparse,
		// and the lightbox path (make_child_displayed_gallery()) merges onto the album's settings
		// with no display-type defaults layer beneath it, so a member left unset there would take
		// the album cover's value - e.g. this gallery's width against the album's crop. Filling the
		// group here keeps it self-contained on both paths and is a no-op on the permalink path,
		// where DisplayedGallery::merge_display_settings() would supply the same values anyway.
		foreach ( self::CROSS_TYPE_CHILD_SETTINGS as $key ) {
			if ( ! array_key_exists( $key, $geometry ) && array_key_exists( $key, $declared ) ) {
				$geometry[ $key ] = $declared[ $key ];
			}
		}

		return $geometry;
	}

	/**
	 * Returns the current page as an integer.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 * @return int
	 */
	public function get_current_page( DisplayedGallery $displayed_gallery ): int {
		$router = Router::get_instance();
		return (int) $router->get_parameter( 'page', $displayed_gallery->id(), 1 );
	}

	/**
	 * Returns the album description string.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return string
	 */
	public function get_description( DisplayedGallery $displayed_gallery ): string {
		// Important: do not array_shift() $displayed_gallery->container_ids as it will affect breadcrumbs.
		$container_ids = $displayed_gallery->container_ids;

		if ( 'galleries' === $displayed_gallery->source ) {
			$gallery_id = array_shift( $container_ids );
			$gallery    = GalleryMapper::get_instance()->find( $gallery_id );
			if ( $gallery && ! empty( $gallery->galdesc ) ) {
				return $gallery->galdesc;
			}
		} elseif ( 'albums' === $displayed_gallery->source ) {
			$album_id = array_shift( $container_ids );
			$album    = AlbumMapper::get_instance()->find( $album_id );
			if ( $album && ! empty( $album->albumdesc ) ) {
				return $album->albumdesc;
			}
		}

		return '';
	}

	/**
	 * Get the entities belonging to the displayed gallery for the current page.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return array
	 */
	public function get_entities( DisplayedGallery $displayed_gallery ): array {
		$current_page = $this->get_current_page( $displayed_gallery );
		$offset       = $displayed_gallery->display_settings['galleries_per_page'] * ( $current_page - 1 );

		return $displayed_gallery->get_included_entities( $displayed_gallery->display_settings['galleries_per_page'], $offset );
	}

	/**
	 * Get the first available image ID from an album's children (galleries or nested albums).
	 *
	 * Album IDs already walked are tracked in $visited so a cyclic or self-referential
	 * album tree cannot recurse until the stack is exhausted. Pruning an already-walked
	 * album never changes the result: it was explored in full and yielded no image.
	 *
	 * @param object $album The album object with sortorder property.
	 * @param object $image_mapper The image mapper instance.
	 * @param array  $visited Album IDs already visited (passed by reference).
	 *
	 * @return int|null The first image ID found, or null if none available.
	 */
	protected function get_first_image_from_album( $album, $image_mapper, array &$visited = [] ) {
		// A sortorder that failed to unserialize is truthy but not iterable.
		if ( empty( $album->sortorder ) || ! is_array( $album->sortorder ) ) {
			return null;
		}

		// Seed the starting album so an album listing itself is skipped too.
		if ( ! empty( $album->id_field ) && ! empty( $album->{$album->id_field} ) ) {
			$visited[ (int) $album->{$album->id_field} ] = true;
		}

		$gallery_mapper = GalleryMapper::get_instance();
		$album_mapper   = AlbumMapper::get_instance();

		// Iterate through sortorder to find the first available image.
		foreach ( $album->sortorder as $entity_id ) {
			// Check if this is a nested album (prefixed with 'a').
			if ( is_string( $entity_id ) && substr( $entity_id, 0, 1 ) === 'a' ) {
				$nested_album_id = intval( substr( $entity_id, 1 ) );
				if ( ! $nested_album_id || isset( $visited[ $nested_album_id ] ) ) {
					continue;
				}
				$visited[ $nested_album_id ] = true;
				$nested_album                = $album_mapper->find( $nested_album_id );

				if ( $nested_album ) {
					// If nested album has a preview pic, use it.
					if ( ! empty( $nested_album->previewpic ) && $nested_album->previewpic > 0 ) {
						return $nested_album->previewpic;
					}

					// Recursively check nested album's children.
					if ( ! empty( $nested_album->sortorder ) ) {
						$nested_preview = $this->get_first_image_from_album( $nested_album, $image_mapper, $visited );
						if ( $nested_preview ) {
							return $nested_preview;
						}
					}
				}
			} else {
				// This is a gallery ID.
				$gallery_id = intval( $entity_id );
				$gallery    = $gallery_mapper->find( $gallery_id );

				if ( $gallery ) {
					if ( ! empty( $gallery->previewpic ) && $gallery->previewpic > 0 ) {
						return $gallery->previewpic;
					}

					// find_all() takes condition strings, not a column map, and cannot express
					// limit/order. pid breaks the tie when every row still sits at sortorder 0.
					$images = ImageMapper::get_instance()
						->select()
						->where(
							[
								[ 'galleryid = %d', $gallery_id ],
								[ 'exclude = %d', 0 ],
							]
						)
						->order_by( 'sortorder' )
						->order_by( 'pid' )
						->limit( 1 )
						->run_query();

					if ( ! empty( $images ) ) {
						return $images[0]->pid;
					}
				}
			}
		}

		return null;
	}

	/**
	 * Returns the order that Album display types appear in the IGW selector.
	 *
	 * @return float
	 */
	public function get_order(): float {
		return NGG_DISPLAY_PRIORITY_BASE + NGG_DISPLAY_PRIORITY_STEP;
	}

	/**
	 * Get the original children belonging to an album when viewing another child.
	 *
	 * @param array $display_settings Array of display type settings.
	 *
	 * @return array
	 */
	public function get_original_album_entities( array $display_settings ): array {
		if ( isset( $display_settings['original_album_entities'] ) ) {
			return $display_settings['original_album_entities'];
		} elseif ( isset( $display_settings['original_settings'] ) && $this->get_original_album_entities( $display_settings['original_settings'] ) ) {
			return $this->get_original_album_entities( $display_settings['original_settings'] );
		}

		return [];
	}

	/**
	 * Gets the parent album for the entity being displayed.
	 *
	 * @param int|string $entity_id Gallery ID.
	 * @return null|object Album object.
	 */
	public function get_parent_album_for( $entity_id ) {
		$retval = null;

		foreach ( $this->albums as $album ) {
			// Using strict comparison here breaks the breadcrumb generation.
			//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( in_array( $entity_id, $album->sortorder ) ) {
				$retval = $album;
				break;
			}
		}

		return $retval;
	}

	/**
	 * Renders the displayed gallery.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 * @param bool             $return_output Return or print the result.
	 *
	 * @return ?string
	 */
	public function index_action( $displayed_gallery, $return_output = false ) {
		$router = Router::get_instance();

		$this->cast_numeric_settings( $displayed_gallery, self::NUMERIC_SETTINGS );

		// We need to fetch the selected album containers. We need to do this, because once we fetch the included
		// entities, we need to iterate over each entity and assign it a parent_id, which is the album that it belongs
		// to. We need to do this because the link to the gallery, is not /nggallery/gallery--id,
		// but /nggallery/album--id/gallery--id.

		// Are we to display a gallery? Ensure our 'gallery' isn't just a paginated album view.
		$gallery = $router->get_parameter( 'gallery' );
		$album   = $router->get_parameter( 'album' );
		if ( $gallery && strpos( $gallery, 'nggpage--' ) !== 0 ) {
			// Resolve the drill-down before claiming the one-gallery-per-post slot. The
			// alternate comes back unchanged when ?gallery= names a gallery outside this
			// shortcode's own albums (#811), and a shortcode that refuses must not consume
			// the slot: on a page with several album shortcodes the one that legitimately
			// contains the gallery may be reached second, and claiming the slot first would
			// leave it rendering nothing.
			$alternate_displayed_gallery = $this->get_alternate_displayed_gallery( $displayed_gallery );
			if ( $alternate_displayed_gallery !== $displayed_gallery ) {
				// Basic albums only support one per post.
				if ( isset( $GLOBALS['nggShowGallery'] ) ) {
					return '';
				}

				$GLOBALS['nggShowGallery'] = true;

				$renderer = Renderer::get_instance( 'inner' );

				// For legacy templates we just generate the description & breadcrumb string and prepend it to the generated
				// display of the gallery. For modern templates we attach a filter to the display renderer just for this
				// one particular gallery, that seeks out the container element and injects the breadcrumbs there.
				\add_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_description_to_legacy_templates' ], 8, 2 );
				\add_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_breadcrumbs_to_legacy_templates' ], 9, 2 );
				\add_filter( 'ngg_display_type_rendering_object', [ $this, 'add_breadcrumbs_and_descriptions' ], 10, 2 );

				$output = $renderer->display_images( $alternate_displayed_gallery, $return_output );

				\remove_filter( 'ngg_display_type_rendering_object', [ $this, 'add_breadcrumbs_and_descriptions' ], 10 );
				\remove_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_description_to_legacy_templates' ], 8 );
				\remove_filter( 'ngg_displayed_gallery_rendering', [ $this, 'add_breadcrumbs_to_legacy_templates' ], 9 );

				return $output;
			}
		} elseif ( ! is_null( $album ) ) {
			// If we're viewing a sub-album, then we use that album as a container instead.
			// Are we to display a sub-album?
			$result    = AlbumMapper::get_instance()->get_by_slug( $album );
			$album_sub = $result ? $result->{$result->id_field} : null;
			if ( null !== $album_sub ) {
				$album = $album_sub;
			} elseif ( '0' !== $album && 'all' !== $album ) {
				// Slug did not resolve to a real album; fall back to the main album view
				// instead of storing the raw slug as a container id.
				$album = null;
			}

			if ( null !== $album ) {
				// Preserve the original album list before altering the DisplayedGallery.
				$original_albums = $displayed_gallery->get_albums();

				// Only navigate into the requested album when it belongs to this shortcode's
				// own album hierarchy. Without this check every album shortcode on the page
				// reacts to the same ?album= parameter and renders identical content. The
				// 'all'/'0' reset values are non-numeric and fall through to the reset below.
				$album_numeric_id = is_numeric( $album_sub ) ? (int) $album_sub : 0;

				if ( ! $album_numeric_id || $this->is_album_in_hierarchy( $album_numeric_id, AlbumMapper::get_instance(), $original_albums ) ) {
					if ( in_array( $album, $displayed_gallery->container_ids, true ) ) {
						$viewing_original_album = true;
					}

					$displayed_gallery->entity_ids    = [];
					$displayed_gallery->sortorder     = [];
					$displayed_gallery->container_ids = ( '0' === $album || 'all' === $album ) ? [] : [ $album ];

					$displayed_gallery->display_settings['original_album_id']       = 'a' . $album_sub;
					$displayed_gallery->display_settings['original_album_entities'] = array_merge( $original_albums, $displayed_gallery->get_albums() );
				}
			}
		}

		// Get the albums
		// TODO: This should probably be moved to the elseif block above.
		$this->albums = $displayed_gallery->get_albums();

		// None of the above: Display the main album. Get the settings required for display.
		$entities = $this->get_entities( $displayed_gallery );

		// If there are entities to be displayed.
		if ( $entities ) {
			$display_settings = $this->prepare_display_settings(
				$displayed_gallery->get_entity(),
				$displayed_gallery->display_settings
			);

			if ( ! empty( $display_settings['template'] ) && 'default' !== $display_settings['template'] ) {
				// Add additional parameters.
				$router->get_routed_app()->remove_parameter( 'ajax_pagination_referrer' );
				$display_settings['current_page'] = $this->get_current_page( $displayed_gallery );

				$breadcrumbs = $this->render_legacy_template_breadcrumbs( $displayed_gallery, $entities );
				$description = $this->render_legacy_template_description( $displayed_gallery );

				// If enabled enqueue the child entities as JSON for lightboxes to read.
				$retval = $this->legacy_render( $display_settings['template'], $display_settings, $return_output, 'album' );

				if ( ! empty( $description ) ) {
					$retval = $description . $retval;
				}

				if ( ! isset( $viewing_original_album ) && ! empty( $breadcrumbs ) ) {
					$retval = $breadcrumbs . $retval;
				}

				return $retval;
			} else {
				$params = $display_settings;
				$params = $this->prepare_display_parameters( $displayed_gallery, $params );

				$view = new View( $this->template, $params, $this->legacy_template );

				// Rather than messing with filters and return values, this method just directly calls add_breadcrumbs_and_descriptions().
				$view_element = $view->render_object();
				if ( ! isset( $viewing_original_album ) ) {
					$view_element = $this->add_breadcrumbs_and_descriptions( $view_element, $displayed_gallery );
				}
				$content = $view->rasterize_object( $view_element );

				if ( ! $return_output ) {
					// We cannot truly escape this content as it may come from user-supplied or 3rd party templates.
					echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				return $content;
			}
		} else {
			$view = new View(
				'GalleryDisplay/NoImagesFound',
				[],
				'photocrati-nextgen_gallery_display#no_images_found'
			);
			return $view->render( $return_output );
		}
	}

	/**
	 * Determines whether the DisplayedGallery is a basic album.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return bool
	 */
	public function is_basic_album( DisplayedGallery $displayed_gallery ): bool {
		return in_array( $displayed_gallery->display_type, [ NGG_BASIC_COMPACT_ALBUM, NGG_BASIC_EXTENDED_ALBUM ], true );
	}

	/**
	 * Creates a displayed gallery of a gallery belonging to an album. Shared by index_action() and enqueue_frontend_resources()
	 * to allow lightboxes to open album children directly.
	 *
	 * @param \stdClass $gallery Object.
	 * @param array     $display_settings An Array of display type settings.
	 *
	 * @return object
	 */
	public function make_child_displayed_gallery( \stdClass $gallery, array $display_settings ) {
		// Apply the gallery's own saved display settings for the child display type (#787),
		// mirroring get_alternate_displayed_gallery(). array_merge lets the saved settings win over
		// the album's incoming defaults; the eCommerce overrides below still run after this.
		//
		// Unlike the permalink path this child is built as a bare DisplayedGallery with no
		// display_type assigned, so DisplayedGallery::merge_display_settings() never runs and the
		// base here is the album's own settings (including its cover geometry), not the child display
		// type's defaults. That is pre-existing and deliberately left alone: this displayed gallery
		// exists to carry the lightbox effect code and inline JS payload for the child, while the
		// album's tiles are sized from the album's settings by the caller - assigning display_type
		// would re-base every existing album lightbox child, which is well outside #787. What #787
		// needs from it is that a carried thumbnail override arrives as a complete group rather than
		// splicing into the album's geometry, and saved_child_settings() guarantees that.
		if ( ! empty( $display_settings['gallery_display_type'] ) ) {
			// $gallery is the album-loop entity and already carries display_type / display_type_settings
			// as direct ngg_gallery columns, so read from it instead of a per-child GalleryMapper::find().
			$display_settings = array_merge(
				$display_settings,
				$this->saved_child_settings( $gallery, $display_settings['gallery_display_type'] )
			);
		}

		if ( ! empty( $gallery->is_ecommerce_enabled ) ) {
			$display_settings['is_ecommerce_enabled'] = 1;
			// Album display types set ngg_triggers_display='never'; override so eCommerce trigger icons
			// are shown for this individual gallery (not suppressed by the album context).
			if ( ! isset( $display_settings['ngg_triggers_display'] ) || 'never' === $display_settings['ngg_triggers_display'] ) {
				$display_settings['ngg_triggers_display'] = 'always';
			}
		}

		$gallery->displayed_gallery                    = new DisplayedGallery();
		$gallery->displayed_gallery->container_ids     = [ $gallery->{$gallery->id_field} ];
		$gallery->displayed_gallery->display_settings  = $display_settings;
		$gallery->displayed_gallery->returns           = 'included';
		$gallery->displayed_gallery->source            = 'galleries';
		$gallery->displayed_gallery->images_list_count = $gallery->displayed_gallery->get_entity_count();
		$gallery->displayed_gallery->is_album_gallery  = true;
		$gallery->displayed_gallery->to_transient();

		$displayed_gallery = $gallery->displayed_gallery;

		// Add "galleries = {};".
		DisplayManager::add_script_data(
			'ngg_common',
			'galleries',
			new \stdClass(),
			true,
			false
		);

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

		return $gallery;
	}

	/**
	 * Prepares the correct display settings to use for the current situation. Registers album children when necessary
	 * for the "Open album children in lightbox" feature.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 * @param array            $params Array of display type settings.
	 *
	 * @return array
	 */
	public function prepare_display_settings( DisplayedGallery $displayed_gallery, array $params ): array {
		$image_gen    = ThumbnailsManager::get_instance();
		$image_mapper = ImageMapper::get_instance();
		$router       = Router::get_instance();
		$storage      = StorageManager::get_instance();

		$app = $router->get_routed_app();

		$ajax_pagination_referrer = $router->get_parameter( 'ajax_pagination_referrer' );
		$pagination_result        = $this->create_pagination(
			$this->get_current_page( $displayed_gallery ),
			$displayed_gallery->get_entity_count(),
			$params['galleries_per_page'],
			urldecode( $ajax_pagination_referrer ? $ajax_pagination_referrer : '' )
		);

		$params['displayed_gallery'] = $displayed_gallery;
		$params['entities']          = $this->get_entities( $displayed_gallery );
		$params['pagination']        = $pagination_result['output'];
		$params['pagination_next']   = $pagination_result['next'];
		$params['pagination_prev']   = $pagination_result['prev'];

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
			$gallery->previewurl                       = '';
			$gallery->previewpic_fullsized_display_url = '';
			$preview_image_id                          = $gallery->previewpic;

			// If no preview is set for an album, try to get the first image from its children.
			if ( ( ! $preview_image_id || $preview_image_id <= 0 ) && $gallery->is_album && ! empty( $gallery->sortorder ) ) {
				$preview_image_id = $this->get_first_image_from_album( $gallery, $image_mapper );
			}

			if ( $preview_image_id && $preview_image_id > 0 ) {
				$image = $image_mapper->find( intval( $preview_image_id ) );
				if ( $image ) {
					$gallery->previewpic_image = $image;

					// previewpic_fullsized_url is an <a href> in the compact album, so it stays
					// canonical; the lightbox and <img src> attributes get the cache-buster so an
					// edited cover refreshes for returning visitors (#829).
					$gallery->previewpic_fullsized_url         = $storage->get_image_url( $image );
					$gallery->previewpic_fullsized_display_url = $storage->get_cache_busted_image_url( $image );

					$gallery->previewurl  = $storage->get_cache_busted_image_url( $image, $image_gen->get_size_name( $image_gen_params ) );
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
					$page = get_post( $gallery->pageid );
					if ( $page && isset( $page->ID ) ) {
						$gallery->pagelink = get_page_link( $page->ID );
					}
				}
				if ( empty( $gallery->pagelink ) ) {
					$pagelink          = $app->get_routed_url( true );
					$pagelink          = $app->remove_parameter( 'album', null, $pagelink );
					$pagelink          = $app->remove_parameter( 'gallery', null, $pagelink );
					$pagelink          = $app->remove_parameter( 'nggpage', null, $pagelink );
					$pagelink          = $app->set_parameter( 'album', $gallery->slug, null, false, $pagelink );
					$gallery->pagelink = $pagelink;
				}
			} else {
				// Otherwise, if it's a gallery then it will look like
				// /nggallery/album--slug/gallery--slug.

				if ( $gallery->pageid > 0 ) {
					$page = get_post( $gallery->pageid );
					if ( $page && isset( $page->ID ) ) {
						$gallery->pagelink = get_page_link( $page->ID );
					}
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
					} elseif ( [ '0' ] === $displayed_gallery->container_ids || [ '' ] === $displayed_gallery->container_ids ) {
						// Legacy compat: use an album slug of 'all' if we're missing a container_id.
						$pagelink = $app->set_parameter( 'album', 'all', null, false, $pagelink );
					} else {
						$pagelink = $app->remove_parameter( 'nggpage', null, $pagelink );
						$pagelink = $app->remove_parameter( 'album', null, $pagelink );
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
			if ( ! empty( $displayed_gallery->display_settings['open_gallery_in_lightbox'] ) && 'gallery' === $gallery->entity_type ) {
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
		 * Register each gallery belonging to the album that has just been rendered, so that when the MVC controller
		 * system 'catches up' and runs $this->render_object() that method knows what galleries to inline as JS.
		 */
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

		self::$display_settings[ $displayed_gallery->id() ] = $params;

		return $params;
	}

	/**
	 * Renders breadcrumb HTML for legacy templates.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 * @param array            $entities Array of album children.
	 * @param ?int             $gallery_id Gallery ID.
	 *
	 * @return string|null
	 */
	public function render_legacy_template_breadcrumbs( DisplayedGallery $displayed_gallery, array $entities, $gallery_id = false ) {
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

	/**
	 * Renders description HTML for legacy templates.
	 *
	 * @param DisplayedGallery $displayed_gallery DisplayedGallery object.
	 *
	 * @return string|null
	 */
	public function render_legacy_template_description( DisplayedGallery $displayed_gallery ) {
		if ( ! empty( $displayed_gallery->display_settings['template'] ) && $this->are_descriptions_enabled( $displayed_gallery->display_settings ) ) {
			return $this->generate_description( $displayed_gallery );
		} else {
			return '';
		}
	}
}
