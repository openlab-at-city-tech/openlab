<?php
namespace AIOSEO\TableOfContents\Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\TableOfContents\Traits\Helpers as TraitHelpers;

/**
 * Contains helper functions
 *
 * @since 1.0.0
 */
class Helpers {
	use TraitHelpers\Api;
	use TraitHelpers\Arrays;
	use TraitHelpers\Constants;
	use TraitHelpers\DateTime;
	use TraitHelpers\Strings;
	use TraitHelpers\ThirdParty;
	use TraitHelpers\Vue;
	use TraitHelpers\Wp;
	use TraitHelpers\WpContext;
	use TraitHelpers\WpMultisite;
	use TraitHelpers\WpUri;

	/**
	 * Checks if we are in a dev environment or not.
	 *
	 * @since 1.0.0
	 *
	 * @return boolean True if we are, false if not.
	 */
	public function isDev() {
		return aioseoTableOfContents()->isDev || isset( $_REQUEST['aioseo-dev'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Generates a UTM URL from the URL and medium/content that are passed in.
	 *
	 * @since 1.0.0
	 *
	 * @param  string      $url     The URL to parse.
	 * @param  string      $medium  The UTM medium parameter.
	 * @param  string|null $content The UTM content parameter or null.
	 * @param  boolean     $esc     Whether or not to escape the URL.
	 * @return string               The new URL.
	 */
	public function utmUrl( $url, $medium, $content = null, $esc = true ) {
		// First, remove any existing utm parameters on the URL.
		$url = remove_query_arg( [
			'utm_source',
			'utm_medium',
			'utm_campaign',
			'utm_content'
		], $url );

		// Generate the new arguments.
		$args = [
			'utm_source'   => 'WordPress',
			'utm_campaign' => 'toc-plugin',
			'utm_medium'   => $medium
		];

		// Content is not used by default.
		if ( $content ) {
			$args['utm_content'] = $content;
		}

		// Return the new URL.
		$url = add_query_arg( $args, $url );

		return $esc ? esc_url( $url ) : $url;
	}

	/**
	 * Returns the excluded domains.
	 *
	 * @since 1.1.1
	 *
	 * @return array The excluded domains.
	 */
	public function getExcludedDomains() {
		if ( ! aioseoTableOfContents()->options->advanced->enable ) {
			return [];
		}

		$excludedDomains = aioseoTableOfContents()->options->advanced->excludeDomains;
		if ( ! is_string( $excludedDomains ) ) {
			return [];
		}

		$pattern = '/([\.?!][\r\n\s]+|\r|\n|\s{2,})/u';

		return array_map( 'trim', preg_split( $pattern, (string) $excludedDomains, -1, PREG_SPLIT_NO_EMPTY ) );
	}

	/**
	 * Checks if the given string is serialized, and if so, unserializes it.
	 * If the serialized string contains an object, we abort to prevent PHP object injection.
	 *
	 * @since 1.2.0
	 *
	 * @param  string       $string The string.
	 * @return string|array         The string or unserialized data.
	 */
	public function maybeUnserialize( $string ) {
		if ( ! is_string( $string ) ) {
			return $string;
		}

		$string = trim( $string );
		if ( is_serialized( $string ) && ! $this->stringContains( $string, ';O:' ) ) {
			// We want to add extra hardening for PHP versions greater than 5.6.
			return version_compare( PHP_VERSION, '7.0', '<' )
				? @unserialize( $string )
				: @unserialize( $string, [ 'allowed_classes' => false ] ); // phpcs:disable PHPCompatibility.FunctionUse.NewFunctionParameters.unserialize_optionsFound
		}

		return $string;
	}
}