<?php

// If Gravity Forms is not loaded, exit.
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Dropbox Chooser field for Gravity Forms.
 *
 * @see GF_Field
 */
class GF_Field_Dropbox extends GF_Field {

	/**
	 * Field type.
	 *
	 * @since  1.0
	 * @access public
	 * @var    string $type Field type.
	 */
	public $type = 'dropbox';

	/**
	 * Returns the field inner markup.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array        $form  Form object.
	 * @param string|array $value Field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
	 * @param null|array   $entry Entry object currently being edited. Defaults to null.
	 *
	 * @uses GF_Field::is_form_editor()
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		// If we are in the form editor, display an error message.
		if ( $this->is_form_editor() ) {
			return sprintf(
				'<div class="ginput_container"><p>%s</p></div>',
				esc_html__( 'Dropbox Upload field is unavailable because the Dropbox Add-On is not configured using a custom Dropbox App.', 'gravityformsdropbox' )
			);
		}

		return;

	}

	/**
	 * Returns the field button properties for the form editor.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GF_Field::get_form_editor_field_title()
	 *
	 * @return array
	 */
	public function get_form_editor_button() {

		return array();

	}

	/**
	 * Returns the class names of the settings which should be available on the field in the form editor.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 */
	public function get_form_editor_field_settings() {

		return array( 'label_setting' );

	}

	/**
	 * Return the field title.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {

		return esc_attr__( 'Dropbox Upload', 'gravityformsdropbox' );

	}

	/**
	 * Format the entry value for display on the entry detail page and for the {all_fields} merge tag.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string|array $value    The field value.
	 * @param string       $currency The entry currency code.
	 * @param bool|false   $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param string       $format   The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param string       $media    The location where the value will be displayed. Possible values: screen or email.
	 *
	 * @return string
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		// Initialize return string.
		$return = '';

		// If field has no value, return.
		if ( empty( $value ) ) {
			return $return;
		}

		// Initialize output array.
		$output = array();

		// Convert field value JSON to array.
		$files = json_decode( $value, true );

		// Loop through files.
		foreach ( $files as $file ) {

			// If we are using the text format, add the file URL to the output.
			if ( 'text' === $format ) {
				$output[] = $file . PHP_EOL;
				continue;
			}

			// Get the file name.
			$file_name = explode( '?dl=', basename( $file ) );
			$file_name = $file_name[0];

			// Add list item to output array.
			$output[] = sprintf(
				'<li><a href="%s" target="_blank" title="%s">%s</a></li>',
				$file,
				esc_attr__( 'Click to view', 'gravityformsdropbox' ),
				$file_name
			);

		}

		// Join the output strings together.
		$return = join( PHP_EOL, $output );

		return empty( $return ) || 'text' === $format ? $return : sprintf( '<ul>%s</ul>', $return );

	}

	/**
	 * Format the entry value for display on the entries list page.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string|array $value    The field value.
	 * @param array        $entry    The Entry Object currently being processed.
	 * @param string       $field_id The field or input ID currently being processed.
	 * @param array        $columns  The properties for the columns being displayed on the entry list page.
	 * @param array        $form     The Form Object currently being processed.
	 *
	 * @uses GFEntryList::get_icon_url()
	 *
	 * @return string
	 */
	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		// If field value is empty, return.
		if ( empty( $value ) ) {
			return;
		}

		// Convert field value JSON to array.
		$files = json_decode( $value, true );

		// If there is only one file, display the link.
		if ( 1 === count( $files ) ) {

			// Get the file name.
			$file_name = explode( '?dl=', basename( $files[0] ) );
			$file_name = $file_name[0];

			// Get the icon URL.
			$thumb     = GFEntryList::get_icon_url( $file_name );

			$file_path = esc_attr( $files[0] );

			return sprintf(
				'<a href="%s" target="_blank" title="%s"><img src="%s" alt="%s" /></a>',
				$file_path,
				esc_attr__( 'Click to view', 'gravityforms' ),
				$thumb,
				$file_name
			);

		}

