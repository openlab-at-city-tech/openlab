<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 *
 * For input data:
 * 1. Sanitize data
 * 2. Based on field type, also validate data
 * Internal fields have spec with 'internal' => true
 */
class EPKB_Input_Filter {

	// basic fields
	const TEXT = 'text';                // use Text or Textarea input
	const CHECKBOX = 'checkbox';
	const RADIO = 'radio';
	const EMAIL = 'email';

	// advanced fields
	const SELECTION = 'select';         // use Dropdown or Radio_buttons_horizontal
	const CHECKBOXES_MULTI_SELECT = 'multi_select';
	const CHECKBOXES_MULTI_SELECT_NOT = 'multi_select_not';

	// custom fields
	const COLOR_HEX = 'color_hex';
	const NUMBER = 'number';   // use Text input
	const TRUE_FALSE = 'true_false';

	// special fields
	const ID = 'id';
	const LICENSE_KEY = 'license_key';
	const ENUMERATION = 'enumeration';  // use when input has to be from a list of values
	const INTERNAL_ARRAY = 'internal_array';              // array of stored values
	const WP_EDITOR = 'wp_editor';          // WP TinyMCE editor or text that contains HTML elements
	const URL = 'url';      // slug or url
	const TYPOGRAPHY = 'typography';      // slug or url

	/**
	 * Validate and sanitize input. If input not in spec then exclude it.
	 *
	 * NOTE: Missing input is not handled i.e. no default values are supplied.
	 *
	 * @param array $input to sanitize - array of settings (key-value pairs)
	 * @param array $specification
	 * @return array|WP_Error returns key - value pairs
	 */
	public function validate_and_sanitize_specs( array $input, array $specification ) {

		if ( empty( $input ) ) {
			return new WP_Error( 'invalid_input', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' (5532)' );
		}

		$sanitized_input = array();
		$errors = array();

		// filter each field
		foreach ( $input as $key => $input_value ) {

			if ( ! isset( $specification[$key] ) || $input_value === null ) {
				continue;
			}

			$field_spec = $specification[$key];
			$field_spec = wp_parse_args( $field_spec, EPKB_KB_Config_Specs::get_defaults() );

			// SANITIZE FIELD
			$type = empty( $field_spec['type'] ) ? '' : $field_spec['type'];
			switch ( $type ) {

				case self::CHECKBOXES_MULTI_SELECT:
				case self::CHECKBOXES_MULTI_SELECT_NOT:

					$input_value = is_array( $input_value ) ? $input_value : array();
					$input_adj = array();
					foreach ( $input_value as $arr_key => $arr_value ) {

						// one choice can have multiple true [key,value] pairs separated by comma
						$arr_value = empty($arr_value) ? '' : $arr_value;
						$tmp = explode(',', $arr_value);
						if ( ! empty($tmp[0]) && ! empty($tmp[1]) ) {
							$arr_key = $tmp[0];
							$arr_value = $tmp[1];
						}
						$input_adj[$arr_key] = sanitize_text_field($arr_value);
					}
					$input_value = $input_adj;
					break;

				case self::INTERNAL_ARRAY:
					// no need to sanitize
					break;
				
				case self::TYPOGRAPHY:
					$input_value = self::sanitize_typography( $input_value );
					break;
				
				case self::WP_EDITOR:
					$input_value = wp_kses( $input_value, EPKB_Utilities::get_extended_html_tags() );
					break;

				case self::TRUE_FALSE:
					// done in filter below
					break;

				case self::URL:
					$input_value = empty( $input_value ) || ! is_string( $input_value ) ? '' : trim( $input_value );
					break;

				case self::TEXT:
					if ( ! empty( $field_spec['allowed_tags'] ) ) {
						$input_value = wp_kses( $input_value, $field_spec['allowed_tags'] );
					} else {
						$input_value = trim( sanitize_text_field( $input_value ) );
					}
					break;

				default:
					$input_value = trim( sanitize_text_field( $input_value ) );
			}

			// validate/sanitize input
			$result = $this->filter_input_field( $input_value, $field_spec );
			if ( is_wp_error( $result ) ) {

				EPKB_Logging::add_log( 'Please change the value of ' . $field_spec['label'] . ' field. Current value: "' . $input_value . '" - ' . $result->get_error_message() . ', code: ' . $result->get_error_message(), $result );

				// log error only if a) NOT internal fields and more than 1 error encountered OR b) debug on
				if ( ( empty($field_spec['internal']) && count($errors) > 0 ) || ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ||
					! in_array( $field_spec['type'], array(self::CHECKBOX, self::SELECTION, self::CHECKBOXES_MULTI_SELECT, self::CHECKBOXES_MULTI_SELECT_NOT, self::TRUE_FALSE, self::ENUMERATION) )) {

					$lang = '<strong style="color:#d5ff8b">' . esc_html( $field_spec['label'] ) . '</strong>';
					/* translators: %s: value entered by user. */
					$errors[] = '<div style="padding: 20px 0 20px 0;">'. sprintf( esc_html__( 'Please change value of the %s field.', 'echo-knowledge-base' ), $lang ) . ' ' . $result->get_error_message() . '</div>';

					// internal fields and first error will just use default value
				} else {
					$sanitized_input[$key] = $field_spec['default'];
				}

			} else {
				$sanitized_input[$key] = $result;
			}

		} // foreach

		if ( empty( $errors ) ) {
			return $sanitized_input;
		}

		return new WP_Error('invalid_input', ' ' . implode(" ", $errors) );
	}

