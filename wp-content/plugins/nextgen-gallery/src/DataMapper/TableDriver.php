<?php

namespace Imagely\NGG\DataMapper;

use Imagely\NGG\Util\Serializable;

class TableDriver extends DriverBase {

	public $where_clauses    = [];
	public $order_clauses    = [];
	public $group_by_columns = [];
	public $limit_clause     = '';
	public $select_clause    = '';
	public $delete_clause    = '';
	public $use_cache        = true;
	public $debug            = false;

	public $_custom_post_mapper;

	// Necessary for backwards compatibility.
	public $custom_post_name = __CLASS__;

	public function __construct( $object_name = '' ) {
		parent::__construct( $object_name );

		try {
			if ( ! isset( $this->primary_key_column ) ) {
				$this->primary_key_column = $this->_lookup_primary_key_column();
			}

			$this->migrate();
		} catch ( \Exception $exception ) {
		}

		// Each record in a NextGEN Gallery table has an associated custom post in the wp_posts table.
		$this->_custom_post_mapper              = new WPPostDriver( $this->get_object_name() );
		$this->_custom_post_mapper->model_class = 'Imagely\NGG\DataTypes\DataMapperExtraFields';
	}

	/**
	 * Returns the database connection object for WordPress
	 *
	 * @global \wpdb $wpdb
	 * @return \wpdb
	 */
	public function _wpdb() {
		global $wpdb;
		return $wpdb;
	}

	/**
	 * Looks up the primary key column for this table
	 *
	 * @throws \Exception
	 */
	public function _lookup_primary_key_column() {
		$key = $this->_wpdb()->get_row( "SHOW INDEX FROM {$this->get_table_name()} WHERE Key_name='PRIMARY'", ARRAY_A );
		if ( ! $key ) {
			throw new \Exception( "Please specify the primary key for {$this->get_table_name ()}" );
		}
		return $key['Column_name'];
	}

	/**
	 * Gets the name of the primary key column
	 *
	 * @return string
	 */
	public function get_primary_key_column() {
		return $this->primary_key_column;
	}

	/**
	 * Determines whether we're going to execute a SELECT statement
	 *
	 * @return boolean
	 */
	public function is_select_statement() {
		return (bool) $this->select_clause;
	}

	/**
	 * Determines if we're going to be executing a DELETE statement
	 *
	 * @return bool
	 */
	public function is_delete_statement() {
		return (bool) $this->delete_clause;
	}

	/**
	 * Orders the results of the query
	 * This method may be used multiple of times to order by more than column
	 *
	 * @param $order_by
	 * @param $direction
	 * @return self
	 */
	public function order_by( $order_by, $direction = 'ASC' ) {
		// We treat the rand() function as an exception.
		if ( preg_match( '/rand\(\s*\)/', $order_by ) ) {
			$order = 'rand()';
		} else {
			$order_by = $this->_clean_column( $order_by );

			// If the order by clause is a column, then it should be backticked.
			if ( $this->has_column( $order_by ) ) {
				$order_by = "`{$order_by}`";
			}

			$direction = $this->_clean_column( $direction );
			$order     = "{$order_by} {$direction}";
		}

		$this->order_clauses[] = $order;

		return $this;
	}

	/**
	 * Specifies a limit and optional offset
	 *
	 * @param integer $max
	 * @param integer $offset
	 * @return self
	 */
	public function limit( $max, $offset = 0 ) {
		if ( $offset ) {
			$limit = $this->_wpdb()->prepare( 'LIMIT %d, %d', max( 0, $offset ), $max );
		} else {
			$limit = $this->_wpdb()->prepare( 'LIMIT %d', max( 0, $max ) );
		}

		/***
		 * Set $limit to false when we want to display all records, that is $items_per_page = all.
		 * LIMIT 0 results in no entries found error. So we remove limit_clause altogether.
		 */

		if ( (int) $max < 0 ) {
			$limit              = false;
			$this->limit_clause = false;
		}

		if ( $limit ) {
			$this->limit_clause = $limit;
		}

		return $this;
	}

	/**
	 * Specifics a group by clause for one or more columns
	 *
	 * @param array|string $columns
	 * @return self
	 */
	public function group_by( $columns = [] ) {
		if ( ! is_array( $columns ) ) {
			$columns = [ $columns ];
		}
		$this->group_by_columns = array_merge( $this->group_by_columns, $columns );
		return $this;
	}

