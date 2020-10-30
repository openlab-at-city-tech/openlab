<?php
namespace Ari_Fancy_Lightbox;

use Ari\App\Plugin as Ari_Plugin;
use Ari\Utils\Request as Request;
use Ari\Utils\Response as Response;
use Ari_Fancy_Lightbox\Helpers\Settings as Settings;
use Ari_Fancy_Lightbox\Helpers\Screen as Screen;
use Ari_Fancy_Lightbox\Loader as Loader;
use Ari\Wordpress\Nextgen as Nextgen_Helper;

class Plugin extends Ari_Plugin {
    public function init() {
        $this->load_translations();

        if ( Nextgen_Helper::is_installed_v2() ) {
            Nextgen_Helper::install_lightbox_v2(
                'arifancybox',
                __( 'ARI Fancy Lightbox', 'ari-fancy-lightbox' ),
                'class="ari-fancybox" data-fancybox-group="%GALLERY_NAME%"'
            );
        }

        if ( is_admin() ) {
            $this->special_handlers();

            add_action( 'admin_enqueue_scripts', function() { $this->admin_enqueue_scripts(); } );
            add_action( 'admin_menu', function() { $this->admin_menu(); } );
            add_action( 'admin_init', function() { $this->admin_init(); } );

            add_filter( 'plugin_action_links_' . plugin_basename( ARIFANCYLIGHTBOX_EXEC_FILE ) , function( $links ) { return $this->plugin_action_links( $links ); } );
        } else {
            add_action( 'init', function() { $this->client_init(); } );
            add_action( 'wp_enqueue_scripts', function() { $this->enqueue_scripts(); } );

            $loader = new Loader();
            $loader->run();
        }

        do_action( 'ari-fancybox-loaded' );

        parent::init();
    }

    private function client_init() {
        $this->foogallery_support();
    }

    private function special_handlers() {
        $settings_tab_key = 'afl_settings_tabs_state';

        add_filter( 'pre_option_ari_fancy_lightbox_settings', function( $value ) use ( $settings_tab_key ) {
            if ( Request::exists( $settings_tab_key ) ) {
                $active_tab = Request::get_var( $settings_tab_key );

                set_transient( $settings_tab_key, $active_tab, 1000 );
            }

            return $value;
        });
    }

    private function load_translations() {
        load_plugin_textdomain( 'ari-fancy-lightbox', false, ARIFANCYLIGHTBOX_SLUG . '/languages' );
    }

    private function admin_menu() {
        $pages = array();
        $settings_cap = 'manage_options';

        $pages[] = add_menu_page(
            __( 'ARI Fancy Lightbox', 'ari-fancy-lightbox' ),
            __( 'ARI Fancy Lightbox', 'ari-fancy-lightbox' ),
            $settings_cap,
            'ari-fancy-lightbox',
            array( $this, 'display_settings' ),
            ! ARI_WP_LEGACY ? 'dashicons-format-image' : ''
        );

        foreach ( $pages as $page ) {
            add_action( 'load-' . $page, function() {
                Screen::register();
            });
        }
    }

	private function admin_enqueue_scripts() {
		$options = $this->options;

        wp_register_script( 'ari-fancy-lightbox-app', $options->assets_url . 'common/app.js', array( 'jquery' ), $options->version );
        wp_register_script( 'ari-fancy-lightbox-app-helper', $options->assets_url . 'common/helper.js', array( 'ari-fancy-lightbox-app' ), $options->version );
        wp_register_style( 'ari-fancy-lightbox-app', $options->assets_url . 'common/css/style.css', array(), $options->version );

        wp_register_style( 'ari-qtip', $options->assets_url . 'qtip/css/jquery.qtip.min.css', array(), $options->version );
        wp_register_script( 'ari-qtip', $options->assets_url . 'qtip/js/jquery.qtip.min.js', array( 'jquery' ), $options->version );

        $form_dependencies = array( 'jquery', 'jquery-ui-slider', 'ari-qtip' );
        if ( wp_script_is( 'jquery-ui-spinner', 'registered' ) )
            $form_dependencies[] = 'jquery-ui-spinner';

        wp_register_script( 'ari-form-elements', $options->assets_url . 'common/form-elements.js', $form_dependencies, $options->version );
        wp_register_script( 'ari-wp-tabs', $options->assets_url . 'common/tabs.js', array( 'jquery' ), $options->version );

        $this->common_enqueue_scripts();
    }

