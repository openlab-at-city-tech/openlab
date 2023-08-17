<?php
/**
 * Sets or Inserts fresh scan records in DB.
 * Used by
 *  Cron that syncs report data (WPMUDEV_BLC\App\Http_Requests\Sync_Scan_Results\Controller)
 *  Hub endpoint the sets report data (WPMUDEV_BLC\App\Hub_Endpoints\Set_Data\Controller)
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Scan_Models
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Scan_Models;

// Abort if called directly.
defined( 'WPINC' ) || die;

use stdClass;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\Core\Utils\Utilities;
use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use function is_multisite;
use function property_exists;
use function restore_current_blog;
use function switch_to_blog;

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Scan_Models
 */
class Scan_Data extends Base {
	/**
	 * Inserts new scan data to DB.
	 *
	 * @param string $json_data The data to insert needs to be in json format.
	 *
	 * @return bool
	 */
	public function set( string $json_data = '' ) {
		if ( empty( $json_data ) ) {
			Utilities::log( 'BLC_SET_SCAN_DATA_ERROR - Exiting because input does not contain any data.' );

			return false;
		}

		$unformated_input = json_decode( $json_data );

		// Make sure input is valid json.
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			Utilities::log( 'BLC_SET_SCAN_DATA_ERROR - Exiting because input was not valid json string.' );

			return false;
		}

		$params  = $unformated_input->params;
		$url     = property_exists( $params, 'url' ) ? $params->url : null;
		$site_id = property_exists( $params, 'site_id' ) ? intval( $params->site_id ) : null;

		if ( $site_id !== Utilities::site_id() ) {
			Utilities::log( 'BLC_SET_SCAN_DATA_ERROR - Exiting because BLC HUB API ping does not contain site id.' );

			return false;
		}

		$input          = $this->get_formatted_input( $params );
		$use_subsite_id = false;

		if ( empty( $input ) ) {
			return false;
		}

		if ( is_multisite() ) {
			$subside_id = Utilities::subsite_id_from_url( $url );

			if ( ! empty( $subside_id ) ) {
				$use_subsite_id = true;

				switch_to_blog( $subside_id );
			}
		}

		Settings::instance()->init();
		Settings::instance()->set( array( 'scan_results' => $input ) );
		Settings::instance()->set( array( 'scan_status' => 'completed' ) );
		Settings::instance()->save();

		if ( $use_subsite_id ) {
			restore_current_blog();
		}

		return true;
	}

	/**
	 * Returns an array of expected scan data values.
	 *
	 * @param stdClass $params The input object.
	 *
	 * @return array|null
	 */
	public function get_formatted_input( stdClass $params ) {
		//if ( ! empty( $params->scanning->is_running ) ) {
		//	Utilities::log( 'BLC_SET_SCAN_DATA_ERROR - Exiting because BLC HUB API scan is running currently.' );

		//	return null;
		//}

		$results = $params->last_result;

		return array(
			'broken_links_list' => $this->limit_links_number( $results->broken_links ),
			'broken_links'      => $results->num_broken_links ?? null,
			'succeeded_urls'    => $results->num_successful_links ?? null,
			'total_urls'        => $results->num_found_links ?? null,
			'unique_urls'       => $results->num_site_unique_links ?? null,
			'start_time'        => $results->start_unix_time_utc ?? null,
			'end_time'          => $results->ended_unix_time_utc ?? null,
			'duration'          => $results->scan_duration ?? null,
		);
	}

	/**
	 * Limits the number of links to be stored in db. Limit is set to 20.
	 *
	 * @param array $links_list An array with all broken links sent from Hub API.
	 *
	 * @return array|null
	 */
	public function limit_links_number( array $links_list = array() ) {
		if ( empty( $links_list ) ) {
			return null;
		}

		$links_limit = apply_filters( 'wpmudev_blc_settings_links_limit', 20, $links_list, $this );

		// Make sure we don't store ignored ones.
		// We only store the broken links to be sent to be sent from the
		if ( count( $links_list ) > $links_limit ) {
			$new_links_list = array();

			foreach( $links_list as $key => $broken_link ) {
				if ( property_exists( $broken_link, 'is_ignored' ) && ! empty( $broken_link->is_ignored ) ) {
					continue;
				}

				$new_links_list[] = $broken_link;
			}

			$links_list = $new_links_list;
		}

		return array_slice(
			$links_list,
			0,
			$links_limit
		);
	}
}
