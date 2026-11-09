<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\Functions\Actions;

/**
 * Registers a submenu item under a plugin's existing menu that links to the Harbor feature manager.
 *
 * @since 1.0.0
 */
class Register_Submenu {

	/**
	 * @param string $parent_slug The slug of the parent top-level menu item.
	 *
	 * @return void
	 */
	public function __invoke( string $parent_slug ): void {
		if (
			! did_action( 'lw_harbor/loaded' )
			/** This filter is documented in src/Harbor/Admin/Feature_Manager_Page.php */
			|| apply_filters( 'lw-harbor/hide_menu_item', false )
		) {
			return;
		}

		add_action(
			'admin_menu',
			static function () use ( $parent_slug ): void {
				$page_url = lw_harbor_get_license_page_url();

				if ( $page_url === '' ) {
					return;
				}

				add_submenu_page(
					$parent_slug,
					__( 'Nexcess Licensing', 'tribe-common' ),
					__( 'Licensing', 'tribe-common' ),
					'manage_options',
					$page_url
				);
			},
			PHP_INT_MAX
		);
	}
}
