<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_Plugin_Optin {

    public static $user_name = '';
    public static $api_url   = 'https://translatepress.com/wp-json/trp-api/';
    public static $stats_url = 'https://usagetracker.cozmoslabs.com/update';
    public static $plugin_optin_status = '';
    public static $plugin_optin_email  = '';

    public static $plugin_option_key       = 'trp_plugin_optin';
    public static $plugin_option_email_key = 'trp_plugin_optin_email';

    public function __construct(){

        if ( !wp_next_scheduled( 'trp_plugin_optin_sync' ) )
            wp_schedule_event( time(), 'weekly', 'trp_plugin_optin_sync' );

        add_action( 'trp_plugin_optin_sync', array( 'TRP_Plugin_Optin', 'sync_data' ) );
        
        self::$plugin_optin_status = get_option( self::$plugin_option_key, false );
        self::$plugin_optin_email  = get_option( self::$plugin_option_email_key, false );

    }

    public function redirect_to_plugin_optin_page(){

        if( !isset( $_GET['page'] ) )
            return;

        if( self::$plugin_optin_status !== false )
            return;
        
        // Default/in-plugin tabs will be hardcoded, but anything that is added through hooks will be automatically filled
        $trp_settings_pages = apply_filters( 'trp_settings_tabs', array() );

        if( !empty( $trp_settings_pages ) ){
            $pages = array();

            foreach( $trp_settings_pages as $page ) {
                $pages[] = $page['page'];
            }

            $trp_settings_pages = $pages;
        }

        $trp_settings_pages[] = 'translate-press';
        $trp_settings_pages[] = 'trp_addons_page';
        $trp_settings_pages[] = 'trp_license_key';

        if( !in_array( $_GET['page'], $trp_settings_pages ) )
            return;

        wp_safe_redirect( admin_url( 'admin.php?page=trp_optin_page' ) );
        exit();

    }

    public function add_submenu_page_optin() {
        add_submenu_page( 'TRPHidden', 'TranslatePress Optin', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_optin_page', array(
            $this,
            'optin_page_content'
        ) );
	}

    public function optin_page_content(){
        require_once TRP_PLUGIN_DIR . 'partials/plugin-optin-page.php';
    }

    public function process_optin_actions(){

        if( !isset( $_GET['page'] ) || $_GET['page'] != 'trp_optin_page' || !isset( $_GET['_wpnonce'] ) )
            return;

        if( wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'trp_enable_plugin_optin' ) ){

            $args = array(
                'method' => 'POST',
                'body'   => array(
                    'email'   => get_option( 'admin_email' ),
                    'name'    => self::get_user_name(),
                    'version' => self::get_current_active_version(),
                ),
            );

            $trp_settings = get_option( 'trp_settings', false );

            if( !empty( $trp_settings ) && count( $trp_settings['translation-languages'] ) > 1 ){
                    
                $multiple_languages = isset( $trp_settings['translation-languages'] ) && ( $trp_settings['translation-languages'] ) > 1 ? true : false;
                
                // also check if custom translation tables are present
                $translation_tables = false;

                global $wpdb;
                $dictionary_table_name = $wpdb->prefix . 'trp_dictionary_' . strtolower( $trp_settings['default-language'] ) . '_'. strtolower( $trp_settings['translation-languages'][1] );

                if( $wpdb->get_var( "SHOW TABLES LIKE '$dictionary_table_name'" ) == $dictionary_table_name || (int)$wpdb->get_var( "SELECT COUNT(id) FROM $dictionary_table_name WHERE translated != ''" ) > 25 )
                    $translation_tables = true;

                if( $multiple_languages && $translation_tables )
                    $args['body']['existingSettings'] = true;
            }

            $request = wp_remote_post( self::$api_url . 'pluginOptinSubscribe/', $args );

            update_option( self::$plugin_option_key, 'yes' );
            update_option( self::$plugin_option_email_key, get_option( 'admin_email' ) );

            $settings = get_option( 'trp_advanced_settings', array() );

            if( empty( $settings ) )
                $settings = array( 'plugin_optin_setting' => 'yes' );
            else
                $settings['plugin_optin_setting'] = 'yes';

            update_option( 'trp_advanced_settings', $settings );

            wp_safe_redirect( admin_url( 'options-general.php?page=translate-press' ) );
            exit;

        }

        if( wp_verify_nonce( sanitize_text_field( $_GET['_wpnonce'] ), 'trp_disable_plugin_optin' ) ){

            update_option( self::$plugin_option_key, 'no' );

            $settings = get_option( 'trp_advanced_settings', array() );

            if( empty( $settings ) )
                $settings = array( 'plugin_optin_setting' => 'no' );
            else
                $settings['plugin_optin_setting'] = 'no';

            update_option( 'trp_advanced_settings', $settings );

            wp_safe_redirect( admin_url( 'options-general.php?page=translate-press' ) );
            exit;

        }

    }

    // Update tags when a paid version is activated
    public function process_paid_plugin_activation( $plugin ){

        if( self::$plugin_optin_status !== 'yes' || self::$plugin_optin_email === false )
            return;

        $target_plugins = [ 'translatepress-personal/index.php', 'translatepress-developer/index.php', 'translatepress-business/index.php' ];

        if( !in_array( $plugin, $target_plugins ) )
            return;

        $version = explode( '/', $plugin );
        $version = str_replace( 'translatepress-', '', $version[0] );

        // Update user version tag
        $args = array(
            'method' => 'POST',
            'body'   => [
                'email'   => self::$plugin_optin_email,
                'version' => $version,
            ],
        );

        $request = wp_remote_post( self::$api_url . 'pluginOptinUpdateVersion/', $args );

    }

    // Update tags when a paid version is deactivated
    public function process_paid_plugin_deactivation( $plugin ){

        if( self::$plugin_optin_status !== 'yes' || self::$plugin_optin_email === false )
            return;

        $target_plugins = [ 'translatepress-personal/index.php', 'translatepress-developer/index.php', 'translatepress-business/index.php' ];

        if( !in_array( $plugin, $target_plugins ) )
            return;

        // Update user version tag
        $args = array(
            'method' => 'POST',
            'body'   => [
                'email'   => self::$plugin_optin_email,
                'version' => 'free',
            ],
        );

        $request = wp_remote_post( self::$api_url . 'pluginOptinUpdateVersion/', $args );

    }

    // Advanced settings
    public function setup_plugin_optin_advanced_setting( $settings_array ){

        $settings_array[] = array(
            'name'          => 'plugin_optin_setting',
            'type'          => 'checkbox',
            'label'         => esc_html__( 'Marketing optin', 'translatepress-multilingual' ),
            'description'   => esc_html__( 'Opt in to our security and feature updates notifications, and non-sensitive diagnostic tracking.', 'translatepress-multilingual' ),
            'id'            => 'miscellaneous_options',
            'container'     => 'miscellaneous_options'
        );

        return $settings_array;

    }

    public function process_plugin_optin_advanced_setting( $settings, $submitted_settings, $previous_settings ){

        if( !isset( $settings['plugin_optin_setting'] ) || $settings['plugin_optin_setting'] == 'no' ){

            update_option( self::$plugin_option_key, 'no' );

            if( self::$plugin_optin_email === false )
                return $settings;

            $args = array(
                'method' => 'POST',
                'body'   => [
                    'email'    => self::$plugin_optin_email,
                ],
            );

            $request = wp_remote_post( self::$api_url . 'pluginOptinArchiveSubscriber/', $args );
            
        } else if ( $settings['plugin_optin_setting'] == 'yes' ){

            if( isset( $previous_settings['plugin_optin_setting'] ) && $settings['plugin_optin_setting'] == $previous_settings['plugin_optin_setting'] ){

                // if the user has not changed the setting, we don't need to send the data again but if the option is not set, we need to send the data
                if( self::$plugin_optin_status == 'yes' )
                    return $settings;

            }
            
            update_option( self::$plugin_option_key, 'yes' );
            update_option( self::$plugin_option_email_key, get_option( 'admin_email' ) );

            if( self::$plugin_optin_email === false )
                return $settings;

            $args = array(
                'method' => 'POST',
                'body'   => [
                    'email'   => self::$plugin_optin_email,
                    'name'    => self::get_user_name(),
                    'version' => self::get_current_active_version(),
                ],
            );

            $request = wp_remote_post( self::$api_url . 'pluginOptinSubscribe/', $args );

        }

        return $settings;

    }

    // Determine current user name
    public static function get_user_name(){

        if( !empty( self::$user_name ) )
            return self::$user_name;

        $user = wp_get_current_user();

        $name = $user->display_name;

        $first_name = get_user_meta( $user->ID, 'first_name', true );
        $last_name  = get_user_meta( $user->ID, 'last_name', true );

        if( !empty( $first_name ) && !empty( $last_name ) )
            $name = $first_name . ' ' . $last_name;

        self::$user_name = $name;

        return self::$user_name;

    }

    // Determine current active plugin version
    public static function get_current_active_version(){

        if( !function_exists( 'is_plugin_active' ) )
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        if( is_plugin_active( 'translatepress-developer/index.php' ) )
            return 'developer';
        elseif( is_plugin_active( 'translatepress-business/index.php' ) )
            return 'business';
        elseif( is_plugin_active( 'translatepress-personal/index.php' ) )
            return 'personal';

        return 'free';

    }

    public static function sync_data(){

        if( self::$plugin_optin_status !== 'yes' )
            return;

        $trp_settings = get_option( 'trp_settings', 'not_set' );

        $args = array(
            'method' => 'POST',
            'body'   => array(
                'home_url'              => home_url(),
                'product'               => 'trp',
                'email'                 => self::$plugin_optin_email,
                'name'                  => self::get_user_name(),
                'version'               => self::get_current_active_version(),
                'license'               => get_option('trp_license_key'),
                'active_plugins'        => json_encode( get_option( 'active_plugins', array() ) ),
                'wp_version'            => get_bloginfo('version'),
                'wp_locale'             => get_locale(),
                'plugin_version'        => defined( 'TRP_PLUGIN_VERSION' ) ? TRP_PLUGIN_VERSION : '',
                'php_version'           => defined( 'PHP_VERSION' ) ? PHP_VERSION : '',
            ),
        );

        // Only send the major version for WordPress and PHP
        // e.g. 1.x
        $target_keys = array( 'wp_version', 'php_version' );

        foreach( $target_keys as $key ){
            $version_number = explode( '.', $args['body'][$key] );

            if( isset( $version_number[0] ) && isset( $version_number[1] ) )
                $args['body'][$key] = $version_number[0] . '.' . $version_number[1];
        }

        $args = apply_filters( 'cozmoslabs_plugin_optin_trp_metadata', $args );

        $request = wp_remote_post( self::$stats_url, $args );

        // echo wp_remote_retrieve_body( $request );
        // die();

    }
}

