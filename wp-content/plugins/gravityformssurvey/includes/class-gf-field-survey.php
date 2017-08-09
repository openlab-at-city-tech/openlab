<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Survey extends GF_Field {

	public $type = 'survey';

	// # FORM EDITOR & FIELD MARKUP -------------------------------------------------------------------------------------

	/**
	 * Return the field title, for use in the form editor.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'Survey', 'gravityformssurvey' );
	}

	/**
	 * Assign the Survey button to the Advanced Fields group.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	/**
	 * Return the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'gsurvey-setting-question',
			'gsurvey-setting-field-type',
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'admin_label_setting',
			'visibility_setting',
			'description_setting',
			'css_class_setting',
			'label_placement_setting',
		);
	}
}

GF_Fields::register( new GF_Field_Survey() );
