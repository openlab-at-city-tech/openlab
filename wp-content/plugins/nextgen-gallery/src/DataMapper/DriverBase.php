<?php

namespace Imagely\NGG\DataMapper;

use Imagely\NGG\Util\Transient;

abstract class DriverBase {

	public $_object_name;
	public $_columns            = [];
	public $_table_columns      = [];
	public $_serialized_columns = [];

	public $primary_key_column = '';

	public $use_cache;
	public $cache = [];

	public $model_class = '';

	public function __construct( $object_name = '' ) {
		$this->_object_name = $object_name;
		$this->lookup_columns();
	}

	abstract public function add_where_clause( $where_clauses, $join );
	abstract public function save_entity( $entity );
	abstract public function select( $fields = null );

	/**
	 * @return string
	 */
	public function get_object_name() {
		return $this->_object_name;
	}

	/**
	 * @global string $table_prefix
	 * @return string
	 */
	public function get_table_name() {
		global $table_prefix;
		global $wpdb;

		$prefix = $table_prefix;

		if ( $wpdb != null && $wpdb->prefix != null ) {
			$prefix = $wpdb->prefix;
		}

		return \apply_filters( 'ngg_datamapper_table_name', $prefix . $this->_object_name, $this->_object_name );
	}

	/**
	 * Looks up using SQL the columns existing in the database, result is cached
	 */
	public function lookup_columns() {
		// Avoid doing multiple SHOW COLUMNS if we can help it.
		$key                  = Transient::create_key( 'col_in_' . $this->get_table_name(), 'columns' );
		$this->_table_columns = Transient::fetch( $key, false );

		if ( ! $this->_table_columns ) {
			$this->update_columns_cache();
		}

		return $this->_table_columns;
	}

	/**
	 * Looks up using SQL the columns existing in the database
	 */
	public function update_columns_cache() {
		global $wpdb;

		$key = Transient::create_key( 'col_in_' . $this->get_table_name(), 'columns' );

		$this->_table_columns = [];

		// $wpdb->prepare() cannot be used just yet as it only supported the %i placeholder for column names as of
		// WordPress 6.2 which is newer than NextGEN's current minimum WordPress version.
		//
		// TODO: Once NextGEN's minimum WP version is 6.2 or higher use wpdb->prepare() here.
		//
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		foreach ( $wpdb->get_results( "SHOW COLUMNS FROM {$this->get_table_name()}" ) as $row ) {
			$this->_table_columns[] = $row->Field;
		}

		Transient::update( $key, $this->_table_columns );
	}

	/**
	 * @param string $column_name
	 * @return bool
	 */
	public function has_column( $column_name ) {
		if ( empty( $this->_table_columns ) ) {
			$this->lookup_columns();
		}
		return array_search( $column_name, $this->_table_columns ) !== false;
	}

	/**
	 * @return string
	 */
	public function get_primary_key_column() {
		return $this->primary_key_column;
	}

	public function cache( $key, $results ) {
		if ( $this->use_cache ) {
			$this->cache[ $key ] = $results;
		}
	}

	public function get_from_cache( $key, $default = null ) {
		if ( $this->use_cache && isset( $this->cache[ $key ] ) ) {
			return $this->cache[ $key ];
		} else {
			return $default;
		}
	}

	public function flush_query_cache() {
		$this->cache = [];
	}

	/**
	 * Used to clean column or table names in a SQL query
	 *
	 * @param string $val
	 * @return string
	 */
	public function _clean_column( $val ) {
		return str_replace( [ ';', "'", '"', '`' ], [ '' ], $val );
	}

	/**
	 * Notes that a particular columns is serialized, and should be deserialized when converted to an entity
	 *
	 * @param $column
	 */
	public function add_serialized_column( $column ) {
		$this->_serialized_columns[] = $column;
	}

	public function deserialize_columns( $object ) {
		foreach ( $this->_serialized_columns as $column ) {
			if ( isset( $object->$column ) && is_string( $object->$column ) ) {
				$object->$column = \Imagely\NGG\Util\Serializable::unserialize( $object->$column );
			}
		}
	}

	/**
	 * @param array       $conditions (optional)
	 * @param object|bool $model (optional)
	 * @return null|object
	 */
	public function find_first( $conditions = [] ) {
		$results = $this->select()->where_and( $conditions )->limit( 1, 0 )->run_query();
		if ( $results ) {
			return $this->convert_to_model( $results[0] );
		} else {
			return null;
		}
	}