if( !class_exists( 'Cozmoslabs_Plugin_Optin_Metadata_Builder' ) ) {
    /**
     * Version 1.0.0
     */
    class Cozmoslabs_Plugin_Optin_Metadata_Builder {

        public $option_prefix               = '';
        public $blacklisted_option_slugs    = [];
        public $blacklisted_option_patterns = [];
        public $blacklisted_option_names    = [];
        protected $metadata;

        public function __construct(){

            $this->metadata = [
                'settings' => [],
                'add-ons'  => [],
                'custom'   => [],
                'cpt'      => [],
            ];

            add_filter( 'cozmoslabs_plugin_optin_'. $this->option_prefix .'metadata', array( $this, 'build_metadata' ) );

        }

        public function build_metadata( $args ){
            // Get all options that start with the prefix
            $options = $this->get_option_keys();

            if( !empty( $options ) ){

                foreach( $options as $option ){

                    // exclude exact option names
                    if( in_array( $option['option_name'], $this->blacklisted_option_slugs ) ){
                        continue;
                    }

                    // exclude patterns
                    if( !empty( $this->blacklisted_option_patterns ) ){
                        $found_pattern = false;

                        foreach( $this->blacklisted_option_patterns as $pattern ){
                            if( strpos( $option['option_name'], $pattern ) !== false ){
                                $found_pattern = true;
                                break;
                            }
                        }

                        if( $found_pattern )
                            continue;
                    }

                    $option_value = get_option( $option['option_name'], false );

                    if( !empty( $option_value ) ){

                        if( is_array( $option_value ) ){
                            foreach( $option_value as $key => $value ){
                                if( !is_array( $value ) ){
                                    if( in_array( $key, $this->blacklisted_option_names ) )
                                    unset( $option_value[ $key ] );
                                } else {
                                    if( in_array( $key, $this->blacklisted_option_names ) )
                                        unset( $option_value[ $key ] );

                                    foreach( $value as $key_deep => $value_deep ){
                                        if( in_array( $key_deep, $this->blacklisted_option_names ) )
                                            unset( $option_value[ $key ][ $key_deep ] );
                                    }
                                }
                            }
                        }

                        // cleanup options like array( array( 'abc' ) ) to be array( 'abc' ) 
                        if( is_array( $option_value ) && count( $option_value ) == 1 && isset( $option_value[0] ) )
                            $option_value = $option_value[0];
                        
                        $this->metadata['settings'][ $option['option_name'] ] = $option_value;
                    }

                }

            }

            // Ability to add custom data
            $this->metadata = apply_filters( 'cozmoslabs_plugin_optin_'. $this->option_prefix .'metadata_builder_metadata', $this->metadata );

            $args['body']['metadata'] = $this->metadata;

            return $args;
        }

        private function get_option_keys(){

            global $wpdb;

            if( empty( $this->option_prefix ) )
                return [];
            
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT option_name FROM {$wpdb->prefix}options WHERE option_name LIKE %s", $this->option_prefix . '%' ), 'ARRAY_A' );

            if( !empty( $result ) )
                return $result;
        
            return [];

        }

    }
}

