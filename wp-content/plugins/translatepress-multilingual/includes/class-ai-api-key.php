<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('TRP_AI_API_KEY') ) {
    class TRP_AI_API_KEY
    {
        public function __construct(){
        }

        public function tp_api_key_menu()
        {
            add_submenu_page(
                'TRPHidden',
                trp_get_tp_ai_api_key_labels( 'submenu_page_title' ),
                'TRPHidden',
                'manage_options',
                'trp_ai_api_key',
                array($this, 'tp_api_key_page')
            );
        }

        public function register_tp_api_key_setting(){
            register_setting( 'trp_license_key', 'trp_license_key', array( $this, 'sanitize_tp_api_key' ) );
        }

        public function sanitize_tp_api_key( $license_key ) {
            return sanitize_text_field( trim( $license_key ) );
        }

        public function tp_api_key_page()
        {

            $trp = TRP_Translate_Press::get_trp_instance();

            // force check license when accessing the License Tab.
            $trp->get_component('plugin_updater')->force_check_tp_api_key('true');

            $license = get_option('trp_license_key');
            // don't show the license in html
            $license = str_repeat("*", strlen($license));
            $status = get_option('trp_license_status');
            $details = get_option('trp_license_details');
            $action = 'options.php';
            ob_start();
            require TRP_PLUGIN_DIR . 'partials/ai-api-key-settings-page.php';
            echo ob_get_clean();//phpcs:ignore
        }

        public function tp_api_key_activation_message() {
            if ( isset( $_GET['trp_sl_activation'] ) && ! empty( $_GET['message'] ) && isset( $_GET['trp_license_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_GET['trp_license_nonce'] ), 'trp_license_display_message' ) ) {
                return wp_kses_post( urldecode( $_GET['message'] ) );//phpcs:ignore
            }
            return '';
        }
    }
}
