<?php
// This allows local dev environments to opt out of sharded dbs
if ( ! defined( 'DO_SHARDB' ) || ! DO_SHARDB ) {
	return;
}

if( !class_exists( 'wpdb' ) ) {
	$wpdb = true;
	require( ABSPATH . WPINC . '/wp-db.php' );
}

global $shardb_hash_length;

if( !isset( $shardb_hash_length ) ) {
	$wpdb = new wpdb( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );
	return;
}

if ( is_multisite() ) :
class SharDB extends wpdb {
	var $ready = true;
	var $never_connected = true;
	var $site_tables = false;
	var $blog_tables = false;
	var $all_tables = false;

	var $dbh;
	var $dbhs;
	var $single_db = false;
	var $db_server = array();
	var $db_servers = array();
	var $db_tables = array();

	var $persistent = false;
	var $max_connections = 10;
	var $srtm = false;
	var $db_connections;
	var $open_connections = null;
	var $current_host;
	var $dbh2host = array();
	var $last_used_server;
	var $used_servers = array();
	var $written_servers = array();
	var $last_found_rows_result = null;

	function __construct($dbuser, $dbpassword, $dbname, $dbhost) {
//		register_shutdown_function( array( $this, '__destruct' ) );

		if ( defined( 'WP_DEBUG' ) )
			$this->show_errors = (bool) WP_DEBUG;

		if ( defined( 'DB_CHARSET' ) )
			$this->charset = DB_CHARSET;
		else
			$this->charset = 'utf8';

		if ( defined( 'DB_COLLATE' ) )
			$this->collate = DB_COLLATE;
		elseif ( $this->charset == 'utf8' )
			$this->collate = 'utf8_general_ci';

		$this->save_queries = (bool) constant( 'SAVEQUERIES' );

		if ( !$this->single_db ) {
			if ( empty( $this->db_servers ) && isset( $GLOBALS['db_servers'] ) && is_array( $GLOBALS['db_servers'] ) )
				$this->db_servers =& $GLOBALS['db_servers'];
			if ( empty( $this->db_tables ) && isset( $GLOBALS['db_tables'] ) && is_array( $GLOBALS['db_tables'] ) )
				$this->db_tables =& $GLOBALS['db_tables'];
		}
		if ( empty( $this->db_servers ) ) {
			if ( empty( $this->db_server ) )
				$this->bail( 'No database servers have been set up.' );
			else
				$this->single_db = true;
		}
		$this->user_tables = $this->global_tables;
		$this->global_tables = array_merge( $this->user_tables, $this->ms_global_tables );
		$this->blog_tables = $this->tables;
		$this->site_tables = array_merge( $this->blog_tables, $this->old_tables );
		$this->all_tables = array_merge( $this->global_tables, $this->site_tables );
		$this->dbuser = $dbuser;
		$this->dbpassword = $dbpassword;
		$this->dbname = $dbname;
		$this->dbhost = $dbhost;

		/* Use ext/mysqli if it exists and:
		 *  - WP_USE_EXT_MYSQL is defined as false, or
		 *  - We are a development version of WordPress, or
		 *  - We are running PHP 5.5 or greater, or
		 *  - ext/mysql is not loaded.
		 */
		if ( function_exists( 'mysqli_connect' ) ) {
			if ( defined( 'WP_USE_EXT_MYSQL' ) ) {
				$this->use_mysqli = ! WP_USE_EXT_MYSQL;
			} elseif ( version_compare( phpversion(), '5.5', '>=' ) ) {
				$this->use_mysqli = true;
			} elseif ( false !== strpos( $GLOBALS['wp_version'], '-' ) ) {
				$this->use_mysqli = true;
			}
		}

		if ( null === $this->open_connections )
			$this->open_connections = array();

	}

