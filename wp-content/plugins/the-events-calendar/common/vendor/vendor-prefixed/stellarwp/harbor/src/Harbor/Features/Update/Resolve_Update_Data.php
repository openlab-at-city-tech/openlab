<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Update;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Collection;
use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Repository;
use TEC\Common\LiquidWeb\Harbor\Portal\Contracts\Download_Url_Builder;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Catalog_Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Contracts\Installable;
use TEC\Common\LiquidWeb\Harbor\Features\Feature_Repository;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use WP_Error;

/**
 * Resolves update data by joining the Feature_Repository and Catalog.
 *
 * The Feature_Repository determines which features the site is licensed for
 * (availability). The Catalog provides version metadata. A Download_Url_Builder
 * is passed through to each feature's get_update_data() so the package URL is
 * built there, keeping the responsibility with the feature type.
 *
 * Only features where is_available() returns true are included,
 * ensuring the update API only serves updates the site is licensed for.
 * Dot-org features are excluded since WordPress.org serves their updates.
 *
 * Works for both Plugin and Theme features — the caller passes the desired
 * feature type constant, and the handler is responsible for reading any
 * type-specific fields (e.g. plugin_file, stylesheet) from the Feature object.
 *
 * @since 1.0.0
 */
class Resolve_Update_Data {

	use With_Debugging;

	/**
	 * The feature repository.
	 *
	 * @since 1.0.0
	 *
	 * @var Feature_Repository
	 */
	private Feature_Repository $feature_repository;

	/**
	 * The catalog repository.
	 *
	 * @since 1.0.0
	 *
	 * @var Catalog_Repository
	 */
	private Catalog_Repository $catalog_repository;

	/**
	 * The download URL builder.
	 *
	 * @since 1.0.0
	 *
	 * @var Download_Url_Builder
	 */
	private Download_Url_Builder $url_builder;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature_Repository   $feature_repository The feature repository.
	 * @param Catalog_Repository   $catalog_repository The catalog repository.
	 * @param Download_Url_Builder $url_builder        The download URL builder.
	 */
	public function __construct(
		Feature_Repository $feature_repository,
		Catalog_Repository $catalog_repository,
		Download_Url_Builder $url_builder
	) {
		$this->feature_repository = $feature_repository;
		$this->catalog_repository = $catalog_repository;
		$this->url_builder        = $url_builder;
	}

	/**
	 * Fetches available Installable features of the given type and transforms them into update data.
	 *
	 * Joins feature availability from the Feature_Repository with version metadata
	 * from the Catalog_Repository. The Download_Url_Builder is passed to each feature's
	 * get_update_data() so the package URL is built there.
	 *
	 * @since 1.0.0
	 *
	 * @param string $type The feature type to resolve (a Feature::TYPE_* constant).
	 *
	 * @return array<string, array<string, mixed>>|WP_Error Keyed by slug, each entry contains update fields.
	 */
	public function __invoke( string $type ) {
		$features = $this->feature_repository->get();

		if ( is_wp_error( $features ) ) {
			static::debug_log_wp_error(
				$features,
				'Resolve_Update_Data: feature repository failed'
			);

			return $features;
		}

		$catalog = $this->catalog_repository->get();

		if ( is_wp_error( $catalog ) ) {
			static::debug_log_wp_error(
				$catalog,
				'Resolve_Update_Data: catalog repository failed'
			);

			return $catalog;
		}

		$catalog_features = $this->build_catalog_feature_map( $catalog );
		$available        = $features->filter( null, null, true, $type );
		$updates          = [];

		foreach ( $available as $feature ) {
			if ( ! $feature instanceof Installable ) {
				continue;
			}

			$slug            = $feature->get_slug();
			$catalog_feature = $catalog_features[ $slug ] ?? null;

			if ( $catalog_feature === null || $catalog_feature->is_wporg() ) {
				continue;
			}

			$updates[ $slug ] = $feature->get_update_data( $catalog_feature, $this->url_builder );
		}

		return $updates;
	}

	/**
	 * Builds a flat map of feature slug to Catalog_Feature from the catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Collection $catalog The catalog collection.
	 *
	 * @return array<string, Catalog_Feature>
	 */
	private function build_catalog_feature_map( Catalog_Collection $catalog ): array {
		$map = [];

		foreach ( $catalog as $product ) {
			foreach ( $product->get_features() as $catalog_feature ) {
				$map[ $catalog_feature->get_slug() ] = $catalog_feature;
			}
		}

		return $map;
	}
}
