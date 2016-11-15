<?php 
require_once($this->plugin_path.'admin/general_settings.php');

class wp_lightbox_admin_menu{
	
	private $menu_name;	
		
	private $databese_parametrs;
	
	private $plugin_url;
	
	private $plugin_path;
	
	private $text_parametrs;

	public  $wp_lightbox_2_general_settings_page;
	
	function __construct($param){
		$this->menu_name='WP Lightbox 2';
		$this->databese_parametrs=$param['databese_parametrs']->get_general_settings;
			
		$this->wp_lightbox_2_general_settings_page  =new wp_lightbox_2_general_settings_page( array( 'plugin_url'=> $this->plugin_url, 'plugin_path' => $this->plugin_path,'databese_settings' =>$this->databese_parametrs));
		
		// set plugin url
		if(isset($param['plugin_url']))
			$this->plugin_url=$param['plugin_url'];
		else
			$this->plugin_url=trailingslashit(dirname(plugins_url('',__FILE__)));
		// set plugin path
		if(isset($param['plugin_path']))
			$this->plugin_path=$param['plugin_path'];
		else
			$this->plugin_path=trailingslashit(dirname(plugin_dir_path(__FILE__)));

		

	}

	
	/// function for registr new button
	function poll_button_register($plugin_array)
	{
		$url = $this->plugin_url.'admin/scripts/editor_plugin.js';
		$plugin_array["poll_mce"] = $url;
		return $plugin_array;
	
	}


	public function window_for_inserting_contentt(){}
	public function create_menu(){
		
		$manage_page_main = add_menu_page( $this->menu_name, $this->menu_name, 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this->wp_lightbox_2_general_settings_page, 'controller_page'),$this->plugin_url.'admin/images/icon_lightboxx2.png');
							add_submenu_page( str_replace( ' ', '-', $this->menu_name), 'General settings', 'General settings', 'manage_options', str_replace( ' ', '-', $this->menu_name), array($this->wp_lightbox_2_general_settings_page, 'controller_page'));
		add_action('admin_print_styles-' .$manage_page_main, array($this,'menu_requeried_scripts'));
	}
	public function menu_requeried_scripts(){
		wp_enqueue_script('jquery-ui-style');
		wp_enqueue_script('jquery');	
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script("jquery-ui-widget");
		wp_enqueue_script("jquery-ui-mouse");
		wp_enqueue_script("jquery-ui-slider");
		wp_enqueue_script("jquery-ui-sortable");
		wp_enqueue_script('wp-color-picker');	
		wp_enqueue_style("jquery-ui-style");
		wp_enqueue_style("admin_style_wp_lightbox");		
		wp_enqueue_style( 'wp-color-picker' );
		add_thickbox();
			
	}
	
}