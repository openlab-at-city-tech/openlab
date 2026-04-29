<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\Util\Transient;

/**
 * WP-CLI Cache management commands for NextGEN Gallery.
 *
 * Provides cache-related WP-CLI commands for NextGEN Gallery.
 */
class Cache {

	/**
	 * Flushes NextGen Gallery caches
	 *
	 * @param array $args       Command arguments.
	 * @param array $assoc_args Associative arguments.
	 * @synopsis [--expired]
	 */
	public function flush( $args, $assoc_args ) {
		$expired = ! empty( $assoc_args['expired'] ) ? true : false;
		Transient::flush( $expired );
		\WP_CLI::success( 'Flushed caches' );
	}
}
