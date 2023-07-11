<?php

/**
 * Class TRP_Settings
 *
 * In charge of settings page and settings option.
 */
class TRP_Settings{

    protected $settings;
    protected $trp_query;
    protected $url_converter;
    protected $trp_languages;
    protected $machine_translator;

    /**
     * Return array of customization options for language switchers.
     *
     * Customization options include whether to add flags, full names or short names.
     * Used for all types of language switchers.
     *
     * @return array            Array with customization options.
     */
    public function get_language_switcher_options(){
        $ls_options = apply_filters( 'trp_language_switcher_output', array(
            'full-names'         => array( 'full_names'  => true, 'short_names'  => false, 'flags' => false, 'no_html' => false, 'label' => __( 'Full Language Names', 'translatepress-multilingual' ) ),
            'short-names'        => array( 'full_names'  => false, 'short_names'  => true, 'flags' => false, 'no_html' => false, 'label' => __( 'Short Language Names', 'translatepress-multilingual' ) ),
            'flags-full-names'   => array( 'full_names'  => true, 'short_names'  => false, 'flags' => true, 'no_html' => false, 'label' => __( 'Flags with Full Language Names', 'translatepress-multilingual' ) ),
            'flags-short-names'  => array( 'full_names'  => false, 'short_names'  => true, 'flags' => true, 'no_html' => false, 'label' => __( 'Flags with Short Language Names', 'translatepress-multilingual' ) ),
            'only-flags'         => array( 'full_names'  => false, 'short_names'  => false, 'flags' => true, 'no_html' => false, 'label' => __( 'Only Flags', 'translatepress-multilingual' ) ),
	        'full-names-no-html' => array( 'full_names'  => false, 'short_names'  => false, 'flags' => false, 'no_html' => true, 'label' => __( 'Full Language Names No HTML', 'translatepress-multilingual' ) )
        ) );
        return $ls_options;
    }

    /**
     * Echo html for selecting language from all available language in settings.
     *
     * @param string $ls_type       shortcode_options | menu_options | floater_options
     * @param string $ls_setting    The selected language switcher customization setting (get_language_switcher_options())
     */
    public function output_language_switcher_select( $ls_type, $ls_setting ){
        $ls_options = $this->get_language_switcher_options();
        // Use the full names no HTML option only for the menu - for extra compatibility with certain themes and menus
	    if ($ls_type !== 'menu-options'){
	    	unset($ls_options['full-names-no-html']);
	    }
        $output = '<select id="' . esc_attr( $ls_type ) . '" name="trp_settings[' . esc_attr( $ls_type ) .']" class="trp-select trp-ls-select-option">';
        foreach( $ls_options as $key => $ls_option ){
            $selected = ( $ls_setting == $key ) ? 'selected' : '';
            $output .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $ls_option['label'] ). '</option>';

        }
        $output .= '</select>';

