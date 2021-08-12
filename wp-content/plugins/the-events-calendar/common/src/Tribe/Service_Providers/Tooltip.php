<?php

namespace Tribe\Service_Providers;

/**
 * Class Tribe__Service_Providers__Tooltip
 *
 * @since 4.9.8
 *
 * Handles the registration and creation of our async process handlers.
 */
class Tooltip extends \tad_DI52_ServiceProvider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.9.8
	 */
	public function register() {
		tribe_singleton( 'tooltip.view', '\Tribe\Tooltip\View' );

		$this->hook();
	}

	/**
	 * Setup hooks for classes.
	 *
	 * @since 4.9.8
	 */
	private function hook() {
		add_action( 'tribe_common_loaded', [ $this, 'add_tooltip_assets' ] );
	}

	/**
	 * Register assets associated with tooltip
	 *
	 * @since 4.9.8
	 */
	public function add_tooltip_assets() {
		$main = \Tribe__Main::instance();

		tribe_asset(
			$main,
			'tribe-tooltip',
			'tooltip.css',
			[ 'tribe-common-skeleton-style' ],
			[ 'wp_register_style', 'admin_enqueue_scripts' ],
			[ 'groups' => 'tribe-tooltip' ]
		);

		tribe_asset(
			$main,
			'tribe-tooltip-js',
			'tooltip.js',
			[ 'jquery', 'tribe-common' ],
			[ 'wp_register_script', 'admin_enqueue_scripts' ],
			[ 'groups' => 'tribe-tooltip' ]
		);
	}
}
