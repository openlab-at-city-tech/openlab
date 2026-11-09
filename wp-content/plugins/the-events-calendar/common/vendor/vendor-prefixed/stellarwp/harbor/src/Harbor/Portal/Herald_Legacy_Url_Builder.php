<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal;

use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Legacy\License_Repository as Legacy_License_Repository;
use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;
use TEC\Common\LiquidWeb\Harbor\Site\Data;

/**
 * Builds Herald download URLs authenticated by a legacy license key.
 *
 * Used when a plugin has registered a legacy license entry (via the
 * `lw-harbor/legacy_licenses` filter) whose `slug` matches the requested
 * feature. The legacy entry carries the per-plugin key that Herald accepts
 * on `/legacy/download`.
 *
 * URL format: `{herald_base_url}/legacy/download?plugin={slug}&key={legacy_key}&site={domain}`
 *
 * Returns an empty string when the domain is missing, when no legacy entry
 * matches the slug, when the matched entry is inactive, when its key is empty,
 * or when the entry has not opted in via `use_for_updates`.
 * `Herald_Routing_Url_Builder` treats an empty return as "no legacy URL
 * available for this slug" and falls back to the Unified builder.
 *
 * @since 1.3.0
 */
final class Herald_Legacy_Url_Builder implements Download_Url_Builder {

	/**
	 * The legacy license repository.
	 *
	 * @since 1.3.0
	 *
	 * @var Legacy_License_Repository
	 */
	private Legacy_License_Repository $legacy_repository;

	/**
	 * Site data provider.
	 *
	 * @since 1.3.0
	 *
	 * @var Data
	 */
	private Data $site_data;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 *
	 * @param Legacy_License_Repository $legacy_repository The legacy license repository.
	 * @param Data                      $site_data         Site data provider.
	 */
	public function __construct( Legacy_License_Repository $legacy_repository, Data $site_data ) {
		$this->legacy_repository = $legacy_repository;
		$this->site_data         = $site_data;
	}

	/**
	 * Builds the legacy Herald download URL for the given feature slug.
	 *
	 * @since 1.3.0
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

		$legacy = $this->legacy_repository->find( $slug );

		if ( $legacy === null || ! $legacy->is_active || $legacy->key === '' || ! $legacy->use_for_updates ) {
			return '';
		}

		return add_query_arg(
			[
				'plugin' => rawurlencode( $slug ),
				'key'    => rawurlencode( $legacy->key ),
				'site'   => rawurlencode( $domain ),
			],
			Config::get_herald_base_url() . '/legacy/download'
		);
	}
}
