<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth;

use TEC\Common\StellarWP\Uplink\API\V3\Auth\Contracts\Auth_Url;

final class Auth_Url_Builder {

	/**
	 * @var Nonce
	 */
	private $nonce;

	/**
	 * @var Auth_Url
	 */
	private $auth_url_manager;

	/**
	 * @var string
	 */
	private $license_key;

	/**
	 * @param  Nonce  $nonce  The Nonce creator.
	 * @param  Auth_Url  $auth_url_manager  The auth URL manager.
	 */
	public function __construct(
		Nonce $nonce,
		Auth_Url $auth_url_manager
	) {
		$this->nonce            = $nonce;
		$this->auth_url_manager = $auth_url_manager;
	}

	/**
	 * Build a brand's authorization URL, with the uplink_callback base64 query variable.
	 *
	 * @note This URL requires escaping.
	 *
	 * @param  string  $slug  The product/service slug.
	 * @param  string  $domain  An optional domain associated with a license key to pass along.
	 *
	 * @return string
	 */
	public function build( string $slug, string $domain = '' ): string {
		global $pagenow;

		if ( empty( $pagenow ) ) {
			return '';
		}

		$callback_url = admin_url( $pagenow );

		// If building the URL in an ajax context, use the referring URL.
		if ( wp_parse_url( $pagenow, PHP_URL_PATH ) === 'admin-ajax.php' ) {
			$callback_url = wp_get_referer();
		}

		$auth_url = $this->auth_url_manager->get( $slug );

		if ( ! $auth_url ) {
			return '';
		}

		// Query arguments to combine with $_GET and add to the authorization URL.
		$args = [
			'uplink_domain' => $domain,
			'uplink_slug'   => $slug,
		];

		// Optionally include a license key if set.
		if ( ! empty( $this->license_key ) ) {
			$args['uplink_license'] = $this->license_key;
		}

		$url = add_query_arg(
			array_filter( array_merge( $_GET, $args ) ),
			$callback_url
		);

		return sprintf( '%s?%s',
			$auth_url,
			http_build_query( [
				'uplink_callback' => base64_encode( $this->nonce->create_url( $url ) ),
			] )
		);
	}

	/**
	 * Optionally set a license key to provide in uplink_callback query arg.
	 *
	 * @param string $key The license key to pass in the auth url.
	 *
	 * @return self
	 */
	public function set_license( string $key ): self {
		$this->license_key = $key;

		return $this;
	}
}
