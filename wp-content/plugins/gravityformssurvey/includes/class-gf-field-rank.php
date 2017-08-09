<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Rank extends GF_Field {

	public $type = 'rank';

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
		);
	}

	/**
	 * Conditional logic not currently supported.
	 *
	 * @return bool
	 */
	public function is_conditional_logic_supported() {
		return false;
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
		$is_admin      = $this->is_entry_detail() || $this->is_form_editor();
		$disabled_text = $is_admin ? 'disabled="disabled"' : '';

		$id       = $this->id;
		$field_id = $form_id == 0 ? "gsurvey-rank-$id" : 'gsurvey-rank-' . $form_id . "-$id";

		$hidden_input = sprintf( "<input type='hidden' id='%s-hidden' name='input_%d' />", $field_id, $this->id );

		return sprintf( "<div class='ginput_container ginput_container_rank'><ul class='gsurvey-rank' id='%s'>%s</ul>%s</div>", $field_id, $this->get_rank_choices( $value, $disabled_text, $form_id ), $hidden_input );
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
	 * Returns the markup for the rank choices.
	 *
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param string $disabled_text The input disabled attribute when in the form editor or entry detail page.
	 * @param int $form_id The ID of the current form.
	 *
	 * @return string
	 */
	public function get_rank_choices( $value, $disabled_text, $form_id ) {
		$choices          = array();
		$content          = '';
		$is_entry_detail  = $this->is_entry_detail();
		$is_form_editor   = $this->is_form_editor();

		if ( ! empty( $value ) ) {
			$ordered_values = explode( ',', $value );
			foreach ( $ordered_values as $ordered_value ) {
				$choices[] = array(
					'value' => $ordered_value,
					'text'  => RGFormsModel::get_choice_text( $this, $ordered_value ),
				);
			}
		} else {
			$choices = $this->choices;
		}

		if ( is_array( $this->choices ) ) {
			$choice_id = 0;
			$count     = 1;

			foreach ( $choices as $choice ) {

				if ( $is_entry_detail || $is_form_editor || $form_id == 0 ) {
					$id = $this->id . '_' . $choice_id ++;
				} else {
					$id = $form_id . '_' . $this->id . '_' . $choice_id ++;
				}

				$field_value = ! empty( $choice['value'] ) || $this->enableChoiceValue ? $choice['value'] : $choice['text'];

				$content .= sprintf( "<li data-index='%s' class='gsurvey-rank-choice choice_%s' id='%s' >%s</li>", $choice_id, $id, esc_attr( $field_value ), $choice['text'] );

				if ( $is_form_editor && $count >= 5 ) {
					break;
				}

				$count ++;
			}

			$total = sizeof( $this->choices );
			if ( $count < $total ) {
				$content .= "<li class='gchoice_total'>" . sprintf( esc_html__( '%d of %d items shown. Edit field to view all', 'gravityforms' ), $count, $total ) . '</li>';
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

		return $this->get_value_entry_detail( $value, '', false, $format );
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

		return $this->get_value_entry_detail( $value, '', false, 'text' );
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
		$ordered_values = ! empty( $value ) ? explode( ',', $value ) : '';
		$new_value      = '';

		if ( is_array( $ordered_values ) ) {
			switch ( $format ) {
				case 'text' :
					$c = 1;
					foreach ( $ordered_values as &$ordered_value ) {
						$ordered_value = $c ++ . '. ' . RGFormsModel::get_choice_text( $this, $ordered_value );
					}
					$new_value = implode( ', ', $ordered_values );
					break;

				default :
					foreach ( $ordered_values as $ordered_value ) {
						$new_value .= sprintf( '<li>%s</li>', RGFormsModel::get_choice_text( $this, $ordered_value ) );
					}
					$new_value = sprintf( "<ol class='gsurvey-rank-entry'>%s</ol>", $new_value );
			}
		}

		return $new_value;
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

		return $this->get_value_entry_detail( rgar( $entry, $input_id ), '', false, 'text' );
	}
}

GF_Fields::register( new GF_Field_Rank() );
