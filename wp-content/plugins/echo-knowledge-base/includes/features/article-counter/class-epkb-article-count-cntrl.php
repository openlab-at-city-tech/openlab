<?php

/**
 * Process front-end Ajax operations.
 */
class EPKB_Article_Count_Cntrl {

	public function __construct() {
		add_action( 'wp_ajax_epkb_count_article_view', array( $this, 'process_article_count' ) );
		add_action( 'wp_ajax_nopriv_epkb_count_article_view', array( $this, 'process_article_count' ) );
	}

	/**
	 * Record article view
	 */
	public function process_article_count() {

		// check wpnonce
		$wp_nonce = EPKB_Utilities::post( '_wpnonce_epkb_ajax_action' );
		if ( empty( $wp_nonce ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $wp_nonce ) ), '_wpnonce_epkb_ajax_action' ) ) {
			wp_die();
		}

		$article_id = EPKB_Utilities::get( 'article_id' );
		if ( empty( $article_id ) ) {
			wp_die();
		}

		EPKB_Article_Count_Handler::maybe_increase_article_count( $article_id );
		wp_die();
	}
}