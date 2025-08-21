<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle AJAX calls for FAQs
 */
class EPKB_FAQs_AJAX {

    public function __construct() {
        add_action( 'wp_ajax_epkb_faq_get_shortcode', array( $this, 'epkb_handle_shortcode_preview' ) );
    }

    /**
     * Handle AJAX request for FAQ shortcode preview
     */
    public function epkb_handle_shortcode_preview() {

		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

        // Get shortcode parameters
        $params = EPKB_Utilities::post( 'shortcode_params', array(), 'db-config' );
        $is_cached = EPKB_Utilities::post( 'is_cached', 'false' ) == 'true';
        $is_initial_load = EPKB_Utilities::post( 'is_initial_load', 'false' ) == 'true';
        
        // Sanitize shortcode parameters
        $sanitized_params = array();
        foreach ( $params as $key => $value ) {
            if ( $key === 'group_ids' && is_array( $value ) ) {
                $sanitized_params[$key] = implode( ", ", $value );
            } else {
                $sanitized_params[$key] = sanitize_text_field( $value );
            }
        }

        // Generate shortcode output
        $output = EPKB_FAQs_Shortcode::output_shortcode( $sanitized_params );
        if ( empty( $output ) ) {
            wp_send_json_error( 'Shortcode output is empty', 400 );
        }
        
        $response = array(
            'message'       => $is_initial_load ? '' : esc_html__( 'Shortcode changed', 'echo-knowledge-base' ),
            'data'          => $output,
        );
        
        // If not cached, get all design presets
        if ( ! $is_cached ) {
            $all_presets = array();
            for ( $i = 1; $i <= 18; $i++ ) {
                $all_presets[$i] = EPKB_FAQs_Utilities::get_design_settings( (string) $i );
            }
            $response['all_design_presets'] = $all_presets;
        }
        
        wp_die( wp_json_encode( $response ) );
    }
}
