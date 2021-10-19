<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Quiz extends GF_Field {

	public $type = 'quiz';

	// # FORM EDITOR & FIELD MARKUP -------------------------------------------------------------------------------------

	/**
	 * Return the field title, for use in the form editor.
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'Quiz', 'gravityformsquiz' );
	}

	/**
	 * Assign the Quiz button to the Advanced Fields group.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
			'icon'  => $this->get_form_editor_field_icon(),
		);
	}

	/**
	 * Set custom form editor field icon.
	 *
	 * @since 3.3
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--quiz';
	}

	/**
	 * Return the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'gquiz-setting-field-type',
			'gquiz-setting-question',
			'gquiz-setting-choices',
			'gquiz-setting-show-answer-explanation',
			'gquiz-setting-randomize-quiz-choices',
		);
	}

}

GF_Fields::register( new GF_Field_Quiz() );
