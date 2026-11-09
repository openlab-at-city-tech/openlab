<?php declare( strict_types=1 );

namespace TEC\Common\LiquidWeb\Harbor\API\Functions\Actions;

use TEC\Common\LiquidWeb\Harbor\Config;
use TEC\Common\LiquidWeb\Harbor\Notice\Notice;
use TEC\Common\LiquidWeb\Harbor\Notice\Notice_Controller;
use TEC\Common\LiquidWeb\Harbor\Traits\With_Debugging;
use Throwable;

/**
 * Displays an informational notice on a plugin's legacy license settings page.
 *
 * Informs users that licensing is now managed centrally through Nexcess's
 * unified system while the legacy page remains available for older licenses.
 *
 * @since 1.0.0
 */
class Display_Legacy_License_Page_Notice {

	use With_Debugging;

	/**
	 * @since 1.0.0
	 *
	 * @param string $product_name Optional human-readable product name (e.g. "GiveWP", "Kadence").
	 *                             When omitted, a generic message is displayed.
	 *
	 * @return void
	 */
	public function __invoke( string $product_name = '' ): void {
		try {
			$url = lw_harbor_get_license_page_url();

			if ( $product_name !== '' ) {
				$message = sprintf(
					/* translators: 1: product name (e.g. "GiveWP"), 2: URL to the Nexcess Licensing page. */
					__(
						'%1$s is now part of Nexcess\'s software offerings. This page is still available for managing legacy licenses from your previous %1$s account. If you purchased a new plan through Nexcess, your products are managed through <a href="%2$s">Nexcess Licensing</a>.',
						'tribe-common'
					),
					esc_html( $product_name ),
					esc_url( $url )
				);
			} else {
				$message = sprintf(
					/* translators: %s is the URL to the Nexcess Licensing page. */
					__(
						'This plugin is now part of Nexcess\'s software offerings. This page is still available for managing legacy licenses from your previous account. If you purchased a new plan through Nexcess, your products are managed through <a href="%s">Nexcess Licensing</a>.',
						'tribe-common'
					),
					esc_url( $url )
				);
			}

			$notice = new Notice( Notice::INFO, $message );
			Config::get_container()->get( Notice_Controller::class )->render( $notice->to_array() );
		} catch ( Throwable $e ) {
			$this->debug_log_throwable( $e, 'Error displaying legacy license page notice' );
		}
	}
}
