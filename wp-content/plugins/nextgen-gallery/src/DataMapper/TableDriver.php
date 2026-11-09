<?php

namespace Imagely\NGG\DataMapper;

use Imagely\NGG\Util\Serializable;

/**
 * Table driver for data mapper.
 */
class TableDriver extends DriverBase {

	/**
	 * Where clauses array.
	 *
	 * @var array
	 */
	public $where_clauses = [];

	/**
	 * Order clauses array.
	 *
	 * @var array
	 */
	public $order_clauses = [];

	/**
	 * Group by columns array.
	 *
	 * @var array
	 */
	public $group_by_columns = [];

	/**
	 * Limit clause.
	 *
	 * @var string
	 */
	public $limit_clause = '';

	/**
	 * Select clause.
	 *
	 * @var string
	 */
	public $select_clause = '';

	/**
	 * Delete clause.
	 *
	 * @var string
	 */
	public $delete_clause = '';

	/**
	 * Whether to use cache.
	 *
	 * @var bool
	 */
	public $use_cache = true;

	/**
	 * Debug flag.
	 *
	 * @var bool
	 */
	public $debug = false;

	/**
	 * Custom post mapper instance.
	 *
	 * @var object|null
	 */
	public $_custom_post_mapper;

	// Necessary for backwards compatibility.
	/**
	 * Custom post name.
	 *
	 * @var string
	 */
	public $custom_post_name = __CLASS__;

