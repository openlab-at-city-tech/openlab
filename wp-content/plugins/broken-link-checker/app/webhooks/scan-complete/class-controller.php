<?php
/**
 * Controller for Scan completed webhook.
 * Generates a custom webhook http://site.com/broken-link-checker-scan/blc-scan-complete.
 * This can be used as a return url for api to send requests when Scan is completed.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Webhooks\Scan_Complete
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Webhooks\Scan_Complete;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Controllers\Webhook;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Emails\Scan_Complete
 * 
 * @todo Maybe delete this file.
 */
class Controller extends Webhook {
	/**
	 * The webhook.
	 *
	 * @var string $webhook The webhook
	 */
	public $webhook = 'broken-link-checker-scan';

	/**
	 * The webhook tag.
	 *
	 * @var string $webhook The webhook tag
	 */
	public $webhook_tag = 'blc-scan-complete';

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
		$this->webhook_title = __( 'Scan results', 'broken-link-checker' );
	}

	/**
	 * Executes the webhook action(s).
	 *
	 * @param $wp
	 *
	 * @return void
	 */
	public function webhook_action( &$wp ) {
		$this->settings            = Settings::instance()->get();
		$scan_data = $_POST['scan_data'] ?? null;

		if ( ! empty( $scan_data ) ) {
			// Get scan values
		}

		die(0);
	}
}
