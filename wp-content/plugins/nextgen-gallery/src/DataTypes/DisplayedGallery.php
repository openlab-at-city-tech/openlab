<?php

namespace Imagely\NGG\DataTypes;

use Imagely\NGG\DataMapper\Model;
use Imagely\NGG\DataMappers\Album as AlbumMapper;
use Imagely\NGG\DataMappers\DisplayType as DisplayTypeMapper;
use Imagely\NGG\DataMappers\DisplayedGallery as Mapper;
use Imagely\NGG\DataMappers\Gallery as GalleryMapper;
use Imagely\NGG\DataMappers\Image as ImageMapper;
use Imagely\NGG\Display\I18N;
use Imagely\NGG\DisplayedGallery\SourceManager;
use Imagely\NGG\DisplayType\ControllerFactory;
use Imagely\NGG\Settings\Settings;
use Imagely\NGG\Util\Transient;

class DisplayedGallery extends Model {

	public $ID;
	public $album_ids;
	public $container_ids;
	public $display;
	public $display_settings;
	public $display_type;
	public $effect_code;
	public $entity_ids;
	public $excluded_container_ids;
	public $exclusions;
	public $gallery_ids;
	public $id;
	public $ids;
	public $image_ids;
	public $images_list_count;
	public $inner_content;
	public $is_album_gallery;
	public $maximum_entity_count;
	public $order_by;
	public $order_direction;
	public $returns;
	public $skip_excluding_globally_excluded_images;
	public $slug;
	public $sortorder;
	public $source;
	public $src;
	public $tag_ids;
	public $tagcloud;
	public $transient_id;

	// The "alternative" approach to using "ORDER BY RAND()" works by finding X image PID in a kind of shotgun-blast
	// like scattering in a second query made via $wpdb that is then fed into the query built by _get_image_entities().
	// This variable is used to cache the results of that inner quasi-random PID retrieval so that multiple calls
	// to $displayed_gallery->get_entities() don't return different results for each invocation. This is important
	// for NextGen Pro's galleria module in order to 'localize' the results of get_entities() to JSON.
	protected static $_random_image_ids_cache = [];

	public function __construct( \stdClass $object = null ) {
		parent::__construct( $object );

		// Apply the default display type settings.
		if ( isset( $this->display_type ) ) {
			// Settings are stored in posts and must be found by their ID while shortcodes are likely using an alias.
			if ( ControllerFactory::has_controller( $this->display_type ) ) {
				$this->display_type = ControllerFactory::get_display_type_id( $this->display_type );
			}

			$mapper       = DisplayTypeMapper::get_instance();
			$display_type = $mapper->find_by_name( $this->display_type );
			if ( $display_type ) {
				$this->display_settings = array_merge( $display_type->settings, $this->display_settings );
				$this->display_type     = $display_type->name;
			}
		}

		// Only some sources should have their own maximum_entity_count.
		if ( ! empty( $this->display_settings['maximum_entity_count'] )
		&& in_array( $this->source, [ 'tag', 'tags', 'random_images', 'recent_images', 'random', 'recent' ] ) ) {
			$this->maximum_entity_count = $this->display_settings['maximum_entity_count'];
		}

		// If no maximum_entity_count has been given, then set a maximum.
		if ( ! isset( $this->maximum_entity_count ) ) {
			$settings                   = Settings::get_instance();
			$this->maximum_entity_count = $settings->get( 'maximum_entity_count', 500 );
		}

		\do_action( 'ngg_displayed_gallery_construct', $this );
	}

	public function get_mapper() {
		return Mapper::get_instance();
	}

	/**
	 * @param int    $limit Limit
	 * @param int    $offset Offset
	 * @param bool   $id_only ID Only
	 * @param string $returns Included/Excluded
	 * @return array
	 */
	public function get_entities( $limit = false, $offset = false, $id_only = false, $returns = 'included' ) {
		$retval     = [];
		$source_obj = $this->get_source();
		$max        = $this->get_maximum_entity_count();

		if ( ! $limit || ( is_numeric( $limit ) && $limit > $max ) ) {
			$limit = $max;
		}

		// Ensure that all parameters have values that are expected.
		if ( $this->_parse_parameters() ) {
			// Is this an image query?
			if ( in_array( 'image', $source_obj->returns ) ) {
				$retval = $this->_get_image_entities( $source_obj, $limit, $offset, $id_only, $returns );
			}

			// Is this a gallery/album query?
			elseif ( in_array( 'gallery', $source_obj->returns ) ) {
				$retval = $this->_get_album_and_gallery_entities( $source_obj, $limit, $offset, $id_only, $returns );
			}
		}

		return $retval;
	}

