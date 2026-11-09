<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features;

use TEC\Common\LiquidWeb\Harbor\Features\Strategy\Strategy_Factory;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Features\Error_Code;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use Throwable;
use WP_Error;

/**
 * Central orchestrator for enabling, disabling, and querying features.
 *
 * Delegates the actual mechanism to the appropriate strategy and fires
 * global + slug-specific WordPress actions around each operation.
 *
 * @since 1.0.0
 */
class Manager {

	use With_Debugging;

	/**
	 * The repository for fetching available features.
	 *
	 * @since 1.0.0
	 *
	 * @var Feature_Repository
	 */
	private Feature_Repository $repository;

	/**
	 * The strategy factory for determining how to toggle features.
	 *
	 * @since 1.0.0
	 *
	 * @var Strategy_Factory
	 */
	private Strategy_Factory $strategy_factory;

	/**
	 * Constructor for the central feature orchestrator.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature_Repository $repository       The repository for fetching available features.
	 * @param Strategy_Factory   $strategy_factory The strategy factory.
	 *
	 * @return void
	 */
	public function __construct( Feature_Repository $repository, Strategy_Factory $strategy_factory ) {
		$this->repository       = $repository;
		$this->strategy_factory = $strategy_factory;
	}

	/**
	 * Enables a feature by slug.
	 *
	 * Fires 'lw-harbor/feature_enabling' and 'lw-harbor/{slug}/feature_enabling'
	 * before the operation, and the corresponding 'feature_enabled' actions after success.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return Feature|WP_Error The feature with updated is_enabled state, or WP_Error on failure.
	 */
	public function enable( string $slug ) {
		static::debug_log(
			sprintf(
				'Enabling feature "%s".',
				$slug
			)
		);

		$features = $this->repository->get();

		if ( is_wp_error( $features ) ) {
			static::debug_log_wp_error(
				$features,
				sprintf( 'Cannot enable "%s": feature resolution failed', $slug )
			);

			return $features;
		}

		$feature = $features->get( $slug );

		if ( ! $feature ) {
			static::debug_log(
				sprintf(
					'Cannot enable "%s": not found in catalog.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found in the catalog.', $slug )
			);
		}

		if ( $feature->is_in_catalog_tier() && ! $feature->is_available() ) {
			static::debug_log(
				sprintf(
					'Cannot enable "%s": capability has been revoked.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::CAPABILITY_REVOKED,
				__( 'This feature has been removed from your license capabilities. Contact support.', 'tribe-common' )
			);
		}

		/**
		 * Fires before a feature is enabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature being enabled.
		 *
		 * @return void
		 */
		do_action( 'lw-harbor/feature_enabling', $feature->to_array() );

		/**
		 * Fires before a specific feature is enabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature being enabled.
		 *
		 * @return void
		 */
		do_action( "lw-harbor/{$slug}/feature_enabling", $feature->to_array() );

		try {
			$strategy = $this->strategy_factory->make( $feature );

			$result = $strategy->enable();
		} catch ( Throwable $e ) {
			static::debug_log_throwable(
				$e,
				sprintf( 'Exception enabling feature "%s"', $slug )
			);

			return new WP_Error(
				Error_Code::FEATURE_ENABLE_FAILED,
				$e->getMessage()
			);
		}

		if ( is_wp_error( $result ) ) {
			static::debug_log_wp_error(
				$result,
				sprintf( 'Strategy failed enabling "%s"', $slug )
			);

			return $result;
		}

		$this->repository->refresh();

		$feature = $this->get( $slug );

		if ( ! $feature ) {
			static::debug_log(
				sprintf(
					'Feature "%s" not found after enabling.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found after enabling.', $slug )
			);
		}

		static::debug_log(
			sprintf(
				'Feature "%s" enabled successfully.',
				$slug
			)
		);

		/**
		 * Fires after a feature has been successfully enabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature that was enabled.
		 *
		 * @return void
		 */
		do_action( 'lw-harbor/feature_enabled', $feature->to_array() );

		/**
		 * Fires after a specific feature has been successfully enabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature that was enabled.
		 *
		 * @return void
		 */
		do_action( "lw-harbor/{$slug}/feature_enabled", $feature->to_array() );

		return $feature;
	}

	/**
	 * Disables a feature by slug.
	 *
	 * Fires 'lw-harbor/feature_disabling' and 'lw-harbor/{slug}/feature_disabling'
	 * before the operation, and the corresponding 'feature_disabled' actions after success.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return Feature|WP_Error The feature with updated is_enabled state, or WP_Error on failure.
	 */
	public function disable( string $slug ) {
		static::debug_log(
			sprintf(
				'Disabling feature "%s".',
				$slug
			)
		);

		$features = $this->repository->get();

		if ( is_wp_error( $features ) ) {
			static::debug_log_wp_error(
				$features,
				sprintf( 'Cannot disable "%s": feature resolution failed', $slug )
			);

			return $features;
		}

		$feature = $features->get( $slug );

		if ( ! $feature ) {
			static::debug_log(
				sprintf(
					'Cannot disable "%s": not found in catalog.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found in the catalog.', $slug )
			);
		}

		/**
		 * Fires before a feature is disabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature being disabled.
		 *
		 * @return void
		 */
		do_action( 'lw-harbor/feature_disabling', $feature->to_array() );

		/**
		 * Fires before a specific feature is disabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature being disabled.
		 *
		 * @return void
		 */
		do_action( "lw-harbor/{$slug}/feature_disabling", $feature->to_array() );

		try {
			$strategy = $this->strategy_factory->make( $feature );

			$result = $strategy->disable();
		} catch ( Throwable $e ) {
			static::debug_log_throwable(
				$e,
				sprintf( 'Exception disabling feature "%s"', $slug )
			);

			return new WP_Error(
				Error_Code::FEATURE_DISABLE_FAILED,
				$e->getMessage()
			);
		}

		if ( is_wp_error( $result ) ) {
			static::debug_log_wp_error(
				$result,
				sprintf( 'Strategy failed disabling "%s"', $slug )
			);

			return $result;
		}

		$this->repository->refresh();

		$feature = $this->get( $slug );

		if ( ! $feature ) {
			static::debug_log(
				sprintf(
					'Feature "%s" not found after disabling.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found after disabling.', $slug )
			);
		}

		static::debug_log(
			sprintf(
				'Feature "%s" disabled successfully.',
				$slug
			)
		);

		/**
		 * Fires after a feature has been successfully disabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature that was disabled.
		 *
		 * @return void
		 */
		do_action( 'lw-harbor/feature_disabled', $feature->to_array() );

		/**
		 * Fires after a specific feature has been successfully disabled.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature that was disabled.
		 *
		 * @return void
		 */
		do_action( "lw-harbor/{$slug}/feature_disabled", $feature->to_array() );

		return $feature;
	}

	/**
	 * Updates a feature by slug.
	 *
	 * Fires 'lw-harbor/feature_updating' and 'lw-harbor/{slug}/feature_updating'
	 * before the operation, and the corresponding 'feature_updated' actions after success.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return Feature|WP_Error The feature with updated state, or WP_Error on failure.
	 */
	public function update( string $slug ) {
		static::debug_log(
			sprintf(
				'Updating feature "%s".',
				$slug
			)
		);

		$features = $this->repository->get();

		if ( is_wp_error( $features ) ) {
			static::debug_log_wp_error(
				$features,
				sprintf( 'Cannot update "%s": feature resolution failed', $slug )
			);

			return $features;
		}

		$feature = $features->get( $slug );

		if ( ! $feature ) {
			static::debug_log(
				sprintf(
					'Cannot update "%s": not found in catalog.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found in the catalog.', $slug )
			);
		}

		/**
		 * Fires before a feature is updated.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature being updated.
		 *
		 * @return void
		 */
		do_action( 'lw-harbor/feature_updating', $feature->to_array() );

		/**
		 * Fires before a specific feature is updated.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature being updated.
		 *
		 * @return void
		 */
		do_action( "lw-harbor/{$slug}/feature_updating", $feature->to_array() );

		try {
			$strategy = $this->strategy_factory->make( $feature );

			$result = $strategy->update();
		} catch ( Throwable $e ) {
			static::debug_log_throwable(
				$e,
				sprintf( 'Exception updating feature "%s"', $slug )
			);

			return new WP_Error(
				Error_Code::UPDATE_FAILED,
				$e->getMessage()
			);
		}

		if ( is_wp_error( $result ) ) {
			static::debug_log_wp_error(
				$result,
				sprintf( 'Strategy failed updating "%s"', $slug )
			);

			return $result;
		}

		$this->repository->refresh();

		$feature = $this->get( $slug );

		if ( ! $feature ) {
			static::debug_log(
				sprintf(
					'Feature "%s" not found after updating.',
					$slug
				)
			);

			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found after updating.', $slug )
			);
		}

		static::debug_log(
			sprintf(
				'Feature "%s" updated successfully.',
				$slug
			)
		);

		/**
		 * Fires after a feature has been successfully updated.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature that was updated.
		 *
		 * @return void
		 */
		do_action( 'lw-harbor/feature_updated', $feature->to_array() );

		/**
		 * Fires after a specific feature has been successfully updated.
		 *
		 * @since 1.0.0
		 *
		 * @param array<string, mixed> $feature The feature that was updated.
		 *
		 * @return void
		 */
		do_action( "lw-harbor/{$slug}/feature_updated", $feature->to_array() );

		return $feature;
	}

