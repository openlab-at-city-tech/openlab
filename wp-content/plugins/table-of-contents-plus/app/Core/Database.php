<?php
namespace AIOSEO\TableOfContents\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds queries for the database and returns results.
 *
 * @since 1.0.0
 */
class Database {
	/**
	 * List of custom tables we support.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	public $customTables = [
		'aioseo_table_of_contents_cache',
		'aioseo_table_of_contents_links',
		'aioseo_table_of_contents_link_status',
		'aioseo_table_of_contents_notifications',
		'aioseo_table_of_contents_posts'
	];

	/**
	 * Holds the global $wpdb instance.
	 *
	 * @since 1.0.0
	 *
	 * @var \wpdb
	 */
	public $db = null;

	/**
	 * Holds the $wpdb prefix.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $prefix = '';

	/**
	 * Cached result of php_sapi_name() for performance.
	 *
	 * @since 1.0.0
	 *
	 * @var string|null
	 */
	private static $sapiName = null;

	/**
	 * The database table in use by this query.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $table = '';

	/**
	 * The sql statement (SELECT, INSERT, UPDATE, DELETE, etc.).
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $statement = '';

	/**
	 * The limit clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $limit = '';

	/**
	 * The group clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $group = [];

	/**
	 * The order by clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $order = [];

	/**
	 * The select clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $select = [];

	/**
	 * The set clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $set = [];

	/**
	 * Duplicate clause for the INSERT query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $onDuplicate = [];

	/**
	 * Ignore clause for the INSERT query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $ignore = false;

	/**
	 * The where clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $where = [];

	/**
	 * The union clause for the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $union = [];

	/**
	 * The JOIN clause for the SQL query.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $join = [];

	/**
	 * Determines whether the select statement should be distinct.
	 *
	 * @since 1.0.0
	 *
	 * @var bool
	 */
	private $distinct = false;

	/**
	 * The order by direction for the query.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $orderDirection = 'ASC';

	/**
	 * The query string is populated after the __toString function is run.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $query = '';

	/**
	 * The sql query results are stored here.
	 *
	 * @since 1.0.0
	 *
	 * @var mixed
	 */
	private $result = [];

	/**
	 * The method in which $wpdb will output results.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $output = 'OBJECT';

	/**
	 * Whether or not to strip tags.
	 *
	 * @since 1.0.0
	 *
	 * @var boolean
	 */
	private $stripTags = false;

	/**
	 * Set which option to use to escape the sql query.
	 *
	 * @since 1.0.0
	 *
	 * @var integer
	 */
	protected $escapeOptions = 0;

	/**
	 * A cache of all queries and their results.
	 *
	 * @var array
	 */
	private $cache = [];

	/**
	 * Whether or not to reset the cached results.
	 *
	 * @var boolean
	 */
	private $shouldResetCache = false;

	/**
	 * Constant for escape options.
	 *
	 * @since 1.0.0
	 *
	 * @var integer
	 */
	const ESCAPE_FORCE = 2;

	/**
	 * Constant for escape options.
	 *
	 * @since 1.0.0
	 *
	 * @var integer
	 */
	const ESCAPE_STRIP_HTML = 4;

	/**
	 * Constant for escape options.
	 *
	 * @since 1.0.0
	 *
	 * @var integer
	 */
	const ESCAPE_QUOTE = 8;

	/**
	 * List of model class instances.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	private $models = [];

	/**
	 * The last query that ran, stringified.
	 *
	 * @since 1.1.0
	 */
	public $lastQuery = '';

	/**
	 * Prepares the database class for use.
	 *
	 * @since 1.0.0
	 *
	 * @global object $wpdb The WordPress database object.
	 */
	public function __construct( $escape = null ) {
		global $wpdb;
		$this->db            = $wpdb;
		$this->prefix        = $wpdb->prefix;
		$this->escapeOptions = is_null( $escape ) ? self::ESCAPE_STRIP_HTML | self::ESCAPE_QUOTE : $escape;
	}

	/**
	 * If this is a clone, lets reset all the data.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		// We need to reset the result separetely as well since it is not in the default array.
		$this->reset( [ 'result' ] );
		$this->reset();
	}

	/**
	 * Gets all columns from a table.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $table The name of the table to lookup columns for.
	 * @return array         An array of custom AIOSEO tables.
	 */
	public function getColumns( $table ) {
		// Ensure the table name has the DB prefix.
		if ( 0 !== stripos( $table, $this->prefix ) ) {
			$table = $this->prefix . $table;
		}

		// If the table is not an AIOSEO one, get it from the DB.
		if ( 0 !== stripos( $table, $this->prefix . 'aioseo_' ) ) {
			return $this->db->get_col( 'SHOW COLUMNS FROM `' . $table . '`' );
		}

		$schema = $this->getTablesWithColumns();

		return $schema[ $table ];
	}

	/**
	 * Checks if a table exists.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $table The name of the table.
	 * @return bool          Whether or not the table exists.
	 */
	public function tableExists( $table ) {
		// Ensure the table name has the DB prefix.
		if ( 0 !== stripos( $table, $this->prefix ) ) {
			$table = $this->prefix . $table;
		}

		$tables = $this->getTablesWithColumns();

		return isset( $tables[ $table ] );
	}

