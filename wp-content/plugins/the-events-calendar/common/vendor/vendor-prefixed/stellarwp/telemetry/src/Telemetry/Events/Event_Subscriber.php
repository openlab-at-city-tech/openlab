<?php
/**
 * Handles all actions and filters related to telemetry events.
 *
 * @since 2.1.0
 *
 * @package StellarWP\Telemetry
 */

namespace TEC\Common\StellarWP\Telemetry\Events;

use TEC\Common\StellarWP\Telemetry\Config;
use TEC\Common\StellarWP\Telemetry\Contracts\Abstract_Subscriber;

/**
 * Handles all actions and filters related to telemetry events.
 *
 * @since 2.1.0
 *
 * @package StellarWP\Telemetry
 */
class Event_Subscriber extends Abstract_Subscriber {

	/**
	 * @var array
	 */
	private static $events = [];

	/**
	 * @inheritDoc
	 *
	 * @since 2.1.0
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'shutdown', [ $this, 'send_cached_events' ] );
		add_action( 'stellarwp/telemetry/' . Config::get_hook_prefix() . 'event', [ $this, 'cache_event' ], 10, 2 );
		add_action( 'wp_ajax_' . Event::AJAX_ACTION, [ $this, 'send_events' ], 10, 1 );
		add_action( 'wp_ajax_nopriv_' . Event::AJAX_ACTION, [ $this, 'send_events' ], 10, 1 );
	}

	/**
	 * Caches an event to be sent during shutdown.
	 *
	 * @since 2.2.0
	 *
	 * @param string $name         The name of the event.
	 * @param array  $data         The data sent along with the event.
	 *
	 * @return void
	 */
	public function cache_event( $name, $data ) {
		self::$events[] = [
			'name'         => $name,
			'data'         => wp_json_encode( $data ),
			'stellar_slug' => Config::get_stellar_slug(),
		];
	}

	/**
	 * Sends the events that have been stored for the current request.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public function send_cached_events() {
		if ( empty( self::$events ) ) {
			return;
		}

		$url = admin_url( 'admin-ajax.php' );

		wp_remote_post(
			$url,
			[
				'blocking'  => false,
				'sslverify' => false,
				'body'      => [
					'action' => Event::AJAX_ACTION,
					'events' => self::$events,
				],
			]
		);

		self::$events = [];
	}

	/**
	 * Send the event to the telemetry server.
	 *
	 * @since 2.2.0
	 *
	 * @return void
	 */
	public function send_events() {
		// Get the passed event array.
		$events = filter_input( INPUT_POST, 'events', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ); // phpcs:ignore WordPressVIPMinimum.Security.PHPFilterFunctions.RestrictedFilter

		if ( empty( $events ) ) {
			return;
		}

		$this->container->get( Event::class )->send_batch( (array) $events );
	}
}
