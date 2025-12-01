<?php
class TRP_Step_Addons implements TRP_Onboarding_Step_Interface {

    protected WP_Error $errors;
    protected $settings;
    protected array $addons;

    public function __construct( $settings ){
        $this->settings = $settings;
        $this->errors = new WP_Error();
        $this->addons = $this->get_addons();
    }
    public function handle($data) {
        if ($this->errors->has_errors()) {
            wp_redirect(add_query_arg(['step' => 'languages', 'status' => 'error']));
            exit;
        }

        $this->save_addons_settings($data);
        wp_redirect(add_query_arg(['step' => 'finish']));
        exit;
    }

    public function save_addons_settings($data){

        $add_ons_settings = get_option( 'trp_add_ons_settings', array() );
        $is_enabled = array();

        $post_addons = isset($data['is_active']) ? (array) $data['is_active'] : array();
        foreach( $post_addons as $slug => $active ){
            if ($this->slug_exists($this->addons, $slug)) {
                $is_enabled[$slug] = $active;
                do_action( 'trp_add_ons_activate', $slug );
            }
        }

        update_option( 'trp_add_ons_settings', $is_enabled );
    }

    public function get_addons()
    {
        $addons_strings_array = array(
                'advanced' => array(
                        'header' => array(
                                'name' => __('Advanced Add-ons', 'translatepress-multilingual'),
                                'description' => __('These addons extend your translation plugin and are available in the Developer, Business and Personal plans.', 'translatepress-multilingual'),
                        ),
                        'addons' => array(
                                array('slug' => 'tp-add-on-seo-pack/tp-seo-pack.php',
                                        'type' => 'add-on',
                                        'name' => __('SEO Pack', 'translatepress-multilingual'),
                                        'description' => __('SEO support for page slug, page title, description and Facebook and Twitter social graph information. The HTML lang attribute is properly set.', 'translatepress-multilingual'),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/seo_icon_translatepress_addon_page.png',
                                ),
                                array('slug' => 'tp-add-on-extra-languages/tp-extra-languages.php',
                                        'type' => 'add-on',
                                        'name' => __('Multiple Languages', 'translatepress-multilingual'),
                                        'description' => __('Add as many languages as you need for your project to go global. Publish your language only when all your translations are done.', 'translatepress-multilingual'),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/multiple_lang_addon_page.png',
                                )
                        )
                ),
                'pro' => array(
                        'header' => array(
                                'name' => __('Pro Add-ons', 'translatepress-multilingual'),
                                'description' => __('These addons extend your translation plugin and are available in the Business and Developer plans.', 'translatepress-multilingual'),
                        ),
                        'addons' => array(
                                array(  'slug' => 'tp-add-on-deepl/index.php',
                                        'type' => 'add-on',
                                        'name' => __( 'DeepL Automatic Translation', 'translatepress-multilingual' ),
                                        'description' => __( 'Automatically translate your website through the DeepL API.', 'translatepress-multilingual' ),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/deepl-add-on-page.png',
                                ),
                                array(  'slug' => 'tp-add-on-automatic-language-detection/tp-automatic-language-detection.php',
                                        'type' => 'add-on',
                                        'name' => __( 'Automatic User Language Detection', 'translatepress-multilingual' ),
                                        'description' => __( 'Prompts visitors to switch to their preferred language based on their browser settings or IP address and remembers the last visited language.', 'translatepress-multilingual' ),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/automatic_user_lang_detection_addon_page.png',
                                ),
                                array(  'slug' => 'tp-add-on-translator-accounts/index.php',
                                        'type' => 'add-on',
                                        'name' => __( 'Translator Accounts', 'translatepress-multilingual' ),
                                        'description' => __( 'Create translator accounts for new users or allow existing users that are not administrators to translate your website.', 'translatepress-multilingual' ),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/translator_accounts_addon_page.png',
                                ),
                                array(  'slug' => 'tp-add-on-browse-as-other-roles/tp-browse-as-other-role.php',
                                        'type' => 'add-on',
                                        'name' => __( 'Browse As User Role', 'translatepress-multilingual' ),
                                        'description' => __( 'Navigate your website just like a particular user role would. Really useful for dynamic content or hidden content that appears for particular users.', 'translatepress-multilingual' ),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/browse_as_user_role_addon_page.png',
                                ),
                                array(  'slug' => 'tp-add-on-navigation-based-on-language/tp-navigation-based-on-language.php',
                                        'type' => 'add-on',
                                        'name' => __( 'Navigation Based on Language', 'translatepress-multilingual' ),
                                        'description' => __( 'Configure different menu items for different languages.', 'translatepress-multilingual' ),
                                        'icon' => TRP_PLUGIN_URL . 'assets/images/navigation_based_on_lang_addon_page.png',
                                ),
                        )
                )
        );

        return $addons_strings_array;
    }

    private function slug_exists($array, $target_slug) {
        foreach ($array as $key => $value) {
            if ($key === 'slug' && $value === $target_slug) {
                return true;
            }
            if (is_array($value) && $this->slug_exists($value, $target_slug)) {
                return true;
            }
        }
        return false;
    }


