<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Features\Update;

use TEC\Common\LiquidWeb\Harbor\Features\Contracts\Installable;
use TEC\Common\LiquidWeb\Harbor\Features\Feature_Repository;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;
use TEC\Common\LiquidWeb\Harbor\Legacy\License_Repository as Legacy_License_Repository;
use TEC\Common\LiquidWeb\Harbor\Licensing\License_Manager;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use stdClass;
use TEC\Common\LiquidWeb\Harbor\Utils\Cast;

/**
 * Consolidated update handler for Theme features.
 *
 * Filters `themes_api`, `pre_set_site_transient_update_themes`,
 * and `site_transient_update_themes` to provide update information
 * from the consolidation server.
 *
 * @since 1.0.0
 */
class Theme_Handler {

	use With_Debugging;

	/**
	 * The update data resolver.
	 *
	 * @since 1.0.0
	 *
	 * @var Resolve_Update_Data
	 */
	private Resolve_Update_Data $resolver;

	/**
	 * The feature repository.
	 *
	 * @since 1.0.0
	 *
	 * @var Feature_Repository
	 */
	private Feature_Repository $feature_repository;

	/**
	 * The license manager.
	 *
	 * @since 1.0.0
	 *
	 * @var License_Manager
	 */
	private License_Manager $license_manager;

	/**
	 * The legacy license repository.
	 *
	 * @since 1.3.0
	 *
	 * @var Legacy_License_Repository
	 */
	private Legacy_License_Repository $legacy_repository;

	/**
	 * Constructor for the consolidated theme update handler.
	 *
	 * @since 1.0.0
	 *
	 * @param Resolve_Update_Data       $resolver           The update data resolver.
	 * @param Feature_Repository        $feature_repository The feature repository.
	 * @param License_Manager           $license_manager    The license manager.
	 * @param Legacy_License_Repository $legacy_repository  The legacy license repository.
	 *
	 * @return void
	 */
	public function __construct(
		Resolve_Update_Data $resolver,
		Feature_Repository $feature_repository,
		License_Manager $license_manager,
		Legacy_License_Repository $legacy_repository
	) {
		$this->resolver           = $resolver;
		$this->feature_repository = $feature_repository;
		$this->license_manager    = $license_manager;
		$this->legacy_repository  = $legacy_repository;
	}

	/**
	 * Filters the themes_api response for Theme features.
	 *
	 * Resolves update data by joining the Feature_Repository and Catalog.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed       $result The current result (false or object).
	 * @param string|null $action The API action.
	 * @param object|null $args   The API request args.
	 *
	 * @return mixed
	 */
	public function filter_themes_api( $result, ?string $action = null, $args = null ) {
		if ( 'theme_information' !== $action || ! is_object( $args ) || empty( $args->slug ) ) {
			return $result;
		}

		// Cheap gate: skip when there is neither a Unified key nor any legacy entry opted into updates.
		// The downstream resolver still confirms each legacy entry's grant before granting availability.
		if ( empty( $this->license_manager->get_key() ) && ! $this->legacy_repository->any_used_for_updates() ) {
			return $result;
		}

		/** @var string $slug */
		$slug = $args->slug;

		// Check whether the requested slug belongs to a known Theme feature.
		$features = $this->feature_repository->get();

		if ( is_wp_error( $features ) ) {
			static::debug_log_wp_error(
				$features,
				'Theme_Handler::filter_themes_api: feature repository failed'
			);

			return $result;
		}

		$feature = $features->get( $slug );

		if ( $feature === null ) {
			return $result;
		}

		if (
			$feature instanceof Installable
			&& $feature->is_wporg()
		) {
			return $result;
		}

		$response = ( $this->resolver )( Feature::TYPE_THEME );

		if ( is_wp_error( $response ) ) {
			static::debug_log_wp_error(
				$response,
				sprintf(
					'Theme_Handler::filter_themes_api: resolver failed for "%s"',
					$slug
				)
			);

			return $result;
		}

		if ( empty( $response[ $slug ] ) ) {
			return $result;
		}

		return $this->to_wp_format( $slug, $response[ $slug ] );
	}

	/**
	 * Filters the update_themes transient to inject consolidated updates.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $transient The update_themes transient value.
	 *
	 * @return mixed
	 */
	public function filter_update_check( $transient ) {
		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}

