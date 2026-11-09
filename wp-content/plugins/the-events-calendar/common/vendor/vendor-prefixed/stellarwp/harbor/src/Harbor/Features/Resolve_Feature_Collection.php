<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Collection;
use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Repository;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Catalog_Feature;
use TEC\Common\LiquidWeb\Harbor\Portal\Results\Product_Catalog;
use TEC\Common\LiquidWeb\Harbor\Features\Contracts\Installable;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Plugin;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Service;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Theme;
use TEC\Common\LiquidWeb\Harbor\Legacy\Legacy_License;
use TEC\Common\LiquidWeb\Harbor\Legacy\License_Repository as Legacy_License_Repository;
use TEC\Common\LiquidWeb\Harbor\Licensing\Enums\Validation_Status;
use TEC\Common\LiquidWeb\Harbor\Licensing\Error_Code as Licensing_Error_Code;
use TEC\Common\LiquidWeb\Harbor\Licensing\License_Manager;
use TEC\Common\LiquidWeb\Harbor\Licensing\Product_Collection;
use TEC\Common\LiquidWeb\Harbor\Site\Data;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use WP_Error;

/**
 * Joins catalog and licensing data to produce a resolved Feature_Collection.
 *
 * For each catalog feature, computes is_available and in_catalog_tier by checking
 * effective capability grants across purchases and the brand's licensed tier rank.
 * dot.org and free-tier (rank 0) features are unconditionally available regardless of capabilities.
 * A legacy license whose slug matches the catalog feature also grants availability (and counts
 * as in-tier) when it is active, has a non-empty key, and has opted in via `use_for_updates`.
 * The opt-in prevents Harbor from advertising updates for legacy keys whose backend is not
 * compatible with Herald's `/legacy/download` endpoint.
 *
 * @since 1.0.0
 */
class Resolve_Feature_Collection {

	use With_Debugging;

	/**
	 * The catalog repository.
	 *
	 * @since 1.0.0
	 *
	 * @var Catalog_Repository
	 */
	private Catalog_Repository $catalog;

	/**
	 * The license manager.
	 *
	 * @since 1.0.0
	 *
	 * @var License_Manager
	 */
	private License_Manager $licensing;

	/**
	 * The site data provider.
	 *
	 * @since 1.0.0
	 *
	 * @var Data
	 */
	private Data $site_data;

	/**
	 * The legacy license repository.
	 *
	 * @since 1.3.0
	 *
	 * @var Legacy_License_Repository
	 */
	private Legacy_License_Repository $legacy_repository;

	/**
	 * Map of catalog type strings to Feature subclass names.
	 *
	 * @since 1.0.0
	 *
	 * @var array<string, class-string<Feature>>
	 */
	private array $type_map = [
		Feature::TYPE_PLUGIN  => Plugin::class,
		Feature::TYPE_THEME   => Theme::class,
		Feature::TYPE_SERVICE => Service::class,
	];

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Repository             $catalog           The catalog repository.
	 * @param License_Manager                $licensing         The license manager.
	 * @param Data                           $site_data         The site data provider.
	 * @param Legacy_License_Repository|null $legacy_repository The legacy license repository. Default null, which creates a new instance for backwards compatibility.
	 */
	public function __construct(
		Catalog_Repository $catalog,
		License_Manager $licensing,
		Data $site_data,
		?Legacy_License_Repository $legacy_repository = null
	) {
		$this->catalog           = $catalog;
		$this->licensing         = $licensing;
		$this->site_data         = $site_data;
		$this->legacy_repository = $legacy_repository ?? new Legacy_License_Repository();
	}

	/**
	 * Registers a Feature subclass for a given catalog type string.
	 *
	 * @since 1.0.0
	 *
	 * @param string                $type          A Feature::TYPE_* constant (e.g. Feature::TYPE_PLUGIN).
	 * @param class-string<Feature> $feature_class The Feature subclass FQCN.
	 *
	 * @return void
	 */
	public function register_type( string $type, string $feature_class ): void { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint -- class-string<Feature> is a PHPStan type narrowing.
		$this->type_map[ $type ] = $feature_class;
	}

