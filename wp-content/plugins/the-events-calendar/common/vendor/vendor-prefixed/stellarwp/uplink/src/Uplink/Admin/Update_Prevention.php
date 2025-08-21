<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use WP_Error;
use WP_Upgrader;

/**
 * Class Update_Prevention engine for a plugin with invalid/empty keys
 *
 * @package TEC\Common\StellarWP\Uplink\Admin;
 */
class Update_Prevention {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	public function __construct() {
		$this->container = Config::get_container();
	}

	/**
	 * Checks for the list of constants associate with plugin to make sure we are dealing
	 * with a plugin owned by the plugin using this library.
	 *
	 * @since  4.9.12
	 *
	 * @param  string $plugin Plugin file partial path, folder and main php file.
	 *
	 * @return bool
	 */
	public function is_stellar_uplink_resource( string $plugin ): bool {
		$collection = $this->container->get( Collection::class );
		$resources  = $collection->get_by_path( $plugin );

		if ( ! $resources->count() ) {
			return false;
		}

		return (bool) $collection->get_plugins( $resources )->count();
	}

	/**
	 * Filters the source file location for the upgrade package for the PUE Update_Prevention engine.
	 *
	 * @since  4.9.12
	 *
	 * @param string|WP_Error $source        File source location.
	 * @param mixed           $remote_source Remote file source location.
	 * @param WP_Upgrader     $upgrader      WP_Upgrader instance.
	 * @param array<mixed>    $extras         Extra arguments passed to hooked filters.
	 *
	 * @return string|WP_Error
	 */
	public function filter_upgrader_source_selection( $source, $remote_source, WP_Upgrader $upgrader, array $extras ) {
		if ( ! isset( $extras['plugin'] ) ) {
			return $source;
		}

		$plugin = $extras['plugin'];

		// Bail if we are not dealing with a plugin we own.
		if ( ! $this->is_stellar_uplink_resource( $plugin ) ) {
			return $source;
		}

		$incompatible_plugins = apply_filters(
			'stellarwp/uplink/' . Config::get_hook_prefix(). '/update_prevention_incompatible_plugins',
			[],
			$source,
			$remote_source
		);

		// Bail when there are no incompatible plugins.
		if ( empty( $incompatible_plugins ) ) {
			return $source;
		}

		/**
		 * Filter if we should prevent the update.
		 *
		 * @since  4.9.12
		 *
		 * @param bool        $should_revent        Flag false to skip the prevention.
		 * @param string      $plugin               Plugin core file path
		 * @param array       $incompatible_plugins Which plugins were incompatible with new version of the plugin.
		 * @param string      $source               File source location.
		 * @param string      $remote_source        Remote file source location.
		 * @param WP_Upgrader $upgrader             WP_Upgrader instance.
		 * @param array       $extra                Extra arguments passed to hooked filters.
		 */
		$should_prevent_update = apply_filters(
			'stellarwp/uplink/' . Config::get_hook_prefix(). '/should_prevent_update_without_license',
			true,
			$plugin,
			$incompatible_plugins,
			$source,
			$remote_source,
			$upgrader,
			$extras
		);

		// Bail if the filter above returns anything but true.
		if ( true !== $should_prevent_update ) {
			return $source;
		}

		$full_plugin_path = $remote_source . '/' . $plugin;
		$plugin_data = get_plugin_data( $full_plugin_path );

		$link_read_more = '<a href="http://evnt.is/1aev" target="_blank">' . esc_html__( 'Read more', 'tribe-common' ) . '.</a>';

		$message = sprintf(
			esc_html__( 'Your update failed due to an incompatibility between the version (%1$s) of the %2$s you tried to update to. %3$s', 'tribe-common' ),
			esc_html( $plugin_data['Version'] ),
			esc_html( $plugin_data['Name'] ),
			$link_read_more
		);

		return new WP_Error(
			'stellarwp-uplink-updater-failed-prevention',
			$message,
			[]
		);
	}

}
