<?php
/**
 * Controller for Recipient activation virtual post.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Emails\Recipient_Activation
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Virtual_Posts\Recipient_Activation;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Virtual_Post;
use WPMUDEV_BLC\App\Webhooks\Recipient_Activation\Controller as Recipient_Activation;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Virtual_Posts\Recipient_Activation
 */
class Controller extends Virtual_Post {
	public function prepare_vars() {
		$this->post_title = __( 'Broken links reports', 'broken-link-checker' );
	}

	/**
	 * @param string $the_content
	 *
	 * @return string
	 */
	protected function post_content( string $the_content = '' ) {
		$activated_recipient = Recipient_Activation::instance()->activated_recipient;
		$action              = isset( $_GET['action'] ) && in_array(
			$_GET['action'],
			array(
				'activate',
				'cancel',
			)
		) ?
			$_GET['action'] : null;

		if ( empty( $activated_recipient ) || is_null( $action ) ) {
			global $wp_query;

			$wp_query->set_404();
			status_header( 404 );
			nocache_headers();
			include get_query_template( '404' );
			die();
		}

		ob_start();
		if ( 'activate' === $action ) {
			View::instance()->render_activation_message( $activated_recipient );
		} elseif ( 'cancel' === $action ) {
			View::instance()->render_cancellation_message( $activated_recipient );
		}

		return ob_get_clean();
	}

	public function can_load_virtual_post() {
		global $wp;

		return ! empty( $wp->query_vars ) && array_key_exists( Recipient_Activation::instance()->webhook_tag, $wp->query_vars );
	}
}
