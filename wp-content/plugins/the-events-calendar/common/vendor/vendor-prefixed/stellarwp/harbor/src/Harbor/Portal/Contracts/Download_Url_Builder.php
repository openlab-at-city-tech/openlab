<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Contracts;

/**
 * Contract for building download URLs for catalog features.
 *
 * Implementations encapsulate how a feature slug is turned into an
 * authenticated download URL. This allows the download backend to be
 * swapped (e.g. Herald, a future replacement, or a test double) without
 * changing consumers.
 *
 * @since 1.0.0
 */
interface Download_Url_Builder {

	/**
	 * Builds a download URL for the given feature slug.
	 *
	 * Implementations should return an empty string when the URL cannot
	 * be constructed (e.g. missing license key or domain).
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The catalog feature slug.
	 *
	 * @return string
	 */
	public function build( string $slug ): string;
}