class Cozmoslabs_Plugin_Optin_Metadata_Builder_TRP extends Cozmoslabs_Plugin_Optin_Metadata_Builder {

    public function __construct(){
        $this->option_prefix = 'trp_';

        parent::__construct();

        $this->blacklisted_option_slugs = [
            'trp_ald_plugin_version',
            'trp_db_errors',
            'trp_db_stored_data',
            'trp_in_sp_add_gettext_slugs',
            'trp_license_details',
            'trp_license_key',
            'trp_machine_translated_characters',
            'trp_plugin_optin',
            'trp_plugin_optin_email',
            'trp_plugin_version',
            'trp_post_type_base_slug_translation',
            'trp_seopack_version',
            'trp_show_error_db_message',
            'trp_show_notice_about_old_slugs_being_deleted',
            'trp_taxonomy_slug_translation',
            'trp_updated_database_gettext_original_id_cleanup',
            'trp_updated_database_gettext_original_id_insert',
            'trp_updated_database_gettext_original_id_update',
            'trp_were_old_slug_tables_found',
            'trp_add_ons_settings',
        ];

        $this->blacklisted_option_names = [
            'deepl-api-key',
            'google-translate-key',
        ];

        $this->blacklisted_option_patterns = [
            'trp_migrate_old_slug_to_new_parent_and_translate_slug_table',
            'trp_woo_',
        ];

        add_action( 'cozmoslabs_plugin_optin_'. $this->option_prefix .'metadata_builder_metadata', array( $this, 'build_custom_plugin_metadata' ) );
    }

