<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Admin;

use TEC\Common\StellarWP\ContainerContract\ContainerInterface;
use TEC\Common\StellarWP\Uplink\Auth\Auth_Url_Builder;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Resources\Collection;
use TEC\Common\StellarWP\Uplink\Utils;

class Ajax {

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var Group
	 */
	protected $group;

	public function __construct( Group $group ) {
		$this->container = Config::get_container();
		$this->group     = $group;
	}

	/**
	 * @since 1.0.0
	 * @return void
	 */
	public function validate_license(): void {
		$submission = [
			'_wpnonce' => sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ?? '' ) ),
			'slug'     => sanitize_text_field( wp_unslash( $_POST['slug'] ?? '' ) ),
			'key'      => Utils\Sanitize::key( wp_unslash( $_POST['key'] ?? '' ) ),
		];

		if ( empty( $submission['key'] ) || ! wp_verify_nonce( $submission['_wpnonce'], $this->group->get_name() ) ) {
			wp_send_json_error( [
				'status'  => 0,
				'message' => __( 'Invalid request: nonce field is expired. Please try again.', 'tribe-common' ),
			] );
		}

		$collection = $this->container->get( Collection::class );
		$plugin     = $collection->offsetGet( $submission['slug'] );

		if ( ! $plugin ) {
			wp_send_json_error( [
				'message'    => sprintf(
					__( 'Error: The plugin with slug "%s" was not found. It is impossible to validate the license key, please contact the plugin author.', 'tribe-common' ),
					$submission['slug']
				),
				'submission' => $submission,
			] );
		}

		$results  = $plugin->validate_license( $submission['key'] );
		$message  = is_plugin_active_for_network( $plugin->get_path() ) ? $results->get_network_message()->get() : $results->get_message()->get();
		$auth_url = Config::get_container()
			->get( Auth_Url_Builder::class )
			->set_license( $submission['key'] )
			->build( $submission['slug'], get_site_url() );

		wp_send_json( [
			'status'  => absint( $results->is_valid() ),
			'message' => $message,
			'auth_url' => $auth_url,
		] );
	}

}
