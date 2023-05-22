<?php
/**
 * BLC Dashboard admin page view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notice\Local
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Local;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

/**
 * NOTE
 * This Admin Notice is not going to be used. Keeping it as an archive temporarily.
 */

/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Notice\Local
 */
class View extends Base {
	/**
	 * Renders the output.
	 *
	 * @return void Renders the output.
	 * @since 2.1
	 *
	 */
	public function render( $params = array() ) {
		$this->render_body();
	}

	public function render_body() {
		if ( Settings::instance()->get( 'use_legacy_blc_version' ) ) {
			return;
		}

		$dashborad_url = admin_url( 'admin.php?page=blc_dash' );
		$message       = sprintf(
			__( 'Cloud-based Broken Link Checker is currently active on your site. <a href="%1$s">Activate the old version</a> to go back to using Local BLC.', 'broken-link-checker' ),
			$dashborad_url
		);

		printf( '
			<div id="activate-local-blc-notice" class="wrap wrap-blc-local-notice activate-local-blc-notice notice-info">
				<table>
	                <tr>
	                    <td><span class="sui-icon-warning-alert" aria-hidden="true"></span></td>
	                    <td>%1$s</td>
	                </tr>
	            </table>
			</div>
			',
			$message
		);
	}
}
