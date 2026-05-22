<?php
/**
 * Gravity Forms Advanced Post Creation Form Population Functions.
 *
 * @package     Gravity_Forms\Gravity_Forms_APC
 * @copyright   Copyright (c) 2008-2025, Rocketgenius
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

namespace Gravity_Forms\Gravity_Forms_APC\Helpers;

if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class Form_Population.
 *
 * Handles form population from entry data for Advanced Post Creation.
 *
 * @since 1.6.0
 */
class Form_Population {

	/**
	 * Hidden field name template for single file uploads.
	 */
	const HIDDEN_SINGLE_FILE_FIELD = 'gform_apc_file_%d';

	/**
	 * Hidden field name template for multiple file uploads.
	 */
	const HIDDEN_MULTIPLE_FILES_FIELD = 'gform_apc_files_%d';

	/**
	 * Class name for read-only fields.
	 */
	const READONLY_CLASS = 'gform-apc-readonly';

	/**
	 * Gets an array of population compatible values for the supplied form and entry.
	 *
	 * @since 1.6.0
	 *
	 * @param array $form  The form to be populated.
	 * @param array $entry The entry being used as the source of the values to be populated.
	 *
	 * @return array
	 */
	public static function get_population_values_from_entry( $form, $entry ) {
		$population_values = [];

		// Entries created by other forms are out of scope.
		if ( $form['id'] != rgar( $entry, 'form_id' ) ) {
			return $population_values;
		}

		/** @var \GF_Field $field */
		foreach ( $form['fields'] as $field ) {

			if ( $field->displayOnly ) {
				continue;
			}

			$field_id = strval( $field->id );
			$inputs   = $field->get_entry_inputs();
			$values   = [];

			if ( is_array( $inputs ) ) {
				foreach ( $inputs as $input ) {
					$input_id            = strval( $input['id'] );
					$values[ $input_id ] = rgar( $entry, $input_id );
				}
			} else {
				$values = rgar( $entry, $field_id );
			}

			$population_values[ $field_id ] = $values;

		}

		return $population_values;
	}

	/**
	 * Set up dynamic population of the fields using the supplied input values.
	 *
	 * @since 1.6.0
	 *
	 * @param array $form   The form being populated.
	 * @param array $values The values to be populated.
	 * @param array $editable_fields Field IDs that can be edited.
	 *
	 * @return array
	 */
	public static function do_population( $form, $values, $editable_fields = [] ) {

		// File upload fields require special handling.
		add_filter( 'gform_field_content', [ self::class, 'filter_fileupload_field_content' ], 10, 5 );

		/** @var \GF_Field $field */
		foreach ( $form['fields'] as &$field ) {

			if ( $field->displayOnly  && $field->type !== 'password' ) { // Password fields are displayOnly, but need to be marked as readonly.
				continue;
			}

			$field_value = rgar( $values, $field->id );

			// Check if the field should be marked as read-only *before* checking if it's empty.
			$is_readonly_field = ! empty( $editable_fields ) && ! in_array( (string) $field->id, $editable_fields );

			if ( $is_readonly_field ) {
				// Mark the field as read-only. This also stores the value (even if empty) for potential restoration.
				self::mark_field_as_readonly( $field, $field_value, $form );
			}

			// If the original value was empty, there's nothing to populate, skip to the next field.
			if ( \GFCommon::is_empty_array( $field_value ) ) {
				continue;
			}

			// Only proceed with population logic if there is a value.
			$field->allowsPrepopulate = true;

			$input_type = $field->get_input_type();

			switch ( $input_type ) {
				case 'checkbox':
					break;

				case 'fileupload':
					// Special handling for file uploads.
					self::handle_fileupload_field( $field, $field_value, $editable_fields, $form );
					break;

				default:

					if ( ! is_array( $field_value ) ) {
						break;
					}

					$inputs = $field->get_entry_inputs();
					if ( is_array( $inputs ) ) {
						foreach ( $inputs as &$input ) {
							$input_value = rgar( $field_value, $input['id'] );
							if ( $input_value ) {
								$input['name'] = self::set_field_population_filter( $input['id'], $input_value );
							}
						}
						$field->inputs = $inputs;
						$field_value   = false;
					}
			}

			if ( $field_value ) {
				$field->inputName = self::set_field_population_filter( $field->id, $field_value );
			}

		}

		// Add general class if there are any readonly fields.
		if ( ! empty( $form['gform_apc_readonly_values'] ) ) {
			if ( ! isset( $form['cssClass'] ) ) {
				$form['cssClass'] = '';
			}
			$form['cssClass'] = trim( $form['cssClass'] . ' gform-apc-has-readonly' );
		}

		return $form;
	}

