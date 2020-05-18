<?php

class M_WordPress_Routing extends C_Base_Module
{
	static $_use_canonical_redirect = TRUE;
    static $_use_old_slugs          = TRUE;

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
			'photocrati-wordpress_routing',
			'WordPress Routing',
			"Integrates the MVC module's routing implementation with WordPress",
			'3.1.8',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	function _register_adapters()
	{
        $this->get_registry()->add_adapter('I_Router', 'A_WordPress_Base_Url');
		$this->get_registry()->add_adapter('I_Router', 'A_WordPress_Router');
        $this->get_registry()->add_adapter('I_Routing_App', 'A_WordPress_Routing_App');
	}

	function _register_hooks()
	{
        add_action('template_redirect', array(&$this, 'restore_request_uri'), 1);

        // These two things cause conflicts in NGG. So we temporarily
        // disable them and then reactivate them, if they were used,
        // in the restore_request_uri() method
        if (has_action('template_redirect', 'wp_old_slug_redirect')) {
            remove_action( 'template_redirect', 'wp_old_slug_redirect');
        }
        if (has_action('template_redirect', 'redirect_canonical')) {
            remove_action( 'template_redirect', 'redirect_canonical');
        }
	}

    /**
     * When WordPress sees a url like http://foobar.com/nggallery/page/2/, it thinks that it is an
     * invalid url. Therefore, we modify the request uri before WordPress parses the request, and then
     * restore the request uri afterwards
     */
    function restore_request_uri()
	{
		if (isset($_SERVER['NGG_ORIG_REQUEST_URI']))
        {
            $request_uri = $_SERVER['NGG_ORIG_REQUEST_URI'];
            $_SERVER['UNENCODED_URL'] = $_SERVER['HTTP_X_ORIGINAL_URL'] = $_SERVER['REQUEST_URI'] = $request_uri;

            if (isset($_SERVER['ORIG_PATH_INFO'])) {
                $_SERVER['PATH_INFO'] = $_SERVER['ORIG_PATH_INFO'];
            }
		}
        // this is the proper behavior but it causes problems with WPML
        else {
            if (self::$_use_old_slugs) wp_old_slug_redirect();
            if (self::$_use_canonical_redirect) redirect_canonical();
        }
	}

    function get_type_list()
    {
        return array(
            'A_WordPress_Base_Url'    => 'adapter.wordpress_base_url.php',
            'A_Wordpress_Router'      => 'adapter.wordpress_router.php',
            'A_Wordpress_Routing_App' => 'adapter.wordpress_routing_app.php'
        );
    }
}

new M_WordPress_Routing();