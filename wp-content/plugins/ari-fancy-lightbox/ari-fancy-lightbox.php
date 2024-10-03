<?php
/*
	Plugin Name: ARI Fancy Lightbox
	Plugin URI: http://wp-quiz.ari-soft.com/plugins/wordpress-fancy-lightbox.html
	Description: Full-featured lightbox plugin for WordPress.
	Version: 1.3.18
	Author: ARI Soft
	Author URI: http://www.ari-soft.com
	Text Domain: ari-fancy-lightbox
	Domain Path: /languages
	License: GPL2
 */

defined( 'ABSPATH' ) or die( 'Access forbidden!' );

define( 'ARIFANCYLIGHTBOX_EXEC_FILE', __FILE__ );
define( 'ARIFANCYLIGHTBOX_URL', plugin_dir_url( __FILE__ ) );
define( 'ARIFANCYLIGHTBOX_PATH', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'ARI_WP_LEGACY' ) ) {
    $current_wp_version = get_bloginfo( 'version' );
    define( 'ARI_WP_LEGACY', version_compare( $current_wp_version, '4.0', '<' ) );
}

if ( ! function_exists( 'ari_fancy_lightbox_activation_check' ) ) {
    function ari_fancy_lightbox_activation_check() {
        $min_php_version = '5.4.0';
        $min_wp_version = '3.4';

        $current_wp_version = get_bloginfo( 'version' );
        $current_php_version = PHP_VERSION;

        $is_supported_php_version = version_compare( $current_php_version, $min_php_version, '>=' );
        $is_spl_installed = function_exists( 'spl_autoload_register' );
        $is_supported_wp_version = version_compare( $current_wp_version, $min_wp_version, '>=' );

        if ( ! $is_supported_php_version || ! $is_spl_installed || ! $is_supported_wp_version ) {
            deactivate_plugins( basename( ARIFANCYLIGHTBOX_EXEC_FILE ) );

            $recommendations = array();

            if ( ! $is_supported_php_version )
                $recommendations[] = sprintf(
                    /* translators: %1$s: current PHP version, %2$s: min PHP version */
                    __( 'update PHP version on your server from v. %1$s to at least v. %2$s', 'ari-fancy-lightbox' ),
                    $current_php_version,
                    $min_php_version
                );

            if ( ! $is_spl_installed )
                $recommendations[] = __( 'install PHP SPL extension', 'ari-fancy-lightbox' );

            if ( ! $is_supported_wp_version )
                $recommendations[] = sprintf(
                    /* translators: %1$s: current WP version, %2$s: min WP version */
                    __( 'update WordPress v. %1$s to at least v. %2$s', 'ari-fancy-lightbox' ),
                    $current_wp_version,
                    $min_wp_version
                );
				
            wp_die(
                esc_html(
                    sprintf(
                        /* translators: %1s: recommendations, %2$s: dashboard link */ 
                        _x(
                            '"ARI Fancy Lightbox" can not be activated. It requires PHP version 5.4.0+ with SPL extension and WordPress 4.0+.<br /><br /><b>Recommendations:</b> %1$s.<br /><br /><a href="%2$s" class="button button-primary">Back</a>',
                            '%1$s = recommendations, %2$s = dashboard link',
                            'ari-fancy-lightbox'
                        ),
                        join( ', ', $recommendations ),
                        get_dashboard_url( get_current_user_id() )
                    )
                )
            );
        } else {
            add_option( 'ari_fancy_lightbox_redirect', true );

            ari_fancy_lightbox_init();
        }
    }
}

if ( version_compare( PHP_VERSION, '5.4.0', '>=' ) ) {
    require_once ARIFANCYLIGHTBOX_PATH . 'loader.php';

    add_action( 'plugins_loaded', 'ari_fancy_lightbox_init' );
} else {
    if ( ! function_exists( 'ari_fancy_lightbox_requirement_notice' ) ) {
        function ari_fancy_lightbox_requirement_notice() {
            printf(
                '<div class="notice notice-error"><p>%s</p></div>',
                esc_html(
                    sprintf(
                        /* translators: %1s: installed PHP version */ 
                        _x(
                            '"ARI Fancy Lightbox" requires PHP v. 5.4.0+, but PHP version %1$s is used on the site.',
                            '%1$s = installed PHP version',
                            'ari-fancy-lightbox'
                        ),
                        PHP_VERSION
                    )
                )
            );
        }
    }

    add_action( 'admin_notices', 'ari_fancy_lightbox_requirement_notice' );
}

register_activation_hook( ARIFANCYLIGHTBOX_EXEC_FILE, 'ari_fancy_lightbox_activation_check' );
