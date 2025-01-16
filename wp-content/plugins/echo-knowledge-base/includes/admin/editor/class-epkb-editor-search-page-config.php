<?php

/**
 * SEARCH RESULTS (Advanced Search) Configuration for the front end editor
 */
 
class EPKB_Editor_Search_Page_Config extends EPKB_Editor_KB_Base_Config {

	protected $page_type = 'search-page';

	/**
	 * Retrieve Editor configuration
	 */
	public function load_setting_zones() {

		$this->setting_zones = [];

		$editor_settings_zones = apply_filters( 'epkb_front_end_editor_search_config', $this->config );
		if ( ! empty( $editor_settings_zones ) && is_array($editor_settings_zones) ) {
			$this->setting_zones += $editor_settings_zones;
		}
	}
}