	/**
	 * Gets all images in the displayed gallery
	 *
	 * @param \stdClass $source_obj
	 * @param int       $limit
	 * @param int       $offset
	 * @param boolean   $id_only
	 * @param string    $returns
	 */
	public function _get_image_entities( $source_obj, $limit, $offset, $id_only, $returns ) {
		global $wpdb;

		$settings  = Settings::get_instance();
		$mapper    = ImageMapper::get_instance();
		$image_key = $mapper->get_primary_key_column();
		$select    = $id_only ? $image_key : $mapper->get_table_name() . '.*';

		if ( strtoupper( $this->order_direction ) == 'DSC' ) {
			$this->order_direction = 'DESC';
		}

		$sort_direction = in_array( strtoupper( $this->order_direction ), [ 'ASC', 'DESC' ] )
			? $this->order_direction
			: $settings->get( 'galSortDir' );

		$sort_by = in_array( strtolower( $this->order_by ), array_merge( ImageMapper::get_instance()->get_column_names(), [ 'rand()' ] ) )
			? $this->order_by
			: $settings->get( 'galSort' );

		$this->container_ids = $this->container_ids ? array_map( [ $wpdb, '_escape' ], $this->container_ids ) : [];
		$this->entity_ids    = $this->entity_ids ? array_map( [ $wpdb, '_escape' ], $this->entity_ids ) : [];
		$this->exclusions    = $this->exclusions ? array_map( [ $wpdb, '_escape' ], $this->exclusions ) : [];

		// Here's what this method is doing:
		// 1) Determines what results need returned
		// 2) Determines from what container ids the results should come from
		// 3) Applies ORDER BY clause
		// 4) Applies LIMIT/OFFSET clause
		// 5) Executes the query and returns the result.

		// We start with the most difficult query. When returns is "both", we need to return a list of both included
		// and excluded entity ids, and mark specifically which entities are excluded.
		if ( $returns == 'both' ) {
			// We need to add two dynamic columns, one called "sortorder" and the other called "exclude".
			$if_true      = 1;
			$if_false     = 0;
			$excluded_set = $this->entity_ids;

			if ( ! $excluded_set ) {
				$if_true      = 0;
				$if_false     = 1;
				$excluded_set = $this->exclusions;
			}

			$sortorder_set = $this->sortorder ?: $excluded_set;

			// Add sortorder column.
			if ( $sortorder_set ) {
				$select = $this->_add_find_in_set_column( $select, $image_key, $sortorder_set, 'new_sortorder', true );

				// A user might want to sort the results by the order of images that they specified to be included.
				// For that we need some trickery by reversing the order direction.
				$sort_direction = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
				$sort_by        = 'new_sortorder';
			}

			// Add exclude column.
			if ( $excluded_set ) {
				$select  = $this->_add_find_in_set_column( $select, $image_key, $excluded_set, 'exclude' );
				$select .= ", IF (exclude = 0 AND @exclude = 0, $if_true, $if_false) AS 'exclude'";
			}

			// Select what we want.
			$mapper->select( $select );
		}

		// When returns is "included", the query is relatively simple. We just provide a where clause to limit how many
		// images we're returning based on the entity_ids, exclusions, and container_ids parameters.
		if ( $returns == 'included' ) {
			// If the sortorder property is available, then we need to override the sortorder.
			if ( $this->sortorder ) {
				$select         = $this->_add_find_in_set_column( $select, $image_key, $this->sortorder, 'new_sortorder', true );
				$sort_direction = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
				$sort_by        = 'new_sortorder';
			}

			$mapper->select( $select );

			// Filter based on entity_ids selection.
			if ( $this->entity_ids ) {
				$mapper->where( [ "{$image_key} IN %s", $this->entity_ids ] );
			}

			// Filter based on exclusions selection.
			if ( $this->exclusions ) {
				$mapper->where( [ "{$image_key} NOT IN %s", $this->exclusions ] );
			}

			// Ensure that no images marked as excluded at the gallery level are returned.
			if ( empty( $this->skip_excluding_globally_excluded_images ) ) {
				$mapper->where( [ 'exclude = %d', 0 ] );
			}
		} elseif ( $returns == 'excluded' ) {
			// When returns is "excluded", it's a little more complicated as the query is the negated form of the
			// "included". entity_ids become the list of exclusions, and exclusions become the list of entity_ids to
			// return. All results we return must be marked as excluded.

			// If the sortorder property is available, then we need to override the sortorder.
			if ( $this->sortorder ) {
				$select         = $this->_add_find_in_set_column( $select, $image_key, $this->sortorder, 'new_sortorder', true );
				$sort_direction = $this->order_direction == 'ASC' ? 'DESC' : 'ASC';
				$sort_by        = 'new_sortorder';
			}

			// Mark each result as excluded.
			$select .= ', 1 AS exclude';
			$mapper->select( $select );

			// Is this case, entity_ids become the exclusions.
			$exclusions = $this->entity_ids;

			// Remove the exclusions always takes precedence over entity_ids, so we adjust the list of ids.
			if ( $this->exclusions ) {
				foreach ( $this->exclusions as $excluded_entity_id ) {
					if ( ( $index = array_search( $excluded_entity_id, $exclusions ) ) !== false ) {
						unset( $exclusions[ $index ] );
					}
				}
			}

			// Filter based on exclusions selection.
			if ( $exclusions ) {
				$mapper->where( [ "{$image_key} NOT IN %s", $exclusions ] );
			}

			// Filter based on selected exclusions.
			elseif ( $this->exclusions ) {
				$mapper->where( [ "{$image_key} IN %s", $this->exclusions ] );
			}

			// Ensure that images marked as excluded are returned as well.
			$mapper->where( [ 'exclude = 1' ] );
		}

		// Filter based on containers_ids. Container ids is a little more complicated as it can contain gallery ids or tags.
		if ( $this->container_ids ) {
			// Container ids are tags.
			if ( $source_obj->name == 'tags' ) {
				$term_ids = $this->get_term_ids_for_tags( $this->container_ids );
				$mapper->where( [ "{$image_key} IN %s", get_objects_in_term( $term_ids, 'ngg_tag' ) ] );
			} else {
				// Container ids are gallery ids.
				$mapper->where( [ 'galleryid IN %s', $this->container_ids ] );
			}
		}

		// Filter based on excluded container ids.
		if ( $this->excluded_container_ids ) {
			// Container ids are tags.
			if ( $source_obj->name == 'tags' ) {
				$term_ids = $this->get_term_ids_for_tags( $this->excluded_container_ids );
				$mapper->where( [ "{$image_key} NOT IN %s", get_objects_in_term( $term_ids, 'ngg_tag' ) ] );
			} else {
				// Container ids are gallery ids.
				$mapper->where( [ 'galleryid NOT IN %s', $this->excluded_container_ids ] );
			}
		}

		// Adjust the query more based on what source was selected.
		if ( in_array( $this->source, [ 'recent', 'recent_images' ] ) ) {
			$sort_direction = 'DESC';
			$sort_by        = apply_filters( 'ngg_recent_images_sort_by_column', 'imagedate' );
		} elseif ( $this->source == 'random_images' && empty( $this->entity_ids ) ) {
			// A gallery with source=random and a non-empty entity_ids is treated as source=images & image_ids=(entity_ids)
			// In this case however source is random but no image ID are pre-filled.
			//
			// Here we must transform our query from "SELECT * FROM ngg_pictures WHERE gallery_id = X" into something
			// like "SELECT * FROM ngg_pictures WHERE pid IN (SELECT pid FROM ngg_pictures WHERE gallery_id = X ORDER BY RAND())".
			$table_name    = $mapper->get_table_name();
			$where_clauses = [];
			$old_where_sql = '';

			// $this->get_entities_count() works by calling count(get_entities()) which means that for random galleries
			// there will be no limit passed to this method -- adjust the $limit now based on the maximum_entity_count.
			$max = $this->get_maximum_entity_count();
			if ( ! $limit || ( is_numeric( $limit ) && $limit > $max ) ) {
				$limit = $max;
			}

			foreach ( $mapper->where_clauses as $where ) {
				$where_clauses[] = '(' . $where . ')';
			}

			if ( $where_clauses ) {
				$old_where_sql = 'WHERE ' . implode( ' AND ', $where_clauses );
			}

			$noExtras = '/*NGG_NO_EXTRAS_TABLE*/';

			if ( Settings::get_instance()->get( 'use_alternate_random_method' ) ) {
				// Check if the random image PID have been cached and use them (again) if already found.
				$id = $this->ID();
				if ( ! empty( self::$_random_image_ids_cache[ $id ] ) ) {
					$image_ids = self::$_random_image_ids_cache[ $id ];
				} else {
					global $wpdb;

					// Prevent infinite loops: retrieve the image count and if needed just pull in every image available.
					// PHP-CS flags this but it is a false positive, the $old_where_sql is an already prepared SQL string.
					//
					// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$total     = $wpdb->get_var( "SELECT COUNT(`pid`) FROM {$wpdb->nggpictures} {$old_where_sql}" );
					$image_ids = [];

					if ( $total <= $limit ) {
						// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$image_ids = $wpdb->get_col( "SELECT `pictures`.`pid` FROM {$wpdb->nggpictures} `pictures` {$old_where_sql} LIMIT {$total}" );
					} else {
						// Start retrieving random ID from the DB and hope they exist; continue looping until our count is full.
						$segments = ceil( $limit / 4 );
						while ( count( $image_ids ) < $limit ) {
							$newID     = $this->_query_random_ids_for_cache( $segments, $old_where_sql );
							$image_ids = array_merge( array_unique( $image_ids ), $newID );
						}
					}

					// Prevent overflow.
					if ( count( $image_ids ) > $limit ) {
						array_splice( $image_ids, $limit );
					}

					// Give things an extra shake.
					shuffle( $image_ids );

					// Cache these ID in memory so that any attempts to call get_entities() more than once will result
					// in the same images being retrieved for the duration of that page execution.
					self::$_random_image_ids_cache[ $id ] = $image_ids;
				}

				$image_ids = implode( ',', $image_ids );

				// Replace the existing WHERE clause with one where aready retrieved "random" PID are included.
				$mapper->where_clauses = [ " {$noExtras} `{$image_key}` IN ({$image_ids}) {$noExtras}" ];
			} else {
				// Replace the existing WHERE clause with one that selects from a sub-query that is randomly ordered.
				$sub_where             = "SELECT `{$image_key}` FROM `{$table_name}` i {$old_where_sql} ORDER BY RAND() LIMIT {$limit}";
				$mapper->where_clauses = [ " {$noExtras} `{$image_key}` IN (SELECT `{$image_key}` FROM ({$sub_where}) o) {$noExtras}" ];
			}
		}

		// Apply a sorting order.
		if ( $sort_by ) {
			$mapper->order_by( $sort_by, $sort_direction );
		}

		// Apply a limit.
		if ( $limit ) {
			if ( $offset ) {
				$mapper->limit( $limit, $offset );
			} else {
				$mapper->limit( $limit );
			}
		}

		$results = $mapper->run_query();

		if ( ! is_admin() && in_array( 'image', $source_obj->returns ) ) {
			foreach ( $results as $entity ) {
				if ( ! empty( $entity->description ) ) {
					$entity->description = I18N::translate( $entity->description, 'pic_' . $entity->pid . '_description' );
				}
				if ( ! empty( $entity->alttext ) ) {
					$entity->alttext = I18N::translate( $entity->alttext, 'pic_' . $entity->pid . '_alttext' );
				}
			}
		}

		return $results;
	}

