<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Rating extends GF_Field {

	public $type = 'rating';

	// # FORM EDITOR & FIELD MARKUP -------------------------------------------------------------------------------------

	/**
	 * Prevent the field button being added.
	 *
	 * @return array
	 */
	public function get_form_editor_button() {
		return array();
	}

	/**
	 * Return the settings which should be available on the field in the form editor.
	 *
	 * @return array
	 */
	function get_form_editor_field_settings() {
		return array(
			'choices_setting',
			'rules_setting',
		);
	}

	/**
	 * Enable support for using the field with conditional logic.
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Returns the field inner markup.
	 *
	 * @param array $form The Form Object currently being processed.
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array $entry Null or the Entry Object currently being edited.
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {
		$form_id       = absint( $form['id'] );
		$disabled_text = $this->is_form_editor() ? 'disabled="disabled"' : '';

		$id       = $this->id;
		$field_id = $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		return sprintf( "<div class='ginput_container gsurvey-rating-wrapper'><div class='gsurvey-rating' id='%s'>%s</div></div>", $field_id, $this->get_rating_choices( $value, $disabled_text, $form_id ) );
	}

	/**
	 * Returns the input ID to be assigned to the field label for attribute.
	 *
	 * @param array $form The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_first_input_id( $form ) {
		return '';
	}

	/**
	 * Returns the markup for the rating choices.
	 *
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param string $disabled_text The input disabled attribute when in the form editor or entry detail page.
	 * @param int $form_id The ID of the current form.
	 *
	 * @return string
	 */
	public function get_rating_choices( $value, $disabled_text, $form_id ) {
		$content        = '';
		$is_form_editor = $this->is_form_editor();

		if ( is_array( $this->choices ) ) {
			// choices reversed in the form editor from version 2.1.1
			if ( $this->reversed ) {
				$this->choices = array_reverse( $this->choices );
			}

			$choice_id = 0;
			$count     = 1;

			$logic_event = $this->get_conditional_logic_event( 'click' );

			foreach ( $this->choices as $choice ) {

				if ( $is_form_editor || $form_id == 0 ) {
					$id = $this->id . '_' . $choice_id ++;
				} else {
					$id = $form_id . '_' . $this->id . '_' . $choice_id ++;
				}

				$choice_label = $choice['text'];
				$field_value  = ! empty( $choice['value'] ) || $this->enableChoiceValue ? $choice['value'] : $choice['text'];

				if ( rgblank( $value ) && RG_CURRENT_VIEW != 'entry' ) {
					$checked = rgar( $choice, 'isSelected' ) ? "checked='checked'" : '';
				} else {
					$checked = RGFormsModel::choice_value_match( $this, $choice, $value ) ? "checked='checked'" : '';
				}

				$content .= sprintf( "<input name='input_%d' type='radio' value='%s' %s id='choice_%s' %s %s %s /><label for='choice_%s' title='%s'>%s</label>", $this->id, esc_attr( $field_value ), $checked, $id, $logic_event, $this->get_tabindex(), $disabled_text, $id, esc_attr( $choice_label ), $choice_label );

				if ( $is_form_editor && $count >= 5 ) {
					break;
				}

				$count ++;
			}
		}

		return $content;
	}

	// # ENTRY RELATED --------------------------------------------------------------------------------------------------

	/**
	 * Format the entry value for when the field/input merge tag is processed. Not called for the {all_fields} merge tag.
	 *
	 * @param string|array $value The field value. Depending on the location the merge tag is being used the following functions may have already been applied to the value: esc_html, nl2br, and urlencode.
	 * @param string $input_id The field or input ID from the merge tag currently being processed.
	 * @param array $entry The Entry Object currently being processed.
	 * @param array $form The Form Object currently being processed.
	 * @param string $modifier The merge tag modifier. e.g. value
	 * @param string|array $raw_value The raw field value from before any formatting was applied to $value.
	 * @param bool $url_encode Indicates if the urlencode function may have been applied to the $value.
	 * @param bool $esc_html Indicates if the esc_html function may have been applied to the $value.
	 * @param string $format The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param bool $nl2br Indicates if the nl2br function may have been applied to the $value.
	 *
	 * @return string
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		$text = RGFormsModel::get_choice_text( $this, $value );

		return $url_encode ? urlencode( $text ) : $text;
	}

	/**
	 * Format the entry value for display on the entries list page.
	 *
	 * @param string|array $value The field value.
	 * @param array $entry The Entry Object currently being processed.
	 * @param string $field_id The field or input ID currently being processed.
	 * @param array $columns The properties for the columns being displayed on the entry list page.
	 * @param array $form The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		return RGFormsModel::get_choice_text( $this, $value );
	}

	/**
	 * Format the entry value for display on the entry detail page and for the {all_fields} merge tag.
	 *
	 * @param string|array $value The field value.
	 * @param string $currency The entry currency code.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string $format The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string $media The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		return RGFormsModel::get_choice_text( $this, $value );
	}

	/**
	 * Format the entry value before it is used in entry exports and by framework add-ons using GFAddOn::get_field_value().
	 *
	 * @param array $entry The entry currently being processed.
	 * @param string $input_id The field or input ID.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param bool|false $is_csv Is the value going to be used in the .csv entries export?
	 *
	 * @return string
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		return RGFormsModel::get_choice_text( $this, rgar( $entry, $input_id ) );
	}
}

GF_Fields::register( new GF_Field_Rating() );
