<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contains methods to force reset data
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Reset {

	public function __construct() {
		add_action( 'wp_ajax_epkb_reset_sequence', array( $this, 'reset_articles_and_categories_sequence' ) );
		add_action( 'wp_ajax_nopriv_epkb_reset_sequence', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_show_sequence', array( $this, 'show_articles_and_categories_sequence' ) );
		add_action( 'wp_ajax_nopriv_epkb_show_sequence', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user clicks to reset sequences for Articles and Categories
	 */
	public static function reset_articles_and_categories_sequence() {

		// is set in request
		$kb_id = EPKB_KB_Handler::get_current_kb_id();

		delete_option( 'epkb_articles_sequence_' . $kb_id );
		delete_option( 'epkb_categories_sequence_' . $kb_id );

		// show error on fail or continue
		$result = self::update_articles_sequence( $kb_id );
		if ( ! $result ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 501 ) );
		}
		$updated_sequence = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( is_wp_error( $updated_sequence ) ) {
			EPKB_Utilities::ajax_show_error_die( $updated_sequence->get_error_message() );
		}
		if ( empty( $updated_sequence ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 502 ) );
		}

		// show error on fail or continue
		$result = self::update_categories_sequence( $kb_id );
		if ( ! $result ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 503 ) );
		}
		$updated_sequence = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( is_wp_error( $updated_sequence ) ) {
			EPKB_Utilities::ajax_show_error_die( $updated_sequence->get_error_message() );
		}
		if ( empty( $updated_sequence ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 502 ) );
		}

		// show success message
		EPKB_Utilities::ajax_show_info_die( esc_html__( 'The sequence for Articles and Categories was successfully reset.', 'echo-knowledge-base' ) );
	}

	/**
	 * Update sequences for Articles and Categories
	 *
	 * @param $kb_id
	 * @return bool
	 */
	public static function update_articles_and_categories_sequence( $kb_id ) {

		// log error on fail or continue
		$result = self::update_articles_sequence( $kb_id );
		if ( ! $result ) {
			EPKB_Logging::add_log( 'Could not update Articles sequence (501).', $kb_id );
			return false;
		}
		$updated_sequence = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
		if ( $updated_sequence === null ) {
			EPKB_Logging::add_log( 'Could not update Articles sequence (502).', $kb_id );
			return false;
		}

		// log error on fail or continue
		$result = self::update_categories_sequence( $kb_id );
		if ( ! $result ) {
			EPKB_Logging::add_log( 'Could not update Categories sequence (503).', $kb_id );
			return false;
		}
		$updated_sequence = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
		if ( $updated_sequence === null ) {
			EPKB_Logging::add_log( 'Could not update Categories sequence (504).', $kb_id );
			return false;
		}

		return true;
	}

	/**
	 * Update Articles sequence based on title or creation date
	 * - if sequence is user defined but is incorrect, then sequence will be re-set based on title or creation date
	 *
	 * @param $kb_id
	 * @return bool
	 */
	private static function update_articles_sequence( $kb_id ) {
		$article_admin = new EPKB_Articles_Admin();
		return $article_admin->update_articles_sequence( $kb_id );
	}

	/**
	 * Update Categories sequence based on name or creation date
	 * - if sequence is user defined but is incorrect, then sequence will be re-set based on name or creation date
	 *
	 * @param $kb_id
	 * @return bool
	 */
	private static function update_categories_sequence( $kb_id ) {
		$category_admin = new EPKB_Categories_Admin();
		$category_taxonomy_slug = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		return $category_admin->update_categories_sequence( 0, 0, $category_taxonomy_slug );
	}

	/**
	 * Return configuration array for Reset Sequence admin settings box
	 *
	 * @return array
	 */
	public static function get_reset_sequence_box_config() {
		return array(
			'title' => esc_html__( 'Refresh Articles and Categories sequence', 'echo-knowledge-base' ),
			'html'  => EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Reset Sequence', 'echo-knowledge-base' ), 'epkb_reset_sequence', 'epkb_reset_sequence', '', true, true, 'epkb-primary-btn' ) .
			           EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Show Sequence', 'echo-knowledge-base' ), 'epkb_show_sequence', 'epkb_show_sequence', '', true, true, 'epkb-primary-btn' ) . '
						<div class="epkb-show-sequence-wrap"></div>'
		);
	}

	public static function show_articles_and_categories_sequence() {

		// is set in request
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

		$article_sequence = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );

		if ( empty( $article_sequence ) ) {
			EPKB_Utilities::ajax_show_error_die( 'No article sequence found.' );
		}

		$category_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, array(), true );

		if ( empty( $category_seq_data ) ) {
			EPKB_Utilities::ajax_show_error_die( 'No category sequence found.' );
		}

		ob_start(); ?>

		<div class="epkb-show-sequence-header"><?php esc_html_e( 'Article Sequence', 'echo-knowledge-base' ); ?></div>
		<div class="epkb-show-sequence-list">
			<div class="epkb-show-sequence-item epkb-show-sequence-item--title">
				<div class="epkb-show-sequence-category_name"><?php esc_html( _x( 'Category', 'taxonomy singular name' ) ); ?></div>
				<div class="epkb-show-sequence-category_articles"><?php esc_html_e( 'Articles', 'echo-knowledge-base' ); ?></div>
			</div>
			<?php self::show_categories_sequence_tree( $category_seq_data, $article_sequence ); ?>
		</div><?php

		$html = ob_get_clean();
		wp_die( wp_json_encode( [ 'html' => $html, 'message' => '' ] ) );
	}

	private static function show_categories_sequence_tree( $category_seq_data, $article_sequence, $depth = 0 ) {
		if ( empty( $category_seq_data ) ) {
			return;
		}

		$depth++;

		foreach( $category_seq_data as $category_id => $cat ) {
			$category = $article_sequence[$category_id]; ?>

			<div class="epkb-show-sequence-item epkb-show-sequence-item--depth-<?php echo esc_attr( $depth ); ?>">
				<div class="epkb-show-sequence-category_name"  style="border-left-width: <?php echo esc_attr( $depth * 10 ); ?>px;">
					<a href="<?php echo esc_url( get_term_link( $category_id ) ); ?>" target="_blank"><?php echo esc_html( $category[0] ); ?></a>
					<div class="epkb-show-sequence-category_description"><?php echo esc_html( $category[1] ); ?></div>
				</div>

				<ul class="epkb-show-sequence-category_articles"><?php

					if ( count( $category ) < 3 ) { ?>
						<li><?php esc_html_e( 'No Articles', 'echo-knowledge-base' ); ?></li><?php
					}

					foreach ( $category as $article_id => $article_title ) {
						if ( $article_id == 0 || $article_id == 1 ) {
							continue;
						} ?>

						<li><a href="<?php echo esc_url( get_the_permalink( $article_id ) ); ?>" target="_blank"><?php echo esc_html( $article_title ); ?></a></li><?php
					} ?>
				</ul>
			</div> <?php

			self::show_categories_sequence_tree( $cat, $article_sequence, $depth );
		}
	}
}