	/**
	 * @param int    $limit
	 * @param string $where_sql Must be the full "WHERE x=y" string
	 * @return int[]
	 */
	public function _query_random_ids_for_cache( $limit = 10, $where_sql = '' ) {
		global $wpdb;
		$mod = rand( 3, 9 );

		if ( empty( $where_sql ) ) {
			$where_sql = 'WHERE 1=1';
		}

		// The following query uses $where_sql which is an already prepared clause generated by the DataMapper
		//
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		return $wpdb->get_col(
			"SELECT `pictures`.`pid` from {$wpdb->nggpictures} `pictures`
                    JOIN (SELECT CEIL(MAX(`pid`) * RAND()) AS `pid` FROM {$wpdb->nggpictures}) AS `x` ON `pictures`.`pid` >= `x`.`pid`
                    {$where_sql}
                    AND `pictures`.`pid` MOD {$mod} = 0
                    LIMIT {$limit}"
		);
		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}

	/**
	 * Gets all gallery and album entities from albums specified, if any
	 *
	 * @param \stdClass $source_obj
	 * @param int       $limit
	 * @param int       $offset
	 * @param boolean   $id_only
	 * @param array     $returns
	 */
	public function _get_album_and_gallery_entities( $source_obj, $limit = false, $offset = false, $id_only = false, $returns = 'included' ) {
		// Album queries are difficult and inefficient to perform due to the database schema. To complicate things,
		// we're returning two different types of entities - galleries, and sub-albums. The user prefixes entity_id's
		// with an 'a' to distinguish album ids from gallery ids. E.g. entity_ids=[1, "a2", 3].
		$album_mapper   = AlbumMapper::get_instance();
		$gallery_mapper = GalleryMapper::get_instance();
		$album_key      = $album_mapper->get_primary_key_column();
		$gallery_key    = $gallery_mapper->get_primary_key_column();
		$select         = $id_only ? $album_key . ', sortorder' : $album_mapper->get_table_name() . '.*';
		$retval         = [];

		// If no exclusions are specified, are entity_ids are specified, and we're to return is "included", then we
		// have a relatively easy query to perform - we just fetch each entity listed in the entity_ids field.
		if ( $returns == 'included' && $this->entity_ids && empty( $this->exclusions ) ) {
			$retval = $this->_entities_to_galleries_and_albums( $this->entity_ids, $id_only, [], $limit, $offset );
		} else {
			// It's not going to be easy. We'll start by fetching the albums and retrieving each of their entities.
			$album_mapper->select( $select );

			// Fetch the albums, and find the entity ids of the sub-albums and galleries.
			$entity_ids   = [];
			$excluded_ids = [];

			// Filter by container ids. If container_ids === '0' we retrieve all existing gallery_ids and use them as
			// the available entity_ids for comparability with 1.9x.
			$container_ids = $this->container_ids;
			if ( $container_ids ) {
				if ( $container_ids !== [ '0' ] && $container_ids !== [ '' ] ) {
					$container_ids = array_map( 'intval', $container_ids );
					$album_mapper->where( [ "{$album_key} IN %s", $container_ids ] );

					// This order_by is necessary for albums to be ordered correctly given the WHERE .. IN() above.
					$order_string = implode( ',', $container_ids );
					$album_mapper->order_by( "FIELD('id', {$order_string})" );

					foreach ( $album_mapper->run_query() as $album ) {
						$entity_ids = array_merge( $entity_ids, (array) $album->sortorder );
					}
				} elseif ( $container_ids === [ '0' ] || $container_ids === [ '' ] ) {
					foreach ( $gallery_mapper->select( $gallery_key )->run_query() as $gallery ) {
						$entity_ids[] = $gallery->$gallery_key;
					}
				}
			}

			// Break the list of entities into two groups, included entities and excluded entity ids
			// If a specific list of entity ids have been specified, then we know what entity ids are meant to be
			// included. We can compute the intersection and also determine what entity ids are to be excluded.
			if ( $this->entity_ids ) {
				// Determine the real list of included entity ids. Exclusions always take precedence.
				$included_ids = $this->entity_ids;
				foreach ( $this->exclusions as $excluded_id ) {
					if ( ( $index = array_search( $excluded_id, $included_ids ) ) !== false ) {
						unset( $included_ids[ $index ] );
					}
				}
				$excluded_ids = array_diff( $entity_ids, $included_ids );
			} elseif ( $this->exclusions ) {
				// We only have a list of exclusions.
				$included_ids = array_diff( $entity_ids, $this->exclusions );
				$excluded_ids = array_diff( $entity_ids, $included_ids );
			} else {
				// We have no entity ids and no exclusions.
				$included_ids = $entity_ids;
			}

			// We've built our two groups. Let's determine how we'll focus on them.  We're interested in only the included ids.
			if ( $returns == 'included' ) {
				$retval = $this->_entities_to_galleries_and_albums( $included_ids, $id_only, [], $limit, $offset );
			}

			// We're interested in only the excluded ids.
			elseif ( $returns == 'excluded' ) {
				$retval = $this->_entities_to_galleries_and_albums( $excluded_ids, $id_only, $excluded_ids, $limit, $offset );
			}

			// We're interested in both groups.
			else {
				$retval = $this->_entities_to_galleries_and_albums( $entity_ids, $id_only, $excluded_ids, $limit, $offset );
			}
		}

		return $retval;
	}

