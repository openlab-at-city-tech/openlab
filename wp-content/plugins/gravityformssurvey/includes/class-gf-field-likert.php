<?php

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

class GF_Field_Likert extends GF_Field {

	public $type = 'likert';

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
			'gsurvey-likert-setting-enable-multiple-rows',
			'gsurvey-likert-setting-columns',
			'rules_setting',
		);
	}

	/**
	 * Conditional logic not currently supported.
	 *
	 * @todo Review once the GF advancedconditionallogic branch is merged with trunk.
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
		$is_admin      = ( $this->is_entry_detail() && ! $this->is_entry_detail_edit() ) || $this->is_form_editor();
		$disabled_text = $is_admin ? 'disabled="disabled"' : '';

		$id       = $this->id;
		$field_id = $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";

		return sprintf( "<div class='ginput_container ginput_container_likert'><table class='gsurvey-likert' id='%s'>%s</table></div>", esc_attr( $field_id ), $this->get_likert_rows( $value, $disabled_text, $form_id ) );
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
	 * Returns the markup for the likert rows.
	 *
	 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param string $disabled_text The input disabled attribute when in the form editor or entry detail page.
	 * @param int $form_id The ID of the current form.
	 *
	 * @return string
	 */
	public function get_likert_rows( $value, $disabled_text, $form_id ) {
		$multiple_rows = $this->gsurveyLikertEnableMultipleRows;
		$num_rows      = $multiple_rows ? count( $this->gsurveyLikertRows ) : 1;

		// Start column header row.
		$content = '<thead>';
		$content .= '<tr>';

		if ( $multiple_rows ) {
			$content .= "<th scope='col' class='gsurvey-likert-row-label'></th>";
		}

		foreach ( $this->choices as $choice ) {
			$content .= sprintf( "<th scope='col' class='gsurvey-likert-choice-label'>%s</th>", $choice['text'] );
		}

		$content .= '</tr>';
		$content .= '</thead>';
		// End column header row.

		$count = 1;

		$content .= '<tbody>';
		for ( $i = 1; $i <= $num_rows; $i ++ ) {

			$index     = $i - 1;
			$id        = $multiple_rows ? $this->inputs[ $index ]['id'] : $this->id;
			$row_text  = $this->gsurveyLikertRows[ $index ]['text'];
			$row_value = $this->gsurveyLikertRows[ $index ]['value'];

			$content .= '<tr>';
			if ( $multiple_rows ) {
				$content .= "<td data-label='' class='gsurvey-likert-row-label'>{$row_text}</td>";
			}

			$choice_id = 1;
			foreach ( $this->choices as $choice ) {
				//hack to skip numbers ending in 0. so that 5.1 doesn't conflict with 5.10
				if ( $choice_id % 10 == 0 ) {
					$choice_id ++;
				}

				$cell_class = 'gsurvey-likert-choice';
				$checked    = '';
				$selected   = $this->is_choice_selected( $value, $row_value, $choice['value'] );

				if ( $selected ) {
					$checked = "checked='checked'";
					$cell_class .= ' gsurvey-likert-selected';
				}

				$input_name  = sprintf( 'input_%s', $id );
				$field_value = $multiple_rows ? $row_value . ':' . $choice['value'] : $choice['value'];
				$input_id    = sprintf( 'choice_%d_%s_%d', $form_id, str_replace( '.', '_', $id ), $choice_id );

				$content .= sprintf( "<td data-label='%s' class='%s'><input name='%s' type='radio' value='%s' %s id='%s' %s %s %s/></td>", esc_attr( wp_strip_all_tags( $choice['text'], true ) ), $cell_class, $input_name, esc_attr( $field_value ), $checked, $input_id, $disabled_text, $this->get_tabindex(), $this->get_conditional_logic_event( 'click' ) );
				$choice_id ++;
			}
			
			$content .= '</tr>';

			if ( $this->is_form_editor() && $count >= 5 ) {
				break;
			}
			$count ++;

		}

		if ( $count < $num_rows ) {
			// Form editor context
			$cols = $multiple_rows ? count( $this->choices ) + 1 : count( $this->choices );
			$content .= sprintf( "<tr><td colspan='%d'>" . esc_html__( '%d of %d items shown. Edit field to view all', 'gravityformssurvey' ) . '</td></tr>', $cols, $count, $num_rows );
		}

		$content .= '</tbody>';

		return $content;
	}

	// # SUBMISSION -----------------------------------------------------------------------------------------------------

	/**
	 * Used to determine the required validation result.
	 *
	 * @param int $form_id The ID of the form currently being processed.
	 *
	 * @return bool
	 */
	public function is_value_submission_empty( $form_id ) {
		$inputs = $this->get_entry_inputs();

		if ( is_array( $inputs ) ) {
			foreach ( $inputs as $input ) {
				$value = rgpost( 'input_' . str_replace( '.', '_', $input['id'] ) );
				if ( is_array( $value ) || strlen( trim( $value ) ) <= 0 ) {
					return true;
				}
			}

			return false;
		} else {
			$value    = rgpost( 'input_' . $this->id );
			$is_empty = is_array( $value ) || strlen( trim( $value ) ) <= 0;

			return $is_empty;
		}
	}

	// # ENTRY RELATED --------------------------------------------------------------------------------------------------

	/**
	 * The Likert field type only uses inputs to store the entry values when multiple rows are enabled.
	 *
	 * @return array|null
	 */
	public function get_entry_inputs() {
		return $this->gsurveyLikertEnableMultipleRows ? $this->inputs : null;
	}

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
		$column_text = $this->get_column_text( $value, $entry, $input_id, true );

		return $url_encode ? urlencode( $column_text ) : $column_text;
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

		return esc_html( $this->get_column_text( $value, $entry, $field_id ) );
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
		if ( $media == 'email' ) {
			$inputs = $this->get_entry_inputs();

			if ( is_array( $inputs ) ) {
				$items = array();
				foreach ( $inputs as $input ) {
					$column_text = $this->get_column_text( $value, false, $input['id'], true );

					if ( empty( $column_text ) ) {
						continue;
					}

					$items[] = $column_text;
				}

				if ( empty( $items ) ) {

					return '';
				} elseif ( $format == 'html' ) {

					return sprintf( "<ul class='gsurvey-likert-entry'><li>%s</li></ul>", implode( '</li><li>', $items ) );
				} else {

					return implode( ', ', $items );
				}
			} else {

				return $this->get_column_text( $value );
			}
		} else {

			$form = GFFormsModel::get_form_meta( $this->formId );

			if ( $this->is_entry_detail() && $this->hide_empty_likert_field( $form, $value ) ) {

				return '';
			}

			return $this->get_field_input( $form, $value );
		}
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
		if ( empty( $input_id ) || absint( $input_id ) == $input_id ) {
			$inputs = $this->get_entry_inputs();
			if ( is_array( $inputs ) ) {
				$items = array();
				foreach ( $inputs as $input ) {
					$items[] = $this->get_column_text( false, $entry, $input['id'], true );
				}

				return implode( ', ', $items );
			} else {

				return $this->get_column_text( rgar( $entry, $this->id ) );
			}
		} else {

			return $this->get_column_text( false, $entry, $input_id );
		}
	}

	// # HELPERS --------------------------------------------------------------------------------------------------------

	/**
	 * Used to determine if the likert radio input should get the checked attribute.
	 *
	 * @param string|array $value The field value.
	 * @param string $row_id The unique ID for the row being processed.
	 * @param string $choice_value The choice value for the column currently being processed.
	 *
	 * @return bool
	 */
	public function is_choice_selected( $value, $row_id, $choice_value ) {
		if ( $this->gsurveyLikertEnableMultipleRows ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $row ) {
					if ( ! empty( $row ) ) {
						list( $row_val, $col_val ) = rgexplode( ':', $row, 2 );
						if ( $row_id == $row_val ) {
							return $col_val == $choice_value;
						}
					}
				}
			}

			return false;
		} else {
			return $value == $choice_value;
		}
	}

	/**
	 * Return the Likert column text for the stored entry value.
	 *
	 * @param string|array $value The field value.
	 * @param array|false $entry The Entry Object currently being processed.
	 * @param string|false $field_id The field or input ID currently being processed.
	 * @param bool|false $include_row_text Should the row text be returned along with the column text?
	 *
	 * @return string
	 */
	public function get_column_text( $value, $entry = false, $field_id = false, $include_row_text = false ) {
		if ( $this->gsurveyLikertEnableMultipleRows ) {
			$row_id = $this->get_row_id( $field_id );
			$values = ! empty( $entry ) ? RGFormsModel::get_lead_field_value( $entry, $this ) : $value;

			if ( $row_id && is_array( $values ) ) {
				foreach ( $values as $value ) {
					if ( ! empty( $value ) ) {
						list( $row_val, $col_val ) = rgexplode( ':', $value, 2 );
						if ( $row_id == $row_val ) {
							$choice_text = $this->get_choice_text( $col_val );

							return $include_row_text ? sprintf( '%s: %s', $this->get_row_label( $field_id ), $choice_text ) : $choice_text;
						}
					}
				}
			}

			return '';
		} else {

			return $this->get_choice_text( $value );
		}
	}

	/**
	 * Return the text for the supplied choice value.
	 *
	 * @param string $value The choice value.
	 *
	 * @return string
	 */
	public function get_choice_text( $value ) {
		if ( is_array( $this->choices ) ) {
			foreach ( $this->choices as $choice ) {
				if ( $choice['value'] == $value ) {
					return $choice['text'];
				}
			}
		}

		return '';
	}

	/**
	 * Retrieves the rows unique ID (value) by comparing the input label property with the row text property.
	 *
	 * @param string $input_id The ID of the input currently being processed.
	 *
	 * @return string|bool
	 */
	public function get_row_id( $input_id ) {
		$row_label = $this->get_row_label( $input_id );

		if ( $row_label && is_array( $this->gsurveyLikertRows ) ) {

			foreach ( $this->gsurveyLikertRows as $row ) {
				if ( $row_label == trim( $row['text'] ) ) {
					return $row['value'];
				}
			}
		}

		return false;
	}

	/**
	 * Retrieves the row label from the specified input.
	 *
	 * @param string $input_id The ID of the input currently being processed.
	 *
	 * @return string
	 */
	public function get_row_label( $input_id ) {
		$input = RGFormsModel::get_input( $this, $input_id );

		return trim( rgar( $input, 'label' ) );
	}

	/**
	 * Helper to determine if the empty field should be displayed when the lead detail grid is processed.
	 *
	 * @param array $form The Form object for the current Entry.
	 * @param string|array $value The field value.
	 *
	 * @return bool
	 */
	public function hide_empty_likert_field( $form, $value ) {
		$mode                       = empty( $_POST['screen_mode'] ) ? 'view' : $_POST['screen_mode'];
		$allow_display_empty_fields = $mode == 'view';

		return GFCommon::is_empty_array( $value ) && ! GFEntryDetail::maybe_display_empty_fields( $allow_display_empty_fields, $form, false );
	}

	public function sanitize_settings() {
		parent::sanitize_settings();

		$this->gsurveyLikertEnableMultipleRows = (bool) $this->gsurveyLikertEnableMultipleRows;
		$this->gsurveyLikertEnableScoring      = (bool) $this->gsurveyLikertEnableScoring;

		if ( $this->gsurveyLikertEnableMultipleRows && is_array( $this->gsurveyLikertRows ) ) {
			foreach ( $this->gsurveyLikertRows as &$row ) {
				if ( isset( $row['text'] ) ) {
					$row['text'] = trim( $this->maybe_wp_kses( $row['text'] ) );
				}

				if ( isset( $row['value'] ) ) {
					$row['value'] = wp_strip_all_tags( $row['value'] );
				}
			}
		}
	}
}

GF_Fields::register( new GF_Field_Likert() );
