<?php

//------------------------------------------

GFForms::include_addon_framework();

class GFImageChoices extends GFAddOn {

	protected $_version = GFIC_VERSION;
	protected $_min_gravityforms_version = '2.6';

	protected $_slug = GFIC_SLUG;
	protected $_path = 'gf-image-choices/gf-image-choices.php';
	protected $_full_path = __FILE__;
	protected $_title = GFIC_NAME;
	protected $_short_title = 'Image Choices';
	protected $_url = 'https://jetsloth.com/gravity-forms-image-choices/';

    protected $_defaultTheme = "simple";
    protected $_defaultFeatureColor = "none";
    protected $_defaultFeatureColorCustom = "";
    protected $_defaultAlignment = "default";
    protected $_defaultColumns = "fixed";
    protected $_defaultImageDisplay = "default";
    protected $_defaultLightboxImageSize = "full";
    protected $_defaultImageSize = "medium";

	protected $_supported_field_types = ['radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option'];
	protected $_supported_input_types = ['radio', 'checkbox'];
	protected $_standard_merge_tags = ['all_fields', 'pricing_fields'];//'all_quiz_results'


    public function get_license_key($init = false) {
	    $key = ( defined('GF_IMAGE_CHOICES_LICENSE') ) ? GF_IMAGE_CHOICES_LICENSE : $this->get_plugin_setting( 'gf_image_choices_license_key' );
	    if ( $init && false === get_transient('gf_image_choices_license_check') ) {
		    $settings = $this->get_plugin_settings();
		    if ( false === $settings ) {
			    $settings = array();
		    }
		    $settings['gf_image_choices_license_key'] = $key;
		    $this->update_plugin_settings($settings);
		    $this->license_validation( null, $key );
		    set_transient('gf_image_choices_license_check', '1', DAY_IN_SECONDS);
	    }
        return $key;
    }

	/**
	 * Gets the supported field types and wraps them in a filter.
	 *
	 * @return array
	 */
	public function get_supported_field_types() {
		return apply_filters( 'gfic_supported_field_types', $this->_supported_field_types );
	}

	/**
	 * Gets the supported input types and wraps them in a filter.
	 *
	 * @return array
	 */
	public function get_supported_input_types() {
		return apply_filters( 'gfic_supported_input_types', $this->_supported_input_types );
	}


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


	public function use_new_features() {
		$use_legacy_setting = $this->get_plugin_setting('gf_image_choices_use_legacy_styles');
		$use_legacy_value = ( !empty($use_legacy_setting) );
        return ( false === $use_legacy_value );
	}

	/**
	 * Handles anything which requires early initialization.
	 */
	public function pre_init() {
		parent::pre_init();
	}

    public function field_supports_product_image( $field ) {
        if ( empty($field) ) {
            return false;
        }
        return is_object($field) ? ( $field->type == "product" && $field->inputType == "singleproduct" ) : ( $field['input'] === "product" && $field['inputType'] === "singleproduct" );
    }

    public function field_has_product_image_enabled( $field ) {
        return ( !empty($field) && $this->field_supports_product_image($field) && $this->get_field_settings_value("enabled", false, $field, true) );
    }

	public function form_contains_product_image_fields( $form ) {
		$has_pi = false;
		foreach( $form['fields'] as $field ) {
			if ( $this->field_has_product_image_enabled( $field ) ) {
				$has_pi = true;
				break;
			}
		}
		return $has_pi;
	}

	public function field_supports_image_choices( $field ) {
		if ( empty($field) ) {
			return false;
		}
		$is_supported_field_type = ( is_object( $field ) && property_exists($field, 'type') && in_array($field->type, $this->get_supported_field_types()) );
		$is_supported_input_type = ( in_array($field->type, $this->get_supported_input_types()) || ( property_exists($field, 'inputType') && !empty($field->inputType) && in_array($field->inputType, $this->get_supported_input_types()) ) );
		return ($is_supported_field_type && $is_supported_input_type);
	}

    public function field_has_image_choices_enabled( $field ) {
        return ( !empty($field) && $this->get_field_settings_value("enableImages", false, $field) );
    }

