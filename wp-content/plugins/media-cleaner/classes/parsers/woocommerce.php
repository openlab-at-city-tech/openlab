<?php

add_action( 'wpmc_scan_once', 'wpmc_scan_once_woocommerce' );
add_action( 'wpmc_scan_postmeta', 'wpmc_scan_postmeta_woocommerce' );

function wpmc_scan_once_woocommerce() {
	global $wpdb, $wpmc;

	// WooCommerce Categories Images
	$query = "SELECT meta_value FROM $wpdb->termmeta WHERE meta_key LIKE '%thumbnail_id%'";
	$metas = $wpdb->get_col( $query );
	if ( count( $metas ) > 0 ) {
		$postmeta_images_ids = array();
		foreach ( $metas as $meta ) {
			if ( is_numeric( $meta ) && $meta > 0 ) {
				array_push( $postmeta_images_ids, $meta );
			}
		}
		$wpmc->add_reference_id( $postmeta_images_ids, 'WOOCOOMMERCE (ID)' );
	}

	// PlaceHolder Image ID
	$placeholder_id = get_option( 'woocommerce_placeholder_image', null, true );
	if ( !empty( $placeholder_id ) ) {
		$wpmc->add_reference_id( (int)$placeholder_id, 'WOOCOOMMERCE (ID)' );
	}

	// Images in Product Category Descriptions
	$query = "SELECT description FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat' AND description <> ''";
	$descs = $wpdb->get_col( $query );
	if ( count( $metas ) > 0 ) {
		$postmeta_images_urls = [];
		foreach ( $descs as $desc ) {
			$postmeta_images_urls = array_merge( $postmeta_images_urls, $wpmc->get_urls_from_html( $desc ) );
		}
		$wpmc->add_reference_url( $postmeta_images_urls, 'WOOCOOMMERCE (URL)' );
	}
}

function wpmc_scan_postmeta_woocommerce( $id ) {
	global $wpdb, $wpmc;

	// Downloadable files
	$downloable_files = get_post_meta( $id, '_downloadable_files', true );
	if ( !empty( $downloable_files ) ) {
		foreach ( $downloable_files as $file ) {
			$wpmc->add_reference_url( $wpmc->clean_url( $file['file'] ), 'WOOCOOMMERCE DOWNLOAD (URL)', $id );
		}
	} 

	// Galleries
	$galleries_images_wc = array();
	$id = (int)$id;
	$res = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $id
		AND meta_key = '_product_image_gallery'" );
	foreach ( $res as $values ) {
		$ids = explode( ',', $values );
		$galleries_images_wc = array_merge( $galleries_images_wc, $ids );
	}
	$wpmc->add_reference_id( $galleries_images_wc, 'WOOCOOMMERCE GALLERY (ID)', $id );
}

?>