    private function enqueue_scripts() {
        $this->common_enqueue_scripts();
    }

    private function common_enqueue_scripts() {
        $options = $this->options;

        wp_register_script( 'ari-fancybox', $options->assets_url . 'fancybox/jquery.fancybox.min.js', array( 'jquery' ), $options->version );
        wp_register_style( 'ari-fancybox', $options->assets_url . 'fancybox/jquery.fancybox.min.css', array(), $options->version );
    }

    private function admin_init() {
        Settings::instance()->init();
        if ( get_option( 'ari_fancy_lightbox_redirect', false ) ) {
            delete_option( 'ari_fancy_lightbox_redirect' );
            if ( ! isset( $_GET['activate-multi'] ) ) {
                Response::redirect( admin_url( 'admin.php?page=ari-fancy-lightbox' ) );
            }
        }

        $no_header = (bool) Request::get_var( 'noheader' );

        if ( ! $no_header ) {
            $page = Request::get_var( 'page' );

            if ( 0 === strpos( $page, 'ari-fancy-lightbox' ) ) {
                ob_start();

                add_action( 'admin_page_' . $page , function() {
                    ob_end_flush();
                }, 99 );
            }
        }

        $this->foogallery_support();
    }

    protected function need_to_update() {
        $installed_version = get_option( ARIFANCYLIGHTBOX_VERSION_OPTION );

        return ( $installed_version != $this->options->version );
    }

    protected function install() {
        $installer = new \Ari_Fancy_Lightbox\Installer();

        return $installer->run();
    }

    private function plugin_action_links( $links ) {
        $settings_link = '<a href="admin.php?page=ari-fancy-lightbox">' . __( 'Settings', 'ari-fancy-lightbox' ) . '</a>';
        $support_link = '<a href="http://www.ari-soft.com/ARI-Fancy-Lightbox/" target="_blank">' . __( 'Support', 'ari-fancy-lightbox' ) . '</a>';
        $upgrade_link = '<a href="http://wp-quiz.ari-soft.com/plugins/wordpress-fancy-lightbox.html#pricing" target="_blank"><b>' . __( 'Upgrade', 'ari-fancy-lightbox' ) . '</b></a>';

        $links[] = $settings_link;
        $links[] = $support_link;
        $links[] = $upgrade_link;

        return $links;
    }

    private function foogallery_support() {
        if ( ! defined( 'FOOGALLERY_VERSION' ) )
            return ;

        add_filter( 'foogallery_gallery_template_field_lightboxes', function( $lightboxes ) {
            $lightboxes['arifancylightbox'] = __( 'ARI Fancy Lightbox', 'ari-fancy-lightbox' );

            return $lightboxes;
        }, 99 );

        add_filter( 'foogallery_attachment_html_link_attributes', function( $attr, $args, $attachment ) {
            $lightbox = foogallery_gallery_template_setting( 'lightbox', 'unknown' );

            if ( 'arifancylightbox' === $lightbox ) {
                global $current_foogallery;
                
                $attr['data-fancybox-group'] = $current_foogallery ? 'lightbox_' . $current_foogallery->ID : 'lightbox';

                if ( ! isset( $attr['class'] ) )
                    $attr['class'] = '';
                else
                    $attr['class'] .= ' ';

                $attr['class'] .= 'ari-fancybox';
            }

            return $attr;
        }, 10, 3 );
    }
}