	/**
	 * Takes a list of entities, and returns the mapped galleries and sub-albums
	 *
	 * @param array $entity_ids
	 * @param bool  $id_only
	 * @param array $exclusions
	 * @param int   $limit
	 * @param int   $offset
	 * @return array
	 */
	public function _entities_to_galleries_and_albums( $entity_ids, $id_only = false, $exclusions = [], $limit = false, $offset = false ) {
		$retval         = [];
		$gallery_ids    = [];
		$album_ids      = [];
		$album_mapper   = AlbumMapper::get_instance();
		$gallery_mapper = GalleryMapper::get_instance();
		$image_mapper   = ImageMapper::get_instance();
		$album_key      = $album_mapper->get_primary_key_column();
		$gallery_key    = $gallery_mapper->get_primary_key_column();
		$album_select   = ( $id_only ? $album_key : $album_mapper->get_table_name() . '.*' ) . ', 1 AS is_album,   0 AS is_gallery, name AS title, albumdesc AS galdesc';
		$gallery_select = ( $id_only ? $gallery_key : $gallery_mapper->get_table_name() . '.*' ) . ', 1 AS is_gallery, 0 AS is_album';

		// Modify the sort order of the entities.
		if ( $this->sortorder ) {
			$sortorder  = array_intersect( $this->sortorder, $entity_ids );
			$entity_ids = array_merge( $sortorder, array_diff( $entity_ids, $sortorder ) );
		}

		// Segment entity ids into two groups - galleries and albums.
		foreach ( $entity_ids as $entity_id ) {
			if ( substr( $entity_id, 0, 1 ) == 'a' ) {
				$album_ids[] = intval( substr( $entity_id, 1 ) );
			} else {
				$gallery_ids[] = intval( $entity_id );
			}
		}

		// Adjust query to include an exclude property.
		if ( $exclusions ) {
			$album_select   = $this->_add_find_in_set_column( $album_select, $album_key, $this->exclusions, 'exclude' );
			$album_select   = $this->_add_if_column( $album_select, 'exclude', 0, 1 );
			$gallery_select = $this->_add_find_in_set_column( $gallery_select, $gallery_key, $this->exclusions, 'exclude' );
			$gallery_select = $this->_add_if_column( $gallery_select, 'exclude', 0, 1 );
		}

		// Add sorting parameter to the gallery and album queries.
		if ( $gallery_ids ) {
			$gallery_select = $this->_add_find_in_set_column( $gallery_select, $gallery_key, $gallery_ids, 'ordered_by', true );
		} else {
			$gallery_select .= ', 0 AS ordered_by';
		}

		if ( $album_ids ) {
			$album_select = $this->_add_find_in_set_column( $album_select, $album_key, $album_ids, 'ordered_by', true );
		} else {
			$album_select .= ', 0 AS ordered_by';
		}

		// Fetch entities.
		$galleries = $gallery_mapper
			->select( $gallery_select )
			->where( [ "{$gallery_key} IN %s", $gallery_ids ] )
			->order_by( 'ordered_by', 'DESC' )
			->run_query();

		$counts = $image_mapper
			->select( 'galleryid, COUNT(*) as counter' )
			->where( [ [ 'galleryid IN %s', $gallery_ids ], [ 'exclude = %d', 0 ] ] )
			->group_by( 'galleryid' )
			->run_query( false, false, true );

		$albums = $album_mapper
			->select( $album_select )
			->where( [ "{$album_key} IN %s", $album_ids ] )
			->order_by( 'ordered_by', 'DESC' )
			->run_query();

		// Reorder entities according to order specified in entity_ids.
		foreach ( $entity_ids as $entity_id ) {
			if ( substr( $entity_id, 0, 1 ) == 'a' ) {
				$album = array_shift( $albums );
				if ( $album ) {
					$retval[] = $album;
				}
			} else {
				$gallery = array_shift( $galleries );
				if ( $gallery ) {
					foreach ( $counts as $id => $gal_count ) {
						if ( $gal_count->galleryid == $gallery->gid ) {
							$gallery->counter = intval( $gal_count->counter );
							unset( $counts[ $id ] );
						}
					}
					$retval[] = $gallery;
				}
			}
		}

		// Sort the entities.
		if ( $this->order_by && $this->order_by != 'sortorder' ) {
			usort( $retval, [ &$this, '_sort_album_result' ] );
		}
		if ( $this->order_direction == 'DESC' ) {
			$retval = array_reverse( $retval );
		}

		// Limit the entities.
		if ( $limit ) {
			$retval = array_slice( $retval, $offset, $limit );
		}

		return $retval;
	}

