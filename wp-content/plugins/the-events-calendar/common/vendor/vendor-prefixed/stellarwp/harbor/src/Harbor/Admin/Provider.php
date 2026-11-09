<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\Admin;

use TEC\Common\LiquidWeb\Harbor\Contracts\Abstract_Provider;

class Provider extends Abstract_Provider {
	/**
	 * Register the service provider.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register() {
		$this->container->singleton( Feature_Manager_Page::class );

		add_action( 'admin_menu', [ $this, 'register_unified_feature_manager_page' ], 20, 0 );
	}

	/**
	 * Registers the unified feature manager page if this instance
	 * has the highest Harbor version among all active instances.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_unified_feature_manager_page(): void {
		$this->container->get( Feature_Manager_Page::class )->maybe_register_page();
	}
}
