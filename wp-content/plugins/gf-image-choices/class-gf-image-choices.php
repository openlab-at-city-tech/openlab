<?php

//------------------------------------------

GFForms::include_addon_framework();

class GFImageChoices extends GFAddOn {

	protected $_version = GFIC_VERSION;
	protected $_min_gravityforms_version = '2.0';

	protected $_slug = GFIC_SLUG;
	protected $_path = 'gf-image-choices/gf-image-choices.php';
	protected $_full_path = __FILE__;
	protected $_title = GFIC_NAME;
	protected $_short_title = 'Image Choices';
	protected $_url = 'https://jetsloth.com/gravity-forms-image-choices/';

	protected $_supported_field_types = ['radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option'];
	protected $_supported_input_types = ['radio', 'checkbox'];
	protected $_standard_merge_tags = ['all_fields', 'pricing_fields'];//'all_quiz_results'

	/**
	 * Members plugin integration
	 */
	protected $_capabilities = array( 'gravityforms_edit_forms', 'gravityforms_edit_settings' );

	/**
	 * Permissions
	 */
	protected $_capabilities_settings_page = 'gravityforms_edit_settings';
	protected $_capabilities_form_settings = 'gravityforms_edit_forms';
	protected $_capabilities_uninstall = 'gravityforms_uninstall';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFImageChoices
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFImageChoices();
		}

		return self::$_instance;
	}

	private function __clone() {
	} /* do nothing */

	/**
	 * Handles anything which requires early initialization.
	 */
	public function pre_init() {
		parent::pre_init();
	}

	public function add_inline_options_label_lookup( $form_string, $form, $current_page ) {
		$option_labels_lookup = [];
		foreach( $form['fields'] as $field ) {
		    if ( !is_object($field) ) {
		        continue;
            }
		    if ( property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages && $field->type == "option" && ( $field->get_input_type() == "radio" || $field->get_input_type() == "checkbox" ) ) {
			    $key = "field_" . $field->id;
			    if ( !isset($option_labels_lookup[$key]) ) {
				    $option_labels_lookup[$key] = [];
                }
		        foreach( $field->choices as $i => $choice ) {
			        $option_labels_lookup[$key][] = $choice['text'];
                }
            }
		}
		$form_string .= "<script type=\"text/javascript\"> window.imageChoicesOptionLabels = window.imageChoicesOptionLabels || {}; window.imageChoicesOptionLabels[".$form['id']."] = " . json_encode($option_labels_lookup) . ";  </script>";

		return $form_string;
    }

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {

		// add a special class to relevant fields so we can identify them later
		add_action( 'gform_field_css_class', array( $this, 'add_custom_class' ), 10, 3 );
		add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'add_image_options_markup' ), 10, 4 );

		add_filter( 'gform_footer_init_scripts_filter', array( $this, 'add_inline_options_label_lookup' ), 10, 3 );

		// display on entry detail
		add_filter( 'gform_entry_field_value', array( $this, 'custom_entry_field_value' ), 20, 4 );
		add_filter( 'gform_order_summary', array( $this, 'custom_order_summary_entry_field_value' ), 10, 4 );

		// display in notifications
		add_filter( 'gform_merge_tag_filter', array( $this, 'custom_notification_merge_tag' ), 11, 5 );
		//add_filter( 'gform_replace_merge_tags', array( $this, 'custom_replace_merge_tags' ), 10, 7 );
		add_filter( 'gform_replace_merge_tags', array( $this, 'render_quiz_results_merge_tag' ), 100, 7 );

		add_action( 'gform_noconflict_scripts', array( $this, 'register_our_noconflict_scripts' ), 10, 1 );

		// inline css overrides. Run as late as possible
		add_action( 'gform_enqueue_scripts', array( $this, 'frontend_inline_styles' ), PHP_INT_MAX, 1 );

		// Prep for new styles options coming soon (similar to Color Picker)
		update_option('gfic_legacy_styles', 1);

		parent::init();

	}


	/**
	 * Initialize the admin specific hooks.
	 */
	public function init_admin() {

		// form editor
		add_action( 'gform_field_standard_settings', array( $this, 'image_choice_field_settings' ), 10, 2 );
		if ( GFIC_GF_MIN_2_5 ) {
			add_filter( 'gform_field_settings_tabs', array( $this, 'custom_settings_tab' ), 10, 1 );
			add_action( 'gform_field_settings_tab_content_image_choices', array( $this, 'custom_settings_markup' ), 10, 1 );
		}
		else {
			add_action( 'gform_field_appearance_settings', array( $this, 'image_choices_field_appearance_settings' ), 300, 2 );
		}

		add_filter( 'gform_tooltips', array( $this, 'add_image_choice_field_tooltips' ) );

		// display results on entry list
		add_filter( 'gform_entries_field_value', array( $this, 'entries_table_field_value' ), 10, 4 );

		$name = plugin_basename($this->_path);
		add_action( 'after_plugin_row_'.$name, array( $this, 'gf_plugin_row' ), 10, 2 );


		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		parent::init_admin();

	}

	public function admin_enqueue_scripts() {
		if ( $this->is_form_editor() ) {
			wp_enqueue_media();// For Media Library
		}
	}


	public function get_app_menu_icon() {
		return 'dashicons-images-alt2';
	}

	public function get_menu_icon() {
		return 'dashicons-images-alt2';
	}

	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	public function register_our_noconflict_scripts( $scripts ) {
		$scripts[] = 'media-audiovideo';
		return $scripts;
	}

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$gf_image_choices_js_deps = array( 'jquery', 'jquery-ui-sortable', 'jetsloth_filters_actions', 'jetsloth_lightbox' );
		if ( wp_is_mobile() ) {
			$gf_image_choices_js_deps[] = 'jquery-touch-punch';
		}

		$scripts = array(
			array(
				'handle'   => 'gf_image_choices_form_editor_js',
				'src'      => $this->get_base_url() . '/js/gf_image_choices_form_editor.js',
				'version'  => $this->_version,
				'deps'     => array( 'jquery' ),
				'callback' => array( $this, 'localize_admin_scripts' ),
				'enqueue'  => array(
					array( 'admin_page' => array( 'form_editor', 'plugin_settings', 'form_settings' ) ),
				),
			),
			array(
				'handle'   => 'gf_image_choices_ace_editor',
				'src'      => $this->get_base_url() . '/lib/ace/ace.js',
				'deps'     => array( 'jquery' ),
				'enqueue'  => array(
					array( 'admin_page' => array( 'plugin_settings', 'form_settings' ) ),
				),
			),
			array(
				'handle'  => 'jetsloth_lightbox',
				'src'     => $this->get_base_url() . '/js/jetsloth-lightbox.js',
				'version' => $this->_version,
				'deps'     => array( 'jquery' ),
				'enqueue' => array(
					array( 'admin_page' => array( 'entry_detail', 'entry_edit' ) ),
					array( 'field_types' => array( 'radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option' ) ),
				),
			),
			array(
				'handle'  => 'jetsloth_filters_actions',
				'src'     => $this->get_base_url() . '/js/jetsloth-filters-actions.js',
				'version' => $this->_version,
				'deps'     => array( 'jquery' ),
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'entry_view', 'entry_detail', 'entry_edit' ) ),
					array( 'field_types' => array( 'radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option' ) ),
				),
			),
			array(
				'handle'  => 'gf_image_choices_js',
				'src'     => $this->get_base_url() . '/js/gf_image_choices.js',
				'version' => $this->_version,
				'deps'    => $gf_image_choices_js_deps,
				'callback' => array( $this, 'localize_scripts' ),
				'enqueue' => array(
					array( 'admin_page' => array( 'form_editor', 'entry_view', 'entry_detail', 'entry_edit' ) ),
					array( 'field_types' => array( 'radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option' ) ),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {

		$editor_styles = array(
			'handle'  => 'gf_image_choices_form_editor_css',
			'src'     => $this->get_base_url() . '/css/gf_image_choices_form_editor.css',
			'version' => $this->_version,
			'enqueue' => array(
				array('admin_page' => array( 'form_editor', 'plugin_settings', 'form_settings', 'entry_view', 'entry_detail' )),
				array('query' => 'page=gf_entries&view=entry&id=_notempty_')
			),
		);

		$frontend_styles = array(
			'handle'  => 'gf_image_choices_css',
			'src'     => $this->get_base_url() . '/css/gf_image_choices.css',
			'version' => $this->_version,
			'media'   => 'screen',
			'enqueue' => array(
				array('admin_page' => array( 'form_editor', 'entry_view', 'entry_detail' )),
				array('field_types' => array( 'radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option' )),
				array('query' => 'page=gf_entries&view=entry&id=_notempty_')
			),
		);

		$styles = array(
			$editor_styles,
		);

		$include_frontend_styles = apply_filters( 'gfic_enqueue_core_css', true );
		if ( $include_frontend_styles ) {
			$styles[] = $frontend_styles;
		}

		return array_merge( parent::styles(), $styles );
	}


	public function frontend_inline_styles( $form ) {

		$form_id = rgar( $form, 'id' );
		$form_settings = $this->get_form_settings( $form );

		$ignore_global_css_value = (isset($form_settings['gf_image_choices_ignore_global_css'])) ? $form_settings['gf_image_choices_ignore_global_css'] : 0;
		$global_css_value = $this->get_plugin_setting('gf_image_choices_custom_css_global');
		$global_ref = "gf_image_choices_custom_global";
		if ( empty($ignore_global_css_value) && !empty($global_css_value) && !wp_style_is($global_ref) ) {
			wp_register_style( $global_ref, false );
			wp_enqueue_style( $global_ref );
			wp_add_inline_style( $global_ref, $global_css_value );
		}


		$form_css_value = (isset($form_settings['gf_image_choices_custom_css'])) ? $form_settings['gf_image_choices_custom_css'] : '';
		$ref = "gf_image_choices_custom_{$form_id}";
		if ( !empty($form_css_value) && !wp_style_is($ref) ) {
			wp_register_style( $ref, false );
			wp_enqueue_style( $ref );
			wp_add_inline_style( $ref, $form_css_value );
		}

		$output_responsive_list_css = apply_filters('gfic_responsive_list_css', false);

		$form_markup_version = ( !empty( rgar( $form, 'markupVersion' ) ) ) ? rgar( $form, 'markupVersion' ) : 1;
		if ( $form_markup_version == 2 ):
			ob_start();
			?>
            @media only screen and (max-width: 736px) and (min-width: 481px) {

            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_2col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_2col .gfield_radio,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_3col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_3col .gfield_radio,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_4col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_4col .gfield_radio,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_5col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_5col .gfield_radio {
            display: grid;
            grid-template-columns: repeat(2, 50%);
            }

            }

            @media only screen and (max-width: 480px) {

            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_2col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_2col .gfield_radio,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_3col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_3col .gfield_radio,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_4col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_4col .gfield_radio,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_5col .gfield_checkbox,
            .gform_wrapper:not(.gform_legacy_markup_wrapper) .image-choices-field.gf_list_5col .gfield_radio {
            display: grid;
            grid-template-columns: repeat(1, 100%);
            }

            }
			<?php
			$gf_list_css = ob_get_clean();
			$list_css_ref = "gf_image_choices_list_styles";
			if ( !empty($output_responsive_list_css) && !wp_style_is($list_css_ref) ) {
				wp_register_style( $list_css_ref, false );
				wp_enqueue_style( $list_css_ref );
				wp_add_inline_style( $list_css_ref, $gf_list_css );
			}
			?>
		<?php
		else:
			ob_start();
			?>
            .image-choices-field.gf_list_1col,
            .image-choices-field.gf_list_2col,
            .image-choices-field.gf_list_3col,
            .image-choices-field.gf_list_4col,
            .image-choices-field.gf_list_5col,
            .gform_wrapper .gfield.image-choices-field.gf_list_2col,
            .gform_wrapper .gfield.image-choices-field.gf_list_3col,
            .gform_wrapper .gfield.image-choices-field.gf_list_4col,
            .gform_wrapper .gfield.image-choices-field.gf_list_5col {
            margin-right: -2% !important;
            }

            .image-choices-field.gf_list_1col .image-choices-choice,
            .image-choices-field.gf_list_2col .image-choices-choice,
            .image-choices-field.gf_list_3col .image-choices-choice,
            .image-choices-field.gf_list_4col .image-choices-choice,
            .image-choices-field.gf_list_5col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_2col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_3col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_4col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_5col li.image-choices-choice {
            margin-right: 2% !important;
            }

            .image-choices-field.gf_list_1col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_1col li.image-choices-choice {
            width: 98% !important;
            }

            .image-choices-field.gf_list_2col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_2col li.image-choices-choice {
            width: 48% !important;
            }

            .image-choices-field.gf_list_3col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_3col li.image-choices-choice {
            width: 31% !important;
            }

            .image-choices-field.gf_list_4col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_4col li.image-choices-choice {
            width: 23% !important;
            }

            .image-choices-field.gf_list_5col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_5col li.image-choices-choice {
            width: 18% !important;
            }
			<?php if ( !empty($output_responsive_list_css) ): ?>
            @media only screen and (max-width: 736px) {

            .image-choices-field.gf_list_2col .image-choices-choice,
            .image-choices-field.gf_list_3col .image-choices-choice,
            .image-choices-field.gf_list_4col .image-choices-choice,
            .image-choices-field.gf_list_5col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_2col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_3col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_4col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_5col li.image-choices-choice {
            width: 48% !important;
            }

            }

            @media only screen and (max-width: 480px) {

            .image-choices-field.gf_list_2col .image-choices-choice,
            .image-choices-field.gf_list_3col .image-choices-choice,
            .image-choices-field.gf_list_4col .image-choices-choice,
            .image-choices-field.gf_list_5col .image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_2col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_3col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_4col li.image-choices-choice,
            .gform_wrapper .gfield.image-choices-field.gf_list_5col li.image-choices-choice {
            width: 98% !important;
            }

            }
		<?php
		endif;

			$legacy_gf_list_css = ob_get_clean();
			$legacy_list_ref = "gf_image_choices_legacy_list_styles";
			if ( !wp_style_is($legacy_list_ref) ) {
				wp_register_style( $legacy_list_ref, false );
				wp_enqueue_style( $legacy_list_ref );
				wp_add_inline_style( $legacy_list_ref, $legacy_gf_list_css );
			}
		endif;

	}



	/**
	 * Localize the strings used by the scripts.
	 */
	public function localize_admin_scripts() {


		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		wp_localize_script( 'gf_image_choices_form_editor_js', 'imageChoicesFieldVars', array(
			'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
			'is_gf_min_2_5' => GFIC_GF_MIN_2_5,
			//'imagesUrl' => $this->get_base_url() . '/images',
		) );

		//localize strings for the js file
		wp_localize_script( 'gf_image_choices_form_editor_js', 'imageChoicesFieldStrings', array(
			'confirmImagesToggle'    => esc_html__( 'Color picker choices are enabled on this field. Are you sure you want to remove the colors and use images instead?', 'gf_image_choices' ),
			'uploadImage'    => esc_html__( 'Upload image', 'gf_image_choices' ),
			'removeImage'    => esc_html__( 'Remove this image', 'gf_image_choices' ),
			'removeAllChoices'    => esc_html__( 'Remove All Choices', 'gf_image_choices' ),
			'useLightboxWarning'    => esc_html__( 'It looks like you created your choices for this field prior to our release of the lightbox functionality. In order for lightbox to work with this field, you will need to remove and re-add the images for these choices again.', 'gf_image_choices' ),
		) );

		$elementor_compat = ( $this->is_elementor_installed() ) ? $this->get_plugin_setting('gf_image_choices_elementor_lightbox_compat') : '';
		wp_localize_script( 'gf_image_choices_js', 'imageChoicesVars', array(
			'is_gf_min_2_5' => GFIC_GF_MIN_2_5,
			'elementorCompat' => ( !empty( $elementor_compat ) ) ? $elementor_compat : '',
		) );

	}

	public function localize_scripts() {

		$lazy_load_global_value = $this->get_plugin_setting('gf_image_choices_lazy_load_global');
		if ( empty($lazy_load_global_value) ) {
			$lazy_load_global_value = 0;
		}

		$elementor_compat = ( $this->is_elementor_installed() ) ? $this->get_plugin_setting('gf_image_choices_elementor_lightbox_compat') : '';

		wp_localize_script( 'gf_image_choices_js', 'imageChoicesVars', array(
			'is_gf_min_2_5' => GFIC_GF_MIN_2_5,
			'elementorCompat' => ( !empty( $elementor_compat ) ) ? $elementor_compat : '',
			'lazyLoadGlobal' => $lazy_load_global_value,
		) );

	}


	/**
	 * Creates a settings page for this add-on.
	 */
	public function plugin_settings_fields() {

		$field = array();

		$license = $this->get_plugin_setting('gf_image_choices_license_key');
		$status = get_option('gf_image_choices_license_status');

		$license_field = array(
			'name' => 'gf_image_choices_license_key',
			'tooltip' => esc_html__('Enter the license key you received after purchasing the plugin.', 'gf_image_choices'),
			'label' => esc_html__('License Key', 'gf_image_choices'),
			'type' => 'text',
			'input_type' => 'password',
			'class' => 'medium',
			'default_value' => '',
			'validation_callback' => array($this, 'license_validation'),
			'feedback_callback' => array($this, 'license_feedback'),
			'error_message' => esc_html__( 'Invalid license', 'gf_image_choices' ),
		);

		if (!empty($license) && !empty($status)) {
			$license_field['after_input'] = ($status == 'valid') ? ' License is valid' : ' Invalid or expired license';
		}


		$fields[] = array(
			'title'  => esc_html__('To unlock plugin updates, please enter your license key below', 'gf_image_choices'),
			'fields' => array(
				$license_field
			)
		);


		$lazy_load_global_value = $this->get_plugin_setting('gf_image_choices_lazy_load_global');
		if ( empty($lazy_load_global_value) ) {
			$lazy_load_global_value = 0;
		}

		if ( GFIC_GF_MIN_2_5 ) {
			$lazy_load_global_field = array(
				'name' => 'gf_image_choices_lazy_load_global',
				'type' => 'toggle',
				'label' => __( 'Enable lazy loading', 'gf_image_choices' ),
				'default_value' => (int) $lazy_load_global_value,
			);
		}
		else {
			$lazy_load_global_field = array(
				'name' => 'gf_image_choices_lazy_load_global_checkbox',
				//'label' => '',
				'type' => 'checkbox',
				'choices' => array(
					array(
						'label' => esc_html__('Enable lazy loading', 'gf_image_choices'),
						'name' => 'gf_image_choices_lazy_load_global',
						'default_value' => $lazy_load_global_value,
					),
				),
			);
		}

		$fields[] = array(
			'title'  => esc_html__('Lazy Load', 'gf_image_choices'),
			'description' => esc_html__('With lazy load enabled, the images in choices will be loaded only as they enter (or about to enter) the viewport. This reduces initial page load time, initial page weight, and system resource usage, all of which have positive impacts on performance.', 'gf_image_choices'),
			//'class' => 'gform-settings-panel--half',
			'fields' => array(
				$lazy_load_global_field
			)
		);


		if ( $this->is_elementor_installed() ) {

			$elementor_compat_value = $this->get_plugin_setting('gf_image_choices_elementor_lightbox_compat');
			$elementor_compat_field = array(
				'name' => 'gf_image_choices_elementor_lightbox_compat',
				'tooltip' => esc_html__('Elementor by default will automatically open all image links in its own lightbox, clashing with the Image Choices lightbox feature and results in two lightboxes opening at the same time. This setting helps keep it to a single lightbox', 'gf_image_choices'),
				'label' => esc_html__('Elementor Lightbox', 'gf_image_choices'),
				'type' => 'select',
				'class' => 'medium',
				'default_value' => 'jetsloth',
				'choices' => array(
					array(
						'value' => 'jetsloth',
						'label' => esc_html__('Use JetSloth lightbox for Image Choices', 'gf_image_choices')
					),
					array(
						'value' => 'elementor',
						'label' => esc_html__('Use Elementor lightbox for Image Choices', 'gf_image_choices')
					),
				)
			);
			if (!empty($elementor_compat_value)) {
				$elementor_compat_field['default_value'] = $elementor_compat_value;
			}

			$fields[] = array(
				'title'  => esc_html__('Compatibility Settings', 'gf_image_choices'),
				'fields' => array(
					$elementor_compat_field
				)
			);

		}


		$legacy_styles_field = [];
		$legacy_styles_site_option_value = get_option('gfic_legacy_styles', '');
		if ( $legacy_styles_site_option_value != '' ) {

			$legacy_styles_field = array(
				'type' => 'hidden',
				'name' => 'gf_image_choices_legacy_styles',
				'default_value' => $legacy_styles_site_option_value
			);

		}

		$custom_css_global_value = $this->get_plugin_setting('gf_image_choices_custom_css_global');
		$custom_css_global_field = array(
			'name' => 'gf_image_choices_custom_css_global',
			'tooltip' => esc_html__('These styles will be loaded for all forms.<br/>Find examples at <a href="https://jetsloth.com/support/gravity-forms-image-choices/">https://jetsloth.com/support/gravity-forms-image-choices/</a>', 'gf_image_choices'),
			'label' => esc_html__('Custom CSS', 'gf_image_choices'),
			'type' => 'textarea',
			'class' => 'large',
			'default_value' => $custom_css_global_value
		);

		$fields[] = array(
			'title'  => esc_html__('Enter your own css to style image choices', 'gf_image_choices'),
			'fields' => array(
				$legacy_styles_field,
				$custom_css_global_field
			)
		);


		return $fields;
	}

	private function is_elementor_installed() {
		$installed = did_action( 'elementor/loaded' );
		return apply_filters( 'gfic_elementor_compat', $installed );
	}

	/**
	 * Configures the settings which should be rendered on the Form Settings > Image Choices tab.
	 *
	 * @return array
	 */
	public function form_settings_fields( $form ) {

		$settings = $this->get_form_settings( $form );

		$form_choices_entry_value = (isset($settings['gf_image_choices_entry_value'])) ? $settings['gf_image_choices_entry_value'] : 'value';
		$form_choices_entry_field = array(
			'name' => 'gf_image_choices_entry_value',
			//'tooltip' => esc_html__('The selected collapsible section will be opened by default when the form loads.', 'gf_image_choices'),
			'label' => esc_html__('Default Entry / Notification Display', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'default_value' => 'value',
			'choices' => array(
				array(
					'value' => 'value',
					'label' => esc_html__('Value (Gravity Forms default)', 'gf_image_choices')
				),
				array(
					'value' => 'image',
					'label' => esc_html__('Image', 'gf_image_choices')
				),
				array(
					'value' => 'text',
					'label' => esc_html__('Label', 'gf_image_choices')
				),
				array(
					'value' => 'image_text',
					'label' => esc_html__('Image and Label', 'gf_image_choices')
				),
				array(
					'value' => 'image_text_price',
					'label' => esc_html__('Image, Label and Price (Product or Option fields only)', 'gf_image_choices')
				),
				array(
					'value' => 'image_price',
					'label' => esc_html__('Image and Price (Product or Option fields only)', 'gf_image_choices')
				),
				array(
					'value' => 'image_value',
					'label' => esc_html__('Image and Value', 'gf_image_choices')
				)
			)
		);
		if (!empty($form_choices_entry_value)) {
			$form_choices_entry_field['default_value'] = $form_choices_entry_value;
		}

		$form_lightbox_size_value = (isset($settings['gf_image_choices_lightbox_size'])) ? $settings['gf_image_choices_lightbox_size'] : 'full';
		$form_choices_lightbox_size_field = array(
			'name' => 'gf_image_choices_lightbox_size',
			'tooltip' => esc_html__('The selected image size will be used in the lightbox, if enabled.', 'gf_image_choices'),
			'label' => esc_html__('Lightbox Image Size', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'default_value' => 'full',
			'choices' => array()
		);

		$size_names = apply_filters('image_size_names_choose', array(
			'thumbnail' => __( 'Thumbnail' ),
			'medium'    => __( 'Medium' ),
			'large'     => __( 'Large' ),
			'full'      => __( 'Full Size' )
		));

		foreach(get_intermediate_image_sizes() as $_size) {
			if ( in_array( $_size, array('thumbnail', 'medium', 'large') ) ) {
				$label = isset($size_names[$_size]) ? $size_names[$_size] : $_size;
				$form_choices_lightbox_size_field['choices'][] = array(
					'value' => $_size,
					'label' => $label
				);
			}
            elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$label = isset($size_names[$_size]) ? $size_names[$_size] : $_size;
				$form_choices_lightbox_size_field['choices'][] = array(
					'value' => $_size,
					'label' => $label
				);
			}
		}

		$form_choices_lightbox_size_field['choices'][] = array(
			'value' => 'full',
			'label' => isset($size_names['full']) ? $size_names['full'] : 'full'
		);

		if (!empty($form_lightbox_size_value)) {
			$form_choices_lightbox_size_field['default_value'] = $form_lightbox_size_value;
		}


		$form_custom_css_value = (isset($settings['gf_image_choices_custom_css'])) ? $settings['gf_image_choices_custom_css'] : '';
		$form_custom_css_field = array(
			'name' => 'gf_image_choices_custom_css',
			'tooltip' => esc_html__('These styles will be loaded for this form only.<br/>Find examples at <a href="https://jetsloth.com/support/gravity-forms-image-choices/">https://jetsloth.com/support/gravity-forms-image-choices/</a>', 'gf_image_choices'),
			'label' => esc_html__('Custom CSS', 'gf_image_choices'),
			'type' => 'textarea',
			'class' => 'large',
			'default_value' => $form_custom_css_value
		);

		$form_ignore_global_css_value = (isset($settings['gf_image_choices_ignore_global_css']) && $settings['gf_image_choices_ignore_global_css'] == 1) ? 1 : 0;
		$form_ignore_global_css_field = array(
			'name' => 'gf_image_choices_ignore_global_css',
			'label' => '',
			'type' => 'checkbox',
			'choices' => array(
				array(
					'label' => esc_html__('Ignore Global Custom CSS for this form?', 'gf_image_choices'),
					'tooltip' => esc_html__('If checked, the custom css entered in the global settings won\'t be loaded for this form.', 'gf_image_choices'),
					'name' => 'gf_image_choices_ignore_global_css'
				)
			)
		);
		if (!empty($form_ignore_global_css_value)) {
			$form_ignore_global_css_field['choices'][0]['default_value'] = 1;
		}



		$lazy_load_global_value = $this->get_plugin_setting('gf_image_choices_lazy_load_global');
		if ( empty($lazy_load_global_value) ) {
			$lazy_load_global_value = 0;
		}
		$lazy_load_global_label = ( empty($lazy_load_global_value) ) ? esc_html__('No', 'gf_image_choices') : esc_html__('Yes', 'gf_image_choices');

		$lazy_load_value = (isset($settings['gf_image_choices_lazy_load'])) ? $settings['gf_image_choices_lazy_load'] : '';
		$lazy_load_field = array(
			'name' => 'gf_image_choices_lazy_load',
			'label' => esc_html__('Lazy Loading', 'gf_image_choices'),
			'type' => 'select',
			'default_value' => $lazy_load_value,
			'choices' => array(
				array(
					'label' => sprintf( esc_html__('Use global setting (%s)', 'gf_image_choices'), $lazy_load_global_label ),
					'value' => '',
				),
				array(
					'label' => esc_html__('Yes', 'gf_image_choices'),
					'value' => 1,
				),
				array(
					'label' => esc_html__('No', 'gf_image_choices'),
					'value' => 0,
				),
			),
		);


		return array(
			array(
				'title' => esc_html__( 'Image Choices', 'gf_image_choices' ),
				'fields' => array(
					$form_choices_entry_field,
					$form_choices_lightbox_size_field,
					$lazy_load_field,
					$form_custom_css_field,
					$form_ignore_global_css_field
				)
			)
		);

	}



	/**
	 * Format the field values for entry list page so they show the image instead of values.
	 *
	 * @param string|array $value The field value.
	 * @param int $form_id The ID of the form currently being processed.
	 * @param string $field_id The ID of the field currently being processed.
	 * @param array $entry The entry object currently being processed.
	 *
	 * @return string|array
	 */
	public function entries_table_field_value( $value, $form_id, $field_id, $entry ) {
		if ( ! rgblank( $value ) ) {
			$form_meta = RGFormsModel::get_form_meta( $form_id );
			$field     = RGFormsModel::get_field( $form_meta, $field_id );

			$form = GFAPI::get_form($form_id);

			return $this->maybe_format_field_values( $value, $field, $form, $entry );
		}

		return $value;
	}

	/**
	 * Format the field values on the entry detail page so they show the image instead of values.
	 *
	 * @param string|array $value The field value.
	 * @param GF_Field $field The field currently being processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 *
	 * @return string|array
	 */
	public function custom_entry_field_value( $value, $field, $entry, $form ) {
		return ! rgblank( $value ) ? $this->maybe_format_field_values( $value, $field, $form, $entry ) : $value;
	}

	public function custom_order_summary_entry_field_value( $markup, $form, $entry, $order_summary, $format = 'html' ) {
		if ($format == 'text') {
			return $markup;
		}

		$style = 'style="width:80px; height:auto; max-width:100%;"';

		// Products by default display value (not text/label)
		// Eg: Selected Product Value
		// Product Options by default display both the main Field Label and the selected Choice Value
		// Eg: Product Option Field Label: Selected Option Value

		$contains_image_choices_fields = false;

		// ---------
		$settings = $this->get_form_settings( $form );
		$form_choices_entry_setting = (isset($settings['gf_image_choices_entry_value'])) ? $settings['gf_image_choices_entry_value'] : 'value';
		// ---------

		$fields_id_lookup = array();
		$fields_label_lookup = array();
		foreach($form['fields'] as $field) {
			$fields_id_lookup[$field->id] = $field;
			$fields_label_lookup[$field->label] = $field;
		}

		$product_summary_image_choices = array();
		foreach ($order_summary['products'] as $product_field_id => $product) {

			$data = array(
				'product_field_id' => $product_field_id,
				'product_field_label' => '',
				'product_summary_display_value' => $product['name'],
				'product_field_selected_value' => $product['name'],
				'product_field_selected_price' => $product['price'],
				'product_field_selected_price_formatted' => GFCommon::format_number( $product['price'], 'currency' ),
				'product_field_selected_label' => '',
				'product_field_selected_image' => '',
				'product_field_entry_value_type' => 'value',
				'options' => array()
			);

			if (isset($fields_id_lookup[$product_field_id])) {
				$field = $fields_id_lookup[$product_field_id];
				if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages ) {
					$field_choices_entry_setting = (property_exists($field, 'imageChoices_entrySetting') && !empty($field->imageChoices_entrySetting)) ? $field->imageChoices_entrySetting : 'form_setting';
					$data['product_field_label'] = $field->label;
					$data['product_field_selected_label'] = RGFormsModel::get_choice_text($field, $data['product_field_selected_value']);
					$data['product_field_entry_value_type'] = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
					$data['product_field_selected_image'] = $this->get_choice_image_src($field, $data['product_field_selected_value']);
					$contains_image_choices_fields = true;
				}
			}

			if (isset($product['options']) && !empty($product['options'])) {
				foreach($product['options'] as $option) {
					$option_field_label = $option['field_label'];
					$option_data = array(
						'option_field_label' => $option_field_label,
						'option_summary_display_value' => $option['option_label'],
						'option_field_selected_value' => $option['option_name'],
						'option_field_selected_price' => $option['price'],
						'option_field_selected_price_formatted' => GFCommon::format_number( $option['price'], 'currency' ),
						'option_field_selected_label' => '',
						'option_field_selected_image' => '',
						'option_field_entry_value_type' => 'value'
					);

					if (isset($fields_label_lookup[$option_field_label])) {
						$field = $fields_label_lookup[$option_field_label];
						if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages ) {
							$field_choices_entry_setting = (property_exists($field, 'imageChoices_entrySetting') && !empty($field->imageChoices_entrySetting)) ? $field->imageChoices_entrySetting : 'form_setting';
							$option_data['option_field_selected_label'] = RGFormsModel::get_choice_text($field, $option_data['option_field_selected_value']);
							$option_data['option_field_entry_value_type'] = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
							$option_data['option_field_selected_image'] = $this->get_choice_image_src($field, $option_data['option_field_selected_value']);
							$contains_image_choices_fields = true;
						}
					}

					array_push($data['options'], $option_data);
				}
			}
			array_push($product_summary_image_choices, $data);
		}

		if ($contains_image_choices_fields) {
			$image_entry_setting_values = array('src', 'image', 'image_text', 'image_text_price', 'image_price', 'image_value');

			foreach($product_summary_image_choices as $summary_item) {
				if (
					in_array($summary_item['product_field_entry_value_type'], $image_entry_setting_values)
					&& !empty($summary_item['product_field_selected_image'])) {

					$replacement_markup = '';

					if ($summary_item['product_field_entry_value_type'] == 'src') {
						$replacement_markup = $summary_item['product_field_selected_image'];
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image') {
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_text') {
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_field_selected_label'].'</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_text_price') {
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">' . $summary_item['product_field_selected_label'] . ' (' . $summary_item['product_field_selected_price_formatted'] . ')' . '</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_price') {
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_field_selected_price_formatted'].'</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_value') {
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_summary_display_value'].'</div>';
					}

					if (!empty($replacement_markup)) {
						if (strpos($markup, '<div class="product_name">'.$summary_item['product_summary_display_value'].'</div>') === FALSE) {
							$markup = str_replace(
								$summary_item['product_summary_display_value'],
								$replacement_markup,
								$markup
							);
						}
						else {
							$markup = str_replace(
								'<div class="product_name">'.$summary_item['product_summary_display_value'].'</div>',
								$replacement_markup,
								$markup
							);
						}
					}

				}
				else if ($summary_item['product_field_entry_value_type'] == 'text') {
					// Text
					$markup = str_replace(
						$summary_item['product_summary_display_value'],
						//$summary_item['product_field_selected_label'] . ' (' . GFCommon::to_money( $summary_item['product_field_selected_price'], $entry['currency'] ) . ')',
						$summary_item['product_field_selected_label'],
						$markup
					);
				}


				foreach($summary_item['options'] as $option_item) {
					if (in_array($option_item['option_field_entry_value_type'], $image_entry_setting_values)) {
						$replacement_markup = '';
						if ($option_item['option_field_entry_value_type'] == 'src') {
							$replacement_markup = $option_item['option_field_selected_image'];
						}
						else if ($option_item['option_field_entry_value_type'] == 'image') {
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div>';
						}
						else if ($option_item['option_field_entry_value_type'] == 'image_text') {
							//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div><div class="product_option_name">'.$option_item['option_field_label'] . ': ' . $option_item['option_field_selected_label'].'</div>';
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div><div class="product_option_name">'.$option_item['option_summary_display_value'].'</div>';
						}
						else if ($option_item['option_field_entry_value_type'] == 'image_text_price') {
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div><div class="product_option_name">'.$option_item['option_summary_display_value'] . ' (' . $option_item['option_field_selected_price_formatted'] . ')' . '</div>';
						}
						else if ($option_item['option_field_entry_value_type'] == 'image_price') {
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div><div class="product_option_name">'.$option_item['option_field_selected_price_formatted'].'</div>';
						}
						else if ($option_item['option_field_entry_value_type'] == 'image_value') {
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div><div class="product_option_name">' . $option_item['option_summary_display_value'] . '</div>';
						}

						if (!empty($replacement_markup)) {
							$markup = str_replace(
								$option_item['option_summary_display_value'],
								$replacement_markup,
								$markup
							);
						}

					}
					else if ($option_item['option_field_entry_value_type'] == 'text') {
						$markup = str_replace(
							$option_item['option_summary_display_value'],
							//$option_item['option_field_label'] . ': ' . $option_item['option_field_selected_label'] . ' (' . GFCommon::to_money( $option_item['option_field_selected_price'], $entry['currency'] ) . ')',
							$option_item['option_field_label'] . ': ' . $option_item['option_field_selected_label'],
							$markup
						);
					}
				}
			}

		}

		return $markup;
	}

	public function get_choice_image_src($field, $value, $choice_value_or_text = 'value') {
		$img = '';

		if ($choice_value_or_text != 'value' && $choice_value_or_text != 'text') {
			$choice_value_or_text = 'value';
		}

		foreach($field->choices as $choice) {
			if ($choice[$choice_value_or_text] != $value) {
				continue;
			}
			if (isset($choice['imageChoices_image']) && !empty($choice['imageChoices_image'])) {
				$img = $choice['imageChoices_image'];
			}
		}
		return $img;
	}

	public function get_choice_image_item_markup($src = '', $text_value = '', $modifier = '') {

		$size = (!empty($modifier) && strlen($modifier) > 5 && substr($modifier, 0, 6) == 'image_') ? substr($modifier, 6) : '80px';

		if (GFCommon::is_entry_detail()) {

			$item_html_tag = ( GFIC_GF_MIN_2_5 ) ? "div" : "li";
			$markup = '<'.$item_html_tag.' class="gf-image-choices-entry-choice">';
			//$markup .= '<span class="gf-image-choices-entry-choice-image" style="background-image:url('.$src.')"></span>';
			$markup .= '<span class="gf-image-choices-entry-choice-image">';
			if (!empty($src)) {
				$markup .= '<img src="'.$src.'" style="width:'.$size.'; height:auto; max-width:100%;" />';
			}
			$markup .= '</span>';
			if (!empty($text_value)) {
				$markup .= '<span class="gf-image-choices-entry-choice-text">'.html_entity_decode($text_value).'</span>';
			}
			$markup .= '</'.$item_html_tag.'>';

		}
		else {

			$markup = '<span style="display: inline-block; vertical-align: top; margin: 0 10px 20px; text-align: center;">';
			$markup .= '<span style="display: inline-block;">';
			if (!empty($src)) {
				$markup .= '<img src="'.$src.'" style="width:'.$size.'; height:auto; max-width:100%;" />';
			}
			$markup .= '</span>';
			if (!empty($text_value)) {
				$markup .= '<span style="display: block; font-size: 12px;">'.html_entity_decode($text_value).'</span>';
			}
			$markup .= '</span>';

		}


		return $markup;
	}

	public function wrap_choice_images_markup($choice_images_markup = array()) {

		if (GFCommon::is_entry_detail()) {
			$item_html_tag = ( GFIC_GF_MIN_2_5 ) ? "div" : "ul";
			array_unshift($choice_images_markup, '<'.$item_html_tag.' class="gf-image-choices-entry">');
			array_push($choice_images_markup, '</'.$item_html_tag.'>');
		}
		else {
			array_unshift($choice_images_markup, '<div style="display: block; text-align: center;">');
			array_push($choice_images_markup, '</div>');
		}

		return implode('', $choice_images_markup);

	}

	/**
	 * If the field has image choices then replace the choice value with the image preview.
	 *
	 * @param string $value The current merge tag value to be filtered. Replace it with any other text to replace the merge tag output, or return “false” to disable this field’s merge tag output.
	 * @param string $merge_tag If the merge tag being executed is an individual field merge tag (i.e. {Name:3}), this variable will contain the field’s ID. If not, this variable will contain the name of the merge tag (i.e. all_fields or all_quiz_results)
	 * @param string $modifier The string containing any modifiers for this merge tag
	 * @param Object $field The current field.
	 * @param mixed $raw_value The raw value submitted for this field.
	 *
	 * @return string
	 */

	public function custom_notification_merge_tag($value, $merge_tag, $modifier, $field, $raw_value) {

		$is_supported_field_type = ( is_object( $field ) && property_exists($field, 'type') && in_array($field->type, $this->_supported_field_types) );
		$is_supported_input_type = ( in_array($field->type, $this->_supported_input_types) || ( property_exists($field, 'inputType') && !empty($field->inputType) && in_array($field->inputType, $this->_supported_input_types) ) );
		$is_supported_field = ($is_supported_field_type && $is_supported_input_type);
		$is_image_choices_enabled = (property_exists($field, 'imageChoices_enableImages') && !empty($field->imageChoices_enableImages));

		if ( !$is_supported_field || !$is_image_choices_enabled ) {
			return $value;
		}


		$is_standard_merge_tag = ( !empty($merge_tag) && in_array($merge_tag, $this->_standard_merge_tags) );
		$is_image_specific_modifier = ( !empty($modifier) && substr($modifier, 0, 5) == 'image' );
		$is_src_specific_modifier = ( !empty($modifier) && substr($modifier, 0, 3) == 'src' );

		if ( !$is_standard_merge_tag && !$is_image_specific_modifier && !$is_src_specific_modifier ) {
			return $value;
		}


		// ---------
		$form = GFAPI::get_form( $field->formId );
		$settings = $this->get_form_settings( $form );
		$form_choices_entry_setting = (isset($settings['gf_image_choices_entry_value'])) ? $settings['gf_image_choices_entry_value'] : 'value';
		$field_choices_entry_setting = (property_exists($field, 'imageChoices_entrySetting') && !empty($field->imageChoices_entrySetting)) ? $field->imageChoices_entrySetting : 'form_setting';
		$field_entry_value_type = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_text_price', 'image_price', 'image_value');
		// ---------

		if ($is_image_specific_modifier) {
			$field_entry_value_type = 'image';
		}

		if ($is_src_specific_modifier) {
			$field_entry_value_type = 'src';
		}

		if (!$is_image_specific_modifier && !$is_src_specific_modifier && ($field_entry_value_type == 'value' || !in_array($field_entry_value_type, $image_entry_setting_values))) {
			return $value;
		}

		$field_input_type = ( property_exists($field, 'inputType') && !empty($field->inputType) ) ? $field->inputType : $field->type;

		$text = RGFormsModel::get_choice_text($field, $value);
		$image = '';

		if ( $field_input_type == 'checkbox' ) {

			$merge_tag_dot_pos = strpos( $merge_tag, "." );
			$merge_tag_contains_dot = ( $merge_tag_dot_pos !== FALSE );
			$return_input_id = ( $merge_tag_contains_dot ) ? substr( $merge_tag, $merge_tag_dot_pos + 1 ) : "";

			$return_images = array();
			$return_strings = array();

			foreach ($raw_value as $key => $choice_raw_value) {
				if (empty($choice_raw_value)) {
					continue;
				}

				switch( $field->type ) {
					case "product" :
					case "option" :
						$choice_value = (strpos($choice_raw_value, "|") !== FALSE) ? strstr($choice_raw_value, "|", true) : $choice_raw_value;
						$choice_value_or_text = 'value';
						break;
					default :
						$choice_value = $choice_raw_value;
						$choice_value_or_text = 'value';
						break;
				}

				$image = $this->get_choice_image_src($field, $choice_value, $choice_value_or_text);
				$text = RGFormsModel::get_choice_text($field, $choice_raw_value);
				$price = ( ($field->type == 'product' || $field->type == 'option') && strpos($choice_raw_value, "|") !== FALSE ) ? substr($choice_raw_value, strpos($choice_raw_value, "|") + 1) : '';

				if ($is_image_specific_modifier) {
					//$image_item_markup = $this->get_choice_image_item_markup($image, $text, $modifier);
					if ( !$merge_tag_contains_dot || $merge_tag == $key ) {
						$image_item_markup = $this->get_choice_image_item_markup($image, "", $modifier);
						array_push($return_images, $image_item_markup);
					}
				}
				else if ($is_src_specific_modifier) {
					if ( !$merge_tag_contains_dot || $merge_tag == $key ) {
						array_push($return_images, $image);
					}
				}
				else if ($field_entry_value_type == 'text') {
					if ( !$merge_tag_contains_dot || $merge_tag == $key ) {
						array_push($return_strings, $text);
					}
				}
				else if (!empty($image)) {
					$image_item_markup = '';
					if ($field_entry_value_type == 'src') {
						$image_item_markup = $image;
					}
					else if ($field_entry_value_type == 'image') {
						$image_item_markup = $this->get_choice_image_item_markup($image);
					}
					else if ($field_entry_value_type == 'image_text') {
						$image_item_markup = $this->get_choice_image_item_markup($image, $text);
					}
					else if ($field_entry_value_type == 'image_text_price') {
						if ( !empty($price) ) {
							$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')');
						}
						else {
							// if it's not a product or option field, just return the image and label
							$image_item_markup = $this->get_choice_image_item_markup($image, $text);
						}
					}
					else if ($field_entry_value_type == 'image_price') {
						if ( !empty($price) ) {
							$image_item_markup = $this->get_choice_image_item_markup($image, GFCommon::format_number($price, 'currency'));
						}
						else {
							// if it's not a product or option field, just return the image
							$image_item_markup = $this->get_choice_image_item_markup($image);
						}
					}
					else if ($field_entry_value_type == 'image_value') {
						$image_item_markup = $this->get_choice_image_item_markup($image, $choice_value);
					}

					if ( !$merge_tag_contains_dot || $merge_tag == $key ) {
						array_push($return_images, $image_item_markup);
					}
				}

			}

			if (!empty($return_images)) {
				$value = implode(" ", $return_images);
			}
			else {
				$value = implode(', ', $return_strings);
			}

		}
		else if ( $field_input_type == 'radio' ) {

			switch( $field->type ) {
				case "product" :
				case "option" :
					$choice_value = (strpos($raw_value, "|") !== FALSE) ? strstr($raw_value, "|", true) : $raw_value;
					$choice_value_or_text = 'value';
					break;
				default :
					$choice_value = $raw_value;
					$choice_value_or_text = 'value';
					break;
			}

			$image_item_markup = '';
			$image = $this->get_choice_image_src($field, $choice_value, $choice_value_or_text);
			$price = ( ($field->type == 'product' || $field->type == 'option') && strpos($raw_value, "|") !== FALSE ) ? substr($raw_value, strpos($raw_value, "|") + 1) : '';

			if ($is_image_specific_modifier) {
				//$image_item_markup = $this->get_choice_image_item_markup($image, $text, $modifier);
				$image_item_markup = $this->get_choice_image_item_markup($image, "", $modifier);
			}
			else if ($is_src_specific_modifier) {
				$value = $image;
			}
			else if ($field_entry_value_type == 'text') {
				$value = $text;
			}
			else if (!empty($image)) {
				if ($field_entry_value_type == 'image') {
					$image_item_markup = $this->get_choice_image_item_markup($image);
				}
				else if ($field_entry_value_type == 'image_text') {
					$image_item_markup = $this->get_choice_image_item_markup($image, $text);
				}
				else if ($field_entry_value_type == 'image_text_price') {
					if ( !empty($price) ) {
						$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')');
					}
					else {
						// if it's not a product or option field, just return the image and label
						$image_item_markup = $this->get_choice_image_item_markup($image, $text);
					}
				}
				else if ($field_entry_value_type == 'image_price') {
					if ( !empty($price) ) {
						$image_item_markup = $this->get_choice_image_item_markup($image, GFCommon::format_number($price, 'currency'));
					}
					else {
						// if it's not a product or option field, just return the image
						$image_item_markup = $this->get_choice_image_item_markup($image);
					}
				}
				else if ($field_entry_value_type == 'image_value') {
					$image_item_markup = $this->get_choice_image_item_markup($image, $choice_value);
				}
			}

			if (!empty($image_item_markup)) {
				$value = $image_item_markup;
			}

		}

		return $value;

	}


	/**
	 * Replace the image merge tags.
	 *
	 * @param string $text The current text in which merge tags are being replaced.
	 * @param array $form The current form object.
	 * @param array $entry The current entry object.
	 * @param bool $url_encode Whether or not to encode any URLs found in the replaced value.
	 * @param bool $esc_html Whether or not to encode HTML found in the replaced value.
	 * @param bool $nl2br Whether or not to convert newlines to break tags.
	 * @param string $format The format requested for the location the merge is being used. Possible values: html, text or url.
	 *
	 * @return string
	 */
	public function custom_replace_merge_tags( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( empty( $entry ) || empty( $form ) || (!empty($format) && $format != 'html') ) {
			return $text;
		}

		// Default image merge tag modifier. eg {:1:image}
		preg_match( "/\{(?P<field_label>.*)\:(?P<field_id>\d+)\:image\}/", $text, $matches );
		if ( !empty($matches) ) {
			$merge_tag = $matches[0];
			$field_label = (isset($matches['field_label']) && !empty($matches['field_label'])) ? $matches['field_label'] : "";
			$field_id = (isset($matches['field_id']) && !empty($matches['field_id'])) ? $matches['field_id'] : "";
			$field = RGFormsModel::get_field( $form, $field_id );
			$field_value = '';
			if (is_object( $field )) {
				if ( $field->type == 'checkbox' || ( property_exists($field, 'inputType') && !empty($field->inputType) && $field->inputType == 'checkbox' ) ) {
					$field_value = $field->get_value_export( $entry, $field_id, true );
				}
				else {
					$field_value = rgar($entry, $field_id);
				}
			}
			$merge_tag_replacement = $this->custom_notification_merge_tag($merge_tag, $field_id, "image", $field, $field_value);
			return str_replace($merge_tag, $merge_tag_replacement, $text);
		}

		// Custom image size merge tag modifier. eg {:1:image_200px}
		preg_match( "/\{(?P<field_label>.*)\:(?P<field_id>\d+)\:image_(?P<image_size>\d+)px\}/", $text, $matches );
		if ( !empty($matches) && isset($matches['image_size']) && !empty($matches['image_size']) ) {
			$merge_tag = $matches[0];
			$field_label = (isset($matches['field_label']) && !empty($matches['field_label'])) ? $matches['field_label'] : "";
			$field_id = (isset($matches['field_id']) && !empty($matches['field_id'])) ? $matches['field_id'] : "";
			$image_size = $matches['image_size'];
			$field = RGFormsModel::get_field( $form, $field_id );
			$field_value = '';
			if (is_object( $field )) {
				if ( $field->type == 'checkbox' || ( property_exists($field, 'inputType') && !empty($field->inputType) && $field->inputType == 'checkbox' ) ) {
					$field_value = $field->get_value_export( $entry, $field_id, true );
				}
				else {
					$field_value = rgar($entry, $field_id);
				}
			}
			$merge_tag_replacement = $this->custom_notification_merge_tag($merge_tag, $field_id, "image_{$image_size}px", $field, $field_value);
			return str_replace($merge_tag, $merge_tag_replacement, $text);
		}

		return $text;

	}

	/**
	 * Replace the quiz result merge tags.
	 *
	 * @param string $text The current text in which merge tags are being replaced.
	 * @param array $form The current form object.
	 * @param array $entry The current entry object.
	 * @param bool $url_encode Whether or not to encode any URLs found in the replaced value.
	 * @param bool $esc_html Whether or not to encode HTML found in the replaced value.
	 * @param bool $nl2br Whether or not to convert newlines to break tags.
	 * @param string $format The format requested for the location the merge is being used. Possible values: html, text or url.
	 *
	 * @return string
	 */
	public function render_quiz_results_merge_tag( $text, $form, $entry, $url_encode, $esc_html, $nl2br, $format ) {

		if ( empty( $entry ) || empty( $form ) || (!empty($format) && $format != 'html') ) {
			return $text;
		}

		if ( strpos($text, "gquiz-container") === false || strpos($text, "gquiz-image-choices-choice") !== false ) {
			// if it's not yet the quiz results markup, or the image choices markup already done
			return $text;
		}

		// check if it's a quiz
		$quiz_fields = GFAPI::get_fields_by_type( $form, array( 'quiz' ) );
		if ( empty ( $quiz_fields ) ) {
			return $text;
		}

		// ---------
		$settings = $this->get_form_settings( $form );
		$form_choices_entry_setting = (isset($settings['gf_image_choices_entry_value'])) ? $settings['gf_image_choices_entry_value'] : 'value';
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_text_price', 'image_price', 'image_value');
		// ---------


		$image_choices_fields = array();
		foreach($form['fields'] as &$field) {
			if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && !empty($field->imageChoices_enableImages)) {
				$image_choices_fields[$field->id] = $field;
			}
		}

		$results = gf_quiz()->get_quiz_results( $form, $entry );
		$fields = $results['fields'];

		foreach($fields as $results_field) {
			$field_id = $results_field['id'];

			if ( !isset( $image_choices_fields[$field_id] ) ) {
				continue;
			}

			$field = $image_choices_fields[ $field_id ];

			$field_choices_entry_setting = (property_exists($field, 'imageChoices_entrySetting') && !empty($field->imageChoices_entrySetting)) ? $field->imageChoices_entrySetting : 'form_setting';
			$field_entry_value_type = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
			if ($field_entry_value_type == 'value' || !in_array($field_entry_value_type, $image_entry_setting_values)) {
				continue;
			}

			$field_markup = $results_field['markup'];
			//$is_correct = $results_field['is_correct'];

			$choices_data = array();
			foreach ($field->choices as $choice) {
				array_push($choices_data, array(
					'text' => $choice['text'],
					'value' => $choice['value'],
					'image' => (isset($choice['imageChoices_image'])) ? $choice['imageChoices_image'] : '',
					'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : ''
				));
			}

			// Modifying the existing elements easier with DOMDocument
			$dom = new DOMDocument;
			$dom->loadHTML(mb_convert_encoding($field_markup, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

			$div_elems = $dom->getElementsByTagName('div');
			foreach($div_elems as $div) {
				if (!$div->hasAttribute('class')) {
					continue;
				}

				$cls = $div->getAttribute('class');
				if (strpos($cls, 'gquiz-field') === FALSE) {
					continue;
				}

				$cls .= ' gquiz-image-choices-field';
				$div->setAttribute('class', $cls);
			}

			$ul_elems = $dom->getElementsByTagName('ul');
			foreach($ul_elems as $ul) {
				$cls = '';
				if ($ul->hasAttribute('class')) {
					$cls = $ul->getAttribute('class') . ' ';
				}
				$cls .= 'gquiz-image-choices';
				$ul->setAttribute('class', $cls);
				$ul->setAttribute('style', 'list-style: none; display: block; text-align: left;');// inline styles for notification email
			}

			$li_elems = $dom->getElementsByTagName('li');
			foreach($li_elems as $li) {
				$cls = '';
				if ($li->hasAttribute('class')) {
					$cls = $li->getAttribute('class') . ' ';
				}
				$cls .= 'gquiz-image-choices-choice';
				$li->setAttribute('class', $cls);
				$li->setAttribute('style', 'display: inline-block; vertical-align: top; margin: 0 10px 20px; text-align: center; position: relative; text-align: center;');// inline styles for notification email

				$trimmed_value = $li->nodeValue;
				$trimmed_value = preg_replace("/[\r\n]+/", "\n", $trimmed_value);
				$trimmed_value = preg_replace("/\s+/", ' ', $trimmed_value);
				$trimmed_value = trim($trimmed_value);

				foreach ($choices_data as $choice) {

					$trimmed_choice_text = $choice['text'];
					$trimmed_choice_text = preg_replace("/[\r\n]+/", "\n", $trimmed_choice_text);
					$trimmed_choice_text = preg_replace("/\s+/", ' ', $trimmed_choice_text);
					$trimmed_choice_text = trim($trimmed_choice_text);

					if ($trimmed_value != $trimmed_choice_text) {
						continue;
					}

					if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_text_price' || $field_entry_value_type == 'image_price' || $field_entry_value_type == 'image_value') {
						$img = $dom->createElement('span');
						$img->setAttribute('class', 'gquiz-image-choices-choice-image');
						$img->setAttribute('style', 'background-image:url('.$choice['image'].'); display: inline-block; width: 80px; height: 80px; background-size: cover; background-repeat: no-repeat; background-position: 50%;');// inline styles for notification email
						if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value' || $field_entry_value_type == 'image_text_price' || $field_entry_value_type == 'image_price') {
							$txt = $dom->createElement('span');
							$txt->setAttribute('class', 'gquiz-image-choices-choice-text');
							$txt->setAttribute('style', 'display: block; font-size: 12px;');// inline styles for notification email
							$txt->nodeValue = $choice['text'];
							$img->appendChild($txt);
						}

						$icons = $li->getElementsByTagName('img');
						$icon_src = '';
						foreach($icons as $icon) {
							$icon_src = $icon->getAttribute('src');
						}
						$icon = $dom->createElement('img');
						$icon->setAttribute('src', $icon_src);
						$icon->setAttribute('class', 'gquiz-image-choices-choice-icon');
						$icon->setAttribute('style', 'display: block; margin: 0 auto;');

						$li->nodeValue = "";

						$li->appendChild($img);
						$li->appendChild($icon);

					}

				}

			}

			$html = $dom->saveHTML($dom->documentElement);
			//$new_field_markup = utf8_decode( $html );
			$new_field_markup = $html;

			$text = str_replace($field_markup, $new_field_markup, $text);

		}

		return $text;

	}


	/**
	 * If the field has image choices then replace the choice value with the image preview.
	 *
	 * @param string $value The field value.
	 * @param GF_Field|null $field The field object being processed or null.
	 * @param array $form The form object currently being processed.
	 * @param array $entry The entry object currently being processed.
	 *
	 * @return string
	 */

	public function maybe_format_field_values( $value, $field, $form, $entry ) {

		if (!isset($field) || empty($field)) {
			return $value;
		}


		// ---------
		$settings = $this->get_form_settings( $form );
		$form_choices_entry_setting = (isset($settings['gf_image_choices_entry_value'])) ? $settings['gf_image_choices_entry_value'] : 'value';
		$field_choices_entry_setting = (property_exists($field, 'imageChoices_entrySetting') && !empty($field->imageChoices_entrySetting)) ? $field->imageChoices_entrySetting : 'form_setting';
		$field_entry_value_type = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_text_price', 'image_price', 'image_value');
		// ---------

		$real_value = RGFormsModel::get_lead_field_value( $entry, $field );

		//if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages && $field->type != 'product' && $field->type != 'option') {
		if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages && $field_entry_value_type != 'value' ) {
			$type_property = ($field->type == 'survey' || $field->type == 'poll' || $field->type == 'quiz' || $field->type == 'post_custom_field' || $field->type == 'product' || $field->type == 'option') ? 'inputType' : 'type';

			// Product field doesn't have checkboxes, only radio
			// Option field has both

			if ($field[$type_property] == 'checkbox' && ( strpos($value, ', ') !== FALSE || strpos($value, "<ul class='bulleted'>") !== FALSE ) ) {

				// multiple selections
				$ordered_values = '';//(!empty($value)) ? explode(', ', $value) : '';
				if ( !empty($value) && strpos($value, ', ') !== FALSE ) {
					$ordered_values = explode(', ', $value);
				}
				else if ( !empty($value) && strpos($value, "<ul class='bulleted'>") !== FALSE ) {
					$ordered_values = [];
					foreach( $real_value as $choice_id => $choice_value ) {
						if ( !empty($choice_value) ) {
							$ordered_values[] = $choice_value;
						}
					}
				}

				if (is_array($ordered_values)) {
					$return_strings = array();
					$return_images = array();
					foreach ($ordered_values as $ordered_value) {
						if ($field->type != 'option') {
							$image = $this->get_choice_image_src($field, $ordered_value);
							$text = RGFormsModel::get_choice_text($field, $ordered_value);
							if ($field_entry_value_type == 'text') {
								array_push($return_strings, $text);
							}
							else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
								if ($field_entry_value_type == 'src') {
									array_push($return_images, $image);
								}
								else {
									$image_item_markup = '';
									if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
										$image_item_markup = $this->get_choice_image_item_markup($image);
									}
									else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_text_price') {
										$image_item_markup = $this->get_choice_image_item_markup($image, $text);
									}
									else if ($field_entry_value_type == 'image_value') {
										$image_item_markup = $this->get_choice_image_item_markup($image, $ordered_value);
									}
									array_push($return_images, $image_item_markup);
								}
							}
						}
						else {
							// product option field CHECKBOX - saved as
							// Value|Price
							// Eg: Choice Value|0

							list($name, $price) = explode("|", $ordered_value);
							$image = $this->get_choice_image_src($field, $name);
							$text = RGFormsModel::get_choice_text($field, $name);

							if ($field_entry_value_type == 'text') {
								array_push($return_strings, $text);
							}
							else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
								if ($field_entry_value_type == 'src') {
									array_push($return_images, $image);
								}
								else {
									$image_item_markup = '';
									if ($field_entry_value_type == 'image') {
										$image_item_markup = $this->get_choice_image_item_markup($image);
									}
									else if ($field_entry_value_type == 'image_text') {
										$image_item_markup = $this->get_choice_image_item_markup($image, $text);
									}
									else if ($field_entry_value_type == 'image_text_price') {
										if ( !empty($price) ) {
											$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')' );
										}
										else {
											$image_item_markup = $this->get_choice_image_item_markup($image, $text);
										}
									}
									else if ($field_entry_value_type == 'image_price') {
										if ( !empty($price) ) {
											$image_item_markup = $this->get_choice_image_item_markup($image, GFCommon::format_number($price, 'currency'));
										}
										else {
											$image_item_markup = $this->get_choice_image_item_markup($image);
										}
									}
									else if ($field_entry_value_type == 'image_value') {
										$image_item_markup = $this->get_choice_image_item_markup($image, $name);
									}
									array_push($return_images, $image_item_markup);
								}
							}

						}
					}

					if ($field_entry_value_type != 'src' && in_array($field_entry_value_type, $image_entry_setting_values) && !empty($return_images)) {
						$value = $this->wrap_choice_images_markup($return_images);
					}
					else {
						$value = implode(', ', $return_strings);
					}
				}

			}
			else {

				// either a radio, or a checkbox with a single selection
				if ($field->type == 'checkbox' || ( $field->type == 'post_custom_field' && $field->inputType == 'checkbox' )) {

					// When on the View Entry page, checkbox field is unordered list HTML
					// so just grab the real values
					$checkbox_text_values = $field->get_value_entry_detail($real_value, '', true, 'text');

					$return_strings = array();
					$return_images = array();

					foreach ($real_value as $key => $choice_value) {
						if (!empty($choice_value)) {
							$image = $this->get_choice_image_src($field, $choice_value);
							$text = RGFormsModel::get_choice_text($field, $choice_value);

							if ($field_entry_value_type == 'text') {
								array_push($return_strings, $text);
							}
							else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
								if ($field_entry_value_type == 'src') {
									array_push($return_strings, $image);
								}
								else {
									$image_item_markup = '';
									if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
										$image_item_markup = $this->get_choice_image_item_markup($image);
									}
									else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_text_price') {
										$image_item_markup = $this->get_choice_image_item_markup($image, $text);
									}
									else if ($field_entry_value_type == 'image_value') {
										$image_item_markup = $this->get_choice_image_item_markup($image, $choice_value);
									}
									array_push($return_images, $image_item_markup);
								}
							}

						}
					}

					if ($field_entry_value_type != 'src' && in_array($field_entry_value_type, $image_entry_setting_values) && !empty($return_images)) {
						$value = $this->wrap_choice_images_markup($return_images);
					}
					else {
						if (!empty($return_strings)) {
							$markup = '<ul class="bulleted">';
							foreach ($return_strings as $return_value) {
								$markup .= '<li>' . $return_value . '</li>';
							}
							$markup .= '</ul>';
							$value = $markup;
						}
						else {
							$value = implode(', ', $return_strings);
						}
					}

				}
				else if ($field->type == 'quiz' && $field->is_entry_detail()) {

					// Can only show text (choice label) or image, or both.
					// Can't show value - doesn't let user set it anyway, it's a unique id

					$choices_data = array();
					foreach ($field->choices as $choice) {
						array_push($choices_data, array(
							'text' => $choice['text'],
							'value' => $choice['value'],
							'image' => (isset($choice['imageChoices_image'])) ? $choice['imageChoices_image'] : '',
							'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : ''
						));
					}

					// Taken from GFQuiz::display_quiz_on_entry_detail (class-gf-quiz.php)
					$new_value = '';
					$new_value .= '<div class="gquiz_entry">';
					$results = gf_quiz()->get_quiz_results($form, $entry, false);
					$field_markup = '';

					foreach ($results['fields'] as $field_results) {
						if ($field_results['id'] == $field->id) {
							$field_markup = $field_results['markup'];
							break;
						}
					}

					if (in_array($field_entry_value_type, $image_entry_setting_values)) {
						// Modify the markup for image choices
						$field_markup = str_replace('<ul>', '<ul class="gf-image-choices-entry">', $field_markup);
						$field_markup = str_replace('<li>', '<li class="gf-image-choices-entry-choice">', $field_markup);
						foreach ($choices_data as $choice) {
							$replacement_markup = '';
							if ($field_entry_value_type == 'src') {
								$replacement_markup = $choice['image'];
							}
							else if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
								$replacement_markup = '<span class="gf-image-choices-entry-choice-image" style="background-image:url(' . $choice['image'] . ')"></span>';
							}
							else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value' || $field_entry_value_type == 'image_text_price') {
								$replacement_markup = '<span class="gf-image-choices-entry-choice-image" style="background-image:url(' . $choice['image'] . ')"></span><span class="gf-image-choices-entry-choice-text">' . html_entity_decode($choice['text']) . '</span>';
							}
							$field_markup = str_replace($choice['text'], $replacement_markup, $field_markup);
						}
					}

					$new_value .= $field_markup;
					$new_value .= '</div>';

					$value = $new_value;

				}
				else if ($field->type == 'poll' && $field->is_entry_detail()) {

					$choices_data = array();
					foreach ($field->choices as $choice) {
						array_push($choices_data, array(
							'id' => $choice['id'],
							'text' => $choice['text'],
							'value' => $choice['value'],
							'image' => (isset($choice['imageChoices_image'])) ? $choice['imageChoices_image'] : '',
							'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : ''
						));
					}

					// Taken from GFPolls::display_poll_on_entry_detail (class-gf-polls.php)
					$results = gf_polls()->gpoll_get_results($form['id'], $field->id, 'green', true, true, $entry);
					$new_value = sprintf('<div class="gpoll_entry">%s</div>', rgar($results, 'summary'));
					gf_polls()->gpoll_add_scripts = true;

					//if original response is not in results display below
					$selected_values = gf_polls()->get_selected_values($form['id'], $field->id, $entry);
					$possible_choices = gf_polls()->get_possible_choices($form['id'], $field->id);
					foreach ($selected_values as $selected_value) {
						if (!in_array($selected_value, $possible_choices)) {
							$new_value = sprintf('%s<h2>%s</h2>%s', $new_value, esc_html__('Original Response', 'gravityformspolls'), $value);
							break;
						}
					}

					if (in_array($field_entry_value_type, $image_entry_setting_values)) {
						// Now modify the markup for image choices
						$new_value = str_replace('<div class="gpoll_entry">', '<div class="gpoll_entry gf-image-choices-entry">', $new_value);
						foreach ($choices_data as $choice) {
							$replacement_markup = '';
							if ($field_entry_value_type == 'src') {
								$replacement_markup = '<div class="gf-image-choices-entry-choice">'.$choice['image'].'</div>';
							}
							else if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
								$replacement_markup = '<div class="gf-image-choices-entry-choice"><span class="gf-image-choices-entry-choice-image" style="background-image:url(' . $choice['image'] . ')"></span></div>';
							}
							else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value' || $field_entry_value_type == 'image_text_price') {
								// Don't return value for poll fields. IF that's selected, use text
								$replacement_markup = '<div class="gf-image-choices-entry-choice"><span class="gf-image-choices-entry-choice-image" style="background-image:url(' . $choice['image'] . ')"></span><span class="gf-image-choices-entry-choice-text">' . html_entity_decode($choice['text']) . '</span></div>';
							}
							$new_value = str_replace($choice['text'], $replacement_markup, $new_value);
						}
					}

					$value = $new_value;

				}
				else if ($field->type == 'survey' && $field->is_entry_detail()) {

					if ($field[$type_property] == 'checkbox') {

						$return_strings = array();
						$return_images = array();

						foreach ($real_value as $key => $choice_value) {
							if (!empty($choice_value)) {
								$image = $this->get_choice_image_src($field, $choice_value);
								$text = RGFormsModel::get_choice_text($field, $choice_value);
								if ($field_entry_value_type == 'text') {
									array_push($return_strings, $text);
								}
								else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
									if ($field_entry_value_type == 'src') {
										array_push($return_images, $image);
									}
									else {
										$image_item_markup = '';
										if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
											$image_item_markup = $this->get_choice_image_item_markup($image);
										}
										else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value' || $field_entry_value_type == 'image_text_price') {
											// Don't return value for survey fields. IF that's selected, use text
											$image_item_markup = $this->get_choice_image_item_markup($image, $text);
										}
										/*
										else if ($field_entry_value_type == 'image_value') {
											$image_item_markup = $this->get_choice_image_item_markup($image, $choice_value);
										}
										*/
										array_push($return_images, $image_item_markup);
									}
								}
							}
						}

						if ($field_entry_value_type != 'src' && in_array($field_entry_value_type, $image_entry_setting_values) && !empty($return_images)) {
							$value = $this->wrap_choice_images_markup($return_images);
						}
						else {
							if (!empty($return_strings)) {
								$markup = '<ul class="bulleted">';
								foreach ($return_strings as $return_value) {
									$markup .= '<li>' . $return_value . '</li>';
								}
								$markup .= '</ul>';
								$value = $markup;
							}
							else {
								$value = implode(', ', $return_strings);
							}
						}

					}
					else {

						$image = $this->get_choice_image_src($field, $real_value);
						$text = RGFormsModel::get_choice_text($field, $real_value);

						if ($field_entry_value_type == 'text') {
							$value = $text;
						}
						else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
							if ($field_entry_value_type == 'src') {
								$value = $image;
							}
							else {
								$image_item_markup = '';
								if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value' || $field_entry_value_type == 'image_text_price') {
									// Don't return value for survey fields. IF that's selected, use text
									$image_item_markup = $this->get_choice_image_item_markup($image, $text);
								}
								/*
								else if ($field_entry_value_type == 'image_value') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $real_value);
								}
								*/
								$value = $this->wrap_choice_images_markup(array($image_item_markup));
							}
						}

					}

				}
				else if ($field->type == 'radio'
					|| $field->type == 'post_custom_field'
					|| ($field->type == 'quiz' && !$field->is_entry_detail())
					|| ($field->type == 'poll' && !$field->is_entry_detail())
					|| ($field->type == 'survey' && !$field->is_entry_detail())) {

					$image = $this->get_choice_image_src($field, $value);
					$text = RGFormsModel::get_choice_text($field, $value);

					// Don't show value for quiz, survey and poll fields
					$force_text_instead_of_value = ($field->type == 'quiz' || $field->type == 'poll' || $field->type == 'survey');

					if ($field_entry_value_type == 'text') {
						$value = $text;
					}
					else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
						if ($field_entry_value_type == 'src') {
							$value = $image;
						}
						else {
							$image_item_markup = '';
							if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
								$image_item_markup = $this->get_choice_image_item_markup($image);
							}
							else if ($field_entry_value_type == 'image_text' || ($force_text_instead_of_value && $field_entry_value_type == 'image_value') || $field_entry_value_type == 'image_text_price') {
								$image_item_markup = $this->get_choice_image_item_markup($image, $text);
							}
							else if ($field_entry_value_type == 'image_value') {
								$image_item_markup = $this->get_choice_image_item_markup($image, $value);
							}
							$value = $this->wrap_choice_images_markup(array($image_item_markup));
						}
					}

				}
				else if ($field->type == 'product') {

					// product field IF NOT FREE - single selection - saved as
					// Value ($Price)
					// Eg: Choice Value ($20.00)

					// product field IF FREE - single selection - saved as
					// Value
					// Eg: Choice Value

					if (strpos($value, '(') === FALSE && strpos($value, ')') === FALSE) {
						// FREE
						$image = $this->get_choice_image_src($field, $value);
						$text = RGFormsModel::get_choice_text($field, $value);
						if ($field_entry_value_type == 'text') {
							$value = $text;
						}
						else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
							if ($field_entry_value_type == 'src') {
								$value = $image;
							}
							else {
								$image_item_markup = '';
								if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_text_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $text);
								}
								else if ($field_entry_value_type == 'image_value') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $value);
								}
								$value = $this->wrap_choice_images_markup(array($image_item_markup));
							}
						}
					}
					else {
						// NOT FREE
						$value_without_price = trim(substr($value, 0, strrpos($value, '(')));
						preg_match('#\((.*?)\)#', $value, $price_str_match);
						$price = $price_str_match[1];
						$image = $this->get_choice_image_src($field, $value_without_price);
						$text = RGFormsModel::get_choice_text($field, $value_without_price);
						if ($field_entry_value_type == 'text') {
							$value = $text;
						}
						else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
							if ($field_entry_value_type == 'src') {
								$value = $image;
							}
							else {
								$image_item_markup = '';
								if ($field_entry_value_type == 'image') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $text);
								}
								else if ($field_entry_value_type == 'image_text_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . $price . ')');
									//$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')');
								}
								else if ($field_entry_value_type == 'image_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $price);
									//$image_item_markup = $this->get_choice_image_item_markup($image, GFCommon::format_number($price, 'currency'));
								}
								else if ($field_entry_value_type == 'image_value') {
									//$image_item_markup = $this->get_choice_image_item_markup($image, $value_without_price);
									$image_item_markup = $this->get_choice_image_item_markup($image, $value);
								}
								$value = $this->wrap_choice_images_markup(array($image_item_markup));
							}
						}
					}
				}
				else if ($field->type == 'option') {

					// product option field RADIO - single selection - saved as
					// Value ($Price)
					// Eg: Choice Value ($20.00)

					// product option field CHECKBOX - single selection - saved as
					// Value|Price
					// Eg: Choice Value|0

					if (strpos($value, '|') === FALSE) {
						// RADIO
						$value_without_price = (strpos($value, '(') !== FALSE) ? trim(substr($value, 0, strrpos($value, '('))) : $value;
						preg_match('#\((.*?)\)#', $value, $price_str_match);
						$price = $price_str_match[1];
						$image = $this->get_choice_image_src($field, $value_without_price);
						$text = RGFormsModel::get_choice_text($field, $value_without_price);

						if ($field_entry_value_type == 'text') {
							$value = $text;
						}
						else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
							if ($field_entry_value_type == 'src') {
								$value = $image;
							}
							else {
								$image_item_markup = '';
								if ($field_entry_value_type == 'image') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $text);
								}
								else if ($field_entry_value_type == 'image_text_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . $price . ')');
									//$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')');
								}
								else if ($field_entry_value_type == 'image_price') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $price);
									//$image_item_markup = $this->get_choice_image_item_markup($image, GFCommon::format_number($price, 'currency'));
								}
								else if ($field_entry_value_type == 'image_value') {
									//$image_item_markup = $this->get_choice_image_item_markup($image, $value_without_price);
									$image_item_markup = $this->get_choice_image_item_markup($image, $value);
								}
								$value = $this->wrap_choice_images_markup(array($image_item_markup));
							}
						}
					}
					else {
						// CHECKBOX
						list($name, $price) = explode("|", $value);
						$image = $this->get_choice_image_src($field, $name);
						$text = RGFormsModel::get_choice_text($field, $name);

						if ($field_entry_value_type == 'text') {
							$value = $text;
						}
						else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {
							if ($field_entry_value_type == 'src') {
								$value = $image;
							}
							else {
								$image_item_markup = '';
								if ($field_entry_value_type == 'image') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $text);
								}
								else if ($field_entry_value_type == 'image_text_price') {
									if ( !empty($price) ) {
										$image_item_markup = $this->get_choice_image_item_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')');
									}
									else {
										$image_item_markup = $this->get_choice_image_item_markup($image, $text);
									}
								}
								else if ($field_entry_value_type == 'image_price') {
									if ( !empty($price) ) {
										$image_item_markup = $this->get_choice_image_item_markup($image, GFCommon::format_number($price, 'currency'));
									}
									else {
										$image_item_markup = $this->get_choice_image_item_markup($image);
									}
								}
								else if ($field_entry_value_type == 'image_value') {
									$image_item_markup = $this->get_choice_image_item_markup($image, $name);
								}
								$value = $this->wrap_choice_images_markup(array($image_item_markup));
							}
						}
					}

				}

			}

			return $value;
		}

		return $value;
	}

	/**
	 * Add the image-choices-field class to the fields where images are enabled.
	 *
	 * @param string $classes The CSS classes to be filtered, separated by empty spaces.
	 * @param GF_Field $field The field currently being processed.
	 * @param array $form The form currently being processed.
	 *
	 * @return string
	 */
	public function add_custom_class( $classes, $field, $form ) {
		if ( property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages ) {

			$classes .= (GFCommon::is_form_editor()) ? ' image-choices-admin-field ' : ' image-choices-field ';
			$classes .= 'image-choices-use-images ';
			if ( property_exists($field, 'imageChoices_showLabels') && $field->imageChoices_showLabels ) {
				$classes .= 'image-choices-show-labels ';
			}
			if ( property_exists($field, 'imageChoices_useLightbox') && $field->imageChoices_useLightbox ) {
				$classes .= 'image-choices-use-lightbox ';
			}


			$lazy_load_global_value = $this->get_plugin_setting('gf_image_choices_lazy_load_global');
			if ( empty($lazy_load_global_value) ) {
				$lazy_load_global_value = 0;
			}
			$form_settings = $this->get_form_settings( $form );
			$form_lazy_load_value = (isset($form_settings['gf_image_choices_lazy_load'])) ? $form_settings['gf_image_choices_lazy_load'] : $lazy_load_global_value;
			$lazy_load = ( $form_lazy_load_value === '' ) ? $lazy_load_global_value : $form_lazy_load_value;

			if ( !empty($lazy_load) && !is_admin() ) {
				$classes .= 'has-jetsloth-lazy ';
			}

			/*
			if ( property_exists($field, 'imageChoices_choicesLayout') && $field->imageChoices_choicesLayout ) {
				$classes .= 'image-choices-layout-'.$field->imageChoices_choicesLayout.' ';
			}
			*/
		}

		return $classes;
	}

	/**
	 * Add the tooltips for the field.
	 *
	 * @param array $tooltips An associative array of tooltips where the key is the tooltip name and the value is the tooltip.
	 *
	 * @return array
	 */
	public function add_image_choice_field_tooltips( $tooltips ) {
		$tooltips['image_choices_use_images'] = '<h6>' . esc_html__( 'Use Images', 'gf_image_choices' ) . '</h6>' . esc_html__( 'Enable to use of images as choices.', 'gf_image_choices' );
		$tooltips['image_choices_show_labels'] = '<h6>' . esc_html__( 'Show Labels', 'gf_image_choices' ) . '</h6>' . esc_html__( 'Enable the display of the labels together with the image.', 'gf_image_choices' );
		$tooltips['image_choices_use_lightbox'] = '<h6>' . esc_html__( 'Use Lightbox', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the user will be able to preview large versions of each image in a lightbox.', 'gf_image_choices' );
		$tooltips['image_choices_show_prices'] = '<h6>' . esc_html__( 'Show Prices', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the product price will be displayed below the image.', 'gf_image_choices' );
		$tooltips['image_choices_show_labels'] = '<h6>' . esc_html__( 'Show Labels', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the choices labels will be displayed along with the image.', 'gf_image_choices' );
		return $tooltips;
	}

	/**
	 * Add the custom settings for the fields to the fields general tab.
	 *
	 * @param int $position The position the settings should be located at.
	 * @param int $form_id The ID of the form currently being edited.
	 */
	public function image_choice_field_settings( $position, $form_id ) {
		$pos = ( GFIC_GF_MIN_2_5 ) ? 1350 : 1375;
		if ( $position == $pos ) {
			?>
            <!-- Image Choices Toggle -->
            <li class="image-choices-setting-use-images field_setting">
				<?php if ( !GFIC_GF_MIN_2_5 ): ?><label class="section_label"><?php esc_html_e("Image Choices", 'gf_image_choices'); ?></label><?php endif; ?>
                <input type="checkbox" id="field_choice_images_enabled" class="field_choice_images_enabled" onclick="imageChoicesAdmin.toggleEnableImages(this.checked);" onkeypress="imageChoicesAdmin.toggleEnableImages(this.checked);"> <label for="field_choice_images_enabled"><?php echo GFIC_GF_MIN_2_5 ? esc_html__("Use Image Choices", 'gf_image_choices') : esc_html__("Use images", 'gf_image_choices'); ?></label>
            </li>
			<?php
			//wp_enqueue_media();// For Media Library
		}
	}

	public function custom_settings_tab( $tabs ) {
		$tabs[] = array(
			'id' => 'image_choices', // Tab id is used later with the action hook that will output content for this specific tab.
			'title' => esc_html__('Image Choices', 'gf_image_choices'),
			//'toggle_classes' => '', // Goes into the tab button class attribute.
			//'body_classes'   => '', // Goes into the tab content ( ul tag ) class attribute.
		);

		return $tabs;
	}

	public function custom_settings_markup( $form ) {
		?>
        <!-- Image Choices Label Display Settings -->
        <li class="image-choices-setting-show-labels field_setting">
			<?php if ( !GFIC_GF_MIN_2_5 ): ?><label class="section_label"><?php esc_html_e("Image Choices Display", 'gf_image_choices'); ?></label><?php endif; ?>
            <input id="image_choices_show_labels" class="image_choices_show_labels" type="checkbox" onclick="imageChoicesAdmin.toggleShowLabels(this.checked);" onkeypress="imageChoicesAdmin.toggleShowLabels(this.checked);"> <label for="image_choices_show_labels"><?php echo esc_html__("Show labels", 'gf_image_choices'); gform_tooltip('image_choices_show_labels') ?></label>
        </li>
        <!-- Image Choices Price Display Setting -->
        <li class="image-choices-setting-show-prices field_setting">
			<?php if ( !GFIC_GF_MIN_2_5 ): ?><label class="section_label"><?php esc_html_e("Image Choices Prices", 'gf_image_choices'); ?></label><?php endif; ?>
            <input id="image_choices_show_prices" class="image_choices_show_prices" type="checkbox" onclick="imageChoicesAdmin.toggleShowPrices(this.checked);" onkeypress="imageChoicesAdmin.toggleShowPrices(this.checked);"> <label for="image_choices_show_prices"><?php echo esc_html__("Show prices", 'gf_image_choices'); gform_tooltip('image_choices_show_prices'); ?></label>
        </li>
        <!-- Image Choices Entry / Notification Display Settings -->
        <li class="image-choices-setting-entry-value field_setting">
            <label for="image-choices-entry-value" class="section_label"><?php echo GFIC_GF_MIN_2_5 ? esc_html__("Entry / Notification Display") : esc_html__("Image Choices Entry / Notification Display"); ?></label>
            <select id="image-choices-entry-value" class="image-choices-entry-value" onchange="imageChoicesAdmin.updateEntrySetting(this.value);">
                <option value="form_setting"><?php esc_html_e("Use Form Setting", 'gf_image_choices'); ?></option>
                <option value="value"><?php esc_html_e("Value", 'gf_image_choices'); ?></option>
                <option value="image"><?php esc_html_e("Image", 'gf_image_choices'); ?></option>
                <option value="text"><?php esc_html_e("Text", 'gf_image_choices'); ?></option>
                <option value="image_text"><?php esc_html_e("Image and Label", 'gf_image_choices'); ?></option>
                <option value="image_text_price"><?php esc_html_e("Image, Label and Price (Product or Option fields only)", 'gf_image_choices'); ?></option>
                <option value="image_price"><?php esc_html_e("Image and Price (Product or Option fields only)", 'gf_image_choices'); ?></option>
                <option value="image_value"><?php esc_html_e("Image and Value", 'gf_image_choices'); ?></option>
            </select>
        </li>
        <!-- Image Choices Lightbox Setting -->
        <li class="image-choices-setting-use-lightbox field_setting">
			<?php if ( !GFIC_GF_MIN_2_5 ): ?><label class="section_label"><?php esc_html_e("Image Choices Lightbox", 'gf_image_choices'); ?></label><?php endif; ?>
            <input id="image_choices_use_lightbox" class="image_choices_use_lightbox" type="checkbox" onclick="imageChoicesAdmin.toggleUseLightbox(this.checked);" onkeypress="imageChoicesAdmin.toggleUseLightbox(this.checked);"> <label for="image_choices_use_lightbox"><?php echo esc_html__("Use lightbox", 'gf_image_choices'); gform_tooltip('image_choices_use_lightbox'); ?></label>
        </li>
		<?php
	}

	public function image_choices_field_appearance_settings( $position, $form_id ) {
		if ( $position == 500 ) {
			$this->custom_settings_markup( GFAPI::get_form( $form_id ) );
		}
	}


	public function add_image_options_markup( $choice_markup, $choice, $field, $value ) {
		if (  property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages ) {
			$img = (isset($choice['imageChoices_image'])) ? $choice['imageChoices_image'] : '';
			$imgID = (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : '';

			$form = GFAPI::get_form( $field->formId );
			$form_settings = $this->get_form_settings($form);
			$form_lightbox_size = (isset($form_settings['gf_image_choices_lightbox_size'])) ? $form_settings['gf_image_choices_lightbox_size'] : 'full';

			$lazy_load_global_value = $this->get_plugin_setting('gf_image_choices_lazy_load_global');
			if ( empty($lazy_load_global_value) ) {
				$lazy_load_global_value = 0;
			}
			$form_lazy_load_value = (isset($form_settings['gf_image_choices_lazy_load'])) ? $form_settings['gf_image_choices_lazy_load'] : $lazy_load_global_value;
			$lazy_load = ( $form_lazy_load_value === '' ) ? $lazy_load_global_value : $form_lazy_load_value;


			if (empty($img)) {
				$img = '';
			}
			$img = str_replace('$', '\$', $img);

			$lightboxImg = (!empty($imgID)) ? wp_get_attachment_image_src($imgID, $form_lightbox_size) : '';
			if (!empty($lightboxImg)) {
				$lightboxImg = $lightboxImg[0];
			}
			$lightboxImg = str_replace('$', '\$', $lightboxImg);

			if ( ($field->type == 'checkbox' || $field->optionType == 'checkbox') && empty($choice) ) {
				// if this condition is met, it's the 'Select all' option
				return $choice_markup;
			}

			if ( !empty($lazy_load ) && !is_admin() ) {
				$img_markup = implode("", array(
					'<span class="image-choices-choice-image-wrap jetsloth-lazy" data-lazy-bg="' . $img . '">',
					'<img src="" data-lazy-src="' . $img . '" alt="" class="image-choices-choice-image jetsloth-lazy" data-lightbox-src="' . $lightboxImg . '" />',
					'</span>',
				));
			}
			else {
				$img_markup = implode("", array(
					'<span class="image-choices-choice-image-wrap" style="background-image:url('.$img.')">',
					'<img src="'.$img.'" alt="" class="image-choices-choice-image" data-lightbox-src="'.$lightboxImg.'" />',
					'</span>',
				));
			}

			if ($field->type == 'product' && !GFCommon::is_form_editor() && property_exists($field, 'imageChoices_showPrices') && $field->imageChoices_showPrices) {
				$choice_price = str_replace('$', '\$', $choice['price']);
				$choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
					'<label ${1} >',
					$img_markup,
					'<span class="image-choices-choice-text">${2}</span>',
					'<span class="image-choices-choice-price">',
					'<span class="ginput_price"> '.$choice_price.'</span>',
					'</span>',
					'</label>'
				)), $choice_markup);
			}
			else if ($field->type != 'option' || GFCommon::is_form_editor()) {
				$choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
					'<label ${1} >',
					$img_markup,
					'<span class="image-choices-choice-text">${2}</span>',
					'</label>'
				)), $choice_markup);
			}
			else {
			    // OPTION FIELD
				$choice_markup = str_replace('<label', '<label data-img="'.$img.'" data-lightbox-src="'.$lightboxImg.'"', $choice_markup);
			}

			return apply_filters( 'gfic_choice_html', $choice_markup );
		}

		return $choice_markup;
	}


	public function gfpdf_format_image_item_markup( $html, $size ) {

		$html = str_replace( 'width:80px;', 'width:'.$size.';', $html );
		$html = str_replace( ['<span', 'span>'], ['<div', 'div>'], $html );
		$html = str_replace( 'margin: 0 10px 20px; text-align: center;', 'margin: 0 10px 20px; text-align: left; width:'.$size.'; float:left;', $html );
		$html = str_replace( 'font-size: 12px;', 'font-size: 12px; text-align: center;', $html );

		return $html;

	}


	public function gfpdf_ic_field_content( $value, $field, $entry, $form, $image_width = 80 ) {

		if ( empty($field) || !is_object($field) || !property_exists($field, 'imageChoices_enableImages') || !$field->imageChoices_enableImages ) {
			return $value;
		}

		$size = $image_width."px";
		$value = $this->maybe_format_field_values( $value, $field, $form, $entry );
		$value = $this->gfpdf_format_image_item_markup( $value, $size );

		return $value;

	}

	public function gfpdf_ic_field_product_value( $html, $products, $field, $form, $entry, $image_width = 80 ) {

		$form_id = rgar($form, "id");
		$size = $image_width."px";

		foreach ( $products['products'] as $field_id => $product ) {

			// First see if the Product field has images
			$product_field = RGFormsModel::get_field( $form_id, $field_id );

			if ( property_exists($product_field, 'imageChoices_enableImages') && !empty($product_field->imageChoices_enableImages) ) {
				// if the product field is using image choices, it's a radio button field
				$product_value = $product_field->get_value_export( $entry );
				$product_image = $this->get_choice_image_src( $product_field, $product_value );

				// CUSTOM HTML
				//$product_html = $product['name'];
				//$product_html .= '<div><img src="'.$product_image.'" style="width:'.$size.'; height:auto; max-width:100%;" /></div>';

				// DEFAULT HTML
				$product_html = $this->get_choice_image_item_markup( $product_image, $product['name'] );
				$product_html = $this->gfpdf_format_image_item_markup( $product_html, $size );

				$html = preg_replace( '/<div class="product_name">\s*'.$product['name'].'\s*<\/div>/', '<div class="product_name">'.$product_html.'</div>', $html );

			}

			// Then see if the Option fields have images
			if ( count( $product['options'] ) > 0 ) {
				foreach ( $product['options'] as $option ) {

					$option_field = RGFormsModel::get_field( $form_id, $option['id'] );
					if ( !property_exists($option_field, 'imageChoices_enableImages') || empty($option_field->imageChoices_enableImages) ) {
						// if Image Choices not enabled on this option, continue to next
						continue;
					}

					$option_image = $this->get_choice_image_src( $option_field, $option['option_name'], 'text' );

					// CUSTOM HTML
					//$option_html = $option['option_label'];
					//$option_html .= '<div><img src="'.$option_image.'" style="width:'.$size.'; height:auto; max-width:100%;" /></div>';

					// DEFAULT HTML
					$option_html = gf_image_choices()->get_choice_image_item_markup( $option_image, $option['option_label'] );
					$option_html = $this->gfpdf_format_image_item_markup( $option_html, $size );

					$html = preg_replace( '/<li>\s*'.$option['option_label'].'\s*<\/li>/', '<li>'.$option_html.'</li>', $html );

				}
			}
		}

		return $html;

	}

	/**
	 * Add custom messages after plugin row based on license status
	 */

	public function gf_plugin_row($plugin_file='', $plugin_data=array(), $status='') {
		$row = array();
		$license_key = trim($this->get_plugin_setting('gf_image_choices_license_key'));
		$license_status = get_option('gf_image_choices_license_status', '');
		if (empty($license_key) || empty($license_status)) {
			$row = array(
				'<tr class="plugin-update-tr">',
				'<td colspan="3" class="plugin-update gf_image_choices-plugin-update">',
				'<div class="update-message">',
				'<a href="' . admin_url('admin.php?page=gf_settings&subview=' . $this->_slug) . '">Activate</a> your license to receive plugin updates and support. Need a license key? <a href="' . $this->_url . '" target="_blank">Purchase one now</a>.',
				'</div>',
				'<style type="text/css">',
				'.plugin-update.gf_image_choices-plugin-update .update-message:before {',
				'content: "\f348";',
				'margin-top: 0;',
				'font-family: dashicons;',
				'font-size: 20px;',
				'position: relative;',
				'top: 5px;',
				'color: orange;',
				'margin-right: 8px;',
				'}',
				'.plugin-update.gf_image_choices-plugin-update {',
				'background-color: #fff6e5;',
				'}',
				'.plugin-update.gf_image_choices-plugin-update .update-message {',
				'margin: 0 20px 6px 40px !important;',
				'line-height: 28px;',
				'}',
				'</style>',
				'</td>',
				'</tr>'
			);
		}
        elseif(!empty($license_key) && $license_status != 'valid') {
			$row = array(
				'<tr class="plugin-update-tr">',
				'<td colspan="3" class="plugin-update gf_image_choices-plugin-update">',
				'<div class="update-message">',
				'Your license is invalid or expired. <a href="'.admin_url('admin.php?page=gf_settings&subview='.$this->_slug).'">Enter valid license key</a> or <a href="'.$this->_url.'" target="_blank">purchase a new one</a>.',
				'<style type="text/css">',
				'.plugin-update.gf_image_choices-plugin-update .update-message:before {',
				'content: "\f348";',
				'margin-top: 0;',
				'font-family: dashicons;',
				'font-size: 20px;',
				'position: relative;',
				'top: 5px;',
				'color: #d54e21;',
				'margin-right: 8px;',
				'}',
				'.plugin-update.gf_image_choices-plugin-update {',
				'background-color: #ffe5e5;',
				'}',
				'.plugin-update.gf_image_choices-plugin-update .update-message {',
				'margin: 0 20px 6px 40px !important;',
				'line-height: 28px;',
				'}',
				'</style>',
				'</div>',
				'</td>',
				'</tr>'
			);
		}

		echo implode('', $row);
	}



	/**
	 * Determine if the license key is valid so the appropriate icon can be displayed next to the field.
	 *
	 * @param string $value The current value of the license_key field.
	 * @param array $field The field properties.
	 *
	 * @return bool|null
	 */
	public function license_feedback( $value, $field ) {
		if ( empty( $value ) ) {
			return null;
		}

		// Send the remote request to check the license is valid
		$license_data = $this->perform_edd_license_request( 'check_license', $value );

		$valid = null;
		if ( empty( $license_data ) || !is_object($license_data) || !property_exists($license_data, 'license') || $license_data->license == 'invalid' ) {
			$valid = false;
		}
        elseif ( $license_data->license == 'valid' ) {
			$valid = true;
		}

		if (!empty($license_data) && is_object($license_data) && property_exists($license_data, 'license')) {
			update_option('gf_image_choices_license_status', $license_data->license);
		}

		return $valid;
	}


	/**
	 * Handle license key activation or deactivation.
	 *
	 * @param array $field The field properties.
	 * @param string $field_setting The submitted value of the license_key field.
	 */
	public function license_validation( $field, $field_setting ) {
		$old_license = $this->get_plugin_setting( 'gf_image_choices_license_key' );

		if ( $old_license && $field_setting != $old_license ) {
			// Send the remote request to deactivate the old license
			$response = $this->perform_edd_license_request( 'deactivate_license', $old_license );
			if ( !empty($response) && is_object($response) && property_exists($response, 'license') && $response->license == 'deactivated' ) {
				delete_option('gf_image_choices_license_status');
			}
		}

		if ( ! empty( $field_setting ) ) {
			// Send the remote request to activate the new license
			$response = $this->perform_edd_license_request( 'activate_license', $field_setting );
			if ( !empty($response) && is_object($response) && property_exists($response, 'license') ) {
				update_option('gf_image_choices_license_status', $response->license);
			}
		}
	}


	/**
	 * Send a request to the EDD store url.
	 *
	 * @param string $edd_action The action to perform (check_license, activate_license or deactivate_license).
	 * @param string $license The license key.
	 *
	 * @return object
	 */
	public function perform_edd_license_request( $edd_action, $license ) {

		// Prepare the request arguments
		$args = array(
			'timeout' => GFIC_TIMEOUT,
			'sslverify' => GFIC_SSL_VERIFY,
			'body' => array(
				'edd_action' => $edd_action,
				'license' => trim($license),
				'item_name' => urlencode(GFIC_NAME),
				'url' => home_url(),
			)
		);

		// Send the remote request
		$response = wp_remote_post(GFIC_HOME, $args);

		return json_decode( wp_remote_retrieve_body( $response ) );
	}


	public function debug_output($data = '', $background='black', $color='white') {
		echo '<pre style="padding:20px; background:'.$background.'; color:'.$color.';">';
		print_r($data);
		echo '</pre>';
	}


} // end class
