<?php

/***
    {
        Module: photocrati-frame_communication,
		Depends: { photocrati-router }
    }
***/

class M_Frame_Communication extends C_Base_Module
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
			'photocrati-frame_communication',
			'Frame/iFrame Inter-Communication',
			'Provides a means for HTML frames to share server-side events with each other',
			'3.3.21',
            'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
            'Imagely',
            'https://www.imagely.com',
			$context
		);

        C_NextGen_Settings::get_instance()->add_option_handler('C_Frame_Communication_Option_Handler', array(
           'frame_event_cookie_name',
        ));
        C_NextGen_Global_Settings::get_instance()->add_option_handler('C_Frame_Communication_Option_Handler', array(
            'frame_event_cookie_name',
        ));
	}

	function _register_utilities()
	{
		$this->get_registry()->add_utility(
			'I_Frame_Event_Publisher', 'C_Frame_Event_Publisher'
		);
	}

	function _register_hooks()
	{
		add_action('init', array($this, 'register_script'));
        add_filter('ngg_admin_script_handles', array($this, 'add_script_to_ngg_pages'));
        add_action('ngg_enqueue_frame_event_publisher_script', array($this, 'enqueue_script'));

        // Elementor's editor.php runs `new \WP_Scripts()` which requires we register scripts on both init and this
        // action if we want the attach-to-post code to function (which relies on frame_event_publisher)
        add_action('elementor/editor/before_enqueue_scripts', array($this, 'register_script'));
    }

	function add_script_to_ngg_pages($scripts)
	{
		$scripts['frame_event_publisher'] = $this->module_version;
		return $scripts;
	}

	function enqueue_script()
	{
		wp_enqueue_script('frame_event_publisher');
		wp_localize_script(
			'frame_event_publisher',
			'frame_event_publisher_domain',
			array(parse_url(site_url(), PHP_URL_HOST))
		);
	}


	function register_script()
	{
		$router = C_Router::get_instance();

		wp_register_script(
			'frame_event_publisher',
			$router->get_static_url('photocrati-frame_communication#frame_event_publisher.js'),
			array('jquery'),
			$this->module_version
		);
	}

    function get_type_list()
    {
        return array(
            'C_Frame_Communication_Option_Handler'	=> 'class.frame_communication_option_handler.php',
            'C_Frame_Event_Publisher' 			    => 'class.frame_event_publisher.php'
        );
    }
}

class C_Frame_Communication_Option_Handler
{
	function get($key, $default='X-Frame-Events')
	{
		return 'X-Frame-Events';
	}
}

new M_Frame_Communication();