	private function filter_input_field( $value, $field_spec ) {

		// further sanitize the field
		switch ( $field_spec['type'] ) {

			case self::TEXT:
			case self::URL:
			case self::EMAIL:
				return $this->filter_text( $value, $field_spec );

			case self::LICENSE_KEY:
				return $this->filter_license_key( $value, $field_spec );

			case self::CHECKBOX:
				return $this->filter_checkbox( $value );

			case self::SELECTION:
				return $this->filter_select( $value, $field_spec );

			case self::CHECKBOXES_MULTI_SELECT:
			case self::CHECKBOXES_MULTI_SELECT_NOT:
				// no filtering needed;
				return $value;

			case self::NUMBER:
				return $this->filter_number( $value, $field_spec );

			case self::COLOR_HEX:
				return $this->filter_color_hex( $value, $field_spec );

			case self::TRUE_FALSE:
				return $this->filter_true_false( $value );

			case self::ID:
				return $this->filter_id( $value );

			case self::ENUMERATION:
				return $this->filter_enumeration( $value, $field_spec );

			case self::INTERNAL_ARRAY:
				// no filtering needed
				return $value;
			
			case self::TYPOGRAPHY:
				return $this->filter_typography( $value );

			case self::WP_EDITOR:
				return $this->filter_wp_editor( $value, $field_spec );

			default:
				return new WP_Error('eckb-invalid-input-type', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' - ' . $field_spec['type']);
		}
	}