	public function form_contains_image_choices_fields( $form ) {
		$has_ic = false;
		foreach( $form['fields'] as $field ) {
			if ( $this->field_has_image_choices_enabled( $field ) ) {
				$has_ic = true;
				break;
			}
		}
		return $has_ic;
	}


	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {

		$this->get_license_key(true);

		// add a special class to relevant fields so we can identify them later
		add_action( 'gform_field_css_class', array( $this, 'add_custom_class' ), 10, 3 );
		add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'add_image_options_markup' ), 10, 4 );
		add_filter( 'gform_field_content', array( $this, 'add_product_image_markup' ), 100, 5 );

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

		parent::init();

	}

    // legacy compat with Gravity Wiz GPPA
	public function add_inline_options_label_lookup( $form_string, $form, $current_page ) {
        return $form_string;
	}


	/**
	 * Initialize the admin specific hooks.
	 */
	public function init_admin() {

		if ( $this->use_new_features() ) {
			GFCommon::remove_dismissible_message("gf_image_choices_legacy_mode_message");
		}
        else {
	        GFCommon::add_dismissible_message(
		        "Image Choices is currently set to legacy mode. To take advantage of the new and improved styles and features, please turn legacy mode off <a href='" . $this->get_plugin_settings_url() . "#gform-settings-section-image-choices-legacy-settings'>in settings</a> and <strong><i>test your forms</i></strong>, or <a href='" . "https://jetsloth.com/support/gravity-forms-image-choices/new-styles-and-settings/" . "' target='_blank'>check out this article</a> for more info.",
		        "gf_image_choices_legacy_mode_message",
		        'updated',
		        false,
		        true
	        );
        }

		// form editor
		add_action( 'gform_field_standard_settings', array( $this, 'image_choice_field_settings' ), 10, 2 );
		add_filter( 'gform_field_settings_tabs', array( $this, 'custom_settings_tab' ), 10, 1 );
		add_action( 'gform_field_settings_tab_content_image_choices', array( $this, 'custom_image_choices_settings_markup' ), 10, 1 );
		add_action( 'gform_field_settings_tab_content_product_image', array( $this, 'custom_product_image_settings_markup' ), 10, 1 );

		add_filter( 'gform_tooltips', array( $this, 'add_image_choice_field_tooltips' ) );

		// display results on entry list
		add_filter( 'gform_entries_field_value', array( $this, 'entries_table_field_value' ), 10, 4 );

		$name = plugin_basename($this->_path);
		add_action( 'after_plugin_row_'.$name, array( $this, 'gf_plugin_row' ), 10, 2 );


		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_footer', array($this, 'maybe_show_splash_page') );

		parent::init_admin();

	}

	public function init_ajax() {

		parent::init_ajax();

		add_action( 'wp_ajax_gf_image_choices_url_replacement', array( $this, 'ajax_url_replacement' ) );
		add_action( 'wp_ajax_gf_image_choices_get_url_replacement_form_ids', array( $this, 'ajax_get_url_replacement_form_ids' ) );

		add_action( 'wp_ajax_gf_image_choices_image_size_replacement', array( $this, 'ajax_image_size_replacement' ) );
		add_action( 'wp_ajax_gf_image_choices_get_image_size_replacement_form_ids', array( $this, 'ajax_get_image_size_replacement_form_ids' ) );
	}


	public function admin_enqueue_scripts() {
		if ( $this->is_form_editor() ) {
			wp_enqueue_media();// For Media Library
		}
        if ( $this->is_plugin_settings( $this->_slug ) || $this->is_form_settings( $this->_slug ) ) {
	        wp_enqueue_code_editor( array( 'type' => 'text/css' ) );// for custom CSS
        }
	}


	public function get_app_menu_icon() {
		return $this->get_base_url() . '/images/icons/icon-image-choices.svg';
	}

	public function get_menu_icon() {
		return $this->get_base_url() . '/images/icons/icon-image-choices.svg';
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
        $use_new_features = $this->use_new_features();

		$gf_image_choices_js_deps = array( 'jquery', 'jetsloth_lightbox' );
        $admin_js_deps = array( 'jquery', 'code-editor', 'wp-color-picker' );

        if ( $use_new_features && !is_admin() ) {
	        $gf_image_choices_js_deps[] = "jetsloth_match_height";
        }

        $admin_script = array(
	        'handle'   => 'gf_image_choices_admin',
	        'src'      => $this->get_base_url() . '/js/gf_image_choices_admin.js',
	        'version'  => $this->_version,
	        'deps'     => $admin_js_deps,
	        'callback' => array( $this, 'localize_admin_scripts' ),
	        'enqueue'  => array(
		        array( 'admin_page' => array( 'form_editor', 'plugin_settings', 'form_settings' ) ),
	        ),
        );

        $lightbox_script = array(
	        'handle'  => 'jetsloth_lightbox',
	        'src'     => $this->get_base_url() . '/js/jetsloth-lightbox.js',
	        'version' => $this->_version,
	        'deps'     => array( 'jquery' ),
	        'enqueue' => array(
		        array( 'admin_page' => array( 'entry_detail', 'entry_edit' ) ),
		        //array( 'field_types' => $this->get_supported_field_types() ),
		        array( $this, 'maybe_enqueue_main_scripts_styles' )
	        ),
        );

        $main_script = array(
	        'handle'  => 'gf_image_choices',
	        'src'     => $this->get_base_url() . '/js/gf_image_choices.js',
	        'version' => $this->_version,
	        'deps'    => $gf_image_choices_js_deps,
	        'callback' => array( $this, 'localize_scripts' ),
	        'enqueue' => array(
		        array( 'admin_page' => array( 'form_editor', 'entry_view', 'entry_detail', 'entry_edit' ) ),
		        //array( 'field_types' => $this->get_supported_field_types() ),
		        array( $this, 'maybe_enqueue_main_scripts_styles' )
	        ),
        );

        $match_height_script = array(
	        'handle'  => 'jetsloth_match_height',
	        'src'     => $this->get_base_url() . '/js/jetsloth-match-height.js',
	        'version' => $this->_version,
	        'enqueue' => array(
		        //array( 'field_types' => $this->get_supported_field_types() ),
		        array( $this, 'maybe_enqueue_main_scripts_styles' )
	        ),
        );

        if ( is_admin() ) {
	        $scripts = array(
		        $admin_script,
	        );
            if ( !$use_new_features ) {
                $scripts[] = $lightbox_script;
                $scripts[] = $main_script;
            }
        }
        else {
	        $scripts = array(
		        $lightbox_script
	        );
            if ( $use_new_features ) {
                $scripts[] = $match_height_script;
            }
            $scripts[] = $main_script;
        }

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {

		$use_new_features = $this->use_new_features();

		$admin_styles_deps = array('code-editor', 'wp-color-picker');

		$admin_styles = array(
			'handle'  => $use_new_features ? 'gf_image_choices_admin' : 'gf_image_choices_legacy_admin',
			'src'     => $use_new_features ? $this->get_base_url() . '/css/gf_image_choices_admin.css' : $this->get_base_url() . '/css/gf_image_choices_legacy_admin.css',
			'version' => $this->_version,
            'deps' => $admin_styles_deps,
			'enqueue' => array(
				array('admin_page' => array( 'form_editor', 'plugin_settings', 'form_settings', 'entry_view', 'entry_detail' )),
				array('query' => 'page=gf_entries'),
				array('query' => 'page=gf_edit_forms')
			),
		);

		$frontend_styles = array(
			'handle'  => $use_new_features ? 'gf_image_choices' : 'gf_image_choices_legacy',
			'src'     => $use_new_features ? $this->get_base_url() . '/css/gf_image_choices.css' : $this->get_base_url() . '/css/gf_image_choices_legacy.css',
			'version' => $this->_version,
			'media'   => 'screen',
			'enqueue' => array(
				//array( 'field_types' => $this->get_supported_field_types() ),
		        array( $this, 'maybe_enqueue_main_scripts_styles' )
            ),
		);

        if ( is_admin() ) {
	        $styles = $use_new_features ? array( $admin_styles ) : array( $admin_styles, $frontend_styles );
        }
        else {
	        $include_frontend_styles = apply_filters( 'gfic_enqueue_core_css', true );
            $styles = ( $use_new_features || $include_frontend_styles ) ? array( $frontend_styles ) : array();
        }

		return array_merge( parent::styles(), $styles );
	}

	public function maybe_enqueue_main_scripts_styles( $form ) {
		return( !empty($form) && ( $this->form_contains_image_choices_fields($form) || $this->form_contains_product_image_fields($form) ) );
	}


	public function maybe_enqueue_legacy_list_styles( $form ) {

		if ( $this->use_new_features() ) {
			return;
		}

		$responsive_list_css_settings_value = $this->get_plugin_setting('gf_image_choices_enqueue_responsive_list_css');
		$responsive_list_css_settings_value = ( !empty($responsive_list_css_settings_value) );

		$output_responsive_list_css = apply_filters('gfic_responsive_list_css', $responsive_list_css_settings_value);

		if ( !GFCommon::is_legacy_markup_enabled( $form ) ) {
			ob_start();
			echo file_get_contents( dirname( __FILE__ ) . '/css/gf_image_choices_legacy_list_styles_2.css' );
			$gf_list_css = ob_get_clean();
			$list_css_ref = "gf_image_choices_list_styles";
			if ( !empty($output_responsive_list_css) && !wp_style_is($list_css_ref) ) {
				wp_register_style( $list_css_ref, false );
				wp_enqueue_style( $list_css_ref );
				wp_add_inline_style( $list_css_ref, $gf_list_css );
			}
		}
        else {
			ob_start();
			echo file_get_contents( dirname( __FILE__ ) . '/css/gf_image_choices_legacy_list_styles_1.css' );
			if ( !empty($output_responsive_list_css) ) {
				echo file_get_contents( dirname( __FILE__ ) . '/css/gf_image_choices_legacy_responsive_list.css' );
			}
			$legacy_gf_list_css = ob_get_clean();
			$legacy_list_ref = "gf_image_choices_legacy_list_styles";
			if ( !wp_style_is($legacy_list_ref) ) {
				wp_register_style( $legacy_list_ref, false );
				wp_enqueue_style( $legacy_list_ref );
				wp_add_inline_style( $legacy_list_ref, $legacy_gf_list_css );
			}
	    }

	}


    public function get_field_feature_color( $field, $fallback_to_form = true, $for_product_image = false ) {
        $color = false;

	    $is_product_image_field = ( $for_product_image === true );

	    $field_feature_color = $this->get_field_settings_value("featureColor", "form_setting", $field, $is_product_image_field);
	    if ( $field_feature_color == "custom" ) {
		    $color = $this->get_field_settings_value("featureColorCustom", false, $field, $is_product_image_field);
	    }

        if ( empty($color) && $fallback_to_form ) {
	        $form = GFAPI::get_form( $field['formId'] );
	        $form_settings = $this->get_form_settings( $form );
            $form_feature_color = $this->get_form_settings_value("gf_image_choices_feature_color", "global_setting", $form, $form_settings);
            if ( $form_feature_color == "custom" ) {
	            $color = $this->get_form_settings_value("gf_image_choices_feature_color_custom", false, $form, $form_settings);
            }
            else {
                $plugin_settings = $this->get_plugin_settings();
                $plugin_feature_color = $this->get_plugin_settings_value("gf_image_choices_global_feature_color", $this->_defaultFeatureColor, $plugin_settings);
                if ( $plugin_feature_color == "custom" ) {
	                $color = $this->get_plugin_settings_value("gf_image_choices_global_feature_color_custom", false, $plugin_settings);
                }
            }
        }

        return $color;
    }

    public function get_field_theme( $field, $fallback_to_form = true, $for_product_image = false ) {
	    $theme = false;

	    $is_product_image_field = ( $for_product_image === true );

	    $field_theme = $this->get_field_settings_value("theme", "form_setting", $field, $is_product_image_field);
	    if ( (empty($field_theme) || $field_theme == "form_setting") && $fallback_to_form ) {
		    $form = GFAPI::get_form( $field['formId'] );
		    $form_settings = $this->get_form_settings( $form );
		    $form_theme = $this->get_form_settings_value("gf_image_choices_theme", "global_setting", $form, $form_settings);
		    if ( empty($form_theme) || $form_theme == "global_setting" ) {
			    $plugin_settings = $this->get_plugin_settings();
			    $theme = $this->get_plugin_settings_value("gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings);
		    }
            else {
                $theme = $form_theme;
            }
	    }
        else {
            $theme = $field_theme;
        }

        return $theme;
    }

	public function frontend_inline_styles( $form ) {

		if ( is_admin() || wp_doing_ajax() ) {
			return;
		}

		$form_id = rgar( $form, 'id' );
		$form_settings = $this->get_form_settings( $form );
		$plugin_settings = $this->get_plugin_settings();
		$use_new_features = $this->use_new_features();

		////// product image
		if ( $use_new_features && $this->form_contains_product_image_fields( $form ) ) {

			ob_start();

			// global heights
			$global_setting_height = $this->get_plugin_settings_value( "gf_image_choices_global_height", "", $plugin_settings );
			if ( !empty($global_setting_height) ) {
				?>
                .product-image-field[class*="product-image-theme--"] {
                    --product-image-height: <?php echo $global_setting_height; ?>px;
                }
				<?php
			}
			$global_setting_medium_height = $this->get_plugin_settings_value( "gf_image_choices_global_height_medium", "", $plugin_settings );
			if ( !empty($global_setting_medium_height) ) {
				?>
                .product-image-field[class*="product-image-theme--"] {
                    --product-image-height-medium: <?php echo $global_setting_medium_height; ?>px;
                }
				<?php
			}
			$global_setting_small_height = $this->get_plugin_settings_value( "gf_image_choices_global_height_small", "", $plugin_settings );
			if ( !empty($global_setting_small_height) ) {
				?>
                .product-image-field[class*="product-image-theme--"] {
                    --product-image-height-small: <?php echo $global_setting_small_height; ?>px;
                }
				<?php
			}

			$global_overrides_css = ob_get_clean();
			$global_overrides_css_ref = "product_image_css_overrides";
			if ( !wp_style_is($global_overrides_css_ref) && !empty($global_overrides_css) ) {
				wp_register_style( $global_overrides_css_ref, false );
				wp_enqueue_style( $global_overrides_css_ref );
				wp_add_inline_style( $global_overrides_css_ref, $global_overrides_css );
			}


			ob_start();

			// form heights
			$form_setting_height = $this->get_form_settings_value( "gf_image_choices_height", "", $form, $form_settings );
			if ( !empty($form_setting_height) ) {
				?>
                #gform_<?php echo $form_id; ?> .gform_fields .product-image-field[class*="product-image-theme--"] {
                    --product-image-height: <?php echo $form_setting_height; ?>px;
                }
				<?php
			}
			$form_setting_medium_height = $this->get_form_settings_value( "gf_image_choices_height_medium", "", $form, $form_settings );
			if ( !empty($form_setting_medium_height) ) {
				?>
                #gform_<?php echo $form_id; ?> .gform_fields .product-image-field[class*="product-image-theme--"] {
                    --product-image-height-medium: <?php echo $form_setting_medium_height; ?>px;
                }
				<?php
			}
			$form_setting_small_height = $this->get_form_settings_value( "gf_image_choices_height_small", "", $form, $form_settings );
			if ( !empty($form_setting_small_height) ) {
				?>
                #gform_<?php echo $form_id; ?> .gform_fields .product-image-field[class*="product-image-theme--"] {
                    --product-image-height-small: <?php echo $form_setting_small_height; ?>px;
                }
				<?php
			}


			// field heights
			foreach( $form['fields'] as $field ) {
				if ( !$this->field_has_product_image_enabled( $field ) ) {
					continue;
				}
				$height = $this->get_field_settings_value("height", "", $field, true);
				if ( !empty($height) ) {
					?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.product-image-field[class*="product-image-theme--"] {
                        --product-image-height: <?php echo $height; ?>px;
                    }
					<?php
				}
				$medium_height = $this->get_field_settings_value("heightMedium", "", $field, true);
				if ( !empty($medium_height) ) {
					?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.product-image-field[class*="product-image-theme--"] {
                        --product-image-height-medium: <?php echo $medium_height; ?>px;
                    }
					<?php
				}
				$small_height = $this->get_field_settings_value("heightSmall", "", $field, true);
				if ( !empty($small_height) ) {
					?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.product-image-field[class*="product-image-theme--"] {
                        --product-image-height-small: <?php echo $small_height; ?>px;
                    }
					<?php
				}
			}

			$form_overrides_css = ob_get_clean();
			$form_overrides_css_ref = "product_image_css_overrides_{$form_id}";
			if ( !wp_style_is($form_overrides_css_ref) && !empty($form_overrides_css) ) {
				wp_register_style( $form_overrides_css_ref, false );
				wp_enqueue_style( $form_overrides_css_ref );
				wp_add_inline_style( $form_overrides_css_ref, $form_overrides_css );
			}

		}

        ////// image choices

        if ( !$this->form_contains_image_choices_fields($form) ) {
            return;
        }

        if ( $use_new_features ) {

	        // global feature color
	        ob_start();
	        $plugin_feature_color = $this->get_plugin_settings_value("gf_image_choices_global_feature_color", $this->_defaultFeatureColor, $plugin_settings);
	        if ( $plugin_feature_color == "custom" ) {
		        $global_color = $this->get_plugin_settings_value("gf_image_choices_global_feature_color_custom", false, $plugin_settings);
		        if ( !empty($global_color) ) {
			        ?>
                    .image-choices-field[class*="ic-theme--"] {
                        --ic-feature-color: <?php echo $global_color; ?>;
                    }
			        <?php
		        }
	        }

            // global column width
	        $global_setting_columns = $this->get_plugin_settings_value( "gf_image_choices_global_columns", $this->_defaultColumns, $plugin_settings );
	        $global_setting_columns_width = $this->get_plugin_settings_value( "gf_image_choices_global_columns_width", "", $plugin_settings );
            if ( $global_setting_columns == "fixed" && !empty($global_setting_columns_width) ) {
	            ?>
                .image-choices-field[class*="ic-theme--"] {
                    --ic-width: <?php echo $global_setting_columns_width; ?>px;
                }
	            <?php
            }
	        $global_setting_medium_columns = $this->get_plugin_settings_value( "gf_image_choices_global_columns_medium", $this->_defaultColumns, $plugin_settings );
	        $global_setting_medium_columns_width = $this->get_plugin_settings_value( "gf_image_choices_global_columns_width_medium", "", $plugin_settings );
            if ( $global_setting_medium_columns == "fixed" && !empty($global_setting_medium_columns_width) ) {
	            ?>
                .image-choices-field[class*="ic-theme--"] {
                    --ic-width-medium: <?php echo $global_setting_medium_columns_width; ?>px;
                }
	            <?php
            }
	        $global_setting_small_columns = $this->get_plugin_settings_value( "gf_image_choices_global_columns_small", $this->_defaultColumns, $plugin_settings );
	        $global_setting_small_columns_width = $this->get_plugin_settings_value( "gf_image_choices_global_columns_width_small", "", $plugin_settings );
            if ( $global_setting_small_columns == "fixed" && !empty($global_setting_small_columns_width) ) {
	            ?>
                .image-choices-field[class*="ic-theme--"] {
                    --ic-width-small: <?php echo $global_setting_small_columns_width; ?>px;
                }
	            <?php
            }

            // global heights
	        $global_setting_height = $this->get_plugin_settings_value( "gf_image_choices_global_height", "", $plugin_settings );
            if ( !empty($global_setting_height) ) {
	            ?>
                .image-choices-field[class*="ic-theme--"] {
                    --ic-height: <?php echo $global_setting_height; ?>px;
                }
	            <?php
            }
	        $global_setting_medium_height = $this->get_plugin_settings_value( "gf_image_choices_global_height_medium", "", $plugin_settings );
            if ( !empty($global_setting_medium_height) ) {
	            ?>
                .image-choices-field[class*="ic-theme--"] {
                    --ic-height-medium: <?php echo $global_setting_medium_height; ?>px;
                }
	            <?php
            }
	        $global_setting_small_height = $this->get_plugin_settings_value( "gf_image_choices_global_height_small", "", $plugin_settings );
            if ( !empty($global_setting_small_height) ) {
	            ?>
                .image-choices-field[class*="ic-theme--"] {
                    --ic-height-small: <?php echo $global_setting_small_height; ?>px;
                }
	            <?php
            }

	        $global_overrides_css = ob_get_clean();
	        $global_overrides_css_ref = "gf_image_choices_css_overrides";
	        if ( !wp_style_is($global_overrides_css_ref) && !empty($global_overrides_css) ) {
		        wp_register_style( $global_overrides_css_ref, false );
		        wp_enqueue_style( $global_overrides_css_ref );
		        wp_add_inline_style( $global_overrides_css_ref, $global_overrides_css );
	        }

	        // form feature color
	        ob_start();
	        $form_feature_color = $this->get_form_settings_value("gf_image_choices_feature_color", "global_setting", $form, $form_settings);
	        if ( $form_feature_color == "custom" ) {
		        $form_color = $this->get_form_settings_value("gf_image_choices_feature_color_custom", false, $form, $form_settings);
		        if ( !empty($form_color) ) {
			        ?>
                    #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                        --ic-feature-color: <?php echo $form_color; ?>;
                    }
			        <?php
		        }
	        }

	        // form column widths
	        $form_setting_columns = $this->get_form_settings_value( "gf_image_choices_columns", "global_setting", $form, $form_settings );
	        $form_setting_columns_width = $this->get_form_settings_value( "gf_image_choices_columns_width", "", $form, $form_settings );
	        if ( $form_setting_columns == "fixed" && !empty($form_setting_columns_width) ) {
		        ?>
                #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                    --ic-width: <?php echo $form_setting_columns_width; ?>px;
                }
		        <?php
	        }
	        $form_setting_medium_columns = $this->get_form_settings_value( "gf_image_choices_columns_medium", "global_setting", $form, $form_settings );
	        $form_setting_medium_columns_width = $this->get_form_settings_value( "gf_image_choices_columns_width_medium", "", $form, $form_settings );
	        if ( $form_setting_medium_columns == "fixed" && !empty($form_setting_medium_columns_width) ) {
		        ?>
                #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                    --ic-width-medium: <?php echo $form_setting_medium_columns_width; ?>px;
                }
		        <?php
	        }
	        $form_setting_small_columns = $this->get_form_settings_value( "gf_image_choices_columns_small", "global_setting", $form, $form_settings );
	        $form_setting_small_columns_width = $this->get_form_settings_value( "gf_image_choices_columns_width_small", "", $form, $form_settings );
	        if ( $form_setting_small_columns == "fixed" && !empty($form_setting_small_columns_width) ) {
		        ?>
                #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                    --ic-width-small: <?php echo $form_setting_small_columns_width; ?>px;
                }
		        <?php
	        }

            // form heights
	        $form_setting_height = $this->get_form_settings_value( "gf_image_choices_height", "", $form, $form_settings );
	        if ( !empty($form_setting_height) ) {
		        ?>
                #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                    --ic-height: <?php echo $form_setting_height; ?>px;
                }
		        <?php
	        }
	        $form_setting_medium_height = $this->get_form_settings_value( "gf_image_choices_height_medium", "", $form, $form_settings );
	        if ( !empty($form_setting_medium_height) ) {
		        ?>
                #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                    --ic-height-medium: <?php echo $form_setting_medium_height; ?>px;
                }
		        <?php
	        }
	        $form_setting_small_height = $this->get_form_settings_value( "gf_image_choices_height_small", "", $form, $form_settings );
	        if ( !empty($form_setting_small_height) ) {
		        ?>
                #gform_<?php echo $form_id; ?> .gform_fields .image-choices-field[class*="ic-theme--"] {
                    --ic-height-small: <?php echo $form_setting_small_height; ?>px;
                }
		        <?php
	        }


	        // fields feature colors
	        foreach( $form['fields'] as $field ) {
		        $feature_color = $this->get_field_feature_color( $field, false );
		        if ( !empty($feature_color) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-feature-color: <?php echo $feature_color; ?>;
                    }
			        <?php
		        }
	        }

	        // field column widths
	        foreach( $form['fields'] as $field ) {
		        $columns = $this->get_field_settings_value("columns", "form_setting", $field);
		        $column_width = $this->get_field_settings_value("columnsWidth", "", $field);
		        if ( $columns === "fixed" && !empty($column_width) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-width: <?php echo $column_width; ?>px;
                    }
			        <?php
		        }
		        $medium_columns = $this->get_field_settings_value("columnsMedium", "form_setting", $field);
		        $medium_column_width = $this->get_field_settings_value("columnsWidthMedium", "", $field);
		        if ( $medium_columns === "fixed" && !empty($medium_column_width) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-width-medium: <?php echo $medium_column_width; ?>px;
                    }
			        <?php
		        }
		        $small_columns = $this->get_field_settings_value("columnsSmall", "form_setting", $field);
		        $small_column_width = $this->get_field_settings_value("columnsWidthSmall", "", $field);
		        if ( $small_columns === "fixed" && !empty($small_column_width) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-width-small: <?php echo $small_column_width; ?>px;
                    }
			        <?php
		        }
	        }


	        // field heights
	        foreach( $form['fields'] as $field ) {
		        $height = $this->get_field_settings_value("height", "", $field);
		        if ( !empty($height) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-height: <?php echo $height; ?>px;
                    }
			        <?php
		        }
		        $medium_height = $this->get_field_settings_value("heightMedium", "", $field);
		        if ( !empty($medium_height) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-height-medium: <?php echo $medium_height; ?>px;
                    }
			        <?php
		        }
		        $small_height = $this->get_field_settings_value("heightSmall", "", $field);
		        if ( !empty($small_height) ) {
			        ?>
                    #field_<?php echo $field["formId"] . "_" . $field["id"]; ?>.image-choices-field[class*="ic-theme--"] {
                        --ic-height-small: <?php echo $small_height; ?>px;
                    }
			        <?php
		        }
	        }

	        $form_overrides_css = ob_get_clean();
	        $form_overrides_css_ref = "gf_image_choices_css_overrides_{$form_id}";
	        if ( !wp_style_is($form_overrides_css_ref) && !empty($form_overrides_css) ) {
		        wp_register_style( $form_overrides_css_ref, false );
		        wp_enqueue_style( $form_overrides_css_ref );
		        wp_add_inline_style( $form_overrides_css_ref, $form_overrides_css );
	        }

        }

		$ignore_global_css_value = (isset($form_settings['gf_image_choices_ignore_global_css'])) ? $form_settings['gf_image_choices_ignore_global_css'] : 0;

        if ( $use_new_features ) {

	        $global_css_value = $this->get_plugin_setting('gf_image_choices_user_css_global');
	        $global_ref = "gf_image_choices_user_css_global";
	        if ( empty($ignore_global_css_value) && !empty($global_css_value) && !wp_style_is($global_ref) ) {
		        wp_register_style( $global_ref, false );
		        wp_enqueue_style( $global_ref );
		        wp_add_inline_style( $global_ref, $global_css_value );
	        }

	        $form_css_value = (isset($form_settings['gf_image_choices_user_css_form'])) ? $form_settings['gf_image_choices_user_css_form'] : '';
	        $ref = "gf_image_choices_user_css_form_{$form_id}";
	        if ( !empty($form_css_value) && !wp_style_is($ref) ) {
		        wp_register_style( $ref, false );
		        wp_enqueue_style( $ref );
		        wp_add_inline_style( $ref, $form_css_value );
	        }

        }
        else {

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

	        $this->maybe_enqueue_legacy_list_styles( $form );

        }

	}



	/**
	 * Localize the strings used by the scripts.
	 */
	public function localize_admin_scripts() {

		$use_new_features = $this->use_new_features();
        $plugin_settings = $this->get_plugin_settings();
		$elementor_compat = ( $this->is_elementor_installed() ) ? $this->get_plugin_settings_value('gf_image_choices_elementor_lightbox_compat', '', $plugin_settings) : '';

		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		wp_localize_script( 'gf_image_choices_admin', 'imageChoicesFieldVars', array(
			'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
		) );

		//localize strings for the js file
		wp_localize_script( 'gf_image_choices_admin', 'imageChoicesFieldStrings', array(
			'confirmImagesToggle'    => esc_html__( 'Color picker choices are enabled on this field. Are you sure you want to remove the colors and use images instead?', 'gf_image_choices' ),
			'uploadImage'    => esc_html__( 'Upload image', 'gf_image_choices' ),
			'removeImage'    => esc_html__( 'Remove this image', 'gf_image_choices' ),
			'removeAllChoices'    => esc_html__( 'Remove All Choices', 'gf_image_choices' ),
			'useLightboxWarning'    => esc_html__( "It looks like we don't have the media ID for the selected image(s), which is needed for the lightbox functionality. In order for lightbox to work with this field, you might want to remove and re-add the image(s) again or use one of the available filters to add the ID or the large image URL.", 'gf_image_choices' ),
		) );

		$js_name = $use_new_features ? 'gf_image_choices_admin' : 'gf_image_choices';
		wp_localize_script( $js_name, 'imageChoicesVars', array(
			'gf_version' => GFCommon::$version,
			'version' => $this->_version,
			'form_settings' => admin_url( "admin.php?subview=settings&page=gf_edit_forms&view=settings&id=" ),
			'useNewFeatures' => $use_new_features ? 'true' : 'false',
            'defaults' => $use_new_features ? array(
                'theme' => $this->_defaultTheme,
                'featureColor' => $this->_defaultFeatureColor,
                'featureColorCustom' => $this->_defaultFeatureColorCustom,
                'alignment' => $this->_defaultAlignment,
                'columns' => $this->_defaultColumns,
                'imageDisplay' => $this->_defaultImageDisplay,
                'lightboxSize' => $this->_defaultLightboxImageSize,
                'imageSize' => $this->_defaultImageSize,
            ) : array(),
            'globals' => $use_new_features ? array(
	            'theme' => $this->get_plugin_settings_value("gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings),
	            'featureColor' => $this->get_plugin_settings_value("gf_image_choices_global_feature_color", $this->_defaultFeatureColor, $plugin_settings),
	            'featureColorCustom' => $this->get_plugin_settings_value("gf_image_choices_global_feature_color_custom", $this->_defaultFeatureColorCustom, $plugin_settings),
	            'alignment' => $this->get_plugin_settings_value("gf_image_choices_global_align", $this->_defaultAlignment, $plugin_settings),
	            'columns' => $this->get_plugin_settings_value("gf_image_choices_global_columns", $this->_defaultColumns, $plugin_settings),
	            'imageDisplay' => $this->get_plugin_settings_value("gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings),
	            'lightboxSize' => $this->get_plugin_settings_value("gf_image_choices_global_lightbox_size", $this->_defaultLightboxImageSize, $plugin_settings),
	            'imageSize' => $this->get_plugin_settings_value("gf_image_choices_global_image_size", $this->_defaultImageSize, $plugin_settings),
            ) : array(),
			'elementorCompat' => ( !empty( $elementor_compat ) ) ? $elementor_compat : '',
		) );

	}

	public function localize_scripts() {

		$lazy_load_global_value = $this->get_plugin_setting('gf_image_choices_lazy_load_global');
		if ( empty($lazy_load_global_value) ) {
			$lazy_load_global_value = 0;
		}

		$elementor_compat = ( $this->is_elementor_installed() ) ? $this->get_plugin_setting('gf_image_choices_elementor_lightbox_compat') : '';

		wp_localize_script( 'gf_image_choices', 'imageChoicesVars', array(
			'gf_version' => GFCommon::$version,
			'version' => $this->_version,
			'useNewFeatures' => $this->use_new_features() ? 'true' : 'false',
			'elementorCompat' => ( !empty( $elementor_compat ) ) ? $elementor_compat : '',
			'lazyLoadGlobal' => $lazy_load_global_value,
		) );

	}


	/**
	 * Creates a settings page for this add-on.
	 */
	public function plugin_settings_fields() {

        $plugin_settings = $this->get_plugin_settings();
        $use_new_features = $this->use_new_features();

		//$license = $this->get_plugin_settings_value('gf_image_choices_license_key', "", $plugin_settings);
        $license = $this->get_license_key();
		$status = get_option('gf_image_choices_license_status');

		$license_field = array(
			'name' => 'gf_image_choices_license_key',
			'tooltip' => esc_html__('Enter the license key you received after purchasing the plugin.', 'gf_image_choices'),
			'label' => esc_html__('License Key', 'gf_image_choices'),
			'type' => 'text',
			'input_type' => 'password',
			'class' => 'medium',
			'default_value' => ( defined('GF_IMAGE_CHOICES_LICENSE') ) ? GF_IMAGE_CHOICES_LICENSE : '',
			'validation_callback' => array($this, 'license_validation'),
			'feedback_callback' => array($this, 'license_feedback'),
			'error_message' => esc_html__( 'Invalid license', 'gf_image_choices' ),
		);

        /*
		if (!empty($license) && !empty($status)) {
			$license_field['after_input'] = ($status == 'valid') ? ' License is valid' : ' Invalid or expired license';
		}
        */

		$license_section = array(
			'type' => 'section',
			'title'  => esc_html__('To unlock plugin updates, please enter your license key below', 'gf_image_choices'),
			'fields' => array(
				$license_field
			)
		);



		$use_legacy_styles_value = $this->get_plugin_settings_value('gf_image_choices_use_legacy_styles', true, $plugin_settings);
		$legacy_styles_toggle_field = array(
			'name' => 'gf_image_choices_use_legacy_styles',
			'type' => 'toggle',
            'tooltip' => esc_html__('In legacy mode, image choices will not make use of the new features and styles. It will mostly continue to run like 1.3.x versions', 'gf_image_choices'),
			'label' => __( 'Switch to legacy mode', 'gf_image_choices' ),
		);

		$new_styles_dependency = array(
			'live'   => true,
			'fields' => array(
				array(
					'field' => 'gf_image_choices_use_legacy_styles',
					'values' => array( false, '0', null, 'false', '' ),
				),
			),
		);

		$responsive_list_css_toggle_value = $this->get_plugin_settings_value('gf_image_choices_enqueue_responsive_list_css', false, $plugin_settings);
		$responsive_list_css_toggle_field = array(
			'name' => 'gf_image_choices_enqueue_responsive_list_css',
			'type' => 'toggle',
			'label' => __( 'Enqueue responsive gf_list_*col styles', 'gf_image_choices' ),
            'dependency' => array(
	            'live'   => true,
	            'fields' => array(
		            array(
			            'field' => 'gf_image_choices_use_legacy_styles',
		            ),
	            ),
            )
		);

		$style_switch_section = array(
			'type' => 'section',
			'title'  => esc_html__('Image Choices Legacy Settings', 'gf_image_choices'),
            'description' => esc_html("Version 1.4+ is a major update that includes new features and settings offering a better experience for Image Choices. If you've updated from previous versions and are having issues in your forms with these new settings, switch to legacy mode which will run more like 1.3.x versions and without the new features. It's always best practice to test your forms after updating settings or versions.", 'gf_image_choices'),
			'fields' => array(
				$legacy_styles_toggle_field,
				$responsive_list_css_toggle_field
			)
		);



		// NEW: THEME SETTING
		$theme_value = $this->get_plugin_settings_value( "gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings );
		$theme_field = array(
			'name' => 'gf_image_choices_global_theme',
			'label' => esc_html__( 'Default Theme', 'gf_image_choices' ),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_settings_theme_choices() )
		);
		if ( !empty($theme_value) ) {
			$theme_field['default_value'] = $theme_value;
		}

		// NEW: THEME PREVIEW
		$theme_preview_field = array(
			'name' => 'gf_image_choices_global_theme_preview',
			'label' => '',
			'type' => 'html',
			'class' => 'medium',
			'html' => $this->get_settings_theme_preview_html(),
		);

		$theme_section = array(
			'type' => 'section',
            'dependency' => $new_styles_dependency,
			'title' => esc_html__( 'Theme', 'gf_image_choices' ),
			'class' => 'gform-settings-panel--half',
			'fields' => array(
				$theme_field,
				$theme_preview_field,
			)
		);

		// NEW: FEATURE COLOR
		$feature_color_value = $this->get_plugin_settings_value("gf_image_choices_global_feature_color", $this->_defaultFeatureColor, $plugin_settings);
		$feature_color_field = array(
			'name' => 'gf_image_choices_global_feature_color',
			'label' => esc_html__('Default Feature Color', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => array(
				array(
					'value' => 'none',
					'label' => esc_html__('None', 'gf_image_choices')
				),
				array(
					'value' => 'custom',
					'label' => esc_html__('Custom', 'gf_image_choices')
				),
			)
		);
		if ( !empty($feature_color_value) ) {
			$feature_color_field['default_value'] = $feature_color_value;
		}

		// NEW: FEATURE COLOR - CUSTOM
		$feature_color_custom_value = $this->get_plugin_settings_value("gf_image_choices_global_feature_color_custom", $this->_defaultFeatureColorCustom, $plugin_settings);
		$feature_color_custom_field = array(
			'name' => 'gf_image_choices_global_feature_color_custom',
			'label' => esc_html__('Custom Feature Color', 'gf_image_choices'),
			'default_value' => $feature_color_custom_value,
			'type' => 'text',
			'class' => 'medium'
		);

		$feature_color_section = array(
			'type' => 'section',
			'dependency' => $new_styles_dependency,
			'title' => esc_html__( 'Feature Color', 'gf_image_choices' ),
			'class' => 'gform-settings-panel--half',
			'fields' => array(
				$feature_color_field,
				$feature_color_custom_field,
			)
		);


		// NEW: ALIGNMENT
		$alignment_value = $this->get_plugin_settings_value("gf_image_choices_global_align", $this->_defaultAlignment, $plugin_settings);
		$alignment_field = array(
			'name' => 'gf_image_choices_global_align',
			'label' => esc_html__('Default Choices Alignment', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_settings_align_choices() )
		);
		if ( !empty($alignment_value) ) {
			$alignment_field['default_value'] = $alignment_value;
		}

		// NEW: IMAGE STYLE
		$image_style_value = $this->get_plugin_settings_value("gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings);
		$image_style_field = array(
			'name' => 'gf_image_choices_global_image_style',
			'label' => esc_html__('Default Image Display Style', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_settings_image_style_choices() )
		);
		if ( !empty($image_style_value) ) {
			$image_style_field['default_value'] = $image_style_value;
		}

		// NEW: MEDIA SIZE
		/*
		$image_size_value = $this->get_plugin_settings_value("gf_image_choices_global_image_size", $this->_defaultImageSize, $plugin_settings);
		$image_size_field = array(
			'name' => 'gf_image_choices_global_image_size',
			'tooltip' => esc_html__('The selected image size will be used in the choices on form display.', 'gf_image_choices'),
			'label' => esc_html__('Default Image Size', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_media_image_sizes_choices() )
		);
		if ( !empty($image_size_value) ) {
			$image_size_field['default_value'] = $image_size_value;
		}
		*/

		// NEW: COLUMNS
		$columns_value = $this->get_plugin_settings_value("gf_image_choices_global_columns", $this->_defaultColumns, $plugin_settings);
		$columns_field = array(
			'name' => 'gf_image_choices_global_columns',
			'label' => esc_html__('Default Columns', 'gf_image_choices'),
            'tooltip' => __('<h6>Columns</h6>Control the fluid and responsive layout of your choices<br/><br/>Fixed Width: Will use a fixed pixel width for columns vs a fluid layout<br/><br/>Auto: Automatic fluid columns based on the number of choices<br/><br/>1 - 12: Choose the column number that works for you.', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_settings_columns_choices() )
		);
		if ( !empty($columns_value) ) {
			$columns_field['default_value'] = $columns_value;
		}

		$columns_width_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_width", "", $plugin_settings);
		$columns_width_field = array(
			'name' => 'gf_image_choices_global_columns_width',
			'label' => esc_html__('Column Width', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
			'class' => 'medium',
			'dependency' => array(
				'live' => true,
				'fields' => array(
					array(
						'field' => 'gf_image_choices_global_columns',
						'values' => array('fixed'),
					),
				)
			)
		);
		if ( !empty($columns_width_value) ) {
			$columns_width_field['default_value'] = $columns_width_value;
		}

		// NEW: COLUMNS - MEDIUM
		$columns_medium_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_medium", $this->_defaultColumns, $plugin_settings);
		$columns_medium_field = array(
			'name' => 'gf_image_choices_global_columns_medium',
			'label' => esc_html__('Medium Default Columns (at medium screen sizes)', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_settings_columns_choices() )
		);
		if ( !empty($columns_medium_value) ) {
			$columns_medium_field['default_value'] = $columns_medium_value;
		}

		$columns_medium_width_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_width_medium", "", $plugin_settings);
		$columns_medium_width_field = array(
			'name' => 'gf_image_choices_global_columns_width_medium',
			'label' => esc_html__('Medium Column Width', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
			'class' => 'medium',
			'dependency' => array(
				'live' => true,
				'fields' => array(
					array(
						'field' => 'gf_image_choices_global_columns_medium',
						'values' => array('fixed'),
					),
				)
			)
		);
		if ( !empty($columns_medium_width_value) ) {
			$columns_medium_width_field['default_value'] = $columns_medium_width_value;
		}

		// NEW: COLUMNS - SMALL
		$columns_small_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_small", $this->_defaultColumns, $plugin_settings);
		$columns_small_field = array(
			'name' => 'gf_image_choices_global_columns_small',
			'label' => esc_html__('Small Default Columns (at small screen sizes)', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_settings_columns_choices() )
		);
		if ( !empty($columns_small_value) ) {
			$columns_small_field['default_value'] = $columns_small_value;
		}

		$columns_small_width_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_width_small", "", $plugin_settings);
		$columns_small_width_field = array(
			'name' => 'gf_image_choices_global_columns_width_small',
			'label' => esc_html__('Small Column Width', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
			'class' => 'medium',
			'dependency' => array(
				'live' => true,
				'fields' => array(
					array(
						'field' => 'gf_image_choices_global_columns_small',
						'values' => array('fixed'),
					),
				)
			)
		);
		if ( !empty($columns_small_width_value) ) {
			$columns_small_width_field['default_value'] = $columns_small_width_value;
		}

		$layout_section = array(
			'type' => 'section',
			'dependency' => $new_styles_dependency,
			'title' => esc_html__( 'Choices Layout', 'gf_image_choices' ),
			'class' => 'gform-settings-panel--half',
			'fields' => array(
				$alignment_field,
				$columns_field,
                $columns_width_field,
				$columns_medium_field,
				$columns_medium_width_field,
				$columns_small_field,
				$columns_small_width_field,
			)
		);

		// NEW: LIGHTBOX IMAGE SIZE
		$lightbox_size_value = $this->get_plugin_settings_value("gf_image_choices_global_lightbox_size", $this->_defaultLightboxImageSize, $plugin_settings);
		$lightbox_size_field = array(
			'name' => 'gf_image_choices_global_lightbox_size',
			'tooltip' => esc_html__('The selected image size will be used in the lightbox, if enabled.', 'gf_image_choices'),
			'label' => esc_html__('Default Lightbox Image Size', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'choices' => $this->get_settings_select_choices_array( $this->get_media_image_sizes_choices() )
		);
		if ( !empty($lightbox_size_value) ) {
			$lightbox_size_field['default_value'] = $lightbox_size_value;
		}


		$item_height_value = $this->get_plugin_settings_value("gf_image_choices_global_height", "", $plugin_settings);
		$item_height_field = array(
			'name' => 'gf_image_choices_global_height',
			'label' => esc_html__('Display Height', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
			'class' => 'medium',
		);
		if ( !empty($item_height_value) ) {
			$item_height_field['default_value'] = $item_height_value;
		}

		$item_medium_height_value = $this->get_plugin_settings_value("gf_image_choices_global_height_medium", "", $plugin_settings);
		$item_medium_height_field = array(
			'name' => 'gf_image_choices_global_height_medium',
			'label' => esc_html__('Medium Display Height (at medium screen sizes)', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
			'class' => 'medium',
		);
		if ( !empty($item_medium_height_value) ) {
			$item_medium_height_field['default_value'] = $item_medium_height_value;
		}

		$item_small_height_value = $this->get_plugin_settings_value("gf_image_choices_global_height_small", "", $plugin_settings);
		$item_small_height_field = array(
			'name' => 'gf_image_choices_global_height_small',
			'label' => esc_html__('Small Display Height (at small screen sizes)', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
			'class' => 'medium',
		);
		if ( !empty($item_small_height_value) ) {
			$item_small_height_field['default_value'] = $item_small_height_value;
		}

		$image_options_section = array(
			'type' => 'section',
			'dependency' => $new_styles_dependency,
			'title' => esc_html__( 'Image Options', 'gf_image_choices' ),
			'class' => 'gform-settings-panel--half',
			'fields' => array(
				$image_style_field,
				$lightbox_size_field,
                $item_height_field,
                $item_medium_height_field,
                $item_small_height_field,
			)
		);

		$lazy_load_global_value = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0, $plugin_settings);

		$lazy_load_global_field = array(
			'name' => 'gf_image_choices_lazy_load_global',
			'type' => 'toggle',
			'tooltip' => esc_html__('With lazy load enabled, the images in choices will be loaded only as they enter (or about to enter) the viewport. This reduces initial page load time, initial page weight, and system resource usage, all of which have positive impacts on performance.', 'gf_image_choices'),
			'label' => __( 'Enable lazy loading', 'gf_image_choices' ),
			'default_value' => (int) $lazy_load_global_value,
		);

        $lazy_load_section = array(
	        'type' => 'section',
	        'title'  => esc_html__('Lazy Load', 'gf_image_choices'),
	        'description' => esc_html__('With lazy load enabled, the images in choices will be loaded only as they enter (or about to enter) the viewport. This reduces initial page load time, initial page weight, and system resource usage, all of which have positive impacts on performance.', 'gf_image_choices'),
	        'fields' => array(
		        $lazy_load_global_field
	        )
        );


		$compatibility_section = false;
		if ( $this->is_elementor_installed() ) {

			$elementor_compat_value = $this->get_plugin_settings_value('gf_image_choices_elementor_lightbox_compat', null, $plugin_settings);
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

			$compatibility_section = array(
				'type' => 'section',
				'title'  => esc_html__('Compatibility Settings', 'gf_image_choices'),
				'fields' => array(
					$elementor_compat_field
				)
			);

		}




		$custom_css_global_value = $this->get_plugin_settings_value('gf_image_choices_user_css_global', '', $plugin_settings);
		$custom_css_global_field = array(
			'name' => 'gf_image_choices_user_css_global',
			'tooltip' => esc_html__('These styles will be loaded for all forms.', 'gf_image_choices'),
			'label' => esc_html__('Enter your own css to style image choices or override any variables', 'gf_image_choices'),
			'type' => 'textarea',
			'class' => 'large',
			'default_value' => $custom_css_global_value
		);

		$custom_css_section = array(
			'type' => 'section',
            'dependency' => $new_styles_dependency,
			'title'  => esc_html__('Custom CSS', 'gf_image_choices'),
			'fields' => array(
				$custom_css_global_field
			)
		);

		$legacy_custom_css_global_value = $this->get_plugin_settings_value('gf_image_choices_custom_css_global', '', $plugin_settings);
		$legacy_custom_css_global_field = array(
			'name' => 'gf_image_choices_custom_css_global',
			'tooltip' => $use_new_features ? null : esc_html__('These styles will be loaded for all forms.<br/>Find examples at <a href="https://jetsloth.com/support/gravity-forms-image-choices/">https://jetsloth.com/support/gravity-forms-image-choices/</a>', 'gf_image_choices'),
			'label' => $use_new_features ? esc_html__("Note: This legacy CSS is ignored as it's most likely not needed with the new features. Use the Custom CSS box above for customising any of the new styling.", 'gf_image_choices') : esc_html__('Custom Legacy CSS', 'gf_image_choices'),
			'type' => 'textarea',
			'class' => 'large',
			'default_value' => $legacy_custom_css_global_value
		);

		$legacy_custom_css_section = array(
			'type' => 'section',
			'title' => $use_new_features ? esc_html__('Legacy CSS', 'gf_image_choices') : esc_html__('Enter your own css to style legacy image choices', 'gf_image_choices'),
			'id' => 'gf_image_choices_legacy_custom_css_section',
			'collapsible' => true,
			'is_collapsed' => $use_new_features,
			'fields' => array(
				$legacy_custom_css_global_field
			)
		);



        $settings_panels = array(
	        $license_section,
	        $theme_section,
	        $feature_color_section,
	        $layout_section,
	        $image_options_section,
	        $lazy_load_section
        );
		if ( !empty($compatibility_section) ) {
			$settings_panels[] = $compatibility_section;
		}
		$settings_panels[] = $custom_css_section;
		$settings_panels[] = $style_switch_section;
		$settings_panels[] = $legacy_custom_css_section;


		$forms = $this->get_image_choices_form_ids( true );

        $tabs = array(
            'gf_image_choices_settings_tab' => array(
	            'type' => 'tab',
                'id' => 'gf_image_choices_settings_tab',
                'title' => esc_html__( 'Settings', 'gf_image_choices' ),
                'sections' => $settings_panels
            ),
            'gf_image_choices_tools_tab' => array(
	            'type' => 'tab',
	            'id' => 'gf_image_choices_tools_tab',
	            'title' => esc_html__( 'Tools', 'gf_image_choices' ),
                'sections' => array(
                    array(
	                    'type' => 'section',
                        'title' => esc_html__( 'Important', 'gf_image_choices' ),
                        'class' => 'jetbase-important',
                        'fields' => array(
                            array(
	                            'name' => 'gf_image_choices_tools_important_notice',
	                            'label' => '',
	                            'type' => 'html',
	                            'class' => 'medium',
	                            'html' => $this->get_tools_tab_important_notice_html( $forms ),
                            )
                        )
                    ),
                    array(
	                    'type' => 'section',
                        'title' => esc_html__( 'Choices Image URL Replacement', 'gf_image_choices' ),
                        'id' => 'gf_image_choices_url_replacement_section',
	                    'class' => 'gform-settings-panel--half',
                        'fields' => array(
                            array(
	                            'name' => 'gf_image_choices_url_replacement',
	                            'label' => '',
	                            'type' => 'html',
	                            'class' => 'medium',
	                            'html' => $this->get_tools_url_replacement_tab_html( $forms ),
                            )
                        )
                    ),
                    array(
	                    'type' => 'section',
                        'title' => esc_html__( 'Choices Image Size Replacement', 'gf_image_choices' ),
	                    'id' => 'gf_image_choices_size_replacement_section',
	                    'class' => 'gform-settings-panel--half',
	                    'fields' => array(
		                    array(
			                    'name' => 'gf_image_choices_size_replacement',
			                    'label' => '',
			                    'type' => 'html',
			                    'class' => 'medium',
			                    'html' => $this->get_tools_image_replacement_tab_html(),
		                    )
                        )
                    ),
                )
            ),
        );

		return $tabs;
	}

    public function get_image_choices_form_ids( $with_titles = false ) {
        $form_ids = [];
	    $forms = GFAPI::get_forms();
	    foreach( $forms as $form ) {
		    foreach( $form['fields'] as $field ) {
			    if ( rgobj($field, "imageChoices_enableImages") ) {
				    $form_ids[] = $with_titles ? array(
                        "id" => rgar($form, "id"),
                        "title" => rgar($form, "title"),
                    ) : rgar($form, "id");
                    break;
			    }
		    }
	    }
        return $form_ids;
    }

    public function ajax_get_image_size_replacement_form_ids() {
	    wp_send_json(array(
		    "success" => true,
		    "forms" => $this->get_image_choices_form_ids()
	    ));
    }

    public function ajax_image_size_replacement() {
	    $new_size = rgpost("new");
	    if ( empty($new_size) ) {
		    wp_send_json_error(array(
			    "success" => false,
			    "message" => esc_html("New size required")
		    ));
	    }

	    $form_id = (int) rgpost("id");
	    if ( empty($form_id) ) {
		    wp_send_json_error(array(
			    "success" => false,
			    "message" => esc_html("Form ID required")
		    ));
	    }

	    $form = GFAPI::get_form( $form_id );
	    if ( empty($form) ) {
		    wp_send_json_error(array(
			    "success" => false,
			    "message" => esc_html("Form not found")
		    ));
	    }

	    $replacements = 0;
	    foreach( $form['fields'] as &$field ) {

		    if ( !rgobj($field, "imageChoices_enableImages") ) {
			    continue;
		    }

		    foreach( $field->choices as &$choice ) {
			    $image_url = ( isset($choice['imageChoices_image']) ) ? $choice['imageChoices_image'] : "";
			    $image_id = ( isset($choice['imageChoices_imageID']) ) ? $choice['imageChoices_imageID'] : "";
                if ( empty($image_id) && !empty($image_url) ) {
	                $image_id = attachment_url_to_postid( $image_url );
                }
                if ( !empty($image_id) ) {
	                $new_image_url = wp_get_attachment_image_url( (int) $image_id, $new_size );
                    if ( !empty($new_image_url) ) {
	                    $choice['imageChoices_image'] = $new_image_url;
	                    $replacements++;
                    }
                }
		    }

	    }

	    if ( $replacements > 0 ) {
		    $result = GFAPI::update_form( $form );
		    if ( empty($result) ) {
			    wp_send_json_error(array(
				    "success" => false,
				    "message" => esc_html("Failed to update form")
			    ));
		    }

	    }

	    wp_send_json([
		    "success" => true,
		    "id" => $form_id,
		    "total" => $replacements
	    ]);

    }

    public function ajax_get_url_replacement_form_ids() {
	    $old_url = rgpost("old");
        if ( empty($old_url) ) {
	        wp_send_json_error(array(
		        "success" => false,
		        "error" => esc_html("Old URL required")
	        ));
        }

	    $form_ids = [];
	    $forms = GFAPI::get_forms();
	    foreach( $forms as $form ) {
		    foreach( $form['fields'] as $field ) {
                /*
			    if ( !rgobj($field, "imageChoices_enableImages") ) {
                    continue;
			    }
                */
			    $found_in_field = false;
			    foreach( $field->choices as &$choice ) {
				    if ( strpos( rgar($choice, "imageChoices_image", ""), $old_url ) !== FALSE || strpos( rgar($choice, "imageChoices_largeImage", ""), $old_url ) !== FALSE ) {
					    $found_in_field = true;
					    break;
				    }
			    }
			    if ( $found_in_field ) {
				    $form_ids[] = rgar($form, "id");
                    break;
			    }
		    }
	    }

	    wp_send_json(array(
		    "success" => true,
		    "forms" => $form_ids
	    ));
    }


    public function ajax_url_replacement() {
        $old_url = rgpost("old");
        $new_url = rgpost("new");
	    if ( empty($old_url) || empty($new_url) || $old_url == $new_url ) {
		    wp_send_json_error(array(
			    "success" => false,
			    "error" => esc_html("One or both URLs are missing or invalid")
		    ));
	    }

	    $form_id = (int) rgpost("id");
	    if ( empty($form_id) ) {
		    wp_send_json_error(array(
			    "success" => false,
			    "message" => esc_html("Form ID required")
		    ));
	    }

        $form = GFAPI::get_form( $form_id );
	    if ( empty($form) ) {
		    wp_send_json_error(array(
			    "success" => false,
			    "message" => esc_html("Form not found")
		    ));
	    }

	    $replacements = 0;
	    foreach( $form['fields'] as &$field ) {

		    if ( !rgobj($field, "imageChoices_enableImages") ) {
			    continue;
		    }

		    foreach( $field->choices as &$choice ) {
			    $image_url = ( isset($choice['imageChoices_image']) ) ? $choice['imageChoices_image'] : "";
			    $large_image_url = ( isset($choice['imageChoices_largeImage']) ) ? $choice['imageChoices_largeImage'] : "";
                $replaced = false;
			    if ( !empty($image_url) && strpos($image_url, $old_url) !== FALSE ) {
				    $choice['imageChoices_image'] = str_replace($old_url, $new_url, $image_url);
				    $replaced = true;
			    }
			    if ( !empty($large_image_url) && strpos($large_image_url, $old_url) !== FALSE ) {
				    $choice['imageChoices_largeImage'] = str_replace($old_url, $new_url, $large_image_url);
				    $replaced = true;
			    }
                if ( $replaced ) {
	                $replacements++;
                }
		    }

	    }

	    if ( $replacements > 0 ) {
		    $result = GFAPI::update_form( $form );
		    if ( empty($result) ) {
			    wp_send_json_error(array(
				    "success" => false,
				    "message" => esc_html("Failed to update form")
			    ));
		    }

	    }

	    wp_send_json([
		    "success" => true,
            "id" => $form_id,
            "total" => $replacements
        ]);

    }

	private function get_tools_tab_important_notice_html() {
        ob_start();
        ?>
        <div id="image_choices_tools_important_notice" class="jetbase-alert jetbase-alert--warning">
            <div class="jetbase-alert__inner">
                <div class="jetbase-alert__body">
                    <div class="alert gforms_note_warning"> <?php _e("Always make a backup of your database before using these tools, as the process cannot be undone if an error occurs.", "gf_image_choices"); ?></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
	}


    private function get_tools_url_replacement_tab_html( $forms = array() ) {
	    if ( empty($forms) ) {
		    $forms = $this->get_image_choices_form_ids( true );
	    }
        ob_start();
        ?>
        <div id="image_choices_url_replacement" class="jetbase-tool">
            <div class="jetbase-tool__inner">
                <div class="jetbase-tool__header">
                    <div class="jetbase-tool__description gform-settings-description">
                        <?php _e("This tool will help you with a replacement of URLs for your Image Choices. Handy, for example, when changing domains.", "gf_image_choices"); ?><br/>
                    </div>
                </div>
                <div class="jetbase-tool__body">
                    <div class="jetbase-form__field gform-settings-field">
                        <div class="gform-settings-field__header">
                            <label for="image_choices_url_replacement_form_select" class="jetbase-form__label gform-settings-label"><?php _e("Form", "gf_image_choices"); ?></label>
                        </div>
                        <div class="gform-settings-input__container">
                            <select id="image_choices_url_replacement_form_select" name="image_choices_url_replacement_form" class="jetbase-form__input jetbase-form__input--select medium">
                                <option value="all" selected><?php _e('All forms', 'gf_image_choices'); ?></option>
                                <?php if (!empty($forms)): foreach( $forms as $form ): ?>
                                    <option value="<?php echo $form['id']; ?>"><?php echo $form['title']; ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="jetbase-form__field gform-settings-field">
                        <div class="gform-settings-field__header">
                            <label for="image_choices_url_replacement_from_input" class="jetbase-form__label gform-settings-label"><?php _e("From", "gf_image_choices"); ?></label>
                        </div>
                        <div class="gform-settings-input__container">
                            <input id="image_choices_url_replacement_from_input" name="image_choices_url_replacement_from" class="jetbase-form__input jetbase-form__input--text medium" type="text" placeholder="<?php _e("Old base URL", "gf_image_choices"); ?>" />
                        </div>
                    </div>
                    <div class="jetbase-form__field gform-settings-field">
                        <div class="gform-settings-field__header">
                            <label for="image_choices_url_replacement_to_input" class="jetbase-form__label gform-settings-label"><?php _e("To", "gf_image_choices"); ?></label>
                        </div>
                        <div class="gform-settings-input__container">
                            <input id="image_choices_url_replacement_to_input" name="image_choices_url_replacement_to" class="jetbase-form__input jetbase-form__input--text medium" type="text" placeholder="<?php _e("New base URL", "gf_image_choices"); ?>" />
                        </div>
                    </div>
                    <div class="jetbase-tool__progress">
                        <div class="jetbase-tool__progress-status"></div>
                        <div class="jetbase-tool__progress-bar">
                            <div class="jetbase-tool__progress-percent"></div>
                        </div>
                    </div>
                </div>
                <div class="jetbase-tool__footer">
                    <button id="image_choices_url_replacement_submit" type="button" class="jetbase-tool__btn button primary"><?php _e("Go", "gf_image_choices"); ?></button>
                    <div class="jetbase-spinner"></div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_tools_image_replacement_tab_html( $forms = array() ) {
	    if ( empty($forms) ) {
		    $forms = $this->get_image_choices_form_ids( true );
	    }
	    ob_start();
	    ?>
        <div id="image_choices_image_replacement" class="jetbase-tool">
            <div class="jetbase-tool__inner">
                <div class="jetbase-tool__header">
                    <div class="jetbase-tool__description gform-settings-description">
				        <?php _e("This tool will help you with an update of the media image size for all existing Image Choices.", "gf_image_choices"); ?><br/>
                    </div>
                </div>
                <div class="jetbase-tool__body">
                    <div class="jetbase-form__field gform-settings-field">
                        <div class="gform-settings-field__header">
                            <label for="image_choices_image_replacement_form_select" class="jetbase-form__label gform-settings-label"><?php _e("Form", "gf_image_choices"); ?></label>
                        </div>
                        <div class="gform-settings-input__container">
                            <select id="image_choices_image_replacement_form_select" name="image_choices_image_replacement_form" class="jetbase-form__input jetbase-form__input--select medium">
                                <option value="all" selected><?php _e('All forms', 'gf_image_choices'); ?></option>
                                <?php if (!empty($forms)): foreach( $forms as $form ): ?>
                                    <option value="<?php echo $form['id']; ?>"><?php echo $form['title']; ?></option>
				                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="jetbase-form__field gform-settings-field">
                        <div class="gform-settings-field__header">
                            <label for="image_choices_image_replacement_size_select" class="jetbase-form__label gform-settings-label"><?php _e("New Size", "gf_image_choices"); ?></label>
                        </div>
                        <div class="gform-settings-input__container">
                            <select id="image_choices_image_replacement_size_select" name="image_choices_image_replacement_size" class="jetbase-form__select medium">
                                <option value=""><?php _e("Select a size", "gf_image_choices"); ?></option>
		                        <?php echo $this->get_settings_select_options_html( $this->get_media_image_sizes_choices() ); ?>
                            </select>
                        </div>
                    </div>
                    <div class="jetbase-tool__progress">
                        <div class="jetbase-tool__progress-status"></div>
                        <div class="jetbase-tool__progress-bar">
                            <div class="jetbase-tool__progress-percent"></div>
                        </div>
                    </div>
                </div>
                <div class="jetbase-tool__footer">
                    <button id="image_choices_image_replacement_submit" type="button" class="jetbase-tool__btn button primary"><?php _e("Go", "gf_image_choices"); ?></button>
                    <div class="jetbase-spinner"></div>
                </div>
            </div>
        </div>
	    <?php
	    return ob_get_clean();
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

        /*
		$legacy_markup_section = array();
		if ( GFCommon::is_legacy_markup_enabled( $form ) && $this->form_contains_image_choices_fields($form) ) {

			ob_start();
			?>
            <div id="image_choices_settings_legacy_notice" class="jetbase-alert jetbase-alert--warning">
                <div class="jetbase-alert__inner">
                    <div class="jetbase-alert__body">
                        <div class="alert gforms_note_warning">
                            <p><?php _e("Image Choices 1.4+ does not support legacy markup. Please update your forms settings and turn off legacy markup if you would like to continue using Image Choices in this form.", "gf_image_choices"); ?></p>
                            <p><a class="gform-button gform-button--white gform-button--size-xs" href="<?php echo esc_url( admin_url( "admin.php?subview=settings&page=gf_edit_forms&view=settings&id={$form['id']}" ) ); ?>#gform_setting_markupVersion" aria-label="">Form settings</a></p>
                        </div>
                    </div>
                </div>
            </div>
			<?php
			$legacy_warning = ob_get_clean();
			$legacy_markup_section = array(
				'title' => esc_html__( 'Legacy Form Markup', 'gf_image_choices' ),
				'fields' => array(
					array(
						'name' => 'gf_image_choices_legacy_html_warning',
						'label' => '',
						'type' => 'html',
						'html' => $legacy_warning
					)
				)
			);

		}
        */

		$settings = $this->get_form_settings( $form );
		$plugin_settings = $this->get_plugin_settings();
        $use_new_features = $this->use_new_features();

        if ( $use_new_features ) {

	        // THEME SETTING
	        $display_theme_choices = $this->get_settings_theme_choices();

	        $global_theme_value = $this->get_plugin_settings_value("gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings);
	        $form_theme_value = $this->get_form_settings_value("gf_image_choices_theme", "global_setting", $form, $settings);
	        $form_theme_field = array(
		        'name' => 'gf_image_choices_theme',
		        'label' => esc_html__( 'Theme', 'gf_image_choices' ),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array_merge(
			        array(
				        array(
					        'value' => 'global_setting',
					        'label' => sprintf( esc_html__("Use Global Setting (%s)", 'gf_image_choices'), $display_theme_choices[$global_theme_value] )
				        )
			        ),
			        $this->get_settings_select_choices_array( $display_theme_choices )
		        )
	        );
	        if ( !empty($form_theme_value) ) {
		        //$form_theme_field['default_value'] = $form_theme_value;
	        }

	        // THEME PREVIEW
	        $form_theme_preview_field = array(
		        'name' => 'gf_image_choices_theme_preview',
		        'label' => '',
		        'type' => 'html',
		        'class' => 'medium',
		        'html' => $this->get_settings_theme_preview_html(),
	        );

	        $theme_section = array(
		        'title' => esc_html__( 'Theme', 'gf_image_choices' ),
		        'class' => 'gform-settings-panel--half',
		        'fields' => array(
			        $form_theme_field,
			        $form_theme_preview_field,
		        )
	        );

	        // FEATURE COLOR
	        $global_feature_color_value = $this->get_plugin_settings_value("gf_image_choices_global_feature_color", $this->_defaultFeatureColor, $plugin_settings);// none or custom
	        $global_feature_color_custom_value = $this->get_plugin_settings_value("gf_image_choices_global_feature_color_custom", $this->_defaultFeatureColorCustom, $plugin_settings);

	        $form_feature_color_value = $this->get_form_settings_value("gf_image_choices_feature_color", "global_setting", $form, $settings);
	        $form_feature_color_field = array(
		        'name' => 'gf_image_choices_feature_color',
		        'label' => esc_html__('Feature Color', 'gf_image_choices'),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array(
			        array(
				        'value' => 'global_setting',
				        'label' => ( $global_feature_color_value == "custom" && !empty($global_feature_color_custom_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), $global_feature_color_custom_value ) : sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucwords($global_feature_color_value) )
			        ),
			        array(
				        'value' => 'custom',
				        'label' => esc_html__('Custom', 'gf_image_choices')
			        ),
		        )
	        );
	        if (!empty($form_feature_color_value)) {
		        $form_feature_color_field['default_value'] = $form_feature_color_value;
	        }

	        // FEATURE COLOR - CUSTOM
	        $form_feature_color_custom_value = $this->get_form_settings_value("gf_image_choices_feature_color_custom", "", $form, $settings);
	        $form_feature_color_custom_field = array(
		        'name' => 'gf_image_choices_feature_color_custom',
		        'label' => esc_html__('Custom Feature Color', 'gf_image_choices'),
		        'default_value' => $form_feature_color_custom_value,
		        'type' => 'text',
		        'class' => 'medium'
	        );

	        $feature_color_section = array(
		        'title' => esc_html__( 'Feature Color', 'gf_image_choices' ),
		        'class' => 'gform-settings-panel--half',
		        'fields' => array(
			        $form_feature_color_field,
			        $form_feature_color_custom_field,
		        )
	        );

	        // ALIGNMENT
	        $global_alignment_value = $this->get_plugin_settings_value("gf_image_choices_global_align", $this->_defaultAlignment, $plugin_settings);
	        $form_alignment_value = $this->get_form_settings_value("gf_image_choices_align", "global_setting", $form, $settings);
	        $form_alignment_field = array(
		        'name' => 'gf_image_choices_align',
		        'label' => esc_html__('Choices Alignment', 'gf_image_choices'),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array_merge(
			        array(
				        array(
					        'value' => 'global_setting',
					        'label' => ( !empty($global_alignment_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucwords($global_alignment_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
				        )
			        ),
			        $this->get_settings_select_choices_array( $this->get_settings_align_choices() )
		        )
	        );
	        if (!empty($form_alignment_value)) {
		        $form_alignment_field['default_value'] = $form_alignment_value;
	        }

	        // COLUMNS
	        $global_columns_value = $this->get_plugin_settings_value("gf_image_choices_global_columns", $this->_defaultColumns, $plugin_settings);
	        $global_columns_width_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_width", "", $plugin_settings);
            if ( $global_columns_value === "fixed" ) {
	            $global_columns_value = ( !empty($global_columns_width_value) ) ? "Fixed ({$global_columns_width_value}px)" : "Fixed (theme default)";
            }
	        $form_columns_value = $this->get_form_settings_value("gf_image_choices_columns", "global_setting", $form, $settings);
	        $form_columns_field = array(
		        'name' => 'gf_image_choices_columns',
		        'label' => esc_html__('Columns', 'gf_image_choices'),
                'tooltip' => __('<h6>Columns</h6>Control the fluid and responsive layout of your choices<br/><br/>Fixed Width: Will use a fixed pixel width for columns vs a fluid layout<br/><br/>Auto: Automatic fluid columns based on the number of choices<br/><br/>1 - 12: Choose the column number that works for you.', 'gf_image_choices'),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array_merge(
			        array(
				        array(
					        'value' => 'global_setting',
					        'label' => ( !empty($global_columns_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucfirst($global_columns_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
				        )
			        ),
			        $this->get_settings_select_choices_array( $this->get_settings_columns_choices() )
		        )
	        );
	        if (!empty($form_columns_value)) {
		        $form_columns_field['default_value'] = $form_columns_value;
	        }

	        $form_columns_width_value = $this->get_form_settings_value("gf_image_choices_columns_width", "", $form, $settings);
	        $form_columns_width_field = array(
		        'name' => 'gf_image_choices_columns_width',
		        'label' => esc_html__('Column Width', 'gf_image_choices'),
		        'type' => 'text',
		        'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
		        'class' => 'medium',
		        'dependency' => array(
			        'live' => true,
			        'fields' => array(
				        array(
					        'field' => 'gf_image_choices_columns',
					        'values' => array('fixed'),
				        ),
			        )
		        )
	        );
	        if ( !empty($form_columns_width_value) ) {
		        $form_columns_width_field['default_value'] = $form_columns_width_value;
	        }

	        // COLUMNS - MEDIUM
	        $global_columns_medium_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_medium", $this->_defaultColumns, $plugin_settings);
	        $global_columns_medium_width_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_width_medium", "", $plugin_settings);
	        if ( $global_columns_medium_value === "fixed" ) {
		        $global_columns_medium_value = ( !empty($global_columns_medium_width_value) ) ? "Fixed ({$global_columns_medium_width_value}px)" : "Fixed (theme default)";
	        }
	        $form_columns_medium_value = $this->get_form_settings_value("gf_image_choices_columns_medium", "global_setting", $form, $settings);
	        $form_columns_medium_field = array(
		        'name' => 'gf_image_choices_columns_medium',
		        'label' => esc_html__('Medium Columns (at medium screen sizes)', 'gf_image_choices'),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array_merge(
			        array(
				        array(
					        'value' => 'global_setting',
					        'label' => ( !empty($global_columns_medium_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucfirst($global_columns_medium_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
				        )
			        ),
			        $this->get_settings_select_choices_array( $this->get_settings_columns_choices() )
		        )
	        );
	        if (!empty($form_columns_medium_value)) {
		        $form_columns_medium_field['default_value'] = $form_columns_medium_value;
	        }

	        $form_columns_medium_width_value = $this->get_form_settings_value("gf_image_choices_columns_width_medium", "", $form, $settings);
	        $form_columns_medium_width_field = array(
		        'name' => 'gf_image_choices_columns_width_medium',
		        'label' => esc_html__('Column Width', 'gf_image_choices'),
		        'type' => 'text',
		        'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
		        'class' => 'medium',
		        'dependency' => array(
			        'live' => true,
			        'fields' => array(
				        array(
					        'field' => 'gf_image_choices_columns_medium',
					        'values' => array('fixed'),
				        ),
			        )
		        )
	        );
	        if ( !empty($form_columns_medium_width_value) ) {
		        $form_columns_medium_width_field['default_value'] = $form_columns_medium_width_value;
	        }

	        // COLUMNS - SMALL
	        $global_columns_small_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_small", $this->_defaultColumns, $plugin_settings);
	        $global_columns_small_width_value = $this->get_plugin_settings_value("gf_image_choices_global_columns_width_small", "", $plugin_settings);
	        if ( $global_columns_small_value === "fixed" ) {
		        $global_columns_small_value = ( !empty($global_columns_small_width_value) ) ? "Fixed ({$global_columns_small_width_value}px)" : "Fixed (theme default)";
	        }
	        $form_columns_small_value = $this->get_form_settings_value("gf_image_choices_columns_small", "global_setting", $form, $settings);
	        $form_columns_small_field = array(
		        'name' => 'gf_image_choices_columns_small',
		        'label' => esc_html__('Small Columns (at small screen sizes)', 'gf_image_choices'),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array_merge(
			        array(
				        array(
					        'value' => 'global_setting',
					        'label' => ( !empty($global_columns_small_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucfirst($global_columns_small_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
				        )
			        ),
			        $this->get_settings_select_choices_array( $this->get_settings_columns_choices() )
		        )
	        );
	        if (!empty($form_columns_small_value)) {
		        $form_columns_small_field['default_value'] = $form_columns_small_value;
	        }

	        $form_columns_small_width_value = $this->get_form_settings_value("gf_image_choices_columns_width_small", "", $form, $settings);
	        $form_columns_small_width_field = array(
		        'name' => 'gf_image_choices_columns_width_small',
		        'label' => esc_html__('Column Width', 'gf_image_choices'),
		        'type' => 'text',
		        'placeholder' => esc_html__('px value or leave blank for theme default', 'gf_image_choices'),
		        'class' => 'medium',
		        'dependency' => array(
			        'live' => true,
			        'fields' => array(
				        array(
					        'field' => 'gf_image_choices_columns_small',
					        'values' => array('fixed'),
				        ),
			        )
		        )
	        );
	        if ( !empty($form_columns_small_width_value) ) {
		        $form_columns_small_width_field['default_value'] = $form_columns_small_width_value;
	        }

	        $layout_section = array(
		        'title' => esc_html__( 'Choices Layout', 'gf_image_choices' ),
		        'class' => 'gform-settings-panel--half',
		        'fields' => array(
			        $form_alignment_field,
			        $form_columns_field,
                    $form_columns_width_field,
			        $form_columns_medium_field,
			        $form_columns_medium_width_field,
			        $form_columns_small_field,
			        $form_columns_small_width_field,
		        )
	        );

	        // IMAGE STYLE
	        $global_image_style_value = $this->get_plugin_settings_value("gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings);
	        $form_image_style_value = $this->get_form_settings_value("gf_image_choices_image_style", "global_setting", $form, $settings);
	        $form_image_style_field = array(
		        'name' => 'gf_image_choices_image_style',
		        'label' => esc_html__('Image Display Style', 'gf_image_choices'),
		        'type' => 'select',
		        'class' => 'medium',
		        'choices' => array_merge(
			        array(
				        array(
					        'value' => 'global_setting',
					        'label' => ( !empty($global_image_style_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucwords($global_image_style_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
				        )
			        ),
			        $this->get_settings_select_choices_array( $this->get_settings_image_style_choices() )
		        )
	        );
	        if (!empty($form_image_style_value)) {
		        $form_image_style_field['default_value'] = $form_image_style_value;
	        }

        }

		// LAZY LOAD IMAGES
		$lazy_load_global_value = $this->get_plugin_settings_value("gf_image_choices_lazy_load_global", 0, $plugin_settings);
		$lazy_load_global_label = ( empty($lazy_load_global_value) ) ? esc_html__('No', 'gf_image_choices') : esc_html__('Yes', 'gf_image_choices');

		$lazy_load_value = $this->get_form_settings_value("gf_image_choices_lazy_load", "", $form, $settings);
		$lazy_load_field = array(
			'name' => 'gf_image_choices_lazy_load',
			'tooltip' => esc_html__('With lazy load enabled, the images in choices will be loaded only as they enter (or about to enter) the viewport. This reduces initial page load time, initial page weight, and system resource usage, all of which have positive impacts on performance.', 'gf_image_choices'),
			'label' => esc_html__('Lazy Loading', 'gf_image_choices'),
			'type' => 'select',
			'default_value' => $lazy_load_value,
			'choices' => array(
				array(
					'label' => sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), $lazy_load_global_label ),
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

		// LIGHTBOX IMAGE SIZE
		$global_lightbox_size_value = $this->get_plugin_settings_value("gf_image_choices_global_lightbox_size", $this->_defaultLightboxImageSize, $plugin_settings);
		$form_lightbox_size_value = $this->get_form_settings_value("gf_image_choices_lightbox_size", "global_setting", $form, $settings);
		$form_lightbox_size_choices = $use_new_features ? array_merge(
			array(
				array(
					'value' => 'global_setting',
					'label' => ( !empty($global_lightbox_size_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucwords($global_lightbox_size_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
				)
			),
			$this->get_settings_select_choices_array( $this->get_media_image_sizes_choices() )
		) : $this->get_settings_select_choices_array( $this->get_media_image_sizes_choices() );

		$form_lightbox_size_field = array(
			'name' => 'gf_image_choices_lightbox_size',
			'tooltip' => esc_html__('The selected image size will be used in the lightbox, if enabled.', 'gf_image_choices'),
			'label' => esc_html__('Lightbox Image Size', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'default_value' => $this->_defaultLightboxImageSize,
			'choices' => $form_lightbox_size_choices
		);

		if (!empty($form_lightbox_size_value)) {
			$form_lightbox_size_field['default_value'] = $form_lightbox_size_value;
		}


		//$global_item_height_value = $this->get_plugin_settings_value("gf_image_choices_global_height", "", $plugin_settings);
		$form_item_height_value = $this->get_form_settings_value("gf_image_choices_height", "", $form, $settings);
		$form_item_height_field = array(
			'name' => 'gf_image_choices_height',
			'label' => esc_html__('Display Height', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'),
			'class' => 'medium',
		);
		if ( !empty($form_item_height_value) ) {
			$form_item_height_field['default_value'] = $form_item_height_value;
		}

		//$global_item_medium_height_value = $this->get_plugin_settings_value("gf_image_choices_global_height_medium", "", $plugin_settings);
		$form_item_medium_height_value = $this->get_form_settings_value("gf_image_choices_height_medium", "", $form, $settings);
		$form_item_medium_height_field = array(
			'name' => 'gf_image_choices_height_medium',
			'label' => esc_html__('Medium Display Height (at medium screen sizes)', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'),
			'class' => 'medium',
		);
		if ( !empty($form_item_medium_height_value) ) {
			$form_item_medium_height_field['default_value'] = $form_item_medium_height_value;
		}

		//$global_item_small_height_value = $this->get_plugin_settings_value("gf_image_choices_global_height_small", "", $plugin_settings);
		$form_item_small_height_value = $this->get_form_settings_value("gf_image_choices_height_small", "", $form, $settings);
		$form_item_small_height_field = array(
			'name' => 'gf_image_choices_height_small',
			'label' => esc_html__('Small Display Height (at small screen sizes)', 'gf_image_choices'),
			'type' => 'text',
			'placeholder' => esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'),
			'class' => 'medium',
		);
		if ( !empty($form_item_small_height_value) ) {
			$form_item_small_height_field['default_value'] = $form_item_small_height_value;
		}


		$image_options_section = array(
			'title' => esc_html__( 'Image Options', 'gf_image_choices' ),
			'class' => 'gform-settings-panel--half',
			'fields' => array(
				$use_new_features ? $form_image_style_field : array(),
				//$form_image_size_field,
				$lazy_load_field,
				$form_lightbox_size_field,
				$form_item_height_field,
				$form_item_medium_height_field,
				$form_item_small_height_field,
			)
		);

		// MEDIA SIZE
		/*
		$global_image_size_value = $this->get_plugin_settings_value("gf_image_choices_global_image_size", $this->_defaultImageSize, $plugin_settings);
		$form_image_size_value = $this->get_form_settings_value("gf_image_choices_image_size", "global_setting", $settings);
		$form_image_size_field = array(
			'name' => 'gf_image_choices_image_size',
			'tooltip' => esc_html__('The selected image size will be used in the choices on form display.', 'gf_image_choices'),
			'label' => esc_html__('Image Size', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'default_value' => $this->_defaultImageSize,
			'choices' => array_merge(
				array(
					array(
						'value' => 'global_setting',
						'label' => ( !empty($global_columns_value) ) ? sprintf( esc_html__('Use Global Setting (%s)', 'gf_image_choices'), ucwords($global_image_size_value) ) : esc_html__('Use Global Setting', 'gf_image_choices')
					)
				),
				$this->get_settings_select_choices_array( $this->get_media_image_sizes_choices() )
			)
		);
		if ( !empty($form_image_size_value) ) {
			$form_image_size_field['default_value'] = $form_image_size_value;
		}
		*/


        // ENTRY / NOTIFICATION DISPLAY
		$form_choices_entry_value = $this->get_form_settings_value("gf_image_choices_entry_value", "value", $form, $settings);
		$form_choices_entry_field = array(
			'name' => 'gf_image_choices_entry_value',
			'label' => esc_html__('Entry / Notification Display', 'gf_image_choices'),
			'type' => 'select',
			'class' => 'medium',
			'default_value' => 'value',
            'choices' => $this->get_settings_select_choices_array( $this->get_settings_entry_value_choices() )
		);
		if (!empty($form_choices_entry_value)) {
			$form_choices_entry_field['default_value'] = $form_choices_entry_value;
		}

		$admin_section = array(
			'title' => esc_html__( 'Admin', 'gf_image_choices' ),
			'fields' => array(
				$form_choices_entry_field,
			)
		);


		// IGNORE GLOBAL CSS
		$form_ignore_global_css_field = array(
			'name' => 'gf_image_choices_ignore_global_css',
			'type' => 'toggle',
			'label' => __( 'Ignore Global Custom CSS for this form?', 'gf_image_choices' ),
			'tooltip' => __('If checked, the custom css entered in the global settings won\'t be loaded for this form. <br/><br/>IMPORTANT NOTE: multiple forms on a single page with conflicting settings to ignore global CSS may not work as expected.', 'gf_image_choices'),
		);

        if ( $use_new_features ) {

	        // CUSTOM CSS
	        $form_custom_css_value = $this->get_plugin_settings_value('gf_image_choices_user_css_form', '', $plugin_settings);
	        $form_custom_css_field = array(
		        'name' => 'gf_image_choices_user_css_form',
		        'tooltip' => esc_html__('These styles will be loaded for this form only.', 'gf_image_choices'),
		        'label' => esc_html__('Enter your own css to style image choices or override any variables', 'gf_image_choices'),
		        'type' => 'textarea',
		        'class' => 'large',
		        'default_value' => $form_custom_css_value
	        );
	        $form_custom_css_section = array(
		        'type' => 'section',
		        'title'  => esc_html__('Custom CSS', 'gf_image_choices'),
		        'fields' => array(
			        $form_custom_css_field,
			        $form_ignore_global_css_field
		        )
	        );

        }

		$legacy_form_custom_css_value = $this->get_form_settings_value("gf_image_choices_custom_css", "", $form, $settings);
		$legacy_form_custom_css_field = array(
			'name' => 'gf_image_choices_custom_css',
			'tooltip' => $use_new_features ? null : esc_html__('These styles will be loaded for this form only.<br/>Find examples at <a href="https://jetsloth.com/support/gravity-forms-image-choices/">https://jetsloth.com/support/gravity-forms-image-choices/</a>', 'gf_image_choices'),
			'label' => $use_new_features ? esc_html__("Note: With new styles enabled, legacy CSS is ignored as it's most likely not needed. Use the Custom CSS box above for any new styling.", 'gf_image_choices') : esc_html__('Custom CSS', 'gf_image_choices'),
			'type' => 'textarea',
			'class' => 'large',
			'default_value' => $legacy_form_custom_css_value
		);
		$legacy_form_custom_css_section = array(
			'type' => 'section',
			'title'  => $use_new_features ? esc_html__('Legacy CSS', 'gf_image_choices') : esc_html__('Enter your own css to style image choices in this form', 'gf_image_choices'),
			'id' => 'gf_image_choices_legacy_custom_form_css_section',
			'collapsible' => true,
			'is_collapsed' => $use_new_features,
			'fields' => array(
				$legacy_form_custom_css_field
			)
		);


        if ( $use_new_features ) {
	        return array(
		        $theme_section,
		        $feature_color_section,
		        $layout_section,
		        $image_options_section,
		        $admin_section,
		        $form_custom_css_section,
		        $legacy_form_custom_css_section
	        );
        }
        else {
            return array(
	            array(
		            'title' => esc_html__( 'Image Choices', 'gf_image_choices' ),
		            'fields' => array(
			            $form_choices_entry_field,
			            $form_lightbox_size_field,
			            $lazy_load_field,
			            $legacy_form_custom_css_field,
			            $form_ignore_global_css_field
		            )
	            )
            );
        }

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

			return $this->maybe_format_field_values( $value, $field, $form, $entry, $field_id );
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
		$contains_product_image_fields = false;

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
				'product_image_field' => false,
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

				if ( $this->field_has_product_image_enabled($field) ) {
					$field_choices_entry_setting = $this->get_field_settings_value('entrySetting', 'form_setting', $field, true);
					$data['product_image_field'] = true;
					$data['product_field_label'] = $field->label;
					$data['product_field_selected_label'] = $field->label;
					$data['product_field_entry_value_type'] = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
					$data['product_field_selected_image'] = $this->get_field_settings_value( 'image', '', $field, true );
					$contains_product_image_fields = true;
				}
				else if ( $this->field_has_image_choices_enabled($field) ) {
					$field_choices_entry_setting = $this->get_field_settings_value('entrySetting', 'form_setting', $field);
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
						if ( $this->field_has_image_choices_enabled($field) ) {
							$field_choices_entry_setting = $this->get_field_settings_value('entrySetting', 'form_setting', $field);
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

		if ($contains_image_choices_fields || $contains_product_image_fields) {
			$image_entry_setting_values = array('src', 'image', 'image_text', 'image_text_price', 'image_price', 'image_value');

			foreach($product_summary_image_choices as $summary_item) {
				if (
					in_array($summary_item['product_field_entry_value_type'], $image_entry_setting_values)
					&& !empty($summary_item['product_field_selected_image'])) {

					$replacement_markup = '';
                    $is_product_image_field = ( !empty($summary_item['product_image_field']) );
                    $css_class = $is_product_image_field ? "gf-image-choices-entry-product-image" : "gf-image-choices-entry-choice-image";
                    if ( $is_product_image_field && empty($summary_item['options']) ) {
	                    $css_class .= " gf-image-choices-entry-product-image-no-options";
                    }

					if ($summary_item['product_field_entry_value_type'] == 'src') {
						$replacement_markup = $summary_item['product_field_selected_image'];
					}
                    else if ( $is_product_image_field ) {
	                    // for Product Image on the order summary table, just show image + label (unless they want image src only)
	                    $replacement_markup = '<div class="'.$css_class.'" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /><div class="product_name">'.$summary_item['product_field_selected_label'].'</div></div>';
                    }
					else if ($summary_item['product_field_entry_value_type'] == 'image' ) {
						$replacement_markup = '<div class="'.$css_class.'" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_text' ) {
						$replacement_markup = '<div class="'.$css_class.'" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_field_selected_label'].'</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_text_price') {
						$replacement_markup = '<div class="'.$css_class.'" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">' . $summary_item['product_field_selected_label'] . ' (' . $summary_item['product_field_selected_price_formatted'] . ')' . '</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_price') {
						$replacement_markup = '<div class="'.$css_class.'" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_field_selected_price_formatted'].'</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_value') {
						$replacement_markup = '<div class="'.$css_class.'" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_summary_display_value'].'</div>';
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

	public function get_product_image_markup($src = '', $text_value = '', $modifier = '') {

		$size = (!empty($modifier) && strlen($modifier) > 5 && substr($modifier, 0, 6) == 'image_') ? substr($modifier, 6) : '80px';

		if ( GFCommon::is_entry_detail() ) {

			$markup = '<div class="gf-image-choices-entry"><div class="gf-product-image-entry-wrap">';
			$markup .= '<span class="gf-product-image-entry-image">';
			if (!empty($src)) {
				$markup .= '<img src="'.$src.'" style="width:'.$size.'; height:auto; max-width:100%;" />';
			}
			$markup .= '</span>';
			if (!empty($text_value)) {
				$markup .= '<span class="gf-product-image-entry-text">'.html_entity_decode($text_value).'</span>';
			}
			$markup .= '</div></div>';

		}
		else {

			$markup = '<div style="display: block; text-align: center;"><span style="display: inline-block; vertical-align: top; margin: 0 10px 20px; text-align: center;">';
			$markup .= '<span style="display: inline-block;">';
			if (!empty($src)) {
				$markup .= '<img src="'.$src.'" style="width:'.$size.'; height:auto; max-width:100%;" />';
			}
			$markup .= '</span>';
			if (!empty($text_value)) {
				$markup .= '<span style="display: block; font-size: 12px;">'.html_entity_decode($text_value).'</span>';
			}
			$markup .= '</span></div>';

		}


		return $markup;
	}

	public function get_choice_image_item_markup($src = '', $text_value = '', $modifier = '') {

		$size = (!empty($modifier) && strlen($modifier) > 5 && substr($modifier, 0, 6) == 'image_') ? substr($modifier, 6) : '80px';

		if (GFCommon::is_entry_detail()) {

			$markup = '<div class="gf-image-choices-entry-choice">';
			//$markup .= '<span class="gf-image-choices-entry-choice-image" style="background-image:url('.$src.')"></span>';
			$markup .= '<span class="gf-image-choices-entry-choice-image">';
			if (!empty($src)) {
				$markup .= '<img src="'.$src.'" style="width:'.$size.'; height:auto; max-width:100%;" />';
			}
			$markup .= '</span>';
			if (!empty($text_value)) {
				$markup .= '<span class="gf-image-choices-entry-choice-text">'.html_entity_decode($text_value).'</span>';
			}
			$markup .= '</div>';

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
			array_unshift($choice_images_markup, '<div class="gf-image-choices-entry">');
			array_push($choice_images_markup, '</div>');
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

		$is_supported_field = false;
		$is_product_image_enabled = $this->field_has_product_image_enabled($field);
		$is_image_choices_enabled = $this->field_has_image_choices_enabled($field);

        if ( $is_product_image_enabled ) {
	        $is_supported_field = $this->field_supports_product_image($field);
        }
        else if ( $is_image_choices_enabled ) {
	        $is_supported_field = $this->field_supports_image_choices($field);
        }

		if ( !$is_supported_field || ( !$is_image_choices_enabled && !$is_product_image_enabled ) ) {
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
		$form_choices_entry_setting = $this->get_form_settings_value('gf_image_choices_entry_value', 'value', $form, $settings);
        $field_choices_entry_setting = $this->get_field_settings_value('entrySetting', 'form_setting', $field, $is_product_image_enabled);
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

        if ( $is_product_image_enabled ) {

	        $image = $this->get_field_settings_value( 'image', '', $field, true );
	        $text = $field->label;
	        $price = $field->basePrice;
	        //GFCommon::format_number($price, 'currency')

	        if ($is_image_specific_modifier) {
		        return $this->get_product_image_markup($image, "", $modifier);
	        }
	        else if ($is_src_specific_modifier) {
		        return $image;
	        }
	        else if ($field_entry_value_type == 'text') {
		        return $text;
	        }
	        else if (!empty($image)) {
		        $image_item_markup = '';
		        if ($field_entry_value_type == 'src') {
			        $image_item_markup = $image;
		        }
		        else if ($field_entry_value_type == 'image') {
			        $image_item_markup = $this->get_product_image_markup($image);
		        }
		        else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
			        $image_item_markup = $this->get_product_image_markup($image, $text);
		        }
		        else if ($field_entry_value_type == 'image_text_price') {
			        if ( !empty($price) ) {
				        $image_item_markup = $this->get_product_image_markup($image, $text . ' (' . GFCommon::format_number($price, 'currency') . ')');
			        }
			        else {
				        // if it's not a product or option field, just return the image and label
				        $image_item_markup = $this->get_product_image_markup($image, $text);
			        }
		        }
		        else if ($field_entry_value_type == 'image_price') {
			        if ( !empty($price) ) {
				        $image_item_markup = $this->get_product_image_markup($image, GFCommon::format_number($price, 'currency'));
			        }
			        else {
				        // if it's not a product or option field, just return the image
				        $image_item_markup = $this->get_product_image_markup($image);
			        }
		        }

		        return $image_item_markup;
	        }


        }
		else if ( $field_input_type == 'checkbox' ) {

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

		if ( !$this->form_contains_image_choices_fields($form) ) {
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
			if ( $this->field_has_image_choices_enabled( $field ) ) {
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
			foreach ($field->choices as $i => $choice) {
				array_push($choices_data, array(
					'text' => $choice['text'],
					'value' => $choice['value'],
					'index' => $i,
					'image' => (isset($choice['imageChoices_image'])) ? $choice['imageChoices_image'] : '',
					'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : '',
					'largeImage' => (isset($choice['imageChoices_largeImage'])) ? $choice['imageChoices_largeImage'] : ''
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
			foreach($li_elems as $i => $li) {
				$cls = '';
				if ($li->hasAttribute('class')) {
					$cls = $li->getAttribute('class') . ' ';
				}
				$cls .= 'gquiz-image-choices-choice';
				$li->setAttribute('class', $cls);
				$li->setAttribute('style', 'display: inline-block; vertical-align: top; margin: 0 10px 20px; text-align: center; position: relative; text-align: center;');// inline styles for notification email

				$choice = $choices_data[$i];

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
	 * @param string $entry_column_id The field or input ID currently being processed. Used for Product Image
	 *
	 * @return string
	 */

	public function maybe_format_field_values( $value, $field, $form, $entry, $entry_column_id = '' ) {

		if ( empty($field) || !is_object( $field ) ) {
			return $value;
		}

		$is_for_product_image = $this->field_has_product_image_enabled( $field );


		// ---------
		$settings = $this->get_form_settings( $form );
		$form_choices_entry_setting = (isset($settings['gf_image_choices_entry_value'])) ? $settings['gf_image_choices_entry_value'] : 'value';
        $field_choices_entry_setting = $this->get_field_settings_value( 'entrySetting', 'form_setting', $field, $is_for_product_image );
		$field_entry_value_type = ($field_choices_entry_setting == 'form_setting') ? $form_choices_entry_setting : $field_choices_entry_setting;
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_text_price', 'image_price', 'image_value');
		// ---------

		$form_theme_setting = (isset($settings['gf_image_choices_theme'])) ? $settings['gf_image_choices_theme'] : "global_setting";
        $field_theme_setting = $this->get_field_settings_value( 'theme', 'form_setting', $field, $is_for_product_image );
		$field_theme = ($field_theme_setting == 'form_setting') ? $form_theme_setting : $field_theme_setting;

		$real_value = RGFormsModel::get_lead_field_value( $entry, $field );

        if ( $field_entry_value_type == 'value' ) {
            return $value;
        }
        else if ( $is_for_product_image ) {
            // PRODUCT IMAGE

            // We'll only do a replace if the selected column is for the product Label/Name
            // $entry_column_id will only be present for entry table/list view
            if ( !empty($entry_column_id) && strpos($entry_column_id, ".1") === FALSE ) {
                return $value;
            }

	        $image = $this->get_field_settings_value( 'image', '', $field, $is_for_product_image );
	        $field_id = rgobj($field, 'id');

	        /*
			$real_value for product is Label, Price, Qty
			Eg:
			Array (
				[1.1] => Simple
				[1.2] => $ 105.00
				[1.3] => 1
			)
			*/

	        $text = ( !empty($real_value) && isset($real_value["{$field_id}.1"]) ) ? $real_value["{$field_id}.1"] : RGFormsModel::get_label($field);
	        $price = ( !empty($real_value) && isset($real_value["{$field_id}.2"]) ) ? $real_value["{$field_id}.2"] : rgobj($field, 'basePrice');
            $hasQty = empty( rgobj($field, 'disableQuantity') );
            $qty = null;
            if ( $hasQty ) {
	            $qty = ( !empty($real_value) && isset($real_value["{$field_id}.3"]) && !empty($real_value["{$field_id}.3"]) ) ? (int) $real_value["{$field_id}.3"] : 0;
            }

	        if ( $field_entry_value_type == 'text' ) {
		        // Label
		        return $text;
	        }
	        else if (in_array($field_entry_value_type, $image_entry_setting_values) && !empty($image)) {

		        if ($field_entry_value_type == 'src') {
			        // Image URL
			        return $image;
		        }
		        else {

			        $image_item_markup = '';
			        $text_value = "{$text}";

			        if ($field_entry_value_type == 'image') {
				        // Image only
				        $image_item_markup = $this->get_product_image_markup($image);
			        }
			        else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
				        // Image + Label
				        $image_item_markup = $this->get_product_image_markup($image, $text_value);
			        }
			        else if ($field_entry_value_type == 'image_text_price') {
                        // Image + Label + Price
				        if ( !empty($price) ) {
					        $text_value .= '<br/>(' . GFCommon::format_number($price, 'currency') . ')';
				        }
				        $image_item_markup = $this->get_product_image_markup($image, $text_value);
			        }
			        else if ($field_entry_value_type == 'image_price') {
				        // Image + Price
				        $text_value = "";
				        if ( !empty($price) ) {
					        $text_value .= '(' . GFCommon::format_number($price, 'currency') . ')';
				        }
				        $image_item_markup = $this->get_product_image_markup($image, $text_value);
			        }

			        return $image_item_markup;
		        }
	        }

            return $value;

        }
        else if ( $this->get_field_settings_value( 'enableImages', false, $field ) ) {

	        // IMAGE CHOICES

            $type_property = ($field->type == 'survey' || $field->type == 'poll' || $field->type == 'quiz' || $field->type == 'post_custom_field' || $field->type == 'product' || $field->type == 'option') ? 'inputType' : 'type';

            // Product field doesn't have checkboxes, only radio
            // Option field has both

            if (
                ( $field[$type_property] == 'checkbox' || apply_filters( 'gfic_is_supported_multi_value_field', false, $field ) )
                && ( strpos($value, ', ') !== FALSE || strpos($value, "<ul class='bulleted'>") !== FALSE )
            ) {

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
                if (
                    ($field->type == 'checkbox' || apply_filters( 'gfic_is_supported_multi_value_field', false, $field ))
                    || ( $field->type == 'post_custom_field' && $field->inputType == 'checkbox' )
                ) {

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
                            'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : '',
                            'largeImage' => (isset($choice['imageChoices_largeImage'])) ? $choice['imageChoices_largeImage'] : ''
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
                            'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : '',
                            'largeImage' => (isset($choice['imageChoices_largeImage'])) ? $choice['imageChoices_largeImage'] : ''
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
                else if (
                    $field->type == 'radio'
                    || $field->type == 'post_custom_field'
                    || ($field->type == 'quiz' && !$field->is_entry_detail())
                    || ($field->type == 'poll' && !$field->is_entry_detail())
                    || ($field->type == 'survey' && !$field->is_entry_detail())
                    || apply_filters( 'gfic_is_supported_single_value_field', false, $field )
                ) {

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

		/////// product image

        if ( $this->field_has_product_image_enabled($field) ) {

	        $use_new_features = $this->use_new_features();
            if ( !$use_new_features ) {
                return $classes;
            }

	        $plugin_settings = $this->get_plugin_settings();
	        $form_settings = $this->get_form_settings( $form );

	        $classes .= (GFCommon::is_form_editor()) ? ' product-image-admin-field ' : ' product-image-field ';

	        $image_url = $this->get_field_settings_value("image", "", $field, true);
	        $image_id = $this->get_field_settings_value("imageId", "", $field, true);
            if ( !empty($image_url) ) {
	            $classes .= 'has-product-image ';
            }

	        if ( $this->get_field_settings_value("useLightbox", false, $field, true) ) {
		        $classes .= 'product-image-use-lightbox ';
	        }

	        $global_theme_setting = $this->get_plugin_settings_value("gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings);
	        $form_theme_setting = $this->get_form_settings_value("gf_image_choices_theme", "global_setting", $form, $form_settings);
	        $field_theme_setting = $this->get_field_settings_value("theme", "form_setting", $field, true);
	        $field_theme = $this->get_field_setting_fallback_value( $field_theme_setting, $form_theme_setting, $global_theme_setting );

	        if ( $field_theme !== "none" ) {
		        $classes .= "product-image-theme--{$field_theme} ";
	        }

	        $global_image_style_setting = $this->get_plugin_settings_value("gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings);
	        $form_image_style_setting = $this->get_form_settings_value("gf_image_choices_image_style", "global_setting", $form, $form_settings);
	        $field_image_style_setting = $this->get_field_settings_value("imageStyle", "form_setting", $field, true);
	        $field_image_style = $this->get_field_setting_fallback_value( $field_image_style_setting, $form_image_style_setting, $global_image_style_setting );
	        if ( $field_image_style != "" ) {
		        $classes .= "product-image--{$field_image_style} ";
	        }

	        $field_lightbox_captions_setting = $this->get_field_settings_value("useLightboxCaption", true, $field, true);
	        if ( !empty($field_lightbox_captions_setting) && $field_lightbox_captions_setting !== "No" && $field_lightbox_captions_setting !== "false" ) {
		        $classes .= "product-image-lightbox-captions ";
	        }

	        $lazy_load = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0, $plugin_settings);
//	        $lazy_load_global_value = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0, $plugin_settings);
//	        $form_lazy_load_value = $this->get_form_settings_value('gf_image_choices_lazy_load', "global_setting", $form, $form_settings);
//	        $lazy_load = $this->get_field_setting_fallback_value("form_setting", $form_lazy_load_value, $lazy_load_global_value);

	        if ( !empty($lazy_load) && !is_admin() ) {
		        $classes .= 'has-jetsloth-lazy ';
	        }

            return $classes;
        }

        /////// image choices

		if ( !$this->field_has_image_choices_enabled($field) ) {
			return $classes;
		}

        if ( !isset($plugin_settings) ) {
	        $plugin_settings = $this->get_plugin_settings();
        }
        if ( !isset($form_settings) ) {
	        $form_settings = $this->get_form_settings( $form );
        }
        if ( !isset($use_new_features) ) {
	        $use_new_features = $this->use_new_features();
        }

		$classes .= (GFCommon::is_form_editor()) ? ' image-choices-admin-field ' : ' image-choices-field ';
		$classes .= 'image-choices-use-images ';

		if ( $this->get_field_settings_value("showLabels", true, $field) ) {
			$classes .= 'image-choices-show-labels ';
		}
		if ( $this->get_field_settings_value("useLightbox", false, $field) ) {
			$classes .= 'image-choices-use-lightbox ';
		}

        if ( $use_new_features ) {
	        $global_theme_setting = $this->get_plugin_settings_value("gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings);
	        $form_theme_setting = $this->get_form_settings_value("gf_image_choices_theme", "global_setting", $form, $form_settings);
	        $field_theme_setting = $this->get_field_settings_value("theme", "form_setting", $field);
	        $field_theme = $this->get_field_setting_fallback_value( $field_theme_setting, $form_theme_setting, $global_theme_setting );
	        if ( $field_theme !== "none" ) {
		        $classes .= "ic-theme--{$field_theme} ";
	        }

	        $global_image_style_setting = $this->get_plugin_settings_value("gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings);
	        $form_image_style_setting = $this->get_form_settings_value("gf_image_choices_image_style", "global_setting", $form, $form_settings);
	        $field_image_style_setting = $this->get_field_settings_value("imageStyle", "form_setting", $field);
	        $field_image_style = $this->get_field_setting_fallback_value( $field_image_style_setting, $form_image_style_setting, $global_image_style_setting );
	        if ( $field_image_style != "default" ) {
		        $classes .= "ic-image--{$field_image_style} ";
	        }

	        $global_align_setting = $this->get_plugin_settings_value("gf_image_choices_global_align", $this->_defaultAlignment, $plugin_settings);
	        $form_align_setting = $this->get_form_settings_value("gf_image_choices_align", "global_setting", $form, $form_settings);
	        $field_align_setting = $this->get_field_settings_value("align", "form_setting", $field);
	        $field_align = $this->get_field_setting_fallback_value( $field_align_setting, $form_align_setting, $global_align_setting );
	        if ( $field_align != "default" ) {
		        $classes .= "ic-align--{$field_align} ";
	        }

	        $global_columns_setting = $this->get_plugin_settings_value("gf_image_choices_global_columns", $this->_defaultColumns, $plugin_settings);
	        $form_columns_setting = $this->get_form_settings_value("gf_image_choices_columns", "global_setting", $form, $form_settings);
	        $field_columns_setting = $this->get_field_settings_value("columns", "form_setting", $field);
	        $field_columns = $this->get_field_setting_fallback_value( $field_columns_setting, $form_columns_setting, $global_columns_setting );
	        $classes .= "ic-cols--{$field_columns} ";

	        $global_columns_medium_setting = $this->get_plugin_settings_value("gf_image_choices_global_columns_medium", $this->_defaultColumns, $plugin_settings);
	        $form_columns_medium_setting = $this->get_form_settings_value("gf_image_choices_columns_medium", "global_setting", $form, $form_settings);
	        $field_columns_medium_setting = $this->get_field_settings_value("columnsMedium", "form_setting", $field);
	        $field_columns_medium = $this->get_field_setting_fallback_value( $field_columns_medium_setting, $form_columns_medium_setting, $global_columns_medium_setting );
	        $classes .= "ic-cols-md--{$field_columns_medium} ";

	        $global_columns_small_setting = $this->get_plugin_settings_value("gf_image_choices_global_columns_small", $this->_defaultColumns, $plugin_settings);
	        $form_columns_small_setting = $this->get_form_settings_value("gf_image_choices_columns_small", "global_setting", $form, $form_settings);
	        $field_columns_small_setting = $this->get_field_settings_value("columnsSmall", "form_setting", $field);
	        $field_columns_small = $this->get_field_setting_fallback_value( $field_columns_small_setting, $form_columns_small_setting, $global_columns_small_setting );
	        $classes .= "ic-cols-sm--{$field_columns_small} ";

	        // with new themes and cols, remove any gf_list_*col classes and try replace new auto with some similar overrides
            if ( strpos($classes, "gf_list_") !== FALSE ) {
                $all_classes = explode(" ", $classes);
	            $cols_num = "auto";
                $new_classes = [];
                foreach( $all_classes as $cls ) {
                    if ( substr($cls, 0, 8) != "gf_list_" ) {
                        $new_classes[] = $cls;
                    }
                    else {
	                    $cols_num = str_replace( array("gf_list_", "col"), "", $cls );
                    }
                }

	            if ( $cols_num != "auto" && ( in_array("ic-cols--auto", $new_classes) || in_array("ic-cols-md--auto", $new_classes) || in_array("ic-cols-sm--auto", $new_classes) ) ) {
		            $classes = str_replace(
                            array("ic-cols--auto", "ic-cols-md--auto", "ic-cols-sm--auto"),
                            array("ic-cols--{$cols_num}", "ic-cols-md--{$cols_num}", "ic-cols-sm--2"),
                            implode(" ", $new_classes)
                    );
	            }
                else {
	                $classes = implode(" ", $new_classes);
                }

                $classes .= " ";
            }

        }

		$field_lightbox_captions_setting = $this->get_field_settings_value("useLightboxCaption", true, $field);
		if ( !empty($field_lightbox_captions_setting) && $field_lightbox_captions_setting !== "No" && $field_lightbox_captions_setting !== "false" ) {
			$classes .= "ic-lightbox-captions ";
		}

		$lazy_load = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0, $plugin_settings);
//		$lazy_load_global_value = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0, $plugin_settings);
//		$form_lazy_load_value = $this->get_form_settings_value('gf_image_choices_lazy_load', "global_setting", $form, $form_settings);
//		$lazy_load = $this->get_field_setting_fallback_value("form_setting", $form_lazy_load_value, $lazy_load_global_value);
		if ( !empty($lazy_load) && !is_admin() ) {
			$classes .= 'has-jetsloth-lazy ';
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
		$tooltips['image_choices_use_lightbox'] = '<h6>' . esc_html__( 'Use Lightbox', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the user will be able to preview large versions of each image in a lightbox.', 'gf_image_choices' );
		$tooltips['image_choices_use_lightbox_captions'] = '<h6>' . esc_html__( 'Use Lightbox Captions', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the choice label text will be displayed in the lightbox as the image caption.', 'gf_image_choices' );
		$tooltips['image_choices_show_prices'] = '<h6>' . esc_html__( 'Show Prices', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the product price will be displayed below the image.', 'gf_image_choices' );
		$tooltips['image_choices_show_labels'] = '<h6>' . esc_html__( 'Show Labels', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the choices labels will be displayed along with the image.', 'gf_image_choices' );
		$tooltips['image_choices_columns'] = __('<h6>Columns</h6>Control the fluid and responsive layout of your choices<br/><br/>Fixed Width: Will use a fixed pixel width for columns vs a fluid layout<br/><br/>Auto: Automatic fluid columns based on the number of choices<br/><br/>1 - 12: Choose the column number that works for you.', 'gf_image_choices');

		$tooltips['product_image_use_lightbox'] = '<h6>' . esc_html__( 'Use Lightbox', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the user will be able to preview a large version of the image in a lightbox.', 'gf_image_choices' );
		$tooltips['product_image_use_lightbox_caption'] = '<h6>' . esc_html__( 'Use Lightbox Caption', 'gf_image_choices' ) . '</h6>' . esc_html__( 'With this setting, the field label text will be displayed in the lightbox as the image caption.', 'gf_image_choices' );
		return $tooltips;
	}

	/**
	 * Add the custom settings for the fields to the fields general tab.
	 *
	 * @param int $position The position the settings should be located at.
	 * @param int $form_id The ID of the form currently being edited.
	 */
	public function image_choice_field_settings( $position, $form_id ) {
		if ( $position == 1350 ) {
			?>
            <!-- Product Image Toggle -->
            <li class="product-image-setting-use-image field_setting">
                <input type="checkbox" id="field_product_image_enabled" class="field_product_image_enabled" onclick="imageChoicesAdmin.onProductImageToggleClick(this);" onkeypress="imageChoicesAdmin.onProductImageToggleClick(this);"> <label for="field_product_image_enabled"><?php echo esc_html__("Use Product Image", 'gf_image_choices'); ?></label>
            </li>
            <!-- Image Choices Toggle -->
            <li class="image-choices-setting-use-images field_setting">
                <input type="checkbox" id="field_choice_images_enabled" class="field_choice_images_enabled" onclick="imageChoicesAdmin.toggleEnableImages(this.checked);" onkeypress="imageChoicesAdmin.toggleEnableImages(this.checked);"> <label for="field_choice_images_enabled"><?php echo esc_html__("Use Image Choices", 'gf_image_choices'); ?></label>
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
		$tabs[] = array(
			'id' => 'product_image', // Tab id is used later with the action hook that will output content for this specific tab.
			'title' => esc_html__('Product Image', 'gf_image_choices'),
			//'toggle_classes' => '', // Goes into the tab button class attribute.
			//'body_classes'   => '', // Goes into the tab content ( ul tag ) class attribute.
		);

		return $tabs;
	}

    public function get_field_setting_fallback_value( $value, $form_value, $global_value ) {
        if ( $value == "form_setting" ) {
            $value = $form_value;
        }
	    if ( $value == "global_setting" ) {
            $value = $global_value;
        }
        return $value;
    }

	public function get_field_settings_value( $setting_name, $default_value, $field, $for_product_image = false ) {
		if ( empty($field) || empty($setting_name) ) {
			return null;
		}

        $is_product_image_field = ( $for_product_image === true );
        $setting_prefix = $is_product_image_field ? "productImage_" : "imageChoices_";
        $filter_prefix = $is_product_image_field ? "gfpi_" : "gfic_";

        $full_setting_name = "{$setting_prefix}{$setting_name}";
        $form_id = is_object($field) ? $field->formId : $field["formId"];
		$field_id = is_object($field) ? $field->id : $field["id"];

        $filter_name = strtolower(preg_replace(
	        '/(?<=[a-z])([A-Z]+)/',
	        '_$1',
	        str_replace($setting_prefix, $filter_prefix, $full_setting_name)
        ));

        if ( is_object($field) ) {
	        $value = property_exists($field, $full_setting_name) ? $field->{$full_setting_name} : $default_value;
        }
        else {
	        $value = ( isset($field[$full_setting_name]) ) ? $field[$full_setting_name] : $default_value;
        }

		$value = apply_filters( $filter_name, $value, $form_id, $field_id, $default_value );// Eg: "imageChoices_featureColorCustom" will do ALL fields across ALL forms
		$value = apply_filters( "{$filter_name}_{$form_id}", $value, $form_id, $field_id, $default_value );// Eg: "imageChoices_featureColorCustom_2" will do ALL fields in form with id 2
		return apply_filters( "{$filter_name}_{$form_id}_{$field_id}", $value, $form_id, $field_id, $default_value );// Eg: "imageChoices_featureColorCustom_2_1" will do field with id 1 in form with id 2
    }

    public function get_form_settings_value( $setting_name, $default_value, $form_or_id, $form_settings = false ) {
        if ( empty($form_or_id) || empty($setting_name) ) {
            return null;
        }
        $form_id = ( is_int( $form_or_id ) ) ? $form_or_id : rgar( $form_or_id, "id" );
        if ( empty($form_settings) ) {
	        $form = ( is_int( $form_or_id ) ) ? GFAPI::get_form($form_or_id) : $form_or_id;
	        $form_settings = $this->get_form_settings( $form );
        }

	    $filter_name = str_replace("gf_image_choices_", "gfic_", $setting_name);

	    $value = ( isset($form_settings[$setting_name]) ) ? $form_settings[$setting_name] : $default_value;
        $value = apply_filters( $filter_name, $value, $form_id, $default_value );// Eg: "gfic_feature_color_custom" // will do all forms
        return apply_filters( "{$filter_name}_{$form_id}", $value, $form_id, $default_value );// Eg "gfic_feature_color_custom_2" // will do form with id 2
    }

    public function get_plugin_settings_value( $setting_name, $default_value, $plugin_settings = false ) {
        if ( empty($setting_name) ) {
            return null;
        }
        if ( empty($plugin_settings) ) {
            $value = $this->get_plugin_setting( $setting_name );
        }
        else {
	        $value = ( isset($plugin_settings[$setting_name]) ) ? $plugin_settings[$setting_name] : null;
        }
        if ( is_null($value) ) {
            $value = $default_value;
        }

	    $filter_name = str_replace("gf_image_choices_", "gfic_", $setting_name);

	    return apply_filters( $filter_name, $value, $default_value );// Eg "gfic_global_feature_color_custom" (global)
    }

    protected function get_settings_select_options_html( $choices ) {
	    ob_start();
	    foreach( $choices as $choice_value => $choice_label ) {
		    echo '<option value="'.$choice_value.'">'.$choice_label.'</option>';
	    }
	    return ob_get_clean();
    }

    protected function get_settings_select_choices_array( $choices ) {
        $options = array();
	    foreach( $choices as $choice_value => $choice_label ) {
		    $options[] = array(
                'value' => $choice_value,
                'label' => $choice_label,
            );
	    }
	    return $options;
    }

    public function get_media_image_sizes_choices() {

	    global $_wp_additional_image_sizes;
	    $choices = array();

	    $size_names = apply_filters('image_size_names_choose', array(
		    'thumbnail' => esc_html__( 'Thumbnail', 'gf_image_choices' ),
		    'medium' => esc_html__( 'Medium', 'gf_image_choices' ),
		    'large' => esc_html__( 'Large', 'gf_image_choices' ),
		    'full' => esc_html__( 'Full Size', 'gf_image_choices' )
	    ));

	    foreach( get_intermediate_image_sizes() as $_size ) {
		    if ( in_array( $_size, array('thumbnail', 'medium', 'large') ) ) {
			    $label = isset($size_names[$_size]) ? $size_names[$_size] : $_size;
			    $choices[ $_size ] = $label;
		    }
            elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			    $label = isset($size_names[$_size]) ? $size_names[$_size] : $_size;
			    $choices[ $_size ] = $label;
		    }
	    }

	    $choices['full'] = isset($size_names['full']) ? $size_names['full'] : esc_html__( 'Full Size', 'gf_image_choices' );

	    return $choices;

    }

	public function get_settings_theme_choices() {
        return array(
			'simple' => esc_html__("Simple", 'gf_image_choices'),
			'polaroid' => esc_html__("Polaroid", 'gf_image_choices'),
			'float-card' => esc_html__("Float Card", 'gf_image_choices'),
			'cover-tile' => esc_html__("Cover Tile", 'gf_image_choices'),
			'porthole' => esc_html__("Porthole", 'gf_image_choices'),
			'circle' => esc_html__("Circle", 'gf_image_choices'),
			'none' => esc_html__("None", 'gf_image_choices'),
		);
	}

	public function get_settings_theme_preview_html( $for_product_image = false ) {
		$choices = $this->get_settings_theme_choices();
        $class_prefix = ( $for_product_image === true ) ? "product-image" : "image-choices";
        $filename_prefix = ( $for_product_image === true ) ? "product-image-" : "";
        ob_start();
		echo '<div id="'. $class_prefix .'-theme-preview" class="'. $class_prefix .'-theme-preview">';
		foreach( $choices as $choice_value => $choice_label ) {
            if ( empty($choice_value) || $choice_value == 'none' ) {
                continue;
            }
			echo '<img src="'.plugins_url('', __FILE__).'/images/themes/'.$filename_prefix.'theme-'.$choice_value.'.png?v='.$this->_version.'" alt="'.$choice_label.'" class="'. $class_prefix .'-theme-preview-'.$choice_value.'" />';
		}
		echo '</div>';
        $html = ob_get_clean();
        return $html;
    }
    
    public function get_settings_align_choices() {
        return array(
            'default' => esc_html__("Theme Default", 'gf_image_choices'),
            'left' => esc_html__("Left", 'gf_image_choices'),
            'center' => esc_html__("Center", 'gf_image_choices'),
            'right' => esc_html__("Right", 'gf_image_choices'),
        );
    }

	public function get_settings_image_style_choices() {
		return array(
			'default' => esc_html__("Theme Default", 'gf_image_choices'),
			'cover' => esc_html__("Cover", 'gf_image_choices'),
			'contain' => esc_html__("Contained", 'gf_image_choices'),
			'natural' => esc_html__("Natural", 'gf_image_choices'),
		);
	}

	public function get_settings_columns_choices() {
		return array(
			'fixed' => esc_html__("Fixed Width", 'gf_image_choices'),
			'auto' => esc_html__("Auto", 'gf_image_choices'),
			'12' => esc_html__("12", 'gf_image_choices'),
			'11' => esc_html__("11", 'gf_image_choices'),
			'10' => esc_html__("10", 'gf_image_choices'),
			'9' => esc_html__("9", 'gf_image_choices'),
			'8' => esc_html__("8", 'gf_image_choices'),
			'7' => esc_html__("7", 'gf_image_choices'),
			'6' => esc_html__("6", 'gf_image_choices'),
			'5' => esc_html__("5", 'gf_image_choices'),
			'4' => esc_html__("4", 'gf_image_choices'),
			'3' => esc_html__("3", 'gf_image_choices'),
			'2' => esc_html__("2", 'gf_image_choices'),
			'1' => esc_html__("1", 'gf_image_choices'),
		);
	}

	public function get_settings_entry_value_choices( $for_product_image = false ) {
        if ( $for_product_image ) {
	        return array(
		        'value' => esc_html__('Label (Gravity Forms default)', 'gf_image_choices'),
		        'image_text_price' => esc_html__('Image, Label and Price', 'gf_image_choices'),
		        'image_text' => esc_html__('Image and Label', 'gf_image_choices'),
		        'image_price' => esc_html__('Image and Price', 'gf_image_choices'),
		        'image' => esc_html__('Image only', 'gf_image_choices'),
	        );
        }

		return array(
            'value' => esc_html__('Value (Gravity Forms default)', 'gf_image_choices'),
            'image' => esc_html__('Image', 'gf_image_choices'),
            'text' => esc_html__('Label', 'gf_image_choices'),
            'image_text' => esc_html__('Image and Label', 'gf_image_choices'),
            'image_text_price' => esc_html__('Image, Label and Price (Product or Option fields only)', 'gf_image_choices'),
            'image_price' => esc_html__('Image and Price (Product or Option fields only)', 'gf_image_choices'),
            'image_value' => esc_html__('Image and Value', 'gf_image_choices'),
		);
	}

	public function custom_product_image_settings_markup( $form ) {

		$form_settings = $this->get_form_settings( $form );
		$plugin_settings = $this->get_plugin_settings();
		$use_new_features = $this->use_new_features();

        if ( !$use_new_features ) {
            return;
        }

        ?>
        <!-- Product Image -->
        <li class="product-image-setting-url field_setting">
            <label for="product-image-url" class="section_label"><?php echo esc_html__("Product Image", "gf_image_choices"); ?></label>
            <input type="hidden" id="product-image-url" class="product-image-url" value="" />
            <input type="hidden" id="product-image-id" class="product-image-id" value="" />
            <div id="product-image-preview"></div>
            <button type="button" id="product-image-button" class="button product-image-button" onclick="imageChoicesAdmin.OpenMediaLibrary(this);" title="<?php echo esc_html__("Open Media Library", 'gf_image_choices'); ?>"><?php echo esc_html__("Select image", 'gf_image_choices'); ?></button>
            <button type="button" id="product-image-remove-button" class="button product-image-remove-button" onclick="imageChoicesAdmin.updateProductImage('');"><?php echo esc_html__("Remove image", 'gf_image_choices'); ?></button>
        </li>
        <!-- Product Image Theme Settings -->
		<?php
		$display_theme_labels = $this->get_settings_theme_choices();
		$form_setting_display_style = $this->get_form_settings_value( "gf_image_choices_theme", "global_setting", $form, $form_settings );
		$global_setting_display_style = $this->get_plugin_settings_value( "gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings );
		$form_setting_display_style_label = ( $form_setting_display_style == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), $display_theme_labels[$global_setting_display_style] ) : $display_theme_labels[$form_setting_display_style];
		?>
        <li class="product-image-setting-theme field_setting">
            <label for="product-image-theme" class="section_label"><?php echo esc_html__("Display Style"); ?></label>
            <select id="product-image-theme" class="product-image-theme" onchange="imageChoicesAdmin.updateThemeSetting(this.value, true);">
                <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_display_style_label ); ?></option>
				<?php echo $this->get_settings_select_options_html( $this->get_settings_theme_choices() ); ?>
            </select>
			<?php echo $this->get_settings_theme_preview_html(true); ?>
        </li>
        <!-- Product Image Style Settings -->
		<?php
		$form_setting_image_style = $this->get_form_settings_value( "gf_image_choices_image_style", "global_setting", $form, $form_settings );
		$global_setting_image_style = $this->get_plugin_settings_value( "gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings );
		$form_setting_image_style_label = ( $form_setting_image_style == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucfirst($global_setting_image_style) ) : ucfirst($form_setting_image_style);
		?>
        <li class="product-image-setting-style field_setting">
            <label for="product-image-style" class="section_label"><?php echo esc_html__("Image Display Style"); ?></label>
            <select id="product-image-style" class="product-image-style" onchange="imageChoicesAdmin.updateImageStyleSetting(this.value, true);">
                <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_image_style_label ); ?></option>
				<?php echo $this->get_settings_select_options_html( $this->get_settings_image_style_choices() ); ?>
            </select>
        </li>
        <!-- Item Height Setting -->
        <li class="product-image-setting-height field_setting">
            <label for="product-image-height" class="section_label"><?php echo esc_html__("Display Height"); ?></label>
            <input id="product-image-height" class="product-image-height" type="text" placeholder="<?php echo esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateHeightSetting(this.value, true);" />
        </li>
        <!-- Item Medium Height Setting -->
        <li class="product-image-setting-height-medium field_setting">
            <label for="product-image-height-medium" class="section_label"><?php echo esc_html__("Medium Display Height (at medium screen sizes)"); ?></label>
            <input id="product-image-height-medium" class="product-image-height-medium" type="text" placeholder="<?php echo esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateMediumHeightSetting(this.value, true);" />
        </li>
        <!-- Item Small Height Setting -->
        <li class="product-image-setting-height-small field_setting">
            <label for="product-image-height-small" class="section_label"><?php echo esc_html__("Small Display Height (at small screen sizes)"); ?></label>
            <input id="product-image-height-small" class="product-image-height-small" type="text" placeholder="<?php echo esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateSmallHeightSetting(this.value, true);" />
        </li>
        <!-- Product Image Lightbox Setting -->
        <li class="product-image-setting-use-lightbox field_setting">
            <input id="product_image_use_lightbox" class="product_image_use_lightbox" type="checkbox" onclick="imageChoicesAdmin.toggleUseLightbox(this.checked, true);" onkeypress="imageChoicesAdmin.toggleUseLightbox(this.checked, true);"> <label for="product_image_use_lightbox"><?php echo esc_html__("Use lightbox", 'gf_image_choices'); gform_tooltip('product_image_use_lightbox'); ?></label>
        </li>
        <!-- Product Image Lightbox Caption Setting -->
        <li class="product-image-setting-use-lightbox-caption field_setting">
            <input id="product_image_use_lightbox_caption" class="product_image_use_lightbox_caption" type="checkbox" onclick="imageChoicesAdmin.toggleUseLightboxCaption(this.checked, true);" onkeypress="imageChoicesAdmin.toggleUseLightboxCaption(this.checked, true);"> <label for="product_image_use_lightbox_caption"><?php echo esc_html__("Show label text as lightbox caption", 'gf_image_choices'); gform_tooltip('product_image_use_lightbox_caption'); ?></label>
        </li>
        <!-- Product Image Entry / Notification Display Settings -->
        <li class="product-image-setting-entry-value field_setting">
            <label for="product-image-entry-value" class="section_label"><?php echo esc_html__("Entry / Notification Display"); ?></label>
            <select id="product-image-entry-value" class="product-image-entry-value" onchange="imageChoicesAdmin.updateEntrySetting(this.value, true);">
                <option value="form_setting"><?php esc_html_e("Use Form Setting", 'gf_image_choices'); ?></option>
				<?php echo $this->get_settings_select_options_html( $this->get_settings_entry_value_choices( true ) ); ?>
            </select>
        </li>
        <?php

	}

	public function custom_image_choices_settings_markup( $form ) {

		$form_settings = $this->get_form_settings( $form );
		$plugin_settings = $this->get_plugin_settings();
        $use_new_features = $this->use_new_features();

        if ( $use_new_features ): ?>
            <!-- Image Choices Theme Settings -->
            <?php
            $display_theme_labels = $this->get_settings_theme_choices();
            $form_setting_display_style = $this->get_form_settings_value( "gf_image_choices_theme", "global_setting", $form, $form_settings );
            $global_setting_display_style = $this->get_plugin_settings_value( "gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings );
            $form_setting_display_style_label = ( $form_setting_display_style == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), $display_theme_labels[$global_setting_display_style] ) : $display_theme_labels[$form_setting_display_style];
            ?>
            <li class="image-choices-setting-theme field_setting">
                <label for="image-choices-theme" class="section_label"><?php echo esc_html__("Display Style"); ?></label>
                <select id="image-choices-theme" class="image-choices-theme" onchange="imageChoicesAdmin.updateThemeSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_display_style_label ); ?></option>
                    <?php echo $this->get_settings_select_options_html( $this->get_settings_theme_choices() ); ?>
                </select>
                <?php echo $this->get_settings_theme_preview_html(); ?>
            </li>
            <!-- Image Choices Feature Color Settings -->
            <?php
            $form_setting_feature_color = $this->get_form_settings_value( "gf_image_choices_feature_color", "global_setting", $form, $form_settings );
            $global_setting_feature_color = $this->get_plugin_settings_value( "gf_image_choices_global_feature_color", $this->_defaultFeatureColor, $plugin_settings );
            $form_setting_feature_color_label = ( $form_setting_feature_color == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucwords($global_setting_feature_color) ) : ucwords($form_setting_feature_color);
            ?>
            <li class="image-choices-setting-feature-color field_setting">
                <label for="image-choices-feature-color" class="section_label"><?php esc_html_e("Feature Color", 'gf_image_choices'); ?></label>
                <select id="image-choices-feature-color" class="image-choices-feature-color" onchange="imageChoicesAdmin.updateFeatureColorSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_feature_color_label ); ?></option>
                    <option value="custom"><?php esc_html_e("Custom", 'gf_image_choices'); ?></option>
                </select>
                <label for="image-choices-feature-color-custom"><?php esc_html_e("Custom Feature Color", 'gf_image_choices'); ?></label>
                <input id="image-choices-feature-color-custom" class="image-choices-feature-color-custom" type="text" />
            </li>
            <!-- Image Choices Align Settings -->
            <?php
            $form_setting_align = $this->get_form_settings_value( "gf_image_choices_align", "global_setting", $form, $form_settings );
            $global_setting_align = $this->get_plugin_settings_value( "gf_image_choices_global_align", $this->_defaultAlignment, $plugin_settings );
            $form_setting_align_label = ( $form_setting_align == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucwords($global_setting_align) ) : ucwords($form_setting_align);
            ?>
            <li class="image-choices-setting-align field_setting">
                <label for="image-choices-align" class="section_label"><?php echo esc_html__("Alignment"); ?></label>
                <select id="image-choices-align" class="image-choices-align" onchange="imageChoicesAdmin.updateAlignSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_align_label ); ?></option>
                    <?php echo $this->get_settings_select_options_html( $this->get_settings_align_choices() ); ?>
                </select>
            </li>
            <!-- Image Choices Style Settings -->
            <?php
            $form_setting_image_style = $this->get_form_settings_value( "gf_image_choices_image_style", "global_setting", $form, $form_settings );
            $global_setting_image_style = $this->get_plugin_settings_value( "gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings );
            $form_setting_image_style_label = ( $form_setting_image_style == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucfirst($global_setting_image_style) ) : ucfirst($form_setting_image_style);
            ?>
            <li class="image-choices-setting-image-style field_setting">
                <label for="image-choices-image-style" class="section_label"><?php echo esc_html__("Image Display Style"); ?></label>
                <select id="image-choices-image-style" class="image-choices-image-style" onchange="imageChoicesAdmin.updateImageStyleSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_image_style_label ); ?></option>
                    <?php echo $this->get_settings_select_options_html( $this->get_settings_image_style_choices() ); ?>
                </select>
            </li>
            <!-- Image Choices Columns Settings -->
            <?php
            $form_setting_columns = $this->get_form_settings_value( "gf_image_choices_columns", "global_setting", $form, $form_settings );
            $form_setting_columns_width = $this->get_form_settings_value( "gf_image_choices_columns_width", "", $form, $form_settings );
            $global_setting_columns = $this->get_plugin_settings_value( "gf_image_choices_global_columns", $this->_defaultColumns, $plugin_settings );
	        $global_setting_columns_width = $this->get_plugin_settings_value( "gf_image_choices_global_columns_width", "", $plugin_settings );
            if ( $global_setting_columns == "fixed" ) {
	            $global_setting_columns = ( !empty($global_setting_columns_width) ) ? "Fixed: {$global_setting_columns_width}px" : "Fixed: theme default";
            }
            $form_setting_columns_label = ( $form_setting_columns == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucfirst($global_setting_columns) ) : ucfirst($form_setting_columns);
            if ( $form_setting_columns == "fixed" ) {
	            $form_setting_columns_label = ( !empty($form_setting_columns_width) ) ? "Fixed: {$form_setting_columns_width}px" : "Fixed: theme default";
            }
            ?>
            <li class="image-choices-setting-columns field_setting">
                <label for="image-choices-columns" class="section_label"><?php echo esc_html__("Columns"); gform_tooltip('image_choices_columns'); ?></label>
                <select id="image-choices-columns" class="image-choices-columns" onchange="imageChoicesAdmin.updateColumnsSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_columns_label ); ?></option>
                    <?php echo $this->get_settings_select_options_html( $this->get_settings_columns_choices() ); ?>
                </select>
            </li>
            <!-- Column Width Setting -->
            <li class="image-choices-setting-columns-width field_setting">
                <label for="image-choices-columns-width" class="section_label"><?php echo esc_html__("Column Width"); ?></label>
                <input id="image-choices-columns-width" class="image-choices-columns-width" type="text" placeholder="<?php echo esc_html__('px value or leave blank for theme default', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateColumnsWidthSetting(this.value);" />
            </li>
            <!-- Image Choices Columns (Medium) Settings -->
            <?php
            $form_setting_columns_medium = $this->get_form_settings_value( "gf_image_choices_columns_medium", "global_setting", $form, $form_settings );
            $form_setting_columns_width_medium = $this->get_form_settings_value( "gf_image_choices_columns_width_medium", "global_setting", $form, $form_settings );
            $global_setting_columns_medium = $this->get_plugin_settings_value( "gf_image_choices_global_columns_medium", $this->_defaultColumns, $plugin_settings );
	        $global_setting_columns_medium_width = $this->get_plugin_settings_value( "gf_image_choices_global_columns_width_medium", "", $plugin_settings );
	        if ( $global_setting_columns_medium == "fixed" ) {
		        $global_setting_columns_medium = ( !empty($global_setting_columns_medium_width) ) ? "Fixed: {$global_setting_columns_medium_width}px" : "Fixed: theme default";
	        }
            $form_setting_columns_medium_label = ( $form_setting_columns_medium == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucfirst($global_setting_columns_medium) ) : ucfirst($form_setting_columns_medium);
	        if ( $form_setting_columns_medium == "fixed" ) {
		        $form_setting_columns_medium_label = ( !empty($form_setting_columns_width_medium) ) ? "Fixed: {$form_setting_columns_width_medium}px" : "Fixed: theme default";
	        }
            ?>
            <li class="image-choices-setting-columns-medium field_setting">
                <label for="image-choices-columns-medium" class="section_label"><?php echo esc_html__("Medium Columns (at medium screen sizes)"); ?></label>
                <select id="image-choices-columns-medium" class="image-choices-columns-medium" onchange="imageChoicesAdmin.updateColumnsMediumSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_columns_medium_label ); ?></option>
                    <?php echo $this->get_settings_select_options_html( $this->get_settings_columns_choices() ); ?>
                </select>
            </li>
            <!-- Column Medium Width Setting -->
            <li class="image-choices-setting-columns-width-medium field_setting">
                <label for="image-choices-columns-width-medium" class="section_label"><?php echo esc_html__("Medium Column Width"); ?></label>
                <input id="image-choices-columns-width-medium" class="image-choices-columns-width-medium" type="text" placeholder="<?php echo esc_html__('px value or leave blank for theme default', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateColumnsMediumWidthSetting(this.value);" />
            </li>
            <!-- Image Choices Columns (Small) Settings -->
            <?php
            $form_setting_columns_small = $this->get_form_settings_value( "gf_image_choices_columns_small", "global_setting", $form, $form_settings );
            $form_setting_columns_width_small = $this->get_form_settings_value( "gf_image_choices_columns_width_small", "global_setting", $form, $form_settings );
            $global_setting_columns_small = $this->get_plugin_settings_value( "gf_image_choices_global_columns_small", $this->_defaultColumns, $plugin_settings );
	        $global_setting_columns_small_width = $this->get_plugin_settings_value( "gf_image_choices_global_columns_width_small", "", $plugin_settings );
	        if ( $global_setting_columns_small == "fixed" ) {
		        $global_setting_columns_small = ( !empty($global_setting_columns_small_width) ) ? "Fixed: {$global_setting_columns_small_width}px" : "Fixed: theme default";
	        }
            $form_setting_columns_small_label = ( $form_setting_columns_small == "global_setting" ) ? sprintf( esc_html__("Global: %s", 'gf_image_choices'), ucfirst($global_setting_columns_small) ) : ucfirst($form_setting_columns_small);
	        if ( $form_setting_columns_small == "fixed" ) {
		        $form_setting_columns_small_label = ( !empty($form_setting_columns_width_small) ) ? "Fixed: {$form_setting_columns_width_small}px" : "Fixed: theme default";
	        }
            ?>
            <li class="image-choices-setting-columns-small field_setting">
                <label for="image-choices-columns-small" class="section_label"><?php echo esc_html__("Small Columns (at small screen sizes)"); ?></label>
                <select id="image-choices-columns-small" class="image-choices-columns-small" onchange="imageChoicesAdmin.updateColumnsSmallSetting(this.value);">
                    <option value="form_setting"><?php echo sprintf( esc_html__("Use Form Setting (%s)", 'gf_image_choices'), $form_setting_columns_small_label ); ?></option>
                    <?php echo $this->get_settings_select_options_html( $this->get_settings_columns_choices() ); ?>
                </select>
            </li>
            <!-- Column Small Width Setting -->
            <li class="image-choices-setting-columns-width-small field_setting">
                <label for="image-choices-columns-width-small" class="section_label"><?php echo esc_html__("Small Column Width"); ?></label>
                <input id="image-choices-columns-width-small" class="image-choices-columns-width-small" type="text" placeholder="<?php echo esc_html__('px value or leave blank for theme default', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateColumnsSmallWidthSetting(this.value);" />
            </li>
            <!-- Item Height Setting -->
            <li class="image-choices-setting-height field_setting">
                <label for="image-choices-height" class="section_label"><?php echo esc_html__("Display Height"); ?></label>
                <input id="image-choices-height" class="image-choices-height" type="text" placeholder="<?php echo esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateHeightSetting(this.value);" />
            </li>
            <!-- Item Medium Height Setting -->
            <li class="image-choices-setting-height-medium field_setting">
                <label for="image-choices-height-medium" class="section_label"><?php echo esc_html__("Medium Display Height (at medium screen sizes)"); ?></label>
                <input id="image-choices-height-medium" class="image-choices-height-medium" type="text" placeholder="<?php echo esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateMediumHeightSetting(this.value);" />
            </li>
            <!-- Item Small Height Setting -->
            <li class="image-choices-setting-height-small field_setting">
                <label for="image-choices-height-small" class="section_label"><?php echo esc_html__("Small Display Height (at small screen sizes)"); ?></label>
                <input id="image-choices-height-small" class="image-choices-height-small" type="text" placeholder="<?php echo esc_html__('px value or leave blank for default / fallback', 'gf_image_choices'); ?>" onkeyup="imageChoicesAdmin.updateSmallHeightSetting(this.value);" />
            </li>
        <?php endif; ?>
        <!-- Image Choices Label Display Settings -->
        <li class="image-choices-setting-show-labels field_setting">
            <input id="image_choices_show_labels" class="image_choices_show_labels" type="checkbox" onclick="imageChoicesAdmin.toggleShowLabels(this.checked);" onkeypress="imageChoicesAdmin.toggleShowLabels(this.checked);"> <label for="image_choices_show_labels"><?php echo esc_html__("Show labels", 'gf_image_choices'); gform_tooltip('image_choices_show_labels'); ?></label>
        </li>
        <!-- Image Choices Price Display Setting -->
        <li class="image-choices-setting-show-prices field_setting">
            <input id="image_choices_show_prices" class="image_choices_show_prices" type="checkbox" onclick="imageChoicesAdmin.toggleShowPrices(this.checked);" onkeypress="imageChoicesAdmin.toggleShowPrices(this.checked);"> <label for="image_choices_show_prices"><?php echo esc_html__("Show prices", 'gf_image_choices'); gform_tooltip('image_choices_show_prices'); ?></label>
        </li>
        <!-- Image Choices Lightbox Setting -->
        <li class="image-choices-setting-use-lightbox field_setting">
            <input id="image_choices_use_lightbox" class="image_choices_use_lightbox" type="checkbox" onclick="imageChoicesAdmin.toggleUseLightbox(this.checked);" onkeypress="imageChoicesAdmin.toggleUseLightbox(this.checked);"> <label for="image_choices_use_lightbox"><?php echo esc_html__("Use lightbox", 'gf_image_choices'); gform_tooltip('image_choices_use_lightbox'); ?></label>
        </li>
        <!-- Image Choices Lightbox Caption Setting -->
        <li class="image-choices-setting-use-lightbox-caption field_setting">
            <input id="image_choices_use_lightbox_caption" class="image_choices_use_lightbox_caption" type="checkbox" onclick="imageChoicesAdmin.toggleUseLightboxCaption(this.checked);" onkeypress="imageChoicesAdmin.toggleUseLightboxCaption(this.checked);"> <label for="image_choices_use_lightbox_caption"><?php echo esc_html__("Show choice text as lightbox captions", 'gf_image_choices'); gform_tooltip('image_choices_use_lightbox_captions'); ?></label>
        </li>
        <!-- Image Choices Entry / Notification Display Settings -->
        <li class="image-choices-setting-entry-value field_setting">
            <label for="image-choices-entry-value" class="section_label"><?php echo esc_html__("Entry / Notification Display"); ?></label>
            <select id="image-choices-entry-value" class="image-choices-entry-value" onchange="imageChoicesAdmin.updateEntrySetting(this.value);">
                <option value="form_setting"><?php esc_html_e("Use Form Setting", 'gf_image_choices'); ?></option>
	            <?php echo $this->get_settings_select_options_html( $this->get_settings_entry_value_choices() ); ?>
            </select>
        </li>
		<?php
	}

    public function add_product_image_markup( $content, $field, $value, $lead_id, $form_id ) {

	    if ( !$this->field_has_product_image_enabled($field) || !$this->use_new_features() ) {
		    return $content;
	    }

        if ( GFCommon::is_form_editor() ) {
            return $content;
        }

	    $image_url = $this->get_field_settings_value("image", "", $field, true);
	    $image_id = $this->get_field_settings_value("imageId", "", $field, true);
	    $large_img = $this->get_field_settings_value("largeImage", "", $field, true);

        if ( empty($image_url) ) {
            return $content;
        }

	    $lightbox_attr = "";
	    $lightbox_button = "";

	    $form = GFAPI::get_form( $field->formId );

	    if ( $this->get_field_settings_value("useLightbox", false, $field, true) ) {

		    if ( !empty($large_img) ) {
			    $lightbox_img = $large_img;
		    }
		    else {
			    $form_settings = $this->get_form_settings($form);
			    $plugin_settings = $this->get_plugin_settings();

			    $global_lightbox_size = $this->get_plugin_settings_value('gf_image_choices_global_lightbox_size', $this->_defaultLightboxImageSize, $plugin_settings);
			    $form_lightbox_size = $this->get_form_settings_value('gf_image_choices_lightbox_size', 'global_setting', $form, $form_settings);
			    $lightbox_size = $this->get_field_setting_fallback_value( null, $form_lightbox_size, $global_lightbox_size );

			    $lightbox_img = ( !empty($image_id) ) ? wp_get_attachment_image_src($image_id, $lightbox_size) : '';
			    if ( !empty($lightbox_img) ) {
				    $lightbox_img = $lightbox_img[0];
			    }
		    }
		    // make lightcase safe
		    $lightbox_img = str_replace('$', '\$', $lightbox_img);
		    $lightbox_attr = "data-lightbox-src='" . $lightbox_img . "'";

		    $lightbox_button = "<a href='{$lightbox_img}' class='ic-product-image-lightbox-btn'><i></i></a>";

	    }

	    $lazy_load = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0);
        if ( !empty($lazy_load) ) {
	        $image_html = "<div class='ic-product-image jetsloth-lazy' data-lazy-bg='{$image_url}'><img src='' data-lazy-src='{$image_url}' alt='' class='ic-product-image-element jetsloth-lazy' {$lightbox_attr} /></div>";
        }
        else {
            $image_html = "<div class='ic-product-image' style='background-image:url({$image_url});'><img src='{$image_url}' alt='' class='ic-product-image-element' {$lightbox_attr} /></div>";
        }

	    $image_wrap = "<div class='ic-product-image-wrap'>{$lightbox_button}{$image_html}";
        $price = GFCommon::format_number( $field->basePrice, 'currency' );
        $price_text = "<div class='ic-product-image-price'>{$price}</div></div>";// has closing div for .ic-product-image-wrap

	    $description_setting = 'below';
        if ( !empty( $field->description ) ) {
	        $form_description_setting = rgempty( 'descriptionPlacement', $form ) ? 'below' : $form['descriptionPlacement'];
	        $description_setting = ( !isset( $field->descriptionPlacement ) || empty( $field->descriptionPlacement ) ) ? $form_description_setting : $field->descriptionPlacement;
	        $description_setting = $description_setting == 'above' && ( $field->labelPlacement == 'top_label' || $field->labelPlacement == 'hidden_label' || ( empty( $field->labelPlacement ) && $form[ 'labelPlacement' ] == 'top_label' ) ) ? 'above' : 'below';
        }

	    $content = str_replace("<label class='gfield_label", "{$image_wrap}<label class='gfield_label", $content);

        $element = ( $description_setting == 'above' ) ? "div" : "label";

	    return str_replace("</{$element}><div class='ginput_container", "</{$element}>{$price_text}<div class='ginput_container", $content);

    }

	public function add_image_options_markup( $choice_markup, $choice, $field, $value ) {

		if ( !$this->field_has_image_choices_enabled($field) ) {
			return $choice_markup;
		}

		$is_other_choice = ( $choice['value'] == "gf_other_choice" );
		$is_select_all = ( ($field->type == 'checkbox' || $field->optionType == 'checkbox') && empty($choice) );

		if ( $is_select_all ) {
			// if this condition is met, it's the checkbox field 'Select all' option
			return $choice_markup;
		}
		else if ( $is_other_choice ) {
			// if this condition is met, it's the radio field 'Other' option
			$other_img_global = apply_filters("gfic_other_choice_image", "", $choice, $field, $value);
			$other_imgID_global = apply_filters("gfic_other_choice_imageID", 0, $choice, $field, $value);
			$other_img_form = apply_filters("gfic_other_choice_image_{$field->formId}", $other_img_global, $choice, $field, $value);
			$other_imgID_form = apply_filters("gfic_other_choice_imageID_{$field->formId}", $other_imgID_global, $choice, $field, $value);
			$img = apply_filters("gfic_other_choice_image_{$field->formId}_{$field->id}", $other_img_form, $choice, $field, $value);
			$imgID = apply_filters("gfic_other_choice_imageID_{$field->formId}_{$field->id}", $other_imgID_form, $choice, $field, $value);

			$large_img = apply_filters("gfic_other_choice_large_image_{$field->formId}_{$field->id}", "", $choice, $field, $value);
		}
		else {
			$img = (isset($choice['imageChoices_image'])) ? $choice['imageChoices_image'] : '';
			$imgID = (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : '';

            $large_img = (isset($choice['imageChoices_largeImage'])) ? $choice['imageChoices_largeImage'] : '';
		}

		if ( empty($img) ) {
			$img = '';
		}
		else {
			$img = str_replace('$', '\$', $img);
		}

		$form = GFAPI::get_form( $field->formId );
		$form_settings = $this->get_form_settings($form);
		$plugin_settings = $this->get_plugin_settings();

		$global_lightbox_size = $this->get_plugin_settings_value('gf_image_choices_global_lightbox_size', $this->_defaultLightboxImageSize, $plugin_settings);
		$form_lightbox_size = $this->get_form_settings_value('gf_image_choices_lightbox_size', 'global_setting', $form, $form_settings);
		$lightbox_size = $this->get_field_setting_fallback_value( null, $form_lightbox_size, $global_lightbox_size );

		$global_lazy_load_value = $this->get_plugin_settings_value('gf_image_choices_lazy_load_global', 0, $plugin_settings);
		$form_lazy_load_value = $this->get_form_settings_value('gf_image_choices_lazy_load', 'global_setting', $form, $form_settings);
		$lazy_load = $this->get_field_setting_fallback_value( null, $form_lazy_load_value, $global_lazy_load_value );

		$global_theme_setting = $this->get_plugin_settings_value("gf_image_choices_global_theme", $this->_defaultTheme, $plugin_settings);
		$form_theme_setting = $this->get_form_settings_value("gf_image_choices_theme", "global_setting", $form, $form_settings);
		$field_theme_setting = $this->get_field_settings_value("theme", "form_setting", $field);
		$field_theme = $this->get_field_setting_fallback_value( $field_theme_setting, $form_theme_setting, $global_theme_setting );

		$global_image_style_setting = $this->get_plugin_settings_value("gf_image_choices_global_image_style", $this->_defaultImageDisplay, $plugin_settings);
		$form_image_style_setting = $this->get_form_settings_value("gf_image_choices_image_style", "global_setting", $form, $form_settings);
		$field_image_style_setting = $this->get_field_settings_value("imageStyle", "form_setting", $field);
		$field_image_style = $this->get_field_setting_fallback_value( $field_image_style_setting, $form_image_style_setting, $global_image_style_setting );

		$img_alt = ( !empty($imgID) ) ? get_post_meta( $imgID, '_wp_attachment_image_alt', true ) : '';

		// new option to define large image url
		if ( !empty($large_img) ) {
			$lightboxImg = $large_img;
		}
        else {
	        $lightboxImg = ( !empty($imgID) ) ? wp_get_attachment_image_src($imgID, $lightbox_size) : '';
	        if ( !empty($lightboxImg) ) {
		        $lightboxImg = $lightboxImg[0];
	        }
        }

        // make lightcase safe
		$lightboxImg = str_replace('$', '\$', $lightboxImg);

		$jmh_id = $field['formId'] . "_" . $field['id'];

		if ( empty($img) ) {
			$img_markup = implode("", array(
				'<span class="image-choices-choice-image-wrap" style="background-image:none;"></span>',
			));
		}
		else if ( !empty($lazy_load ) && !is_admin() ) {
			$img_element_markup = '<img src="" data-lazy-src="' . $img . '" alt="' . $img_alt . '" class="image-choices-choice-image jetsloth-lazy" data-lightbox-src="' . $lightboxImg . '" />';
			$img_markup = implode("", array(
				'<span class="image-choices-choice-image-wrap jetsloth-lazy" data-lazy-bg="' . $img . '">',
				apply_filters('gfic_choice_image_html', $img_element_markup, $choice, $field, $value),
				'</span>',
			));
		}
		else {
			$img_element_markup = '<img src="'.$img.'" alt="' . $img_alt . '" class="image-choices-choice-image" data-lightbox-src="'.$lightboxImg.'" />';
			$img_markup = implode("", array(
				'<span class="image-choices-choice-image-wrap" style="background-image:url('.$img.')">',
				apply_filters('gfic_choice_image_html', $img_element_markup, $choice, $field, $value),
				'</span>',
			));
		}


		if ($field->type == 'product' && $this->get_field_settings_value("showPrices", false, $field) ) {
			$choice_price = str_replace('$', '\$', $choice['price']);

            if ( !GFCommon::is_form_editor() && ( $field_theme == "polaroid" || $field_theme == "float-card" ) ) {
	            $choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
		            '<label ${1} >',
		            $img_markup,
		            '<span class="image-choices-choice-price"><span class="ginput_price"> '.$choice_price.'</span></span>',
		            '<span class="image-choices-choice-text">${2}</span>',
		            '</label>',
	            )), $choice_markup);
            }
			else {
				$choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
					'<label ${1} >',
					$img_markup,
					'<span class="image-choices-choice-text">${2}</span>',
					'<span class="image-choices-choice-price"><span class="ginput_price"> '.$choice_price.'</span></span>',
					'</label>',
				)), $choice_markup);
			}

		}
		else if ($field->type != 'option' || GFCommon::is_form_editor()) {

			$choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
				'<label ${1} >',
				$img_markup,
				'<span class="image-choices-choice-text">${2}</span>',
				'</label>',
			)), $choice_markup);

		}
		else {
			// OPTION FIELD
			$choice_markup = str_replace('<label', '<label data-img="'.$img.'" data-lightbox-src="'.$lightboxImg.'" data-text="'.esc_attr($choice['text']).'"', $choice_markup);
		}

		$re = '/class=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
		$choice_classes = "";
		if ( preg_match($re, $choice_markup, $match) ) {
			$choice_classes = explode(" ", $match[2]);
			$choice_classes[] = "image-choices-choice";
			if ( isset($choice['isSelected']) && !empty($choice['isSelected']) ) {
				$choice_classes[] = "image-choices-choice-selected";
			}
			$choice_classes[] = "gform-theme__no-reset--children";
			$choice_markup = str_replace( "class='{$match[2]}'", "class='" . implode(" ", $choice_classes) . "'", $choice_markup );
		}

        if ( $this->use_new_features() ) {

	        if ( $field_theme == "polaroid" || $field_theme == "float-card" ) {
		        $choice_markup = str_replace( "<label ", "<label data-jmh='{$jmh_id}' ", $choice_markup );
	        }
	        if ( $field_theme == "cover-tile" && $field_image_style == "natural" ) {
		        $choice_id = $jmh_id;
		        foreach( $choice_classes as $cls ) {
			        if ( strpos($cls, "gchoice_") !== FALSE ) {
				        $choice_id = str_replace("gchoice_", "", $cls);
				        break;
			        }
		        }
		        $choice_markup = str_replace( "<label ", "<label data-jmh='{$choice_id}' ", $choice_markup );
		        $choice_markup = str_replace( 'class="image-choices-choice-image-wrap"', 'class="image-choices-choice-image-wrap" data-jmh="'.$choice_id.'"', $choice_markup );
	        }

        }

		return apply_filters( 'gfic_choice_html', $choice_markup, $choice, $field, $value );

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


    public function upgrade( $previous_version ) {
        if ( empty($previous_version) ) {
            return;
        }

        $version_num = floatval( substr($previous_version, 0, 3) );

        if ( $version_num < 1.4 ) {
	        // upgraded from < 1.4

	        // switch to use legacy styles by default and show message

	        $plugin_settings = $this->get_plugin_settings();
	        $plugin_settings["gf_image_choices_use_legacy_styles"] = "1";
            $this->update_plugin_settings($plugin_settings);

	        // this isn't needed anymore
            if ( false !== get_option('gfic_legacy_styles') ) {
	            delete_option('gfic_legacy_styles');
            }
        }

	    if ( ( empty(GFIC_SPLASH_ID) || empty(GFIC_SPLASH_URL) || false !== get_option(GFIC_SPLASH_ID . "_seen") ) ) {
		    // splash already shown
		    return;
	    }

	    // set transient to show the splash page
	    set_transient(GFIC_SPLASH_ID, '1', MONTH_IN_SECONDS);// expire in 30 days

    }


	public function maybe_show_splash_page() {

		if ( false === get_transient( GFIC_SPLASH_ID ) ) {
			return;
		}

		delete_transient( GFIC_SPLASH_ID );
		// set the option so this splash doesn't get shown on future updates
		update_option( GFIC_SPLASH_ID . "_seen", '1' );
		?>
        <style>
			.jetbase-splash-page-overlay {
				position: fixed;
				left: 0;
				top: 0;
				right: 0;
				bottom: 0;
				background-color: rgba(36, 39, 70, 0.75);
				padding: 30px;
				z-index: 999999;
			}
			.jetbase-splash-page-overlay:not(.active) {
				display: none;
			}
			.jetbase-splash-page-wrap {
				position: relative;
				width: calc(100vw - 60px);
				height: calc(100vh - 60px);
				background-color: white;
				border-radius: 10px;
				overflow: scroll;
			}
			.jetbase-splash-page-close {
				position: absolute;
				right: 40px;
				top: 40px;
				display: block;
				padding: 0;
				margin: 0;
				background: none;
				border: none;
				cursor: pointer;
				z-index: 999998;
			}
			.jetbase-splash-page-close i {
				display: block;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				overflow: hidden;
				position: relative;
			}
			.jetbase-splash-page-close i:before,
			.jetbase-splash-page-close i:after {
				content: "";
				overflow: hidden;
				display: block;
				width: 50%;
				height: 2px;
				border-radius: 2px;
				background-color: #526982;
				position: absolute;
				left: 50%;
				top: 50%;
			}
			.jetbase-splash-page-close i:before {
				transform: translate(-50%, -50%) rotate(-45deg);
			}
			.jetbase-splash-page-close i:after {
				transform: translate(-50%, -50%) rotate(45deg);
			}
			.jetbase-splash-page-close span {
				border: 0 !important;
				clip: rect(0 0 0 0) !important;
				width: 1px !important;
				height: 1px !important;
				margin: -1px !important;
				overflow: hidden !important;
				padding: 0 !important;
				position: absolute !important;
			}
			.jetbase-splash-page {
				width: 100%;
				height: 100%;
			}
        </style>
        <div id="<?php echo GFIC_SPLASH_ID; ?>" class="jetbase-splash-page-overlay">
            <button type="button" class="jetbase-splash-page-close"><i></i><span>Close</span></button>
            <div class="jetbase-splash-page-wrap">
                <iframe class="jetbase-splash-page" src="<?php echo GFIC_SPLASH_URL; ?>"></iframe>
            </div>
        </div>
        <script>
			(() => {
				const __initSplash = () => {
					const overlay = document.querySelector('.jetbase-splash-page-overlay[id^="gfic_"]');
					if ( !overlay ) {
						return;
					}
					overlay.querySelector('.jetbase-splash-page-close').addEventListener('click', (e) => {
						e.preventDefault();
						const overlay = e.currentTarget.closest('.jetbase-splash-page-overlay');
						overlay.remove();
					});
					overlay.classList.add('active');
				};
				if ( document && document.readyState === "complete" ) {
					__initSplash();
				}
				else {
					document.addEventListener('DOMContentLoaded', (e) => {
						__initSplash();
					});
				}
			})();
        </script>
		<?php
	}


	/**
	 * Add custom messages after plugin row based on license status
	 */

	public function gf_plugin_row($plugin_file='', $plugin_data=array(), $status='') {
		$row = "";
		$license_key = $this->get_license_key();
		$license_status = get_option('gf_image_choices_license_status', '');
		if ( empty($license_key) || empty($license_status) ) {
			ob_start();
			?>
            <tr class="plugin-update-tr">
                <td colspan="3" class="plugin-update gf_image_choices-plugin-update">
                    <div class="update-message">
                        <a href="<?php echo admin_url('admin.php?page=gf_settings&subview=' . $this->_slug); ?>">Activate</a> your license to receive plugin updates and support. Need a license key? <a href="<?php echo $this->_url; ?>" target="_blank">Purchase one now</a>.
                    </div>
                    <style>
						.plugin-update.gf_image_choices-plugin-update .update-message:before {
							content: "\f348";
							margin-top: 0;
							font-family: dashicons;
							font-size: 20px;
							position: relative;
							top: 5px;
							color: orange;
							margin-right: 8px;
						}
						.plugin-update.gf_image_choices-plugin-update {
							background-color: #fff6e5;
						}
						.plugin-update.gf_image_choices-plugin-update .update-message {
							margin: 0 20px 6px 40px !important;
							line-height: 28px;
						}
                    </style>
                </td>
            </tr>
			<?php
			$row = ob_get_clean();
		}
		else if( !empty($license_key) && $license_status != 'valid' ) {
			ob_start();
			?>
            <tr class="plugin-update-tr">
                <td colspan="3" class="plugin-update gf_image_choices-plugin-update">
                    <div class="update-message">
                        Your license is invalid or expired. <a href="<?php echo admin_url('admin.php?page=gf_settings&subview=' . $this->_slug); ?>">Enter a valid license key</a> or <a href="<?php echo $this->_url; ?>" target="_blank">purchase a new one</a>.
                    </div>
                    <style>
						.plugin-update.gf_image_choices-plugin-update .update-message:before {
							content: "\f348";
							margin-top: 0;
							font-family: dashicons;
							font-size: 20px;
							position: relative;
							top: 5px;
							color: #d54e21;
							margin-right: 8px;
						}
						.plugin-update.gf_image_choices-plugin-update {
							background-color: #fff6e5;
						}
						.plugin-update.gf_image_choices-update .update-message {
							margin: 0 20px 6px 40px !important;
							line-height: 28px;
						}
                    </style>
                </td>
            </tr>
			<?php
			$row = ob_get_clean();
		}

		echo $row;
	}



	/**
	 * Determine if the license key is valid so the appropriate icon can be displayed next to the field.
	 *
	 * @param string $value The current value of the license_key field.
	 * @param array $field The field properties.
	 *
	 * @return bool|null
	 */
	public function license_feedback( $value, $field = null ) {
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
		//$old_license = $this->get_plugin_setting( 'gf_image_choices_license_key' );
		$old_license = $this->get_license_key();

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
