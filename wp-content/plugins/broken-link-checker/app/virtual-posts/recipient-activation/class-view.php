<?php
/**
 * Virtual post for Recipient Activation view.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Admin_Pages\Settings
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Virtual_Posts\Recipient_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;


/**
 * Class View
 *
 * @package WPMUDEV_BLC\App\Virtual_Posts\Recipient_Activation
 */
class View extends Base {

	/**
	 * Render the output.
	 *
	 * @since 2.0.0
	 *
	 * @return void Render the output.
	 */
	public function render_activation_message( $params = array() ) {
		$cancellation_link   = $params['cancellation_link'] ?? '';
		$cancelation_message = $this->cancellation_instructions_message( $cancellation_link );

		?>
		<div style="width: 100%; margin: auto;" class="blc-vpost blc-activation-message">
			<h4>
				<?php esc_html_e( 'You have been added in broken links reports recipients.', 'broken-link-checker' ); ?>
			</h4>
			<p>
				<?php
				printf(
				/* translators: 1: Recipient name 2: Opening link tag 3; Closing link tag */
					esc_html__(
						'Hi %1$s. You have become a recipient of site\'s broken links reports. You can continue to site from %2$shere%3$s.',
						'broken-link-checker'
					),
					$params['name'],
					'<a href="' . site_url() . '">',
					'</a>'
				);
				?>
			</p>

			<p>
				<?php echo $cancelation_message; ?>
			</p>
		</div>
		<?php
	}

	protected function cancellation_instructions_message( string $cancellation_link = '' ) {
		$message = '';

		if ( ! empty( $cancellation_link ) ) {
			$message = sprintf(
			/* translators: 1: Opening link tag 2; Closing link tag */
				esc_html__(
					'If you do not wish to receive these reports in your email, you can contact site admins or click %1$shere to cancel email reports instantly%2$s.',
					'broken-link-checker'
				),
				'<a href="' . esc_html( $cancellation_link ) . '">',
				'</a>'
			);
		} else {
			$message = esc_html__(
				'If you do not wish to receive these reports in your email, you can contact site admins.',
				'broken-link-checker'
			);
		}

		return $message;
	}

	public function render_cancellation_message( $params = array() ) {
		?>
		<div style="width: 100%; margin: auto;" class="blc-vpost blc-activation-message">
			<h4>
				<?php
				esc_html_e( 'Your email has been successfully removed from broken links recipients list.', 'broken-link-checker' );
				?>
			</h4>
			<p>
				<?php
				printf(
				/* translators: 1: Recipient name 2: Opening link tag 3; Closing link tag */
					esc_html__(
						'Hi %1$s. You should stop receiving broken links reports for this site. You can visit site from %2$shere%3$s.',
						'broken-link-checker'
					),
					$params['name'],
					'<a href="' . site_url() . '">',
					'</a>'
				);
				?>
			</p>

		</div>
		<?php
	}
}