	/**
	 * Adds a where clause to the driver
	 *
	 * @param array  $where_clauses
	 * @param string $join
	 */
	public function add_where_clause( $where_clauses, $join ) {
		$clauses = [];

		foreach ( $where_clauses as $clause ) {
			extract( $clause );
			if ( $this->has_column( $column ) ) {
				$column = "`{$column}`";
			}
			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

			foreach ( $value as $index => $v ) {
				$v               = $clause['type'] == 'numeric' ? $v : "'{$v}'";
				$value[ $index ] = $v;
			}

			if ( $compare == 'BETWEEN' ) {
				$value = "{$value[0]} AND {$value[1]}";
			} else {
				$value = implode( ', ', $value );
				if ( strpos( $compare, 'IN' ) !== false ) {
					$value = "({$value})";
				}
			}

			$clauses[] = "{$column} {$compare} {$value}";
		}

		$this->where_clauses[] = implode( " {$join} ", $clauses );
	}

	/**
	 * Returns the total number of entities known
	 *
	 * @return int
	 */
	public function count() {
		$retval = 0;

		$key = $this->get_primary_key_column();

		/** @noinspection SqlResolve */
		$results = $this->run_query( "SELECT COUNT(`{$key}`) AS `{$key}` FROM `{$this->get_table_name()}`" );

		if ( $results && isset( $results[0]->$key ) ) {
			$retval = (int) $results[0]->$key;
		}

		return $retval;
	}

	/**
	 * Run the query
	 *
	 * @param string|bool $sql (optional) run the specified SQL
	 * @param object|bool $model (optional)
	 * @param bool        $no_entities (optional)
	 * @return array
	 */
	public function run_query( $sql = false, $model = false, $no_entities = false ) {
		$results = false;
		$retval  = [];

		// Or generate SQL query.
		if ( ! $sql ) {
			$sql = $this->get_generated_query( $no_entities );
		}

		// If we have a SQL statement to execute, then heck, execute it!.
		if ( $sql ) {
			if ( $this->debug ) {
				var_dump( $sql );
			}

			// Try getting the result from cache first.
			if ( $this->is_select_statement() && $this->use_cache ) {
				$results = $this->get_from_cache( $sql );
			}
		}

		if ( ! $results ) {
			$this->_wpdb()->query( $sql );
			$results = $this->_wpdb()->last_result;
			if ( $this->is_select_statement() ) {
				$this->cache( $sql, $results );
			}
		}

		if ( $results ) {
			$retval = [];
			// For each row, create an entity, update its properties, and add it to the result set.
			if ( $no_entities ) {
				$retval = $results;
			} else {
				$id_field = $this->get_primary_key_column();
				foreach ( $results as $row ) {
					if ( $row ) {
						if ( isset( $row->$id_field ) ) {
							if ( $model ) {
								$retval[] = $this->convert_to_model( $row );
							} else {
								$retval[] = $this->_convert_to_entity( $row );
							}
						}
					}
				}
			}
		} elseif ( $this->debug ) {
			var_dump( 'No entities returned from query' );
		}

		// Just a safety check.
		if ( ! $retval ) {
			$retval = [];
		}

		return $retval;
	}

	/**
	 * Converts an entity to something suitable for inserting into a database column
	 *
	 * @param object $entity
	 * @return array
	 */
	public function _convert_to_table_data( $entity ) {
		$data = (array) $entity;
		foreach ( $data as $key => $value ) {
			if ( ! isset( $this->_columns[ $key ] ) || $this->_columns[ $key ]['extra'] ) {
				unset( $data[ $key ] );
				continue;
			}
			if ( is_array( $value ) ) {
				$data[ $key ] = Serializable::serialize( $value );
			}
		}

		return $data;
	}

	public function get_column_names() {
		return array_keys( $this->_columns );
	}

	/**
	 * Migrates the schema of the database
	 *
	 * @throws \Exception
	 */
	public function migrate() {
		if ( ! $this->_columns ) {
			throw new \Exception( "Columns not defined for {$this->get_table_name()}" );
		}

		$added   = false;
		$removed = false;

		// Add any missing columns.
		foreach ( $this->_columns as $key => $properties ) {
			if ( ! in_array( $key, $this->_table_columns ) ) {
				if ( $this->_add_column( $key, $properties['type'], $properties['default_value'] ) ) {
					$added = true;
				}
			}
		}

		if ( $added or $removed ) {
			$this->lookup_columns();
		}
	}

	public function _init() {
		$this->where_clauses    = [];
		$this->order_clauses    = [];
		$this->group_by_columns = [];
		$this->limit_clause     = '';
		$this->select_clause    = '';
	}

