<?php
/**
 * BLC Dashboard admin page view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notice\Legacy
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Legacy;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Notice\Legacy
 */
class View extends Base {
	/**
	 * Renders the output.
	 *
	 * @return void Renders the output.
	 * @since 2.0.0
	 *
	 */
	public function render( $params = array() ) {
		// For now, we won't be using the notice. We will keep only the button to switch to Cloud mode.
		//$this->render_body();
	}

	public function render_body() {
		$dashborad_url = admin_url( 'admin.php?page=blc_dash' );
		$message       = sprintf(
			__( 'We have completely rebuilt BLC with a new cloud-based engine. It’s now 20x faster, more accurate, and works perfectly with any site. Plus, no page limits, no ads, and it’s still 100%% free! Check out Broken Link Checker\'s <a href="%1$s">new dashboard</a>.', 'broken-link-checker' ),
			$dashborad_url
		);

		printf( '
			<div class="wrap wrap-blc-legacy-notice notice notice-info">
				<table>
	                <tr>
	                    <td><span class="sui-notice-icon blc-icon sui-md" aria-hidden="true"></span></td>
	                    <td>%1$s</td>
	                </tr>
	            </table>
			</div>
			',
			$message
		);
	}
}
