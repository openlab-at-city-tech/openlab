<?php

// Max Mega Menu (https://wordpress.org/plugins/megamenu/)
// Added by Mike Meinz
//

add_action('wpmc_scan_widgets', 'wpmc_scan_widgets_maxmegamenu');

function wpmc_scan_widgets_maxmegamenu() {
	global $wpmc;
	global $wpdb;
	$urls = array();
	$q = "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_menu_item_url' and LENGTH(TRIM(meta_value)) > 0;";
	$rows = $wpdb->get_col($q);
	if ( $wpdb->last_error ) {
		error_log($q . " " . $wpdb->last_error);
		$wpmc->log($q . " " . $wpdb->last_error);
		die($wpdb->last_error);
	}

	if ( count( $rows ) > 0 ) {
		foreach( $rows as $metavalue ) {
			if ( ( !empty( $metavalue ) ) && $wpmc->is_url( $metavalue ) ) {
				$url = $wpmc->clean_url( $metavalue );
				if ( !empty( $url ) ) {
					array_push( $urls, $url );
				}
			}
		}
	}

	if ( !empty( $urls ) ) {
		  $wpmc->add_reference_url( $urls, 'MENU (URL)' );
		}
	}

?>