	/**
	 * Marks a field as read-only and stores its value for preservation.
	 *
	 * @since 1.6.0
	 *
	 * @param \GF_Field $field The field to mark as read-only.
	 * @param mixed $field_value The current value of the field.
	 * @param array $form The form being populated.
	 */
	private static function mark_field_as_readonly( &$field, $field_value, &$form ) {
		$field->cssClass = trim( $field->cssClass . ' ' . self::READONLY_CLASS );

		if ( ! isset( $field->customAttributes ) || ! is_array( $field->customAttributes ) ) {
			$field->customAttributes = [];
		}

		// Add custom HTML attributes to make field read-only.
		$field->customAttributes['readonly'] = 'readonly';

		// Add a hidden field to preserve the value on submission.
		if ( ! isset( $form['gform_apc_readonly_values'] ) ) {
			$form['gform_apc_readonly_values'] = [];
		}

		$form['gform_apc_readonly_values'][ $field->id ] = $field_value;
	}

	/**
	 * Handle special file upload field population.
	 *
	 * @since 1.6.0
	 *
	 * @param \GF_Field $field The field being populated
	 * @param mixed $value The file value
	 * @param array $editable_fields Array of editable field IDs
	 * @param array $form The form being populated
	 */
	public static function handle_fileupload_field( &$field, $value, $editable_fields = [], &$form = [] ) {
		// Early exit if no value to process.
		if ( empty( $value ) ) {
			return;
		}

		// Normalize the file value into a consistent format.
		$file_value = self::normalize_file_value( $value );

		// Set the field to allow prepopulation.
		$field->allowsPrepopulate = true;

		// Check if field should be read-only.
		$is_readonly = ! empty( $editable_fields ) && ! in_array( (string) $field->id, $editable_fields );

		// Configure field based on file type (single vs multiple).
		if ( ! $field->multipleFiles ) {
			// Get the single file URL.
			$single_file_url = is_array( $file_value ) ? reset( $file_value ) : $file_value;

			// Store data for the filter to use.
			self::store_field_data( $field->id, [
				'type' => 'single',
				'url' => $single_file_url,
				'is_readonly' => $is_readonly,
			]);
		} else {
			// Ensure we have an array of files.
			$multiple_files = is_array( $file_value ) ? $file_value : [ $file_value ];

			// Store data for the filter to use.
			self::store_field_data( $field->id, [
				'type' => 'multiple',
				'files' => $multiple_files,
				'is_readonly' => $is_readonly,
			]);
		}
	}

	/**
	 * Normalize file upload value into a consistent format.
	 *
	 * @since 1.6.0
	 *
	 * @param mixed $value The file value to normalize.
	 * @return mixed Normalized value (either string for single files or array for multiple files).
	 */
	private static function normalize_file_value( $value ) {
		// If we have a JSON string or a serialized array, parse it.
		if ( self::is_json( $value ) ) {
			return json_decode( $value, true );
		} elseif ( is_serialized( $value ) ) {
			return unserialize( $value );
		}

		return $value;
	}

	/**
	 * Storage for field data used by the filters.
	 *
	 * @var array
	 */
	private static $field_data = [];

	/**
	 * Store field data for use in filters.
	 *
	 * @since 1.6.0
	 *
	 * @param int $field_id The field ID.
	 * @param array $data The data to store.
	 */
	private static function store_field_data( $field_id, $data ) {
		self::$field_data[ $field_id ] = $data;
	}

	/**
	 * Retrieve stored field data.
	 *
	 * @since 1.6.0
	 *
	 * @param int $field_id The field ID.
	 * @return array|null The stored data or null if not found.
	 */
	private static function get_field_data( $field_id ) {
		return isset( self::$field_data[ $field_id ] ) ? self::$field_data[ $field_id ] : null;
	}

	/**
	 * Filter to display fileupload field content during population.
	 * This single handler manages both single and multiple file uploads.
	 *
	 * @since 1.6.0
	 *
	 * @param string    $field_content The field content HTML.
	 * @param \GF_Field $field_obj     The field object.
	 * @param mixed     $input_value   The input value.
	 * @param int       $entry_id      The entry ID.
	 * @param int       $form_id       The form ID.
	 * @return string The modified field content.
	 */
	public static function filter_fileupload_field_content( $field_content, $field_obj, $input_value, $entry_id, $form_id ) {
		// Only process fileupload fields managed by this class.
		if ( $field_obj->type != 'fileupload' ) {
			return $field_content;
		}

		// Get stored data for this field.
		$field_data = self::get_field_data( $field_obj->id );
		if ( ! $field_data ) {
			return $field_content;
		}

		$is_readonly = $field_data['is_readonly'];

		// Delegate rendering based on file upload type.
		if ( $field_data['type'] === 'single' ) {
			$field_content = self::render_single_fileupload_content( $field_content, $field_obj, $field_data, $is_readonly, $form_id );
		} elseif ( $field_data['type'] === 'multiple' ) {
			$field_content = self::render_multiple_fileupload_content( $field_content, $field_obj, $field_data, $is_readonly, $form_id );
		}

		return $field_content;
	}