	/**
	 * Checks if a column exists on a given table.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $table  The name of the table.
	 * @param  string $column The name of the column.
	 * @return bool           Whether or not the column exists.
	 */
	public function columnExists( $table, $column ) {
		// Ensure the table name has the DB prefix.
		if ( 0 !== stripos( $table, $this->prefix ) ) {
			$table = $this->prefix . $table;
		}

		$tables = $this->getTablesWithColumns();

		return isset( $tables[ $table ] ) && in_array( $column, $tables[ $table ], true );
	}

	/**
	 * Get all AIOSEO tables with their columns.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of AIOSEO tables with their columns.
	 */
	public function getTablesWithColumns() {
		$tables = aioseoTableOfContents()->core->cache->get( 'db_schema' );
		if ( ! empty( $tables ) ) {
			return $tables;
		}

		$schema = $this->db->get_results(
			'SELECT TABLE_NAME, COLUMN_NAME
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE TABLE_SCHEMA = DATABASE();'
		);

		// For multisites, only include tables for the current site and the main site.
		// This prevents cache entries from containing data from other subsites' tables.
		// Subsite tables follow the pattern {base_prefix}{blog_id}_ (e.g. wp_2_, wp_3_).
		$siteTablesPrefix   = is_multisite() ? $this->db->get_blog_prefix( get_current_blog_id() ) : $this->prefix;
		$subsitePrefixRegex = is_multisite() ? '/^' . preg_quote( $this->db->base_prefix, '/' ) . '\d/' : '';

		$tables = [];
		foreach ( $schema as $row ) {
			$tableName = $row->TABLE_NAME;

			// For multisites, exclude tables from other subsites (e.g. wp_2_*, wp_3_*).
			// For the main site (blog_id = 1), also exclude all subsite tables since the main site prefix equals the base prefix.
			if ( $subsitePrefixRegex && preg_match( $subsitePrefixRegex, $tableName ) ) {
				// This is a subsite table. Only include it if it belongs to the current site.
				// For the main site (where prefix = base_prefix), exclude all subsite tables.
				if ( $siteTablesPrefix === $this->db->base_prefix || 0 !== strpos( $tableName, $siteTablesPrefix ) ) {
					continue;
				}
			}

			// Only cache tables that contain "aioseo" or "actionscheduler" to reduce cache size.
			if ( false === strpos( $tableName, 'aioseo' ) && false === strpos( $tableName, 'actionscheduler' ) ) {
				continue;
			}

			if ( ! isset( $tables[ $tableName ] ) ) {
				$tables[ $tableName ] = [];
			}

			$tables[ $tableName ][] = $row->COLUMN_NAME;
		}

		aioseoTableOfContents()->core->cache->update( 'db_schema', $tables, DAY_IN_SECONDS );

		return $tables;
	}

