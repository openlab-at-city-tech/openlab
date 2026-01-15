<?php
class TRP_Step_Languages implements TRP_Onboarding_Step_Interface {
    protected array $settings;
    protected TRP_Settings $settings_class;
    protected array $languages;
    protected WP_Error $errors;

    public function __construct( $settings ){
        $this->settings = $settings;
        $trp = TRP_Translate_Press::get_trp_instance();
        $this->languages = $trp->get_component('languages')->get_languages();
        $this->settings_class = $trp->get_component('settings');
        $this->errors = new WP_Error();

        $status = get_option('trp_license_status');
        $multiple_lang_addon_slug = 'tp-add-on-extra-languages/tp-extra-languages.php';
        $addons = get_option('trp_add_ons_settings', array());
        $multiple_lang_status = isset($addons[$multiple_lang_addon_slug]) ? $addons[$multiple_lang_addon_slug] : false;

        if ($status == 'valid' && !array_key_exists('translatepress-multilingual', $trp->tp_product_name)){
            // force multiple languages addon to be enabled if license is valid, and we're not on a free license.
            if(is_array($addons) && !$multiple_lang_status){
                $addons[$multiple_lang_addon_slug] = true;
            }
        } else {
            $addons[$multiple_lang_addon_slug] = false;
        }

        if ($multiple_lang_status !== $addons[$multiple_lang_addon_slug]){
            update_option('trp_add_ons_settings', $addons);
            wp_redirect( add_query_arg( null, null ) );
            exit;
        }
    }

    public function handle($data) {
        if (!wp_verify_nonce($data['_wpnonce_trp_onboarding_languages'], 'trp_onboarding_languages')) {
            $this->errors->add('nonce_fail_languages', __('The link you followed has expired. Please reload the page and try again.', 'translatepress-multilingual'));
        } elseif (empty($data['default_language'])) {
            $this->errors->add('empty_default_language', __('You need to select a default language.', 'translatepress-multilingual'));
        } elseif (!$this->valid_language($data['default_language'])) {
            $this->errors->add('invalid_default_language', __('You are trying to add an invalid default language. Please select a valid option.', 'translatepress-multilingual'));
        } elseif(empty($data['translation_languages'])) {
            $this->errors->add('empty_additional_language', __('Please add an additional language.', 'translatepress-multilingual'));
        } else {
            foreach ($data['translation_languages'] as $additional_language){
                if (!$this->valid_language($additional_language)) {
                    if (!$this->errors->get_error_message('invalid_additional_language')){
                        $this->errors->add('invalid_additional_language', __('You are trying to add an invalid additional language. Please select a valid option.', 'translatepress-multilingual'));
                    }
                }
            }
        }

        if (!$this->errors->has_errors()) {
            // If no errors, we save our data and redirect to next step
            $this->save_languages($data);
            wp_redirect(add_query_arg(['step' => 'switcher']));
            exit;
        }
    }

