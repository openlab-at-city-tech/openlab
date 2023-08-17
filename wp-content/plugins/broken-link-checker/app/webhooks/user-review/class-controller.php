<?php
/**
 * Controller for validating user to review and redirect to wp org.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Webhooks\User_Review
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Webhooks\User_Review;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Controllers\Webhook;
use WPMUDEV_BLC\App\Users\Recipients\Model as Recipients;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Emails\User_Review
 */
class Controller extends Webhook {

	//public $webhook = 'broken-link-checker-review';

	/**
	 * The webhook tag.
	 *
	 * @var string $webhook The webhook tag
	 */
	public $webhook_tag = 'user-review';

	/**
	 * The registered user's / recipient's data in an array. Retrieved by activation code.
	 *
	 * @var array $user_recipient
	 */
	public $user_recipient = array();

	/**
	 * The plugin settings.
	 *
	 * @var array $settings
	 */
	public $settings = array();

	/**
	 * Prepares the class properties.
	 *
	 * @return void
	 */
	public function prepare_vars() {
		$this->webhook_title = esc_html__( 'Review Broken Link Checker plugin', 'broken-link-checker' );
	}

	/**
	 * Executes the webhook action(s).
	 *
	 * @param $wp
	 *
	 * @return void
	 */
	public function webhook_action( &$wp ) {
		$this->settings = Settings::instance()->get();
		$token = $_GET['token'] ?? null;

		if ( empty( $token ) ) {
			wp_die(
				esc_html__( 'It seems there is some missing input', 'broken-link-checker' ),
				esc_html__( 'Invalid action', 'broken-link-checker' )
			);
		}

		$this->user_recipient = Recipients::get_recipient_by_key( $token );

		if ( empty( $this->user_recipient ) || empty( $this->user_recipient['user_id'] ) ) {
			wp_die(
				esc_html__( 'No user found for given input data.', 'broken-link-checker' ),
				esc_html__( 'Invalid action', 'broken-link-checker' )
			);
		}

		Recipients::flag_recipient_reviewed( $this->user_recipient['user_id'] );

		wp_redirect( 'https://wordpress.org/support/plugin/broken-link-checker/reviews/#new-post' );

	}


}