	/**
	 * The query string in all its glory.
	 *
	 * @since 1.0.0
	 *
	 * @return string The actual query string.
	 */
	public function __toString() {
		switch ( strtoupper( $this->statement ) ) {
			case 'INSERT':
				$insert = 'INSERT ';
				if ( $this->ignore ) {
					$insert .= 'IGNORE ';
				}
				$insert .= 'INTO ' . $this->table;
				$clauses   = [];
				$clauses[] = $insert;
				$clauses[] = 'SET ' . implode( ', ', $this->set );
				if ( ! empty( $this->onDuplicate ) ) {
					$clauses[] = 'ON DUPLICATE KEY UPDATE ' . implode( ', ', $this->onDuplicate );
				}

				break;
			case 'REPLACE':
				$clauses   = [];
				$clauses[] = "REPLACE INTO $this->table";
				$clauses[] = 'SET ' . implode( ', ', $this->set );

				break;
			case 'UPDATE':
				$clauses   = [];
				$clauses[] = "UPDATE $this->table";

				if ( count( $this->join ) > 0 ) {
					foreach ( (array) $this->join as $join ) {
						if ( is_array( $join[1] ) ) {
							$joinOn = [];
							foreach ( (array) $join[1] as $left => $right ) {
								$joinOn[] = "$this->table.`$left` = `{$join[0]}`.`$right`";
							}

							$clauses[] = "\t" . ( ( 'LEFT' === $join[2] || 'RIGHT' === $join[2] ) ? $join[2] . ' JOIN ' : 'JOIN ' ) . $join[0] . ' ON ' . implode( ' AND ', $joinOn );
						} else {
							$clauses[] = "\t" . ( ( 'LEFT' === $join[2] || 'RIGHT' === $join[2] ) ? $join[2] . ' JOIN ' : 'JOIN ' ) . "{$join[0]} ON {$join[1]}";
						}
					}
				}

				$clauses[] = 'SET ' . implode( ', ', $this->set );

				if ( count( $this->where ) > 0 ) {
					$clauses[] = "WHERE 1 = 1 AND\n\t" . implode( "\n\tAND ", $this->where );
				}

				if ( count( $this->order ) > 0 ) {
					$clauses[] = 'ORDER BY ' . implode( ', ', $this->order );
				}

				if ( $this->limit ) {
					$clauses[] = 'LIMIT ' . $this->limit;
				}

				break;

			case 'TRUNCATE':
				$clauses   = [];
				$clauses[] = "TRUNCATE TABLE $this->table";
				break;

			case 'DELETE':
				$clauses   = [];
				$clauses[] = "DELETE FROM $this->table";

				if ( count( $this->where ) > 0 ) {
					$clauses[] = "WHERE 1 = 1 AND\n\t" . implode( "\n\tAND ", $this->where );
				}

				if ( count( $this->order ) > 0 ) {
					$clauses[] = 'ORDER BY ' . implode( ', ', $this->order );
				}

				if ( $this->limit ) {
					$clauses[] = 'LIMIT ' . $this->limit;
				}

				break;
			case 'SELECT':
			case 'SELECT DISTINCT':
			default:
				// Select fields.
				$clauses   = [];
				$distinct  = ( $this->distinct || stripos( $this->statement, 'DISTINCT' ) !== false ) ? 'DISTINCT ' : '';
				$select    = ( count( $this->select ) > 0 ) ? implode( ",\n\t", $this->select ) : '*';
				$clauses[] = "SELECT {$distinct}\n\t{$select}";

				// Select table.
				$clauses[] = "FROM $this->table";

				// Select joins.
				if ( ! empty( $this->join ) && count( $this->join ) > 0 ) {
					foreach ( (array) $this->join as $join ) {
						if ( is_array( $join[1] ) ) {
							$joinOn = [];
							foreach ( (array) $join[1] as $left => $right ) {
								$joinOn[] = "$this->table.`$left` = `{$join[0]}`.`$right`";
							}

							$clauses[] = "\t" . ( ( 'LEFT' === $join[2] || 'RIGHT' === $join[2] ) ? $join[2] . ' JOIN ' : 'JOIN ' ) . $join[0] . ' ON ' . implode( ' AND ', $joinOn );
						} else {
							$clauses[] = "\t" . ( ( 'LEFT' === $join[2] || 'RIGHT' === $join[2] ) ? $join[2] . ' JOIN ' : 'JOIN ' ) . "{$join[0]} ON {$join[1]}";
						}
					}
				}

				// Select conditions.
				if ( count( $this->where ) > 0 ) {
					$clauses[] = "WHERE 1 = 1 AND\n\t" . implode( "\n\tAND ", $this->where );
				}

				// Union queries.
				if ( count( $this->union ) > 0 ) {
					foreach ( $this->union as $union ) {
						$keyword   = ( $union[1] ) ? 'UNION' : 'UNION ALL';
						$clauses[] = "\n$keyword\n\n$union[0]";
					}

					$clauses[] = '';
				}

				// Select groups.
				if ( count( $this->group ) > 0 ) {
					$clauses[] = 'GROUP BY ' . implode( ', ', $this->escapeColNames( $this->group ) );
				}

				// Select order.
				if ( count( $this->order ) > 0 ) {
					$orderFragments = [];
					foreach ( $this->escapeColNames( $this->order ) as $col ) {
						$orderFragments[] = ( preg_match( '/ (ASC|DESC|RAND\(\))$/i', (string) $col ) ) ? $col : "$col $this->orderDirection";
					}

					$clauses[] = 'ORDER BY ' . implode( ', ', $orderFragments );
				}

				// Select limit.
				if ( $this->limit ) {
					$clauses[] = 'LIMIT ' . $this->limit;
				}

				break;
		}

		// @HACK for wpdb::prepare.
		$clauses[] = '/* %d = %d */';

		$this->query = str_replace( '%%d = %%d', '%d = %d', str_replace( '%', '%%', implode( "\n", $clauses ) ) );

		$this->lastQuery = $this->query;

		return $this->query;
	}

	/**
	 * Shortcut method to return the query string.
	 *
	 * @since 1.0.0
	 *
	 * @return string The query string.
	 */
	public function query() {
		return $this->__toString();
	}

	/**
	 * Start a new Database Query.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @param  string  $statement      The MySQL statement for the query.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function start( $table = null, $includesPrefix = false, $statement = 'SELECT' ) {
		// Always reset everything when starting a new query.
		$this->reset();
		$this->table = $includesPrefix ? $table : $this->prefix . $table;
		$this->statement = $statement;

		return $this;
	}

	/**
	 * Shortcut method for start with INSERT as the statement.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function insert( $table = null, $includesPrefix = false ) {
		return $this->start( $table, $includesPrefix, 'INSERT' );
	}

	/**
	 * Shortcut method for start with INSERT IGNORE as the statement.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function insertIgnore( $table = null, $includesPrefix = false ) {
		$this->ignore = true;

		return $this->start( $table, $includesPrefix, 'INSERT' );
	}

	/**
	 * Inserts multiple rows into a table in a single query.
	 *
	 * Handles all escaping and sanitization internally:
	 * - esc_sql() for SQL safety (strings only; ints/floats pass through unquoted).
	 * - Strips newlines, null bytes and invalid UTF-8 from string values.
	 * - NULL values become literal NULL.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $table   Table name (without prefix).
	 * @param  array  $columns Column names.
	 * @param  array  $rows    Array of row arrays (values in same order as $columns).
	 * @param  array  $options Optional: 'onDuplicate' => ['col1', 'col2'], 'ignore' => true.
	 * @return void
	 */
	public function bulkInsert( $table, $columns, $rows, $options = [] ) {
		if ( empty( $rows ) ) {
			return;
		}

		$tableName = $this->prefix . $table;

		$valueSets = [];
		foreach ( $rows as $row ) {
			$values = [];
			foreach ( $row as $value ) {
				if ( null === $value ) {
					$values[] = 'NULL';

					continue;
				}

				if ( is_bool( $value ) ) {
					$values[] = $value ? 1 : 0;

					continue;
				}

				if ( is_int( $value ) || is_float( $value ) ) {
					$values[] = $value;

					continue;
				}

				if ( is_array( $value ) || is_object( $value ) ) {
					$value = wp_json_encode( $value );
				}

				// Sanitize string values.
				$value = str_replace( [ "\r\n", "\r", "\n" ], ' ', (string) $value );
				$value = str_replace( "\0", '', $value );
				$value = wp_check_invalid_utf8( $value, true );

				$values[] = "'" . esc_sql( $value ) . "'";
			}

			$valueSets[] = '(' . implode( ', ', $values ) . ')';
		}

		$ignore         = ! empty( $options['ignore'] ) ? 'IGNORE ' : '';
		$columnList     = '`' . implode( '`, `', $columns ) . '`';
		$implodedValues = implode( ', ', $valueSets );

		$sql = "INSERT {$ignore}INTO {$tableName} ({$columnList}) VALUES {$implodedValues}";

		if ( ! empty( $options['onDuplicate'] ) ) {
			$updates = [];
			foreach ( $options['onDuplicate'] as $key => $col ) {
				if ( is_int( $key ) ) {
					$updates[] = "`{$col}` = VALUES(`{$col}`)";
				} else {
					$updates[] = "`{$key}` = {$col}";
				}
			}

			$sql .= ' ON DUPLICATE KEY UPDATE ' . implode( ', ', $updates );
		}

		$this->execute( $sql );
	}