	/**
	 * Find the first table name referenced in a query
	 * @param string query
	 * @return string table
	 */
	function get_table_from_query ( $q ) {
		// Remove characters that can legally trail the table name
		$q = rtrim($q, ';/-#');
		// allow (select...) union [...] style queries. Use the first queries table name.
		$q = ltrim($q, "\t (");

		// Quickly match most common queries
		if ( preg_match('/^\s*(?:'
				. 'SELECT.*?\s+FROM'
				. '|INSERT(?:\s+IGNORE)?(?:\s+INTO)?'
				. '|REPLACE(?:\s+INTO)?'
				. '|UPDATE(?:\s+IGNORE)?'
				. '|DELETE(?:\s+IGNORE)?(?:\s+FROM)?'
				. ')\s+`?(\w+)`?/is', $q, $maybe) )
			return $maybe[1];

		// Refer to the previous query
		if ( preg_match('/^\s*SELECT.*?\s+FOUND_ROWS\(\)/is', $q) )
			return $this->last_table;

		// Big pattern for the rest of the table-related queries in MySQL 5.0
		if ( preg_match('/^\s*(?:'
				. '(?:EXPLAIN\s+(?:EXTENDED\s+)?)?SELECT.*?\s+FROM'
				. '|INSERT(?:\s+LOW_PRIORITY|\s+DELAYED|\s+HIGH_PRIORITY)?(?:\s+IGNORE)?(?:\s+INTO)?'
				. '|REPLACE(?:\s+LOW_PRIORITY|\s+DELAYED)?(?:\s+INTO)?'
				. '|UPDATE(?:\s+LOW_PRIORITY)?(?:\s+IGNORE)?'
				. '|DELETE(?:\s+LOW_PRIORITY|\s+QUICK|\s+IGNORE)*(?:\s+FROM)?'
				. '|DESCRIBE|DESC|EXPLAIN|HANDLER'
				. '|(?:LOCK|UNLOCK)\s+TABLE(?:S)?'
				. '|(?:RENAME|OPTIMIZE|BACKUP|RESTORE|CHECK|CHECKSUM|ANALYZE|OPTIMIZE|REPAIR).*\s+TABLE'
				. '|TRUNCATE(?:\s+TABLE)?'
				. '|CREATE(?:\s+TEMPORARY)?\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?'
				. '|ALTER(?:\s+IGNORE)?\s+TABLE'
				. '|DROP\s+TABLE(?:\s+IF\s+EXISTS)?'
				. '|CREATE(?:\s+\w+)?\s+INDEX.*\s+ON'
				. '|DROP\s+INDEX.*\s+ON'
				. '|LOAD\s+DATA.*INFILE.*INTO\s+TABLE'
				. '|(?:GRANT|REVOKE).*ON\s+TABLE'
				. '|SHOW\s+(?:.*FROM|.*TABLE|.*TABLES\sLIKE)'
				. ')\s+[`\']?(\S+)[`\']?/is', $q, $maybe) )
			return str_replace('\\', '', $maybe[1]);

		// All unmatched queries automatically fall to the global master
		return '';
	}

	/**
	 * Determine the likelihood that this query could alter anything
	 * @param string query
	 * @return bool
	 */
	function is_write_query( $q ) {
		// Quick and dirty: only send SELECT statements to slaves
		$q = ltrim($q, "\t (");
		$word = strtoupper( substr( trim( $q ), 0, 6 ) );
		return 'SELECT' != $word;
	}

	/**
	 * Set a flag to prevent reading from slaves which might be lagging after a write
	 */
	function send_reads_to_masters() {
		$this->srtm = true;
	}

	/**
	 * Get the dataset and partition from the table name. E.g.:
	 * wp_ds_{$dataset}_{$partition}_tablename where $partition is ctype_digit
	 * wp_{$dataset}_{$hash}_tablename where $hash is 1-3 chars of ctype_xdigit
	 * @param unknown_type $table
	 * @return unknown
	 */
	function get_ds_part_from_table( $table ) {
		global $shardb_hash_length, $shardb_dataset, $shardb_num_db, $vip_db;

		$table = str_replace( '\\', '', $table );

		if ( substr( $table, 0, strlen( $this->base_prefix ) ) != $this->base_prefix
			|| !isset( $shardb_hash_length )
			|| !preg_match( '/^' . $this->base_prefix . '([0-9]+)_/', $table, $matches ) )
			return false;

		$dataset = $shardb_dataset;
		$hash = substr( md5( $matches[ 1 ] ), 0, $shardb_hash_length );
		$partition = hexdec( $hash );
		$table_blog_id = $matches[ 1 ];
// VIP Blog Check.
// Added by: Luke Poland
		if ( is_array( $vip_db ) && array_key_exists( $table_blog_id, $vip_db ) )
			$partition = $shardb_num_db + intval( $vip_db[ $table_blog_id ] );
// End VIP Addition
		return compact( 'dataset', 'hash', 'partition' );
	}

