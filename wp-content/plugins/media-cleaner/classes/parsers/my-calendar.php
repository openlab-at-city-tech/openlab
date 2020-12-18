<?php

// My Calendar (https://wordpress.org/plugins/my-calendar/)
// Added by Mike Meinz
//

add_action( 'wpmc_scan_widgets', 'wpmc_scan_widgets_mycalendar' );

function wpmc_scan_widgets_mycalendar() {
	global $wpmc;
	global $wpdb;
	$eventurls = array();
	$q = "SELECT event_desc, event_short, event_link, event_url, event_image FROM " . $wpdb->prefix . "my_calendar WHERE
		 (LOWER(event_desc) like '%http%' or
		 LOWER(event_short) like '%http%' or
		 LOWER(event_link) like 'http%' or
		 LOWER(event_image) like 'http%' or
		 LOWER(event_url) like 'http%');";
	$rows = $wpdb->get_results( $q, ARRAY_N );
	if ( $wpdb->last_error ) {
		error_log( $q . " " . $wpdb->last_error );
		$wpmc->log( $q . " " . $wpdb->last_error );
		die( $wpdb->last_error );
	}
	if ( count( $rows ) > 0 ) {
		foreach ( $rows as $row ) {
			if ( !empty($row[0]) ) { // event_desc
				$urls =  $wpmc->get_urls_from_html( $row[0] );
				$eventurls = array_merge( $eventurls, $urls);
			}
			if ( !empty($row[1]) ) { // event_short
				$urls = $wpmc->get_urls_from_html( $row[1] );
				$eventurls = array_merge( $eventurls, $urls);
			}
			if ( !empty($row[2]) ) { // event_link
				array_push( $eventurls, $wpmc->clean_url( $row[2] ) );
			}
			if ( !empty($row[3]) ) { // event_url
				array_push( $eventurls, $wpmc->clean_url( $row[3] ) );
			}
			if ( !empty($row[4]) ) { // event_image
				array_push( $eventurls, $wpmc->clean_url( $row[4] ) );
			}

		}
	}

	if ( !empty( $eventurls ) ) {
		$wpmc->add_reference_url( $eventurls, 'CALENDAR (URL)' );
	}
}

?>