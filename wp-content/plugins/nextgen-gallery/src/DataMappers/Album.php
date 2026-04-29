<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataMapper\TableDriver;
use Imagely\NGG\Display\I18N;
use Imagely\NGG\Util\Transient;

/**
 * Album data mapper class.
 *
 * Handles database operations for album entities, including CRUD operations
 * and album-specific functionality.
 */
class Album extends TableDriver {

	/**
	 * Singleton instance of the Album mapper.
	 *
	 * @var Album|null
	 */
	private static $instance = null;

	/**
	 * The model class this mapper handles.
	 *
	 * @var string
	 */
	public $model_class = 'Imagely\NGG\DataTypes\Album';

	/**
	 * The primary key column name.
	 *
	 * @var string
	 */
	public $primary_key_column = 'id';

	/**
	 * Custom post name for legacy compatibility.
	 *
	 * @var string
	 */
	public $custom_post_name = 'mixin_nextgen_table_extras';

	/**
	 * Constructor.
	 *
	 * Defines the database table structure and initializes the mapper.
	 */
	public function __construct() {
		$this->define_column( 'albumdesc', 'TEXT' );
		$this->define_column( 'id', 'BIGINT', 0 );
		$this->define_column( 'name', 'VARCHAR(255)' );
		$this->define_column( 'pageid', 'BIGINT', 0 );
		$this->define_column( 'previewpic', 'BIGINT', 0 );
		$this->define_column( 'slug', 'VARCHAR(255)' );
		$this->define_column( 'sortorder', 'TEXT' );
		$this->define_column( 'extras_post_id', 'BIGINT', 0 );

		// Add date columns
		$this->define_column( 'date_created', 'DATETIME' );
		$this->define_column( 'date_modified', 'DATETIME' );

		// Add display type related columns
		$this->define_column( 'display_type', 'VARCHAR(255)', 'photocrati-nextgen_basic_compact_album' );
		$this->define_column( 'display_type_settings', 'MEDIUMTEXT' );

		$this->add_serialized_column( 'sortorder' );
		$this->add_serialized_column( 'display_type_settings' );

		parent::__construct( 'ngg_album' );
	}

	/**
	 * Gets the singleton instance of the Album mapper.
	 *
	 * @return Album The Album mapper instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Album();
		}
		return self::$instance;
	}

	/**
	 * Gets an album by its slug.
	 *
	 * @param string $slug The album slug to search for.
	 * @return null|\Imagely\NGG\DataTypes\Album The album entity or null if not found.
	 */
	public function get_by_slug( $slug ) {
		$results = $this->select()->where( [ 'slug = %s', sanitize_title( $slug ) ] )->limit( 1 )->run_query();
		return array_pop( $results );
	}

	/**
	 * Saves an album entity to the database.
	 *
	 * @param \Imagely\NGG\DataTypes\Album $entity The album entity to save.
	 * @return mixed The result of the save operation.
	 */
	public function save_entity( $entity ) {
		// Set dates for new or existing entities
		$current_time = current_time( 'mysql', true );
		if ( empty( $entity->{$entity->id_field} ) ) {
			$entity->date_created = $current_time;
		}
		$entity->date_modified = $current_time;

		// Initialize display type settings before saving
		$this->initialize_display_type_settings( $entity );

		$retval = parent::save_entity( $entity );

		if ( $retval ) {
			\do_action( 'ngg_album_updated', $entity );
			Transient::flush( 'displayed_gallery_rendering' );
			Transient::flush( 'rest_albums' );
		}

		return $retval;
	}

	/**
	 * Destroys an album entity.
	 *
	 * @param int|\Imagely\NGG\DataTypes\Album $entity The album ID or entity to destroy.
	 * @return bool True if successfully destroyed, false otherwise.
	 */
	public function destroy( $entity ) {
		$retval = parent::destroy( $entity );

		if ( $retval ) {
			Transient::flush( 'displayed_gallery_rendering' );
			Transient::flush( 'rest_albums' );
		}

		return $retval;
	}

	/**
	 * Gets all available display types and their default settings
	 *
	 * @return array Array of display types and their default settings
	 */
	private function get_all_display_type_defaults() {
		$display_mapper = \Imagely\NGG\DataMappers\DisplayType::get_instance();
		$display_types  = $display_mapper->find_by_entity_type( 'album' );
		$defaults       = [];

		if ( ! $display_types ) {
			return $defaults;
		}

		foreach ( $display_types as $display_type ) {
			if ( ! empty( $display_type->hidden_from_ui ) ) {
				continue;
			}
			$defaults[ $display_type->name ] = $display_type->settings;
		}

		return $defaults;
	}

	/**
	 * Ensures display type settings are properly initialized with defaults
	 *
	 * @param object $entity The album entity
	 * @return void
	 */
	private function initialize_display_type_settings( $entity ) {
		// Initialize display type settings if not set
		if ( ! is_array( $entity->display_type_settings ) ) {
			$entity->display_type_settings = [];
		}

		// Get defaults for all display types
		$all_defaults = $this->get_all_display_type_defaults();

		// Ensure all display types have settings
		foreach ( $all_defaults as $type_name => $defaults ) {
			if ( ! isset( $entity->display_type_settings[ $type_name ] ) ) {
				$sanitized_defaults = array_map(
					function ( $value ) {
						return is_bool( $value ) ? (int) $value : $value;
					},
					$defaults
				);

				$entity->display_type_settings[ $type_name ] = $sanitized_defaults;
			}
		}
	}

	/**
	 * Sets default values for an album entity.
	 *
	 * @param \Imagely\NGG\DataTypes\Album $entity The album entity.
	 */
	public function set_defaults( $entity ) {
		$this->set_default_value( $entity, 'name', '' );
		$this->set_default_value( $entity, 'albumdesc', '' );
		$this->set_default_value( $entity, 'sortorder', [] );
		$this->set_default_value( $entity, 'previewpic', 0 );
		$this->set_default_value( $entity, 'exclude', 0 );

		// Normalize sortorder to ensure all gallery IDs are integers and album IDs have 'a' prefix
		if ( ! empty( $entity->sortorder ) && is_array( $entity->sortorder ) ) {
			$entity->sortorder = array_map(
				function ( $item ) {
					// If it starts with 'a', it's an album - keep the prefix and sanitize the ID
					if ( is_string( $item ) && strpos( $item, 'a' ) === 0 ) {
							return 'a' . absint( substr( $item, 1 ) );
					}
					// Otherwise it's a gallery ID - convert to integer
					return absint( $item );
				},
				$entity->sortorder
			);
		}

		if ( isset( $entity->name ) && empty( $entity->slug ) ) {
			$entity->slug = \nggdb::get_unique_slug( sanitize_title( $entity->name ), 'album' );
		}

		if ( ! is_admin() && ! empty( $entity->{$entity->id_field} ) ) {
			if ( ! empty( $entity->name ) ) {
				$entity->name = I18N::translate( $entity->name, 'album_' . $entity->{$entity->id_field} . '_name' );
			}
			if ( ! empty( $entity->albumdesc ) ) {
				$entity->albumdesc = I18N::translate( $entity->albumdesc, 'album_' . $entity->{$entity->id_field} . '_description' );
			}

			// these fields are set when the album is a child to another album.
			if ( ! empty( $entity->title ) ) {
				$entity->title = I18N::translate( $entity->title, 'album_' . $entity->{$entity->id_field} . '_name' );
			}
			if ( ! empty( $entity->galdesc ) ) {
				$entity->galdesc = I18N::translate( $entity->galdesc, 'album_' . $entity->{$entity->id_field} . '_description' );
			}
		}
	}
}