	function get_partition_from_table( $table ) {
		global $shardb_dataset, $db_ds_parts;
		if( isset( $this->db_tables[$table] ) && preg_match( '|^(.*)\_(.*)$|', $db_ds_parts[$this->db_tables[$table]], $match ) )
			return array( 'dataset' => $match[1], 'partition' => $match[2] );

		return false;
	}

	/**
	 * Figure out which database server should handle the query, and connect to it.
	 * @param string query
	 * @return resource mysql database connection
	 */
	function &db_connect( $query = '' ) {

		global $vip_db, $shardb_local_db, $enable_home_db;

		$host = isset( $this->db_server['host'] ) ? $this->db_server['host'] : $this->dbhost;
		if ( $this->persistent ) {

			$connect_function = $this->use_mysqli ? 'mysqli_connect' : 'mysql_pconnect';
			$host = 'p:' . $host;

		} else {

			$connect_function = $this->use_mysqli ? 'mysqli_connect' : 'mysql_connect';

		}

		if ( $this->single_db ) {
			if ( $this->is_resource( $this->dbh ) )
				return $this->dbh;
			$this->dbh = $connect_function( $host, $this->db_server['user'], $this->db_server['password'], true );
			if ( ! $this->is_resource( $this->dbh ) )
				$this->bail("We were unable to connect to the database at {$this->db_server['host']}.");
			if ( ! $this->select( $this->db_server['name'], $this->dbh ) )
				$this->bail("We were unable to select the database.");
			if ( !empty( $this->charset ) ) {
				$collation_query = "SET NAMES '$this->charset'";
				if ( !empty( $this->collate ) )
					$collation_query .= " COLLATE '$this->collate'";
				mysql_query($collation_query, $this->dbh);
			}
			return $this->dbh;
		} else {
			if( $this->never_connected ) {
				$this->never_connected = false;
				if( $enable_home_db && defined( 'MULTISITE' ) ) {
					foreach( $this->tables( 'site', true, 1 ) as $t )
						add_db_table( $t, 'home' );
				}
				if ( empty( $this->db_tables ) && isset( $GLOBALS['db_tables'] ) && is_array( $GLOBALS['db_tables'] ) )
					$this->db_tables =& $GLOBALS['db_tables'];
			}
			if ( empty( $query ) )
				return false;

			$write = $this->is_write_query( $query );
			$table = $this->get_table_from_query( $query );
			$this->last_table = $table;
			$partition = 0;

			 if( ( $ds_part = $this->get_ds_part_from_table( $table ) ) || ( $ds_part = $this->get_partition_from_table( $table ) ) ) {
				extract( $ds_part, EXTR_OVERWRITE );
				$dbhname = "{$dataset}_{$partition}";
			} else {
				$dbhname = $dataset = 'global';
			}
			if ( $this->srtm || $write || array_key_exists("{$dbhname}_w", $this->written_servers) ) {
				$read_dbh = $dbhname . '_r';
				$dbhname .= '_w';
				$operation = 'write';
			} else {
				$dbhname .= '_r';
				$operation = 'read';
			}

			if ( isset( $this->dbhs[$dbhname] ) && $this->is_resource( $this->dbhs[$dbhname] ) ) { // We're already connected!
				// Keep this connection at the top of the stack to prevent disconnecting frequently-used connections
				if ( $k = array_search($dbhname, $this->open_connections) ) {
					unset($this->open_connections[$k]);
					$this->open_connections[] = $dbhname;
				}

				// Using an existing connection, select the db we need and if that fails, disconnect and connect anew.
				if (
					isset( $_server['name'] ) && $this->select( $_server['name'], $this->dbhs[$dbhname] ) ||
						( isset( $this->used_servers[$dbhname]['db'] ) && $this->select( $this->used_servers[$dbhname]['db'], $this->dbhs[$dbhname] ) ) ) {
					$this->last_used_server = $this->used_servers[$dbhname];
					$this->current_host = $this->dbh2host[$dbhname];
					return $this->dbhs[$dbhname];
				} else {
					$this->disconnect($dbhname);
				}
			}

			if ( $write && defined( "MASTER_DB_DEAD" ) )
				$this->bail("We're updating the database, please try back in 5 minutes. If you are posting to your site please hit the refresh button on your browser in a few minutes to post the data again. It will be posted as soon as the database is back online again.");

			// Group eligible servers by R (plus 10,000 if remote)
			$server_groups = array();
			foreach ( $this->db_servers[$dataset][$partition] as $server ) {
				// $o = $server['read'] or $server['write']. If false, don't use this server.
				if ( !($o = $server[$operation]) )
					continue;

				if ( $server['dc'] != DATACENTER )
					$o += 10000;

				if ( isset($_server) && is_array($_server) )
					$server = array_merge($server, $_server);

				// Try the local hostname first when connecting within the DC
				if ( $server['dc'] == DATACENTER && isset($server['lhost']) ) {
					$lserver = $server;
					$lserver['host'] = $lserver['lhost'];
					$server_groups[$o - 0.5][] = $lserver;
				}

				$server_groups[$o][] = $server;
			}

			// Randomize each group and add its members to
			$servers = array();
			ksort($server_groups);
			foreach ( $server_groups as $group ) {
				if ( count($group) > 1 )
					shuffle($group);
				$servers = array_merge($servers, $group);
			}

			// at the following index # we have no choice but to connect
			$max_server_index = count($servers) - 1;

			// Connect to a database server
			foreach ( $servers as $server_index => $server ) {
				$this->timer_start();

				// make sure there's always a port #
				if( strpos( $server['host'], ':' ) !== false )
					list($host, $port) = explode(':', $server['host']);
				else {
					$host = $server['host'];
					$port = 3306;
				}

				// reduce the timeout if the host is on the lan
				$mctime = 0.2; // Default
				if ( $shardb_local_db || strtolower(substr($host, -3)) == 'lan' )
					$mctime = 0.05;

				// connect if necessary or possible
				if ( $write || $server_index == $max_server_index || $this->check_tcp_responsiveness($host, $port, $mctime) ) {
					$this->dbhs[$dbhname] = false;
					$try_count = 0;
					while ( $this->dbhs[$dbhname] === false ) {
						$try_count++;
						$this->dbhs[$dbhname] = $connect_function( "$host", $server['user'], $server['password'] );

						if ( $try_count == 4 ) {
							break;
						} else {
							if ( $this->dbhs[$dbhname] === false )
								// Possibility of waiting up to 3 seconds!
								usleep( (500000 * $try_count) );
						}
					}
				} else {
					$this->dbhs[$dbhname] = false;
				}

				if ( $this->is_resource( $this->dbhs[$dbhname] ) ) {

					$this->db_connections[] = array( "{$server['user']}@$host:$port", number_format( ( $this->timer_stop() ), 7) );
					$this->dbh2host[$dbhname] = $this->current_host = "$host:$port";
					$this->open_connections[] = $dbhname;
					break;

				} else {

					$error_details = array (
						'referrer' => "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}",
						'host' => $host,
						'error' => $this->use_mysqli ? mysqli_error( $this->dbhs[$dbhname] ) : mysql_error(),
						'errno' => $this->use_mysqli ? mysqli_errno( $this->dbhs[$dbhname] ) : mysql_errno(),
						'tcp_responsive' => $this->tcp_responsive,
					);
					$msg = date( "Y-m-d H:i:s" ) . " Can't select $dbhname - ";
					$msg .= "\n" . print_r($error_details, true);

					$this->print_error( $msg );

				}
			} // end foreach ( $servers as $server )

			if ( ! $this->is_resource( $this->dbhs[$dbhname] ) ) {

				echo "Unable to connect to $host:$port while querying table '$table' ($dbhname)";
				return $this->bail("Unable to connect to $host:$port while querying table '$table' ($dbhname)");

			}

			$this->select( $server['name'], $this->dbhs[$dbhname] );
			if ( ! $this->ready ) {
				echo "Connected to $host:$port but unable to select database '{$server['name']}' while querying table '$table' ($dbhname)";
				return $this->bail("Connected to $host:$port but unable to select database '{$server['name']}' while querying table '$table' ($dbhname)");
			}
			if ( !empty($server['charset']) )
				$collation_query = "SET NAMES '{$server['charset']}'";
			elseif ( !empty($this->charset) )
				$collation_query = "SET NAMES '$this->charset'";
			if ( !empty($collation_query) && !empty($server['collate']) )
				$collation_query .= " COLLATE '{$server['collate']}'";
			if ( !empty($collation_query) && !empty($this->collation) )
				$collation_query .= " COLLATE '$this->collation'";

			if ( $this->use_mysqli ) {
				mysqli_query( $this->dbhs[$dbhname], $collation_query );
			} else {
				mysql_query( $collation_query );
			}

			$this->last_used_server = array( "server" => $server['host'], "db" => $server['name'] );

			$this->used_servers[$dbhname] = $this->last_used_server;

			// Close current and prevent future read-only connections to the written cluster
			if ( $write ) {

				if ( isset( $this->dbhs[$dbhname] ) && isset( $this->dbhs[$read_dbh] ) && $this->is_resource( $this->dbhs[$read_dbh] ) && $this->dbhs[$read_dbh] != $this->dbhs[$dbhname] ) {
					$this->disconnect( $read_dbh );
				}

				$this->dbhs[$read_dbh] = & $this->dbhs[$dbhname];

				$this->written_servers[$dbhname] = true;
			}

			while ( count($this->open_connections) > $this->max_connections ) {
				$oldest_connection = array_shift($this->open_connections);
				if ( ! isset( $this->dbhs[$oldest_connection] ) || ! isset( $this->dbhs[$dbhname] ) ) {
					continue;
				}

				if ( $this->dbhs[$oldest_connection] != $this->dbhs[$dbhname] ) {
					$this->disconnect($oldest_connection);
				}
			}
		}
		return $this->dbhs[$dbhname];
	}