	/**
	 * Sanitize and validate text. Output WP Error if text too big/small
	 *
	 * @param $text
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_text( $text, $field_spec ) {

		if ( is_array( $text ) ) {
			$text = '';
		}

		if ( strlen( $text ) > $field_spec['max'] ) {
			$nof_chars_to_remove = strlen( $text ) - $field_spec['max'];

			/* translators: %d: number of characters to remove by user when entering too long string. */
			$msg = sprintf( _n( 'The value is too long. Remove %d character.', 'The value is too long. Remove %d characters.', $nof_chars_to_remove, 'echo-knowledge-base' ), $nof_chars_to_remove );
			return new WP_Error('filter_text_big', $msg );
		}

		if ( ( empty( $text ) && ! empty( $field_spec['mandatory'] ) ) || ( strlen( $text ) > 0 && strlen( $text ) < $field_spec['min'] ) ) {
			$nof_chars_to_remove = $field_spec['min'] - strlen($text);

			/* translators: %d: number of characters to add by user when entering too short string. */
			$msg = sprintf( _n( 'The value is too short. Add at least %d character.', 'The value is too short. Add at least %d characters.', $nof_chars_to_remove, 'echo-knowledge-base' ), $nof_chars_to_remove );
			return new WP_Error('filter_text_small', $msg );
		}

		return $text;
	}

	/**
	 * Sanitize and license key text. Output WP Error if text too big/small
	 *
	 * @param $text
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_license_key( $text, $field_spec ) {

		$text = is_string( $text ) ? trim( $text ) : '';

		return $this->filter_text( $text, $field_spec );
	}

	/**
	 * Sanitize and validate selection. Output WP Error if text is not in the selection
	 *
	 * @param $value
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_select( $value, $field_spec ) {

		// don't check layouts
		if ( $field_spec['name'] == 'kb_main_page_layout' ) {
			return $value;
		}
		
		if ( ! in_array( $value, array_keys($field_spec['options']) )  && ! empty($field_spec['mandatory']) ) {
			$value_text = ( empty($value) ? esc_html__( 'empty', 'echo-knowledge-base' ) : '"' . $value . '"' );
			/* translators: 1: value entered by user, 2: HTML tag, 3: valid values user should enter. */
			$msg = '<br>' . sprintf( esc_html__( 'The value cannot be %1$s. %2$sValid values are: %3$s', 'echo-knowledge-base' ), $value_text, '<br>', implode(", ", $field_spec['options']) );
			return new WP_Error('filter_selection_invalid', $msg );
		}

		return $value;
	}

	/**
	 * Sanitize and validate checkbox.
	 *
	 * @param $value
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_checkbox( $value ) {

		if ( empty($value) || $value == 'off' ) {
			return "off";
		} else if ( $value == "on" ) {
			return $value;
		}

		return new WP_Error('filter_checkbox_invalid', sprintf( esc_html__( 'The value "%s" is not valid', 'echo-knowledge-base' ), $value ) );
	}

	/**
	 * Sanitize and validate a number
	 *
	 * @param $number
	 * @param array $field_spec
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_number( $number, $field_spec=array() ) {

		$number = empty($number) ? 0 : trim($number);
		$number_int = EPKB_Utilities::sanitize_int( $number, null );
		if ( $number != $number_int ) {
			/* translators: %s: value entered by user. */
			return new WP_Error('filter_not_number',sprintf( esc_html__( 'The value "%s" is not a number', 'echo-knowledge-base' ), $number ) );
		}

		if ( $number > $field_spec['max'] ) {
			/* translators: 1: value entered by user, 2: maximum length user should enter. */
			$msg = sprintf( esc_html__( 'The value %1$s is larger than maximum of %2$s', 'echo-knowledge-base' ), $number, $field_spec['max'] );
			return new WP_Error( 'filter_not_number', $msg );
		} else if ( $number < $field_spec['min'] ) {
			/* translators: 1: value entered by user, 2: minimum length user should enter. */
			$msg = sprintf( esc_html__( 'The value %1$s is smaller than minimum of %2$s', 'echo-knowledge-base' ), $number, $field_spec['min'] );
			return new WP_Error( 'filter_not_number', $msg );
		}

		return $number;
	}

	/**
	 * Sanitize and validate true/false value
	 *
	 * @param $boolean
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_true_false( $boolean ) {
		if ( $boolean === true )  {
			return true;
		} else if ( $boolean === false ) {
			return false;
		}

		return new WP_Error( 'filter_not_number', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' (112)' );
	}

	/**
	 * Sanitize and validate HEX color number
	 *
	 * @param $value
	 * @param $field_spec
	 *
	 * @return string|WP_Error
	 */
	public function filter_color_hex( $value, $field_spec=array() ) {
		if ( empty( $value ) && !$field_spec['mandatory'] ) {
			return $value;
		}

		// Check for a hex color string '#c1c2b4'
		if ( preg_match('/^#[a-f0-9]{6}$/i', $value) ) {
			return $value;
		}

		// Check for a hex color string without hash 'c1c2b4'
		else if ( preg_match('/^[a-f0-9]{6}$/i', $value) ) {
			return '#' . $value;
		}

		/* translators: %s: value entered by user. */
		return new WP_Error('filter_not_color_hex', sprintf( esc_html__( 'The value "%s" is not a valid HEX color. Expected format: #xxxxxx', 'echo-knowledge-base' ), $value ));
	}

	/**
	 * Sanitize and validate ID
	 *
	 * @param $id
	 *
	 * @return int|WP_Error
	 */
	private function filter_id( $id ) {
		$id = EPKB_Utilities::sanitize_get_id( $id );
		if ( is_wp_error($id) ) {
			return new WP_Error('filter_not_id', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' - ' . $id . ' - ' . $id->get_error_code() );
		}
		return $id;
	}

	/**
	 * Input has to match one of the predefined values.
	 *
	 * @param $value
	 * @param $field_spec
	 *
	 * @return mixed - WP_Error | valid value
	 */
	private function filter_enumeration( $value, $field_spec ) {
		if ( in_array( $value, $field_spec['options'] ) ) {
			return $value;
		}

		return new WP_Error('filter_not_enumeration', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' - ' . $value );
	}

	/**
	 * Sanitize and validate output from TinyMCE. Output WP Error if text too big/small
	 *
	 * @param $text
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_wp_editor( $text, $field_spec ) {

		if ( is_array($text) ) {
			$text = '';
		}

		if ( strlen($text) > $field_spec['max'] ) {
			$nof_chars_to_remove = strlen($text) - $field_spec['max'];
			/* translators: %d: number of characters to remove by user when entering too long string. */
			$msg = sprintf( _n( 'The value is too long. Remove %d character.', 'The value is too long. Remove %d characters.', $nof_chars_to_remove, 'echo-knowledge-base' ), $nof_chars_to_remove );
			return new WP_Error('filter_text_big', $msg );
		}

		if ( ( empty($text) && ! empty($field_spec['mandatory']) ) || ( strlen($text) > 0 && strlen($text) < $field_spec['min'] ) ) {
			$nof_chars_to_remove = $field_spec['min'] - strlen($text);
			/* translators: %d: number of characters to add by user when entering too short string. */
			$msg = sprintf( _n( 'The value is too short. Add at least %d character.', 'The value is too short. Add at least %d characters.', $nof_chars_to_remove, 'echo-knowledge-base' ), $nof_chars_to_remove );
			return new WP_Error('filter_text_small', $msg );
		}

		return $text;
	}

	/**
	 * Typography field. Allow only needed array keys
	 *
	 * @param $value
	 *
	 * @return mixed - WP_Error | valid value
	 */
	private function filter_typography( $value ) {

		if ( ! is_array( $value ) ) {
			return new WP_Error( 'filter_not_typograghy', esc_html__( 'Error Occurred', 'echo-knowledge-base' ) . ' - ' . EPKB_Utilities::get_variable_string( $value ) );
		}

		$google_fonts = EPKB_Typography::get_google_fonts_family_list();

		if ( ! empty($value['font-family']) && ! in_array($value['font-family'], $google_fonts) ) {
			$value['font-family'] = EPKB_Typography::$typography_defaults['font-family'];
		}

		if ( ! empty($value['font-size']) && ( $value['font-size'] < 4 || $value['font-size'] > 100 ) ) {
			$value['font-size'] = EPKB_Typography::$typography_defaults['font-size'];
		}

		if ( ! in_array($value['font-size-units'], ['px', 'em']) ) {
			$value['font-size-units'] = EPKB_Typography::$typography_defaults['font-size-units'];
		}

		if ( ! empty($value['font-weight']) && ! in_array($value['font-weight'], [100, 200, 300, 400, 500, 600, 700, 800, 900]) ) {
			$value['font-weight'] = EPKB_Typography::$typography_defaults['font-weight'];
		}

		return $value;
	}

	public static function sanitize_typography( $typography ) {

		$typography = is_array($typography) ? $typography : array();
		$typography = array_merge( EPKB_Typography::$typography_defaults, $typography );

		$typography_filtered = [];
		$allowed_keys = array_keys( EPKB_Typography::$typography_defaults );
		foreach ( $typography as $key => $value ) {

			if ( ! in_array( $key, $allowed_keys ) ) {
				continue;
			}

			$typography_filtered[$key] = sanitize_text_field( $value );
		}

		return $typography_filtered;
	}

	/**
	 * Place form fields into an array. Fill missing values with original settings if they are missing.
	 *
	 * @param $submitted_fields
	 * @param $all_fields_specs
	 * @param $orig_settings - original settings, internal fields will be preserved in the result
	 *
	 * @return array of name - value pairs
	 */
	public function retrieve_and_sanitize_form_fields( $submitted_fields, $all_fields_specs, $orig_settings ) {

		$name_values = array();
		foreach ($all_fields_specs as $key => $spec ) {

			// copy over fields that are internal
			if ( ! empty($spec['internal']) || $spec['type'] == self::ID ) {
				$default_value = isset($spec['default']) ? $spec['default'] : '';
				$orig_value = isset($orig_settings[$key]) ? $orig_settings[$key] :$default_value;
				$name_values += array( $key => $orig_value);
				continue;
			}

			// checkboxes in a box have zero or more values
			$is_multiselect =  $spec['type'] == self::CHECKBOXES_MULTI_SELECT;
			if ( $is_multiselect || $spec['type'] == self::CHECKBOXES_MULTI_SELECT_NOT) {

				$multi_selects = array();
				foreach ($submitted_fields as $submitted_key => $submitted_value) {

					$submitted_value = stripslashes( $submitted_value );
					$submitted_value = sanitize_text_field( $submitted_value );

					if ( ! empty($submitted_key) && strpos($submitted_key, $key) === 0) {

						$chunks = $is_multiselect ?  explode('[[-,-]]', $submitted_value) : explode('[[-HIDDEN-]]', $submitted_value);
						if ( empty($chunks[0]) || empty($chunks[1]) || ! empty($chunks[2]) ) {
							continue;
						}

						if ( $is_multiselect ) {
							$multi_selects[$chunks[0]] = $chunks[1];
						} else if ( ! empty($submitted_value) && strpos($submitted_value, '[[-HIDDEN-]]') !== false ) {
							$multi_selects[$chunks[0]] = $chunks[1];
						}
					}
				}

				$name_values += array( $key => $multi_selects );
				continue;
			}

			// checkbox or radio button without value is considered to be 'off'
			if ( empty($submitted_fields[$key]) && ( $spec['type'] == self::CHECKBOX || $spec['type'] == self::RADIO ) ) {
				$submitted_fields[$key] = 'off';
			}

			// for regular input if it exists then retrieve it
			if ( $spec['type'] == self::TYPOGRAPHY ) {
				$input_value = is_array($submitted_fields[$key]) ? $submitted_fields[$key] : array();

			} elseif ( isset($submitted_fields[$key]) ) {
				$input_value = trim( $submitted_fields[ $key ] );

			// if the input is missing then use the original config value
			} else {
				$default_value = isset($spec['default']) ? $spec['default'] : '';
				$input_value = isset($orig_settings[$key]) ? $orig_settings[$key] :$default_value;
			}

			if ( gettype($input_value) !== 'array' ) $input_value = stripslashes( $input_value );

			if ( $spec['type'] == self::WP_EDITOR ) {
				$name_values += array( $key => wp_kses( $input_value, EPKB_Utilities::get_extended_html_tags() ) );
			} elseif ( $spec['type'] == self::TYPOGRAPHY ) {
				$name_values += array( $key => self::sanitize_typography( $input_value ) );

			} elseif ( ( $spec['type'] == self::TEXT ) && ! ( empty( $spec['allowed_tags'] ) ) ) {
				$name_values += array($key => wp_kses($input_value, $spec['allowed_tags']));
			} else if ( ( $spec['type'] == self::EMAIL ) ) {
				$name_values += array( $key => sanitize_email( $input_value ) );
			} else {
				$name_values += array( $key => sanitize_text_field( $input_value ) );
			}
		}

		return $name_values;
	}
}