	/**
	 * Fetches catalog and licensing data and resolves them into a Feature_Collection.
	 *
	 * Iterates each catalog product, applies effective capability grants,
	 * and hydrates Feature objects with computed is_available values.
	 *
	 * @since 1.0.0
	 *
	 * @return Feature_Collection|WP_Error
	 */
	public function __invoke() {
		$catalog = $this->catalog->get();

		if ( is_wp_error( $catalog ) ) {
			static::debug_log_wp_error(
				$catalog,
				'Catalog fetch failed during feature resolution'
			);

			return $catalog;
		}

		$products = $this->licensing->get_products( $this->site_data->get_domain() );

		if ( is_wp_error( $products ) ) {
			if ( $products->get_error_code() === Licensing_Error_Code::INVALID_KEY ) {
				$products = new Product_Collection();
			} else {
				static::debug_log_wp_error(
					$products,
					'Licensing fetch failed during feature resolution'
				);

				return $products;
			}
		}

		$collection         = new Feature_Collection();
		$legacy_licenses    = $this->build_legacy_license_map();
		$capability_sources = $this->resolve_capability_sources( $products, $catalog );

		foreach ( $catalog as $product ) {
			if ( ! $product instanceof Product_Catalog ) {
				continue;
			}

			$license_tier_rank = $this->resolve_license_tier_rank( $product, $products );

			foreach ( $product->get_features() as $catalog_feature ) {
				$legacy_license = $legacy_licenses[ $catalog_feature->get_slug() ] ?? null;
				$feature        = $this->hydrate_feature( $catalog_feature, $product, $capability_sources, $license_tier_rank, $legacy_license );

				if ( is_wp_error( $feature ) ) {
					static::debug_log( $feature->get_error_message() );
					continue;
				}

				$collection->add( $feature );
			}
		}

		return $collection;
	}

	/**
	 * Builds a `slug => Legacy_License` map from the legacy repository.
	 *
	 * Resolved once per `__invoke()` call so the `lw-harbor/legacy_licenses`
	 * filter dispatches once per resolution rather than once per catalog
	 * feature. This also narrows the surface for filter re-entry: only one
	 * dispatch can be in-flight while hydrate_feature() iterates.
	 *
	 * @since 1.3.0
	 *
	 * @return array<string, Legacy_License>
	 */
	private function build_legacy_license_map(): array {
		$map = [];

		foreach ( $this->legacy_repository->all() as $license ) {
			$map[ $license->slug ] = $license;
		}

		return $map;
	}

	/**
	 * Collects the purchase sources of every effective capability on this site.
	 *
	 * Product slugs describe purchases. Capabilities can cover features in any
	 * brand family. Null preserves the unlicensed display when no source is effective.
	 *
	 * @param Product_Collection $products The licensing product collection.
	 * @param Catalog_Collection $catalog  The catalog used for source display names.
	 *
	 * @return array<string, array<string, array<string, string>>>|null Sources by capability, or null when no purchase is effective.
	 */
	private function resolve_capability_sources( Product_Collection $products, Catalog_Collection $catalog ): ?array {
		$sources = null;

		foreach ( $products as $license ) {
			if ( ! $license->get_activated_here() || $this->is_license_invalid( $license->get_validation_status() ) ) {
				continue;
			}

			$sources ??= [];
			$product   = $catalog->get( $license->get_product_slug() );
			$tier      = $product !== null ? $product->get_tier_by_slug( $license->get_tier() ) : null;
			$source    = [
				'type'         => 'purchase',
				'product_slug' => $license->get_product_slug(),
				'tier'         => $license->get_tier(),
				'product_name' => $product !== null ? $product->get_product_name() : $license->get_product_slug(),
				'tier_name'    => $tier !== null ? $tier->get_name() : $license->get_tier(),
			];
			$source_id = $license->get_product_slug() . ':' . $license->get_tier();

			foreach ( $license->get_capabilities() as $capability ) {
				$sources[ $capability ][ $source_id ] = $source;
			}
		}

		return $sources;
	}

	/**
	 * Returns the rank of the user's highest effective tier for a product, or -1 if unlicensed.
	 *
	 * Only entries activated here and not known to be ineffective count, matching
	 * the resolve_capability_sources() guard so both flags are consistent. A product
	 * can have several entries, and an invalid one must not hide a valid one.
	 *
	 * @since 1.0.0
	 *
	 * @param Product_Catalog    $product  The catalog product.
	 * @param Product_Collection $products The licensing product collection.
	 *
	 * @return int The license tier rank, or -1 if no license covers this product.
	 */
	private function resolve_license_tier_rank( Product_Catalog $product, Product_Collection $products ): int {
		$rank = -1;

		foreach ( $products->get_all_by_slug( $product->get_product_slug() ) as $license ) {
			if ( ! $license->get_activated_here() || $this->is_license_invalid( $license->get_validation_status() ) ) {
				continue;
			}

			$tier = $product->get_tier_by_slug( $license->get_tier() );

			if ( $tier !== null ) {
				$rank = max( $rank, $tier->get_rank() );
			}
		}

		return $rank;
	}