	/**
	 * Checks whether a feature is in the catalog AND currently enabled/active.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return bool|WP_Error True if enabled, false if disabled, WP_Error on failure.
	 */
	public function is_enabled( string $slug ) {
		$features = $this->get_all();

		if ( is_wp_error( $features ) ) {
			return $features;
		}

		$feature = $features->get( $slug );

		if ( ! $feature ) {
			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found in the catalog.', $slug )
			);
		}

		return $feature->is_enabled();
	}

	/**
	 * Checks whether a feature is available for the current site's tier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return bool|WP_Error True if available, false if not, WP_Error on failure.
	 */
	public function is_available( string $slug ) {
		$features = $this->get_all();

		if ( is_wp_error( $features ) ) {
			return $features;
		}

		$feature = $features->get( $slug );

		if ( ! $feature ) {
			return new WP_Error(
				Error_Code::FEATURE_NOT_FOUND,
				sprintf( 'Feature "%s" not found in the catalog.', $slug )
			);
		}

		return $feature->is_available();
	}

	/**
	 * Checks whether a feature exists in the catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return bool|WP_Error
	 */
	public function exists( string $slug ) {
		$features = $this->repository->get();

		if ( is_wp_error( $features ) ) {
			return $features;
		}

		return $features->get( $slug ) !== null;
	}

	/**
	 * Gets the feature collection from the catalog with live enabled state.
	 *
	 * @since 1.0.0
	 *
	 * @return Feature_Collection|WP_Error
	 */
	public function get_all() {
		$features = $this->repository->get();

		if ( is_wp_error( $features ) ) {
			return $features;
		}

		$this->stamp_enabled_state( $features );

		return $features;
	}

	/**
	 * Looks up a feature by slug from the cached catalog with live enabled state.
	 *
	 * Returns null when the feature is not found or when the API
	 * request fails, since the catalog is unavailable.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug The feature slug.
	 *
	 * @return Feature|null
	 */
	public function get( string $slug ): ?Feature {
		$features = $this->get_all();

		if ( is_wp_error( $features ) ) {
			return null;
		}

		return $features->get( $slug );
	}

	/**
	 * Stamps live enabled state onto every feature in the collection.
	 *
	 * The Feature_Collection from the repository does not include
	 * is_enabled state. This method overwrites each feature's
	 * is_enabled with the current live state from its strategy.
	 *
	 * @since 1.0.0
	 *
	 * @param Feature_Collection $features The collection to stamp.
	 *
	 * @return void
	 */
	private function stamp_enabled_state( Feature_Collection $features ): void {
		foreach ( $features as $feature ) {
			try {
				$strategy           = $this->strategy_factory->make( $feature );
				$class              = get_class( $feature );
				$data               = $feature->to_array();
				$data['is_enabled'] = $strategy->is_active();

				$features->offsetSet( $feature->get_slug(), $class::from_array( $data ) );
			} catch ( Throwable $e ) {
				static::debug_log_throwable(
					$e,
					sprintf( 'Failed to stamp enabled state for "%s"', $feature->get_slug() )
				);
			}
		}
	}
}
