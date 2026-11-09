<?php

namespace Imagely\NGG\Util;

/**
 * Manages NextGEN transients and grouping of transients.
 */
class Transient {

	/**
	 * Groups array.
	 *
	 * @var array
	 */
	private $_groups = [];

	/**
	 * Instance cache.
	 *
	 * @var Transient|null
	 */
	private static $_instance = null;

	/**
	 * Gets the singleton instance.
	 *
	 * @return Transient
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new Transient();
		}
		return self::$_instance;
	}

	public function __construct() {
		$this->_groups = get_option( 'ngg_transient_groups', [ '__counter' => 1 ] );
	}

	public function add_group( $group_or_groups ) {
		$updated = false;
		$groups  = is_array( $group_or_groups ) ? $group_or_groups : [ $group_or_groups ];

		// Initialize the groups array if it doesn't exist or is not an array.
		// If the 'ngg_transient_groups' option is set and is not an array, this could cause fatal error.
		if ( ! is_array( $this->_groups ) ) {
			$this->_groups = [];
		}

		// Ensure the counter exists; the stored option may have been reset or cleared
		// (e.g. after manually deleting transients) without the default counter key.
		if ( ! isset( $this->_groups['__counter'] ) ) {
			$this->_groups['__counter'] = 1;
		}

		foreach ( $groups as $group ) {
			if ( ! isset( $this->_groups[ $group ] ) ) {
				$id                      = $this->_groups['__counter'] += 1;
				$this->_groups[ $group ] = [
					'id'      => $id,
					'enabled' => true,
				];
				$updated                 = true;
			}
		}

		if ( $updated ) {
			update_option( 'ngg_transient_groups', $this->_groups );
		}
	}

	public function get_group_id( $group_name ) {
		$this->add_group( $group_name );

		return $this->_groups[ $group_name ]['id'];
	}

	public function generate_key( $group, $params = [] ) {
		if ( is_object( $params ) ) {
			$params = (array) $params;
		}

		if ( is_array( $params ) ) {
			foreach ( $params as &$param ) {
				// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
				$param = @wp_json_encode( $param );
			}
			unset( $param );

			// Length-prefix each element before joining so two different parameter sets cannot
			// serialise to the same string: a plain implode( '' ) made ['ab','c'] and ['a','bc']
			// indistinguishable, letting unrelated galleries share one cache slot.
			$params = implode(
				'',
				array_map(
					static function ( $param ) {
						return strlen( (string) $param ) . ':' . $param;
					},
					$params
				)
			);
		}

		global $_wp_using_ext_object_cache;

		$group_id = $this->get_group_id( $group );

		// Derive the key from a cryptographic digest, not crc32(). CRC-32 is a linear
		// error-detecting code over GF(2), not a hash: for any target key, four chosen suffix
		// bytes can be solved for directly, so a Contributor could craft a gallery whose key
		// collides with any other gallery's and poison the shared, visitor-facing render cache.
		// sha1() truncated to 32 hex characters keeps keys short while making collisions infeasible.
		$digest = substr( sha1( (string) $params ), 0, 32 );

		// Object-cache sites can't be invalidated via clear()'s wp_options DELETE, so embed a
		// per-group version in the key; bumping it (see clear()) orphans all prior keys.
		if ( $_wp_using_ext_object_cache ) {
			return $group_id . '_v' . $this->get_group_version( $group_id ) . '__' . $digest;
		}

		return $group_id . '__' . $digest;
	}

	/**
	 * Current invalidation version for a group (object-cache sites only). Defaults to 1 when the
	 * counter has not been stored yet or was evicted.
	 *
	 * @param int $group_id Numeric group id from get_group_id().
	 * @return int
	 */
	private function get_group_version( $group_id ) {
		$version = wp_cache_get( 'ngg_cache_ver_' . $group_id, 'ngg' );

		return ( false !== $version ) ? (int) $version : 1;
	}

	/**
	 * Bumps a group's invalidation version, orphaning every key built against the previous one.
	 * Both branches are atomic: wp_cache_add() only seeds the counter when it is absent (cold
	 * start, jumping to v2 so implicit-v1 keys are invalidated), and wp_cache_incr() is atomic.
	 * This avoids the lost-update race a check-then-set (get + set) would have under concurrent
	 * flushes, where a stale process could clobber a newer version back to the seed value.
	 *
	 * @param int $group_id Numeric group id from get_group_id().
	 * @return void
	 */
	private function bump_group_version( $group_id ) {
		$cache_key = 'ngg_cache_ver_' . $group_id;

		// wp_cache_add() fails when the counter already exists, in which case another request has
		// seeded it and we increment instead.
		if ( ! wp_cache_add( $cache_key, 2, 'ngg' ) ) {
			wp_cache_incr( $cache_key, 1, 'ngg' );
		}
	}

