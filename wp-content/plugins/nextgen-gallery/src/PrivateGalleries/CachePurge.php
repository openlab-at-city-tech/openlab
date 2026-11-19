<?php
/**
 * Clears page caches when a gallery becomes private or public.
 *
 * @package Imagely\NGG\PrivateGalleries
 */

namespace Imagely\NGG\PrivateGalleries;

/**
 * Pages cached while a gallery was public would keep showing it, so the common page cache plugins
 * are flushed whenever the set of private galleries or their pages changes.
 */
class CachePurge {

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public static function register_hooks() {
		add_action( 'ngg_private_galleries_changed', [ self::class, 'maybe_purge' ], 10, 2 );
	}

	/**
	 * Flushes page caches when galleries or client pages were added or removed.
	 *
	 * @param array $map      New private gallery list.
	 * @param array $previous Previous list.
	 * @return void
	 */
	public static function maybe_purge( $map, $previous ) {
		if ( self::signature( (array) $map ) === self::signature( (array) $previous ) ) {
			return;
		}

		self::purge();
	}

	/**
	 * Flushes the page caches this site runs.
	 *
	 * @return void
	 */
	public static function purge() {
		if ( function_exists( 'w3tc_flush_all' ) ) {
			w3tc_flush_all();
		}

		if ( function_exists( 'rocket_clean_domain' ) ) {
			rocket_clean_domain();
		}

		if ( function_exists( 'wp_cache_clear_cache' ) ) {
			wp_cache_clear_cache();
		}

		// LiteSpeed Cache and WP Fastest Cache listen for these.
		do_action( 'litespeed_purge_all' );
		do_action( 'wpfc_clear_all_cache' );

		/**
		 * Fires after the page caches were asked to flush (for other caches, CDNs or a host cache).
		 */
		do_action( 'ngg_private_galleries_purge_caches' );
	}

	/**
	 * Gallery ids and their pages, without the per-gallery extras.
	 *
	 * @param array $map Private gallery list.
	 * @return array
	 */
	private static function signature( array $map ) {
		$signature = [];

		foreach ( $map as $gallery_id => $row ) {
			$signature[ (int) $gallery_id ] = isset( $row['p'] ) ? (int) $row['p'] : 0;
		}

		ksort( $signature );

		return $signature;
	}
}