	/**
	 * Returns true when a known validation status is present but is not 'valid'.
	 *
	 * Covers not_activated, activation_required, expired, suspended, cancelled,
	 * out_of_activations, license_suspended, license_banned, no_entitlement, etc.
	 * A null status means no domain was provided in the request, so capabilities
	 * are left untouched in that case.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $validation_status The product's validation status.
	 *
	 * @return bool
	 */
	private function is_license_invalid( ?string $validation_status ): bool {
		return $validation_status !== null && $validation_status !== Validation_Status::VALID;
	}

	/**
	 * Hydrates a Feature object from a catalog feature entry.
	 *
	 * Maps catalog types (plugin, theme) to Feature subclasses
	 * and computes is_available and in_catalog_tier.
	 *
	 * dot.org and free-tier (rank 0) features are unconditionally available regardless of capabilities.
	 * A legacy license whose slug matches the catalog feature also grants availability and counts as
	 * in-tier, regardless of Unified capabilities or tier rank, when the entry is active, has a
	 * non-empty key, and has opted in via `use_for_updates`.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Feature                                          $catalog_feature    The catalog feature entry.
	 * @param Product_Catalog                                          $product            The parent catalog product.
	 * @param array<string, array<string, array<string, string>>>|null $capability_sources The effective purchase sources by capability, or null if unlicensed.
	 * @param int                                                      $license_tier_rank  The user's licensed tier rank, or -1 if unlicensed.
	 * @param Legacy_License|null                                      $legacy_license     The legacy license entry whose slug matches this feature, or null if none.
	 *
	 * @return Feature|WP_Error The hydrated feature, or WP_Error for unknown types.
	 */
	private function hydrate_feature(
		Catalog_Feature $catalog_feature,
		Product_Catalog $product,
		?array $capability_sources,
		int $license_tier_rank,
		?Legacy_License $legacy_license
	) {
		$catalog_kind = $catalog_feature->get_kind();
		$class        = $this->type_map[ $catalog_kind ] ?? null;

		if ( $class === null ) {
			return new WP_Error(
				Error_Code::UNKNOWN_FEATURE_TYPE,
				sprintf(
					'No Feature subclass registered for catalog kind "%s" (feature: %s).',
					$catalog_kind,
					$catalog_feature->get_slug()
				)
			);
		}

		$minimum_tier = $product->get_tier_by_slug( $catalog_feature->get_minimum_tier() );
		$minimum_rank = $minimum_tier !== null ? $minimum_tier->get_rank() : PHP_INT_MAX;

		$has_legacy_grant = $legacy_license !== null
			&& $legacy_license->is_active
			&& $legacy_license->key !== ''
			&& $legacy_license->use_for_updates;

		$access_sources = array_values( $capability_sources[ $catalog_feature->get_slug() ] ?? [] );

		if ( $catalog_feature->is_wporg() || $minimum_rank === 0 ) {
			$access_sources[] = [ 'type' => 'free' ];
		}

		if ( $has_legacy_grant ) {
			$access_sources[] = [ 'type' => 'legacy' ];
		}

		// Cross-product purchases include their granted features without changing
		// the existing distinction between a brand tier and its bonus capabilities.
		$has_cross_product_grant = false;
		foreach ( $access_sources as $source ) {
			if ( $source['type'] === 'purchase' && $source['product_slug'] !== $product->get_product_slug() ) {
				$has_cross_product_grant = true;
				break;
			}
		}

		$is_available    = $access_sources !== [];
		$in_catalog_tier = $has_legacy_grant || $catalog_feature->is_wporg() || $minimum_rank === 0
			|| $has_cross_product_grant || ( $capability_sources !== null && $license_tier_rank >= $minimum_rank );

		$data = [
			'slug'              => $catalog_feature->get_slug(),
			'product'           => $product->get_product_slug(),
			'tier'              => $catalog_feature->get_minimum_tier(),
			'name'              => $catalog_feature->get_name(),
			'description'       => $catalog_feature->get_description(),
			'type'              => $catalog_kind,
			'is_available'      => $is_available,
			'access_sources'    => $access_sources,
			'in_catalog_tier'   => $in_catalog_tier,
			'documentation_url' => $catalog_feature->get_documentation_url(),
			'release_date'      => $catalog_feature->get_release_date(),
			'plugin_file'       => $catalog_feature->get_plugin_file() ?? '',
			'wporg_slug'        => $catalog_feature->get_wporg_slug(),
			'version'           => $catalog_feature->get_version(),
			'changelog'         => $catalog_feature->get_changelog(),
		];

		$feature = $class::from_array( $data );

		if ( $feature instanceof Installable ) {
			$data['installed_version'] = $feature->get_installed_version();
			$feature                   = $class::from_array( $data );
		}

		return $feature;
	}
}