	/**
	 * Is this a mysql resource
	 */
	function is_resource( $link ) {

		if ( ! $link ) {
			return false;
		}

		return $this->use_mysqli ? ( $link instanceof mysqli ) : is_resource( $link );

	}

	/**
	 * Ensure the database is connected before escaping
	 */
	function _real_escape( $string ) {

		if ( ! $this->dbh ) {
			$this->dbh = $this->db_connect( $string );
		}

		return parent::_real_escape( $string );

	}

	/**
	 * Disconnect and remove connection from open connections list
	 * @param string $dbhname
	 */
	function disconnect( $dbhname ) {

		$k = array_search( $dbhname, $this->open_connections );
		if ( isset( $this->open_connections[$k] ) ) {
			unset( $this->open_connections[$k] );
		}

		if ( $this->is_resource( $this->dbhs[$dbhname] ) ) {

			if ( $this->use_mysqli ) {
				mysqli_close($this->dbhs[$dbhname]);
			} else {
				mysql_close($this->dbhs[$dbhname]);
			}
		}

		unset( $this->dbhs[$dbhname] );
	}
	/**
	 * Basic query. See docs for more details.
	 * @param string $query
	 * @return int number of rows
	 */
	function query($query) {

		if ( preg_match( '/^\s*SELECT\s+FOUND_ROWS(\s*)/i', $query ) ) {
			$this->last_result = $this->last_found_rows_result;
			return 1;
		}

		$this->dbh = $this->db_connect( $query );

		if ( ! $this->is_resource( $this->dbh ) ) {
			return false;
		}

		$result = parent::query( $query );

		if ( preg_match('/^\s*SELECT\s+SQL_CALC_FOUND_ROWS\s/i', $query) ) {

			$_last_result = $this->last_result;
			parent::query( 'SELECT FOUND_ROWS()' );
			$this->last_found_rows_result = $this->last_result;
			++$this->num_queries;

			$this->last_result = $_last_result;

		}

		return $result;

	}
	/**
	 * Check the responsiveness of a tcp/ip daemon
	 * @return (bool) true when $host:$post responds within $float_timeout seconds, else (bool) false
	 */
	function check_tcp_responsiveness($host, $port, $float_timeout) {
		if ( 1 == 2 && function_exists('apc_store') ) {
			$use_apc = true;
			$apc_key = "{$host}{$port}";
			$apc_ttl = 10;
		} else {
			$use_apc = false;
		}
		if ( $use_apc ) {
			$cached_value=apc_fetch($apc_key);
			switch ( $cached_value ) {
				case 'up':
					$this->tcp_responsive = 'true';
					return true;
				case 'down':
					$this->tcp_responsive = 'false';
					return false;
			}
		}
	        $socket = fsockopen($host, $port, $errno, $errstr, $float_timeout);
	        if ( $socket === false ) {
			if ( $use_apc )
				apc_store($apc_key, 'down', $apc_ttl);
			$this->tcp_responsive = "false [ > $float_timeout] ($errno) '$errstr'";
	                return false;
		}
		fclose($socket);
		if ( $use_apc )
			apc_store($apc_key, 'up', $apc_ttl);
		$this->tcp_responsive = 'true';
	        return true;
	}

