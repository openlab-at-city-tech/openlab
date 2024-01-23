<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class bradbury_notice {
	public $name;
	public $type;
	public $dismiss_url;
	public $current_user_id;

	/**
	 * The constructor.
	 *
	 * @param string $name Notice Name.
	 * @param string $type Notice type.
	 * @param string $dismiss_url Notice permanent dismiss URL.
	 *
	 */
	public function __construct( $name, $type, $dismiss_url ) {
		$this->name                  = $name;
		$this->type                  = $type;
		$this->dismiss_url           = $dismiss_url;
		$this->current_user_id       = get_current_user_id();
	}

	public function notice() {
		if ( ! $this->is_dismiss_notice() ) {
			$this->notice_markup();
		}
	}

	private function is_dismiss_notice() {
		return apply_filters( 'bradbury_' . $this->name . '_notice_dismiss', true );
	}

	public function notice_markup() {
		echo '';
	}

	public function get_notices() {

		$academiathemes_theme_admin_notices = get_option( 'bradbury_admin_notices' );
		return $academiathemes_theme_admin_notices;

	}

	public function get_notice_status($notice_id) {

		$theme_admin_notices = $this->get_notices();
		
		if ( is_array($theme_admin_notices) && in_array($notice_id, $theme_admin_notices) ) {
			$this_notice_was_dismissed = TRUE;
		} else {
			$this_notice_was_dismissed = FALSE;
		}		

		return $this_notice_was_dismissed;

	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {
		
		if ( isset( $_GET['bradbury-hide-notice'] ) && isset( $_GET['_bradbury_notice_nonce'] ) ) {
			
			if ( ! wp_verify_nonce( wp_unslash( $_GET['_bradbury_notice_nonce'] ), 'bradbury_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'bradbury' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'bradbury' ) );
			}

			if ( $_GET['bradbury-hide-notice'] ) {
				
				$hide_notice_id = sanitize_text_field( wp_unslash( $_GET['bradbury-hide-notice'] ) );

				$theme_admin_notices = $this->get_notices();

				if ( is_array($theme_admin_notices) ) {
					if ( !in_array($hide_notice_id, $theme_admin_notices) ) {
						// this notice has never been dismissed before
						$theme_admin_notices[] = $hide_notice_id;
						$run_update = TRUE;
					}
				} else {
					// This is the first time a theme admin notice is being dismissed.
					$theme_admin_notices = array();
					$theme_admin_notices[] = $hide_notice_id;
					$run_update = TRUE;
				}

				if ( isset($run_update) ) {
					update_option( 'bradbury_admin_notices', $theme_admin_notices );
				}

			}

		}
	}

}