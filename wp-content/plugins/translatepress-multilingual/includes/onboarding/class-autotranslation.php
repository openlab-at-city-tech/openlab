<?php

class TRP_Step_AutoTranslation implements TRP_Onboarding_Step_Interface {
    protected $settings;
    protected WP_Error $errors;

    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->errors = new WP_Error();
    }

    public function handle($data)
    {
        if (!wp_verify_nonce($data['_wpnonce_trp_onboarding_autotranslation'], 'trp_onboarding_autotranslation')) {
            $this->errors->add('nonce_fail_languages', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
        }
        // Handle license activation if provided
        $license = isset($data['trp_license']) ? sanitize_text_field($data['trp_license']) : '';

        if (!empty($license)) {
            // Save the license and trigger license check
            update_option('trp_license_key', $license);

            $trp = TRP_Translate_Press::get_trp_instance();
            $trp->get_component('plugin_updater')->force_check_license('true');

            // Check license validation results
            $this->check_license_validation_results();
        }

        // Handle automatic translation setting
        $machine_translation_enabled = isset($data['trp_machine_translation']) && $data['trp_machine_translation'] === 'yes';

        // Get current machine translation settings
        $machine_translation_settings = get_option('trp_machine_translation_settings', array());

        if ($machine_translation_enabled) {
            // Check if license is valid before enabling automatic translation
            $license_status = get_option('trp_license_status');
            $license_details = get_option('trp_license_details');

            // Validate that we have a valid license for automatic translation
            if ($license_status !== 'valid' || !isset($license_details['valid'][0])) {
                $this->errors->add('license_required', __('A valid license is required to enable Automatic Translation.', 'translatepress-multilingual'));
            } else {
                // Save automatic translation setting as enabled
                $machine_translation_settings['machine-translation'] = 'yes';
            }
        } else {
            // Save automatic translation setting as disabled
            $machine_translation_settings['machine-translation'] = 'no';
        }

        // Update the settings regardless of enabled/disabled state
        update_option('trp_machine_translation_settings', $machine_translation_settings);

        // Handle errors - don't redirect if there are errors, render() will display them
        if ($this->errors->has_errors()) {
            return;
        }

        //synchronize EDD license with MTAPI
        if(!empty($license)){
            trp_mtapi_sync_license_call(sanitize_text_field($license));
        }

        // Check if continue button was pressed (hidden input is present)
        if (isset($data['submit']) && $data['submit'] == 'activate') {
            // If no errors and continue button was pressed, redirect to addons step
            wp_redirect(add_query_arg(['step' => 'autotranslation']));
            exit;
        }

        // If no errors and no continue button (e.g., just license activation), stay on current step
        wp_redirect(add_query_arg(['step' => 'addons']));
        exit;
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
                    $this->errors->add('site_inactive', __('Your license key is disabled for this URL. Re-enable it from <a target="_blank" href="https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=tp-ai">https://translatepress.com/account</a> -> Manage Sites.', 'translatepress-multilingual'));
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

    public function render()
    {
        // Store that we're on the autotranslation step for install step's "Go back" navigation
        set_transient('trp_onboarding_previous_step', 'autotranslation', 3600); // 1 hour expiry
        
        ?>
        <h1><?php esc_html_e('Enable Automatic Translation', 'translatepress-multilingual'); ?></h1>
        <h3><?php esc_html_e('Automatically translate your website using TranslatePress AI.', 'translatepress-multilingual'); ?></h3>
        <?php
        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . wp_kses_post($message) . '</div>';
        }
        ?>
        <form method="post">
            <?php
            wp_nonce_field('trp_onboarding_autotranslation', '_wpnonce_trp_onboarding_autotranslation');

            require_once(TRP_PLUGIN_DIR . "/includes/mtapi/class-mtapi-customer.php");
            $trp = TRP_Translate_Press::get_trp_instance();
            $translatepress_version_name = reset($trp->tp_product_name);

            $license = get_option('trp_license_key');
            $status = get_option('trp_license_status');
            $details = get_option('trp_license_details');

            if (!isset($details['valid'][0])) {
                $status = false;
            }

            if ($status === false) : ?>

                <div class="trp-settings-options-item trp-settings-switch__wrapper">
                    <div class="trp-switch ">
                        <input type="checkbox" id="trp-machine-translation-enabled" class="trp-switch-input"
                               name="trp_machine_translation" value="yes" disabled >
                        <label for="trp-machine-translation-enabled" class="trp-switch-label"></label>
                    </div>
                    <label for="trp-machine-translation-enabled"><?php esc_html_e('Enable Automatic Translation', 'translatepress-multilingual');?></label>
                </div>

                <div class="trp-onboarding-license">
                    <h4>
                        <img src="<?php echo esc_url(TRP_PLUGIN_URL . 'assets/images/'); ?>ai-icon.svg" width="24" height="24" align="top"/>TranslatePress AI<?php //this is not localized by choice ?>
                    </h4>
                    <p><?php esc_html_e('In order to enable Automatic Translation using TranslatePress AI, please enter your license key from', 'translatepress-multilingual');?> <a href="https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=tp-ai" target="_blank"><?php esc_html_e('your account.', 'translatepress-multilingual');?></a></p>
                    <div>
                        <label for="license-field">License Key</label>
                        <div class="license-field-wrap">
                            <input id="license-field" type="password" name="trp_license" value="<?php echo esc_attr(get_option('trp_license_key', '')); ?>" required />
                            <button class="trp-button-secondary" type="submit" name="submit" value="activate"><?php esc_html_e('Activate License', 'translatepress-multilingual');?></button>
                        </div>
                    </div>
                    <?php
                    // Display errors if any
                    if ($this->errors->has_errors()) {
                        foreach ($this->errors->get_error_messages() as $message) {
                            echo '<div id="trp-mtapi-key" class="ob-notice ob-notice-error">' . wp_kses_post($message) . '</div>';
                        }
                    } else {
                        ?>
                        <div id="trp-mtapi-key" class="ob-notice ob-notice-error">
                            <?php esc_html_e('No Active License Detected for this website.', 'translatepress-multilingual'); ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php if ($translatepress_version_name != 'TranslatePress') : ?>
                <div class="trp-continue-onboarding">
                    <button class="trp-submit-btn" type="submit" name="submit" value="continue"><?php esc_html_e('Continue', 'translatepress-multilingual');?></button>
                </div>
                <div class="trp-primary-text">
                    <a href="<?php echo esc_url(add_query_arg(['step' => 'addons'])); ?>"><?php esc_html_e('Skip and continue with manual translation »', 'translatepress-multilingual'); ?></a>
                </div>
                <?php endif; ?>

                <?php if ($translatepress_version_name == 'TranslatePress') : ?>
                <div class="trp-ob-wrap trp-ob-gold-bg">
                    <div class="trp-ob-generate-license">
                        <div class="trp-ob-generate-license-header">
                            <h3><?php esc_html_e("Get Your Free TranslatePress AI License", 'translatepress-multilingual'); ?></h3>
                        </div>
                        <div class="trp-ob-generate-license-button">
                            <a href="<?php echo esc_url('https://translatepress.com/ai-free/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=tp-ai-free') ?>" target="_blank">
                                <?php esc_html_e('Generate License', 'translatepress-multilingual'); ?>
                            </a>
                        </div>
                    </div>

                    <p><?php esc_html_e('Creating a free account includes: ', 'translatepress-multilingual'); ?></p>
                    <span class="trp-secondary-text trp-check-text">
                            <img src="<?php echo esc_url(TRP_PLUGIN_URL . 'assets/images/'); ?>green-circle-check.png" width="20px" height="20px"/>
                            <?php esc_html_e('Access to TranslatePress AI for instant automatic translations', 'translatepress-multilingual'); ?>
                        </span>

                    <span class="trp-secondary-text trp-check-text">
                            <img src="<?php echo esc_url(TRP_PLUGIN_URL . 'assets/images/'); ?>green-circle-check.png" width="20px" height="20px"/>
                            <?php esc_html_e('2000 AI words to translate automatically', 'translatepress-multilingual'); ?>
                        </span>
                </div>
                <div class="trp-ob-wrap trp-ob-grey-bg trp-primary-text trp-ob-center">
                    <?php esc_html_e('Are you a TranslatePress PRO user?', 'translatepress-multilingual'); ?> <a href="<?php echo esc_url(add_query_arg(['step' => 'install'])); ?>"><?php esc_html_e('Install & Activate your pro plugin.', 'translatepress-multilingual'); ?></a>
                </div>

                <div class="trp-ob-wrap trp-primary-text trp-ob-center">
                    <a href="<?php echo esc_url(add_query_arg(['step' => 'addons'])); ?>"><?php esc_html_e('Skip and continue with manual translation »', 'translatepress-multilingual'); ?></a>
                </div>
                <?php endif; // $translatepress_version_name == 'TranslatePress' ?>
            <?php endif; // $status === false

            if ($status === 'valid') :
                $product_name = '<strong>' . str_replace('+', ' ', $details['valid'][0]->item_name) . '</strong>';

                // MTAPI_URL needs to be defined in wp-config.php for local host development
                $mtapi_url = (defined('MTAPI_URL') ? MTAPI_URL : 'https://mtapi.translatepress.com');

                $mtapi_server = new TRP_MTAPI_Customer($mtapi_url);
                $site_status = $mtapi_server->lookup_site($license, home_url());

                $site_status['quota'] = isset ($site_status['quota']) ? $site_status['quota'] : 0;

                set_transient("trp_mtapi_cached_quota", $site_status['quota'], 5 * 60);

                $quota = ($site_status['quota'] < 500) ? 0 : ceil($site_status['quota'] / 5);

                // this $total_quota is not correct due to quota_used should account for ALL websites added to this license.
                // however, in case the site does have a user defined limit, the quota_used is correct.
                // without further changes to mtapi we don't have a proper way of knowing what's the quota_used.
                // will hide total_quota and let progress bar in place as the approximation is good enough
                if (!isset($site_status['quota_used'])) {
                    $site_status['quota_used'] = 0;
                }
                $total_quota = ceil(($site_status['quota'] + $site_status['quota_used']) / 5);

                $formatted_quota = number_format($quota);
                $formatted_total_quota = number_format($total_quota);

                $usage_percentage = ($total_quota > 0) ? ($quota / $total_quota) * 100 : 0;
                ?>

                <?php
                // Check current automatic translation setting
                $machine_translation_settings = get_option('trp_machine_translation_settings', array());
                $is_machine_translation_enabled = isset($machine_translation_settings['machine-translation']) && $machine_translation_settings['machine-translation'] === 'yes';
                ?>
                
                <div class="trp-settings-options-item trp-settings-switch__wrapper">
                    <div class="trp-switch ">
                        <input type="checkbox" id="trp-machine-translation-enabled" class="trp-switch-input"
                               name="trp_machine_translation" value="yes" <?php checked($is_machine_translation_enabled, true); ?>>
                        <label for="trp-machine-translation-enabled" class="trp-switch-label"></label>
                    </div>
                    <label for="trp-machine-translation-enabled">Enable Automatic Translation</label>
                </div>

                <div class="trp-engine trp-automatic-translation-engine__container" id="mtapi">
                    <span class="trp-primary-text-bold">
                        <img src="<?php echo esc_url(TRP_PLUGIN_URL . 'assets/images/'); ?>ai-icon.svg" width="24" height="24"/>
                        TranslatePress AI <?php //this is not localized by choice
                        ?>
                    </span>

                    <div class="trp-automatic-translation-license-notice__wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 3.33989C18.5083 4.21075 19.7629 5.46042 20.6398 6.96519C21.5167 8.46997 21.9854 10.1777 21.9994 11.9192C22.0135 13.6608 21.5725 15.3758 20.72 16.8946C19.8676 18.4133 18.6332 19.6831 17.1392 20.5782C15.6452 21.4733 13.9434 21.9627 12.2021 21.998C10.4608 22.0332 8.74055 21.6131 7.21155 20.7791C5.68256 19.9452 4.39787 18.7264 3.48467 17.2434C2.57146 15.7604 2.06141 14.0646 2.005 12.3239L2 11.9999L2.005 11.6759C2.061 9.94888 2.56355 8.26585 3.46364 6.79089C4.36373 5.31592 5.63065 4.09934 7.14089 3.25977C8.65113 2.42021 10.3531 1.98629 12.081 2.00033C13.8089 2.01437 15.5036 2.47589 17 3.33989ZM15.707 9.29289C15.5348 9.12072 15.3057 9.01729 15.0627 9.002C14.8197 8.98672 14.5794 9.06064 14.387 9.20989L14.293 9.29289L11 12.5849L9.707 11.2929L9.613 11.2099C9.42058 11.0607 9.18037 10.9869 8.9374 11.0022C8.69444 11.0176 8.46541 11.121 8.29326 11.2932C8.12112 11.4653 8.01768 11.6943 8.00235 11.9373C7.98702 12.1803 8.06086 12.4205 8.21 12.6129L8.293 12.7069L10.293 14.7069L10.387 14.7899C10.5624 14.926 10.778 14.9998 11 14.9998C11.222 14.9998 11.4376 14.926 11.613 14.7899L11.707 14.7069L15.707 10.7069L15.79 10.6129C15.9393 10.4205 16.0132 10.1802 15.9979 9.93721C15.9826 9.69419 15.8792 9.46509 15.707 9.29289Z"
                                  fill="#4AB067"/>
                        </svg>

                        <span id="trp-mtapi-key" class="trp-primary-text"><?php
                            printf(wp_kses(__('You have a valid %s <strong>license</strong>.', 'translatepress-multilingual'), array('strong' => array())),
                                wp_kses($product_name, array('strong' => array()))
                            ); ?>
                        </span>
                    </div>

                    <span class="trp-secondary-text">
                        <?php echo "<strong>" . esc_html($formatted_quota) . "</strong>" . esc_html__(' words remaining. ', 'translatepress-multilingual'); ?>
                    </span>

                    <div class="trp-quota-bar">
                        <div class="trp-quota-progress" style="width: <?php echo esc_attr($usage_percentage); ?>%;"></div>
                    </div>

                    <span class="trp-secondary-text">
                        <?php
                        printf(
                            esc_html__('Manage your license & quota on the %s', 'translatepress-multilingual'),
                            '<a href="' . esc_url('https://translatepress.com/account/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=tp-ai') . '" target="_blank" class="trp-settings-link"> ' . esc_html__('TranslatePress.com Account Page', 'translatepress-multilingual') . '</a>'
                        );
                        ?>
                    </span>
                </div>
                <div class="trp-continue-onboarding">
                    <button class="trp-submit-btn" type="submit" name="submit" value="continue"><?php esc_html_e('Continue', 'translatepress-multilingual');?></button>
                </div>
                <!--<div class="trp-primary-text trp-ob-skip">
                    <a href="<?php echo esc_url(add_query_arg(['step' => 'addons'])); ?>"><?php esc_html_e('Skip this step', 'translatepress-multilingual'); ?></a>
                </div>-->

            <?php
            endif;
            ?>
        </form>
        <?php
    }
}