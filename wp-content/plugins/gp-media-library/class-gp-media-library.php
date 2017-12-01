<?php

class GP_Media_Library extends GWPerk {

	public $version = GP_MEDIA_LIBRARY_VERSION;
	public $min_gravity_perks_version = '1.2.12';
	public $min_gravity_forms_version = '1.9.18';
	public $min_wp_version = '4.4';
	public $prefix = 'gpMediaLibrary';
	public $post_type = 'gplp';
	public $preview_post = null;

	private static $instance = null;

	public static function get_instance( $perk_file ) {

		if( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}

		return self::$instance;
	}

	public function init() {

		parent::init();

		load_plugin_textdomain( 'gp-media-library', false, basename( dirname( __file__ ) ) . '/languages/' );

		$this->enqueue_field_settings();

		add_filter( 'gform_tooltips', array( $this, 'add_tooltips' ) );

		// functionality
		add_action( 'gform_entry_post_save',        array( $this, 'maybe_upload_to_media_library' ), 10, 2 );
		add_action( 'gform_after_update_entry',     array( $this, 'maybe_upload_to_media_library_after_update' ), 10, 2 );
		add_action( 'wp_ajax_rg_delete_file',       array( $this, 'hijack_delete_file' ), 9 );
		add_action( 'gform_delete_lead',            array( $this, 'maybe_delete_from_media_library' ) );

		add_action( 'gform_after_create_post',                    array( $this, 'acf_integration' ), 10, 3 );
		add_action( 'gravityview/fields/fileupload/link_content', array( $this, 'gravityview_file_upload_content' ), 10, 2 );

		add_filter( 'gform_admin_pre_render',       array( $this, 'add_image_merge_tags' ) );
		add_action( 'gform_pre_replace_merge_tags', array( $this, 'replace_image_merge_tags' ), 5, 7 );

	}



	## SETTINGS ##

	public function field_settings_ui( $position ) {
		?>

        <li class="gpml-field-setting field_setting" style="display:none;">
            <input type="checkbox" value="1" id="gpml-enable" onchange="SetFieldProperty( 'uploadMediaLibrary', this.checked );" />
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
            ( function( $ ) {

                $( document ).ready( function(){
                    for( fieldType in fieldSettings ) {
                        if( fieldSettings.hasOwnProperty( fieldType ) && fieldType == 'fileupload' ) {
                            fieldSettings[ fieldType ] += ', .gpml-field-setting';
                        }
                    }
                } );

                $( document ).bind( 'gform_load_field_settings', function( event, field, form ) {
                    $( '#gpml-enable' ).prop( 'checked', field['uploadMediaLibrary'] == true );
                } );

            } )( jQuery );
        </script>

		<?php
	}

	public function add_tooltips( $tooltips ) {
		$tooltips[ 'gpml_enable' ] = sprintf( '<h6>%s</h6> %s', __( 'Upload to Media Library' ), __( 'Upload files from this field to the WordPress Media Library.' ) );
		return $tooltips;
	}




	## FUNCTIONALITY ##