	/**
	 * @param array $conditions (optional)
	 * @return array
	 */
	public function find_all( $conditions = [] ) {
		$results = $this->select()->where_and( $conditions )->run_query();
		if ( $results ) {
			foreach ( $results as &$result ) {
				$result = $this->convert_to_model( $result );
			}
		}

		return $results;
	}

	/**
	 * Filters the query:
	 * array("post_title = %s", "Foo")
	 * OR
	 * array(
	 *     array("post_title = %s", "Foo")
	 * )
	 *
	 * @param array $conditions (optional)
	 * @return self
	 */
	public function where_and( $conditions = [] ) {
		return $this->_where( $conditions, 'AND' );
	}

	/**
	 * @param array $conditions (optional)
	 * @return self
	 */
	public function where( $conditions = [] ) {
		return $this->_where( $conditions, 'AND' );
	}

	/**
	 * Parses the where clauses. They could look like the following:
	 * array(
	 *  "post_id = 1"
	 *  array("post_id = %d", 1),
	 * )
	 *
	 * or simply "post_id = 1"
	 *
	 * @param array|string $conditions
	 * @param string       $operator
	 * @return self
	 */
	public function _where( $conditions, $operator ) {
		$where_clauses = [];

		// If conditions is not an array, make it one.
		if ( ! is_array( $conditions ) ) {
			$conditions = [ $conditions ];
		}
		// Just a single condition was passed, but with a bind.
		elseif ( ! empty( $conditions ) && ! is_array( $conditions[0] ) ) {
			$conditions = [ $conditions ];
		}

		// Iterate through each condition.
		foreach ( $conditions as $condition ) {
			if ( is_string( $condition ) ) {
				$clause = $this->_parse_where_clause( $condition );
				if ( $clause ) {
					$where_clauses[] = $clause;
				}
			} else {
				$clause = array_shift( $condition );
				$clause = $this->_parse_where_clause( $clause, $condition );
				if ( $clause ) {
					$where_clauses[] = $clause;
				}
			}
		}

		// Add where clause to query.
		if ( $where_clauses ) {
			$this->add_where_clause( $where_clauses, $operator );
		}

		return $this;
	}

	/**
	 * Parses a where clause and returns an associative array
	 * representing the query
	 *
	 * E.g. parse_where_clause("post_title = %s", "Foo Bar")
	 *
	 * @global wpdb $wpdb
	 * @param string $condition
	 * @return array
	 */
	public function _parse_where_clause( $condition ) {
		$column   = '';
		$operator = '';
		$value    = '';
		$numeric  = true;

		// Substitute any placeholders.
		global $wpdb;
		$binds = func_get_args();
		$binds = isset( $binds[1] ) ? $binds[1] : []; // first argument is the condition.
		foreach ( $binds as &$bind ) {

			// A bind could be an array, used for the 'IN' operator
			// or a simple scalar value. We need to convert arrays
			// into scalar values.
			if ( is_object( $bind ) ) {
				$bind = (array) $bind;
			}

			if ( is_array( $bind ) && ! empty( $bind ) ) {
				foreach ( $bind as &$val ) {
					if ( ! is_numeric( $val ) ) {
						$val     = '"' . addslashes( $val ) . '"';
						$numeric = false;
					}
				}
				$bind = implode( ',', $bind );
			} elseif ( is_array( $bind ) && empty( $bind ) ) {
				$bind = 'NULL';
			} elseif ( ! is_numeric( $bind ) ) {
				$numeric = false;
			}
		}

		if ( $binds ) {
			// PHP-CS triggers a false positive on this; $condition is a string that contains the placeholders.
			//
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$condition = $wpdb->prepare( $condition, $binds );
		}

		// Parse the where clause.
		if ( preg_match( '/^[^\s]+/', $condition, $match ) ) {
			$column    = trim( array_shift( $match ) );
			$condition = str_replace( $column, '', $condition );
		}

		if ( preg_match( '/(NOT )?IN|(NOT )?LIKE|(NOT )?BETWEEN|[=!<>]+/i', $condition, $match ) ) {
			$operator  = trim( array_shift( $match ) );
			$condition = str_replace( $operator, '', $condition );
			$operator  = strtolower( $operator );
			$value     = trim( $condition );
		}

		// Values will automatically be quoted, so remove them
		// If the value is part of an IN clause or BETWEEN clause and
		// has multiple values, we attempt to split the values apart into an
		// array and iterate over them individually.
		if ( $operator == 'in' ) {
			$values = preg_split( "/'?\s?(,)\s?'?/i", $value );
		} elseif ( $operator == 'between' ) {
			$values = preg_split( "/'?\s?(AND)\s?'?/i", $value );
		}

		// If there's a single value, treat it as an array so that we can still iterate.
		if ( empty( $values ) ) {
			$values = [ $value ];
		}

		foreach ( $values as $index => $value ) {
			$value            = preg_replace( "/^(\()?'/", '', $value );
			$value            = preg_replace( "/'(\))?$/", '', $value );
			$values[ $index ] = $value;
		}

		if ( count( $values ) > 1 ) {
			$value = $values;
		}

		// Return the WP Query meta query parameters.
		$retval = [
			'column'  => $column,
			'value'   => $value,
			'compare' => strtoupper( $operator ),
			'type'    => $numeric ? 'numeric' : 'string',
		];

		return $retval;
	}

