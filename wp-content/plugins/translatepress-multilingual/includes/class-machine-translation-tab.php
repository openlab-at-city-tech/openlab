<?php

class TRP_Machine_Translation_Tab {

    private $settings;

    public function __construct( $settings ) {

        $this->settings = $settings;

        add_action( 'plugins_loaded', array( $this, 'add_upsell_filter' ) );
        add_filter( 'trp_machine_translate_slug', array( $this, 'add_enable_auto_translate_slug_filter' ) );

    }

    /*
    * Add new tab to TP settings
    *
    * Hooked to trp_settings_tabs
    */
    public function add_tab_to_navigation( $tabs ){
        $tab = array(
            'name'  => __( 'Automatic Translation', 'translatepress-multilingual' ),
            'url'   => admin_url( 'admin.php?page=trp_machine_translation' ),
            'page'  => 'trp_machine_translation'
        );

        array_splice( $tabs, 2, 0, array( $tab ) );

        return $tabs;
    }

    /*
    * Add submenu for advanced page tab
    *
    * Hooked to admin_menu
    */
    public function add_submenu_page() {
        add_submenu_page( 'TRPHidden', 'TranslatePress Automatic Translation', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_machine_translation', array( $this, 'machine_translation_page_content' ) );
        add_submenu_page( 'TRPHidden', 'TranslatePress Test Automatic Translation API', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_test_machine_api', array( $this, 'test_api_page_content' ) );
    }

    /**
    * Register setting
    *
    * Hooked to admin_init
    */
    public function register_setting(){
        register_setting( 'trp_machine_translation_settings', 'trp_machine_translation_settings', array( $this, 'sanitize_settings' ) );
    }

    /**
    * Output admin notices after saving settings.
    */
    public function admin_notices(){
        if( isset( $_GET['page'] ) && $_GET['page'] == 'trp_machine_translation' )
            settings_errors();
    }

    /*
    * Sanitize settings
    */
    public function sanitize_settings($mt_settings ){

        $free_version = !class_exists( 'TRP_Handle_Included_Addons' );
        $seo_pack_active = class_exists( 'TRP_IN_Seo_Pack');
        $trp = TRP_Translate_Press::get_trp_instance();
        $machine_translator = $trp->get_component( 'machine_translator' );
        $settings = array();
        $machine_translation_keys = array( 'machine-translation', 'translation-engine', 'google-translate-key', 'deepl-api-type', 'deepl-api-key', 'block-crawlers', 'automatically-translate-slug', 'machine_translation_limit', 'machine_translation_log' );
        foreach( $machine_translation_keys as $key ){
            if( isset( $mt_settings[$key] ) ){
                $settings[$key] = $mt_settings[$key];
            }
        }
        if( !empty( $settings['machine-translation'] ) ) {
            $settings['machine-translation'] = sanitize_text_field( $settings['machine-translation'] );
            if ( $settings['machine-translation'] === 'yes') {
                $machine_translator->check_languages_availability( $this->settings['translation-languages'], true );
            }
        }else
            $settings['machine-translation'] = 'no';

        if( !empty( $settings['translation-engine'] ) )
            $settings['translation-engine'] = sanitize_text_field( $settings['translation-engine']  );
        else
            $settings['translation-engine'] = 'google_translate_v2';

        if($settings['translation-engine'] == 'deepl_upsell' && !class_exists( 'TRP_DeepL' ) && !class_exists( 'TRP_IN_DeepL' )){
            $settings['translation-engine'] = 'google_translate_v2';
        }

        if( !empty( $settings['block-crawlers'] ) )
            $settings['block-crawlers'] = sanitize_text_field( $settings['block-crawlers']  );
        else
            $settings['block-crawlers'] = 'no';

        if( $free_version || !$seo_pack_active ){
            $mt_settings_option = get_option( 'trp_machine_translation_settings' );
            if( isset( $mt_settings_option['automatically-translate-slug'] ) ){
                $settings['automatically-translate-slug'] = $mt_settings_option['automatically-translate-slug'];
            }
        }
        else{
            if( !empty( $settings['automatically-translate-slug'] ) )
                $settings['automatically-translate-slug'] = sanitize_text_field( $settings['automatically-translate-slug'] );
            else
                $settings['automatically-translate-slug'] = 'no';
        }

        return apply_filters( 'trp_machine_translation_sanitize_settings', $settings, $mt_settings );
    }

    /*
    * Automatic Translation
    */
    public function machine_translation_page_content(){
        $trp                       = TRP_Translate_Press::get_trp_instance();

        $machine_translator_logger = $trp->get_component( 'machine_translator_logger' );
        $machine_translator_logger->maybe_reset_counter_date();

        $machine_translator        = $trp->get_component( 'machine_translator' );

        require_once TRP_PLUGIN_DIR . 'partials/machine-translation-settings-page.php';
    }

    /**
    * Test selected API functionality
    */
    public function test_api_page_content(){
        require_once TRP_PLUGIN_DIR . 'partials/test-api-settings-page.php';
    }

    public function load_engines(){
        include_once TRP_PLUGIN_DIR . 'includes/google-translate/functions.php';
        include_once TRP_PLUGIN_DIR . 'includes/google-translate/class-google-translate-v2-machine-translator.php';
    }

    public function get_active_engine( ){
        // This $default is just a fail safe. Should never be used. The real default is set in TRP_Settings->set_options function
        $default = 'TRP_Google_Translate_V2_Machine_Translator';

        if( empty( $this->settings['trp_machine_translation_settings']['translation-engine'] ) )
            $value = $default;
        else {
            $deepl_class_name = class_exists('TRP_IN_Deepl_Machine_Translator' ) ? 'TRP_IN_Deepl_Machine_Translator' : 'TRP_Deepl_Machine_Translator';
            $existing_engines = apply_filters('trp_automatic_translation_engines_classes', array(
                'google_translate_v2' => 'TRP_Google_Translate_V2_Machine_Translator',
                'deepl'               => $deepl_class_name
            ));

            $value = ( isset( $existing_engines[$this->settings['trp_machine_translation_settings']['translation-engine']] ) ) ? $existing_engines[$this->settings['trp_machine_translation_settings']['translation-engine']] : '';

            if( !class_exists( $value ) ) {
                $value = $default; //something is wrong if it reaches this
            }
        }

        return new $value( $this->settings );
    }

    public function add_upsell_filter(){
        if( !class_exists( 'TRP_DeepL' ) && !class_exists( 'TRP_IN_DeepL' ) )
            add_filter( 'trp_machine_translation_engines', [ $this, 'translation_engines_upsell' ], 20 );
    }

    public function translation_engines_upsell( $engines ){
        $engines[] = array( 'value' => 'deepl_upsell', 'label' => __( 'DeepL', 'translatepress-multilingual' ) );

        return $engines;
    }


    public function add_enable_auto_translate_slug_filter( $allow ){
        if( !empty( $this->settings['trp_machine_translation_settings']['machine-translation'] ) &&
            $this->settings['trp_machine_translation_settings']['machine-translation'] == 'yes' &&
            isset( $this->settings['trp_machine_translation_settings']['automatically-translate-slug'] ) &&
            $this->settings['trp_machine_translation_settings']['automatically-translate-slug'] == 'yes'
        ){
            $allow = true;
        }
        return $allow;
    }

    public function display_unsupported_languages(){
        $trp = TRP_Translate_Press::get_trp_instance();
        $machine_translator = $trp->get_component( 'machine_translator' );
        $trp_languages = $trp->get_component( 'languages' );

        $correct_key = $machine_translator->is_correct_api_key();
        $display_recheck_button = false;


        if ( 'yes' === $this->settings['trp_machine_translation_settings']['machine-translation'] &&
            !empty( $machine_translator->get_api_key() ) &&
            !$machine_translator->check_languages_availability($this->settings['translation-languages']) &&
            $correct_key != null
        ){
            $display_recheck_button = true;
            $language_names = $trp_languages->get_language_names( $this->settings['translation-languages'], 'english_name' );

            ?>
            <tr id="trp_unsupported_languages">
                <th scope=row><?php esc_html_e( 'Unsupported languages', 'translatepress-multilingual' ); ?></th>
                <td>
                    <ul class="trp-unsupported-languages">
                        <?php
                        foreach ( $this->settings['translation-languages'] as $language_code ) {
                            if ( !$machine_translator->check_languages_availability( array( $language_code ) ) ) {
                                echo '<li>' . esc_html( $language_names[$language_code] ) . '</li>';
                            }
                        }
                        ?>
                   </ul>
                  <p class="description">
                       <?php echo wp_kses( __( 'The selected automatic translation engine does not provide support for these languages.<br>You can still manually translate pages in these languages using the Translation Editor.', 'translatepress-multilingual' ), array( 'br' => array() ) ); ?>
                   </p>
                </td>
            </tr>

            <?php
        }

        $data = get_option('trp_db_stored_data', array() );
        if (isset($data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'])){
            $languages_that_support_formality = $data['trp_mt_supported_languages'][$this->settings['trp_machine_translation_settings']['translation-engine']]['formality-supported-languages'];
            $show_formality = false;
            foreach ($languages_that_support_formality as $value){
                if($value == "false"){
                    $show_formality = true;
                    break;
                }
            }
            if ( 'yes' === $this->settings['trp_machine_translation_settings']['machine-translation'] &&
                !empty( $machine_translator->get_api_key() ) &&
                $show_formality &&
                $correct_key != null
            ){
                $display_recheck_button = true;
                $language_names = $trp_languages->get_language_names( $this->settings['translation-languages'], 'english_name' );
                ?>
                <tr id="trp_unsupported_languages">
                    <th scope=row><?php esc_html_e( 'Languages without formality', 'translatepress-multilingual' ); ?></th>
                <td>
                    <ul class="trp-unsupported-languages">
                        <?php
                        foreach ( $this->settings['translation-languages'] as $language_code ) {
                            if ( isset($languages_that_support_formality[$language_code]) && $languages_that_support_formality[$language_code] == "false" || !array_key_exists( $language_code, $languages_that_support_formality ) ) {
                                echo '<li>' . esc_html( $language_names[$language_code] ) . '</li>';
                            }
                        }
                        ?>
                    </ul>
                    <p class="description">
                        <?php echo wp_kses( sprintf(__( 'The selected automatic translation engine provides only <a href="%s" target="_blank">default formality</a> settings for these languages for now.<br>Automatic translation will still work if available for these languages. It will just not use the formality setting from TranslatePress <a href="%s" target="_self"> General Tab</a> for the languages listed above.', 'translatepress-multilingual' ), esc_url('https://www.deepl.com/docs-api/translating-text/'), esc_url(admin_url('options-general.php?page=translate-press'))), array('a' => array('href' => array(), 'target' =>array(), 'title' => array()), 'br' => array()) ); ?>
                    </p>
                </td>
                </tr>
                <?php
            }
        }
        if ( 'yes' === $this->settings['trp_machine_translation_settings']['machine-translation'] && $display_recheck_button ){
            ?>

            <tr id="trp_recheck_supported_languages">
                <th scope=row></th>
                <td>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=trp_machine_translation&trp_recheck_supported_languages=1&trp_recheck_supported_languages_nonce=' . wp_create_nonce('trp_recheck_supported_languages') ) ); ?>" class="button-secondary"><?php esc_html_e( 'Recheck supported languages', 'translatepress-multilingual' ); ?></a>
                    <p><i><?php echo wp_kses_post( sprintf( __( '(last checked on %s)', 'translatepress-multilingual' ), esc_html( $machine_translator->get_last_checked_supported_languages() ) ) ); ?> </i></p>
                </td>
            </tr>
            <?php
        }
    }
}