    public function render() {
        // Store that we're on the languages step for install step's "Go back" navigation
        set_transient('trp_onboarding_previous_step', 'languages', 3600); // 1 hour expiry
        
        $default_language = $this->settings['default-language'];
        $translation_languages = $this->settings['translation-languages'];

        foreach($translation_languages as $key => $language) {
            if ($language === $default_language) {
                unset($translation_languages[$key]); // translation-languages come with the default language as part of them
            }
        }

        $url_slugs = isset($this->settings['url-slugs']) ? $this->settings['url-slugs'] : array();
        $default_slug = isset($url_slugs[$default_language]) ? $url_slugs[$default_language] : '';

        foreach ($this->errors->get_error_messages() as $message) {
            echo '<div class="ob-notice ob-notice-error">' . esc_html($message) . '</div>';
        }
        ?>
        <h1><?php esc_html_e('Configure Site Languages', 'translatepress-multilingual'); ?></h1>
        <h3><?php esc_html_e('Select the default and additional languages for your website.', 'translatepress-multilingual'); ?><br/>
            <?php esc_html_e('You can edit your site languages at any point.', 'translatepress-multilingual'); ?></h3>
        <form method="post">
            <?php wp_nonce_field('trp_onboarding_languages', '_wpnonce_trp_onboarding_languages'); ?>
            <label for="trp-default-language"><?php esc_html_e('Default Language', 'translatepress-multilingual'); ?></label>
            <div class="trp-default-language trp-language-wrap">
                <select name="default_language" class="trp-select2">
                    <?php
                    foreach ($this->languages as $lang_code => $language) {
                        echo '<option value="' . esc_attr($lang_code) . '"' . selected($lang_code, $default_language, false) . '>' . esc_html($language) . '</option>';
                    }
                    ?>
                </select>
                <div class="trp-slug-field" style="display: none;"><input type="hidden" name="url_slugs[<?php echo esc_attr($default_language); ?>]" value="<?php echo esc_attr($default_slug); ?>"/></div>
            </div>
            <p class="trp-onboarding-description"><?php esc_html_e('Select the language your content is written in.', 'translatepress-multilingual'); ?></p>

            <?php foreach ($translation_languages as $translation_lang_code) : ?>
            <div class="trp-additional-language trp-language-wrap">
                <div class="trp-language-field">
                    <label><?php esc_html_e('Additional Language', 'translatepress-multilingual'); ?></label>
                    <select name="translation_languages[]" class="trp-select2">
                        <option value=""><?php esc_html_e('Choose a secondary language...', 'translatepress-multilingual');?></option>
                        <?php
                        foreach ($this->languages as $lang_code => $language) {
                            echo '<option value="' . esc_attr($lang_code) . '" '. selected($lang_code, $translation_lang_code, false) .' >' . esc_html($language) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="trp-slug-field">
                    <label><?php esc_html_e('Slug', 'translatepress-multilingual'); ?></label>
                    <input type="text" name="url_slugs[<?php echo esc_attr($translation_lang_code); ?>]" value="<?php echo esc_attr(isset($url_slugs[$translation_lang_code]) ? $url_slugs[$translation_lang_code] : ''); ?>" autocomplete="off" class="trp-language-slug-input"/>
                </div>
                <a class="trp-remove-language" href="#"><?php esc_html_e('Remove', 'translatepress-multilingual');?></a>
            </div>
            <?php endforeach; ?>
            <div class="trp-add-language-wrap">
                <a id="trp-add-language" href="#" class="trp-button-secondary" style="display: inline-block"><?php esc_html_e('Add Language', 'translatepress-multilingual');?></a>
            </div>
            <div class="trp-continue-onboarding"><button class="trp-submit-btn" type="submit"><?php esc_html_e('Continue', 'translatepress-multilingual');?></button></div>

        </form>
        <template id="trp-add-language-template">
            <div class="trp-additional-language trp-language-wrap">
                <div class="trp-language-field">
                    <label><?php esc_html_e('Additional Language', 'translatepress-multilingual'); ?></label>
                    <select name="translation_languages[]" class="trp-select2">
                        <option value=""><?php esc_html_e('Choose a language...', 'translatepress-multilingual');?></option>
                        <?php
                        foreach ($this->languages as $lang_code => $language) {
                            echo '<option value="' . esc_attr($lang_code) . '">' . esc_html($language) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="trp-slug-field">
                    <label><?php esc_html_e('Slug', 'translatepress-multilingual'); ?></label>
                    <input type="text" name="url_slugs[]" autocomplete="off" class="trp-language-slug-input"/>
                </div>
                <a class="trp-remove-language" href="#"><?php esc_html_e('Remove', 'translatepress-multilingual');?></a>
            </div>
        </template>
        <template id="trp-languages-error">
            <div class="trp-extra-languages-error">
                <div class="trp-upgrade-notice">
                    <?php esc_html_e('Add more than two languages with TranslatePress Pro.', 'translatepress-multilingual'); ?>
                    <a href="https://translatepress.com/pricing/?utm_source=tp-onboarding&utm_medium=client-site&utm_campaign=add-languages" class="trp-upgrade-notice-button"><span><?php esc_html_e('Upgrade now ↗', 'translatepress-multilingual'); ?></span></a>
                </div>
                <p><?php esc_html_e('Already a Pro User?', 'translatepress-multilingual'); ?> <a href="<?php echo esc_url(add_query_arg(['step' => 'install'])); ?>"> <?php esc_html_e('Activate License Key', 'translatepress-multilingual'); ?></a></p>
            </div>
        </template>
        <?php
    }

    private function valid_language($default_language)
    {
        if (!array_key_exists($default_language, $this->languages)) {
            return false;
        }
        return true;
    }
    /*
     * Minimal processing before saving Languages settings.
     * Full processing happens inside TRP_Settings -> sanitize_settings() due to register_setting() triggering a hook on update_option()
     */
    private function save_languages($data){
        $trp_settings = get_option('trp_settings', array());
        $trp_settings['default-language'] = sanitize_text_field($data['default_language']);

        if ( !isset ( $data['translation_languages'] ) ){
            $trp_settings['translation-languages'] = array();
        }
        $trp_settings['translation-languages'] = array_filter( array_unique( $data['translation_languages'] ) );

        if ( !in_array( $data['default_language'], $data['translation_languages'] ) ){
            array_unshift( $trp_settings['translation-languages'], $data['default_language'] );
        }

        // we need to add information to published languages as well.
        $trp_settings['publish-languages'] = $trp_settings['translation-languages'];

        // map slugs array
        foreach ($data['url_slugs'] as $slug_key => $url_slug) {
            if (!in_array($slug_key, $trp_settings['translation-languages'])) {
                // ignore incorrect slug mappings to not pollute the settings. Might be overkill.
                unset($data['url_slugs'][$slug_key]);
            }
        }
        $trp_settings['url-slugs'] = $data['url_slugs'];

        // This is important. Without it the tables are not being generated.
        $trp_settings = $this->settings_class->sanitize_settings( $trp_settings );
        update_option('trp_settings', $trp_settings);
    }
}