<?php

namespace Imagely\NGG\DisplayedGallery;

use Imagely\NGG\DataTypes\DisplayType;

class SourceManager {

	private $sources             = [];
	private $entity_types        = [];
	private $registered_defaults = [];

	/* @var SourceManager */
	private static $instance = null;

	/**
	 * @return SourceManager
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new SourceManager();
		}
		return self::$instance;
	}

	public function register_defaults() {
		// Entity types must be registered first!!!.
		// ----------------------------------------.
		$this->register_entity_type( 'gallery', 'galleries' );
		$this->register_entity_type( 'image', 'images' );
		$this->register_entity_type( 'album', 'albums' );

		// Galleries.
		$galleries          = new \stdClass();
		$galleries->name    = 'galleries';
		$galleries->title   = __( 'Galleries', 'nggallery' );
		$galleries->aliases = [ 'gallery', 'images', 'image' ];
		$galleries->returns = [ 'image' ];
		$this->register( $galleries->name, $galleries );

		// Albums.
		$albums          = new \stdClass();
		$albums->name    = 'albums';
		$albums->title   = __( 'Albums', 'nggallery' );
		$albums->aliases = [ 'album' ];
		$albums->returns = [ 'album', 'gallery' ];
		$this->register( $albums->name, $albums );

		// Tags.
		$tags          = new \stdClass();
		$tags->name    = 'tags';
		$tags->title   = __( 'Tags', 'nggallery' );
		$tags->aliases = [ 'tag', 'image_tags', 'image_tag' ];
		$tags->returns = [ 'image' ];
		$this->register( $tags->name, $tags );

		// Random Images.
		$random          = new \stdClass();
		$random->name    = 'random_images';
		$random->title   = __( 'Random Images', 'nggallery' );
		$random->aliases = [ 'random', 'random_image' ];
		$random->returns = [ 'image' ];
		$this->register( $random->name, $random );

		// Recent Images.
		$recent          = new \stdClass();
		$recent->name    = 'recent_images';
		$recent->title   = __( 'Recent Images', 'nggallery' );
		$recent->aliases = [ 'recent', 'recent_image' ];
		$recent->returns = [ 'image' ];
		$this->register( $recent->name, $recent );

		$this->registered_defaults = true;
	}

	/**
	 * @param string    $name
	 * @param \stdClass $properties
	 * @return void
	 */
	public function register( $name, $properties ) {
		// We'll use an object to represent the source.
		$object = $properties;
		if ( ! is_object( $properties ) ) {
			$object = new \stdClass();
			foreach ( $properties as $k => $v ) {
				$object->$k = $v;
			}
		}

		// Set default properties.
		$object->name = $name;
		if ( ! isset( $object->title ) ) {
			$object->title = $name;
		}
		if ( ! isset( $object->returns ) ) {
			$object->returns = [];
		}
		if ( ! isset( $object->aliases ) ) {
			$object->aliases = [];
		}

		// Add internal reference.
		$this->sources[ $name ] = $object;
		foreach ( $object->aliases as $name ) {
			$this->sources[ $name ] = $object;
		}
	}

	public function register_entity_type() {
		$aliases              = func_get_args();
		$name                 = array_shift( $aliases );
		$this->entity_types[] = $name;
		foreach ( $aliases as $alias ) {
			$this->entity_types[ $alias ] = $name;
		}
	}

	/**
	 * @param string $name
	 */
	public function deregister( $name ) {
		if ( ( $source = $this->get( $name ) ) ) {
			unset( $this->sources[ $name ] );
			foreach ( $source->aliases as $alias ) {
				unset( $this->sources[ $alias ] );
			}
		}
	}

	/**
	 * @param string $name_or_alias
	 * @return \stdClass
	 */
	public function get( $name_or_alias ) {
		if ( ! $this->registered_defaults ) {
			$this->register_defaults();
		}

		if ( isset( $this->sources[ $name_or_alias ] ) ) {
			return $this->sources[ $name_or_alias ];
		}

		// Something has gone wrong. Return a skeleton object to prevent warnings being generated.
		$retval          = new \stdClass();
		$retval->name    = 'unknown';
		$retval->title   = __( 'Unknown source', 'nggallery' );
		$retval->aliases = [];
		$retval->returns = [];

		return $retval;
	}

	/**
	 * @param string $name
	 * @return \stdClass|null
	 */
	public function get_entity_type( $name ) {
		if ( ! $this->registered_defaults ) {
			$this->register_defaults();
		}
		$found = array_search( $name, $this->entity_types );
		if ( $found ) {
			return $this->entity_types[ $found ];
		} else {
			return null;
		}
	}

	/**
	 * @return \stdClass[]
	 */
	public function get_all() {
		if ( ! $this->registered_defaults ) {
			$this->register_defaults();
		}
		$retval = [];
		foreach ( array_values( $this->sources ) as $source_obj ) {
			if ( ! in_array( $source_obj, $retval ) ) {
				$retval[] = $source_obj;
			}
		}
		usort( $retval, [ $this, '_sort_by_name' ] );
		return $retval;
	}

	/**
	 * @param string $a
	 * @param string $b
	 * @return int
	 */
	public function _sort_by_name( $a, $b ) {
		return strcmp( $a->name, $b->name );
	}

	/**
	 * @param string $name
	 * @return bool
	 */
	public function is_registered( $name ) {
		return ! is_null( $this->get( $name ) );
	}

	/**
	 * @param \stdClass   $source
	 * @param DisplayType $display_type
	 * @return bool
	 */
	public function is_compatible( $source, $display_type ) {
		$retval = false;

		if ( ( $source = $this->get( $source->name ) ) && is_object( $display_type ) ) {

			// Get the real entity type names for the display type.
			$display_type_entity_types = [];

			foreach ( $display_type->entity_types as $type ) {
				$result = $this->get_entity_type( $type );
				if ( $result ) {
					$display_type_entity_types[] = $result;
				}
			}

			foreach ( $source->returns as $entity_type ) {
				if ( in_array( $entity_type, $display_type_entity_types, true ) ) {
					$retval = true;
					break;
				}
			}
		}

		return $retval;
	}
}