	/**
	 * Shortcut method for start with UPDATE as the statement.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function update( $table = null, $includesPrefix = false ) {
		return $this->start( $table, $includesPrefix, 'UPDATE' );
	}

	/**
	 * Shortcut method for start with REPLACE as the statement.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function replace( $table = null, $includesPrefix = false ) {
		return $this->start( $table, $includesPrefix, 'REPLACE' );
	}

	/**
	 * Shortcut method for start with TRUNCATE as the statement.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function truncate( $table = null, $includesPrefix = false ) {
		return $this->start( $table, $includesPrefix, 'TRUNCATE' );
	}

	/**
	 * Shortcut method for start with DELETE as the statement.
	 *
	 * @since 1.0.0
	 *
	 * @param  string  $table          The name of the table without the WordPress prefix unless includes_prefix is true.
	 * @param  boolean $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                Returns the Database class which can then be method chained for building the query.
	 */
	public function delete( $table = null, $includesPrefix = false ) {
		return $this->start( $table, $includesPrefix, 'DELETE' );
	}

	/**
	 * Adds a SELECT clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function select() {
		$args = (array) func_get_args();
		if ( count( $args ) === 1 && is_array( $args[0] ) ) {
			$args = $args[0];
		}

		$this->select = array_merge( $this->select, $this->escapeColNames( $args ) );

		return $this;
	}

	/**
	 * Adds a WHERE clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function where() {
		$criteria = $this->prepArgs( func_get_args() );

		foreach ( (array) $criteria as $field => $value ) {
			if ( ! preg_match( '/[\(\)<=>!]+/', (string) $field ) && false === stripos( $field, ' IS ' ) ) {
				$operator = ( is_null( $value ) ) ? 'IS' : '=';
				$escaped  = $this->escapeColNames( $field );
				$field    = array_pop( $escaped ) . ' ' . $operator;
			}

			if ( is_null( $value ) && false !== stripos( $field, ' IS ' ) ) {
				// WHERE `field` IS NOT NULL.
				$this->where[] = "$field NULL";
			} elseif ( is_null( $value ) ) {
				// WHERE `field` IS NULL.
				$this->where[] = "$field NULL";
			} elseif ( is_array( $value ) ) {
				$wheres = [];
				foreach ( (array) $value as $val ) {
					$wheres[] = sprintf( "$field %s", $this->escape( $val, $this->getEscapeOptions() | self::ESCAPE_QUOTE ) );
				}

				$this->where[] = '(' . implode( ' OR ', $wheres ) . ')';
			} else {
				$this->where[] = sprintf( "$field %s", $this->escape( $value, $this->getEscapeOptions() | self::ESCAPE_QUOTE ) );
			}
		}

		return $this;
	}

	/**
	 * Adds a complex WHERE clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function whereRaw() {
		$criteria = $this->prepArgs( func_get_args() );

		foreach ( (array) $criteria as $clause ) {
			$this->where[] = $clause;
		}

		return $this;
	}

	/**
	 * Adds a WHERE clause with all arguments sent separated by OR instead of AND inside a subclause.
	 * @example [ 'a' => 1, 'b' => 2 ] becomes "AND (a = 1 OR b = 2)"
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function whereOr() {
		$criteria = $this->prepArgs( func_get_args() );

		$or = [];
		foreach ( (array) $criteria as $field => $value ) {
			if ( ! preg_match( '/[\(\)<=>!]+/', (string) $field ) && false === stripos( $field, ' IS ' ) ) {
				$operator = ( is_null( $value ) ) ? 'IS' : '=';
				$field    = $this->escapeColNames( $field );
				$field    = array_pop( $field ) . ' ' . $operator;
			}

			if ( is_null( $value ) && false !== stripos( $field, ' IS ' ) ) {
				// WHERE `field` IS NOT NULL.
				$or[] = "$field NULL";
				continue;
			}

			if ( is_null( $value ) ) {
				// WHERE `field` IS NULL.
				$or[] = "$field NULL";
				continue;
			}

			$or[] = sprintf( "$field %s", $this->escape( $value, $this->getEscapeOptions() | self::ESCAPE_QUOTE ) );
		}

		// Create our subclause, and add it to the WHERE array.
		$this->where[] = '(' . implode( ' OR ', $or ) . ')';

		return $this;
	}

	/**
	 * Adds a WHERE IN() clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function whereIn() {
		$criteria = $this->prepArgs( func_get_args() );

		foreach ( (array) $criteria as $field => $values ) {
			if ( ! is_array( $values ) ) {
				$values = [ $values ];
			} elseif ( count( $values ) === 0 ) {
				continue;
			}

			foreach ( $values as &$value ) {
				// Note: We can no longer check for `is_numeric` because a value like `61021e6242255` returns true and breaks the query.
				if ( is_integer( $value ) || is_float( $value ) ) {
					// No change.
				} elseif ( is_null( $value ) || false !== stristr( $value, 'NULL' ) ) {
					// Change to a true NULL value.
					$value = null;
				} else {
					$value = sprintf( '%s', $this->escape( $value, $this->getEscapeOptions() | self::ESCAPE_QUOTE ) );
				}
			}

			$values = implode( ',', $values );
			$this->whereRaw( "$field IN($values)" );
		}

		return $this;
	}

	/**
	 * Adds a WHERE NOT IN() clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function whereNotIn() {
		$criteria = $this->prepArgs( func_get_args() );

		foreach ( (array) $criteria as $field => $values ) {
			if ( ! is_array( $values ) ) {
				$values = [ $values ];
			} elseif ( count( $values ) === 0 ) {
				continue;
			}

			foreach ( $values as &$value ) {
				// Note: We can no longer check for `is_numeric` because a value like `61021e6242255` returns true and breaks the query.
				if ( is_int( $value ) || is_float( $value ) ) {
					// No change.
					continue;
				}

				if ( is_null( $value ) || false !== stristr( $value, 'NULL' ) ) {
					// Change to a true NULL value.
					$value = null;
					continue;
				}

				$value = sprintf( '%s', $this->escape( $value, $this->getEscapeOptions() | self::ESCAPE_QUOTE ) );
			}

			$values = implode( ',', $values );
			$this->whereRaw( "$field NOT IN($values)" );
		}

		return $this;
	}

	/**
	 * Adds a WHERE LIKE clause.
	 *
	 * @since 1.0.0
	 *
	 * @param  string   $field        The column name.
	 * @param  string   $value        The value to search for.
	 * @param  bool     $hasWildcard  Whether the value contains LIKE wildcards (% and _) for pattern matching. Default false for security.
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function whereLike( $field, $value, $hasWildcard = false ) {
		if ( is_null( $value ) ) {
			return $this;
		}

		// Escape the column name.
		$escapedField = $this->escapeColNames( $field );
		$field        = array_pop( $escapedField );

		// Escape LIKE wildcards (% and _) unless the value is intended to contain wildcards for pattern matching.
		if ( ! $hasWildcard ) {
			$value = $this->db->esc_like( $value );
		}

		// Escape and quote the value for safe use in LIKE clause.
		$escapedValue = $this->escape( $value, $this->getEscapeOptions() | self::ESCAPE_QUOTE );

		$this->where[] = sprintf( "$field LIKE %s", $escapedValue );

		return $this;
	}

	/**
	 * Adds a LEFT JOIN clause.
	 *
	 * @since 1.0.0
	 *
	 * @param  string       $table          The name of the table to join to this query.
	 * @param  string|array $conditions     The conditions of the join clause.
	 * @param  boolean      $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                     Returns the Database class which can be method chained for more query building.
	 */
	public function leftJoin( $table, $conditions, $includesPrefix = false ) {
		return $this->join( $table, $conditions, 'LEFT', $includesPrefix );
	}

