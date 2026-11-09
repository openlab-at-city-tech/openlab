<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal;

use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Licensing\Repositories\License_Repository;
use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;
use TEC\Common\LiquidWeb\Harbor\Site\Data;

/**
 * Builds Herald download URLs authenticated by the Unified license key.
 *
 * Herald is the StellarWP download service. This implementation authenticates
 * downloads with the per-site Unified license key.
 *
 * URL format: `{herald_base_url}/download/{slug}/latest/{license_key}/zip?site={domain}`
 *
 * Returns an empty string when either the Unified key or the domain is missing.
 * Legacy license keys are handled by `Herald_Legacy_Url_Builder`; routing
 * between the two implementations is the job of `Herald_Routing_Url_Builder`.
 *
 * @since 1.0.0
 */
final class Herald_Url_Builder implements Download_Url_Builder {

	/**
	 * The Unified license key provider.
	 *
	 * @since 1.0.0
	 *
	 * @var License_Repository
	 */
	private License_Repository $license_repository;

	/**
	 * Site data provider.
	 *
	 * @since 1.0.0
	 *
	 * @var Data
	 */
	private Data $site_data;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param License_Repository $license_repository The Unified license key provider.
	 * @param Data               $site_data          Site data provider.
	 */
	public function __construct( License_Repository $license_repository, Data $site_data ) {
		$this->license_repository = $license_repository;
		$this->site_data          = $site_data;
	}

	/**
	 * Builds the Unified Herald download URL for the given feature slug.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The catalog feature slug.
	 *
	 * @return string
	 */
	public function build( string $slug ): string {
		$domain = $this->site_data->get_domain();

		if ( ! $domain ) {
			return '';
		}

		$license_key = $this->license_repository->get_key();

		if ( $license_key === null ) {
			return '';
		}

		$url = Config::get_herald_base_url()
			. '/download/' . rawurlencode( $slug )
			. '/latest/' . rawurlencode( $license_key )
			. '/zip';

		return add_query_arg( [ 'site' => rawurlencode( $domain ) ], $url );
	}
}
