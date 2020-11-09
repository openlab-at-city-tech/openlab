<?php

namespace Advanced_Sidebar_Menu\Traits;

/**
 * Support simple memoization for class methods which respond
 * with different caches based on the arguments provided.
 *
 * @since   8.0.1
 */
trait Memoize {
	/**
	 * Store results of the memoize methods.
	 *
	 * @var array
	 */
	protected $memoize_cache = [];


	/**
	 * Pass me a callback, a method identifier, and some optional arguments and
	 * and I will return the same result every time.
	 *
	 * The passed function will only be called once no matter where it called from
	 * and what the arguments are.
	 * I will always return the value received from the callback on its first run.
	 *
	 * @param callable $fn         - Callback.
	 * @param string   $identifier - Something unique to identify the the method being used
	 *                             so we can determine the difference in the cache.
	 *                             `__METHOD__` works nicely here.
	 * @param mixed    $args       - Arguments will be passed to the callback..
	 *
	 * @return mixed
	 */
	public function once( callable $fn, $identifier, $args ) {
		if ( ! array_key_exists( "{$identifier}::once", $this->memoize_cache ) ) {
			$this->memoize_cache[ "{$identifier}::once" ] = $fn( $args );
		}

		return $this->memoize_cache[ "{$identifier}::once" ];
	}


	/**
	 * Pass me a callback, a method identifier, and some arguments and
	 * I will return the same result every time the arguments are the same.
	 *
	 * If the arguments change, I will return a result matching the change.
	 * I will only call the callback one time for the same set of arguments.
	 *
	 * @param callable $fn         - Callback.
	 * @param string   $identifier - Something unique to identify the the method being used
	 *                             so we can determine the difference in the cache.
	 *                             `__METHOD__` works nicely here.
	 * @param mixed    $args       - Arguments will be passed to the callback as well as determine
	 *                             if we can reuse a result.
	 *
	 * @return mixed
	 */
	public function memoize( callable $fn, $identifier, $args ) {
		//phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize
		$key = md5( serialize( [ $args, $identifier ] ) );
		if ( ! array_key_exists( $key, $this->memoize_cache ) ) {
			$this->memoize_cache[ $key ] = $fn( $args );
		}

		return $this->memoize_cache[ $key ];
	}


	/**
	 * Clear all caches on this class.
	 * Typically used during unit testing.
	 *
	 * @return void
	 */
	public function clear_memoize_cache() {
		$this->memoize_cache = [];
	}
}
