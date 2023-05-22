<?php
/**
 * The Option model
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Options\Settings
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Options\Settings;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Models\Option;
use WPMUDEV_BLC\Core\Utils\Utilities;
use function array_filter;

/**
 * Class Settings
 *
 * @package WPMUDEV_BLC\App\Options\Settings
 */
class Model extends Option {
	/**
	 * Default options. Optional
	 *
	 * @since 2.0.0
	 * @var string|array|null $option_keys
	 */
	public $default = array(
		/*
		 * Scan status (scan_status) :
		 *  none : Never started
		 *  in_progress : Is currently running
		 *  completed : Has been completed
		 */
		'scan_status'                   => 'none',
		'site_connected'                => false,
		'activation_modal_shown'        => false,
		'use_legacy_blc_version'        => true,
		'blc_schedule_scan_in_progress' => false,
		'show_multisite_notice'         => true,
		'installation_timestamp'        => null,
		'v2_activation_request'         => false,
		'schedule'                      => array(
			'active'                     => false,
			'recipients'                 => array(),
			'registered_recipients_data' => array(),
			'emailRecipients'            => array(),
			'frequency'                  => 'daily',
			'days'                       => array(),
			'monthdays'                  => array(),
			'time'                       => '00:00',
		),
		'scan_results'                  => array(
			/*
			 * List of broken links. Storing to be used in Scan Report Emails. Stores limited number links configured
			 *  in `WPMUDEV_BLC\App\Scan_Models\limit_links_number()`
			 */
			'broken_links_list',
			'broken_links'   => null,
			'succeeded_urls' => null,
			'total_urls'     => null,
			'unique_urls'    => null,
			'start_time'     => null,
			'end_time'       => null,
			'duration'       => null,
		),
	);
	/**
	 * The option_name.
	 *
	 * @since 2.0.0
	 * @var string $name
	 */
	protected $name = 'blc_settings';

	/**
	 * Returns the scheduled scan email recipients that have been confirmed.
	 *
	 * @return array.
	 */
	public function get_scan_active_email_recipients() {
		$schedule = $this->get( 'schedule' );

		if ( empty( $schedule ) ) {
			return array();
		}

		$email_recipients = (array) $schedule['emailrecipients'] ?? array();

		$active_recipients = array_filter(
			$email_recipients,
			function ( array $recipient = array() ) {
				return isset( $recipient['confirmed'] ) && $recipient['confirmed'];
			}
		);

		return $active_recipients;
	}

	public function get( string $settings_key = null, string $option_name = null, $default = null, bool $force = false ) {
		if ( Utilities::is_subsite() && 'use_legacy_blc_version' === $settings_key ) {
			return false;
		}

		return parent::get( $settings_key, $option_name, $default, $force );
	}
}