	/**
	 * Returns the total number of entities in this displayed gallery
	 *
	 * @param string $returns
	 * @return int
	 */
	public function get_entity_count( $returns = 'included' ) {
		$retval = 0;

		// Is this an image query?
		$source_obj = $this->get_source();

		if ( in_array( 'image', $source_obj->returns ) ) {
			$retval = count( $this->_get_image_entities( $source_obj, false, false, true, $returns ) );
		}

		// Is this a gallery/album query?
		elseif ( in_array( 'gallery', $source_obj->returns ) ) {
			$retval = count( $this->_get_album_and_gallery_entities( $source_obj, false, false, true, $returns ) );
		}

		$max = $this->get_maximum_entity_count();

		if ( $retval > $max ) {
			$retval = $max;
		}

		return $retval;
	}

	/**
	 * Honor the gallery 'maximum_entity_count' setting ONLY when dealing with random & recent galleries. All others
	 * will always obey the *global* 'maximum_entity_count' setting.
	 */
	public function get_maximum_entity_count() {
		$max = intval( Settings::get_instance()->get( 'maximum_entity_count', 500 ) );

		$sources    = SourceManager::get_instance();
		$source_obj = $this->get_source();
		if ( in_array( $source_obj, [ $sources->get( 'random' ), $sources->get( 'random_images' ), $sources->get( 'recent' ), $sources->get( 'recent_images' ) ] ) ) {
			$max = intval( $this->maximum_entity_count );
		}

		return $max;
	}

