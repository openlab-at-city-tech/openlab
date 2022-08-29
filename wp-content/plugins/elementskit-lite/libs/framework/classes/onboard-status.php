<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

use ElementsKit_Lite\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

class Onboard_Status {

	use Singleton;
	protected $optionKey   = 'elements_kit_onboard_status';
	protected $optionValue = 'onboarded';

	public function onboard() {

		add_action( 'elementskit/admin/after_save', array( $this, 'ajax_action' ) );
		
		if ( get_option( $this->optionKey ) ) {
			return true;
		}

		/**
		 * We are checking if the user current page is elementskit and if the user is not completing onboarding,
		 * We are redirecting to the onboarding page.
		 */
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking current page type. The page only can access admin. So nonce verification is not required.
		$param      = isset( $_GET['ekit-onboard-steps'] ) ? sanitize_text_field( wp_unslash( $_GET['ekit-onboard-steps'] ) ) : null;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Checking current page post_type. The page only can access admin. So nonce verification is not required.
		$requestUri = ( isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '' ) . ( isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '' );

		if ( strpos( $requestUri, 'elementskit' ) !== false && is_admin() ) {
			if ( $param !== 'loaded' && ! get_option( $this->optionKey ) ) {
				wp_safe_redirect( $this->get_onboard_url() );
				exit;
			}
		}

		return true;
	}

	public function ajax_action() {

		if( empty( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'ajax-nonce' ) ){
			return false;
		}

		// finish on-boarding
		$this->finish_onboard();

		if ( isset( $_POST['settings']['tut_term'] ) && $_POST['settings']['tut_term'] == 'user_agreed' ) {
			Plugin_Data_Sender::instance()->send( 'diagnostic-data' ); // send non-sensitive diagnostic data and details about plugin usage.
		}

		if ( isset( $_POST['settings']['newsletter_email'] ) && ! empty( $_POST['settings']['newsletter_email'] ) ) {

			$data = array(
				'email'           => sanitize_email( wp_unslash( $_POST['settings']['newsletter_email'] ) ),
				'environment_id'  => 1,
				'contact_list_id' => 1,
			);

			Plugin_Data_Sender::instance()->sendAutomizyData( 'email-subscribe', $data );
		}
	}

	private function get_onboard_url() {
		return add_query_arg(
			array(
				'page'               => 'elementskit',
				'ekit-onboard-steps' => 'loaded',
			),
			admin_url( 'admin.php' )
		);
	}

	public function redirect_onboard() {
		if ( ! get_option( $this->optionKey ) ) {
			wp_safe_redirect( $this->get_onboard_url() );
			exit;
		}
	}

	public function exit_from_onboard() {
		if ( get_option( $this->optionKey ) ) {
			wp_safe_redirect( $this->get_plugin_url() );
			exit;
		}
	}

	private static function get_plugin_url() {
		return add_query_arg(
			array(
				'page' => 'elementskit',
			),
			admin_url( 'admin.php' )
		);
	}

	public function finish_onboard() {
		if ( ! get_option( $this->optionKey ) ) {
			add_option( $this->optionKey, $this->optionValue );
		}
	}

}
