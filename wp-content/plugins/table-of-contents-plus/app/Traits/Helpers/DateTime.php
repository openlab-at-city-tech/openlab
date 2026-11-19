<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains date/time specific helper methods.
 *
 * @since 1.0.0
 */
trait DateTime {
	/**
	 * Returns a MySQL formatted date.
	 *
	 * @since 1.0.0
	 *
	 * @param  int|string   $time Any format accepted by strtotime.
	 * @return false|string       The MySQL formatted string.
	 */
	public function timeToMysql( $time ) {
		$time = is_string( $time ) ? strtotime( $time ) : $time;

		return date( 'Y-m-d H:i:s', $time ); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
	}
}