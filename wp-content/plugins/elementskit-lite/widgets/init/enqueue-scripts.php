<?php
namespace ElementsKit_Lite\Widgets\Init;
use ElementsKit_Lite\Libs\Framework\Attr;

defined( 'ABSPATH' ) || exit;

class Enqueue_Scripts {

    public function __construct() {

        add_action( 'wp_enqueue_scripts', [$this, 'frontend_js']);
        add_action( 'wp_enqueue_scripts', [$this, 'frontend_css'], 99 );

        add_action( 'elementor/frontend/before_enqueue_scripts', [$this, 'elementor_js'] );
        add_action( 'elementor/editor/after_enqueue_styles', [$this, 'elementor_css'] );

        add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_3rd_party_style' ] );
    }

	public function is_plugin_active($plugin) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || $this->is_plugin_active_for_network( $plugin );
	}

	public function is_plugin_active_for_network($plugin) {
		if ( ! is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins' );
		if ( isset( $plugins[ $plugin ] ) ) {
			return true;
		}

		return false;
	}
    
    public function elementor_js() {
        // Register Scripts
        // size : 814 biyets ** used for back to top button circle progress bar
        wp_register_script( 'animate-circle', \ElementsKit_Lite::widget_url() . 'init/assets/js/animate-circle.min.js', [], \ElementsKit_Lite::version(), true );

        // Enqueue Scripts
        wp_enqueue_script( 'elementskit-elementor', \ElementsKit_Lite::widget_url() . 'init/assets/js/elementor.js', ['jquery', 'elementor-frontend', 'animate-circle'], \ElementsKit_Lite::version(), true );

        // compatibility
        if($this->is_plugin_active('elementskit/elementskit.php') && version_compare(\Elementskit::version(), '3.2.0', '<=')) {
            // added swiper js - elementor remove it when "Improved Asset Loading" is active
            if(defined('ELEMENTOR_ASSETS_URL')) {
                wp_enqueue_script(
                    'swiper',
                    ELEMENTOR_ASSETS_URL . 'lib/swiper/swiper.min.js',
                    [],
                    \ElementsKit_Lite::version(),
                    true
                );
            }
        }

        // added fluent form styles on the editor
        if (in_array('fluentform/fluentform.php', apply_filters('active_plugins', get_option('active_plugins')))) {
            wp_enqueue_style( 'fluent-form-styles' );
            wp_enqueue_style( 'fluentform-public-default' );
        }

		// register scripts for lottie
        wp_register_script( 'lottie', \ElementsKit_Lite::widget_url() . 'lottie/assets/js/lottie.min.js', [], \ElementsKit_Lite::version(), true );
        wp_register_script( 'lottie-init', \ElementsKit_Lite::widget_url() . 'lottie/assets/js/lottie.init.js', ['lottie', 'elementor-frontend'], \ElementsKit_Lite::version(), true );
    }

    public function elementor_css() {
        wp_enqueue_style( 'elementskit-panel', \ElementsKit_Lite::widget_url() . 'init/assets/css/editor.css', [], \ElementsKit_Lite::version() );
    }

    public function frontend_js() {
        if(is_admin()){
            return;
        }
            
        /*
        * Register scripts.
        * This scripts are only loaded when the associated widget is being used on a page.
        */
        wp_enqueue_script( 'ekit-widget-scripts', \ElementsKit_Lite::widget_url() . 'init/assets/js/widget-scripts.js', array( 'jquery' ), \ElementsKit_Lite::version(), true ); // Core most of the widgets init are bundled //
        wp_register_script( 'goodshare', \ElementsKit_Lite::widget_url() . 'init/assets/js/goodshare.min.js', array( 'jquery' ), \ElementsKit_Lite::version(), true ); // sosial share //       
        wp_register_script( 'datatables', \ElementsKit_Lite::widget_url() . 'init/assets/js/datatables.min.js', array( 'jquery' ), \ElementsKit_Lite::version(), true ); // table //

        $user_data = Attr::instance()->utils->get_option('user_data', []);
        $gmap_api_key = !empty($user_data['google_map']) ? $user_data['google_map']['api_key'] : '';
        wp_register_script( 'ekit-google-map-api', 'https://maps.googleapis.com/maps/api/js?key=' . $gmap_api_key . '', array('jquery'), \ElementsKit_Lite::version(), true );
        wp_register_script( 'ekit-google-gmaps', \ElementsKit_Lite::widget_url() . 'init/assets/js/gmaps.min.js', array('jquery'), \ElementsKit_Lite::version(), true );

        // funfact widget
        wp_register_script( 'odometer', \ElementsKit_Lite::widget_url() . 'init/assets/js/odometer.min.js', array('jquery'), \ElementsKit_Lite::version(), true );
    }

    public function frontend_css() {
        if(!is_admin()){
            wp_enqueue_style( 'ekit-widget-styles', \ElementsKit_Lite::widget_url() . 'init/assets/css/widget-styles.css', [], \ElementsKit_Lite::version() );

            wp_enqueue_style( 'ekit-responsive', \ElementsKit_Lite::widget_url() . 'init/assets/css/responsive.css', [], \ElementsKit_Lite::version() );
            
            // style for funfact odometer
            wp_register_style( 'odometer', \ElementsKit_Lite::widget_url() . 'init/assets/css/odometer-theme-default.css', [], \ElementsKit_Lite::version() );
        };

        if ( is_rtl() ) wp_enqueue_style( 'elementskit-rtl', \ElementsKit_Lite::widget_url() . 'init/assets/css/rtl.css', [], \ElementsKit_Lite::version() );
    }

    public function enqueue_3rd_party_style() {
        if (function_exists( 'weforms' )) {
            wp_enqueue_style( 'weforms', plugins_url('/weforms/assets/wpuf/css/frontend-forms.css', 'weforms' ), [], \ElementsKit_Lite::version() );
        }

        if(defined('WPFORMS_PLUGIN_SLUG')){
            wp_enqueue_style( 'wpforms', plugins_url( '/'. WPFORMS_PLUGIN_SLUG . '/assets/css/wpforms-full.css', WPFORMS_PLUGIN_SLUG ), [], \ElementsKit_Lite::version() );
        }
    }
}