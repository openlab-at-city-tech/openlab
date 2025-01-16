<?php
// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

/**
 * Uninstall this plugin
 */
class EPKB_Uninstall {

	public function __construct() {

        flush_rewrite_rules(false);

        delete_option( 'epkb_error_log' );
        delete_option( 'epkb_flush_rewrite_rules' );
        delete_option( 'amgr_error_log' );
		
        $delete_data = get_transient( '_epkb_delete_all_kb_data' );
        if ( ! empty( $delete_data ) ) {
			$this->uninstall_kb();
        }
    }

    /**
     * Removes ALL plugin data for KB #1
     * only when the relevant option is active
     *
     */
    private function uninstall_kb()     {
	    /** @global wpdb $wpdb */
	    global $wpdb;

        delete_option( 'epkb_version' );
		delete_option( 'epkb_last_seen_version' );
        delete_option( 'epkb_config_1' );
        delete_option( 'epkb_orignal_config_1' );
        delete_option( 'epkb_articles_sequence_1' );
        delete_option( 'epkb_categories_sequence_1' );
        delete_option( 'epkb_categories_icons_images_1' );
		delete_option( 'epkb_post_type_1_category_children' );
	    delete_option( 'epkb_one_time_notices' );
        delete_option( 'epkb_ongoing_notices' );
	    delete_option( 'epkb_long_notices' );
		delete_option( 'epkb_elementor_settings_dismissed' );
        delete_option( 'epkb_delete_all_kb_data' );
	    delete_option( 'epkb_flags' );
	    delete_option( 'epkb_openai_api_key' );
	    delete_option( 'epkb_openai_key' );
	    delete_option( 'epkb_ml_custom_css_1' );
	    delete_option( 'epkb_ml_faqs_kb_id_1' );
	    delete_option( 'epkb_ml_faqs_category_ids_1' );
	    delete_option( 'epkb_faq_group_ids_1' );
		delete_transient( '_epkb_plugin_activated' );
	    delete_transient( '_epkb_delete_all_kb_data' );

	    delete_option( 'asea_version' );
	    delete_option( 'asea_version_first' );
	    delete_option( 'asea_error_log' );
	    delete_option( 'asea_license_key' );
	    delete_option( 'asea_license_state' );

	    delete_option( 'elay_version' );
	    delete_option( 'elay_version_first' );
	    delete_option( 'elay_error_log' );
	    delete_option( 'elay_license_key' );
	    delete_option( 'elay_license_state' );

	    delete_option( 'eprf_version' );
	    delete_option( 'eprf_version_first' );
	    delete_option( 'eprf_error_log' );
	    delete_option( 'eprf_license_key' );
	    delete_option( 'eprf_license_state' );

	    delete_option( 'epie_version' );
	    delete_option( 'epie_version_first' );
	    delete_option( 'epie_error_log' );
	    delete_option( 'epie_license_key' );
	    delete_option( 'epie_license_state' );

	    delete_option( 'kblk_version' );
	    delete_option( 'kblk_version_first' );
	    delete_option( 'kblk_error_log' );
	    delete_option( 'kblk_license_key' );
	    delete_option( 'kblk_license_state' );

	    delete_option( 'emkb_version' );
	    delete_option( 'emkb_version_first' );
	    delete_option( 'emkb_error_log' );
	    delete_option( 'emkb_license_key' );
	    delete_option( 'emkb_license_state' );

	    delete_option( 'widg_version' );
	    delete_option( 'widg_version_first' );
	    delete_option( 'widg_error_log' );
	    delete_option( 'widg_license_key' );
	    delete_option( 'widg_license_state' );
		
		delete_option( '_epie_import_current_kb_id' );
		delete_option( '_epie_import_current_step' );
		delete_option( '_epie_import_selected_rows' );
		delete_option( '_epie_import_processed_count' );
	    delete_transient( '_epie_import_articles_to_import' );
    }
}

new EPKB_Uninstall();