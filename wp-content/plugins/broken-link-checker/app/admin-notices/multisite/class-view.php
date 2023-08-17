<?php
/**
 * BLC Dashboard admin page view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Notice\Multisite
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Admin_Notices\Multisite;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Admin_Notice\Multisite
 */
class View extends Base {
	/**
	 * Renders the output.
	 *
	 * @since 2.0.0
	 *
	 * @return void Renders the output.
	 */
	public function render( $params = array() ) {
		$use_legacy     = $params['use_legacy'] ?? false;
		$site_connected = $params['site_connected'] ?? false;


		echo '<div class="sui-wrap blc-multisite-notice-legacy">';

		if ( $use_legacy || ! $site_connected ) {
			$this->render_onboarding_notification();
		} else {
			$this->render_dashboard_notification();
		}

		echo '</div>';
	}

	public function render_onboarding_notification() {
		$message = __( 'Cloud Engine supports Multisite’s main site only and doesn’t support subsites. Subsites will continue using Local BLC', 'broken-link-checker' );

		printf( '
			<div class="wrap multisite-onboarding-notice notice notice-info is-dismissible">
				<span class="notice-content">%1$s</span>
			</div>
			',
			$message
		);
	}

	public function render_dashboard_notification() {
		$message       = __( 'Cloud Engine supports Multisite’s main site only and doesn’t support subsites. Subsites will continue using Local BLC', 'broken-link-checker' );
		$close_message = __( 'Dismiss', 'broken-link-checker' );

		printf( '
			<div role="alert" id="settingsSaved" class="sui-notice multisite-dashboard-notice sui-active sui-notice-yellow" aria-live="assertive" style="display: block; text-align: left;">
				<div class="sui-notice-content">
					<div class="sui-notice-message">
						<span class="sui-notice-icon sui-icon-info sui-md" aria-hidden="true"></span>
						<p>%1$s</p>
					</div>
					<div class="sui-notice-actions">
						<div class="sui-tooltip sui-tooltip-bottom" data-tooltip="%2$s">
							<button class="sui-button-icon notice-dismiss"><i class="sui-icon-check" aria-hidden="true"></i><span class="sui-screen-reader-text">%2$s</span></button>
						</div>
					</div>
				</div>
			</div>
			',
			$message,
			$close_message
		);
	}

	public function render_inline_script() {
		$ajax_nonce = wp_create_nonce( 'wpmudev-blc-multisite-notification-dismiss-nonce' );

		ob_start();
		?>
        (function($){
        $(document).ready(function() {
        $( '.blc-show-multisite-notice .blc-multisite-notice-legacy .notice-dismiss' ).on( 'click', function() {
        var data = {
        action: 'wpmudev_blc_multisite_notification_dismiss',
        security: '<?php echo $ajax_nonce; ?>',
        dismiss: true
        };

        $.post(ajaxurl, data, function(response) {
        $( '.blc-show-multisite-notice .blc-multisite-notice-legacy' ).hide( 300 );
        });
        } )

        });
        })(jQuery);
		<?php

		return ob_get_clean();
	}
}
