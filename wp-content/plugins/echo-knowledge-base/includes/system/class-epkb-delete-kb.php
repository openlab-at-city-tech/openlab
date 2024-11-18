<?php

/**
 * Delete All KB
 *
 */
class EPKB_Delete_KB {

	public function __construct() {

		add_action( 'wp_ajax_epkb_delete_all_kb_data', array( $this, 'delete_all_kb_data' ) );
		add_action( 'wp_ajax_nopriv_epkb_delete_all_kb_data', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Return HTML form to delete all KBs data
	 */
	public function get_delete_all_kbs_data_form() {

		// only administrators can handle this page
		if ( ! current_user_can('manage_options') ) {
			return '';
		}

		$already_deleted = get_transient( '_epkb_delete_all_kb_data' );

        ob_start(); ?>

        <div class="epkb-delete-all-data__message" style="<?php echo empty( $already_deleted ) ? 'display:none' : ''; ?>">
			<p><?php esc_html_e( 'All data successfully deleted', 'echo-knowledge-base' ); ?></p>
		</div>  <?php

        if ( empty( $already_deleted ) ) {  ?>
		    <form id="epkb-delete-all-data__form" class="epkb-delete-all-data__form" method="POST">
                <p class="epkb-delete-all-data__form-title">    <?php
	                echo sprintf( esc_html__( 'Write "%s" in the input box below if you want to immediatelly delete every Knowledge Base instance and all plugin data. ' .
	                                          'You cannot undo this action. Use this option only if you are removing this plugin from your site.', 'echo-knowledge-base' ), 'delete' ); ?>
                </p>    <?php
                EPKB_HTML_Elements::text_basic( array(
				    'value' => '',
				    'name'    => 'epkb_delete_text',
			    ) );
                EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Delete All Plugin Data', 'echo-knowledge-base' ), 'epkb_delete_all_kb_data', '', '', false, '', 'epkb-error-btn' );  ?>
            </form> <?php

			EPKB_HTML_Forms::dialog_confirm_action( array(
				'id'                => 'epkb-editor-delete-warning',
				'title'             => esc_html__( 'Delete KB content', 'echo-knowledge-base' ),
				'body'              => esc_html__( 'Are you sure you want to delete all data?', 'echo-knowledge-base' ),
				'accept_label'      => esc_html__( 'Yes', 'echo-knowledge-base' ),
				'accept_type'       => 'warning',
				'show_cancel_btn' 	=> 'yes',
			) );
        }

		return ob_get_clean();
	}

	/**
	 * Delete all data ajax action
	 */
	public function delete_all_kb_data() {
		/** @global wpdb $wpdb */
		global $wpdb;

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		// ensure user typed delete word
        $delete_text = EPKB_Utilities::post( 'delete_text' );
		if ( $delete_text != 'delete' ) {
			EPKB_Utilities::ajax_show_error_die( sprintf( esc_html__( 'Write "%s" in input box to delete ALL KB data', 'echo-knowledge-base' ), 'delete' ) );
		}

		$db_kb_config = new EPKB_KB_Config_DB();
		$all_kb_ids = $db_kb_config->get_kb_ids();
		foreach ( $all_kb_ids as $kb_id ) {
			self::delete_kb_data( $kb_id );
		}

		// Remove all database tables
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "epkb_kb_search_data" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "epkb_article_ratings" );

		set_transient( '_epkb_delete_all_kb_data', true, DAY_IN_SECONDS );

		wp_die( wp_json_encode( array(
			'status'  => 'success',
			'message' => esc_html__( 'All articles and categories deleted. Options will be deleted when plugin is uninstalled.', 'echo-knowledge-base' ),
		) ) );
	}

	/**
	 * Delete given KB data
	 * @param $kb_id
	 */
	private function delete_kb_data( $kb_id ) {

		// delete all KB post type posts
		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		$kb_posts = get_posts( array(
				'post_type'   => $post_type,
				'post_status' => 'any',
				'posts_per_page' => -1,
			)
		);
		if ( ! empty($kb_posts) ) {
			foreach ($kb_posts as $post) {
				if ( EPKB_KB_Handler::is_kb_post_type($post->post_type) && $post->post_type == $post_type ) {
					wp_delete_post($post->ID, true);
				}
			}
		}

		// delete all KB categories and terms
		$kb_category = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$kb_tag = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );

		// Delete all KB CATEGORIES
		$terms = get_terms( array( 'hide_empty' => false, 'taxonomy' => $kb_category ) );
		if ( ! is_wp_error($terms) && is_array($terms) ) {
			foreach( $terms as $term ) {
				if ( isset($term->term_id) && $term->taxonomy == $kb_category )
					wp_delete_term( $term->term_id, $term->taxonomy );
			}
		}

		// Delete all KB TERMS
		$terms = get_terms( array( 'hide_empty' => false, 'taxonomy' => $kb_tag ) );
		if ( ! is_wp_error($terms) && is_array($terms) ) {
			foreach( $terms as $term ) {
				if ( isset($term->term_id) && $term->taxonomy == $kb_tag )
					wp_delete_term( $term->term_id, $term->taxonomy );
			}
		}
	}

	/**
	 * Return HTML for Archive/Delete KB form
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	public static function get_archive_or_delete_kb_form( $kb_config ) {
		ob_start();
		do_action('eckb_admin_config_page_overview_actions', $kb_config );
		return ob_get_clean();
	}

	/**
	 * Return HTML for Reset Configuration button
	 *
	 * @return false|string
	 */
	public static function get_reset_config_button() {
		ob_start(); ?>

		<form class="epkd-reset-all-configs__form" action="" method="post"> <?php
			EPKB_HTML_Elements::submit_button_v2( esc_html__( 'Reset Configuration', 'echo-knowledge-base' ), 'epkb_reset_config', '', '', true, '', 'epkb-error-btn' );   ?>
		</form>     <?php

		return ob_get_clean();
	}

	/**
	 * Handle request for Reset Configuration button if defined
	 *
	 * @param $kb_id
	 * @return void
	 */
    public static function reset_config_button_handler( $kb_id ) {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$action = EPKB_Utilities::post( 'action' );
		if ( $action != 'epkb_reset_config' ) {
			return;
		}

		// reset KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $kb_config );

		// open Setup Wizard
		if ( ! is_wp_error( $result ) ) {			?>
			<script type="text/javascript">
				window.location.href="<?php echo esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ) ) . '&page=epkb-kb-configuration&setup-wizard-on' ); ?>";
			</script>            <?php
			exit;
		}
    }
}