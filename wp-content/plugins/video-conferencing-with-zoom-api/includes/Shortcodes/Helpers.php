<?php

namespace Codemanas\VczApi\Shortcodes;

/**
 * Class Helpers for Shortcode helper functions
 *
 * @package Codemanas\VczApi\Shortcodes
 */
class Helpers {

	/**
	 * Pagination
	 *
	 * @param $query
	 */
	public static function pagination( $query ) {
		$big = 999999999999999;
		if ( is_front_page() ) {
			$paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : 1;
		} else {
			$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
		}
		echo paginate_links( array(
			'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'  => '?paged=%#%',
			'current' => max( 1, $paged ),
			'total'   => $query->max_num_pages
		) );
	}

	/**
	 * Output only singel link
	 *
	 * @since  3.0.4
	 * @author Deepen
	 */
	public static function generate_link_only() {
		//Get Template
		vczapi_get_template( 'shortcode/zoom-single-link.php', true, false );
	}

	/**
	 * Get Meeting INFO
	 *
	 * @param $meeting_id
	 *
	 * @return bool|mixed|null
	 */
	public static function fetch_meeting( $meeting_id ) {
		$meeting = json_decode( zoom_conference()->getMeetingInfo( $meeting_id ) );
		if ( ! empty( $meeting->error ) ) {
			return false;
		}

		return $meeting;
	}

	/**
	 * Get a webinar detail
	 *
	 * @param $webinar_id
	 *
	 * @return bool|mixed|null
	 */
	public static function fetch_webinar( $webinar_id ) {
		$webinar = json_decode( zoom_conference()->getWebinarInfo( $webinar_id ) );

		return $webinar;
	}


}