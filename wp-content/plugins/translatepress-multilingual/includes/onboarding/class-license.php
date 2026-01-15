<?php
class TRP_Step_License implements TRP_Onboarding_Step_Interface {
    protected array $settings;
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        $this->errors = new WP_Error();
    }

    public function handle($data) {

        // Handle license activation
        $nonce = isset($data['_wpnonce_trp_onboarding_license']) ? $data['_wpnonce_trp_onboarding_license'] : '';
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
            $trp->get_component('plugin_updater')->force_check_license('true');
        }

        if (!wp_verify_nonce($nonce, 'trp_onboarding_license')) {
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
                    $this->errors->add('website_already_on_free_license', __('This website is already activated under a free license. Each website can only use one free license.', 'translatepress-multilingual'));
                    break;
                default:
                    $this->errors->add('license_error', __('An error occurred, please try again.', 'translatepress-multilingual'));
                    break;
            }
        }
    }

    public function render() {
        $trp = TRP_Translate_Press::get_trp_instance();
        if(in_array( 'TranslatePress', $trp->tp_product_name )){
            $back_link = add_query_arg(['step' => 'install']); // we have a free version
        } else {
            $back_link = add_query_arg(['step' => 'languages']); // we have a pro version
        }

        ?>
        <h1><?php esc_html_e('Add your License Key', 'translatepress-multilingual'); ?></h1>
        <h3>
            <?php esc_html_e('Add your License Key to unlock all premium features. Find the License Key in your', 'translatepress-multilingual'); ?>
            <a href="https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=activate-license" target="_blank"> <?php esc_html_e('TranslatePress Account', 'translatepress-multilingual'); ?></a>
        </h3>

        <?php
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Check license status first time we access the page.
            // We first do a force license check, or we might get cached results otherwise.
            $trp = TRP_Translate_Press::get_trp_instance();
            $trp->get_component('plugin_updater')->force_check_license('true');
            $this->check_license_validation_results();
        }

        $license_status = get_option('trp_license_status', '');
        if ($license_status === 'valid') {
            echo '<div class="ob-notice ob-notice-success">' . esc_html__('Your license is valid and active.', 'translatepress-multilingual') . '</div>';
        }
        
        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . wp_kses_post($message) . '</div>';
        }
        ?>

        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('trp_onboarding_license', '_wpnonce_trp_onboarding_license'); ?>
            <div class="trp-onboarding-license">
                <label for="license-field">License Key</label>
                <div class="license-field-wrap">
                    <input id="license-field" type="password" name="trp_license" value="<?php echo esc_attr(get_option('trp_license_key', '')); ?>" required />
                    <button class="trp-submit-btn" type="submit"><?php esc_html_e('Activate License', 'translatepress-multilingual');?></button>
                </div>
            </div>

            <div class="trp-go-back">
                <a href="<?php echo esc_url($back_link); ?>"> <?php esc_html_e('« Go Back', 'translatepress-multilingual'); ?></a>
            </div>

        </form>
        <?php
    }
}