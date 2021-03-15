<?php

class GP_Media_Library extends GWPerk {

	public $version = GP_MEDIA_LIBRARY_VERSION;
	public $min_gravity_perks_version = '1.2.12';
	public $min_gravity_forms_version = '2.0.8';
	public $min_wp_version = '4.4';
	public $prefix = 'gpMediaLibrary';
	public $post_type = 'gplp';
	public $preview_post = null;

	private static $instance = null;

	public static function get_instance( $perk_file ) {

		if ( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}

		return self::$instance;
	}

	public function init() {

		$this->log( "Initializing GP Media Library!\n" . str_repeat( '=', 76 ) );

		parent::init();

		load_plugin_textdomain( 'gp-media-library', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->enqueue_field_settings();

		add_filter( 'gform_tooltips', array( $this, 'add_tooltips' ) );
		add_filter( 'gform_logging_supported', array( $this, 'add_logging_support' ) );
		add_filter( 'gform_entry_meta', array( $this, 'register_entry_meta' ), 10, 2 );
		add_filter( 'gform_entries_field_value', array( $this, 'format_entry_meta_for_display' ), 10, 4 );

		// functionality
		add_filter( 'gform_entry_post_save', array( $this, 'maybe_upload_to_media_library' ), 10, 2 );
		add_action( 'gform_after_update_entry', array( $this, 'maybe_upload_to_media_library_after_update' ), 10, 2 );
		add_action( 'wp_ajax_rg_delete_file', array( $this, 'hijack_delete_file' ), 9 );
		add_action( 'gform_delete_lead', array( $this, 'maybe_delete_from_media_library' ) );

		add_action( 'gform_after_create_post', array( $this, 'acf_integration' ), 10, 3 );
		add_action( 'gform_advancedpostcreation_post_after_creation', array( $this, 'apc_acf_integration' ), 10, 4 );
		add_action( 'gform_advancedpostcreation_post_after_creation', array( $this, 'apc_custom_field_integration' ), 10, 4 );
		add_action( 'gravityview/fields/fileupload/link_content', array( $this, 'gravityview_file_upload_content' ), 10, 2 );

		add_filter( 'gform_admin_pre_render', array( $this, 'add_image_merge_tags' ) );
		add_action( 'gform_pre_replace_merge_tags', array( $this, 'replace_image_merge_tags' ), 5, 7 );

		add_filter( 'gform_pre_send_email', array( $this, 'handle_attachments' ), 10, 4 );

	}

	/**
	 * @return array
	 */
	public function get_supported_field_types() {
		return apply_filters( 'gpml_supported_field_types', array(
			'fileupload',
			'slim',
		) );
	}

	## SETTINGS ##

	public function field_settings_ui( $position ) {
		?>

		<li class="gpml-field-setting field_setting" style="display:none;">
			<input type="checkbox" value="1" id="gpml-enable"
				   onchange="SetFieldProperty( 'uploadMediaLibrary', this.checked );"/>
			<label for="gpml-enable" class="inline">
				<?php _e( 'Upload to Media Library' ); ?>
				<?php gform_tooltip( 'gpml_enable' ); ?>
			</label>
		</li>

		<?php
	}

	public function field_settings_js() {
		?>

		<script type="">
			window.gpmlSupportedFieldTypes = <?php echo json_encode( $this->get_supported_field_types() ); ?>;

			(function ($) {

				$(document).ready(function () {
					for (fieldType in fieldSettings) {
						if (
							fieldSettings.hasOwnProperty(fieldType)
							&& $.inArray(fieldType, window.gpmlSupportedFieldTypes) !== -1
						) {
							fieldSettings[fieldType] += ', .gpml-field-setting';
						}
					}
				});

				$(document).bind('gform_load_field_settings', function (event, field, form) {
					$('#gpml-enable').prop('checked', field['uploadMediaLibrary'] == true);
				});

			})(jQuery);
		</script>

		<?php
	}

	public function add_tooltips( $tooltips ) {
		$tooltips['gpml_enable'] = sprintf( '<h6>%s</h6> %s', __( 'Upload to Media Library' ), __( 'Upload files from this field to the WordPress Media Library.' ) );

		return $tooltips;
	}

	public function register_entry_meta( $entry_meta, $form_id ) {

		$form = GFAPI::get_form( $form_id );
		/**
		 * @var GF_Field $field
		 */
		foreach ( $form['fields'] as $field ) {
			if ( $this->is_applicable_field( $field ) ) {
				$label = _n( 'Media Library ID', 'Media Library IDs', $field->multipleFiles ? 2 : 1, 'gp-media-library' );
				$label = sprintf( '%s (%s)', $label, $field->get_field_label( false, '' ) );
				$entry_meta[ $this->get_file_ids_meta_key( $field->id ) ] = array(
					'label'             => esc_html( $label ),
					'is_default_column' => false,
					'is_numeric'        => false,
				);
			}
		}

		return $entry_meta;
	}

	public function format_entry_meta_for_display( $value, $form_id, $field_id, $entry ) {
		if ( strpos( $field_id, 'gpml_ids_' ) !== false && is_array( $entry[ $field_id ] ) ) {
			$value = implode( ', ', $entry[ $field_id ] );
		}
		return $value;
	}


	## FUNCTIONALITY ##

	public function maybe_upload_to_media_library( $entry, $form ) {

		$this->log( sprintf( 'Checking for files to upload from entry (ID: %d).', $entry['id'] ) );

		$has_change = false;

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$value = $entry[ $field->id ];

			if ( $field->multipleFiles ) {
				$value = json_decode( $value );
			}

			$this->log( sprintf( 'Found field: %s (ID: %d); Value: %s', $field->label, $field->id, print_r( $value, true ) ) );

			if ( empty( $value ) ) {
				continue;
			}

			$has_change = true;
			$ids        = $this->upload_to_media_library( $value, $field, $entry );
			$new_value  = array();

			$this->log( sprintf( 'Uploaded media IDs: %s', print_r( $ids, true ) ) );

			if ( is_wp_error( $ids ) ) {
				continue;
			}

			for ( $i = count( $ids ) - 1; $i >= 0; $i -- ) {
				if ( is_wp_error( $ids[ $i ] ) ) {
					/* @var WP_Error $id */
					$data        = $ids[ $i ]->get_error_data( 'upload_error' );
					$new_value[] = $data['url'];
					// Don't want to save errors in our IDs list.
					unset( $ids[ $i ] );
					$this->log( sprintf( 'Error importing file; restoring original file URL: %s', print_r( $data['url'], true ) ) );
				} else {
					$new_value[] = wp_get_attachment_url( $ids[ $i ] );
				}
			}

			if ( $field->multipleFiles ) {
				$new_value = json_encode( $new_value );
			} else {
				$new_value = $new_value[0];
				$ids       = $ids[0];
			}

			$this->log( sprintf( 'New entry value: %s', print_r( $new_value, true ) ) );

			$entry[ $field->id ]                                 = $new_value;
			$entry[ $this->get_file_ids_meta_key( $field->id ) ] = $ids;

		}

		if ( $has_change ) {
			GFAPI::update_entry( $entry );
		}

		return $entry;
	}

