<?php

namespace Imagely\NGG\WPCLI;

use Imagely\NGG\Util\Transient;

class Cache {

	/**
	 * Flushes NextGen Gallery caches
	 *
	 * @param array $args
	 * @param array $assoc_args
	 * @synopsis [--expired]
	 */
	public function flush( $args, $assoc_args ) {
		$expired = ! empty( $assoc_args['expired'] ) ? true : false;
		Transient::flush( $expired );
		\WP_CLI::success( 'Flushed caches' );
	}
}