	/**
	 * Adds a JOIN clause.
	 *
	 * @since 1.0.0
	 *
	 * @param  string       $table          The name of the table to join to this query.
	 * @param  string|array $conditions     The conditions of the join clause.
	 * @param  string       $direction      This can take 'LEFT' or 'RIGHT' as arguments.
	 * @param  boolean      $includesPrefix This determines if the table name includes the WordPress prefix or not.
	 * @return Database                     Returns the Database class which can be method chained for more query building.
	 */
	public function join( $table, $conditions, $direction = '', $includesPrefix = false ) {
		$this->join[] = [ $includesPrefix ? $table : $this->prefix . $table, $conditions, $direction ];

		return $this;
	}

	/**
	 * Add a UNION query.
	 *
	 * @since 1.0.0
	 *
	 * @param  Database|string $query    The query (Database object or query string) to be joined with.
	 * @param  Bool            $distinct Set whether this union should be distinct or not.
	 * @return Database                  Returns the Database class which can be method chained for more query building.
	 */
	public function union( $query, $distinct = true ) {
		$this->union[] = [ $query, $distinct ];

		return $this;
	}

	/**
	 * Adds a GROUP BY clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function groupBy() {
		$args = (array) func_get_args();
		if ( count( $args ) === 1 && is_array( $args[0] ) ) {
			$args = $args[0];
		}

		$this->group = array_merge( $this->group, $args );

		return $this;
	}


	/**
	 * Adds a ORDER BY clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function orderBy() {
		// Normalize arguments.
		$args = (array) func_get_args();
		if ( count( $args ) === 1 && is_array( $args[0] ) ) {
			$args = $args[0];
		}

		$orderBy = [];
		// Separate commas to account for multiple orders.
		foreach ( $args as $argComma ) {
			$orderBy = array_map( 'trim', array_merge( $orderBy, explode( ',', $argComma ) ) );
		}

		// Validate and sanitize column names and sort directions.
		$sanitizedOrderBy = [];
		foreach ( $orderBy as $ordBy ) {
			$parts     = explode( ' ', $ordBy );
			$column    = str_replace( '`', '', $parts[0] ); // Strip existing ticks first.
			$column    = preg_replace( '/[^a-zA-Z0-9_.]/', '', $column ); // Strip invalid characters from the column name.
			$column    = $this->escapeColNames( $column )[0];
			$direction = isset( $parts[1] ) ? strtoupper( $parts[1] ) : 'ASC';

			// Validate the order direction.
			if ( ! in_array( $direction, [ 'ASC', 'DESC' ], true ) ) {
				$direction = 'ASC';
			}

			$sanitizedOrderBy[] = "$column $direction";
		}

		if ( ! empty( $sanitizedOrderBy ) ) {
			if ( ! empty( $args[0] ) && true !== $args[0] ) {
				$this->order = array_merge( $this->order, $sanitizedOrderBy );
			} else {
				// This allows for overwriting a preexisting order-by setting.
				array_shift( $sanitizedOrderBy );
				$this->order = $sanitizedOrderBy;
			}
		}

		return $this;
	}

	/**
	 * Adds a raw ORDER BY clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function orderByRaw( $orderBy ) {
		$this->order = array_merge( $this->order, $orderBy );

		return $this;
	}

	/**
	 * Sets the sort direction for ORDER BY clauses.
	 *
	 * @since 1.0.0
	 *
	 * @param string    $direction This sets the direction of the order by clause, default is 'ASC'.
	 * @return Database            Returns the Database class which can be method chained for more query building.
	 */
	public function orderDirection( $direction = 'ASC' ) {
		$this->orderDirection = $direction;

		return $this;
	}

