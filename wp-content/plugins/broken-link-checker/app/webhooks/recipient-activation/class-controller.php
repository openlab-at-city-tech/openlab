<?php
/**
 * Controller for Recipient activation webhook.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Webhooks\Recipient_Activation
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Webhooks\Recipient_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\App\Users\Recipients\Model as Recipients;
use WPMUDEV_BLC\Core\Controllers\Webhook;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Emails\Recipient_Activation
 */
class Controller extends Webhook {
	/**
	 * The webhook tag.
	 *
	 * @var string $webhook The webhook tag
	 */
	public $webhook_tag = 'blc-activate-recipient';

	/**
	 * The activated recipient data in an array. Retrieved by activation code.
	 *
	 * @var array $activated_recipient
	 */
	public $activated_recipient = array();

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
		$this->webhook_title = __( 'Activate broken links recipient', 'broken-link-checker' );
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
		$activation_key = $_GET['activation_code'] ?? null;
		$action         = isset( $_GET['action'] ) && in_array( $_GET['action'], array(
			'activate',
			'cancel'
		) ) ?
			$_GET['action'] : null;

		if ( is_null( $action ) ) {
			return;
		}

		$this->activated_recipient = Recipients::get_recipient_by_key( $activation_key );

		if ( empty( $this->activated_recipient ) ) {
			return;
		}

		switch ( $action ) {
			case 'activate' :
				if ( $this->set_recipient_status( $this->activated_recipient ) ) {
					$this->activated_recipient['cancellation_link'] = add_query_arg( array(
						'action'          => 'cancel',
						'activation_code' => esc_html( $activation_key ),
					), $this->webhook_url() );
				}
				break;
			case 'cancel' :
				if ( isset( $this->activated_recipient['user_id'] ) ) {
					$this->unset_registered_recipient( intval( $this->activated_recipient['user_id'] ) );
				} else {
					$this->set_recipient_status( $this->activated_recipient, false );
				}
				break;
		}
	}

	/**
	 * Changes the recipient status for recipients added by email.
	 *
	 * @param array $recipient_data
	 * @param bool $new_status
	 *
	 * @return bool
	 */
	protected function set_recipient_status( array $recipient_data = array(), bool $new_status = true ) {
		if (
			empty( $recipient_data ) ||
			! isset( $recipient_data['email'] ) ||
			! isset( $this->settings['schedule'] ) ||
			( empty( $this->settings['schedule']['emailrecipients'] ) && empty( $this->settings['schedule']['registered_recipients_data'] ) )
		) {
			return false;
		}

		Settings::instance()->init();

		$this->settings['schedule']['emailrecipients'] = array_map(
			function ( $recipient ) use ( $recipient_data, $new_status ) {
				if ( isset( $recipient['email'] ) && $recipient['email'] === $recipient_data['email'] ) {
					$recipient['confirmed'] = boolval( $new_status );
				}

				return $recipient;
			},
			$this->settings['schedule']['emailrecipients']
		);

		Settings::instance()->set( array( 'schedule' => $this->settings['schedule'] ) );
		Settings::instance()->save();

		return true;
	}

	/**
	 * Removes a recipient by user id.
	 *
	 * @param int|null $user_id
	 *
	 * @return false|void
	 */
	protected function unset_registered_recipient( int $user_id = null ) {
		if ( empty( $user_id ) ) {
			return false;
		}

		$user_key = array_search(
			$user_id,
			$this->settings['schedule']['recipients']
		);

		if (
			! is_numeric( $user_key ) ||
			empty( $this->settings['schedule']['recipients'][ $user_key ] ) ||
			intval( $this->settings['schedule']['recipients'][ $user_key ] ) !== $user_id
		) {
			return false;
		}

		unset( $this->settings['schedule']['recipients'][ $user_key ] );

		$this->settings['schedule']['recipients'] = array_values( $this->settings['schedule']['recipients'] );

		unset( $this->settings['schedule']['registered_recipients_data'][ $user_id ] );

		Settings::instance()->set( array( 'schedule' => $this->settings['schedule'] ) );
		Settings::instance()->save();
	}
}
