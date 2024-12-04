<?php


namespace ColibriWP\Theme\Customizer\Controls;

trait ColibriWPControlsAdapter {

	protected $colibri_tab  = ColibriControl::DEFAULT_COLIBRI_TAB;
	protected $default      = '';
	protected $active_rules = array();

	public function json() {
		$json                 = parent::json();
		$json['colibri_tab']  = $this->colibri_tab;
		$json['active_rules'] = $this->active_rules;

		return $json;
	}
}
