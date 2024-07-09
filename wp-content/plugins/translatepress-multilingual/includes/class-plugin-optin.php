<?php

class TRP_Plugin_Optin {

    public static $user_name = '';
    public static $base_url = 'https://translatepress.com/wp-json/trp-api/';

    public function __construct(){

        if ( !wp_next_scheduled( 'trp_plugin_optin_sync' ) )
            wp_schedule_event( time(), 'weekly', 'trp_plugin_optin_sync' );

        add_action( 'trp_plugin_optin_sync', array( 'TRP_Plugin_Optin', 'sync_data' ) );

    }

    public function redirect_to_plugin_optin_page(){

        if( !isset( $_GET['page'] ) )
            return;

        $optin = get_option( 'trp_plugin_optin', false );

        if( $optin !== false )
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

            if( !empty( $trp_settings ) ){
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

            $request = wp_remote_post( self::$base_url . 'pluginOptinSubscribe/', $args );

            update_option( 'trp_plugin_optin', 'yes' );
            update_option( 'trp_plugin_optin_email', get_option( 'admin_email' ) );

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

            update_option( 'trp_plugin_optin', 'no' );

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

        $optin = get_option( 'trp_plugin_optin', false );

        if( $optin !== 'yes' )
            return;

        $optin_email = get_option( 'trp_plugin_optin_email', false );

        if( $optin_email === false )
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
                'email'   => $optin_email,
                'version' => $version,
            ],
        );

        $request = wp_remote_post( self::$base_url . 'pluginOptinUpdateVersion/', $args );

    }

    // Update tags when a paid version is deactivated
    public function process_paid_plugin_deactivation( $plugin ){

        $optin = get_option( 'trp_plugin_optin', false );

        if( $optin !== 'yes' )
            return;

        $optin_email = get_option( 'trp_plugin_optin_email', false );

        if( $optin_email === false )
            return;

        $target_plugins = [ 'translatepress-personal/index.php', 'translatepress-developer/index.php', 'translatepress-business/index.php' ];

        if( !in_array( $plugin, $target_plugins ) )
            return;

        // Update user version tag
        $args = array(
            'method' => 'POST',
            'body'   => [
                'email'   => $optin_email,
                'version' => 'free',
            ],
        );

        $request = wp_remote_post( self::$base_url . 'pluginOptinUpdateVersion/', $args );

    }

    // Advanced settings
    public function setup_plugin_optin_advanced_setting( $settings_array ){

        $settings_array[] = array(
            'name'          => 'plugin_optin_setting',
            'type'          => 'checkbox',
            'label'         => esc_html__( 'Marketing optin', 'translatepress-multilingual' ),
            'description'   => esc_html__( 'Opt in to our security and feature updates notifications, and non-sensitive diagnostic tracking.', 'translatepress-multilingual' ),
            'id'            => 'miscellaneous_options',
            );

        return $settings_array;

    }

    public function process_plugin_optin_advanced_setting( $settings, $submitted_settings, $previous_settings ){

        if( !isset( $settings['plugin_optin_setting'] ) || $settings['plugin_optin_setting'] == 'no' ){

            update_option( 'trp_plugin_optin', 'no' );

            $optin_email = get_option( 'trp_plugin_optin_email', false );

            if( $optin_email === false )
                return $settings;

            $args = array(
                'method' => 'POST',
                'body'   => [
                    'email'    => $optin_email,
                ],
            );

            $request = wp_remote_post( self::$base_url . 'pluginOptinArchiveSubscriber/', $args );
            
        } else if ( $settings['plugin_optin_setting'] == 'yes' && ( !isset( $previous_settings['plugin_optin_setting'] ) || $settings['plugin_optin_setting'] != $previous_settings['plugin_optin_setting'] ) ){
            
            update_option( 'trp_plugin_optin', 'yes' );
            update_option( 'trp_plugin_optin_email', get_option( 'admin_email' ) );

            $optin_email = get_option( 'trp_plugin_optin_email', false );

            if( $optin_email === false )
                return;

            $args = array(
                'method' => 'POST',
                'body'   => [
                    'email'   => $optin_email,
                    'name'    => self::get_user_name(),
                    'version' => self::get_current_active_version(),
                ],
            );

            $request = wp_remote_post( self::$base_url . 'pluginOptinSubscribe/', $args );

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

        $plugin_optin = get_option( 'trp_plugin_optin' );

        if( $plugin_optin != 'yes' )
            return;

        $trp_settings = get_option( 'trp_settings', 'not_set' );

        $args = array(
            'method' => 'POST',
            'body'   => array(
                'home_url'              => home_url(),
                'email'                 => get_option( 'admin_email' ),
                'name'                  => self::get_user_name(),
                'version'               => self::get_current_active_version(),
                'license'               => get_option('trp_license_key'),
                'default_language'      => !empty( $trp_settings['default-language'] ) ? $trp_settings['default-language'] : '',
                'translation_languages' => !empty( $trp_settings['translation-languages'] ) ? implode( ',', $trp_settings['translation-languages'] ) : '',
                'active_plugins'        => json_encode( get_option( 'active_plugins', array() ) ),
            ),
        );

        $request = wp_remote_post( self::$base_url . 'pluginOptinSync/', $args );

    }
}