	public function strip_slashes( $stdObject_or_array_or_string ) {
		/**
		 * Some objects have properties that are recursive objects. To avoid this we have to keep track of what objects
		 * we've already processed when we're running this method recursively.
		 */
		static $level             = 0;
		static $processed_objects = [];

		++$level;
		$processed_objects[] = $stdObject_or_array_or_string;

		if ( is_string( $stdObject_or_array_or_string ) ) {
			$stdObject_or_array_or_string = str_replace( "\\'", "'", str_replace( '\"', '"', str_replace( '\\\\', '\\', $stdObject_or_array_or_string ) ) );
		} elseif ( is_object( $stdObject_or_array_or_string ) && ! in_array( $stdObject_or_array_or_string, $processed_objects ) ) {
			foreach ( get_object_vars( $stdObject_or_array_or_string ) as $key => $val ) {
				if ( $val != $stdObject_or_array_or_string && $key != '_mapper' ) {
					$stdObject_or_array_or_string->$key = $this->strip_slashes( $val );
				}
			}
			$processed_objects[] = $stdObject_or_array_or_string;
		} elseif ( is_array( $stdObject_or_array_or_string ) ) {
			foreach ( $stdObject_or_array_or_string as $key => $val ) {
				if ( $key != '_mixins' ) {
					$stdObject_or_array_or_string[ $key ] = $this->strip_slashes( $val );
				}
			}
		}

		--$level;
		if ( $level == 0 ) {
			$processed_objects = [];
		}

		return $stdObject_or_array_or_string;
	}

	/**
	 * Converts a stdObject entity to a model
	 *
	 * @param object      $stdObject
	 * @param string|bool $context (optional)
	 * @return object
	 */
	public function convert_to_model( $stdObject, $context = false ) {
		try {
			$this->_convert_to_entity( $stdObject );
		} catch ( \Exception $ex ) {
			throw new \E_InvalidEntityException( $ex );
		}

		return $this->create( $stdObject );
	}

	/**
	 * Determines whether an object is actually a model
	 *
	 * @param mixed $obj
	 * @return bool
	 */
	public function is_model( $obj ) {
		return is_subclass_of( $obj, '\Imagely\NGG\DataMapper\Model' );
	}

	/**
	 * If a field has no value, then use the default value.
	 *
	 * @param \stdClass|Model $object
	 */
	public function set_default_value( $object ) {
		$array         = null;
		$field         = null;
		$default_value = null;

		// The first argument MUST be an object.
		if ( ! is_object( $object ) ) {
			throw new \E_InvalidEntityException();
		}

		// This method has two signatures:
		// 1) _set_default_value($object, $field, $default_value)
		// 2) _set_default_value($object, $array_field, $field, $default_value).

		// Handle #1.
		$args = func_get_args();
		if ( count( $args ) == 4 ) {
			list($object, $array, $field, $default_value) = $args;
			if ( ! isset( $object->{$array} ) ) {
				$object->{$array}           = [];
				$object->{$array}[ $field ] = null;
			} else {
				$arr = &$object->{$array};
				if ( ! isset( $arr[ $field ] ) ) {
					$arr[ $field ] = null;
				}
			}
			$array = &$object->{$array};
			$value = &$array[ $field ];
			if ( $value === '' or is_null( $value ) ) {
				$value = $default_value;
			}
		}

		// Handle #2.
		else {
			list($object, $field, $default_value) = $args;
			if ( ! isset( $object->$field ) ) {
				$object->$field = null;
			}
			$value = $object->$field;
			if ( $value === '' or is_null( $value ) ) {
				$object->$field = $default_value;
			}
		}
	}

	public function has_defined_column( $name ) {
		$columns = $this->_columns;
		return isset( $columns[ $name ] );
	}

