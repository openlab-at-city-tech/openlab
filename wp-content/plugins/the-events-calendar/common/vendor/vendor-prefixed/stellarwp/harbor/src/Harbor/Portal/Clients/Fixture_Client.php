<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Portal\Clients;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Collection;
use TEC\Common\LiquidWeb\Harbor\Portal\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Product_Catalog;
use WP_Error;

/**
 * A fixture-based catalog client that reads from a JSON file.
 *
 * @since 1.0.0
 */
final class Fixture_Client implements Portal_Client {

	/**
	 * The path to the fixture JSON file.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected string $fixture_file;

	/**
	 * In-memory cache of the parsed catalog.
	 *
	 * @since 1.0.0
	 *
	 * @var Catalog_Collection|WP_Error|null
	 */
	protected $cache;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $fixture_file Path to the fixture JSON file.
	 */
	public function __construct( string $fixture_file ) {
		$this->fixture_file = $fixture_file;
	}

	/**
	 * Fetch the full catalog for all products.
	 *
	 * @since 1.0.0
	 *
	 * @return Catalog_Collection|WP_Error
	 */
	public function get_catalog() {
		if ( $this->cache !== null ) {
			return $this->cache;
		}

		$json = @file_get_contents( $this->fixture_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents, WordPress.PHP.NoSilencedErrors.Discouraged, WordPressVIPMinimum.Performance.FetchingRemoteData.FileGetContentsUnknown -- local fixture file, error silenced intentionally.

		if ( $json === false ) {
			$this->cache = new WP_Error(
				Error_Code::INVALID_RESPONSE,
				'Catalog fixture file could not be read.'
			);

			return $this->cache;
		}

		$data = json_decode( $json, true );

		if ( ! is_array( $data ) ) {
			$this->cache = new WP_Error(
				Error_Code::INVALID_RESPONSE,
				'Catalog fixture file could not be decoded.'
			);

			return $this->cache;
		}

		$catalogs = new Catalog_Collection();

		foreach ( $data as $item ) {
			if ( ! is_array( $item ) || ! isset( $item['product_slug'] ) ) {
				$this->cache = new WP_Error(
					Error_Code::INVALID_RESPONSE,
					'Catalog entry missing product_slug.'
				);

				return $this->cache;
			}

			/** @var array<string, mixed> $item */
			$catalogs->add( Product_Catalog::from_array( $item ) );
		}

		if ( $catalogs->count() === 0 ) {
			$this->cache = new WP_Error(
				Error_Code::INVALID_RESPONSE,
				'Catalog fixture file is empty.'
			);

			return $this->cache;
		}

		$this->cache = $catalogs;

		return $this->cache;
	}
}
