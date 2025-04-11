<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_Install_Plugins {
    public function get_plugin_slugs() {
        $slugs = array(
            'pb'  => array(
                'all_slugs'    => array(
                    'profile-builder/index.php', 'profile-builder-hobbyist/index.php', 'profile-builder-pro/index.php'
                ),
                'install_slug'   => 'profile-builder/index.php',
                'plugin_zip' => 'https://downloads.wordpress.org/plugin/profile-builder.zip'
            ),
            'pms' => array(
                'all_slugs'    => array(
                    'paid-member-subscriptions/index.php'
                ),
                'install_slug'   => 'paid-member-subscriptions/index.php',
                'plugin_zip' => 'https://downloads.wordpress.org/plugin/paid-member-subscriptions.zip'
            ),
            'wha' => array(
                'all_slugs'    => array(
                    'wp-webhooks/wp-webhooks.php'
                ),
                'install_slug' => 'wp-webhooks/wp-webhooks.php',
                'plugin_zip'   => 'https://downloads.wordpress.org/plugin/wp-webhooks.3.3.1.zip'
            )
        );

        return apply_filters( 'trp_plugin_install_slugs', $slugs );
    }

    public function install_plugins_request(){
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            check_ajax_referer( 'trp_install_plugins', 'security' );
            if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_install_plugins' && !empty( $_POST['plugin_slug'] ) ) {
                $plugin_slug = sanitize_text_field($_POST['plugin_slug']);
                $short_slugs = $this->get_plugin_slugs();
                if ( isset( $short_slugs[$plugin_slug]) ){
                    if ( $this->install_upgrade_activate($plugin_slug) ){
                        $message = esc_html__('Active', 'translatepress-multilingual');
                    }else{
                        $message = wp_kses( sprintf( __('Could not install. Try again from <a href="%s" >Plugins Dashboard.</a>', 'translatepress-multilingual'), admin_url('plugins.php') ), array('a' => array( 'href' => array() ) ) );
                    }
                    wp_die( trp_safe_json_encode( $message ));//phpcs:ignore
                }
            }
        }
        wp_die();
    }

    public function install_upgrade_activate( $short_slug ) {
        $short_slugs = $this->get_plugin_slugs();
        $install_slug = $short_slugs[ $short_slug ]['install_slug'];
        $plugin_zip = $short_slugs[ $short_slug ]['plugin_zip'];

        if ( $this->is_plugin_installed( $short_slug ) ) {
            $this->upgrade_plugin( $install_slug );
            $installed = true;
        } else {
            $installed = $this->install_plugin( $plugin_zip );
        }

        if ( !is_wp_error( $installed ) && $installed ) {
            $activate = activate_plugin( $install_slug );

            if ( is_null( $activate ) ) {
                return true;
            }
        }

        return false;
    }

    public function is_plugin_installed( $short_slug ) {
        $short_slugs = $this->get_plugin_slugs();
        $all_slugs = $short_slugs[ $short_slug ]['all_slugs'];

        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        foreach( $all_slugs as $slug ) {
            if ( !empty( $all_plugins[ $slug ] ) ) {
                return true;
            }
        }

        return false;
    }

    public function is_plugin_active($short_slug){

        $short_slugs = $this->get_plugin_slugs();
        $all_slugs = $short_slugs[ $short_slug ]['all_slugs'];

        foreach( $all_slugs as $slug ) {
            if ( is_plugin_active( $slug ) ) {
                return true;
            }
        }

        return false;
    }

    public function install_plugin( $plugin_zip ) {
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        wp_cache_flush();
        $upgrader  = new Plugin_Upgrader();

        // do not output any messages
        $upgrader->skin = new Automatic_Upgrader_Skin();

        $installed = $upgrader->install( $plugin_zip );
        return $installed;
    }

    public function upgrade_plugin( $plugin_slug ) {
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        wp_cache_flush();
        $upgrader = new Plugin_Upgrader();

        // do not output any messages
        $upgrader->skin = new Automatic_Upgrader_Skin();

        $upgraded = $upgrader->upgrade( $plugin_slug );
        return $upgraded;
    }
}

if( !function_exists( 'wppb_activate_plugin_redirect' ) ){
    function wppb_activate_plugin_redirect(){
        // do nothing, just override pb function in order to not redirect on activation
    }
}