	public function cast_columns( $entity ) {
		foreach ( $this->_columns as $key => $properties ) {
			$value         = property_exists( $entity, $key ) ? $entity->$key : null;
			$default_value = $properties['default_value'];
			if ( ! is_null( $value ) && $value !== $default_value ) {
				$column_type = $this->_columns[ $key ]['type'];
				if ( preg_match( '/varchar|text/i', $column_type ) ) {
					if ( ! is_array( $value ) && ! is_object( $value ) ) {
						$entity->$key = strval( $value );
					}
				} elseif ( preg_match( '/decimal|numeric|double|float/i', $column_type ) ) {
					$entity->$key = floatval( $value );
				} elseif ( preg_match( '/int/i', $column_type ) ) {
					$entity->$key = intval( $value );
				} elseif ( preg_match( '/bool/i', $column_type ) ) {
					$entity->$key = ( $value ? true : false );
				}
			} else {
				// Add property and default value.
				$entity->$key = $default_value;
			}
		}
		return $entity;
	}

	/**
	 * Finds a particular entry by id
	 *
	 * @param int|\stdClass|Model $entity
	 * @return null|object|Model
	 */
	public function find( $entity ) {
		$retval = null;

		// Get primary key of the entity.
		$pkey = $this->get_primary_key_column();
		if ( ! is_numeric( $entity ) ) {
			$entity = isset( $entity->$pkey ) ? intval( $entity->$pkey ) : false;
		}

		// If we have an entity ID, then get the record.
		if ( $entity ) {
			$results = $this->select()->where_and( [ "{$pkey} = %d", $entity ] )->limit( 1, 0 )->run_query();

			if ( $results ) {
				$retval = $this->convert_to_model( $results[0] );
			}
		}

		return $retval;
	}

	/**
	 * Converts a stdObject to an Entity
	 *
	 * @param \stdClass $entity
	 * @return object
	 */
	public function _convert_to_entity( $entity ) {
		// Add extra columns to entity.
		if ( isset( $entity->extras ) ) {
			$extras = $entity->extras;
			unset( $entity->extras );
			foreach ( explode( ',', $extras ) as $extra ) {
				if ( $extra ) {
					list($key, $value) = explode( '@@', $extra );
					if ( $this->has_defined_column( $key ) && ! isset( $entity->key ) && $key !== 'extras_post_id' ) {
						$entity->$key = $value;
					}
				}
			}
		}

		// Cast custom_post_id as integer.
		if ( isset( $entity->extras_post_id ) ) {
			$entity->extras_post_id = intval( $entity->extras_post_id );
		} else {
			$entity->extras_post_id = 0;
		}

		// Add name of the id_field to the entity, and convert the ID to an integer.
		$entity->id_field = $this->get_primary_key_column();

		// Cast columns to their appropriate data type.
		$this->cast_columns( $entity );

		// Strip slashes.
		$this->strip_slashes( $entity );

		// Deserialize columns.
		$this->deserialize_columns( $entity );

		return $entity;
	}

	/**
	 * Creates a new model
	 *
	 * @param array $properties (optional)
	 * @return object
	 */
	public function create( $properties = [] ) {
		$entity = new \stdClass();
		foreach ( $properties as $key => $value ) {
			$entity->$key = $value;
		}

		return new $this->model_class( $entity );
	}

	/**
	 * Saves an entity
	 *
	 * @param \stdClass|Model $entity
	 * @return bool|int Resulting ID or false upon failure
	 */
	public function save( $entity ) {
		$retval = false;
		$model  = $entity;

		$this->flush_query_cache();

		if ( is_array( $entity ) ) {
			throw new \E_InvalidEntityException();
		} elseif ( ! $this->is_model( $entity ) ) {
			// We can work with what we have. But we need to ensure that we've got a model.
			$model = $this->convert_to_model( $entity );
		}

		if ( ! is_array( $model->validation() ) ) {
			$retval = $this->save_entity( $model );
		}

		$this->flush_query_cache();

		return $retval;
	}

	/**
	 * Gets validation errors for the entity
	 *
	 * @param \stdClass|Model $entity
	 * @return array
	 */
	public function get_errors( $entity ) {
		$model = $entity;
		if ( ! $this->is_model( $entity ) ) {
			$model = $this->convert_to_model( $entity );
		}

		return $model->validation();
	}

	/**
	 * @param object $entity
	 */
	public function set_defaults( $entity ) {}

	/**
	 * @param string        $name
	 * @param string        $type
	 * @param string|number $default_value
	 * @param bool          $extra
	 * @return void
	 */
	public function define_column( $name, $type, $default_value = null, $extra = false ) {
		$this->_columns[ $name ] = [
			'type'          => $type,
			'default_value' => $default_value,
			'extra'         => $extra,
		];
	}
}
