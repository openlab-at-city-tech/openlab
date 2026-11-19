<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains all WordPress related URL, URI, path, slug, etc. related helper methods.
 *
 * @since 1.0.0
 */
trait WpUri {
	/**
	 * Returns the site domain.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $unfiltered Whether to get the unfiltered value.
	 * @return string             The site's domain.
	 */
	public function getSiteDomain( $unfiltered = false ) {
		return wp_parse_url( $this->getHomeUrl( $unfiltered ), PHP_URL_HOST );
	}

	/**
	 * Returns the site URL.
	 * NOTE: For multisites inside a sub-directory, this returns the URL for the main site.
	 * This is intentional.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $unfiltered Whether to get the unfiltered value.
	 * @return string             The site's domain.
	 */
	public function getSiteUrl( $unfiltered = false ) {
		$homeUrl = $this->getHomeUrl( $unfiltered );

		return wp_parse_url( $homeUrl, PHP_URL_SCHEME ) . '://' . wp_parse_url( $homeUrl, PHP_URL_HOST );
	}

	/**
	 * Retrieve the home path.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $unfiltered Whether to get the unfiltered value.
	 * @return string             The home path.
	 */
	public function getHomePath( $unfiltered = false ) {
		$path = wp_parse_url( $this->getHomeUrl( $unfiltered ), PHP_URL_PATH );

		return $path ? trailingslashit( $path ) : '/';
	}

	/**
	 * Returns the home URL.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool   $unfiltered Whether to get the unfiltered value.
	 * @return string             The home URL.
	 */
	private function getHomeUrl( $unfiltered = false ) {
		$homeUrl = home_url();
		if ( $unfiltered ) {
			// We want to get this value straight from the DB to prevent plugins like WPML from filtering it.
			// This will otherwise mess with things like license activation requests and redirects.
			$homeUrl = get_option( 'home' );
		}

		return $homeUrl;
	}
}