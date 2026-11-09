<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataMapper\WPPostDriver;
use Imagely\NGG\DisplayType\ControllerFactory;

/**
 * DisplayType data mapper class.
 *
 * Handles database operations for display type entities, including CRUD operations
 * and display type-specific functionality.
 */
class DisplayType extends WPPostDriver {

	/**
	 * Object cache group for display type lookups.
	 *
	 * @var string
	 */
	const CACHE_GROUP = 'ngg_display_types';

	/**
	 * Short TTL for not-found / empty-result entries — avoids freezing transient misses for 24 h.
	 *
	 * @var int
	 */
	const NEGATIVE_CACHE_TTL = 300;

	/**
	 * Singleton instance of the DisplayType mapper.
	 *
	 * @var DisplayType|\Imagely\NGGPro\DataMappers\DisplayType|null
	 */
	public static $instance;

	/**
	 * The model class this mapper handles.
	 *
	 * @var string
	 */
	public $model_class = 'Imagely\NGG\DataTypes\DisplayType';

	/**
	 * WP object cache group.
	 *
	 * @var string
	 */
	protected $object_cache_group = 'ngg_display_types';

	/**
	 * Constructor.
	 *
	 * Defines the database table structure and initializes the mapper.
	 */
	public function __construct() {
		// Define columns.
		$this->define_column( 'ID', 'BIGINT', 0 );
		$this->define_column( 'default_source', 'VARCHAR(255)' );
		$this->define_column( 'name', 'VARCHAR(255)' );
		$this->define_column( 'preview_image_relpath', 'VARCHAR(255)' );
		$this->define_column( 'title', 'VARCHAR(255)' );
		$this->define_column( 'view_order', 'BIGINT', NGG_DISPLAY_PRIORITY_BASE );
		$this->define_column( 'settings', 'MEDIUMTEXT' );

		$this->add_serialized_column( 'settings' );
		$this->add_serialized_column( 'entity_types' );

		parent::__construct( 'display_type' );
	}

	/**
	 * Gets the singleton instance of the DisplayType mapper.
	 *
	 * @return DisplayType|\Imagely\NGGPro\DataMappers\DisplayType The DisplayType mapper instance.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			$class          = apply_filters( 'ngg_datamapper_client_display_type', __CLASS__ );
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * Finds a display type by its name.
	 *
	 * @param string $name The display type name to search for.
	 * @return null|\Imagely\NGG\DataTypes\DisplayType The display type entity or null if not found.
	 */
	public function find_by_name( $name ) {
		$cache_key = 'ngg_display_type_by_name_' . $name;
		$cached    = wp_cache_get( $cache_key, self::CACHE_GROUP );
		if ( false !== $cached ) {
			return '' !== $cached ? $cached : null;
		}

		$retval = null;
		$this->select();
		$this->where( [ 'name = %s', $name ] );
		$this->limit( 1 ); // name is unique; prevents posts_per_page=-1 default on cache miss.

		$results = $this->run_query();

		if ( ! $results ) {
			// Cache find_all() result to avoid N unbounded queries per request when aliases trigger this fallback.
			$all = wp_cache_get( 'ngg_display_type_find_all', self::CACHE_GROUP );
			if ( false === $all ) {
				$all     = $this->find_all();
				$all_ttl = ! empty( $all ) ? DAY_IN_SECONDS : self::NEGATIVE_CACHE_TTL;
				wp_cache_set( 'ngg_display_type_find_all', $all, self::CACHE_GROUP, $all_ttl );
			}
			foreach ( $all as $entity ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( $entity->name == $name || ( isset( $entity->aliases ) && is_array( $entity->aliases ) && in_array( $name, $entity->aliases ) ) ) {
					$retval = $entity;
					break;
				}
			}
		} else {
			$retval = $results[0];
		}

		// Store '' as sentinel for null: wp_cache_get returns false for a miss, but null is a valid "not found"
		// result. Using '' as the stored value lets us distinguish a cache hit (not found) from a cache miss.
		// Use a short TTL for not-found entries — a transient miss (e.g. lookup before Pro registers its
		// display types) must not be frozen as authoritative for 24 h.
		$ttl = null !== $retval ? DAY_IN_SECONDS : self::NEGATIVE_CACHE_TTL;
		wp_cache_set( $cache_key, null !== $retval ? $retval : '', self::CACHE_GROUP, $ttl );

		return $retval;
	}

