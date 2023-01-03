<?php
/**
 * Achievement Upgrade Fix.
 *
 * @package BadgeOS
 */

global $wpdb;

$badgeos_settings   = badgeos_utilities::get_option( 'badgeos_settings' );
$default_point_type = ( ! empty( $badgeos_settings['default_point_type'] ) ) ? $badgeos_settings['default_point_type'] : 0;

$table_name = $wpdb->prefix . 'badgeos_achievements';

$wpdb->query( $wpdb->prepare( "update %s set point_type=%s where point_type=0 or point_type=''", $table_name, $default_point_type ) );

$results = $wpdb->get_results( $wpdb->prepare( "select * from %s where user_id!=0 and points>0 and rec_type='normal'", $table_name ) );
$count   = 0;

$table_name = $wpdb->prefix . 'badgeos_points';
$points     = $wpdb->get_results(
	$wpdb->prepare(
		'select * from %s where this_trigger like %s',
		$table_name,
		'm2dbold:%' . $res->entry_id . ':%'
	)
);

foreach ( $results as $res ) {
	if ( count( $points ) === 0 ) {
		$count++;
		$point_type = $default_point_type;
		if ( intval( $res->point_type ) > 0 ) {
			$point_type = $res->point_type;
		}

		$wpdb->insert(
			$wpdb->prefix . 'badgeos_points',
			array(
				'credit_id'      => $point_type,
				'step_id'        => $res->ID,
				'admin_id'       => 0,
				'user_id'        => $res->user_id,
				'achievement_id' => $res->ID,
				'type'           => 'Award',
				'credit'         => $res->points,
				'dateadded'      => $res->date_earned,
				'this_trigger'   => 'm2dbold:' . $res->entry_id . ':' . $res->this_trigger,
			)
		);
	}
}
echo esc_html( $count );
exit;

