<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal;

use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;

/**
 * Routes a slug to the Herald URL variant that covers it.
 *
 * This is the implementation bound to `Download_Url_Builder` in the container.
 * It owns the policy decision of "legacy first, fall back to Unified" without
 * either concrete builder having to know the other exists. Each builder
 * remains a pure per-backend implementation of the contract; this class is
 * the only one aware that multiple Herald variants coexist.
 *
 * @since 1.3.0
 */
final class Herald_Routing_Url_Builder implements Download_Url_Builder {

	/**
	 * The Unified Herald URL builder, used when no legacy URL is produced.
	 *
	 * @since 1.3.0
	 *
	 * @var Download_Url_Builder
	 */
	private Download_Url_Builder $unified;

	/**
	 * The legacy Herald URL builder, tried first.
	 *
	 * @since 1.3.0
	 *
	 * @var Download_Url_Builder
	 */
	private Download_Url_Builder $legacy;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param Herald_Url_Builder        $unified The Unified Herald URL builder.
	 * @param Herald_Legacy_Url_Builder $legacy  The legacy Herald URL builder.
	 */
	public function __construct( Herald_Url_Builder $unified, Herald_Legacy_Url_Builder $legacy ) {
		$this->unified = $unified;
		$this->legacy  = $legacy;
	}

	/**
	 * Routes the slug to the legacy builder first, falling back to Unified.
	 *
	 * @since 1.3.0
	 *
	 * @param string $slug The catalog feature slug.
	 *
	 * @return string The first non-empty URL produced by a held builder, or an empty string.
	 */
	public function build( string $slug ): string {
		$url = $this->legacy->build( $slug );

		if ( $url !== '' ) {
			return $url;
		}

		return $this->unified->build( $slug );
	}
}
