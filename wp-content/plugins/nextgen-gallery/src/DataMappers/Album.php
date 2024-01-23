<?php

namespace Imagely\NGG\DataMappers;

use Imagely\NGG\DataMapper\TableDriver;
use Imagely\NGG\Display\I18N;
use Imagely\NGG\Util\Transient;

class Album extends TableDriver {

	private static $instance = null;

	public $model_class = 'Imagely\NGG\DataTypes\Album';

	public $primary_key_column = 'id';

	// Necessary for legacy compatibility.
	public $custom_post_name = 'mixin_nextgen_table_extras';

	public function __construct() {
		$this->define_column( 'albumdesc', 'TEXT' );
		$this->define_column( 'id', 'BIGINT', 0 );
		$this->define_column( 'name', 'VARCHAR(255)' );
		$this->define_column( 'pageid', 'BIGINT', 0 );
		$this->define_column( 'previewpic', 'BIGINT', 0 );
		$this->define_column( 'slug', 'VARCHAR(255' );
		$this->define_column( 'sortorder', 'TEXT' );
		$this->define_column( 'extras_post_id', 'BIGINT', 0 );

		$this->add_serialized_column( 'sortorder' );

		parent::__construct( 'ngg_album' );
	}

	/**
	 * @return Album
	 */
	static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Album();
		}
		return self::$instance;
	}

	/**
	 * @param string $slug
	 * @return null|\Imagely\NGG\DataTypes\Album
	 */
	public function get_by_slug( $slug ) {
		$results = $this->select()->where( [ 'slug = %s', sanitize_title( $slug ) ] )->limit( 1 )->run_query();
		return array_pop( $results );
	}

	public function save_entity( $entity ) {
		$retval = parent::save_entity( $entity );

		if ( $retval ) {
			\do_action( 'ngg_album_updated', $entity );
			Transient::flush( 'displayed_gallery_rendering' );
		}

		return $retval;
	}

	/**
	 * @param \Imagely\NGG\DataTypes\Album
	 */
	public function set_defaults( $entity ) {
		$this->set_default_value( $entity, 'name', '' );
		$this->set_default_value( $entity, 'albumdesc', '' );
		$this->set_default_value( $entity, 'sortorder', [] );
		$this->set_default_value( $entity, 'previewpic', 0 );
		$this->set_default_value( $entity, 'exclude', 0 );

		if ( isset( $entity->name ) && ! isset( $entity->slug ) ) {
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
