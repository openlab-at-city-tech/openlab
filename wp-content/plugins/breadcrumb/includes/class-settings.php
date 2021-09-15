<?php
if ( ! defined('ABSPATH')) exit;  // if direct access

class class_breadcrumb_settings{

    public function __construct(){

		add_action( 'admin_menu', array( $this, 'breadcrumb_menu_init' ), 12 );

		}


	public function breadcrumb_settings(){
		include('menu/settings.php');	
	}

	
	public	function breadcrumb_menu_init(){

		add_menu_page(__('Breadcrumb', 'breadcrumb'), __('Breadcrumb', 'breadcrumb'), 'manage_options', 'breadcrumb_settings', array( $this, 'breadcrumb_settings' ), breadcrumb_plugin_url.'assets/admin/images/right-arrow.png');
		}


	}
	
new class_breadcrumb_settings();