	public function get( $key, $default_value = null, $lookup = null ) {
		$retval = $default_value;

		if ( is_null( $lookup ) && defined( 'PHOTOCRATI_CACHE' ) ) {
			$lookup = PHOTOCRATI_CACHE;
		}

		if ( $lookup ) {
			$retval = json_decode( get_transient( $key ) );
			if ( is_object( $retval ) ) {
				$retval = (array) $retval;
			}
			if ( is_null( $retval ) ) {
				$retval = $default_value;
			}
		}

		return $retval;
	}

	public function set( $key, $value, $ttl = 0 ) {
		$retval  = false;
		$enabled = true;

		if ( defined( 'PHOTOCRATI_CACHE' ) ) {
			$enabled = PHOTOCRATI_CACHE;
		}
		if ( defined( 'PHOTOCRATI_CACHE_TTL' )
			&& ! $ttl ) {
			$ttl = PHOTOCRATI_CACHE_TTL;
		}

		if ( $enabled ) {
			$retval = set_transient( $key, wp_json_encode( $value ), $ttl );
		}

		return $retval;
	}

	public function delete( $key ) {
		return delete_transient( $key );
	}

	/**
	 * Clears all (or only expired) transients managed by this utility
	 *
	 * @param string $group Group name to purge
	 * @param bool   $expired Whether to clear all transients (FALSE) or to clear expired transients (TRUE)
	 */
	public function clear( $group = null, $expired = false ) {
		if ( $group === '__counter' ) {
			return;
		}

		// No (or empty) group means "every known group"; clear each individually.
		if ( ! is_string( $group ) || empty( $group ) ) {
			foreach ( $this->_groups as $name => $params ) {
				$this->clear( $name, $expired );
			}
			return;
		}

		global $_wp_using_ext_object_cache;

		// Object-cache sites: transients aren't in wp_options, so bump the group version instead
		// of the SQL DELETE below. flush_expired() is skipped — the backend evicts on its own TTL.
		if ( $_wp_using_ext_object_cache ) {
			if ( ! $expired ) {
				$this->bump_group_version( $this->get_group_id( $group ) );
			}
			return;
		}

		if ( is_string( $group ) && ! empty( $group ) ) {
			global $wpdb;

			// A little query building is necessary here..
			// Clear transients for "the" site or for the current multisite instance.
			$expired_sql = '';
			$params      = [
				$wpdb->esc_like( '_transient_' ) . '%',
				'%' . $wpdb->esc_like( "{$this->get_group_id($group)}__" ) . '%',
				$wpdb->esc_like( '_transient_timeout_' ) . '%',
			];
			if ( $expired ) {
				$params[]    = time();
				$expired_sql = $expired ? 'AND b.option_value < %d' : '';
			}

			$sql = "DELETE a, b
                    FROM {$wpdb->options} a, {$wpdb->options} b
                    WHERE a.option_name LIKE %s
                    AND a.option_name LIKE %s
                    AND a.option_name NOT LIKE %s
                    AND b.option_name = CONCAT('_transient_timeout_', SUBSTRING(a.option_name, 12))
                    {$expired_sql}";

			// This is a false positive.
			//
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
			$wpdb->query( $wpdb->prepare( $sql, $params ) );

			// Clear transients for the main site of a multisite network.
			if ( is_main_site() && is_main_network() ) {
				$expired_sql = '';
				$params      = [
					$wpdb->esc_like( '_site_transient_' ) . '%',
					'%' . $wpdb->esc_like( "{$this->get_group_id($group)}__" ) . '%',
					$wpdb->esc_like( '_site_transient_timeout_' ) . '%',
				];
				if ( $expired ) {
					$params[]    = time();
					$expired_sql = $expired ? 'AND b.option_value < %d' : '';
				}
				$sql = "DELETE a, b
                        FROM {$wpdb->options} a, {$wpdb->options} b
                        WHERE a.option_name LIKE %s
                        AND a.option_name LIKE %s
                        AND a.option_name NOT LIKE %s
                        AND b.option_name = CONCAT('_site_transient_timeout_', SUBSTRING(a.option_name, 17))
                        {$expired_sql}";

				// This is a false positive.
				//
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->query( $wpdb->prepare( $sql, $params ) );
			}
		}
	}

	public static function update( $key, $value, $ttl = null ) {
		return self::get_instance()->set( $key, $value, $ttl );
	}

	public static function fetch( $key, $default_value = null ) {
		return self::get_instance()->get( $key, $default_value );
	}

	public static function flush( $group = null ) {
		self::get_instance()->clear( $group );
	}

	public static function flush_expired( $group = null ) {
		self::get_instance()->clear( $group, true );
	}

	public static function create_key( $group, $params = [] ) {
		return self::get_instance()->generate_key( $group, $params );
	}
}