	/**
	 * Saves a display type entity and busts the object cache.
	 *
	 * @param \Imagely\NGG\DataTypes\DisplayType $entity The display type entity to save.
	 * @return bool|int
	 */
	public function save( $entity ) {
		$new_name = $entity->name ?? null;
		$old_name = null;

		// Bust the old-name cache entry when the display type is being renamed.
		$pkey      = $this->get_primary_key_column();
		$entity_id = isset( $entity->$pkey ) ? intval( $entity->$pkey ) : 0;
		if ( $entity_id ) {
			$existing = $this->find( $entity_id );
			$old_name = $existing->name ?? null;
		}

		$retval = parent::save( $entity );
		if ( $retval ) {
			if ( $new_name ) {
				wp_cache_delete( 'ngg_display_type_by_name_' . $new_name, self::CACHE_GROUP );
			}
			if ( $old_name && $old_name !== $new_name ) {
				wp_cache_delete( 'ngg_display_type_by_name_' . $old_name, self::CACHE_GROUP );
			}
			wp_cache_delete( 'ngg_display_type_find_all', self::CACHE_GROUP );
		}
		return $retval;
	}

	/**
	 * Destroys a display type entity and busts the object cache.
	 *
	 * @param \Imagely\NGG\DataTypes\DisplayType|int $entity The entity or its ID.
	 * @param bool                                   $skip_trash Whether to skip the trash.
	 * @return bool
	 */
	public function destroy( $entity, $skip_trash = true ) {
		$name = null;
		if ( is_object( $entity ) && isset( $entity->name ) ) {
			$name = $entity->name;
		} elseif ( is_numeric( $entity ) ) {
			$existing = $this->find( (int) $entity );
			$name     = $existing->name ?? null;
		}
		$retval = parent::destroy( $entity, $skip_trash );
		if ( $retval ) {
			if ( $name ) {
				wp_cache_delete( 'ngg_display_type_by_name_' . $name, self::CACHE_GROUP );
			}
			wp_cache_delete( 'ngg_display_type_find_all', self::CACHE_GROUP );
		}
		return $retval;
	}

	/**
	 * Finds display types by entity type.
	 *
	 * @param string|array $entity_type The entity type(s) to search for, e.g. image, gallery, album.
	 * @return null|\Imagely\NGG\DataTypes\DisplayType[] Array of display types or null if none found.
	 */
	public function find_by_entity_type( $entity_type ) {
		$find_entity_types = is_array( $entity_type ) ? $entity_type : [ $entity_type ];

		$all = wp_cache_get( 'ngg_display_type_find_all', self::CACHE_GROUP );
		if ( false === $all ) {
			$all     = $this->find_all();
			$all_ttl = ! empty( $all ) ? DAY_IN_SECONDS : self::NEGATIVE_CACHE_TTL;
			wp_cache_set( 'ngg_display_type_find_all', $all, self::CACHE_GROUP, $all_ttl );
		}

		$retval = null;
		foreach ( $all as $display_type ) {
			foreach ( $find_entity_types as $entity_type ) {
				// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( isset( $display_type->entity_types ) && in_array( $entity_type, $display_type->entity_types ) ) {
					$retval[] = $display_type;
					break;
				}
			}
		}

		return $retval;
	}

	/**
	 * Gets the post title for a display type entity.
	 *
	 * @param \Imagely\NGG\DataTypes\DisplayType $entity The display type entity.
	 * @return string The post title.
	 */
	public function get_post_title( $entity ) {
		return $entity->title;
	}

	/**
	 * Sets default values for a display type entity.
	 *
	 * @param \Imagely\NGG\DataTypes\DisplayType $entity The display type entity to set defaults for.
	 */
	public function set_defaults( $entity ) {
		if ( ! isset( $entity->settings ) ) {
			$entity->settings = [];
		}

		$this->set_default_value( $entity, 'aliases', [] );
		$this->set_default_value( $entity, 'default_source', '' );
		$this->set_default_value( $entity, 'hidden_from_igw', false );
		$this->set_default_value( $entity, 'hidden_from_ui', false ); // TODO: remove.
		$this->set_default_value( $entity, 'preview_image_relpath', '' );
		$this->set_default_value( $entity, 'settings', 'use_lightbox_effect', true );
		$this->set_default_value( $entity, 'view_order', NGG_DISPLAY_PRIORITY_BASE );

		// Ensure that no display settings are ever missing if the controller provides defaults.
		if ( ControllerFactory::has_controller( $entity->name ) ) {
			$controller = ControllerFactory::get_controller( $entity->name );
			if ( ! method_exists( $controller, 'get_default_settings' ) ) {
				return;
			}
			$entity->settings = array_merge( $controller->get_default_settings(), $entity->settings );
		}
	}
}