	/**
	 * Selects which fields to collect from the table.
	 * NOTE: Not protected from SQL injection - DO NOT let your users specify DB columns
	 *
	 * @param string $fields
	 * @return self
	 */
	public function select( $fields = null ) {
		// Create a fresh slate.
		$this->_init();
		if ( ! $fields or $fields == '*' ) {
			$fields = $this->get_table_name() . '.*';
		}
		$this->select_clause = "SELECT {$fields}";

		return $this;
	}
	/**
	 * Start a delete statement
	 */
	public function delete() {
		// Create a fresh slate.
		$this->_init();
		$this->delete_clause = 'DELETE';
		return $this;
	}

	/**
	 * Stores the entity
	 *
	 * @param object $entity
	 * @return bool|self
	 */
	public function save_entity( $entity ) {
		$retval = false;

		unset( $entity->id_field );
		$primary_key = $this->get_primary_key_column();
		if ( isset( $entity->$primary_key ) && $entity->$primary_key > 0 ) {
			if ( $this->_update( $entity ) ) {
				$retval = intval( $entity->$primary_key );
			}
		} else {
			$retval = $this->_create( $entity );
			if ( $retval ) {
				$new_entity = $this->find( $retval );
				foreach ( $new_entity as $key => $value ) {
					$entity->$key = $value;
				}
			}
		}

		$entity->id_field = $primary_key;

		// Clean cache.
		if ( $retval ) {
			$this->cache = [];
		}

		return $retval;
	}

	/**
	 * Destroys/deletes an entity
	 *
	 * @param object|Model|int $entity
	 * @return boolean
	 */
	public function destroy( $entity ) {
		$retval = false;
		$key    = $this->get_primary_key_column();

		if ( isset( $entity->extras_post_id ) ) {
			\wp_delete_post( $entity->extras_post_id, true );
		}

		// Find the id of the entity.
		if ( is_object( $entity ) && isset( $entity->$key ) ) {
			$id = (int) $entity->$key;
		} else {
			$id = (int) $entity;
		}

		// If we have an ID, then delete the post.
		if ( is_numeric( $id ) ) {
			$sql    = $this->_wpdb()->prepare(
				"DELETE FROM `{$this->get_table_name()}` WHERE {$key} = %s",
				$id
			);
			$retval = $this->_wpdb()->query( $sql );
		}

		return $retval;
	}

	/**
	 * @param object $entity
	 * @return boolean
	 */
	public function _create( $entity ) {

		$retval             = false;
		$custom_post_entity = $this->create_custom_post_entity( $entity );

		// Try persisting the custom post type record first.
		if ( ( $custom_post_id = $this->_custom_post_mapper->save( $custom_post_entity ) ) ) {
			$entity->extras_post_id = $custom_post_id;
		}

		$table_data = $this->_convert_to_table_data( $entity );

		$id = $this->_wpdb()->insert( $this->get_table_name(), $table_data );

		if ( $id ) {
			$key    = $this->get_primary_key_column();
			$retval = $entity->$key = intval( $this->_wpdb()->insert_id );
		}

		// Remove the custom post if saving the normal table entry failed.
		if ( ! $retval && isset( $custom_post_id ) ) {
			$this->_custom_post_mapper->destroy( $custom_post_id );
		}

		return $retval;
	}

	/**
	 * Updates a record in the database
	 *
	 * @param object $entity
	 * @return int|bool
	 */
	public function _update( $entity ) {
		$key = $this->get_primary_key_column();

		$custom_post_entity = $this->create_custom_post_entity( $entity );
		$custom_post_id     = $this->_custom_post_mapper->save( $custom_post_entity );

		$entity->extras_post_id = $custom_post_id;

		$table_data = $this->_convert_to_table_data( $entity );

		$retval = $this->_wpdb()->update(
			$this->get_table_name(),
			$table_data,
			[
				$key => $entity->$key,
			]
		);

		foreach ( $this->get_extra_columns() as $key ) {
			if ( isset( $custom_post_entity->$key ) ) {
				$entity->$key = $custom_post_entity->$key;
			}
		}

		return $retval;
	}

	/**
	 * @param string        $column_name
	 * @param string        $datatype
	 * @param string|number $default_value
	 * @return bool
	 */
	public function _add_column( $column_name, $datatype, $default_value = null ) {
		if ( isset( $this->_columns[ $column_name ] ) && $this->_columns[ $column_name ]['extra'] ) {
			return false;
		}

		/** @noinspection SqlResolve */
		$sql = "ALTER TABLE `{$this->get_table_name()}` ADD COLUMN `{$column_name}` {$datatype}";
		if ( $default_value ) {
			if ( is_string( $default_value ) ) {
				$default_value = str_replace( "'", "\\'", $default_value );
			}
			$sql .= ' NOT NULL DEFAULT ' . ( is_string( $default_value ) ? "'{$default_value}" : "{$default_value}" );
		}

		$return = (bool) $this->_wpdb()->query( $sql );
		$this->update_columns_cache();
		return $return;
	}