	public function maybe_upload_to_media_library( $entry, $form ) {

		$has_change = false;

		foreach( $form['fields'] as $field ) {

			if( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$value = $entry[ $field->id ];

			if( $field->multipleFiles ) {
				$value = json_decode( $value );
			}

			if( empty( $value ) ) {
				continue;
			}

			$has_change = true;
			$ids        = $this->upload_to_media_library( $value, $field, $entry );
			$new_value  = array();

			if( is_wp_error( $ids ) ) {
			    continue;
            }

			foreach( $ids as $id ) {
				if( ! is_wp_error( $id ) ) {
					$new_value[] = wp_get_attachment_url( $id );
				}
			}

			if( $field->multipleFiles ) {
				$new_value = json_encode( $new_value );
			} else {
				$new_value = $new_value[0];
				$ids       = $ids[0];
			}

			$entry[ $field->id ] = $new_value;

			$this->update_file_ids( $entry['id'], $field->id, $ids );

		}

		if( $has_change ) {
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

		if( ! is_array( $urls ) ) {
			$urls = array( $urls );
		}

		$ids = array();

		foreach( $urls as $url ) {

			// by default $url should be in GF upload directory, if $url is found in the WP directory, we'll assume
			// that we've already uploaded it and will just use the ID
			$id = $this->get_file_id_by_url( $url, $entry['id'], $field->id );

			if( ! $id ) {

			    $tmp = GFFormsModel::get_physical_file_path( $url );
				$file_array = array(
					'name' => basename( $url ),
					'tmp_name' => $tmp
				);

				if ( is_wp_error( $tmp ) ) {
					return $tmp;
				}

				$id = media_handle_sideload( $file_array, 0 );
				if ( is_wp_error( $id ) ) {
					return $id;
				}


                if( ! $this->is_wcpa_submission() )  {
	                // remove the original image
	                @unlink( $file_array[ 'tmp_name' ] );
                }

			}

			$ids[] = $id;

		}

		return $ids;
	}

	public function is_applicable_field( $field ) {
		return $field->get_input_type() == 'fileupload' && $field->uploadMediaLibrary;
	}

	public function hijack_delete_file() {

		check_ajax_referer( 'rg_delete_file', 'rg_delete_file' );

		$entry_id   = intval( rgpost( 'lead_id' ) );
		$field_id   = intval( rgpost( 'field_id' ) );
		$file_index = intval( rgpost( 'file_index' ) );

		$file_id = $this->get_file_ids( $entry_id, $field_id, $file_index );

		if( ! empty( $file_id ) ) {
			wp_delete_attachment( $file_id, true );
		}

	}

	public function maybe_delete_from_media_library( $entry_id ) {

	    if( $this->is_wcpa_submission() ) {
	        return;
        }

		$entry = GFAPI::get_entry( $entry_id );
		$form  = GFAPI::get_form( $entry['form_id'] );

		foreach( $form['fields'] as $field ) {
			if( $this->is_applicable_field( $field ) ) {
				$this->delete_files_from_media_library( $entry_id, $field->id );
			}
		}

	}

	public function delete_files_from_media_library( $entry_id, $field_id ) {

		$file_ids = $this->get_file_ids( $entry_id, $field_id );

		if( empty( $file_ids ) ) {
			return;
		}

		if( ! is_array( $file_ids ) ) {
			$file_ids = array( $file_ids );
		}

		foreach( $file_ids as $file_id ) {
			wp_delete_attachment( $file_id, true );
		}

	}

	public function add_image_merge_tags( $form ) {

		// if the header has already been generated, wait until the footer to output our <script>
		if( ! did_action( 'admin_head' ) ) {
			add_action( 'admin_footer', array( $this, 'add_image_merge_tags_footer' ) );
			return $form;
		}

		?>

        <script type="text/javascript">

            ( function( $ ) {

            	var imageMergeTags = <?php echo json_encode( $this->get_image_merge_tags( $form ) ); ?>;

	            if( window.gform ) {

		            gform.addFilter( 'gform_merge_tags', function( mergeTags, elementId, hideAllFields, excludeFieldTypes, isPrepop, option ) {
			            mergeTags['gpml'] = { label: '<?php _e( 'GP Media Library', 'gp-media-library' ); ?>', tags: [] };
			            for( var i = 0; i < imageMergeTags.length; i++ ) {
				            mergeTags['gpml'].tags.push( {
				            	tag:   imageMergeTags[i].tag,
                                label: imageMergeTags[i].label
				            } );
			            }
			            return mergeTags;
		            } );

	            }

            } )( jQuery );

        </script>

		<?php
		//return the form object from the php hook
		return $form;
	}

	public function add_image_merge_tags_footer() {
		$form = GFAPI::get_form( rgget( 'id' ) );
		if( $form ) {
			$this->add_image_merge_tags( $form );
		}
	}

	public function get_image_merge_tags( $form ) {

	    $merge_tags = array();

	    foreach( $form['fields'] as $field ) {

	        if ( ! $this->is_applicable_field( $field ) ) {
	            continue;
	        }

	        $standard = array( 'thumbnail', 'medium', 'medium_large', 'large' );
	        $sizes    = array_diff( get_intermediate_image_sizes(), $standard );
            $sizes    = array_slice( $sizes, 0, 4 );
	        $sizes    = array_merge( $standard, $sizes );

		    $sizes = gf_apply_filters( array( 'gpml_merge_tag_image_sizes', $form['id'], $field->id ), $sizes );

	        foreach( $sizes as $size ) {
		        $merge_tags[] = array(
			        'tag'   => sprintf( '{%s:%s:%s}', $field->get_field_label( $form, null ), $field->id, $size ),
			        'label' => sprintf( '%s (%s)', $field->get_field_label( $form, null ), $size ),
		        );
	        }

	    }

	    return $merge_tags;
	}

	public function replace_image_merge_tags( $text, $form, $entry ) {

		if( strpos( $text, '{' ) === false || empty( $entry ) ) {
			return $text;
		}

		$image_ids = $this->get_file_ids_by_entry( $entry, $form );

		// Add support for mulit-file merge tags for GFCommon::replace_variables_post_image() by replacing the same
		// merge tag for each image submitted to that field.
		// Note: this regular expression has been modified from the GF version to support capture an unlimited number
		// of modifiers.
		preg_match_all( '/{[^{]*?:(\d+)((?::\w+)*)}/mi', $text, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {

			$search    = $match[0];
			$input_id  = $match[1];
			$modifiers = array_filter( explode( ':', $match[2] ) );
			$replace   = array();

			if ( ! isset( $image_ids[ $input_id ] ) || empty( $modifiers ) ) {
				continue;
			}

			foreach( $image_ids[ $input_id ] as $image_id ) {
				$linkless_search = str_replace( ':link', '', $search );
				$_replace  = GFCommon::replace_variables_post_image( $linkless_search, array( $input_id => $image_id ), $entry );
				if( in_array( 'link', $modifiers ) ) {
					$full_size = wp_get_attachment_image_src( $image_id, 'full' );
					/**
					 * Filter the attributes (and content) used to generate the merge tag link.
					 *
					 * @since 1.0.4
					 *
					 * @param array $link_atts {
					 *
					 *     Any array of attributes that will be used to generate the link.
					 *
					 *     @var string $content The content that will be wrapped by the link.
					 *     @var string $class   The CSS class that will be assigned to the link.
					 *     @var string $href    The URL that the link will target.
					 *
					 * }
					 */
					$link_atts = gf_apply_filters( array( 'gpml_image_merge_tag_link_atts', $form['id'] ), array(
						'content' => $_replace,
						'class'   => 'thickbox',
						'href'    => $full_size[0]
					) );
					$_replace = sprintf( '<a href="%s" class="%s">%s</a>', $link_atts['href'], $link_atts['class'], $link_atts['content'] );
				}
				$replace[] = $_replace;
			}

			/**
			 * Specify how individual images should be joined for multi-file merge tags
			 *
			 * @since 1.0.8
			 *
			 * @param string $glue      The string that should be used to join individual image strings.
			 * @param array  $merge_tag An array of properties for the matched merge tag.
			 * @param array  $form      The current form object.
			 * @param array  $field_id  The ID of the current field.
			 */
			$glue = gf_apply_filters( array( 'gpml_multi_file_merge_tag_glue', $form['id'], $input_id ), "\n", $match, $form, $input_id );
			$text = str_replace( $search, implode( $glue, $replace ), $text );

		}

		return $text;
	}

    public function get_file_ids_by_entry( $entry, $form ) {

	    $_file_ids = array();

	    foreach( $form['fields'] as $field ) {

		    if( ! $this->is_applicable_field( $field ) ) {
			    continue;
		    }

		    $file_ids = $this->get_file_ids( $entry['id'], $field->id );
		    if( empty( $file_ids ) ) {
			    continue;
		    }

		    if( ! is_array( $file_ids ) ) {
			    $file_ids = array( $file_ids );
		    }

            $_file_ids[ $field->id ] = $file_ids;

	    }

	    return $_file_ids;
    }



	## ADVANCED CUSTOM FIELDS ##

	public function acf_integration( $post_id, $entry, $form ) {

		// is ACF PRO
		if( is_callable( 'acf_get_field' ) ) {
			$this->acf_pro_update_fields( $post_id, $entry, $form );
		}
		// is ACF
		else if( is_callable( 'get_field_object' ) ) {
			$this->acf_update_fields( $post_id, $entry, $form );
		}

	}

	public function acf_pro_update_fields( $post_id, $entry, $form ) {

		foreach ( $form['fields'] as $field ) {

			if( $field->type != 'post_custom_field' || ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$acf_field = acf_get_field( $field->postCustomFieldName );
			if( ! in_array( $acf_field['type'], array( 'image', 'file', 'gallery' ) ) ) {
				continue;
			}

			// ACF PRO always saves value as ID (or as serialized array for gallery)
			$value = $this->acf_get_field_value( 'id', $entry, $field, $acf_field['type'] == 'gallery' );
			if( ! $value ) {
				continue;
			}

			update_field( $acf_field['key'], $value, $post_id );

		}

	}

	public function acf_update_fields( $post_id, $entry, $form ) {

		$groups = null;

		foreach ( $form['fields'] as $field ) {

			if( $field->type != 'post_custom_field' || ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			// only need to get the groups once
			if( $groups === null ) {
				$groups = apply_filters( 'acf/get_field_groups', array() );
			}

			foreach( $groups as $group ) {

				$acf_fields = apply_filters( 'acf/field_group/get_fields', array(), $group['id'] );

				foreach( $acf_fields as $acf_field ) {

					if( $acf_field['name'] != $field->postCustomFieldName || ! in_array( $acf_field['type'], array( 'image', 'file' ) ) ) {
						continue;
					}

					$value = $this->acf_get_field_value( $acf_field['save_format'], $entry, $field );
					if( ! $value ) {
						continue;
					}

					update_field( $acf_field['key'], $value, $post_id );

				}
			}

		}

	}

	public function acf_get_field_value( $format, $entry, $gf_field, $is_multi = false ) {

		$value = rgar( $entry, $gf_field->id );
		if( empty( $value ) ) {
			return false;
		}

		switch( $format ) {
			case 'url':
				if( $gf_field->multipleFiles ) {
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

	    if( ! $this->is_applicable_field( $gv_field['field'] ) ) {
	        return $content;
        }

        $file_ids = $this->get_file_ids( $gv_field['entry']['id'], $gv_field['field']->id );
	    if( ! is_array( $file_ids ) ) {
		    $file_ids = array( $file_ids );
        }

        foreach( $file_ids as $file_id ) {
	        $src = wp_get_attachment_url( $file_id );
	        if( strpos( $content, $src ) !== false ) {
	            $thumbnail = wp_get_attachment_image_src( $file_id, 'medium' );
		        $image = new GravityView_Image(array(
			        'src'   => $thumbnail[0],
			        'class' => 'gv-image gv-field-id-' . $gv_field['field_settings']['id'],
			        'alt'   => $gv_field['field_settings']['label'],
			        'width' => (gravityview_get_context() === 'single' ? NULL : 250)
		        ));
		        $content = $image->html();
            }
        }

        return $content;
    }



	## HELPERS ##

	public function get_file_ids( $entry_id, $field_id, $file_index = false ) {

		$ids = gform_get_meta( $entry_id, sprintf( 'gpml_ids_%d', $field_id ) );

		if( $file_index !== false && is_array( $ids ) ) {
			$ids = rgar( $ids, $file_index );
		}

		return $ids;
	}

	public function update_file_ids( $entry_id, $field_id, $file_ids ) {
		gform_update_meta( $entry_id, sprintf( 'gpml_ids_%d', $field_id ), $file_ids );
	}

	public function delete_file_id( $entry_id, $field_id, $file_id ) {

	}

	public function get_file_id_by_url( $url, $entry_id, $field_id ) {
		global $wpdb;

		$file_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid = %s;", $url ) );
		if( $file_id ) {
		    return $file_id;
        }

        // the attachment URL which is saved to the entry might not always match the guid (i.e. S3 Offload Lite); let's
        // check if any of the existing file IDs have a matching attachment URL.
        $file_ids = (array) $this->get_file_ids( $entry_id, $field_id );
		foreach( $file_ids as $file_id ) {
            if( wp_get_attachment_url( $file_id ) == $url ) {
                return $file_id;
            }
        }

		return false;
	}
	
	public function is_wcpa_submission() {
        return rgpost( 'wc_gforms_form_id' ) == true;
    }

	public function documentation() {
		return array(
			'type' => 'url',
			'value' => 'http://gravitywiz.com/documentation/gp-media-preview-for-gravity-forms/'
		);
	}

}

function gp_media_library() {
	return GP_Media_Library::get_instance( null );
}