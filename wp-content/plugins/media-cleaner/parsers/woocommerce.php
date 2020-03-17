<?php

add_action( 'wpmc_scan_once', 'wpmc_scan_once_woocommerce' );
add_action( 'wpmc_scan_postmeta', 'wpmc_scan_postmeta_woocommerce' );

// Only on Start: Analyze WooCommerce Categories Images
function wpmc_scan_once_woocommerce() {
	global $wpdb, $wpmc;
	$query = "SELECT meta_value
		FROM $wpdb->termmeta
		WHERE meta_key LIKE '%thumbnail_id%'";
	$metas = $wpdb->get_col( $query );
	if ( count( $metas ) > 0 ) {
		$postmeta_images_ids = array();
		foreach ( $metas as $meta )
			if ( is_numeric( $meta ) && $meta > 0 )
				array_push( $postmeta_images_ids, $meta );
		$wpmc->add_reference_id( $postmeta_images_ids, 'WOOCOOMMERCE (ID)' );
	}

	$placeholder_id = get_option( 'woocommerce_placeholder_image', null, true );
	if ( !empty( $placeholder_id ) )
		$wpmc->add_reference_id( (int)$placeholder_id, 'WOOCOOMMERCE (ID)' );
}

function wpmc_scan_postmeta_woocommerce( $id ) {
	global $wpdb, $wpmc;
	$galleries_images_wc = array();
	$res = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $id
		AND meta_key = '_product_image_gallery'" );
	foreach ( $res as $values ) {
		$ids = explode( ',', $values );
		$galleries_images_wc = array_merge( $galleries_images_wc, $ids );
	}
	$wpmc->add_reference_id( $galleries_images_wc, 'WOOCOOMMERCE (ID)' );
}

?>