	public function maybe_upload_to_media_library_after_update( $form, $entry_id ) {
		$entry = GFAPI::get_entry( $entry_id );
		$this->maybe_upload_to_media_library( $entry, $form );
	}

	public function upload_to_media_library( $urls, $field, $entry ) {

		if ( ! function_exists( 'media_handle_sideload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}

		if ( ! is_array( $urls ) ) {
			$urls = array( $urls );
		}

		$ids = array();

		foreach ( $urls as $url ) {

			// by default $url should be in GF upload directory, if $url is found in the WP directory, we'll assume
			// that we've already uploaded it and will just use the ID
			$id = $this->get_file_id_by_url( $url, $entry['id'], $field->id );

			if ( ! $id ) {

				// Ensure that we have a fall-back for formId. GPML AJAX Upload snippet doesn't pass a fully loaded object.
				$tmp        = $this->get_physical_file_path( $url, rgar( $entry, 'form_id', $field->formId ) );
				$file_array = array(
					'name'     => basename( $url ),
					'tmp_name' => $tmp
				);

				if ( is_wp_error( $tmp ) ) {
					return $tmp;
				}

				/**
				 * Filter the data that will be used to upload and generate the new media file.
				 *
				 * @param array $media_data {
				 *
				 * @param GF_Field $field The current field object for which the file is being uploaded.
				 * @param array $entry The current entry object.
				 *
				 * @var int $post_id The attachment ID to update or 0 to upload as a new attachment.
				 * @var string $desc The description of the file.
				 * @var array $post_data An array of data used to populate the generated attachment post (i.e. post_title, post_content, post_excerpt).
				 *
				 * }
				 * @since 1.0.11
				 *
				 * @var array $file_array The details of the actual file to be uploaded.
				 */
				$media_data = gf_apply_filters( array( 'gpml_media_data', $field->formId, $field->id ), array(
					'file_array' => $file_array,
					'post_id'    => 0,
					'desc'       => null,
					'post_data'  => array( 'post_meta' => array() )
				), $field, $entry );

				/**
				 * If no post ID is specified, make sure WordPress doesn't automatically set whatever post is set globally
				 * as the object to which the uploaded file is attached. This causes images to be uploaded to the
				 * time-based directory of that post (i.e. /2011/01/file.jpg).
				 */
				if ( empty( $media_data['post_id'] ) && isset( $GLOBALS['post'] ) ) {
					$_globals_post = $GLOBALS['post'];
					unset( $GLOBALS['post'] );
				}

				$id = media_handle_sideload( $media_data['file_array'], $media_data['post_id'], $media_data['desc'], $media_data['post_data'] );

				// Restore the WordPress post global if it was unset above.
				if ( isset( $_globals_post ) ) {
					$GLOBALS['post'] = $_globals_post;
				}

				// If file is not imported successfully, let's add some additional error data.
				if ( is_wp_error( $id ) ) {
					/** @var WP_Error $id */
					$id->add_data( array(
						'form_id'    => $entry['form_id'],
						'entry_id'   => $entry['id'],
						'media_data' => $media_data,
						'url'        => $url,
					), 'upload_error' );
				} // Otherwise, let's process per usual.
				else {

					$post_meta = rgars( $media_data, 'post_data/post_meta', array() );
					foreach ( $post_meta as $meta_key => $meta_value ) {
						update_post_meta( $id, $meta_key, $meta_value );
					}

				}

			}

			$ids[] = $id;

		}

		return $ids;
	}

	public function get_physical_file_path( $url, $form_id ) {

		$path = GFFormsModel::get_physical_file_path( $url );
		if ( strpos( $path, '/' ) === 0 ) {
			return $path;
		}

		// If the above fails, the path has likely been customized via the "gform_upload_path" filter. Let's try to get
		// that customized path and replace it in the provided URL.
		$form_id                 = absint( $form_id );
		$time                    = current_time( 'mysql' );
		$y                       = substr( $time, 0, 4 );
		$m                       = substr( $time, 5, 2 );
		$default_target_root     = GFFormsModel::get_upload_path( $form_id ) . "/$y/$m/";
		$default_target_root_url = GFFormsModel::get_upload_url( $form_id ) . "/$y/$m/";
		$upload_root_info        = gf_apply_filters( array(
			'gform_upload_path',
			$form_id
		), array( 'path' => $default_target_root, 'url' => $default_target_root_url ), $form_id );

		return str_replace( $upload_root_info['url'], $upload_root_info['path'], $url );
	}

	public function is_applicable_field( $field ) {
		return in_array( $field->get_input_type(), $this->get_supported_field_types() ) && $field->uploadMediaLibrary;
	}

	public function hijack_delete_file() {

		check_ajax_referer( 'rg_delete_file', 'rg_delete_file' );

		$entry_id   = intval( rgpost( 'lead_id' ) );
		$field_id   = intval( rgpost( 'field_id' ) );
		$file_index = intval( rgpost( 'file_index' ) );

		$file_id = $this->get_file_ids( $entry_id, $field_id, $file_index );

		if ( ! empty( $file_id ) ) {
			wp_delete_attachment( $file_id, true );
		}

	}

	public function maybe_delete_from_media_library( $entry_id ) {

		if ( $this->is_wcpa_submission() ) {
			return;
		}

		/**
		 * Filter whether files imported into the Media Library from a given entry should be deleted when the entry is deleted.
		 *
		 * @param bool $should_delete Whether the entry's Media Library files should be deleted. Defaults to `true`.
		 * @param int $entry_id The ID of the entry that has been deleted.
		 *
		 * @since 1.2.7
		 *
		 */
		$should_delete = apply_filters( 'gpml_delete_entry_files_from_media_library', true, $entry_id );
		if ( ! $should_delete ) {
			return;
		}

		$entry = GFAPI::get_entry( $entry_id );
		$form  = GFAPI::get_form( $entry['form_id'] );

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_applicable_field( $field ) ) {
				$this->delete_files_from_media_library( $entry_id, $field->id );
			}
		}

	}

