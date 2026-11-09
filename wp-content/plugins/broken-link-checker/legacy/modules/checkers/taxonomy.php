<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/*
Plugin Name: Taxonomy checker
Description: Checks whether taxonomies referenced by a post's terms are still registered.
Version: 1.0
Author: Andrey @ WPMU DEV

ModuleID: taxonomy_checker
ModuleCategory: checker
ModuleContext: on-demand
ModuleLazyInit: true
ModuleClassName: blcTaxonomyChecker
ModulePriority: -1
*/

/**
 * Checks whether a taxonomy referenced by a post's terms is still registered.
 *
 * The "URL" used to represent a taxonomy is taxonomy://taxonomy_slug.
 * A taxonomy is considered "broken" (invalid) when taxonomy_exists() returns false,
 * which happens if the taxonomy was once registered but is no longer — e.g. because
 * the plugin or theme that registered it was deactivated.
 *
 * @package Broken Link Checker
 * @access public
 */
class blcTaxonomyChecker extends blcChecker {

	/**
	 * Returns true for any URL using the taxonomy:// pseudo-scheme.
	 *
	 * @param string     $url        The URL to check.
	 * @param array|bool $parsed_url The result of wp_parse_url() on the URL, or false if the URL is malformed and couldn't be parsed.
	 * @return bool
	 */
	public function can_check( $url, $parsed_url ) {
		return isset( $parsed_url['scheme'] ) && 'taxonomy' === $parsed_url['scheme'];
	}

	/**
	 * Check whether the taxonomy embedded in the pseudo-URL is still registered.
	 *
	 * The pseudo-URL format is:  taxonomy://<taxonomy_slug>
	 *
	 * @param string $url A taxonomy:// pseudo-URL.
	 * @return array {
	 *     @type bool   $broken      True when the taxonomy is not registered.
	 *     @type int    $http_code   200 if valid, 404 if not registered.
	 *     @type string $log         Human-readable explanation of the result.
	 *     @type string $result_hash Stable hash that identifies the result type.
	 * }
	 */
	public function check( $url ) {
		// Extract the taxonomy slug from the pseudo-URL.
		// wp_parse_url() on 'taxonomy://category' gives host = 'category'.
		$parsed   = wp_parse_url( $url );
		$taxonomy = isset( $parsed['host'] ) ? urldecode( $parsed['host'] ) : '';

		if ( '' === $taxonomy ) {
			return array(
				'broken'      => true,
				'http_code'   => 0,
				'log'         => __( 'Could not determine taxonomy slug from the stored URL.', 'broken-link-checker' ),
				'result_hash' => 'taxonomy_empty_slug',
			);
		}

		if ( taxonomy_exists( $taxonomy ) ) {
			return array(
				'broken'      => false,
				'http_code'   => 200,
				/* translators: %s: taxonomy slug */
				'log'         => sprintf( __( "Taxonomy '%s' is registered.", 'broken-link-checker' ), $taxonomy ),
				'result_hash' => 'taxonomy_valid|' . $taxonomy,
			);
		}

		return array(
			'broken'      => true,
			'http_code'   => 404,
			/* translators: %s: taxonomy slug */
			'log'         => sprintf( __( "Taxonomy '%s' is not registered. The term relationship may be orphaned.", 'broken-link-checker' ), $taxonomy ),
			'result_hash' => 'taxonomy_invalid|' . $taxonomy,
		);
	}
}