	/**
	 * Adds a LIMIT clause.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed     $limit A string or array that sets the limit clause.
	 * @return Database        Returns the Database class which can be method chained for more query building.
	 */
	public function limit( $limit, $offset = -1 ) {
		if ( ! is_numeric( $limit ) || $limit <= 0 ) {
			return $this;
		}

		if ( ! is_numeric( $offset ) ) {
			$offset = -1;
		}

		$this->limit = ( -1 === $offset )
			? intval( $limit )
			: intval( $offset ) . ', ' . intval( $limit );

		return $this;
	}

	/**
	 * Converts associative arrays to a SET argument.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args        The arguments.
	 * @return array $preparedSet The prepared arguments.
	 */
	private function prepareSet( $args ) {
		$args = $this->prepArgs( $args );

		$preparedSet = [];
		foreach ( (array) $args as $field => $value ) {
			if ( is_null( $value ) ) {
				$preparedSet[] = "`$field` = NULL";
			} elseif ( is_array( $value ) ) {
				throw new \Exception( 'Cannot save an unserialized array in the database. Data passed was: ' . wp_json_encode( $value ) );
			} elseif ( is_object( $value ) ) {
				throw new \Exception( 'Cannot save an unserialized object in the database. Data passed was: ' . esc_html( $value ) );
			} else {
				$preparedSet[] = sprintf( "`$field` = %s", $this->escape( $value, $this->getEscapeOptions() | self::ESCAPE_QUOTE ) );
			}
		}

		return $preparedSet;
	}

	/**
	 * Adds a SET clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function set() {
		$this->set = array_merge( $this->set, $this->prepareSet( func_get_args() ) );

		return $this;
	}

	/**
	 * Adds an ON DUPLICATE clause.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function onDuplicate() {
		$this->onDuplicate = array_merge( $this->onDuplicate, $this->prepareSet( func_get_args() ) );

		return $this;
	}

	/**
	 * Set the output for the query.
	 *
	 * @since 1.0.0
	 *
	 * @param  string   $output  This can be one of the following: ARRAY_A | ARRAY_N | OBJECT | OBJECT_K.
	 * @return Database          Returns the Database class which can be method chained for more query building.
	 */
	public function output( $output ) {
		$this->output = $output;

		return $this;
	}

	/**
	 * Reset the cache so we make sure the query gets to the DB.
	 *
	 * @since 1.0.0
	 *
	 * @return Database Returns the Database class which can be method chained for more query building.
	 */
	public function resetCache() {
		$this->shouldResetCache = true;

		return $this;
	}

	/**
	 * Run this query.
	 *
	 * @since 1.0.0
	 *
	 * @param  boolean  $reset  Whether to reset the results/query.
	 * @param  string   $return Determine which method to call on the $wpdb object
	 * @param  array    $params Optional extra parameters to pass to the db method call
	 * @return Database         Database query results.
	 */
	public function run( $reset = true, $return = 'results', $params = [] ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		if ( ! in_array( $return, [ 'results', 'col', 'var' ], true ) ) {
			$return = 'results';
		}

		// Cache query string to avoid generating it twice.
		$queryString     = $this->query();
		$prepare         = $this->db->prepare( $queryString, 1, 1 );
		$queryHash       = md5( $queryString );
		$cacheTableName  = $this->getCacheTableName();

		// reset() nulls out the statement below, so we have to grab it before that happens.
		$statement = $this->statement;

		// Pull the result from the in-memory cache if everything checks out.
		if (
			! $this->shouldResetCache &&
			isset( $this->cache[ $cacheTableName ][ $queryHash ][ $return ] ) &&
			empty( $this->join )
		) {
			$this->result = $this->cache[ $cacheTableName ][ $queryHash ][ $return ];

			return $this;
		}

		switch ( $return ) {
			case 'col':
				$this->result = $this->db->get_col( $prepare );
				break;

			case 'var':
				$this->result = $this->db->get_var( $prepare );
				break;

			default:
				$this->result = $this->db->get_results( $prepare, $this->output );
		}

		if ( $reset ) {
			$this->reset();
		}

		// Only cache SELECT queries for performance.
		if ( in_array( $statement, [ 'SELECT', 'SELECT DISTINCT' ], true ) ) {
			$this->cache[ $cacheTableName ][ $queryHash ][ $return ] = $this->result;
		}

		// Reset the cache trigger for the next run.
		$this->shouldResetCache = false;

		return $this;
	}