	/**
	 * @param bool $no_entities Default: false
	 * @return array|string|string[]
	 */
	public function get_generated_query( $no_entities = false ) {
		// Add extras column.
		if ( $this->is_select_statement() && stripos( $this->select_clause, 'count(' ) === false ) {
			$table_name  = $this->get_table_name();
			$primary_key = "{$table_name}.{$this->get_primary_key_column()}";

			if ( stripos( $this->select_clause, 'DISTINCT' ) === false ) {
				$this->select_clause = str_replace( 'SELECT', 'SELECT DISTINCT', $this->select_clause );
			}

			$this->group_by( $primary_key );

			$sql = $this->get_actual_generated_query( $no_entities );

			// Sections may be omitted by wrapping them in mysql's C style comments.
			if ( stripos( $sql, '/*NGG_NO_EXTRAS_TABLE*/' ) !== false ) {
				$parts = explode( '/*NGG_NO_EXTRAS_TABLE*/', $sql );
				foreach ( $parts as $ndx => $row ) {
					if ( $ndx % 2 != 0 ) {
						continue;
					}
					$parts[ $ndx ] = $this->_regex_replace( $row );
				}
				$sql = implode( '', $parts );
			} else {
				$sql = $this->_regex_replace( $sql );
			}
		} else {
			$sql = $this->get_actual_generated_query( $no_entities );
		}

		return $sql;
	}

	/**
	 * @param bool $no_entities Default = false
	 * @return string
	 */
	public function get_actual_generated_query( $no_entities = false ) {
		$sql = [];

		if ( $this->is_select_statement() ) {
			$sql[] = $this->select_clause;
		} elseif ( $this->is_delete_statement() ) {
			$sql[] = $this->delete_clause;
		}

		$sql[]         = 'FROM `' . $this->get_table_name() . '`';
		$where_clauses = [];

		foreach ( $this->where_clauses as $where ) {
			$where_clauses[] = '(' . $where . ')';
		}

		if ( $where_clauses ) {
			$sql[] = 'WHERE ' . implode( ' AND ', $where_clauses );
		}

		if ( $this->is_select_statement() ) {
			if ( $this->group_by_columns ) {
				$sql[] = 'GROUP BY ' . implode( ', ', $this->group_by_columns );
			}
			if ( $this->order_clauses ) {
				$sql[] = 'ORDER BY ' . implode( ', ', $this->order_clauses );
			}
			if ( $this->limit_clause ) {
				$sql[] = $this->limit_clause;
			}
		}
		return implode( ' ', $sql );
	}

	/**
	 * @return array
	 */
	public function get_extra_columns() {
		$retval = [];

		foreach ( $this->_columns as $key => $properties ) {
			if ( $properties['extra'] ) {
				$retval[] = $key;
			}
		}

		return $retval;
	}

	public function create_custom_post_entity( $entity ) {
		$custom_post_entity = new \stdClass();

		// If the custom post entity already exists then it needs an ID.
		if ( isset( $entity->extras_post_id ) ) {
			$custom_post_entity->ID = $entity->extras_post_id;
		}

		// If a property isn't a column for the table, then it belongs to the custom post record.
		foreach ( get_object_vars( $entity ) as $key => $value ) {
			if ( ! $this->has_column( $key ) ) {
				unset( $entity->$key );
				if ( $this->has_defined_column( $key ) && $key != $this->get_primary_key_column() ) {
					$custom_post_entity->$key = $value;
				}
			}
		}

		// Used to help find these type of records.
		$custom_post_entity->post_name = $this->custom_post_name;

		return $custom_post_entity;
	}

	public function _regex_replace( $in ) {
		global $wpdb;
		$from = 'FROM `' . $this->get_table_name() . '`';
		$out  = str_replace( 'FROM', ", GROUP_CONCAT(CONCAT_WS('@@', meta_key, meta_value)) AS 'extras' FROM", $in );
		$out  = str_replace( $from, "{$from} LEFT OUTER JOIN `{$wpdb->postmeta}` ON `{$wpdb->postmeta}`.`post_id` = `extras_post_id` ", $out );
		return $out;
	}
}
