<?php
namespace AIOSEO\TableOfContents\Core;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles our network cache.
 *
 * @since 1.0.0
 */
class NetworkCache extends Cache {
	/**
	 * Returns the cache value for a key if it exists and is not expired.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The cache key name. Use a '%' for a like query.
	 * @return mixed       The value or null if the cache does not exist.
	 */
	public function get( $key ) {
		if ( ! is_multisite() ) {
			return parent::get( $key );
		}

		aioseoTableOfContents()->helpers->switchToBlog( aioseoTableOfContents()->helpers->getNetworkId() );
		$value = parent::get( $key );
		aioseoTableOfContents()->helpers->restoreCurrentBlog();

		return $value;
	}

	/**
	 * Updates the given cache or creates it if it doesn't exist.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key        The cache key name.
	 * @param  mixed  $value      The value.
	 * @param  int    $expiration The expiration time in seconds. Defaults to 24 hours. 0 to no expiration.
	 * @return void
	 */
	public function update( $key, $value, $expiration = DAY_IN_SECONDS ) {
		if ( ! is_multisite() ) {
			parent::update( $key, $value, $expiration );

			return;
		}

		aioseoTableOfContents()->helpers->switchToBlog( aioseoTableOfContents()->helpers->getNetworkId() );
		parent::update( $key, $value, $expiration );
		aioseoTableOfContents()->helpers->restoreCurrentBlog();
	}

	/**
	 * Deletes the given cache key.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The cache key.
	 * @return void
	 */
	public function delete( $key ) {
		if ( ! is_multisite() ) {
			parent::delete( $key );

			return;
		}

		aioseoTableOfContents()->helpers->switchToBlog( aioseoTableOfContents()->helpers->getNetworkId() );
		parent::delete( $key );
		aioseoTableOfContents()->helpers->restoreCurrentBlog();
	}

	/**
	 * Clears all of our cache.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function clear() {
		if ( ! is_multisite() ) {
			parent::clear();

			return;
		}

		aioseoTableOfContents()->helpers->switchToBlog( aioseoTableOfContents()->helpers->getNetworkId() );
		parent::clear();
		aioseoTableOfContents()->helpers->restoreCurrentBlog();
	}

	/**
	 * Clears all of our cache under a certain prefix.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $prefix A prefix to clear or empty to clear everything.
	 * @return void
	 */
	public function clearPrefix( $prefix ) {
		if ( ! is_multisite() ) {
			parent::clearPrefix( $prefix );

			return;
		}

		aioseoTableOfContents()->helpers->switchToBlog( aioseoTableOfContents()->helpers->getNetworkId() );
		parent::clearPrefix( $prefix );
		aioseoTableOfContents()->helpers->restoreCurrentBlog();
	}
}