	/**
	 * Returns all included entities for the displayed gallery
	 *
	 * @param int     $limit
	 * @param int     $offset
	 * @param boolean $id_only
	 * @return array
	 */
	public function get_included_entities( $limit = false, $offset = false, $id_only = false ) {
		return $this->get_entities( $limit, $offset, $id_only, 'included' );
	}

	/**
	 * Adds a FIND_IN_SET call to the select portion of the query, and optionally defines a dynamic column
	 *
	 * @param string  $select
	 * @param string  $key
	 * @param array   $array
	 * @param string  $alias
	 * @param boolean $add_column
	 * @return string
	 */
	public function _add_find_in_set_column( $select, $key, $array, $alias, $add_column = false ) {
		$array = array_map( 'intval', $array );
		$set   = implode( ',', array_reverse( $array ) );

		if ( ! $select ) {
			$select = '1';
		}

		$select .= ", @{$alias} := FIND_IN_SET({$key}, '{$set}')";

		if ( $add_column ) {
			$select .= " AS {$alias}";
		}

		return $select;
	}

	public function _add_if_column( $select, $alias, $true = 1, $false = 0 ) {
		if ( ! $select ) {
			$select = '1';
		}
		$select .= ", IF(@{$alias} = 0, {$true}, {$false}) AS {$alias}";
		return $select;
	}

