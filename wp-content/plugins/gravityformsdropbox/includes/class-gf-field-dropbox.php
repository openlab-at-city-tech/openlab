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
	 * @uses GFAddOn::get_base_url()
	 * @uses GF_Dropbox::get_app_key()
	 * @uses GF_Field::is_entry_detail()
	 * @uses GF_Field::is_form_editor()
	 * @uses GF_Field::get_conditional_logic_event()
	 *
	 * @return string
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		// Get Dropbox app key.
		$dropbox_app_key = gf_dropbox()->get_app_key();

		// Get form ID, entry detail and form editor states.
		$form_id         = absint( $form['id'] );
		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();

		$allowed_extensions_message = empty( $this->allowedExtensions ) ? '' : sprintf(
			/* translators: %s: file extensions. */
			__( 'Accepted file types: %s', 'gravityformsdropbox' ),
			str_replace( ',', ', ', join( ',', $this->get_extensions() ) )
		);

		// If we are in the form editor, display a placeholder button.
		if ( $is_form_editor ) {
			return sprintf(
				"<div class='gdropbox ginput_container'><img class='gform-dropbox-upload-button' src='%s' alt='%s' title='%s' /><span class='gform_dropbox_fileupload_rules gfield_description'>{$allowed_extensions_message}</span></div>",
				gf_dropbox()->get_base_url() . '/images/button-preview.png',
				esc_html__( 'Dropbox Button Preview', 'gravityformsdropbox' ),
				esc_html__( 'Dropbox Button Preview', 'gravityformsdropbox' )
			);
		}

		$id       = (int) $this->id;
		$field_id = $is_entry_detail || $is_form_editor || 0 === $form_id ? "input_$id" : 'input_' . $form_id . "_$id";
		$value    = esc_attr( $value );

		$rules_messages_id          = empty( $this->allowedExtensions ) ? '' : "gfield_dropbox_upload_rules_{$this->formId}_{$this->id}";
		$live_validation_message_id = 'live_validation_message_' . $form_id . '_' . $id;
		$logic_event                = version_compare( GFForms::$version, '2.4-beta-1', '<' ) ? $this->get_conditional_logic_event( 'keyup' ) : '';

		$html  = "<input name='input_{$id}' id='{$field_id}' type='hidden' value='{$value}' {$logic_event} {$this->get_aria_describedby( array( $rules_messages_id ) )} />";
		$html .= "<script type='text/javascript' src='//www.dropbox.com/static/api/2/dropins.js' id='dropboxjs' data-app-key='{$dropbox_app_key}'></script>";
		$html .= $allowed_extensions_message ? "<span class='gform_dropbox_fileupload_rules' id='{$rules_messages_id}'>{$allowed_extensions_message}</span>" : '';
		$html .= "<div class='validation_message validation_message--hidden-on-empty' id='{$live_validation_message_id}'></div>";
		return sprintf( "<div class='gdropbox ginput_container'>%s</div><div id='gform_preview_%s_%s' class='gdropbox_preview'></div>", $html, $form_id, $id );

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

		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);

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

		return array(
			'conditional_logic_field_setting',
			'error_message_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'rules_setting',
			'file_extensions_setting',
			'visibility_setting',
			'description_setting',
			'css_class_setting',
			'link_type_setting',
			'multiselect_setting',
		);

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
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a dashicons class.
	 *
	 * @since 2.7
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return gf_dropbox()->get_base_url() . '/images/dropbox-icon.svg';
	}

	/**
	 * Returns the scripts to be included for this field type in the form editor.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @uses GF_Field::get_form_editor_field_title()
	 *
	 * @return string
	 */
	public function get_form_editor_inline_script_on_page_render() {

		$js = sprintf( "function SetDefaultValues_%s(field) {field.label = '%s'; field.linkType = 'preview';}", $this->type, $this->get_form_editor_field_title() ) . PHP_EOL;

		$js .= 'jQuery( document ).bind( "gform_load_field_settings", function( event, field, form ) {';
		$js .= 'jQuery( "#field_multiselect" ).attr( "checked", field["multiselect"] == true );';
		$js .= '} );';

		return $js;

	}

	/**
	 * Returns the scripts to be included with the form init scripts on the front-end.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param array $form Form object.
	 *
	 * @uses GFCommon::get_base_url()
	 *
	 * @return string
	 */
	public function get_form_inline_script_on_page_render( $form ) {

		$options = array(
			'deleteImage' => GFCommon::get_base_url() . '/images/delete.png',
			'deleteText'  => esc_attr__( 'Delete file', 'gravityforms' ),
			'extensions'  => $this->get_extensions(),
			'formId'      => $form['id'],
			'inputId'     => $this->id,
			'linkType'    => $this->get_link_type( $form ),
			'multiselect' => $this->multiselect,
		);

		$script = 'new GFDropbox(' . json_encode( $options ) . ');';

		return $script;

	}

	/**
	 * Returns the Dropbox link type the field will use.
	 *
	 * @since 3.1
	 *
	 * @param array $form The form containing the current field.
	 *
	 * @return string
	 */
	public function get_link_type( $form ) {
		$link_type = $this->linkType ?: 'preview';

		/**
		 * Allows the link type to be overridden.
		 *
		 * @since unknown
		 * @since 3.1 Moved from get_form_inline_script_on_page_render().
		 *
		 * @param string $link_type The Dropbox link type. Possible values: preview (a preview link to the document for sharing) or direct (an expiring link to download the contents of the file). Default: preview.
		 * @param array  $form      The form containing the current field.
		 * @param int    $field_id  The ID of the current field.
		 */
		return gf_apply_filters( array(
			'gform_dropbox_link_type',
			$form['id'],
			$this->id
		), $link_type, $form, $this->id );
	}

	/**
	 * Retrieve the array of file extensions to be used by the chooser init script.
	 *
	 * @since 2.0.1
	 *
	 * @return array
	 */
	public function get_extensions() {
		if ( empty( $this->allowedExtensions ) ) {
			return array();
		}

		return GFCommon::clean_extensions( array_map( 'trim', explode( ',', strtolower( $this->allowedExtensions ) ) ) );
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
		$allowed_extensions = ! empty( $this->allowedExtensions ) ? $this->get_extensions() : array();

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
