<?php declare( strict_types=1 );

namespace TEC\Common\StellarWP\Uplink\Auth;

use TEC\Common\StellarWP\Uplink\Auth\Admin\Disconnect_Controller;
use TEC\Common\StellarWP\Uplink\Auth\Admin\Connect_Controller;
use TEC\Common\StellarWP\Uplink\Auth\Token\Contracts\Token_Manager;
use TEC\Common\StellarWP\Uplink\Config;
use TEC\Common\StellarWP\Uplink\Contracts\Abstract_Provider;
use TEC\Common\StellarWP\Uplink\Storage\Contracts\Storage;

final class Provider extends Abstract_Provider {

	/**
	 * @inheritDoc
	 */
	public function register() {
		if ( ! $this->container->has( Config::TOKEN_OPTION_NAME ) ) {
			return;
		}

		$this->container->singleton(
			Token_Manager::class,
			static function ( $c ) {
				return new Token\Token_Manager( $c->get( Config::TOKEN_OPTION_NAME ) );
			}
		);

		$this->register_nonce();
		$this->register_auth_connect_disconnect();
	}

	/**
	 * Register nonce container definitions.
	 *
	 * @return void
	 */
	private function register_nonce(): void {
		/**
		 * Filter how long the callback nonce is valid for.
		 *
		 * @note There is also an expiration time in the Uplink Origin plugin.
		 *
		 * Default: 35 minutes, to allow time for them to properly log in.
		 *
		 * @param int $expiration Nonce expiration time in seconds.
		 */
		$expiration = apply_filters( 'stellarwp/uplink/' . Config::get_hook_prefix() . '/auth/nonce_expiration', 2100 );
		$expiration = absint( $expiration );

		$this->container->singleton( Nonce::class, static function( $c ) use ( $expiration ) {
			return new Nonce( $c->get( Storage::class ), $expiration );
		} );
	}

	/**
	 * Register token auth connection/disconnection definitions and hooks.
	 *
	 * @return void
	 */
	private function register_auth_connect_disconnect(): void {
		$this->container->singleton( Disconnect_Controller::class, Disconnect_Controller::class );
		$this->container->singleton( Connect_Controller::class, Connect_Controller::class );
		$this->container->singleton( Action_Manager::class, Action_Manager::class );

		$action_manager = $this->container->get( Action_Manager::class );

		// Register a unique action for each resource slug.
		add_action( 'admin_init', [ $action_manager, 'add_actions' ], 1 );

		// Execute the above actions when an uplink_slug query variable.
		add_action( 'admin_init', [ $action_manager, 'do_action' ], 9 );
	}

}
