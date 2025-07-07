<?php
/**
 * Contains all event related functionality.
 *
 * @since 2.1.0
 *
 * @package StellarWP\Telemetry
 */

namespace TEC\Common\StellarWP\Telemetry\Events;

use TEC\Common\StellarWP\Telemetry\Config;
use TEC\Common\StellarWP\Telemetry\Telemetry\Telemetry;

/**
 * The class that handles user triggered events.
 *
 * @since 2.1.0
 *
 * @package StellarWP\Telemetry
 */
class Event {

	/**
	 * The hook name for sending events asyncronously.
	 *
	 * @since 2.2.0
	 */
	public const AJAX_ACTION = 'stellarwp_telemetry_send_event';

	/**
	 * An instance of the Telemetry class.
	 *
	 * @since 2.1.0
	 *
	 * @var \TEC\Common\StellarWP\Telemetry\Telemetry\Telemetry
	 */
	private $telemetry;

	/**
	 * The class constructor.
	 *
	 * @since 2.1.0
	 *
	 * @param Telemetry $telemetry An instance of the Telemetry class.
	 */
	public function __construct( Telemetry $telemetry ) {
		$this->telemetry = $telemetry;
	}

	/**
	 * Sends an event to the telemetry server.
	 *
	 * @since 2.1.0
	 *
	 * @param string $name The name of the event.
	 * @param array  $data Additional information to include with the event.
	 *
	 * @return bool
	 */
	public function send( string $name, array $data = [] ) {
		$data = [
			'token'        => $this->telemetry->get_token(),
			'stellar_slug' => Config::get_stellar_slug(),
			'event'        => $name,
			'event_data'   => wp_json_encode( $data ),
		];

		/**
		 * Provides the ability to filter event data before it is sent to the telemetry server.
		 *
		 * @since 2.1.0
		 *
		 * @param array $data The data about to be sent.
		 */
		$data = apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'event_data', $data );

		$response = $this->telemetry->send( $data, $this->get_url() );

		if ( ! isset( $response['status'] ) ) {
			return false;
		}

		return boolval( $response['status'] );
	}

	/**
	 * Send batched events.
	 *
	 * @since 2.2.0
	 *
	 * @param array $events An array of stored events to send to the telemetry server.
	 *
	 * @return bool
	 */
	public function send_batch( array $events ) {
		$data = [
			'token'  => $this->telemetry->get_token(),
			'events' => $events,
		];

		$response = $this->telemetry->send( $data, $this->get_url() );

		if ( ! isset( $response['status'] ) ) {
			return false;
		}

		return boolval( $response['status'] );
	}

	/**
	 * Gets the url used for sending events.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	protected function get_url() {
		$events_url = Config::get_server_url() . '/events';

		/**
		 * Filters the url used to send events to the telemetry server.
		 *
		 * @since 2.1.0
		 *
		 * @param string $event_url The events endpoint url.
		 */
		return apply_filters( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'events_url', $events_url );
	}
}