	public function __construct( $object_name = '' ) {
		parent::__construct( $object_name );

		try {
			if ( ! isset( $this->primary_key_column ) ) {
				$this->primary_key_column = $this->_lookup_primary_key_column();
			}

			$this->migrate();
		} catch ( \Exception $exception ) {
			// Exception is silently caught here as the table may not exist yet during initial setup.
			unset( $exception );
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
	 * @throws \Exception When primary key cannot be found
	 */
	public function _lookup_primary_key_column() {
		$key = $this->_wpdb()->get_row( "SHOW INDEX FROM {$this->get_table_name()} WHERE Key_name='PRIMARY'", ARRAY_A );
		if ( ! $key ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Table name is internal and safe
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
	 * @param string $order_by
	 * @param string $direction
	 * @return self
	 */
	public function order_by( $order_by, $direction = 'ASC' ) {
		// We treat the rand() function as an exception.
		if ( preg_match( '/rand\(\s*\)/', $order_by ) ) {
			$order = 'rand()';
		} elseif ( preg_match( "/^FIELD\\(\\s*[`']?([A-Za-z_][A-Za-z0-9_]*)[`']?\\s*,\\s*([0-9,\\s]*)\\)$/", (string) $order_by, $m ) ) {
			// Security fix (SQLi): allow the legacy FIELD(<ident>, <int-list>) form used for preserving IN() result order. Both identifier and integer list are strictly validated so no user-controlled string can reach SQL here.
			$ident = $m[1];
			$ints  = array_filter( array_map( 'intval', preg_split( '/\s*,\s*/', trim( $m[2] ) ) ) );
			if ( empty( $ints ) ) {
				// No ids to order by; skip the clause to avoid an empty FIELD().
				return $this;
			}
			$int_csv = implode( ',', $ints );
			$order   = "FIELD(`{$ident}`, {$int_csv})";
		} else {
			// Security fix (SQLi): identifier whitelist via _clean_column leaves only [A-Za-z0-9_], safe to inline in ORDER BY without requiring has_column() (aliased SELECT columns like 'new_sortorder'/'ordered_by' are legitimate but not in _table_columns).
			$order_by = $this->_clean_column( $order_by );
			if ( '' === $order_by ) {
				// Empty after sanitization — refuse to build clause to avoid SQL error and potential open-ORDER-BY.
				return $this;
			}
			if ( $this->has_column( $order_by ) ) {
				$order_by = "`{$order_by}`";
			}

			// Security fix (SQLi): strict whitelist for direction; default to ASC if not exactly ASC/DESC.
			$direction = strtoupper( $this->_clean_column( $direction ) );
			if ( 'ASC' !== $direction && 'DESC' !== $direction ) {
				$direction = 'ASC';
			}
			$order = "{$order_by} {$direction}";
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

		// Security fix (SQLi defense-in-depth): allowlist of comparators. Anything outside this list is
		// coerced to '=' so caller-supplied $compare cannot inject query-shape-changing fragments.
		$allowed_compares = [ '=', '!=', '<>', '<', '<=', '>', '>=', 'IN', 'NOT IN', 'BETWEEN', 'LIKE', 'NOT LIKE', 'IS', 'IS NOT' ];

		foreach ( $where_clauses as $clause ) {
			// Replaced extract() with explicit reads: extract() on a structured array is a foot-gun and would clobber locals if upstream schema gains new keys.
			$column  = isset( $clause['column'] ) ? $clause['column'] : '';
			$value   = isset( $clause['value'] ) ? $clause['value'] : '';
			$compare = isset( $clause['compare'] ) ? $clause['compare'] : '=';
			$type    = isset( $clause['type'] ) ? $clause['type'] : 'string';

			// Security fix (SQLi defense-in-depth): identifier hardening for $column. Schema-defined
			// columns are backticked as before; everything else is forced through the same identifier
			// allowlist used by order_by() ([A-Za-z0-9_]). Empty result drops the clause rather than
			// concatenating raw caller input into SQL.
			if ( $this->has_column( $column ) ) {
				$column = "`{$column}`";
			} else {
				$column = $this->_clean_column( (string) $column );
				if ( '' === $column ) {
					continue;
				}
				$column = "`{$column}`";
			}

			// Security fix (SQLi defense-in-depth): strict comparator allowlist (was: any string passed
			// through). Normalize to upper-case + trim before lookup so 'in' / ' IN ' still match.
			$compare = strtoupper( trim( (string) $compare ) );
			if ( ! in_array( $compare, $allowed_compares, true ) ) {
				$compare = '=';
			}

			if ( ! is_array( $value ) ) {
				$value = [ $value ];
			}

			// A value flagged 'prepared' still carries the escaping _parse_where_clause() applied
			// with $wpdb->prepare(). Strip that pass before esc_sql() below so the value is
			// escaped exactly once: otherwise a term holding a quote, a backslash or an
			// esc_like() sequence is compared against a double-escaped copy and matches nothing.
			$prepared = ! empty( $clause['prepared'] );

			foreach ( $value as $index => $v ) {
				// esc_sql + single-quote wrap for string values; raw cast for numeric. Prevents SQLi when callers pass user-influenced strings.
				if ( 'numeric' === $type ) {
					$v = (float) $v;
				} else {
					$v = "'" . esc_sql( $prepared ? stripslashes( $v ) : $v ) . "'";
				}
				$value[ $index ] = $v;
			}

			if ( 'BETWEEN' === $compare ) {
				$value = "{$value[0]} AND {$value[1]}";
			} else {
				$value = implode( ', ', $value );
				// Exact match on the comparator string (not substring) so values like 'INFO' / 'INSERT'
				// never sneak through into IN-style parenthesization. After normalization above the
				// only IN-family entries are 'IN' and 'NOT IN'.
				if ( 'IN' === $compare || 'NOT IN' === $compare ) {
					$value = "({$value})";
				}
			}

			$clauses[] = "{$column} {$compare} {$value}";
		}

		$this->where_clauses[] = implode( " {$join} ", $clauses );
	}

	/**
	 * {@inheritdoc}
	 */
	public function where_raw( $sql ) {
		$this->where_clauses[] = $sql;
		return $this;
	}

	/**
	 * Returns the total number of entities known
	 *
	 * @return int
	 */
	public function count() {
		$retval = 0;

		$key = $this->get_primary_key_column();

		/**
		 * SQL query results.
		 *
		 * @noinspection SqlResolve
		 */
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
				// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- Debug code intentionally commented out.
				// var_dump( $sql );
				null; // Intentionally empty - debug code commented out.
			}

			// Try getting the result from cache first.
			if ( $this->is_select_statement() && $this->use_cache ) {
				$results = $this->get_from_cache( $sql );
			}
		}

		if ( ! $results ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
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
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- Debug code intentionally commented out.
			// var_dump( 'No entities returned from query' );
			null; // Intentionally empty - debug code commented out.
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
	 * @throws \Exception When columns are not defined
	 */
	public function migrate() {
		if ( ! $this->_columns ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped -- Table name is internal and safe
			throw new \Exception( "Columns not defined for {$this->get_table_name()}" );
		}

		$added   = false;
		$removed = false;

		// Add any missing columns.
		foreach ( $this->_columns as $key => $properties ) {
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ! in_array( $key, $this->_table_columns ) ) {
				if ( $this->_add_column( $key, $properties['type'], $properties['default_value'] ) ) {
					$added = true;
				}
			}
		}

		if ( $added || $removed ) {
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
		if ( ! $fields || $fields == '*' ) {
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
			// Not a truthiness test. _update() returns wpdb::update()'s value, which is the number
			// of affected rows, and that is 0 whenever the row already held the submitted values.
			// A successful no-op update was therefore reported as a failed save, and every caller
			// that tests save() for truthiness had to compensate. Two of them did it by reading
			// $wpdb->last_error afterwards, which is unsound: wpdb::update() has `return false`
			// paths that run before any query is issued (a non-array $data or $where, a
			// process_fields() charset failure), and in those cases last_error still holds
			// whatever the previous query left - usually nothing - so a genuinely rejected write
			// looked like a no-op and was reported to the client as success.
			//
			// But 0 is ambiguous, and testing only `false !==` trades that bug for its mirror:
			// MySQL also reports 0 affected rows when the WHERE matched NOTHING, with no error
			// set. Verified: an UPDATE with an unchanged value and an UPDATE against a deleted
			// primary key both return 0 and both leave last_error empty. This branch is entered on
			// `$entity->$primary_key > 0` alone and never confirms the row still exists, so
			// treating every 0 as success reports a persisted save for a row that is gone - and
			// the subclass hooks then act on it (DataMappers\Gallery::save_entity() re-runs
			// wp_mkdir_p() and fires ngg_created_new_gallery for the deleted gallery). The window
			// is real on exactly the path this change is for: Lightroom's gallery_edit loads the
			// gallery, runs the whole per-image download/resize loop, and saves seconds later.
			//
			// So: false is a refused write; a positive count is a persisted change; and 0 is
			// resolved by asking whether the row is actually there. The extra read happens only on
			// the 0 path, so the per-save cost this change exists to avoid is not reintroduced.
			// The cache is flushed first because this entity was very likely read into it earlier
			// in the same request, and a stale hit would defeat the check.
			$affected = $this->_update( $entity );

			if ( false !== $affected ) {
				if ( 0 !== (int) $affected ) {
					$retval = intval( $entity->$primary_key );
				} else {
					$this->flush_query_cache();

					if ( $this->find( $entity->$primary_key ) ) {
						$retval = intval( $entity->$primary_key );
					}
				}
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
			$this->flush_query_cache();
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

		if ( $retval ) {
			$this->flush_query_cache();
		}

		return $retval;
	}

	/**
	 * Creates an entity in the database.
	 *
	 * @param object $entity
	 * @return boolean
	 */
	public function _create( $entity ) {

		$retval             = false;
		$custom_post_entity = $this->create_custom_post_entity( $entity );

		// Try persisting the custom post type record first.
		$custom_post_id = $this->_custom_post_mapper->save( $custom_post_entity );
		if ( $custom_post_id ) {
			$entity->extras_post_id = $custom_post_id;
		}

		$table_data = $this->_convert_to_table_data( $entity );

		$id = $this->_wpdb()->insert( $this->get_table_name(), $table_data );

		if ( $id ) {
			$key          = $this->get_primary_key_column();
			$retval       = intval( $this->_wpdb()->insert_id );
			$entity->$key = $retval;
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
	 * Adds a column to the table.
	 *
	 * @param string        $column_name
	 * @param string        $datatype
	 * @param string|number $default_value
	 * @return bool
	 */
	public function _add_column( $column_name, $datatype, $default_value = null ) {
		if ( isset( $this->_columns[ $column_name ] ) && $this->_columns[ $column_name ]['extra'] ) {
			return false;
		}

		$table_name = esc_sql( $this->get_table_name() );

		// Direct database check to avoid duplicate column error if transient cache is stale.
		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$exists = $this->_wpdb()->get_results( $this->_wpdb()->prepare( "SHOW COLUMNS FROM `{$table_name}` LIKE %s", $column_name ) );
		if ( ! empty( $exists ) ) {
			return false;
		}

		if ( $default_value ) {
			if ( is_string( $default_value ) ) {
				$sql = $this->_wpdb()->prepare(
					"ALTER TABLE `{$table_name}` ADD COLUMN `{$column_name}` {$datatype} NOT NULL DEFAULT %s",
					str_replace( "'", "\\'", $default_value )
				);
			} else {
				$sql = $this->_wpdb()->prepare(
					"ALTER TABLE `{$table_name}` ADD COLUMN `{$column_name}` {$datatype} NOT NULL DEFAULT %d",
					$default_value
				);
			}
		} else {
			$sql = "ALTER TABLE `{$table_name}` ADD COLUMN `{$column_name}` {$datatype}";
		}

		$return = (bool) $this->_wpdb()->query( $sql );
		$this->update_columns_cache();
		return $return;
	}

	/**
	 * Gets the generated query.
	 *
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
	 * Gets the actual generated query.
	 *
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
	 * Gets extra columns.
	 *
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