	function set_prefix( $prefix, $set_table_names = true ) {

		if ( preg_match( '|[^a-z0-9_]|i', $prefix ) )
			return new WP_Error('invalid_db_prefix', /*WP_I18N_DB_BAD_PREFIX*/'Invalid database prefix'/*/WP_I18N_DB_BAD_PREFIX*/);

		$old_prefix = '';
		if ( isset( $this->base_prefix ) )
			$old_prefix = $this->base_prefix;

		$this->base_prefix = $prefix;

		if ( $set_table_names ) {
			if( empty( $this->blogid ) )
				$scope = 'global';
			else
				$scope = 'all';

			$this->prefix = $this->get_blog_prefix();

			foreach ( $this->tables( $scope ) as $table => $prefixed_table )
				$this->$table = $prefixed_table;
		}
		return $old_prefix;
	}

	function set_blog_id( $blog_id, $site_id = 0 ) {
		if ( ! empty( $site_id ) )
			$this->siteid = $site_id;

		$old_blog_id  = $this->blogid;
		$this->blogid = $blog_id;

		$this->prefix = $this->get_blog_prefix();

		foreach ( $this->tables( 'site' ) as $table => $prefixed_table )
			$this->$table = $prefixed_table;

		return $old_blog_id;
	}
	/* WP 3.0 */
	function tables( $scope = 'all', $prefix = true, $blog_id = 0 ) {
		$key = $scope . '_tables';
		if( !isset( $this->$key ) )
			return array();

		$tables = $this->$key;

		if ( !$prefix )
			return $tables;

		if ( ! $blog_id )
			$blog_id = $this->blogid;

		$blog_prefix = $this->get_blog_prefix( $blog_id );
		$pre_tables = array();

		foreach ( $tables as $table ) {
			if ( in_array( $table, $this->global_tables ) )
				$pre_tables[ $table ] = $this->base_prefix . $table;
			else
				$pre_tables[ $table ] = $blog_prefix . $table;
		}

		if ( isset( $pre_tables['users'] ) ) {
			if( defined( 'CUSTOM_USER_TABLE' ) )
				$pre_tables['users'] = CUSTOM_USER_TABLE;
			if ( defined( 'CUSTOM_USER_META_TABLE' ) )
				$pre_tables['usermeta'] = CUSTOM_USER_META_TABLE;
		}
		return $pre_tables;
	}
} // class SharDB

