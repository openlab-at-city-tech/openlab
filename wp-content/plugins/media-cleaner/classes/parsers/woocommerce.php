<?php

add_action( 'wpmc_scan_once', 'wpmc_scan_once_woocommerce' );
add_action( 'wpmc_scan_postmeta', 'wpmc_scan_postmeta_woocommerce' );

/**
 * Ensure database indexes exist for WooCommerce tables to improve query performance
 */
function wpmc_ensure_woocommerce_indexes() {
	global $wpdb;
	static $wpmc_indexes_checked = false;
	
	// Only check once per request to avoid repeated SHOW INDEX queries
	if ( $wpmc_indexes_checked ) {
		return;
	}
	
	// Check and create index on termmeta for meta_key queries
	$index_exists = $wpdb->get_var( "SHOW INDEX FROM $wpdb->termmeta WHERE Key_name = 'meta_key_value_idx'" );
	if ( !$index_exists ) {
		$wpdb->query( "CREATE INDEX meta_key_value_idx ON $wpdb->termmeta (meta_key, meta_value(20))" );
	}
	
	// Check and create index on term_taxonomy for taxonomy queries
	$taxonomy_index = $wpdb->get_var( "SHOW INDEX FROM $wpdb->term_taxonomy WHERE Key_name = 'taxonomy_desc_idx'" );
	if ( !$taxonomy_index ) {
		$wpdb->query( "CREATE INDEX taxonomy_desc_idx ON $wpdb->term_taxonomy (taxonomy, description(50))" );
	}
	
	// Check and create index on postmeta for meta_key queries
	$postmeta_index = $wpdb->get_var( "SHOW INDEX FROM $wpdb->postmeta WHERE Key_name = 'post_meta_key_value_idx'" );
	if ( !$postmeta_index ) {
		$wpdb->query( "CREATE INDEX post_meta_key_value_idx ON $wpdb->postmeta (post_id, meta_key, meta_value(20))" );
	}
	
	// Check and create index on posts for parent/type queries
	$posts_index = $wpdb->get_var( "SHOW INDEX FROM $wpdb->posts WHERE Key_name = 'parent_type_idx'" );
	if ( !$posts_index ) {
		$wpdb->query( "CREATE INDEX parent_type_idx ON $wpdb->posts (post_parent, post_type)" );
	}
	
	$wpmc_indexes_checked = true;
}

function wpmc_scan_once_woocommerce() {
	global $wpdb, $wpmc;

	// Ensure indexes exist for better performance
	wpmc_ensure_woocommerce_indexes();

	// WooCommerce Categories Images - check if any exist first
	$count_query = "SELECT COUNT(*) FROM $wpdb->termmeta WHERE meta_key LIKE '%thumbnail_id%' AND meta_value != '' AND meta_value IS NOT NULL";
	$meta_count = $wpdb->get_var( $count_query );
	
	if ( $meta_count > 0 ) {
		$query = "SELECT meta_value FROM $wpdb->termmeta WHERE meta_key LIKE '%thumbnail_id%' AND meta_value != '' AND meta_value IS NOT NULL";
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
	}

	// PlaceHolder Image ID
	$placeholder_id = get_option( 'woocommerce_placeholder_image', null, true );
	if ( !empty( $placeholder_id ) ) {
		$wpmc->add_reference_id( (int)$placeholder_id, 'WOOCOMMERCE (ID)' );
	}

	// Images in Product Category Descriptions - check if any exist first
	$desc_count_query = "SELECT COUNT(*) FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat' AND description <> '' AND description IS NOT NULL";
	$desc_count = $wpdb->get_var( $desc_count_query );
	
	if ( $desc_count > 0 ) {
		$query = "SELECT description FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat' AND description <> '' AND description IS NOT NULL";
		$descs = $wpdb->get_col( $query );
		
		if ( count( $descs ) > 0 ) {
			$postmeta_images_urls = [];
			foreach ( $descs as $desc ) {
				$postmeta_images_urls = array_merge( $postmeta_images_urls, $wpmc->get_urls_from_html( $desc ) );
			}
			$wpmc->add_reference_url( $postmeta_images_urls, 'WOOCOMMERCE (URL)' );
		}
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

	// Galleries - check if any exist first
	$galleries_images_wc = array();
	$id = (int)$id;
	
	$gallery_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = '_product_image_gallery' AND meta_value != '' AND meta_value IS NOT NULL", $id ) );
	
	if ( $gallery_count > 0 ) {
		$res = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = '_product_image_gallery' AND meta_value != '' AND meta_value IS NOT NULL", $id ) );

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
	}

	// Product Variations - check if any exist first
	$variations_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'", $id ) );

	if ( $variations_count > 0 ) {
		$variations = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'product_variation'", $id ) );

		foreach ( $variations as $variation_id ) {
			$gallery_variations = array();

			// Check if this variation has additional images before querying
			$variation_images_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = '_wc_additional_variation_images' AND meta_value != '' AND meta_value IS NOT NULL", $variation_id ) );
			
			if ( $variation_images_count > 0 ) {
				$res = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = '_wc_additional_variation_images' AND meta_value != '' AND meta_value IS NOT NULL", $variation_id ) );
				
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
}

?>