	/**
	 * Inject a count select statement and return the result.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $countColumn The column to count with. Defaults to '*' all.
	 * @return int                 The count total.
	 */
	public function count( $countColumn = '*' ) {
		$usingGroup = ! empty( $this->group );
		$results    = $this->select( 'count(' . $countColumn . ') as count' )
			->run()
			->result();

		return 1 === $this->numRows() && ! $usingGroup ? (int) $results[0]->count : $this->numRows();
	}

	/**
	 * Returns the query results based on the output.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed This could be an array or an object based on the original output method.
	 */
	public function result() {
		return $this->result;
	}

	/**
	 * Return a model model from a row.
	 *
	 * @since 1.0.0
	 *
	 * @param string  $class The class to call.
	 * @return object        The class object.
	 */
	public function model( $class ) {
		$result = $this->result();

		return ! empty( $result ) ? ( is_array( $result ) ? new $class( (array) current( $result ) ) : $result ) : new $class();
	}

	/**
	 * Return an array of model models from the result
	 *
	 * @since 1.0.0
	 *
	 * @param  string $class  The class to call.
	 * @param  string $id     The id of the index to use.
	 * @param  bool $toJson Whether to convert to json.
	 * @return array         An array of class models.
	 */
	public function models( $class, $id = null, $toJson = false ) {
		if ( empty( $this->models ) ) {
			$i      = 0;
			$models = [];
			foreach ( $this->result() as $row ) {
				$var   = ( null === $id ) ? $row : $row[ $id ];
				$class = new $class( $var );
				// Lets add the class to the array using the class ID.
				$models[ $class->id ] = $toJson ? $class->jsonSerialize() : $class;
				$i++;
			}

			$this->models = $models;
		}

		return $this->models;
	}

	/**
	 * Returns the last error reported by MySQL.
	 *
	 * @since 1.0.0
	 *
	 * @return string The last error.
	 */
	public function lastError() {
		return $this->db->last_error;
	}

	/**
	 * Return the $wpdb insert_id from the last query.
	 *
	 * @since 1.0.0
	 *
	 * @return integer The id of the most recent INSERT query.
	 */
	public function insertId() {
		return $this->db->insert_id;
	}

	/**
	 * Return the $wpdb rows_affected from the last query.
	 *
	 * @since 1.0.0
	 *
	 * @return integer The number of rows affected.
	 */
	public function rowsAffected() {
		return $this->db->rows_affected;
	}

	/**
	 * Return the $wpdb num_rows from the last query.
	 *
	 * @since 1.0.0
	 *
	 * @return integer The count for the number of rows in the last query.
	 */
	public function numRows() {
		return $this->db->num_rows;
	}

	/**
	 * Check if the last query had any rows.
	 *
	 * @since 1.0.0
	 *
	 * @return bool Whether there were any rows retrived by the last query.
	 */
	public function nullSet() {
		return ( $this->numRows() < 1 );
	}

	/**
	 * This will start a MySQL transaction. Be sure to commit or rollback!
	 *
	 * @since 1.0.0
	 */
	public function startTransaction() {
		$this->db->query( 'START TRANSACTION' );
	}

	/**
	 * This will commit a MySQL transaction. Used in conjunction with startTransaction.
	 *
	 * @since 1.0.0
	 */
	public function commit() {
		$this->db->query( 'COMMIT' );
	}

	/**
	 * This will rollback a MySQL transaction. Used in conjunction with startTransaction.
	 *
	 * @since 1.0.0
	 */
	public function rollback() {
		$this->db->query( 'ROLLBACK' );
	}

	/**
	 * Fast way to execute queries.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $sql The sql query to execute.
	 * @return mixed       Could be an array or object depending on the result set.
	 */
	public function execute( $sql, $results = false ) {
		$this->lastQuery = $sql;

		if ( $results ) {
			$this->result = $this->db->get_results( $sql );

			return $this;
		}

		return $this->db->query( $sql );
	}

	/**
	 * Escape a value for safe use in SQL queries.
	 *
	 * @param string  $value   The value to be escaped.
	 * @param boolean $options Escape options.
	 * @return string          The escaped SQL value.
	 */
	public function escape( $value, $options = null ) {
		if ( is_array( $value ) ) {
			foreach ( $value as &$val ) {
				$val = $this->escape( $val, $options );
			}

			return $value;
		}

		$options = ( is_null( $options ) ) ? $this->getEscapeOptions() : $options;
		if ( ( $options & self::ESCAPE_STRIP_HTML ) !== 0 && isset( $this->stripTags ) && true === $this->stripTags ) {
			$value = wp_strip_all_tags( $value );
		}

		// Cache php_sapi_name() result for performance.
		if ( null === self::$sapiName ) {
			self::$sapiName = php_sapi_name();
		}

		// Check if we need to escape and quote the value.
		$needsEscaping = ( ( $options & self::ESCAPE_FORCE ) !== 0 || 'cli' === self::$sapiName ) ||
			( ( $options & self::ESCAPE_QUOTE ) !== 0 && ! is_int( $value ) && ! is_float( $value ) );

		if ( $needsEscaping ) {
			$value = esc_sql( $value );
			$value = "'$value'";
		}

		return $value;
	}

