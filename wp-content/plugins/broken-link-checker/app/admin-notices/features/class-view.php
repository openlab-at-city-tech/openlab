<?php
/**
 * BLC Dashboard admin page view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notice\Features
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Features;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Notice\Features
 */
class View extends Base {
	/**
	 * Renders the output.
	 *
	 * @since 2.1
	 *
	 * @return void Renders the output.
	 */
	public function render( $params = array() ) {
		$this->render_body();
	}

	public function render_body() {
		$header      = __( 'Introducing Edit Link and Unlink features in Broken Link Checker!', 'broken-link-checker' );
		$message     = __( 'Now you can edit links and unlink broken links directly from the new Cloud version with ease.', 'broken-link-checker' );
		$current_url = site_url() . $_SERVER['REQUEST_URI'];
		$target_url  = add_query_arg( array(
			'highlights_shown' => true,
			'redirect'         => urlencode( $current_url ),
			'nonce'            => wp_create_nonce( 'blc_highlights_shown' ),
		), $current_url );

		printf( '
			<div id="activate-features-blc-notice" class="wrap wrap-blc-features-notice activate-features-blc-notice notice-info">
				<table>
	                <tr>
	                    <td class="blc-icon-wrap"><div class="sui-notice-icon blc-icon" aria-hidden="true"></div></td>
	                    <td class="features-wrap">
	                    	<h2>%1$s</h2>
	                    	<p>%2$s</p>
						</td>
						<td class="features-close-wrap">
							<a href="%3$s">
								<span class="sui-icon-cross-close" aria-hidden="true"></span>
							</a>
						</td>
	                </tr>
	            </table>
			</div>
			',
			$header,
			$message,
			$target_url
		);
	}
}