    public function render() {
        $trp = TRP_Translate_Press::get_trp_instance();
        $translatepress_product_name = reset($trp->tp_product_name);
        $license_status = get_option('trp_license_status');
        $license_details = get_option('trp_license_details');
        set_transient('trp_onboarding_previous_step', 'addons', 3600); // 1 hour expiry

        // Validate that we have a valid license for automatic translation
        if ($license_status !== 'valid' || !isset($license_details['valid'][0])) {
            $license_status = 'invalid';
        }

        if ($this->errors instanceof WP_Error) {
            foreach ($this->errors->get_error_messages() as $message) {
                echo '<div class="notice notice-error"><p>' . esc_html($message) . '</p></div>';
            }
        }

        $addons_settings = get_option( 'trp_add_ons_settings', array() );

        ?>

        <h1><?php esc_html_e('Enable Modules', 'translatepress-multilingual'); ?></h1>
        <h3><?php esc_html_e('Enable Add-on modules to extend TranslatePress and enhance the functionality of your translated site.', 'translatepress-multilingual'); ?></h3>

        <?php if($license_status == 'invalid' || $translatepress_product_name == 'TranslatePress' ) : ?>
        <div class="trp-extra-languages-error">
            <div class="trp-upgrade-notice">
                <?php esc_html_e('More functionality with TranslatePress Pro.', 'translatepress-multilingual'); ?>
                <a href="https://translatepress.com/pricing/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=enable-addons" class="trp-upgrade-notice-button"><span><?php esc_html_e('Upgrade now ↗', 'translatepress-multilingual'); ?></span></a>
            </div>
            <p style="padding: 0 1rem;"><?php esc_html_e('Already a Pro User?', 'translatepress-multilingual'); ?> <a href="<?php echo esc_url(add_query_arg(['step' => 'install'])); ?>"> <?php esc_html_e('Activate License Key', 'translatepress-multilingual'); ?></a></p>
        </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('trp_onboarding_addons'); ?>
            <?php
            $addons = $this->get_addons();
            foreach ( $addons as $type => $addon_type ) : ?>
                <h3 id="trp-addon-text-h3"><?php echo esc_html( $addon_type['header']['name'] ); ?></h3>
                <h4 id="trp-addon-text-h4"><?php echo esc_html( $addon_type['header']['description'] ); ?></h4>
                <div class="add-ons-table">
                    <div id="add-on-row">
                        <div id="icon" class="trp-cell-add-on manage-column column-icon column-primary"></div>
                        <div id="add_on" class="trp-cell-add-on manage-column column-add_on"></div>
                        <div id="actions" class="trp-cell-add-on manage-column column-actions"></div>
                    </div>

                    <div class="the-list-addon-page">

                        <?php foreach ( $addon_type['addons'] as $addon) : ?>
                            <?php
                            $disabled = true;
                            if ($translatepress_product_name == 'TranslatePress Personal' && $type == 'advanced') {
                                $disabled = false;
                            }
                            if ($translatepress_product_name == 'TranslatePress Business' || $translatepress_product_name == 'TranslatePress Developer') {
                                $disabled = false;
                            }
                            if ($license_status == 'invalid') {
                                $disabled = true;
                            }

                            $active = false;
                            if (!$disabled) {
                                $active = !empty($addons_settings[$addon['slug']]);
                            }
                            ?>

                            <div class="add-on-row">
                                <div class="trp-cell-add-on icon column-icon" data-colname>
                                    <img class="addon-icon" src="<?php echo esc_html($addon['icon']); ?>" width="81px" height="81px" alt="<?php echo esc_html($addon['name']); ?>">
                                </div>

                                <div class="trp-cell-add-on column-add_on">
                                    <strong class="trp-add-ons-name trp-accent-text-bold"><?php echo esc_html($addon['name']); ?></strong>
                                    <br>
                                    <h4 class="trp-primary-text trp-addon-description"><?php echo esc_html($addon['description']); ?></h4>
                                </div>

                                <div class="trp-cell-add-on trp-addon-button">
                                    <div class="trp-switch">
                                        <input type="checkbox" id="trp-id-addon-<?php echo esc_attr($addon['slug']); ?>"
                                               class="trp-switch-input"
                                               name="is_active[<?php echo esc_attr( $addon['slug']); ?>]"
                                               value="yes"
                                                <?php echo $disabled ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''; ?>
                                                <?php checked($active); ?> />

                                        <label for="trp-id-addon-<?php echo esc_attr($addon['slug']); ?>" class="trp-switch-label"
                                                <?php echo $disabled ? 'title="'.esc_attr__('This add-on is not available on your current plan.', 'translatepress-multilingual') .'"' : ''; ?>
                                        ></label>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach;

            ?>
            <div class="trp-continue-onboarding"><button type="submit" class="trp-submit-btn"><?php esc_html_e('Continue', 'translatepress-multilingual');?></button></div>

        </form>
        <?php
    }
}