        echo $output;/* phpcs:ignore */ /* escaped above */
    }

    /**
     * Echo html for selecting language selector position.
     *
     * @param string $ls_position    The selected language switcher position
     */
    public function output_language_switcher_floater_possition( $ls_position ){
        $ls_options = array(
            'bottom-right'  => array( 'label' => __( 'Bottom Right', 'translatepress-multilingual' ) ),
            'bottom-left'   => array( 'label' => __( 'Bottom Left', 'translatepress-multilingual' ) ),
            'top-right'     => array( 'label' => __( 'Top Right', 'translatepress-multilingual' ) ),
            'top-left'      => array( 'label' => __( 'Top Left', 'translatepress-multilingual' ) ),

        );

        $output = '<select id="floater-position" name="trp_settings[floater-position]" class="trp-select trp-ls-select-option">';
        foreach( $ls_options as $key => $ls_option ){
            $selected = ( $ls_position == $key ) ? 'selected' : '';
            $output .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $ls_option['label'] ). '</option>';
        }
        $output .= '</select>';

        echo $output; /* phpcs:ignore */ /* escaped above */
    }

	/**
	 * Echo html for selecting language selector color.
	 *
	 * @param string $ls_color    The selected language switcher color.
	 */
	public function output_language_switcher_floater_color( $ls_color ){
        $ls_options = array(
            'dark'  => array( 'label' => __( 'Dark', 'translatepress-multilingual' ) ),
            'light' => array( 'label' => __( 'Light', 'translatepress-multilingual' ) )
        );


		$output = '<select id="floater-color" name="trp_settings[floater-color]" class="trp-select trp-ls-select-option">';
		foreach( $ls_options as $key => $ls_option ){
			$selected = ( $ls_color == $key ) ? 'selected' : '';
			$output .= '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $ls_option['label'] ). '</option>';
		}
		$output .= '</select>';

		echo $output; /* phpcs:ignore */ /* escaped above */
	}

    /**
     * Returns settings_option.
     *
     * @return array        Settings option.
     */
    public function get_settings(){
        if ( $this->settings == null ){
            $this->set_options();
        }
        return $this->settings;
    }

    /**
     * Returns the value of an individual setting or the default provided.
     *
     * @param string $name
     * @param default mixed
     *
     * @return mixed Setting Value
     */
    public function get_setting($name, $default = null){
        if( array_key_exists($name, $this->settings ) ){
            return maybe_unserialize($this->settings[$name]);
        } else {
            return $default;
        }
    }

    /**
     * Register Settings subpage for TranslatePress
     */
    public function register_menu_page(){
        add_options_page( 'TranslatePress', 'TranslatePress', apply_filters( 'trp_settings_capability', 'manage_options' ), 'translate-press', array( $this, 'settings_page_content' ) );

        add_submenu_page( 'TRPHidden', 'TranslatePress Addons', 'TRPHidden', 'manage_options', 'trp_addons_page', array($this, 'addons_page_content') );
    }

    /**
     * Settings page content.
     */
    public function settings_page_content(){
	    if ( ! $this->trp_languages ){
            $trp                 = TRP_Translate_Press::get_trp_instance();
            $this->trp_languages = $trp->get_component( 'languages' );
        }

        $languages = $this->trp_languages->get_languages( 'english_name' );

        require_once TRP_PLUGIN_DIR . 'partials/main-settings-page.php';
    }

    /**
     * Addons page content.
     */
    public function addons_page_content(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $install_plugins = $trp->get_component('install_plugins');

        $active_plugin = __('Active', 'translatepress-multilingual');
        $inactive_plugin = __('Install & Activate', 'translatepress-multilingual');

        $plugins = array( 'pb', 'pms' );
        $plugin_settings = array();
        foreach($plugins as $plugin ){
            $plugin_settings[$plugin] = array();
            if ( $install_plugins->is_plugin_active( $plugin ) ) {
                $plugin_settings[$plugin]['install_button'] = $active_plugin;
                $plugin_settings[$plugin]['disabled']       = 'disabled';
            }else{
                $plugin_settings[$plugin]['install_button'] = $inactive_plugin;
                $plugin_settings[$plugin]['disabled']       = '';
            }
        }

        require_once TRP_PLUGIN_DIR . 'partials/addons-settings-page.php';
    }

    /**
     * Register settings option.
     */
    public function register_setting(){
        register_setting( 'trp_settings', 'trp_settings', array( $this, 'sanitize_settings' ) );
    }

    /**
     * Sanitizes settings option after save.
     *
     * Updates menu items for languages to be used in Menus.
     *
     * @param array $settings       Raw settings option.
     * @return array                Sanitized option page.
     */
    public function sanitize_settings( $settings ){
        if ( ! $this->trp_query ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->trp_query = $trp->get_component( 'query' );
        }
        if ( ! $this->trp_languages ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->trp_languages = $trp->get_component( 'languages' );
        }
        if ( !isset ( $settings['default-language'] ) ) {
            $settings['default-language'] = 'en_US';
        }
        if ( !isset ( $settings['translation-languages'] ) ){
            $settings['translation-languages'] = array();
        }
        if ( !isset ( $settings['publish-languages'] ) ){
            $settings['publish-languages'] = array();
        }

        $settings['translation-languages'] = array_filter( array_unique( $settings['translation-languages'] ) );
        $settings['publish-languages'] = array_filter( array_unique( $settings['publish-languages'] ) );

        if ( ! in_array( $settings['default-language'], $settings['translation-languages'] ) ){
            array_unshift( $settings['translation-languages'], $settings['default-language'] );
        }
        if ( ! in_array( $settings['default-language'], $settings['publish-languages'] ) ){
            array_unshift( $settings['publish-languages'], $settings['default-language'] );
        }

        // check if submitted language codes are valid. Default language is included here too
        $check_language_codes = array_unique( array_merge($settings['translation-languages'], $settings['publish-languages']) );
        foreach($check_language_codes as $check_language_code ){
            if ( !trp_is_valid_language_code($check_language_code) ){
                add_settings_error( 'trp_advanced_settings', 'settings_error', esc_html__('Invalid language code. Please try again.', 'translatepress-multilingual'), 'error' );
                return get_option( 'trp_settings', 'not_set' );
            }
        }

        if( !empty( $settings['native_or_english_name'] ) )
            $settings['native_or_english_name'] = sanitize_text_field( $settings['native_or_english_name']  );
        else
            $settings['native_or_english_name'] = 'english_name';

        if( !empty( $settings['add-subdirectory-to-default-language'] ) )
            $settings['add-subdirectory-to-default-language'] = sanitize_text_field( $settings['add-subdirectory-to-default-language']  );
        else
            $settings['add-subdirectory-to-default-language'] = 'no';

        if( !empty( $settings['force-language-to-custom-links'] ) )
            $settings['force-language-to-custom-links'] = sanitize_text_field( $settings['force-language-to-custom-links']  );
        else
            $settings['force-language-to-custom-links'] = 'no';


        if ( !empty( $settings['trp-ls-floater'] ) ){
            $settings['trp-ls-floater'] = sanitize_text_field( $settings['trp-ls-floater'] );
        }else{
            $settings['trp-ls-floater'] = 'no';
        }

        $language_switcher_options = $this->get_language_switcher_options();
        if ( ! isset( $language_switcher_options[ $settings['shortcode-options'] ] ) ){
            $settings['shortcode-options'] = 'flags-full-names';
        }
        if ( ! isset( $language_switcher_options[ $settings['menu-options'] ] ) ){
            $settings['menu-options'] = 'flags-full-names';
        }
        if ( ! isset( $language_switcher_options[ $settings['floater-options'] ] ) ){
            $settings['floater-options'] = 'flags-full-names';
        }

        if ( ! isset( $settings['floater-position'] ) ){
            $settings['floater-position'] = 'bottom-right';
        }

	    if ( ! isset( $settings['floater-color'] ) ){
		    $settings['floater-color'] = 'dark';
	    }

	    if ( !empty( $settings['trp-ls-show-poweredby'] ) ){
		    $settings['trp-ls-show-poweredby'] = sanitize_text_field( $settings['trp-ls-show-poweredby'] );
	    }else{
		    $settings['trp-ls-show-poweredby'] = 'no';
	    }

        if ( ! isset( $settings['url-slugs'] ) ){
            $settings['url-slugs'] = $this->trp_languages->get_iso_codes( $settings['translation-languages'] );
        }

        foreach( $settings['translation-languages'] as $language_code ){
            if ( empty ( $settings['url-slugs'][$language_code] ) ){
                $settings['url-slugs'][$language_code] = $language_code;
            }else{
                $settings['url-slugs'][$language_code] = sanitize_title( strtolower( $settings['url-slugs'][$language_code] )) ;
            }
        }

        foreach ($settings['translation-languages'] as $value=>$language){
            if(isset($settings['translation-languages-formality'][$value])) {
                if ( $settings['translation-languages-formality'][ $value ] == 'informal' ) {
                    $settings['translation-languages-formality-parameter'][ $language ] = 'informal';
                } else {
                    if ( $settings['translation-languages-formality'][ $value ] == 'formal' ) {
                        $settings['translation-languages-formality-parameter'][ $language ] = 'formal';
                    } else {
                        $settings['translation-languages-formality-parameter'][ $language ] = 'default';
                    }
                }
            }
        }

        unset($settings['translation-languages-formality']);

        // check for duplicates in url slugs
        $duplicate_exists = false;
        foreach( $settings['url-slugs'] as $urlslug ) {
            if ( count ( array_keys( $settings['url-slugs'], $urlslug ) ) > 1 ){
                $duplicate_exists = true;
                break;
            }
        }
        if ( $duplicate_exists ){
            foreach( $settings['translation-languages'] as $language_code ) {
                $settings['url-slugs'][$language_code] = $language_code;
            }
        }

        $this->create_menu_entries( $settings['publish-languages'] );

        $gettext_table_creation = $this->trp_query->get_query_component('gettext_table_creation');
        require_once( ABSPATH . 'wp-includes/load.php' );
        foreach ( $settings['translation-languages'] as $language_code ){
            if ( $settings['default-language'] != $language_code ) {
                $this->trp_query->check_table( $settings['default-language'], $language_code );
            }
            wp_download_language_pack( $language_code );
            $gettext_table_creation->check_gettext_table( $language_code );
        }

        //in version 1.6.6 we normalized the original strings and created new tables
        $this->trp_query->check_original_table();
        $this->trp_query->check_original_meta_table();
        $gettext_table_creation->check_gettext_original_table();
        $gettext_table_creation->check_gettext_original_meta_table();

        // regenerate permalinks in case something changed
        flush_rewrite_rules();

        return apply_filters( 'trp_extra_sanitize_settings', $settings );
    }

    /**
     * Output admin notices after saving settings.
     */
    public function admin_notices(){
        settings_errors( 'trp_settings' );
    }

    /**
     * Set options array variable to be used across plugin.
     *
     * Sets a default option if it does not exist.
     */
    protected function set_options(){
        $settings_option = get_option( 'trp_settings', 'not_set' );

        // initialize default settings
        $default = get_locale();
        if ( empty( $default ) ){
            $default = 'en_US';
        }
        $default_settings = array(
            'default-language'                     => $default,
            'translation-languages'                => array( $default ),
            'publish-languages'                    => array( $default ),
            'native_or_english_name'               => 'english_name',
            'add-subdirectory-to-default-language' => 'no',
            'force-language-to-custom-links'       => 'yes',
            'trp-ls-floater'                       => 'yes',
            'shortcode-options'                    => 'flags-full-names',
            'menu-options'                         => 'flags-full-names',
            'floater-options'                      => 'flags-full-names',
            'floater-position'                     => 'bottom-right',
	        'floater-color'                        => 'dark',
	        'trp-ls-show-poweredby'                => 'no',
            'url-slugs'                            => array( 'en_US' => 'en', '' ),
        );

        if ( 'not_set' == $settings_option ){
            update_option ( 'trp_settings', $default_settings );
            $settings_option = $default_settings;
        }else{
            // Add any missing default option for trp_setting
            foreach ( $default_settings as $key_default_setting => $value_default_setting ){
                if ( !isset ( $settings_option[$key_default_setting] ) ) {
                    $settings_option[$key_default_setting] = $value_default_setting;
                }
            }
        }

        // Might have saved invalid language codes in the past so this code protects against SQL Injections using invalid language codes which are used in queries
        $check_language_codes = array_unique( array_merge($settings_option['translation-languages'], $settings_option['publish-languages']) );
        foreach($check_language_codes as $check_language_code ) {
            if ( !trp_is_valid_language_code( $check_language_code ) ) {
                add_filter('plugins_loaded', array($this, 'show_invalid_language_codes_error_notice'), 999999);
            }
        }


        /**
         * These options (trp_advanced_settings,trp_machine_translation_settings) are not part of the actual trp_settings DB option.
         * But they are included in $settings variable across TP
         */
        $settings_option['trp_advanced_settings'] = get_option('trp_advanced_settings', array() );

        // Add any missing default option for trp_machine_translation_settings
        $default_trp_machine_translation_settings = $this->get_default_trp_machine_translation_settings();
        $settings_option['trp_machine_translation_settings'] = array_merge( $default_trp_machine_translation_settings, get_option( 'trp_machine_translation_settings', $default_trp_machine_translation_settings ) );


        /* @deprecated Setting only used for compatibility with Deepl Add-on 1.0.0 */
        if ( $settings_option['trp_machine_translation_settings']['translation-engine'] === 'deepl' && defined( 'TRP_DL_PLUGIN_VERSION' ) && TRP_DL_PLUGIN_VERSION === '1.0.0' ) {
            $trp_languages = new TRP_Languages();
            $settings_option['machine-translate-codes'] = $trp_languages->get_iso_codes($settings_option['translation-languages']);
            if ( isset( $settings_option['trp_machine_translation_settings']['deepl-api-key'] ) ) {
                $settings_option['deepl-api-key'] = $settings_option['trp_machine_translation_settings']['deepl-api-key'];
            }
        }

        $this->settings = $settings_option;
    }

    public function show_invalid_language_codes_error_notice(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $error_manager = $trp->get_component( 'error_manager' );

        $error_manager->record_error(
            array( 'message'         => esc_html__('Language codes can contain only A-Z a-z 0-9 - _ characters. Check your language codes in TranslatePress General Settings.', 'translatepress-multilingual'),
                   'notification_id' => 'trp_invalid_language_code' ) );
    }

    public function get_default_trp_machine_translation_settings(){
        return apply_filters( 'trp_get_default_trp_machine_translation_settings', array(
            // default settings for trp_machine_translation_settings
            'machine-translation'               => 'no',
            'translation-engine'                => 'google_translate_v2',
            'block-crawlers'                    => 'yes',
            'automatically-translate-slug'      => 'yes',
            'machine_translation_counter_date'  => date ("Y-m-d" ),
            'machine_translation_counter'       => 0,
            'machine_translation_limit'         => 1000000
            /*
             * These settings are merged into the saved DB option.
             * Be sure to set any checkboxes options to 'no' in sanitize_settings.
             * Unchecked checkboxes don't have a POST value when saving settings so they will be overwritten by merging.
             */
        ));
    }

    /**
     * Enqueue scripts and styles for settings page.
     *
     * @param string $hook          Admin page.
     */
    public function enqueue_scripts_and_styles( $hook ) {
        if( in_array( $hook, [ 'settings_page_translate-press', 'admin_page_trp_license_key', 'admin_page_trp_addons_page', 'admin_page_trp_advanced_page', 'admin_page_trp_machine_translation', 'admin_page_trp_test_machine_api', 'admin_page_trp_optin_page' ] ) ){
            wp_enqueue_style(
                'trp-settings-style',
                TRP_PLUGIN_URL . 'assets/css/trp-back-end-style.css',
                array(),
                TRP_PLUGIN_VERSION
            );
        }

        if( in_array( $hook, array( 'settings_page_translate-press', 'admin_page_trp_advanced_page', 'admin_page_trp_machine_translation' ) ) ) {
            wp_enqueue_script( 'trp-settings-script', TRP_PLUGIN_URL . 'assets/js/trp-back-end-script.js', array( 'jquery', 'jquery-ui-sortable' ), TRP_PLUGIN_VERSION );

            if ( ! $this->trp_languages ){
                $trp                 = TRP_Translate_Press::get_trp_instance();
                $this->trp_languages = $trp->get_component( 'languages' );
            }

            $all_language_codes = $this->trp_languages->get_all_language_codes();
            $iso_codes          = $this->trp_languages->get_iso_codes( $all_language_codes, false );

            $tp_data = get_option('trp_db_stored_data', array() );
            $languages_that_support_formality = isset( $tp_data['trp_mt_supported_languages']['deepl'] ) ? $tp_data['trp_mt_supported_languages']['deepl']['formality-supported-languages'] : '' ;

            wp_localize_script( 'trp-settings-script', 'trp_url_slugs_info', array( 'iso_codes'                         => $iso_codes,
                                                                                                      'languages_that_support_formality'  => $languages_that_support_formality,
                                                                                                      'error_message_duplicate_slugs'     => __( 'Error! Duplicate URL slug values.', 'translatepress-multilingual' ),
                                                                                                      'error_message_formality'           => wp_kses( __( 'You cannot select two languages that have the same <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">iso code</a> but different formalities because doing so will lead to duplicate <a href="https://developers.google.com/search/docs/specialty/international/localized-versions" target="_blank">hreflang tags</a>.', 'translatepress-multilingual'), [ 'a' => [ 'href' => [], 'class' => [], 'rel' => [], 'target' => [] ] ] ),
                                                                                                      'error_message_duplicate_languages' => wp_kses( __( 'Duplicate language detected.<br>Each language can only be added once to ensure accurate translation management.<br> Please change the duplicate language entry and try again. ', 'translatepress-multilingual'), [ 'br' => [] ] )
                                                                                               )
            );

            wp_enqueue_script( 'trp-select2-lib-js', TRP_PLUGIN_URL . 'assets/lib/select2-lib/dist/js/select2.min.js', array( 'jquery' ), TRP_PLUGIN_VERSION );
            wp_enqueue_style( 'trp-select2-lib-css', TRP_PLUGIN_URL . 'assets/lib/select2-lib/dist/css/select2.min.css', array(), TRP_PLUGIN_VERSION );

        }

        if( in_array( $hook, array( 'admin_page_trp_addons_page' ) ) ) {
            wp_enqueue_script( 'trp-add-ons-script', TRP_PLUGIN_URL . 'assets/js/trp-back-end-add-ons.js', array( ), TRP_PLUGIN_VERSION, true );
            wp_localize_script( 'trp-add-ons-script', 'trp_addons_localized', array( 'admin_ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' =>  wp_create_nonce( 'trp_install_plugins' )) );
        }
    }

    /**
     * Output HTML for Translation Language option.
     *
     * Hooked to trp_language_selector.
     *
     * @param array $languages          All available languages.
     */
    public function languages_selector( $languages ){
        if ( ! $this->url_converter ) {
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->url_converter = $trp->get_component('url_converter');
        }
        $selected_language_code = '';

        require_once TRP_PLUGIN_DIR . 'partials/main-settings-language-selector.php';
    }

    /**
     * Update language switcher menu items.
     *
     * @param array $languages          Array of language codes to create menu items for.
     */
    public function create_menu_entries( $languages ){
        if ( ! $this->trp_languages ){
            $trp = TRP_Translate_Press::get_trp_instance();
            $this->trp_languages = $trp->get_component( 'languages' );
        }
        $published_languages = $this->trp_languages->get_language_names( $languages, 'english_name' );
        $published_languages['current_language'] = __( 'Current Language', 'translatepress-multilingual' );
        $languages[] = 'current_language';
        $posts = get_posts( array( 'post_type' =>'language_switcher',  'posts_per_page'   => -1  ) );

        if ( count( $published_languages ) == 3 ){
            $languages[] = 'opposite_language';
            $published_languages['opposite_language'] = __( 'Opposite Language', 'translatepress-multilingual' );
        }

        foreach ( $published_languages as $language_code => $language_name ) {
            $existing_ls = null;
            foreach( $posts as $post ){
                if ( $post->post_content == $language_code ){
                    $existing_ls = $post;
                    break;
                }
            }

            $ls = array(
                'post_title' => $language_name,
                'post_content' => $language_code,
                'post_status' => 'publish',
                'post_type' => 'language_switcher'
            );
            if ( $existing_ls ){
                $ls['ID'] = $existing_ls->ID;
                wp_update_post( $ls );
            }else{
                wp_insert_post( $ls );
            }
        }

        foreach ( $posts as $post ){
            if ( ! in_array( $post->post_content, $languages ) ){
                wp_delete_post( $post->ID );
            }
        }
    }

    /**
     * Add navigation tabs in settings.
     *
     */
    public function add_navigation_tabs(){
        $tabs = array(
            array(
                'name'  => __( 'General', 'translatepress-multilingual' ),
                'url'   => admin_url( 'options-general.php?page=translate-press' ),
                'page'  => 'translate-press'
            ),
            array(
                'name'  => __( 'Translate Site', 'translatepress-multilingual' ),
                'url'   => add_query_arg( 'trp-edit-translation', 'true', home_url() ),
                'page'  => 'trp_translation_editor'
            ),
	        array(
		        'name'  => __( 'Addons', 'translatepress-multilingual' ),
		        'url'   => admin_url( 'admin.php?page=trp_addons_page' ),
		        'page'  => 'trp_addons_page'
	        ),
        );

        if( class_exists( 'TRP_LICENSE_PAGE' ) ) {
            $tabs[] = array(
                'name'  => __( 'License', 'translatepress-multilingual' ),
                'url'   => admin_url( 'admin.php?page=trp_license_key' ),
                'page'  => 'trp_license_key'
            );
        }

	    $tabs = apply_filters( 'trp_settings_tabs', $tabs );

        $active_tab = 'translate-press';
        if ( isset( $_GET['page'] ) ){
            $active_tab = sanitize_text_field( wp_unslash( $_GET['page'] ) );
        }

        require TRP_PLUGIN_DIR . 'partials/settings-navigation-tabs.php';
    }

    /**
     * Add SVG icon symbols to use throughout the admin.
     */
    public function add_svg_icons() {
        ?>
        <svg width="0" height="0" class="hidden">
			<symbol aria-hidden="true" data-prefix="fas" data-icon="check-circle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="check-circle">
                <path fill="currentColor" d="M504 256c0 136.967-111.033 248-248 248S8 392.967 8 256 119.033 8 256 8s248 111.033 248 248zM227.314 387.314l184-184c6.248-6.248 6.248-16.379 0-22.627l-22.627-22.627c-6.248-6.249-16.379-6.249-22.628 0L216 308.118l-70.059-70.059c-6.248-6.248-16.379-6.248-22.628 0l-22.627 22.627c-6.248 6.248-6.248 16.379 0 22.627l104 104c6.249 6.249 16.379 6.249 22.628.001z"></path>
            </symbol>
            <symbol aria-hidden="true" data-prefix="fas" data-icon="times-circle" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" id="times-circle">
                <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm121.6 313.1c4.7 4.7 4.7 12.3 0 17L338 377.6c-4.7 4.7-12.3 4.7-17 0L256 312l-65.1 65.6c-4.7 4.7-12.3 4.7-17 0L134.4 338c-4.7-4.7-4.7-12.3 0-17l65.6-65-65.6-65.1c-4.7-4.7-4.7-12.3 0-17l39.6-39.6c4.7-4.7 12.3-4.7 17 0l65 65.7 65.1-65.6c4.7-4.7 12.3-4.7 17 0l39.6 39.6c4.7 4.7 4.7 12.3 0 17L312 256l65.6 65.1z"></path>
            </symbol>
        </svg>
        <?php
    }

    /**
     * Plugin action links.
     *
     * Adds action links to the plugin list table
     *
     * Fired by `plugin_action_links` filter.
     *
     * @param array $links An array of plugin action links.
     *
     * @return array An array of plugin action links.
     */
    public function plugin_action_links( $links ) {
        $settings_link = sprintf( '<a href="%1$s">%2$s</a>', admin_url( 'options-general.php?page=translate-press' ), __( 'Settings', 'translatepress-multilingual' ) );

        array_unshift( $links, $settings_link );

        if( !trp_is_paid_version() ) {
            $links['go_pro'] = sprintf( '<a href="%1$s" target="_blank" style="color: #e76054; font-weight: bold;">%2$s</a>', esc_url( trp_add_affiliate_id_to_link( 'https://translatepress.com/pricing/?utm_source=wpbackend&utm_medium=clientsite&utm_content=tpeditor&utm_campaign=tpfree' ) ), esc_html__( 'Pro Features', 'translatepress-multilingual' ) );
        }else {
            $license_details = get_option( 'trp_license_details' );
            $is_demosite     = ( strpos( site_url(), 'https://demo.translatepress.com' ) !== false );
            if ( !empty( $license_details ) && !$is_demosite ) {
                if ( !empty( $license_details['invalid'] ) ) {
                    $license_detail = $license_details['invalid'][0];
                    if ( isset( $license_detail->error ) && $license_detail->error == 'missing' ) {
                        $links['license'] = sprintf( '<a href="%1$s" target="_blank" style="color: #e76054; font-weight: bold;">%2$s</a>', esc_url(trp_add_affiliate_id_to_link( admin_url( '/admin.php?page=trp_license_key' ) ) ), esc_html__( 'Activate License', 'translatepress-multilingual' ) );
                    }
                }
            }
        }
        return $links;
    }

    public function trp_dismiss_email_course(){

        $user_id = get_current_user_id();

        if( empty( $user_id ) )
            die();

        update_user_meta( $user_id, 'trp_email_course_dismissed', 1 );
        die();
        
    }

}
