<?php

namespace ElementorOne\Connect\Classes;

use ElementorOne\Connect\Facade;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class HomeUrl
 */
class HomeUrl {

	/**
	 * Facade instance
	 * @var Facade
	 */
	private Facade $facade;

	/**
	 * HomeUrl constructor
	 * @param Facade $facade
	 */
	public function __construct( Facade $facade ) {
		$this->facade = $facade;
	}

	/**
	 * Ensure home URL is set
	 * @return string|null
	 */
	public function get_saved(): ?string {
		$data = $this->facade->data();
		$home_url = $data->get_home_url();

		if ( ! empty( $home_url ) ) {
			return (string) $home_url;
		}

		$home_url = $this->fetch_home_url();
		if ( ! empty( $home_url ) ) {
			$data->set_home_url( $home_url );
		}

		return $home_url;
	}

	/**
	 * Fetch home URL from client
	 * @return string|null
	 */
	private function fetch_home_url(): ?string {
		try {
			$client = $this->facade->service()->get_client();
			$redirect_uri = $client['redirect_uris'][0] ?? '';
			return self::extract_home_url( $redirect_uri );
		} catch ( \Throwable $_th ) {
			return null;
		}
	}

	/**
	 * Extract home URL from redirect URI
	 * Removes the callback path (e.g., /wp-admin/admin.php?...) from the redirect URI
	 * Example: https://example.com/wp-admin/admin.php?page=callback -> https://example.com
	 * @param string $redirect_uri
	 * @return string
	 */
	private static function extract_home_url( string $redirect_uri ): string {
		return preg_replace( '/\/[^\/]+\/[^\/]+\.php.*$/', '', $redirect_uri ) ?? '';
	}

	/**
	 * Get current home URL
	 * @return string
	 */
	public function get_current(): string {
		if ( defined( 'ICL_SITEPRESS_VERSION' ) ) {
			return get_site_url();
		}
		return home_url();
	}

	/**
	 * Check if home URL is valid
	 * @return bool
	 */
	public function is_valid(): bool {
		$saved = $this->get_saved();
		$current = $this->get_current();

		return empty( $saved ) || $current === $saved;
	}
}