    public function build_custom_plugin_metadata(){

        // add-ons data
        $this->metadata['addons'] = $this->generate_addon_settings();

        $this->metadata['settings'] = $this->process_settings_metadata( $this->metadata['settings'] );

        return $this->metadata;
    }

    public function generate_addon_settings(){
        $add_on_option_slugs = [
            'trp_add_ons_settings',
        ];

        $add_ons = [];

        foreach( $add_on_option_slugs as $option_slug ){
            $option = get_option( $option_slug, false );

            if( !empty( $option ) ){
                foreach( $option as $slug => $value ){
                    if( ( is_bool( $value ) && $value == true ) || $value == 'show' ){
                        $add_on_name = explode( '/', $slug );
                        $add_on_name = str_replace( 'tp-add-on-', '', $add_on_name[0] );                        

                        $add_ons[ $add_on_name ] = true;
                    }
                }
            }
        }

        // Add integrations as active add-ons if they have restrictions
        if( !empty( $this->metadata['content_restriction'] ) ) {
            // Elementor integration
            if( !empty( $this->metadata['content_restriction']['elementor_restrictions'] ) ) {
                $add_ons['elementor-integration'] = true;
            }

            // Gutenberg integration
            if( !empty( $this->metadata['content_restriction']['blocks_restrictions'] ) ) {
                $add_ons['gutenberg-integration'] = true;
            }
        }

        return $add_ons;
    }

    public function process_settings_metadata( $settings ){

        $trp                    = TRP_Translate_Press::get_trp_instance();
        $trp_settings_component = $trp->get_component( 'settings' );
        $trp_settings           = $trp_settings_component->get_settings();

        if( !empty( $settings['trp_settings']['translation-languages'] ) ){
            $settings['trp_settings']['translation-languages'] = implode( ',', $settings['trp_settings']['translation-languages'] );
        }

        if( !empty( $settings['trp_settings']['publish-languages'] ) ){
            $settings['trp_settings']['publish-languages'] = implode( ',', $settings['trp_settings']['publish-languages'] );
        }

        // In addition to Machine Translation being enabled, for the selected translation engine, verify if a license key is set. 
        // If no license key is set, consider machine translation as disabled.
        if( !empty( $settings['trp_machine_translation_settings']['machine-translation'] ) && $settings['trp_machine_translation_settings']['machine-translation'] == 'yes' && !empty( $settings['trp_machine_translation_settings']['translation-engine'] ) ){
            if( $settings['trp_machine_translation_settings']['translation-engine'] == 'mtapi' && empty( $trp_settings['trp_license_key'] ) ){
                $settings['trp_machine_translation_settings']['machine-translation'] = 'no';
            } else if( $settings['trp_machine_translation_settings']['translation-engine'] == 'google_translate_v2' && empty( $trp_settings['trp_machine_translation_settings']['google-translate-key'] ) ){
                $settings['trp_machine_translation_settings']['machine-translation'] = 'no';
            } else if( $settings['trp_machine_translation_settings']['translation-engine'] == 'deepl' && empty( $trp_settings['trp_machine_translation_settings']['deepl-api-key'] ) ){
                $settings['trp_machine_translation_settings']['machine-translation'] = 'no';
            }
        }

        return $settings;

    }
}

new Cozmoslabs_Plugin_Optin_Metadata_Builder_TRP();