$wpdb = new SharDB(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

if( !class_exists( 'BPDB' ) ) :

class BPDB extends SharDB {

	function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {
		parent::__construct( $dbuser, $dbpassword, $dbname, $dbhost );

		$args = func_get_args();
		$args = call_user_func_array( array( &$this, 'init' ), $args );

		if ( $args['host'] )
			$this->db_servers['dbh_global'] = $args;
	}

	/**
	 * Determine if a database supports a particular feature.
	 *
	 * Overriden here to work around differences between bbPress', and WordPress', implementation differences.
	 * In particular, when BuddyPress tries to run bbPress' SQL installation script, the collation check always
	 * failed. The capability is long supported by WordPress' minimum required MySQL version, so this is safe.
	 */
	function has_cap( $db_cap, $_table_name='' ) {
		if ( 'collation' == $db_cap )
			return true;

		return parent::has_cap( $db_cap );
	}

	/**
	 * Initialises the class variables based on provided arguments.
	 * Based on, and taken from, the BackPress class in turn taken from the 1.0 branch of bbPress.
	 */
	function init( $args ) {
		if ( 4 == func_num_args() ) {
			$args = array(
				'user'     => $args,
				'password' => func_get_arg( 1 ),
				'name'     => func_get_arg( 2 ),
				'host'     => func_get_arg( 3 ),
				'charset'  => defined( 'BBDB_CHARSET' ) ? BBDB_CHARSET : false,
				'collate'  => defined( 'BBDB_COLLATE' ) ? BBDB_COLLATE : false,
			);
		}

		$defaults = array(
			'user'     => false,
			'password' => false,
			'name'     => false,
			'host'     => 'localhost',
			'charset'  => false,
			'collate'  => false,
			'errors'   => false
		);

		return wp_parse_args( $args, $defaults );
	}

	function escape_deep( $data ) {
		return $this->escape( $data );
	}
} // class BPDB

endif; // !class_exists( 'BPDB' )
endif; // is_multisite()
