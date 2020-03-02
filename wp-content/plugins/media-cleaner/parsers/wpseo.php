<?php

add_action( 'wpmc_scan_postmeta', 'wpmc_scan_postmeta_wpseo', 10, 1 );

function wpmc_scan_postmeta_wpseo( $id ) {
	global $wpmc;
  $data = get_post_meta( $id, '_yoast_wpseo_opengraph-image', true );
	if ( !empty( $data ) )
		$wpmc->add_reference_url( $data, 'META (URL)' );
  $data = get_post_meta( $id, '_yoast_wpseo_opengraph-image-id', true );
  if ( !empty( $data ) )
		$wpmc->add_reference_id( $data, 'META (ID)' );
}

?>