	/**
	 * Parses the list of parameters provided in the displayed gallery, and ensures everything meets expectations
	 *
	 * @return boolean
	 */
	public function _parse_parameters() {
		$valid = false;

		// Ensure that the source is valid.
		if ( SourceManager::get_instance()->get( $this->source ) ) {
			$valid = true;
		}

		// Ensure that exclusions, entity_ids, and sortorder have valid elements. IE likes to send empty array as an
		// array with a single element that has no value.
		if ( $this->exclusions && ! $this->exclusions[0] ) {
			$this->exclusions = [];
		}
		if ( $this->entity_ids && ! $this->entity_ids[0] ) {
			$this->entity_ids = [];
		}
		if ( $this->sortorder && ! $this->sortorder[0] ) {
			$this->sortorder = [];
		}

		return $valid;
	}

	/**
	 * Returns a list of term ids for the list of tags
	 *
	 * @global \wpdb $wpdb
	 * @param array $tags
	 * @return array
	 */
	public function get_term_ids_for_tags( $tags = false ) {
		global $wpdb;

		// If no tags were provided, get them from the container_ids.
		if ( ! $tags || ! is_array( $tags ) ) {
			$tags = $this->container_ids;
		}

		// Convert container ids to a string suitable for WHERE IN.
		$container_ids = [];
		if ( is_array( $tags ) && ! in_array( 'all', array_map( 'strtolower', $tags ) ) ) {
			foreach ( $tags as $ndx => $container ) {
				$container       = esc_sql( str_replace( '%', '%%', $container ) );
				$container_ids[] = "'{$container}'";
			}

			$container_ids = implode( ',', $container_ids );
		}

		// Construct query.
		$query = "SELECT {$wpdb->term_taxonomy}.term_id FROM {$wpdb->term_taxonomy}
                  INNER JOIN {$wpdb->terms} ON {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
                  WHERE {$wpdb->term_taxonomy}.term_id = {$wpdb->terms}.term_id
                  AND {$wpdb->term_taxonomy}.taxonomy = %s";

		if ( ! empty( $container_ids ) ) {
			$query .= " AND ({$wpdb->terms}.slug IN ({$container_ids}) OR {$wpdb->terms}.name IN ({$container_ids}))";
		}

		$query .= " ORDER BY {$wpdb->terms}.term_id";

		// This is a false positive
		//
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$query = $wpdb->prepare( $query, 'ngg_tag' );

		// Get all term_ids for each image tag slug.
		$term_ids = [];

		// This is a false positive
		//
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $query );
		if ( is_array( $results ) && ! empty( $results ) ) {
			foreach ( $results as $row ) {
				$term_ids[] = $row->term_id;
			}
		}

		return $term_ids;
	}