	/**
	 * Get the current escape options.
	 *
	 * @since 1.0.0
	 *
	 * @return integer The current escape options.
	 */
	public function getEscapeOptions() {
		return $this->escapeOptions;
	}


	/**
	 * Set the current escape options.
	 *
	 * @since 1.0.0
	 *
	 * @param integer $options
	 */
	public function setEscapeOptions( $options ) {
		$this->escapeOptions = $options;
	}

	/**
	 * Backtick-escapes an array of column and/or table names.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $cols An array of column names to be escaped.
	 * @return array       An array of escaped column names.
	 */
	private function escapeColNames( $cols ) {
		if ( ! is_array( $cols ) ) {
			$cols = [ $cols ];
		}

		foreach ( $cols as &$col ) {
			if ( false === stripos( $col, '(' ) && false === stripos( $col, ' ' ) && false === stripos( $col, '*' ) ) {
				if ( stripos( $col, '.' ) ) {
					list( $table, $c ) = explode( '.', $col );
					$col = "`$table`.`$c`";
				} else {
					$col = "`$col`";
				}
			}
		}

		return $cols;
	}

	/**
	 * Gets a variable list of function arguments and reformats them as needed for many of the functions of this class.
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed $values This could be anything, but if used properly its usually a string or an array.
	 * @return array         If the preparation is correct it will return an array of arguments.
	 */
	private function prepArgs( $values ) {
		$values = (array) $values;
		if ( ! is_array( $values[0] ) && count( $values ) === 2 ) {
			$values = [ $values[0] => $values[1] ];
		} elseif ( is_array( $values[0] ) && count( $values ) === 1 ) {
			$values = $values[0];
		}

		return $values;
	}

	/**
	 * Resets all the variables that make up the query.
	 *
	 * @since 1.0.0
	 *
	 * @param array     $what Set which items you want to reset, all are selected by default.
	 * @return Database       Returns the Database object.
	 */
	public function reset(
		$what = [
			'table',
			'statement',
			'limit',
			'group',
			'order',
			'select',
			'set',
			'onDuplicate',
			'ignore',
			'where',
			'union',
			'distinct',
			'orderDirection',
			'query',
			'output',
			'stripTags',
			'models',
			'join'
		]
	) {
		// If we are not running a select query, let's bust the cache for this table.
		$selectStatements = [ 'SELECT', 'SELECT DISTINCT' ];
		if (
			! empty( $this->statement ) &&
			! in_array( $this->statement, $selectStatements, true )
		) {
			$this->bustCache( $this->getCacheTableName() );
		}

		foreach ( (array) $what as $var ) {
			switch ( $var ) {
				case 'group':
				case 'order':
				case 'select':
				case 'set':
				case 'onDuplicate':
				case 'where':
				case 'union':
				case 'join':
					$this->$var = [];
					break;
				case 'orderDirection':
					$this->$var = 'ASC';
					break;
				case 'ignore':
				case 'stripTags':
					$this->$var = false;
					break;
				case 'output':
					$this->$var = 'OBJECT';
					break;
				default:
					if ( isset( $this->$var ) ) {
						$this->$var = null;
					}
					break;
			}
		}

		return $this;
	}

	/**
	 * Get the current value of one or more query properties. If only one property is specified, returns the value;
	 * if an array of values is specified, then returns an array of values.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array  $what You can pass in an array of options to retrieve. By default it selects all if them.
	 * @return string|array       Returns the value of whichever variables are passed in.
	 */
	public function getQueryProperty(
		$what = [
			'table',
			'statement',
			'limit',
			'group',
			'order',
			'select',
			'set',
			'onDuplicate',
			'where',
			'union',
			'distinct',
			'orderDirection',
			'query',
			'output',
			'result'
		]
	) {
		if ( is_array( $what ) ) {
			$return = [];
			foreach ( (array) $what as $which ) {
				$return[ $which ] = $this->$which;
			}

			return $return;
		} else {
			return $this->$what;
		}
	}

	/**
	 * Get a table name for the cache key.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $cacheTableName The table name to check against.
	 * @return string                 The cache key table name.
	 */
	private function getCacheTableName( $cacheTableName = null ) {
		$cacheTableName = empty( $cacheTableName ) ? $this->table : $cacheTableName;

		foreach ( $this->customTables as $tableName ) {
			if ( false !== stripos( $cacheTableName, $this->prefix . $tableName ) ) {
				$cacheTableName = $tableName;
				break;
			}
		}

		return $cacheTableName;
	}

	/**
	 * Busts the cache for the given table name.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|null $tableName The table name.
	 * @return void
	 */
	public function bustCache( $tableName = null ) {
		if ( ! $tableName ) {
			// Bust all the cache.
			$this->cache = [];

			return;
		}

		unset( $this->cache[ $tableName ] );
	}

	/**
	 * In order to not have a conflict, we need to return a clone.
	 *
	 * @since 1.0.0
	 *
	 * @return Database The cloned Database object.
	 */
	public function noConflict() {
		return clone $this;
	}
}