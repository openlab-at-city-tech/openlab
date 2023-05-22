<?php
/**
 * An enpoint where Hub can send requests and retrive scan schedule data.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Hub_Endpoints\Set_Data
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Hub_Endpoints\Set_Data;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Controllers\Hub_Endpoint;
use WPMUDEV_BLC\App\Scan_Models\Scan_Data;
use WPMUDEV_BLC\App\Emails\Scan_Report\Controller as ReportMailer;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Hub_Endpoint\Scan_Data
 */
class Controller extends Hub_Endpoint {
	/**
	 * Sets the endpoint's action vars to be used by Dash plugin.
	 */
	protected function setup_action_vars() {
		$this->endpoint_action_name     = 'blc_set_data';
		$this->endpoint_action_callback = 'set_scan_data';
	}

	/**
	 * Prints a json string with schedule data.
	 */
	public function set_scan_data() {
		$input_json = file_get_contents( 'php://input' );

		if ( ! Scan_Data::instance()->set( $input_json ) ) {
			$this->output_formatted_response(
				array(
					'code'    => 'ERROR_SET_SCAN_DATA',
					'message' => 'Something went wrong when saving scan data.',
					'data'    => '',
				),
				false
			);
		} else {
			if ( Settings::instance()->get( 'blc_schedule_scan_in_progress' ) ) {
				ReportMailer::instance()->init();
				ReportMailer::instance()->send_email();
				Settings::instance()->set( array( 'blc_schedule_scan_in_progress' => false ) );
				Settings::instance()->save();
			}

			$this->output_formatted_response(
				array(
					'data_received' => true,
					'data_stored'   => true,
				),
				true
			);
		}
	}

}
