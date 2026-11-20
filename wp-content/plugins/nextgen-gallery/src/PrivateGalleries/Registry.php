<?php
/**
 * Cached list of the private galleries that have a client page.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

use Imagely\NGG\Util\Transient;

/**
 * One autoloaded option listing every set up private gallery: its page carries the client mark,
 * or the page is gone (still hidden, shown as "Page missing").
 */
class Registry {

	/**
	 * Option holding the map.
	 *
	 * @var string
	 */
	const OPTION = 'ngg_private_galleries';

	/**
	 * Post meta key marking a page as a client page. One row per gallery id.
	 *
	 * @var string
	 */
	const PAGE_MARK = '_ngg_client_gallery';

	/**
	 * Request cache of the option.
	 *
	 * @var array<int,array{p:int,a:int,x:array}>|null
	 */
	private static $map = null;

	/**
	 * Every set up private gallery.
	 *
	 * @return array<int,array{p:int,a:int}> Gallery id => [ 'p' => page id, 'a' => author id ].
	 */
	public static function get_all() {
		if ( null === self::$map ) {
			$stored    = get_option( self::OPTION, [] );
			self::$map = [];

			foreach ( is_array( $stored ) ? $stored : [] as $gid => $row ) {
				if ( is_array( $row ) && isset( $row['p'] ) ) {
					self::$map[ (int) $gid ] = [
						'p' => (int) $row['p'],
						'a' => isset( $row['a'] ) ? (int) $row['a'] : 0,
						'x' => isset( $row['x'] ) && is_array( $row['x'] ) ? $row['x'] : [],
					];
				}
			}
		}

		return self::$map;
	}

	/**
	 * Whether a gallery is a set up private gallery.
	 *
	 * @param int|string $gallery_id Gallery id.
	 * @return bool
	 */
	public static function is_private( $gallery_id ) {
		$map = self::get_all();
		return isset( $map[ (int) $gallery_id ] );
	}

	/**
	 * The client page of a set up private gallery.
	 *
	 * @param int|string $gallery_id Gallery id.
	 * @return int 0 when the gallery is not a set up private gallery.
	 */
	public static function page_for( $gallery_id ) {
		$map = self::get_all();
		return isset( $map[ (int) $gallery_id ] ) ? $map[ (int) $gallery_id ]['p'] : 0;
	}

	/**
	 * The author of a set up private gallery.
	 *
	 * @param int|string $gallery_id Gallery id.
	 * @return int
	 */
	public static function author_of( $gallery_id ) {
		$map = self::get_all();
		return isset( $map[ (int) $gallery_id ] ) ? $map[ (int) $gallery_id ]['a'] : 0;
	}

	/**
	 * Extra data an add-on stored for a private gallery (see ngg_private_galleries_map).
	 *
	 * @param int|string $gallery_id Gallery id.
	 * @return array
	 */
	public static function extra( $gallery_id ) {
		$map = self::get_all();
		return isset( $map[ (int) $gallery_id ] ) ? $map[ (int) $gallery_id ]['x'] : [];
	}

	/**
	 * The private galleries whose client page is the given page.
	 *
	 * @param int $page_id Page id.
	 * @return int[]
	 */
	public static function galleries_for_page( $page_id ) {
		$page_id = (int) $page_id;
		$retval  = [];

		if ( $page_id <= 0 ) {
			return $retval;
		}

		foreach ( self::get_all() as $gid => $row ) {
			if ( $row['p'] === $page_id ) {
				$retval[] = (int) $gid;
			}
		}

		return $retval;
	}

	/**
	 * Every client page id.
	 *
	 * @return int[]
	 */
	public static function page_ids() {
		$ids = [];

		foreach ( self::get_all() as $row ) {
			$ids[ $row['p'] ] = $row['p'];
		}

		return array_values( $ids );
	}

	/**
	 * Whether a page is the client page of at least one private gallery.
	 *
	 * @param int $page_id Page id.
	 * @return bool
	 */
	public static function is_client_page( $page_id ) {
		return in_array( (int) $page_id, self::page_ids(), true );
	}

	/**
	 * Rebuilds the option from the gallery table and the page marks.
	 *
	 * @return array<int,array{p:int,a:int}> The new map.
	 */
	public static function rebuild() {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- This query is what fills the cache.
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT g.gid, g.pageid, g.author FROM {$wpdb->nggallery} g
				LEFT JOIN {$wpdb->posts} p ON p.ID = g.pageid
				WHERE g.is_private = 1 AND g.pageid > 0
				AND ( p.ID IS NULL OR EXISTS (
					SELECT 1 FROM {$wpdb->postmeta} m
					WHERE m.post_id = g.pageid AND m.meta_key = %s AND m.meta_value = CAST( g.gid AS CHAR )
				) )",
				self::PAGE_MARK
			)
		);

		// A failed read is not "no private galleries": keep the stored list instead of unhiding them all.
		if ( ! is_array( $rows ) || ! $wpdb->ready || ! empty( $wpdb->last_error ) ) {
			return self::get_all();
		}

		$map = [];

		foreach ( (array) $rows as $row ) {
			$map[ (int) $row->gid ] = [
				'p' => (int) $row->pageid,
				'a' => (int) $row->author,
				'x' => [],
			];
		}

		/**
		 * Filters the private gallery list before it is cached. Add-ons store their per-gallery
		 * data under 'x' so reading it costs no query.
		 *
		 * @param array $map Gallery id => [ 'p' => page id, 'a' => author id, 'x' => extra ].
		 */
		$map = (array) apply_filters( 'ngg_private_galleries_map', $map );

		ksort( $map );

		$previous  = self::get_all();
		self::$map = $map;

		if ( $previous !== $map || false === get_option( self::OPTION, false ) ) {
			update_option( self::OPTION, $map, true );

			// Rendered output and the galleries table both depend on who is private.
			Transient::flush( 'displayed_gallery_rendering' );
			Transient::flush( 'rest_galleries' );

			/**
			 * Fires after the set of private galleries changed.
			 *
			 * @param array $map      Gallery id => [ 'p' => page id, 'a' => author id ].
			 * @param array $previous The previous map.
			 */
			do_action( 'ngg_private_galleries_changed', $map, $previous );
		}

		return $map;
	}

	/**
	 * Forgets the request cache (tests, and after another process changed the option).
	 *
	 * @return void
	 */
	public static function reset() {
		self::$map = null;
	}
}
