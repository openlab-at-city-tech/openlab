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
		$wpmc->add_reference_id( $postmeta_images_ids, 'WOOCOMMERCE (ID)' );
	}

	// PlaceHolder Image ID
	$placeholder_id = get_option( 'woocommerce_placeholder_image', null, true );
	if ( !empty( $placeholder_id ) ) {
		$wpmc->add_reference_id( (int)$placeholder_id, 'WOOCOMMERCE (ID)' );
	}

	// Images in Product Category Descriptions
	$query = "SELECT description FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat' AND description <> ''";
	$descs = $wpdb->get_col( $query );
	if ( count( $metas ) > 0 ) {
		$postmeta_images_urls = [];
		foreach ( $descs as $desc ) {
			$postmeta_images_urls = array_merge( $postmeta_images_urls, $wpmc->get_urls_from_html( $desc ) );
		}
		$wpmc->add_reference_url( $postmeta_images_urls, 'WOOCOMMERCE (URL)' );
	}
}

function wpmc_scan_postmeta_woocommerce( $id ) {
	global $wpdb, $wpmc;

	// Downloadable files
	$downloable_files = get_post_meta( $id, '_downloadable_files', true );
	if ( !empty( $downloable_files ) ) {
		foreach ( $downloable_files as $file ) {
			$wpmc->add_reference_url( $wpmc->clean_url( $file['file'] ), 'WOOCOMMERCE DOWNLOAD (URL)', $id );
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

	foreach ( $galleries_images_wc as $thumbnail_id ) {

		//* WooCommerce Gallery Images use srcset so the sizes URL are actually used
		$urls = $wpmc->get_thumbnails_urls_from_srcset( $thumbnail_id );
		$wpmc->add_reference_url( $urls, 'WOOCOMMERCE GALLERY (URL) {SAFE}', $id );
	}

	$wpmc->add_reference_id( $galleries_images_wc, 'WOOCOMMERCE GALLERY (ID)', $id );

	// Product Variations
	$variations = $wpdb->get_col( "SELECT ID FROM $wpdb->posts WHERE post_parent = $id AND post_type = 'product_variation'" );

	if ( count( $variations ) > 0 ) {
		foreach ( $variations as $variation_id ) {
			$gallery_variations = array();

			$res = $wpdb->get_col( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $variation_id
				AND meta_key = '_wc_additional_variation_images'" );
			
			if( !empty( $res ) ) {
				foreach ( $res as $values ) {
					$ids = explode( ',', $values );
					$gallery_variations = array_merge( $gallery_variations, $ids );
				}
			}

			// WooCommerce Gallery Images use srcset so the sizes URL are actually used
			foreach ( $gallery_variations as $thumbnail_id ) {
				if( empty( $thumbnail_id ) || !is_numeric( $thumbnail_id ) ) continue;

				$urls = $wpmc->get_thumbnails_urls_from_srcset( intval( $thumbnail_id ) );
				$wpmc->add_reference_url( $urls, 'WOOCOMMERCE VARIATIONS GALLERY (URL) {SAFE}', $id );
				$wpmc->add_reference_id(  $thumbnail_id, 'WOOCOMMERCE VARIATIONS (ID)', $id );
			}
		}
	}
}

?>