	public function delete_files_from_media_library( $entry_id, $field_id ) {

		$file_ids = $this->get_file_ids( $entry_id, $field_id );

		if ( empty( $file_ids ) ) {
			return;
		}

		if ( ! is_array( $file_ids ) ) {
			$file_ids = array( $file_ids );
		}

		foreach ( $file_ids as $file_id ) {
			wp_delete_attachment( $file_id, true );
		}

	}

	public function add_image_merge_tags( $form ) {

		// if the header has already been generated, wait until the footer to output our <script>
		if ( ! did_action( 'admin_head' ) ) {
			add_action( 'admin_footer', array( $this, 'add_image_merge_tags_footer' ) );

			return $form;
		}

		?>

		<script type="text/javascript">

			(function ($) {

				var imageMergeTags = <?php echo json_encode( $this->get_image_merge_tags( $form ) ); ?>;

				if (window.gform) {

					gform.addFilter('gform_merge_tags', function (mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option) {
						mergeTags['gpml'] = {label: '<?php _e( 'GP Media Library', 'gp-media-library' ); ?>', tags: []};
						for (var i = 0; i < imageMergeTags.length; i++) {
							mergeTags['gpml'].tags.push({
								tag: imageMergeTags[i].tag,
								label: imageMergeTags[i].label
							});
						}
						return mergeTags;
					});

				}

			})(jQuery);

		</script>

		<?php
		//return the form object from the php hook
		return $form;
	}