	/**
	 * Renders the content for a single file upload field during population.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param string    $field_content Original field content HTML.
	 * @param \GF_Field $field_obj     Field object.
	 * @param array     $field_data    Stored data for the field.
	 * @param bool      $is_readonly   Whether the field is read-only.
	 * @param int       $form_id       Form ID.
	 * @return string Modified field content.
	 */
	private static function render_single_fileupload_content( $field_content, $field_obj, $field_data, $is_readonly, $form_id ) {
		$single_file_url = $field_data['url'];
		$filename        = basename( $single_file_url );

		// Add hidden input to preserve the value during submission.
		$hidden_field_name = sprintf( self::HIDDEN_SINGLE_FILE_FIELD, $field_obj->id );
		$hidden_input      = "<input type='hidden' name='{$hidden_field_name}' value='" . esc_attr( $single_file_url ) . "' />";

		$file_display = sprintf(
			"<div class='gform_apc_current_file' id='gform_apc_current_file_%d_%d'>" .
			"<strong>%s</strong> " .
			"<a href='%s' target='_blank'>%s</a>" .
			"</div>",
			absint( $form_id ),
			absint( $field_obj->id ),
			esc_html__( 'Current file:', 'gravityformsadvancedpostcreation' ),
			esc_url( $single_file_url ),
			esc_html( $filename ),
		);

		// Insert file display and hidden input.
		$pos = strpos( $field_content, '</div>', strpos( $field_content, 'class="ginput_container' ) );
		if ( $pos !== false ) {
			$field_content = substr_replace( $field_content, $file_display . $hidden_input, $pos, 0 );
		} else {
			$field_content .= $file_display . $hidden_input;
		}

		// Add read-only message and disable input if needed.
		if ( $is_readonly ) {
			$read_only_message = '<div class="gform_apc_readonly_message gfield_description">' . esc_html__( 'This field cannot be edited.', 'gravityformsadvancedpostcreation' ) . '</div>';
			$pos = strpos( $field_content, "<div class='gform_apc_current_file'" );
			if ( $pos !== false ) {
				$field_content = substr_replace( $field_content, $read_only_message, $pos, 0 );
			}
		}

		return $field_content;
	}

	/**
	 * Renders the content for a multiple file upload field during population.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param string    $field_content Original field content HTML.
	 * @param \GF_Field $field_obj     Field object.
	 * @param array     $field_data    Stored data for the field.
	 * @param bool      $is_readonly   Whether the field is read-only.
	 * @param int       $form_id       Form ID.
	 * @return string Modified field content.
	 */
	private static function render_multiple_fileupload_content( $field_content, $field_obj, $field_data, $is_readonly, $form_id ) {
		$multiple_files = $field_data['files'];

		$current_files_html = '';
		if ( ! empty( $multiple_files ) ) {
			$file_divs = ''; // Store individual file divs here
			foreach ( $multiple_files as $file_url ) {
				if ( empty( $file_url ) || ! is_string( $file_url ) ) {
					continue;
				}
				$filename = basename( $file_url );
				// Create a div similar to single file display for each file
				$file_divs .= sprintf(
					'<div class="gform_apc_current_file" id="gform_apc_current_file_%d_%d_%s">' . // Added unique ID using hash
					'<a href="%s" target="_blank">%s</a>' .
					'</div>',
					absint( $form_id ),
					absint( $field_obj->id ),
					md5( $file_url ), // Use hash of URL for unique ID part
					esc_url( $file_url ),
					esc_html( $filename ),
				);
			}
			if ( ! empty( $file_divs ) ) {
				// Wrap all individual file divs in a main container
				$current_files_html = sprintf(
					'<div class="gfield_description validation_message gfield_validation_message validation_message--hidden-on-empty">%s</div>',
					$file_divs
				);
			}
		}

		// Add a hidden field to store the value.
		$hidden_field_name = sprintf( self::HIDDEN_MULTIPLE_FILES_FIELD, $field_obj->id );
		$serialized_files  = esc_attr( json_encode( $multiple_files ) );
		$hidden_input      = "<input type='hidden' class='gform_apc_multiple_files_hidden' name='{$hidden_field_name}' value='{$serialized_files}' />";

		// Add read-only message if needed.
		$read_only_message_html = '';
		if ( $is_readonly ) {
			$read_only_message_html = '<div class="gform_apc_readonly_message gfield_description">' . esc_html__( 'This field cannot be edited.', 'gravityformsadvancedpostcreation' ) . '</div>';
		}

		// Append the read-only message (if any), the file list, and the hidden input at the end.
		$field_content .= $read_only_message_html . $current_files_html . $hidden_input;

		return $field_content;
	}

	/**
	 * Populate the field value by using the "dynamic population" feature of Gravity Forms.
	 *
	 * @since 1.6.0
	 *
	 * @param string $input_id The ID of the field or input to be populated.
	 * @param mixed  $value    The value to be populated.
	 *
	 * @return string
	 */
	public static function set_field_population_filter( $input_id, $value ) {
		$filter_name = 'gf_populated_field_' . str_replace( '.', '_', $input_id );
		add_filter( "gform_field_value_{$filter_name}", function() use ( $value ) {
			return $value;
		}, 50 );

		return $filter_name;
	}

	/**
	 * Check if a string is a valid JSON.
	 *
	 * @since 1.6.0
	 *
	 * @param string $string The string to check.
	 * @return bool
	 */
	public static function is_json( $string ) {
		if ( ! is_string( $string ) || empty( $string ) ) {
			return false;
		}

		$json = json_decode( $string );
		return ( json_last_error() === JSON_ERROR_NONE );
	}
}
