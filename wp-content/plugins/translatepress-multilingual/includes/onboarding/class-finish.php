<?php
class TRP_Step_Finish implements TRP_Onboarding_Step_Interface {
    protected $settings;
    protected string $email;
    protected string $newsletter_checkbox;
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        $this->errors = new WP_Error();
        $current_user = wp_get_current_user();
        $this->email = $current_user->user_email;
        $this->newsletter_checkbox = '';
    }
    public function handle($data) {
        if (!wp_verify_nonce($data['_wpnonce_trp_onboarding_finish'], 'trp_onboarding_finish')) {
            $this->errors->add('nonce_fail_finish', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
        }

        // Process newsletter subscription if checked
        if (!empty($data['trp-checkbox-newsletter'])) {
            $this->email = (empty($data['newsletter-email']) ? '' : $data['newsletter-email']);
            $this->newsletter_checkbox = $data['trp-checkbox-newsletter'];

            $newsletter_email = sanitize_email($data['newsletter-email']);
            if (is_email($newsletter_email)) {
                $this->process_newsletter_subscription($newsletter_email);
            } else {
                $this->errors->add('incorrect_email', __('The email address you added is incorrect.', 'translatepress-multilingual'));
            }
        }

        if (!$this->errors->has_errors()) {
            // If no errors, we save our data and redirect to next step
            wp_redirect(add_query_arg('trp-edit-translation', 'true', home_url()));
            exit;
        }
    }

    public function render() {
        $tp_green_check = TRP_PLUGIN_URL . 'assets/images/circle-check-filled.svg';
        ?>
        <div class="trp-finish-page-container">
            <div class="trp-green-check-logo">
                <img src="<?php echo esc_url( $tp_green_check ); ?>" alt="<?php esc_attr_e("Setup Complete", 'translatepress-multilingual'); ?>">
            </div>

            <h1><?php esc_html_e("You're ready to start translating!", 'translatepress-multilingual'); ?></h1>
            <h3 class="trp-finish-page-text" ><?php esc_html_e('You have successfully set up TranslatePress for your website.', 'translatepress-multilingual'); ?></h3>
            <form method="post">
                <?php wp_nonce_field('trp_onboarding_finish', '_wpnonce_trp_onboarding_finish'); ?>
                <div class="trp-newsletter">
                    <input type="checkbox" class="email-subscription-checkbox" name="trp-checkbox-newsletter" id="trp-checkbox-newsletter" <?php checked($this->newsletter_checkbox, 'on'); ?> />
                    <label for="trp-checkbox-newsletter" title="<?php esc_html_e('Receive ', 'translatepress-multilingual'); ?>">
                        <?php esc_html_e('Sign me up to the Newsletter', 'translatepress-multilingual'); ?>
                    </label>
                    <div class="email-subscription-wrap">
                        <?php
                        foreach ($this->errors->get_error_messages() as $message) {
                            echo '<div class="ob-notice ob-notice-error">' . esc_html($message) . '</div>';
                        }
                        ?>
                        <input id="email-field" type="text" name="newsletter-email" value="<?php echo esc_attr( $this->email ); ?>" />
                    </div>
                    <button type="submit" class="trp-submit-btn trp-onboarding-finnish start">
                        <?php esc_html_e('Start translating', 'translatepress-multilingual');?>
                    </button>
                    <button type="submit" class="trp-submit-btn trp-onboarding-finnish start-submit">
                        <?php esc_html_e('Sign Up and Start translating', 'translatepress-multilingual');?>
                    </button>
                </div>

            </form>

        </div>
        <?php
    }

    private function process_newsletter_subscription($email) {
        if (!defined('TRP_STORE_URL')) {
            define('TRP_STORE_URL', 'https://translatepress.com');
        }

        $trp = TRP_Translate_Press::get_trp_instance();
        $tp_product_name = reset($trp->tp_product_name);
        if (empty($tp_product_name)) {
            $tp_product_name = 'TranslatePress';
        }

        $version_map = [
                'TranslatePress' => 'free',
                'TranslatePress Personal' => 'personal',
                'TranslatePress Business' => 'business',
                'TranslatePress Developer' => 'developer',
        ];

        $version = $version_map[$tp_product_name];

        $data = array(
            'email' => $email,
            'version' => $version
        );

        wp_remote_post(TRP_STORE_URL . '/wp-json/trp-api/emailNewsletterSubscribe', array(
            'timeout' => 3,
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data)
        ));
    }
}