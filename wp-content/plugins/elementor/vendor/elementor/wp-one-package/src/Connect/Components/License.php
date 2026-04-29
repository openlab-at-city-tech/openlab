<?php

namespace ElementorOne\Connect\Components;

use ElementorOne\Connect\Facade;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class License
 */
class License {

	/**
	 * Status active
	 * @var string
	 */
	const STATUS_ACTIVE = 'ACTIVE';

	/**
	 * Facade instance
	 * @var Facade
	 */
	protected Facade $facade;

	/**
	 * Hook name
	 * @var string
	 */
	protected $hook_name;

	/**
	 * License constructor
	 * @param Facade $facade
	 */
	public function __construct( Facade $facade ) {
		$this->facade = $facade;

		$app_prefix = $this->facade->get_config( 'app_prefix' );
		$this->hook_name = 'elementor_one/' . $app_prefix . '_license_info_hook';

		$license_check_enabled = apply_filters( 'elementor_one/' . $app_prefix . '_license_check_enabled', true );
		if ( ! $license_check_enabled ) {
			return;
		}

		if ( $this->facade->utils()->is_connected() ) {
			add_action( 'admin_init', [ $this, 'schedule_license_check' ] );
			add_action( $this->hook_name, [ $this, 'check_license' ] );
		}

		add_action( 'elementor_one/' . $app_prefix . '_connected', [ $this, 'on_connect' ] );
		add_action( 'elementor_one/' . $app_prefix . '_deactivated', [ $this, 'on_deactivation' ] );
		add_action( 'elementor_one/' . $app_prefix . '_disconnected', [ $this, 'on_deactivation' ] );
	}

	/**
	 * Check license
	 * Runs at most once every 12 hours unless $force is true.
	 *
	 * @param bool $force If true, bypasses the 12-hour lock and runs the check.
	 * @return void
	 */
	public function check_license( bool $force = false ): void {
		if ( ! $force && get_transient( $this->get_lock_key() ) ) {
			return;
		}

		try {
			$license_info = $this->facade->service()->get_license_info();
			$license_is_valid = self::STATUS_ACTIVE === ( $license_info['status'] ?? null );
			if ( ! $license_is_valid ) {
				$this->facade->service()->deactivate_license();
			}
		} catch ( \Throwable $th ) {
			if ( in_array( $th->getCode(), [ \WP_Http::UNAUTHORIZED, \WP_Http::FORBIDDEN ], true ) ) {
				$this->facade->data()->clear_session();
			}
		}

		set_transient( $this->get_lock_key(), true, 12 * HOUR_IN_SECONDS );
	}

	/**
	 * Get lock key
	 * @return string
	 */
	private function get_lock_key(): string {
		return 'elementor_one_' . $this->facade->get_config( 'app_prefix' ) . '_license_check_lock';
	}

	/**
	 * Schedule license check
	 * @return void
	 */
	public function schedule_license_check(): void {
		if ( ! wp_next_scheduled( $this->hook_name ) ) {
			wp_schedule_event( time(), 'hourly', $this->hook_name );
		}
	}

	/**
	 * On connect
	 * @return void
	 */
	public function on_connect(): void {
		$this->schedule_license_check();
	}

	/**
	 * On deactivation
	 * @return void
	 */
	public function on_deactivation(): void {
		wp_clear_scheduled_hook( $this->hook_name );
		delete_transient( $this->get_lock_key() );
	}
}
