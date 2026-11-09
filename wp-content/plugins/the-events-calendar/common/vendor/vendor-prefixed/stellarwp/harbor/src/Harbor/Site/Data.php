<?php

namespace TEC\Common\LiquidWeb\Harbor\Site;

use TEC\Common\LiquidWeb\Harbor\Utils\Cast;
use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\LiquidWeb\Harbor\Config;

class Data {
	/**
	 * Container.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// @phpstan-ignore-next-line
		$this->container = Config::get_container();
	}

	/**
	 * Gets the domain for the site.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_domain(): string {
		$cache_key = 'lw_harbor_domain';
		$domain    = $this->container->has( $cache_key ) ? Cast::to_string( $this->container->get( $cache_key ) ) : null;

		if ( null === $domain ) {
			$domain = $this->get_site_domain();
			$this->container->bind(
				$cache_key,
				function () use ( $domain ) {
					return $domain;
				}
			);
		}

		/**
		 * Filters the domain for the site.
		 *
		 * @since 1.0.0
		 *
		 * @param string $domain Domain.
		 */
		$domain = apply_filters( 'lw-harbor/get_domain', $domain );

		return sanitize_text_field( Cast::to_string( $domain ) );
	}

	/**
	 * Returns the domain of the site.
	 *
	 * Derives the host from home_url() rather than reading the siteurl option
	 * directly. home_url() passes through the home_url/site_url filters, so
	 * host-environment plugins that rewrite the site address (e.g. Hostinger's
	 * preview-domain mu-plugin, which rewrites home_url/site_url to the preview
	 * host while leaving the siteurl option pointing at production) are honored.
	 * This keeps the domain Harbor sends for license validation consistent with
	 * the URLs used elsewhere in the activation flow (rest_url/admin_url).
	 *
	 * Under WP-CLI and cron there is no web request for such plugins to hook,
	 * so home_url() returns the canonical (option-backed) domain — no reliance
	 * on request-only server variables.
	 *
	 * @since 1.5.0 resolve from home_url() so URL rewrite filters are respected
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_site_domain(): string {
		/** @var array<string>|false */
		$home_url = wp_parse_url( home_url() );
		if ( ! $home_url || ! isset( $home_url['host'] ) ) {
			return '';
		}

		return strtolower( $home_url['host'] );
	}
}
