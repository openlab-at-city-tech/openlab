<?php

/**
 * Base Class for all KB editor classes
 */
class EPKB_Editor_KB_Base_Config extends EPKB_Editor_Config_Base {

	protected $is_asea = false;
	protected $is_elay = false;
	protected $is_basic_main_page = false;
	protected $is_tabs_main_page = false;
	protected $is_categories_main_page = false;
	protected $is_grid_main_page = false;
	protected $is_sidebar_main_page = false;
	
	function load_config() {

		$kb_id = EPKB_Utilities::get_eckb_kb_id();
		$this->config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		$this->config = apply_filters( 'eckb_kb_config', $this->config );
		if ( empty($this->config) || is_wp_error($this->config) ) {
			$this->config = [];
			return;
		}
		
		$this->is_basic_main_page = $this->config['kb_main_page_layout'] == 'Basic';
		$this->is_tabs_main_page = $this->config['kb_main_page_layout'] == 'Tabs';
		$this->is_categories_main_page = $this->config['kb_main_page_layout'] == 'Categories';
		$this->is_grid_main_page = $this->config['kb_main_page_layout'] == 'Grid';
		$this->is_sidebar_main_page = $this->config['kb_main_page_layout'] == 'Sidebar';
			
		$this->is_asea = EPKB_Utilities::is_advanced_search_enabled( $this->config );
		$this->is_elay = EPKB_Utilities::is_elegant_layouts_enabled();
		
		// use basic layout if Elegant Layouts was disabled
		if ( ! $this->is_elay ) {
			
			if ( $this->is_grid_main_page ) {
				$this->is_grid_main_page = false;
				$this->is_basic_main_page = true;
				$this->config['kb_main_page_layout'] = 'Basic';
			}
			
			if ( $this->is_sidebar_main_page ) {
				$this->is_sidebar_main_page = false;
				$this->is_basic_main_page = true;
				$this->config['kb_main_page_layout'] = 'Basic';
			}
		}
	}
	
	function load_specs() {

		$this->specs = EPKB_KB_Config_Specs::get_fields_specification( $this->config['id'] );

		// specs for add-on configuration
		$this->specs = apply_filters( 'eckb_editor_fields_specs', $this->specs, $this->config['id'] );
	}
	
	function load_setting_zones() {
		return [];
	}
	
}