	/**
	 * Sorts the results of an album query
	 *
	 * @param \stdClass $a
	 * @param \stdClass $b
	 * @return int
	 */
	public function _sort_album_result( $a, $b ) {
		$key = $this->order_by;
		if ( ! isset( $a->$key ) || ! isset( $b->$key ) ) {
			return 0;
		}

		return strcmp( $a->$key, $b->$key );
	}

	/**
	 * Gets the display type object used in this displayed gallery
	 *
	 * @return null|\Imagely\NGG\DataTypes\DisplayType
	 */
	public function get_display_type() {
		return DisplayTypeMapper::get_instance()->find_by_name( $this->display_type );
	}

	/**
	 * Gets albums queried in this displayed gallery
	 *
	 * @return array
	 */
	public function get_albums() {
		$retval = [];
		if ( ( $source = $this->get_source() ) ) {
			if ( in_array( 'album', $source->returns ) ) {
				$mapper    = AlbumMapper::get_instance();
				$album_key = $mapper->get_primary_key_column();
				if ( $this->container_ids ) {
					$mapper->select()->where( [ "{$album_key} IN %s", $this->container_ids ] );
				}
				$retval = $mapper->run_query();
			}
		}

		return $retval;
	}

	/**
	 * Ensures the 'id' attribute is copied to 'ID' as both are used frequently
	 *
	 * @param $value
	 * @return mixed
	 */
	public function id( $value = null ) {
		$retval   = parent::id( $value );
		$this->ID = $this->id;
		return $retval;
	}

	/**
	 * Returns a transient for the displayed gallery
	 *
	 * @return string
	 */
	public function to_transient() {
		$params = $this->get_entity();
		unset( $params->transient_id );

		$key = Transient::create_key( 'displayed_galleries', $params );

		if ( is_null( Transient::fetch( $key, null ) ) ) {
			Transient::update( $key, $params, NGG_DISPLAYED_GALLERY_CACHE_TTL );
		}

		$this->transient_id = $key;
		if ( ! $this->id() ) {
			$this->id( $key );
		}

		return $key;
	}

	public function validation() {
		$retval = [];

		$display_type = $this->get_display_type();
		if ( ! $display_type ) {
			$retval['display_type'][] = 'Invalid display type';
		}

		// Is the display type compatible with the source? E.g., if we're using a display type that expects images,
		// we can't be feeding it galleries and albums.
		$source_manager = SourceManager::get_instance();
		if ( ! $source_manager->is_compatible( $this->get_source(), $display_type ) ) {
			$retval['display_type'][] = __( 'Source not compatible with selected display type', 'nggallery' );
		}

		$retval = array_merge(
			$retval,
			$this->validates_presence_of( 'source' ),
			$this->validates_presence_of( 'display_type' )
		);

		return empty( $retval ) ? true : $retval;
	}

	/**
	 * In case we are dealing with a legacy displayed gallery that was a post under a custom post type registry
	 */
	public function get_entity() {
		$entity = $this;
		unset( $entity->comment_count );
		unset( $entity->comment_status );
		unset( $entity->filter );
		unset( $entity->guid );
		unset( $entity->ping_status );
		unset( $entity->pinged );
		unset( $entity->post_author );
		unset( $entity->post_content_filtered );
		unset( $entity->post_date );
		unset( $entity->post_date_gmt );
		unset( $entity->post_excerpt );
		unset( $entity->post_mime_type );
		unset( $entity->post_modified );
		unset( $entity->post_modified_gmt );
		unset( $entity->post_name );
		unset( $entity->post_parent );
		unset( $entity->post_status );
		unset( $entity->post_title );
		unset( $entity->post_type );
		unset( $entity->to_ping );

		return $entity;
	}

	/**
	 * Gets the corresponding source instance
	 *
	 * @return \stdClass
	 */
	public function get_source() {
		return SourceManager::get_instance()->get( $this->source );
	}

	/**
	 * Returns the galleries queries in this displayed gallery
	 *
	 * @return array
	 */
	public function get_galleries() {
		$retval = [];
		if ( ( $source = $this->get_source() ) ) {
			if ( in_array( 'image', $source->returns ) ) {
				$mapper      = GalleryMapper::get_instance();
				$gallery_key = $mapper->get_primary_key_column();
				$mapper->select();
				if ( $this->container_ids ) {
					$mapper->where( [ "{$gallery_key} IN %s", $this->container_ids ] );
				}
				$retval = $mapper->run_query();
			}
		}
		return $retval;
	}
}
