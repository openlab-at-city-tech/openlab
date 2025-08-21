<?php

namespace TEC\Common\StellarWP\Assets;

class Controller {
	/**
	 * @var Assets
	 */
	protected Assets $assets;

	/**
	 * Controller constructor.
	 *
	 * @param Assets $assets
	 */
	public function __construct( Assets $assets ) {
		$this->assets = $assets;
	}

	/**
	 * Register the actions and filters.
	 *
	 * @return void
	 */
	public function register() {
		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Add actions for the Assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_actions() {
		add_action( 'init', [ $this->assets, 'register_in_wp' ], 1, 0 );
	}

	/**
	 * Add filters for the Assets.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function add_filters() {
		add_filter( 'script_loader_tag', [ $this->assets, 'filter_tag_async_defer' ], 50, 2 );
		add_filter( 'script_loader_tag', [ $this->assets, 'filter_modify_to_module' ], 50, 2 );
		add_filter( 'script_loader_tag', [ $this->assets, 'filter_print_before_after_script' ], 100, 2 );

		// Enqueue late.
		add_filter( 'script_loader_tag', [ $this->assets, 'filter_add_localization_data' ], 500, 2 );
	}
}