		// Cheap gate: skip when there is neither a Unified key nor any legacy entry opted into updates.
		// The downstream resolver still confirms each legacy entry's grant before granting availability.
		if ( empty( $this->license_manager->get_key() ) && ! $this->legacy_repository->any_used_for_updates() ) {
			return $transient;
		}

		$response = ( $this->resolver )( Feature::TYPE_THEME );

		if ( is_wp_error( $response ) ) {
			static::debug_log_wp_error(
				$response,
				'Theme_Handler::filter_update_check: resolver failed'
			);

			return $transient;
		}

		if ( empty( $response ) ) {
			return $transient;
		}

		/**
		 * WordPress stores theme update data in two arrays on the transient object:
		 * - `response`: themes that have a newer version available.
		 * - `no_update`: themes that are up-to-date (checked, but no update).
		 *
		 * Both are keyed by stylesheet (theme directory name) and contain arrays (not objects).
		 */
		/** @var stdClass $transient */
		if ( ! property_exists( $transient, 'response' ) ) {
			$transient->response = [];
		}

		if ( ! property_exists( $transient, 'no_update' ) ) {
			$transient->no_update = [];
		}

		/** @var array<string, array<string, mixed>> $wp_response */
		$wp_response = $transient->response;
		/** @var array<string, array<string, mixed>> $wp_no_update */
		$wp_no_update = $transient->no_update;

		$installed_themes = wp_get_themes();

		foreach ( $response as $slug => $update_data ) {
			// Skip features that are not installed on this site.
			if ( ! isset( $installed_themes[ $slug ] ) ) {
				continue;
			}

			$update_array = $this->to_update_array( $slug, $update_data );

			/**
			 * Place the update data in `response` if a newer version is available.
			 * WordPress uses this distinction to show (or hide) the theme on the Updates page.
			 *
			 * When we don't have an update, only write to `no_update` if the theme isn't
			 * already in `response` from another system (e.g. legacy licensing) to avoid
			 * clearing updates we didn't provide.
			 */
			if ( $update_data['has_update'] ?? false ) {
				$wp_response[ $slug ] = $update_array;
				unset( $wp_no_update[ $slug ] );
			} elseif ( ! isset( $wp_response[ $slug ] ) ) {
				$wp_no_update[ $slug ] = $update_array;
			}
		}

		$transient->response  = $wp_response;
		$transient->no_update = $wp_no_update;

		return $transient;
	}

	/**
	 * Builds a WordPress-format theme info object for themes_api responses.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $slug        The theme slug.
	 * @param array<string, mixed> $update_data The update data from the consolidation server.
	 *
	 * @return stdClass
	 */
	private function to_wp_format( string $slug, array $update_data ): stdClass {
		$info = new stdClass();

		$info->name          = $update_data['name'] ?? '';
		$info->slug          = $slug;
		$info->version       = $update_data['version'] ?? '';
		$info->requires      = $update_data['requires'] ?? '';
		$info->tested        = $update_data['tested'] ?? '';
		$info->download_link = $update_data['package'] ?? '';
		$info->author        = $update_data['author'] ?? '';
		$info->homepage      = $update_data['url'] ?? '';
		$info->last_updated  = $update_data['last_updated'] ?? '';
		$info->sections      = $update_data['sections'] ?? [ 'description' => '' ];

		return $info;
	}

	/**
	 * Builds an update array for the update_themes transient.
	 *
	 * WordPress theme transients use arrays (not stdClass objects) keyed by stylesheet.
	 *
	 * @since 1.0.0
	 *
	 * @param string               $slug        The theme slug (stylesheet).
	 * @param array<string, mixed> $update_data The update data from the consolidation server.
	 *
	 * @return array<string, string>
	 */
	private function to_update_array( string $slug, array $update_data ): array {
		return [
			'theme'       => $slug,
			'new_version' => Cast::to_string( $update_data['version'] ?? '' ),
			'url'         => Cast::to_string( $update_data['url'] ?? '' ),
			'package'     => Cast::to_string( $update_data['package'] ?? '' ),
			'requires'    => Cast::to_string( $update_data['requires'] ?? '' ),
		];
	}
}
