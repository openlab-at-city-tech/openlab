<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Cron\Actions;

use TEC\Common\LiquidWeb\Harbor\Portal\Catalog_Repository;
use TEC\Common\LiquidWeb\Harbor\Cron\ValueObjects\CronHook;
use TEC\Common\LiquidWeb\Harbor\Features\Types\Feature;

use function get_stylesheet;
use function get_template;
use function is_plugin_active;
use function is_plugin_active_for_network;

/**
 * Unschedules the data refresh cron event when no catalog plugins or themes remain active.
 *
 * Reads the stored catalog from the DB (no API call) and cross-references its
 * plugin and theme features against the active plugins/theme. If none match, the
 * event is removed. The cron will be rescheduled on the next page load via init
 * if any Harbor instance is still active.
 *
 * Conservative defaults: when the catalog is unreadable or contains no installable
 * features, the event is left in place since we cannot confirm Harbor is gone.
 *
 * @since 1.0.0
 */
class Handle_Unschedule_Cron_Data_Refresh {

	/**
	 * @since 1.0.0
	 *
	 * @var Catalog_Repository
	 */
	private Catalog_Repository $catalog;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Catalog_Repository $catalog The catalog repository.
	 */
	public function __construct( Catalog_Repository $catalog ) {
		$this->catalog = $catalog;
	}

	/**
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __invoke(): void {
		if ( $this->has_active_catalog_feature() ) {
			return;
		}

		wp_clear_scheduled_hook( CronHook::DATA_REFRESH );
	}

	/**
	 * Check whether any plugin or theme listed in the stored catalog is still active.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	private function has_active_catalog_feature(): bool {
		$catalog = $this->catalog->get_cached();

		if ( $catalog === null ) {
			return true;
		}

		$found_catalog_feature = false;

		foreach ( $catalog as $product_catalog ) {
			foreach ( $product_catalog->get_features() as $catalog_feature ) {
				$type = $catalog_feature->get_kind();

				if ( $type === Feature::TYPE_PLUGIN ) {
					$plugin_file = $catalog_feature->get_plugin_file();

					if ( $plugin_file === null ) {
						continue;
					}

					$found_catalog_feature = true;

					if ( is_plugin_active( $plugin_file ) || is_plugin_active_for_network( $plugin_file ) ) {
						return true;
					}
				} elseif ( $type === Feature::TYPE_THEME ) {
					$found_catalog_feature = true;
					$slug                  = $catalog_feature->get_slug();

					if ( get_stylesheet() === $slug || get_template() === $slug ) {
						return true;
					}
				}
			}
		}

		// If the catalog has no installable features we cannot determine whether
		// Harbor is still needed, so leave the cron in place.
		if ( ! $found_catalog_feature ) {
			return true;
		}

		return false;
	}
}
