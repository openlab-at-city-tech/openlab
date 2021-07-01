<?php

define('NGG_AJAX_SLUG', 'photocrati_ajax');

class M_Ajax extends C_Base_Module
{
    function define($id = 'pope-module',
                    $name = 'Pope Module',
                    $description = '',
                    $version = '',
                    $uri = '',
                    $author = '',
                    $author_uri = '',
                    $context = FALSE)
    {
        parent::define(
            'photocrati-ajax',
            'AJAX',
            'Provides AJAX functionality',
            '3.3.21',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com'
        );
		C_NextGen_Settings::get_instance()->add_option_handler('C_Ajax_Option_Handler', array(
			'ajax_slug',
			'ajax_url',
			'ajax_js_url'
		));

        if (is_multisite()) C_NextGen_Global_Settings::get_instance()->add_option_handler('C_Ajax_Option_Handler', array(
            'ajax_slug',
            'ajax_url',
            'ajax_js_url'
        ));
    }

    function _register_utilities()
    {
        $this->get_registry()->add_utility('I_Ajax_Controller', 'C_Ajax_Controller');

    }

    /**
     * Hooks into the WordPress framework
     */
    function _register_hooks()
    {
        add_action('init', array(get_class(), 'register_scripts'), 9);
        add_action('ngg_routes', array(&$this, 'define_routes'));
	    add_action('init', array(&$this, 'serve_ajax_request'));
    }

	function serve_ajax_request()
	{
		if (isset($_REQUEST[NGG_AJAX_SLUG])) {
			$controller = C_Ajax_Controller::get_instance();
			$controller->index_action();
            // E_Clean_Exit may cause a warning to be appended to our response, spoiling any JSON sent
            exit;
		}
	}

    function define_routes($router)
    {
        $app = $router->create_app('/photocrati_ajax');
        $app->route('/', 'I_Ajax_Controller#index');
    }

    /**
     * Loads a single script to provide the photocrati_ajax settings to the web browser
     */
    static function register_scripts()
    {
        $settings = C_NextGen_Settings::get_instance();
        $router   = C_Router::get_instance();

        wp_register_script('photocrati_ajax', $router->get_static_url('photocrati-ajax#ajax.min.js'), array('jquery'), NGG_SCRIPT_VERSION);

        $vars = array(
            'url' => $settings->get('ajax_url'),
            'wp_home_url' => $router->get_base_url('home'),
            'wp_site_url' => $router->get_base_url('site'),
            'wp_root_url' => $router->get_base_url('root'),
            'wp_plugins_url' => $router->get_base_url('plugins'),
            'wp_content_url' => $router->get_base_url('content'),
            'wp_includes_url' => includes_url(),
            'ngg_param_slug'          => C_NextGen_Settings::get_instance()->get('router_param_slug', 'nggallery')
        );
        wp_localize_script('photocrati_ajax', 'photocrati_ajax', $vars);
    }

    /**
     * Pass PHP object or array to JS, preserving numeric and boolean value
     * @param string $handle 
     * @param string $name 
     * @param object|array $data 
     */
    static function pass_data_to_js($handle, $var_name, $data)
    {
        $var_name = esc_js($var_name);
        return wp_add_inline_script($handle, "let {$var_name} = ".json_encode($data, JSON_NUMERIC_CHECK));
    }

    function get_type_list()
    {
        return array(
            'C_Ajax_Installer' => 'class.ajax_installer.php',
            'C_Ajax_Controller' => 'class.ajax_controller.php',
            'M_Ajax' => 'module.ajax.php'
        );
    }
}

class C_Ajax_Option_Handler {
	private $slug = NGG_AJAX_SLUG;

	function get_router() {
		return C_Router::get_instance();
	}

	function get( $key, $default = null ) {
		$retval = $default;

		switch ( $key ) {
			case 'ajax_slug':
				$retval = $this->slug;
				break;
			case 'ajax_url':
				$retval = site_url( "/index.php?{$this->slug}=1" );
				if ( is_ssl() && strpos( $retval, 'https' ) === false ) {
					$retval = str_replace( 'http', 'https', $retval );
				}
				break;
		}

		return $retval;
	}
}

new M_Ajax();