		return sprintf( esc_html__( '%d files', 'gravityforms' ), count( $files ) );

	}

	/**
	 * Format the entry value before it is used in entry exports and by framework add-ons using GFAddOn::get_field_value().
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array      $entry    The entry currently being processed.
	 * @param string     $input_id The field or input ID.
	 * @param bool|false $use_text When processing choice based fields should the choice text be returned instead of the value.
	 * @param bool|false $is_csv   Is the value going to be used in the .csv entries export?
	 *
	 * @return string
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {

		// Get the input ID from the field object.
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		// Get the field value.
		$value = rgar( $entry, $input_id );

		// If the field value is empty, return.
		if ( rgblank( $value ) ) {
			return;
		}

		// Convert field value JSON to array.
		$value = json_decode( $value, true );

		return implode( ' , ', $value );

	}

	/**
	 * Format the entry value for when the field/input merge tag is processed. Not called for the {all_fields} merge tag.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string|array $value      The field value. Depending on the location the merge tag is being used the following functions may have already been applied to the value: esc_html, nl2br, and urlencode.
	 * @param string       $input_id   The field or input ID from the merge tag currently being processed.
	 * @param array        $entry      The Entry Object currently being processed.
	 * @param array        $form       The Form Object currently being processed.
	 * @param string       $modifier   The merge tag modifier. e.g. value
	 * @param string|array $raw_value  The raw field value from before any formatting was applied to $value.
	 * @param bool         $url_encode Indicates if the urlencode function may have been applied to the $value.
	 * @param bool         $esc_html   Indicates if the esc_html function may have been applied to the $value.
	 * @param string       $format     The format requested for the location the merge is being used. Possible values: html, text or url.
	 * @param bool         $nl2br      Indicates if the nl2br function may have been applied to the $value.
	 *
	 * @return string
	 */
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {

		// If field value is empty, return.
		if ( empty( $value ) ) {
			return;
		}

		// Convert field value JSON to array.
		$files = json_decode( $value, true );

		// Initialize return string.
		$return = '';

		// If we are using the HTML format, display the files as an unordered list.
		if ( 'html' === $format ) {

			// Start unordered list.
			$return = '<ul>';

			// Loop through files and add to list.
			foreach ( $files as $file ) {
				$return .= sprintf( '<li><a href="%s">%s</a></li>', $file, basename( $file ) );
			}

			// End unordered list.
			$return .= '</ul>';

			return $return;

		}

		// Loop through files.
		foreach ( $files as $file ) {
			$return .= $file . PHP_EOL;
		}

		return $return;

	}

	/**
	 * Validate selected files.
	 *
	 * @since 1.2.1
	 * @access public
	 *
	 * @param string|array $value The field value from get_value_submission().
	 * @param array        $form  The Form Object currently being processed.
	 *
	 * @uses GFCommon::clean_extensions()
	 * @uses GFCommon::file_name_has_disallowed_extension()
	 * @uses GFCommon::match_file_extension()
	 */
	public function validate( $value, $form ) {

		// If field value is empty, return.
		if ( empty( $value ) ) {
			return;
		}

		// Convert field value JSON to array.
		$files = json_decode( $value, true );

		// Get allowed extensions.
		$allowed_extensions = ! empty( $this->allowedExtensions ) ? GFCommon::clean_extensions( explode( ',', strtolower( $this->allowedExtensions ) ) ) : array();

		// Loop through the files.
		foreach ( $files as $file ) {

			// Get file path without extension.
			$file_path = explode( '?', $file );
			$file_path = reset( $file_path );

			// If no allowed extensions are set, use default allowed extensions.
			if ( empty( $allowed_extensions ) ) {
				if ( GFCommon::file_name_has_disallowed_extension( $file_path ) ) {
					$this->failed_validation = true;
					$this->validation_message = empty( $this->errorMessage ) ? esc_html__( 'The uploaded file type is not allowed.', 'gravityformsdropbox' ) : $this->errorMessage;
				}
			} else {
				if ( ! GFCommon::match_file_extension( $file_path, $allowed_extensions ) ) {
					$this->failed_validation  = true;
					$this->validation_message = empty( $this->errorMessage ) ? sprintf( esc_html__( 'The uploaded file type is not allowed. Must be one of the following: %s', 'gravityformsdropbox' ), strtolower( $this->allowedExtensions ) ) : $this->errorMessage;
				}
			}

		}

	}

}

// Register field with Gravity Forms.
GF_Fields::register( new GF_Field_Dropbox() );
