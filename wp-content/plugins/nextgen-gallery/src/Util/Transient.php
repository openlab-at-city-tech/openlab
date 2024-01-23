<?php

namespace Imagely\NGG\Util;

/**
 * Manages NextGEN transients and grouping of transients.
 */
class Transient {

	private $_groups          = [];
	private static $_instance = null;

	protected $_tracker;

	/**
	 * @return Transient
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new Transient();
		}
		return self::$_instance;
	}

	public function __construct() {
		global $_wp_using_ext_object_cache;

		$this->_groups = get_option( 'ngg_transient_groups', [ '__counter' => 1 ] );
		if ( $_wp_using_ext_object_cache ) {
			$this->_tracker = get_option( 'photocrati_cache_tracker', [] );
		}

		register_shutdown_function( [ $this, '_update_tracker' ] );
	}

	public function delete_tracked( $group = null ) {
		global $_wp_using_ext_object_cache;

		if ( $_wp_using_ext_object_cache ) {
			if ( $group ) {
				if ( is_array( $this->_tracker ) && isset( $this->_tracker[ $this->get_group_id( $group ) ] ) ) {
					foreach ( $this->_tracker[ $this->get_group_id( $group ) ] as $key ) {
						delete_transient( $this->get_group_id( $group ) . '__' . $key );
					}

					unset( $this->_tracker[ $this->get_group_id( $group ) ] );
				}
			} else {
				foreach ( $this->_groups as $group => $data ) {
					$this->delete_tracked( $group );
				}
			}
		}
	}

	/**
	 * Despite the underscore prefix this cannot be marked protected: it is used by register_shutdown_function()
	 */
	public function _update_tracker() {
		global $_wp_using_ext_object_cache;

		if ( $_wp_using_ext_object_cache ) {
			$current_value = get_option( 'photocrati_cache_tracker', [] );
			if ( $current_value !== $this->_tracker ) {
				update_option( 'photocrati_cache_tracker', $this->_tracker, 'no' );
			}
		}
	}

	public function add_group( $group_or_groups ) {
		$updated = false;
		$groups  = is_array( $group_or_groups ) ? $group_or_groups : [ $group_or_groups ];

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
				$param = @json_encode( $param );
			}
			$params = implode( '', $params );
		}

		return $this->get_group_id( $group ) . '__' . str_replace( '-', '_', crc32( $params ) );
	}

	public function get( $key, $default = null, $lookup = null ) {
		$retval = $default;

		if ( is_null( $lookup ) && defined( 'PHOTOCRATI_CACHE' ) ) {
			$lookup = PHOTOCRATI_CACHE;
		}

		if ( $lookup ) {
			$retval = json_decode( get_transient( $key ) );
			if ( is_object( $retval ) ) {
				$retval = (array) $retval;
			}
			if ( is_null( $retval ) ) {
				$retval = $default;
			}
		}

		return $retval;
	}

	protected function _track_key( $key ) {
		global $_wp_using_ext_object_cache;

		if ( $_wp_using_ext_object_cache ) {
			$parts = explode( '__', $key );
			$group = $parts[0];
			$id    = $parts[1];
			if ( ! isset( $this->_tracker[ $group ] ) ) {
				$this->_tracker[ $group ] = [];
			}
			if ( ! in_array( $id, $this->_tracker[ $group ] ) ) {
				$this->_tracker[ $group ][] = $id;
			}
		}
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
			$retval = set_transient( $key, json_encode( $value ), $ttl );
			if ( $retval ) {
				$this->_track_key( $key );
			}
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
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
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
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
				$wpdb->query( $wpdb->prepare( $sql, $params ) );
			}

			if ( $expired ) {
				$this->delete_tracked( $group );
			}
		} else {
			foreach ( $this->_groups as $name => $params ) {
				$this->clear( $name, $expired );
			}
		}
	}

	public static function update( $key, $value, $ttl = null ) {
		return self::get_instance()->set( $key, $value, $ttl );
	}

	public static function fetch( $key, $default = null ) {
		return self::get_instance()->get( $key, $default );
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
