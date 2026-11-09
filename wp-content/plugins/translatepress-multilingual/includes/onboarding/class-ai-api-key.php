<?php
class TRP_Step_AI_API_Key implements TRP_Onboarding_Step_Interface {
    protected array $settings;
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        $this->errors = new WP_Error();
    }

    public function handle($data) {

        // Handle license activation
        $nonce = isset($data['_wpnonce_trp_onboarding_ai_api_key']) ? $data['_wpnonce_trp_onboarding_ai_api_key'] : '';
        $license = isset($data['trp_license']) ? $data['trp_license'] : '';

        if(!empty($license)){
            update_option('trp_license_key', sanitize_text_field($license));
            /*
             * We save the license and trigger a license check
             * The license details and status are then saved in the options:
             * * trp_license_details
             * * trp_license_status
             * We'll use these options to show different error messages.
             */
            $trp = TRP_Translate_Press::get_trp_instance();
            $trp->get_component('plugin_updater')->force_check_tp_api_key('true');
        }

        if (!wp_verify_nonce($nonce, 'trp_onboarding_ai_api_key')) {
            $this->errors->add('nonce_fail_license', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
        } elseif(empty($license)) {
            $this->errors->add('empty_license', __('Your TranslatePress license key is invalid or missing.', 'translatepress-multilingual'));
        } else {
            // Check license validation results after activation attempt
            $this->check_license_validation_results();
        }

        if (!$this->errors->has_errors()) {
            //synchronize EDD license with MTAPI
            trp_mtapi_sync_license_call(sanitize_text_field($license));

            // If no errors, we save our data and redirect to next step
            $previous_step = get_transient('trp_onboarding_previous_step');
            $previous_step = ($previous_step) ? $previous_step : 'languages';
            wp_redirect(add_query_arg(['step' => $previous_step]));
            exit;
        }
    }

    private function check_license_validation_results() {
        $license_details = get_option('trp_license_details');

        // Check for invalid license details
        if (!empty($license_details) && !empty($license_details['invalid'])) {
            $license_detail = $license_details['invalid'][0];
            
            switch($license_detail->error) {
                case 'expired':
                    $this->errors->add('expired', sprintf(
                        __('Your license key expired on %s.', 'translatepress-multilingual'),
                        date_i18n(get_option('date_format'), strtotime($license_detail->expires, current_time('timestamp')))
                    ));
                    break;
                case 'revoked':
                    $this->errors->add('revoked', __('Your license key has been disabled.', 'translatepress-multilingual'));
                    break;
                case 'missing':
                    $this->errors->add('missing', __('Your TranslatePress license key is invalid or missing.', 'translatepress-multilingual'));
                    break;
                case 'invalid':
                case 'site_inactive':
                    $this->errors->add('site_inactive', __('Your license key is disabled for this URL. Re-enable it from <a target="_blank" href="https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=activate-license">https://translatepress.com/account</a> -> Manage Sites.', 'translatepress-multilingual'));
                    break;
                case 'item_name_mismatch':
                    $this->errors->add('item_name_mismatch', __('<p><strong>License key mismatch.</strong> The license you entered doesn\'t match the TranslatePress version you have installed.</p><p>Please check that you\'ve installed the correct version for your license from your TranslatePress account.</p>', 'translatepress-multilingual'));
                    break;
                case 'no_activations_left':
                    $this->errors->add('no_activations_left', __('Your license key has reached its activation limit.', 'translatepress-multilingual'));
                    break;
                case 'website_already_on_free_license':
                    $this->errors->add('website_already_on_free_license', trp_get_tp_ai_api_key_labels( 'already_on_free' ));
                    break;
                default:
                    $this->errors->add('license_error', __('An error occurred, please try again.', 'translatepress-multilingual'));
                    break;
            }
        }
    }

    public function render() {
        // Return to whichever onboarding step sent the user here (e.g. the Enable Modules step),
        // so activating the license — or skipping via "Go Back" — brings them back where they were.
        $previous_step = get_transient('trp_onboarding_previous_step');
        $back_link     = add_query_arg(['step' => $previous_step ? $previous_step : 'languages']);

        $license_labels = trp_get_tp_ai_api_key_labels();
        ?>
        <h1><?php echo esc_html( $license_labels['onboarding_heading'] ); ?></h1>
        <h3>
            <?php echo esc_html( $license_labels['onboarding_subheading'] ); ?>
            <a href="https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=activate-license" target="_blank"> <?php esc_html_e('TranslatePress Account', 'translatepress-multilingual'); ?></a>
        </h3>

        <?php
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Check license status first time we access the page.
            // We first do a force license check, or we might get cached results otherwise.
            $trp = TRP_Translate_Press::get_trp_instance();
            $trp->get_component('plugin_updater')->force_check_tp_api_key('true');
            $this->check_license_validation_results();
        }

        $license_status = get_option('trp_license_status', '');
        if ($license_status === 'valid') {
            echo '<div class="ob-notice ob-notice-success">' . esc_html( $license_labels['onboarding_valid'] ) . '</div>';
        }
        
        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . wp_kses_post($message) . '</div>';
        }
        ?>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('trp_onboarding_ai_api_key', '_wpnonce_trp_onboarding_ai_api_key'); ?>
            <div class="trp-onboarding-license">
                <label for="license-field"><?php echo esc_html( $license_labels['onboarding_field_label'] ); ?></label>
                <div class="license-field-wrap">
                    <input id="license-field" type="password" name="trp_license" value="<?php echo esc_attr(get_option('trp_license_key', '')); ?>" required />
                    <button class="trp-submit-btn" type="submit"><?php echo esc_html( $license_labels['onboarding_activate_button'] );?></button>
                </div>
            </div>

            <div class="trp-go-back">
                <a href="<?php echo esc_url($back_link); ?>"> <?php esc_html_e('« Go Back', 'translatepress-multilingual'); ?></a>
            </div>

        </form>
        <?php
    }
}