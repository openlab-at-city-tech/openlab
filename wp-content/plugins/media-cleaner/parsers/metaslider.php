<?php

add_action( 'wpmc_scan_widgets', 'wpmc_scan_widgets_metaslider' );

function wpmc_scan_widgets_metaslider() {
	global $wpdb;
	global $wpmc;
	$q = "SELECT object_id
		FROM {$wpdb->term_relationships}
		WHERE object_id > 0
		AND term_taxonomy_id
		IN (SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy = 'ml-slider');";
	$imageIds = $wpdb->get_col( $q );
	if ( $wpdb->last_error ) {
		error_log( $q . " " . $wpdb->last_error );
		$wpmc->log( $q . " " . $wpdb->last_error );
		die( $wpdb->last_error );
	}
	if ( count( $imageIds) > 0 ) {
		$wpmc->add_reference_id( $imageIds, 'METASLIDER (ID)' );
	}
}
?>