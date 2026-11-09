<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TRP_AI_API_Key_Check{

    private $store_url;

    public function __construct(){
        // Use constant from wp-config.php if defined, otherwise use default URL
        $this->store_url = defined('TRP_STORE_URL') ? TRP_STORE_URL : "https://translatepress.com";
    }

    protected function get_option( $license_key_option ){
        return get_option( $license_key_option );
    }

    protected function delete_option( $license_key_option ){
        delete_option( $license_key_option );
    }

    protected function update_option( $license_key_option, $value ){
        update_option( $license_key_option, $value );
    }

    protected function tp_api_key_page_url( ){
        return admin_url( 'admin.php?page=trp_ai_api_key' );
    }

    public function edd_sanitize_tp_api_key( $new ) {
        $new = sanitize_text_field($new);
        $old = $this->get_option( 'trp_license_key' );
        if( $old && $old != $new ) {
            $this->delete_option( 'trp_license_status' ); // new license has been entered, so must reactivate
        }
        return $new;
    }

    /**
     * This function is run when wordpress checks for updates ( twice a day I believe )
     * @param $transient_data
     * @return mixed
     */
    public function check_tp_api_key( $transient_data ){

        if( empty( $transient_data->response ) )
            return $transient_data;

        if ( false === ( $trp_check_license = get_transient( 'trp_checked_licence' ) ) ) {
            $this->force_check_tp_api_key();
            set_transient( 'trp_checked_licence', 'yes', DAY_IN_SECONDS );
        }

        return $transient_data;
    }

    /**
     * This function is run when accessing the license page.
     * @return null
     */
    public function force_check_tp_api_key($api_cache_bypass = 'false'){
        $license = trim( $this->get_option( 'trp_license_key' ) );

        $license_information_for_all_addons = array();
        $license_status = 'invalid'; // by default this is invalid.

        if( empty( $license ) ){
            $license_information_for_all_addons['invalid'][] = (object) array( 'error' => 'missing' );
            $this->update_option('trp_license_details', $license_information_for_all_addons);
            $this->update_option( 'trp_license_status', $license_status );
            return;
        }

        $trp = TRP_Translate_Press::get_trp_instance();

        if (!empty($trp->tp_product_name)) {
            foreach ($trp->tp_product_name as $active_pro_addon_name) {
                // data to send in our API request
                $api_params = array(
                    'edd_action' => 'activate_license',                  //as the license is already activated this does not do anything. We could use check_license action but it gives different results  so we can't use it consistently with the result we get from the moment we activate it
                    'license'    => $license,
                    'item_name'  => urlencode($active_pro_addon_name),   // the name of our product in EDD
                    'url'        => home_url()
                );

                if($api_cache_bypass){
                    $api_params['cache_bypass'] = $api_cache_bypass;
                }

                if( !empty( $license ) || get_option( 'trp_plugin_optin' ) == 'yes' ){
                    $api_params['machine_translated_strings_data'] = json_encode( get_option( 'trp_machine_translated_characters', array() ), JSON_HEX_QUOT );

                    $machine_translation_settings = get_option( 'trp_machine_translation_settings', false );

                    if( !empty( $machine_translation_settings ) && is_array( $machine_translation_settings ) ) {

                        if( isset( $machine_translation_settings['machine-translation'] ) ) {
                            $api_params['machine_translation_enabled'] = $machine_translation_settings['machine-translation'];
                        }

                        if( isset( $machine_translation_settings['translation-engine'] ) ) {
                            $api_params['translation_engine'] = $machine_translation_settings['translation-engine'];
                        }
                    }
                }

                // Store debug information in transients with obfuscated license
                $debug_params = $api_params;
                if (!empty($debug_params['license']) && strlen($debug_params['license']) > 10) {
                    $debug_params['license'] = substr($debug_params['license'], 0, 5) . str_repeat('*', strlen($debug_params['license']) - 10) . substr($debug_params['license'], -5);
                }
                set_transient('trp_debug_force_check_license_request', array(
                    'url' => $this->store_url,
                    'params' => $debug_params,
                    'timestamp' => current_time('mysql')
                ), 60);

                // Call the custom API.
                $response = wp_remote_post($this->store_url, array('timeout' => 15, 'sslverify' => false, 'body' => $api_params));

                // Store response debug information
                set_transient('trp_debug_force_check_license_response', array(
                    'response_code' => is_wp_error($response) ? 'ERROR' : wp_remote_retrieve_response_code($response),
                    'response_body' => is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_body($response),
                    'timestamp' => current_time('mysql')
                ), 60);

                // make sure the response came back okay
                if (!is_wp_error($response)) {
                    $license_data = json_decode(wp_remote_retrieve_body($response));
                    $license_status = $license_data->license;   // $license_data->license will be either "valid" or "invalid"
                    if (false === $license_data->success) {
                        $license_information_for_all_addons['invalid'][] = $license_data;
                        break;//we only need one failure
                    } else {
                        $license_information_for_all_addons['valid'][] = $license_data;
                    }
                }
            }
        }

        //store the license reponse for each addon in the database
        $this->update_option('trp_license_details', $license_information_for_all_addons);
        // $license_data->license will be either "valid" or "invalid"
        $this->update_option( 'trp_license_status', $license_status );

    }

    /*
     * This is triggered on admin_init inside class-translate-press.php
     * It's stupid and should be refactored so it's in the same flow as the tp_api_key_page() function in TRP_AI_API_KEY class
     * The messages are duplicated in the includes/onboarding/class-ai-api-key.php since we can't use this function as it is
     */
    public function activate_tp_api_key() {

        // listen for our activate button to be clicked
        if( isset( $_POST['trp_edd_license_activate'] ) ) {
            // run a quick security check
            if( ! check_admin_referer( 'trp_license_nonce', 'trp_license_nonce' ) )
                return; // get out if we didn't click the Activate button

            if( !current_user_can( 'manage_options' ) )
                return;

            if ( isset( $_POST['trp_license_key'] ) && preg_match('/^[*]+$/', $_POST['trp_license_key']) && strlen( $_POST['trp_license_key'] ) > 5 ) { //phpcs:ignore
                // pressed submit without altering the existing license key (containing only * as outputted by default)
                // useful for Deactivating/Activating valid license back
                $license = get_option('trp_license_key', '');
            }else{
                // save the license
                $license = $this->edd_sanitize_tp_api_key( trim( $_POST['trp_license_key'] ) );//phpcs:ignore
                $this->update_option( 'trp_license_key', $license );
            }
            if( empty( $license ) ){
                $message = __( 'Your TranslatePress license key is invalid or missing.', 'translatepress-multilingual' );
                $redirect = add_query_arg( array( 'trp_sl_activation' => 'false', 'message' => urlencode( $message ), 'trp_license_nonce' => wp_create_nonce('trp_license_display_message') ), $this->tp_api_key_page_url() );
                wp_redirect( $redirect );
                exit();
            }

            $message = array();//we will check the license for each addon and we will sotre the messages in an array
            $license_information_for_all_addons = array();

            $trp = TRP_Translate_Press::get_trp_instance();
            if( !empty( $trp->tp_product_name ) ){
                foreach ($trp->tp_product_name as $active_pro_addon_name ){
                    // data to send in our API request
                    $api_params = array(
                        'edd_action' => 'activate_license',
                        'cache_bypass' => 'true',
                        'license'    => $license,
                        'item_name'  => urlencode( $active_pro_addon_name ), // the name of our product in EDD
                        'url'        => home_url()
                    );

                    if( !empty( $license ) || get_option( 'trp_plugin_optin' ) == 'yes' ){
                        $api_params['machine_translated_strings_data'] = json_encode( get_option( 'trp_machine_translated_characters', array() ), JSON_HEX_QUOT );

                        $machine_translation_settings = get_option( 'trp_machine_translation_settings', false );

                        if( !empty( $machine_translation_settings ) && is_array( $machine_translation_settings ) ) {

                            if( isset( $machine_translation_settings['machine-translation'] ) ) {
                                $api_params['machine_translation_enabled'] = $machine_translation_settings['machine-translation'];
                            }

                            if( isset( $machine_translation_settings['translation-engine'] ) ) {
                                $api_params['translation_engine'] = $machine_translation_settings['translation-engine'];
                            }
                        }
                    }

                    // Store debug information in transients with obfuscated license
                    $debug_params = $api_params;
                    if (!empty($debug_params['license']) && strlen($debug_params['license']) > 10) {
                        $debug_params['license'] = substr($debug_params['license'], 0, 5) . str_repeat('*', strlen($debug_params['license']) - 10) . substr($debug_params['license'], -5);
                    }
                    set_transient('trp_debug_activate_license_request', array(
                        'url' => $this->store_url,
                        'params' => $debug_params,
                        'timestamp' => current_time('mysql')
                    ), 60);

                    // Call the custom API.
                    $response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

                    // Store response debug information
                    set_transient('trp_debug_activate_license_response', array(
                        'response_code' => is_wp_error($response) ? 'ERROR' : wp_remote_retrieve_response_code($response),
                        'response_body' => is_wp_error($response) ? $response->get_error_message() : wp_remote_retrieve_body($response),
                        'timestamp' => current_time('mysql')
                    ), 60);

                    // make sure the response came back okay
                    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
                        $response_error_message = '';
                        if ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) {
                            $response_error_message = $response->get_error_message();
                        }
                        $message[] = ! empty( $response_error_message ) ? $response_error_message : __( 'An error occurred, please try again.', 'translatepress-multilingual' );
                    }
                    else {

                        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                        if ( false === $license_data->success ) {

                            switch( $license_data->error ) {
                                case 'expired' :
                                    $message[] = sprintf(
                                        __( 'Your license key expired on %s.', 'translatepress-multilingual' ),
                                        date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
                                    );
                                    break;
                                case 'revoked' :
                                    $message[] = __( 'Your license key has been disabled.', 'translatepress-multilingual' );
                                    break;
                                case 'missing' :
                                    $message[] = __( 'Your TranslatePress license key is invalid or missing.', 'translatepress-multilingual' );
                                    break;
                                case 'invalid' :
                                case 'site_inactive' :
                                    //[utm7]
                                    $message[] = __( 'Your license key is disabled for this URL. Re-enable it from <a target="_blank" href="https://translatepress.com/account/?utm_source=wp-dashboard&utm_medium=client-site&utm_campaign=license-deactivated">https://translatepress.com/account</a> -> Manage Sites.', 'translatepress-multilingual' );
                                    break;
                                case 'item_name_mismatch' :
                                    $message[] = __( '<p><strong>License key mismatch.</strong> The license you entered doesn’t match the TranslatePress version you have installed.</p><p>Please check that you’ve installed the correct version for your license from your TranslatePress account.</p>' , 'translatepress-multilingual' );

                                    if( !empty( $license_data->item_name ) && urldecode( $license_data->item_name ) === 'TranslatePress' ) {
                                        $message[] = "<p>" . __( 'If you have only the free plugin installed but added a paid license, please install the paid plugin from your TranslatePress account.' , 'translatepress-multilingual' ) . "</p>";
                                    }

                                    break;
                                case 'no_activations_left':

                                    $message[] = __( 'Your license key has reached its activation limit.', 'translatepress-multilingual' );
                                    if( !empty( $license_data->item_name ) && urldecode( $license_data->item_name ) !== 'TranslatePress Developer' )
                                        //[utm8]
                                        $message[] = sprintf( __( 'Upgrade your plan to add more sites. %1$sUpgrade now%2$s', 'translatepress-multilingual' ), '<a href="https://translatepress.com/account/?utm_source=wp-dashboard&utm_medium=client-site&utm_campaign=activation-limit" target="_blank" class="button-primary">', '</a>' );
                                    break;
                                case 'website_already_on_free_license':
                                    $message[] = trp_get_tp_ai_api_key_labels( 'already_on_free' );
                                    break;
                                default :
                                    $message[] = __( 'An error occurred, please try again.', 'translatepress-multilingual' );
                                    break;
                            }

                            $license_information_for_all_addons['invalid'][] =  $license_data;

                        }
                        else{
                            $license_information_for_all_addons['valid'][] =  $license_data;
                            trp_mtapi_sync_license_call( $license );
                        }

                    }
                }
            }

            //store the license reponse for each addon in the database
            $this->update_option( 'trp_license_details', $license_information_for_all_addons );


            // Check if anything passed on a message constituting a failure
            if ( ! empty( $message ) ) {
                $message = implode( "<br/>", array_unique($message) );//if we got the same message for multiple addons show just one, and add a br in case we show multiple messages
                $redirect = add_query_arg( array( 'trp_sl_activation' => 'false', 'message' => urlencode( $message ), 'trp_license_nonce' => wp_create_nonce('trp_license_display_message') ), $this->tp_api_key_page_url() );

                wp_redirect( $redirect );
                exit();
            }

            // $license_data->license will be either "valid" or "invalid"

            $this->update_option( 'trp_license_status', $license_data->license );

            wp_redirect( add_query_arg( array( 'trp_sl_activation' => 'true', 'message' => urlencode( __( 'You have successfully activated your license', 'translatepress-multilingual' ) ), 'trp_license_nonce' => wp_create_nonce('trp_license_display_message')), $this->tp_api_key_page_url() ) );
            exit();
        }
    }

    function deactivate_tp_api_key() {

        // listen for our activate button to be clicked
        if( isset( $_POST['trp_edd_license_deactivate'] ) ) {

            // run a quick security check
            if( ! check_admin_referer( 'trp_license_nonce', 'trp_license_nonce' ) )
                return; // get out if we didn't click the Activate button

            if( !current_user_can( 'manage_options' ) )
                return;

            // retrieve the license from the database
            $license = trim( $this->get_option( 'trp_license_key' ) );

            $trp = TRP_Translate_Press::get_trp_instance();
            if( !empty( $trp->tp_product_name ) ){
                foreach ($trp->tp_product_name as $active_pro_addon_name ){//this loop will actually run just once, as we redirect at the end in all cases

                    // data to send in our API request
                    $api_params = array(
                        'edd_action' => 'deactivate_license',
                        'license'    => $license,
                        'item_name'  => urlencode( $active_pro_addon_name ), // the name of our product in EDD
                        'url'        => home_url()
                    );

                    // Call the custom API.
                    $response = wp_remote_post( $this->store_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

                    // make sure the response came back okay
                    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

                        if ( is_wp_error( $response ) ) {
                            $message = $response->get_error_message();
                        } else {
                            $message = __( 'An error occurred, please try again.', 'translatepress-multilingual' );
                        }

                        $redirect = add_query_arg( array( 'trp_sl_activation' => 'false', 'message' => urlencode( $message ), 'trp_license_nonce' => wp_create_nonce('trp_license_display_message') ), $this->tp_api_key_page_url() );
                        wp_redirect( $redirect );
                        exit();
                    }

                    // decode the license data
                    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

                    // $license_data->license will be either "deactivated" or "failed"
                    // regardless, we delete the record in the client website. Otherwise, if he tries to add a new license, he can't.
                    if( $license_data->license == 'deactivated' || $license_data->license == 'failed') {
                        delete_option( 'trp_license_status' );
                        delete_option( 'trp_license_details' );
                    }

                    wp_redirect( $this->tp_api_key_page_url() );
                    exit();
                    }
                }
        }
    }

}