	public function add_image_merge_tags_footer() {
		$form = GFAPI::get_form( rgget( 'id' ) );
		if ( $form ) {
			$this->add_image_merge_tags( $form );
		}
	}

	public function get_image_merge_tags( $form ) {

		$merge_tags = array();

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$standard = array( 'thumbnail', 'medium', 'medium_large', 'large' );
			$sizes    = array_diff( get_intermediate_image_sizes(), $standard );
			$sizes    = array_slice( $sizes, 0, 4 );
			$sizes    = array_merge( $standard, $sizes );

			$sizes = gf_apply_filters( array( 'gpml_merge_tag_image_sizes', $form['id'], $field->id ), $sizes );

			foreach ( $sizes as $size ) {
				$merge_tags[] = array(
					'tag'   => sprintf( '{%s:%s:%s}', $field->get_field_label( $form, null ), $field->id, $size ),
					'label' => sprintf( '%s (%s)', $field->get_field_label( $form, null ), $size ),
				);
			}

		}

		return $merge_tags;
	}

	public function replace_image_merge_tags( $text, $form, $entry ) {

		if ( strpos( $text, '{' ) === false || ! rgar( $entry, 'id' ) ) {
			return $text;
		}

		$image_ids = $this->get_file_ids_by_entry( $entry, $form );

		// Add support for mulit-file merge tags for GFCommon::replace_variables_post_image() by replacing the same
		// merge tag for each image submitted to that field.
		// Note: this regular expression has been modified from the GF version to support capture an unlimited number
		// of modifiers.
		preg_match_all( '/{[^{]*?:(\d+)((?::[^:}]+)*)}/mi', $text, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {

			$search    = $match[0];
			$input_id  = $match[1];
			$modifiers = array_filter( explode( ':', $match[2] ) );
			$replace   = array();

			if ( ! isset( $image_ids[ $input_id ] ) || empty( $modifiers ) ) {
				continue;
			}

			foreach ( $image_ids[ $input_id ] as $image_id ) {
				$linkless_search = str_replace( ':link', '', $search );
				$_replace        = GFCommon::replace_variables_post_image( $linkless_search, array( $input_id => $image_id ), $entry );
				if ( in_array( 'link', $modifiers ) ) {
					$full_size = wp_get_attachment_image_src( $image_id, 'full' );
					/**
					 * Filter the attributes (and content) used to generate the merge tag link.
					 *
					 * @param array $link_atts {
					 *
					 *     Any array of attributes that will be used to generate the link.
					 *
					 * @since 1.0.4
					 *
					 * @var string $content The content that will be wrapped by the link.
					 * @var string $class The CSS class that will be assigned to the link.
					 * @var string $href The URL that the link will target.
					 *
					 * }
					 */
					$link_atts = gf_apply_filters( array( 'gpml_image_merge_tag_link_atts', $form['id'] ), array(
						'content' => $_replace,
						'class'   => 'thickbox',
						'href'    => $full_size[0]
					) );
					$_replace  = sprintf( '<a href="%s" class="%s">%s</a>', $link_atts['href'], $link_atts['class'], $link_atts['content'] );
				}
				$replace[] = $_replace;
			}

			/**
			 * Specify how individual images should be joined for multi-file merge tags
			 *
			 * @param string $glue The string that should be used to join individual image strings.
			 * @param array $merge_tag An array of properties for the matched merge tag.
			 * @param array $form The current form object.
			 * @param array $field_id The ID of the current field.
			 *
			 * @since 1.0.8
			 *
			 */
			$glue = gf_apply_filters( array(
				'gpml_multi_file_merge_tag_glue',
				$form['id'],
				$input_id
			), "\n", $match, $form, $input_id );
			$text = str_replace( $search, implode( $glue, $replace ), $text );

		}

		return $text;
	}

	public function get_file_ids_by_entry( $entry, $form ) {

		$_file_ids = array();

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$file_ids = $this->get_file_ids( $entry['id'], $field->id );
			if ( empty( $file_ids ) ) {
				continue;
			}

			if ( ! is_array( $file_ids ) ) {
				$file_ids = array( $file_ids );
			}

			$_file_ids[ $field->id ] = $file_ids;

		}

		return $_file_ids;
	}

	/**
	 * Gravity Forms Notifications support auto-attaching submitted files. We have to intercept the notification and
	 * replace the file URLs with file paths so they are correctly attached.
	 *
	 * @param $email
	 * @param $message_format
	 * @param $notification
	 * @param $entry
	 *
	 * @return mixed
	 */
	public function handle_attachments( $email, $message_format, $notification, $entry ) {

		if ( rgar( $notification, 'enableAttachments' ) ) {

			$form = GFAPI::get_form( $entry['form_id'] );
			$file_ids = $this->get_file_ids_by_entry( $entry, $form );
			$ids  = ( count( $file_ids ) > 0 ) ? call_user_func_array( 'array_merge', $file_ids ) : array();

			foreach ( $ids as $id ) {
				$url = wp_get_attachment_url( $id );
				foreach ( $email['attachments'] as &$attachment ) {
					if ( $attachment == $url ) {
						$attachment = get_attached_file( $id );
					}
				}
			}

		}

		return $email;
	}


	## ADVANCED CUSTOM FIELDS ##

	public function acf_integration( $post_id, $entry, $form ) {

		// is ACF PRO
		if ( is_callable( 'acf_get_field' ) ) {
			$this->acf_pro_update_fields( $post_id, $entry, $form );
		} // is ACF
		else if ( is_callable( 'get_field_object' ) ) {
			$this->acf_update_fields( $post_id, $entry, $form );
		}

	}

	public function acf_pro_update_fields( $post_id, $entry, $form ) {

		foreach ( $form['fields'] as $field ) {

			if ( $field->type != 'post_custom_field' || ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$this->acf_update_field( $post_id, $field->postCustomFieldName, $field, $entry );

		}

	}

	public function acf_update_fields( $post_id, $entry, $form ) {

		$groups = null;

		foreach ( $form['fields'] as $field ) {

			if ( $field->type != 'post_custom_field' || ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			// only need to get the groups once
			if ( $groups === null ) {
				$groups = apply_filters( 'acf/get_field_groups', array() );
			}

			foreach ( $groups as $group ) {

				$acf_fields = apply_filters( 'acf/field_group/get_fields', array(), $group['id'] );

				foreach ( $acf_fields as $acf_field ) {

					if ( $acf_field['name'] != $field->postCustomFieldName || ! in_array( $acf_field['type'], array(
							'image',
							'file'
						) ) ) {
						continue;
					}

					$value = $this->acf_get_field_value( $acf_field['save_format'], $entry, $field );
					if ( ! $value ) {
						continue;
					}

					update_field( $acf_field['key'], $value, $post_id );

				}
			}

		}

	}

	public function apc_acf_integration( $post_id, $feed, $entry, $form ) {

		$mappings = rgars( $feed, 'meta/postMetaFields', array() );

		// It appears that non-PRO ACF now comes with the acf_get_field() method so we don't need to distinguish as we do in acf_integration() above.
		$this->apc_acf_pro_update_fields( $post_id, $entry, $form, $mappings );

	}

	/**
	 * Handle mapping GPML-enabled file upload fields to their corresponding ACF fields when mapped via the Advanced
	 * Post Creation add-on.
	 *
	 * @param $post_id
	 * @param $entry
	 * @param $form
	 * @param $mappings
	 *
	 * @return bool
	 */
	public function apc_acf_pro_update_fields( $post_id, $entry, $form, $mappings ) {

		if ( ! is_callable( 'acf_get_field' ) ) {
			return false;
		}

		foreach ( $mappings as $mapping ) {

			$field = GFAPI::get_field( $form, $mapping['value'] );
			if ( ! $field || ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$custom_field = $mapping['key'] == 'gf_custom' ? $mapping['custom_key'] : $mapping['key'];

			$this->acf_update_field( $post_id, $custom_field, $field, $entry );

		}

	}

	public function acf_get_field_value( $format, $entry, $gf_field, $is_multi = false ) {

		$value = rgar( $entry, $gf_field->id );
		if ( empty( $value ) ) {
			return false;
		}

		switch ( $format ) {
			case 'url':
				if ( $gf_field->multipleFiles ) {
					$urls  = json_decode( $value );
					$value = $urls[0];
				}
				break;
			case 'object':
			case 'id':
				$value = $this->get_file_ids( $entry['id'], $gf_field->id, $is_multi ? false : 0 );
				break;
		}

		return $value;
	}

	public function acf_update_field( $post_id, $acf_field_name, $gf_field, $entry ) {

		$acf_field = acf_get_field( $acf_field_name );
		if ( ! in_array( $acf_field['type'], array( 'image', 'file', 'gallery' ) ) ) {
			return;
		}

		$value = $this->acf_get_field_value( 'id', $entry, $gf_field, $acf_field['type'] == 'gallery' );
		if ( ! $value ) {
			return;
		}

		update_field( $acf_field['key'], $value, $post_id );

	}


	## ADVANCED POST CREATION

	public function apc_custom_field_integration( $post_id, $feed, $entry, $form ) {

		$auto_custom_fields = array(
			'_product_image_gallery' /* WooCommerce product gallery */
		);

		/**
		 * Filter which custom fields GP Media Library will attempt to convert to use image IDs.
		 *
		 * @param array $auto_custom_fields A list of custom field keys that should use image IDs.
		 * @param int $post_id ID of the post for which custom fields are being processed.
		 * @param array $entry The current entry ID.
		 * @param array $form The current form.
		 * @param array $feed The current APC feed.
		 *
		 * @since 1.2.8
		 *
		 */
		$auto_custom_fields = gf_apply_filters( array(
			'gpml_auto_convert_custom_fields',
			$form['id']
		), $auto_custom_fields, $post_id, $entry, $form, $feed );

		$mappings = rgars( $feed, 'meta/postMetaFields', array() );

		foreach ( $mappings as $mapping ) {

			$key = $mapping['key'] == 'gf_custom' ? $mapping['custom_key'] : $mapping['key'];
			if ( ! in_array( $key, $auto_custom_fields ) ) {
				continue;
			}

			$field = GFAPI::get_field( $form, $mapping['value'] );
			if ( ! $field || ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$value = $this->acf_get_field_value( 'id', $entry, $field, true );
			if ( ! $value ) {
				continue;
			}

			if ( is_array( $value ) ) {
				$value = implode( ',', $value );
			}

			update_post_meta( $post_id, $key, $value );

		}

	}


	## GRAVITY VIEW ##

	/**
	 * Automatically replace GPML-enabled fields with the medium-sized image (rather than using the default large image).
	 * The GV link will continue to target the full-sized image.
	 *
	 * @param $content
	 * @param $gv_field
	 *
	 * @return string
	 */
	public function gravityview_file_upload_content( $content, $gv_field ) {

		if ( ! $this->is_applicable_field( $gv_field['field'] ) ) {
			return $content;
		}

		$file_ids = $this->get_file_ids( $gv_field['entry']['id'], $gv_field['field']->id );
		if ( ! is_array( $file_ids ) ) {
			$file_ids = array( $file_ids );
		}

		foreach ( $file_ids as $file_id ) {
			$src = wp_get_attachment_url( $file_id );
			if ( strpos( $content, $src ) !== false ) {
				$thumbnail = wp_get_attachment_image_src( $file_id, 'medium' );
				$image     = new GravityView_Image( array(
					'src'   => $thumbnail[0],
					'class' => 'gv-image gv-field-id-' . $gv_field['field_settings']['id'],
					'alt'   => $gv_field['field_settings']['label'],
					'width' => ( gravityview_get_context() === 'single' ? null : 250 )
				) );
				$content   = $image->html();
			}
		}

		return $content;
	}


	## HELPERS ##

	public function get_file_ids( $entry_id, $field_id, $file_index = false ) {

		$ids = gform_get_meta( $entry_id, $this->get_file_ids_meta_key( $field_id ) );

		if ( $file_index !== false && is_array( $ids ) ) {
			$ids = rgar( $ids, $file_index );
		}

		return $ids;
	}

	public function update_file_ids( $entry_id, $field_id, $file_ids ) {
		gform_update_meta( $entry_id, $this->get_file_ids_meta_key( $field_id ), $file_ids );
	}

	public function delete_file_id( $entry_id, $field_id, $file_id ) {

	}

	public function get_file_id_by_url( $url, $entry_id, $field_id ) {

		$file_id = attachment_url_to_postid( $url );
		if ( $file_id ) {
			return $file_id;
		}

		// The attachment URL which is saved to the entry might not always match the guid (i.e. S3 Offload Lite); let's
		// check if any of the existing file IDs have a matching attachment URL.
		$file_ids = (array) $this->get_file_ids( $entry_id, $field_id );
		foreach ( $file_ids as $file_id ) {
			if ( wp_get_attachment_url( $file_id ) === $url ) {
				return $file_id;
			}
		}

		return false;
	}

	public function get_file_ids_meta_key( $field_id ) {
		return sprintf( 'gpml_ids_%d', $field_id );
	}

	public function is_wcpa_submission() {
		return (bool) rgpost( 'wc_gforms_form_id' ) === true;
	}

	public function add_logging_support( $plugins ) {
		$plugins['gp-media-library'] = __( 'GP Media Library' );

		return $plugins;
	}

	public function log_debug( $message ) {
		if ( class_exists( 'GFLogging' ) ) {
			GFLogging::include_logger();
			GFLogging::log_message( 'gp-media-library', $message, KLogger::DEBUG );
		}
	}

	public function log( $message ) {
		$this->log_debug( sprintf( '%s - %s', debug_backtrace()[1]['function'], $message ) );
	}


}

function gp_media_library() {
	return GP_Media_Library::get_instance( null );
}
