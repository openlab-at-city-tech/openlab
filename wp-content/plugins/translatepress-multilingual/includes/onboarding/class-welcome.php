<?php
class TRP_Step_Welcome implements TRP_Onboarding_Step_Interface {
    protected $settings;
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        $this->errors = new WP_Error();
    }
    public function handle($data) {

        if (!wp_verify_nonce($data['_wpnonce_trp_onboarding_welcome'], 'trp_onboarding_welcome')) {
            $this->errors->add('nonce_fail_welcome', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
        }

        if (!$this->errors->has_errors()) {
            wp_redirect(add_query_arg(['step' => 'languages']));
            exit;
        }
    }

    public function render() {

        $tp_logo = TRP_PLUGIN_URL . 'assets/images/tp-logo-square-light.svg';
        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . esc_html($message) . '</div>';
        }
        ?>

        <form method="post">
            <?php wp_nonce_field('trp_onboarding_welcome', '_wpnonce_trp_onboarding_welcome'); ?>

            <div class="trp-welcome-onboarding-container">
            <div class="trp-settings-logo">
                <img src="<?php echo esc_url( $tp_logo ); ?>"
                     alt="TranslatePress Logo">
            </div>

        <h1><?php esc_html_e('Welcome to TranslatePress', 'translatepress-multilingual'); ?></h1>
            <h3 class="trp-welcome-text" ><?php esc_html_e('Quick guided setup to configure TranslatePress in no time!', 'translatepress-multilingual'); ?></h3>

                <h3 class="trp-welcome-text" ><?php esc_html_e('It takes less than a minute.', 'translatepress-multilingual'); ?></h3>
            <div class="trp-continue-onboarding"><button type="submit" class="trp-submit-btn"><?php esc_html_e('Continue', 'translatepress-multilingual');?></button></div>

        </div>
        </form>
        <?php
    }
}