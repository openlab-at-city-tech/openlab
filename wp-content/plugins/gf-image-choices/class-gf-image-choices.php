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

	/**
	 * Handles hooks and loading of language files.
	 */
	public function init() {

		// add a special class to relevant fields so we can identify them later
		add_action( 'gform_field_css_class', array( $this, 'add_custom_class' ), 10, 3 );
		add_filter( 'gform_field_choice_markup_pre_render', array( $this, 'add_image_options_markup' ), 10, 4 );

		// display on entry detail
		add_filter( 'gform_entry_field_value', array( $this, 'custom_entry_field_value' ), 20, 4 );
		add_filter( 'gform_order_summary', array( $this, 'custom_order_summary_entry_field_value' ), 10, 4 );

		// display in notifications
		add_filter( 'gform_merge_tag_filter', array( $this, 'custom_notification_merge_tag' ), 11, 5 );
		//add_filter( 'gform_replace_merge_tags', array( $this, 'custom_replace_merge_tags' ), 10, 7 );
		add_filter( 'gform_replace_merge_tags', array( $this, 'render_quiz_results_merge_tag' ), 100, 7 );

		add_filter('gform_register_init_scripts', array( $this, 'load_custom_css' ), 10, 4);

		parent::init();

	}

	/**
	 * Initialize the admin specific hooks.
	 */
	public function init_admin() {

		// form editor
		add_action( 'gform_field_standard_settings', array( $this, 'image_choice_field_settings' ), 10, 2 );
		add_filter( 'gform_tooltips', array( $this, 'add_image_choice_field_tooltips' ) );

		// display results on entry list
		add_filter( 'gform_entries_field_value', array( $this, 'entries_table_field_value' ), 10, 4 );

		$name = plugin_basename($this->_path);
		add_action( 'after_plugin_row_'.$name, array( $this, 'gf_plugin_row' ), 10, 2 );

		parent::init_admin();

	}


	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$gf_image_choices_js_deps = array( 'jquery', 'jquery-ui-sortable', 'jetsloth_lightbox' );
		if ( wp_is_mobile() ) {
			$gf_image_choices_js_deps[] = 'jquery-touch-punch';
		}

		$scripts = array(
				array(
						'handle'   => 'gf_image_choices_form_editor_js',
						'src'      => $this->get_base_url() . '/js/gf_image_choices_form_editor.js',
						'version'  => $this->_version,
						'deps'     => array( 'jquery' ),
						'callback' => array( $this, 'localize_scripts' ),
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
						'handle'  => 'gf_image_choices_js',
						'src'     => $this->get_base_url() . '/js/gf_image_choices.js',
						'version' => $this->_version,
						'deps'    => $gf_image_choices_js_deps,
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

		$styles = array(
				array(
						'handle'  => 'gf_image_choices_form_editor_css',
						'src'     => $this->get_base_url() . '/css/gf_image_choices_form_editor.css',
						'version' => $this->_version,
						'enqueue' => array(
							array('admin_page' => array( 'form_editor', 'plugin_settings', 'form_settings', 'entry_view', 'entry_detail' )),
							array('query' => 'page=gf_entries&view=entry&id=_notempty_')
						),
				),
				array(
						'handle'  => 'gf_image_choices_css',
						'src'     => $this->get_base_url() . '/css/gf_image_choices.css',
						'version' => $this->_version,
						'media'   => 'screen',
						'enqueue' => array(
							array('admin_page' => array( 'form_editor', 'entry_view', 'entry_detail' )),
							array('field_types' => array( 'radio', 'checkbox', 'survey', 'poll', 'quiz', 'post_custom_field', 'product', 'option' )),
							array('query' => 'page=gf_entries&view=entry&id=_notempty_')
						),
				),
		);

		return array_merge( parent::styles(), $styles );
	}

	function load_custom_css($form) {

		require_once( dirname( __FILE__ ) . '/inc/php-html-css-js-minifier.php' );
		$minifier = PHP_HTML_CSS_JS_Minifier::get_instance();

		$form_settings = $this->get_form_settings($form);
		$form_css_value = (isset($form_settings['gf_image_choices_custom_css'])) ? $form_settings['gf_image_choices_custom_css'] : '';
		if (!empty($form_css_value)) {
			$form_css_value_min = $minifier->minify_css($form_css_value);
			$form_css_script = '(function(){ if (typeof window.gf_image_choices_custom_css_'.$form['id'].' === "undefined") window.gf_image_choices_custom_css_'.$form['id'].' = "'.addslashes($form_css_value_min).'"; })();';
			GFFormDisplay::add_init_script($form['id'], 'gf_image_choices_custom_css_script_'.$form['id'], GFFormDisplay::ON_PAGE_RENDER, $form_css_script);
		}

		$ignore_global_css_value = (isset($form_settings['gf_image_choices_ignore_global_css'])) ? $form_settings['gf_image_choices_ignore_global_css'] : 0;
		$ignore_global_css_value_script = '(function(){ if (typeof window.gf_image_choices_ignore_global_css_'.$form['id'].' === "undefined") window.gf_image_choices_ignore_global_css_'.$form['id'].' = '.$ignore_global_css_value.'; })();';
		GFFormDisplay::add_init_script($form['id'], 'gf_image_choices_ignore_global_css_'.$form['id'], GFFormDisplay::ON_PAGE_RENDER, $ignore_global_css_value_script);

		if (empty($ignore_global_css_value)) {
			$global_css_value = $this->get_plugin_setting('gf_image_choices_custom_css_global');
			if (!empty($global_css_value)) {
				$global_css_value_min = $minifier->minify_css($global_css_value);
				$global_css_script = '(function(){ if (typeof window.gf_image_choices_custom_css_global === "undefined") window.gf_image_choices_custom_css_global = "'.addslashes($global_css_value_min).'"; })();';
				GFFormDisplay::add_init_script($form['id'], 'gf_image_choices_custom_css_global_script', GFFormDisplay::ON_PAGE_RENDER, $global_css_script);
			}
		}

		return $form;
	}


	/**
	 * Localize the strings used by the scripts.
	 */
	public function localize_scripts() {

		// Get current page protocol
		$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		// Output admin-ajax.php URL with same protocol as current page
		$params = array(
				'ajaxurl'   => admin_url( 'admin-ajax.php', $protocol ),
				//'imagesUrl' => $this->get_base_url() . '/images',
		);
		wp_localize_script( 'gf_image_choices_form_editor_js', 'imageChoicesFieldVars', $params );

		//localize strings for the js file
		$strings = array(
				'useImages'    => esc_html__( 'use images', GFIC_TEXT_DOMAIN ),
				'confirmImagesToggle'    => esc_html__( 'Color picker choices are enabled on this field. Are you sure you want to remove the colors and use images instead?', GFIC_TEXT_DOMAIN ),
				'uploadImage'    => esc_html__( 'Upload image', GFIC_TEXT_DOMAIN ),
				'removeImage'    => esc_html__( 'Remove this image', GFIC_TEXT_DOMAIN ),
				'removeAllChoices'    => esc_html__( 'Remove All Choices', GFIC_TEXT_DOMAIN ),
				'entrySettingTitle'    => esc_html__( 'Image Choices Entry / Notification Display', GFIC_TEXT_DOMAIN ),
				'entrySettingForm'    => esc_html__( 'Use Form Setting', GFIC_TEXT_DOMAIN ),
				'entrySettingValue'    => esc_html__( 'Value', GFIC_TEXT_DOMAIN ),
				'entrySettingImage'    => esc_html__( 'Image', GFIC_TEXT_DOMAIN ),
				'entrySettingText'    => esc_html__( 'Label', GFIC_TEXT_DOMAIN ),
				'entrySettingImageText'    => esc_html__( 'Image and Label', GFIC_TEXT_DOMAIN ),
				'entrySettingImageValue'    => esc_html__( 'Image and Value', GFIC_TEXT_DOMAIN ),
				'showLabels'    => esc_html__( 'Show labels', GFIC_TEXT_DOMAIN ),
				'displaySettingsTitle'    => esc_html__( 'Image Choices Display', GFIC_TEXT_DOMAIN ),
				'showPrices'    => esc_html__( 'Show prices', GFIC_TEXT_DOMAIN ),
				'priceSettingsTitle'    => esc_html__( 'Image Choices Price Display', GFIC_TEXT_DOMAIN ),
				'showPricesDescription'    => esc_html__( 'With this setting, the product price will be displayed below the image', GFIC_TEXT_DOMAIN ),
				'lightboxSettingsTitle'    => esc_html__( 'Image Choices Lightbox', GFIC_TEXT_DOMAIN ),
				'useLightbox'    => esc_html__( 'Use lightbox', GFIC_TEXT_DOMAIN ),
				'useLightboxDescription'    => esc_html__( 'With this setting, the user will be able to preview large versions of each image in a lightbox', GFIC_TEXT_DOMAIN ),
				'useLightboxWarning'    => esc_html__( 'It looks like you created your choices for this field prior to our release of the lightbox functionality. In order for lightbox to work with this field, you will need to remove and re-add the images for these choices again.', GFIC_TEXT_DOMAIN ),
				'imageSizeSettingTitle'    => esc_html__( 'Image Size', GFIC_TEXT_DOMAIN ),
				'imageSizeSettingForm'    => esc_html__( 'Use Form Setting', GFIC_TEXT_DOMAIN ),
				'imageSizeSettingThumbnail'    => esc_html__( 'Thumbnail', GFIC_TEXT_DOMAIN ),
				'imageSizeSettingMedium'    => esc_html__( 'Medium', GFIC_TEXT_DOMAIN ),
				'imageSizeSettingLarge'    => esc_html__( 'Large', GFIC_TEXT_DOMAIN ),
				'imageSizeSettingOriginal'    => esc_html__( 'Original (Full)', GFIC_TEXT_DOMAIN ),
				'showLabelsDescription'    => esc_html__( 'With this setting, the choices labels will be displayed along with the image', GFIC_TEXT_DOMAIN ),
				'selectLayout'    => esc_html__( 'Choices layout', GFIC_TEXT_DOMAIN ),
				'layoutHorizontal'    => esc_html__( 'Horizontal (inline)', GFIC_TEXT_DOMAIN ),
				'layoutVertical'    => esc_html__( 'Vertical (stacked)', GFIC_TEXT_DOMAIN ),
		);
		wp_localize_script( 'gf_image_choices_form_editor_js', 'imageChoicesFieldStrings', $strings );

	}


	/**
	 * Creates a settings page for this add-on.
	 */
	public function plugin_settings_fields() {

		$license = $this->get_plugin_setting('gf_image_choices_license_key');
		$status = get_option('gf_image_choices_license_status');

		$license_field = array(
			'name' => 'gf_image_choices_license_key',
			'tooltip' => esc_html__('Enter the license key you received after purchasing the plugin.', GFIC_TEXT_DOMAIN),
			'label' => esc_html__('License Key', GFIC_TEXT_DOMAIN),
			'type' => 'text',
			'input_type' => 'password',
			'class' => 'medium',
			'default_value' => '',
			'validation_callback' => array($this, 'license_validation'),
			'feedback_callback' => array($this, 'license_feedback'),
			'error_message' => esc_html__( 'Invalid license', GFIC_TEXT_DOMAIN ),
		);

		if (!empty($license) && !empty($status)) {
			$license_field['after_input'] = ($status == 'valid') ? ' License is valid' : ' Invalid or expired license';
		}

		$custom_css_global_value = $this->get_plugin_setting('gf_image_choices_custom_css_global');
		$custom_css_global_field = array(
			'name' => 'gf_image_choices_custom_css_global',
			'tooltip' => esc_html__('These styles will be loaded for all forms.<br/>Find examples at <a href="https://jetsloth.com/support/gravity-forms-image-choices/">https://jetsloth.com/support/gravity-forms-image-choices/</a>', GFIC_TEXT_DOMAIN),
			'label' => esc_html__('Custom CSS', GFIC_TEXT_DOMAIN),
			'type' => 'textarea',
			'class' => 'large',
			'default_value' => $custom_css_global_value
		);

		$fields = array(
			array(
				'title'  => esc_html__('To unlock plugin updates, please enter your license key below', GFIC_TEXT_DOMAIN),
				'fields' => array(
					$license_field
				)
			),
			array(
				'title'  => esc_html__('Enter your own css to style image choices', GFIC_TEXT_DOMAIN),
				'fields' => array(
					$custom_css_global_field
				)
			)
		);

		return $fields;
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
			//'tooltip' => esc_html__('The selected collapsible section will be opened by default when the form loads.', GFIC_TEXT_DOMAIN),
			'label' => esc_html__('Default Entry / Notification Display', GFIC_TEXT_DOMAIN),
			'type' => 'select',
			'class' => 'medium',
			'default_value' => 'value',
			'choices' => array(
				array(
					'value' => 'value',
					'label' => esc_html__('Value (Gravity Forms default)', GFIC_TEXT_DOMAIN)
				),
				array(
					'value' => 'image',
					'label' => esc_html__('Image', GFIC_TEXT_DOMAIN)
				),
				array(
					'value' => 'text',
					'label' => esc_html__('Label', GFIC_TEXT_DOMAIN)
				),
				array(
					'value' => 'image_text',
					'label' => esc_html__('Image and Label', GFIC_TEXT_DOMAIN)
				),
				array(
					'value' => 'image_value',
					'label' => esc_html__('Image and Value', GFIC_TEXT_DOMAIN)
				)
			)
		);
		if (!empty($form_choices_entry_value)) {
			$form_choices_entry_field['default_value'] = $form_choices_entry_value;
		}

		$form_lightbox_size_value = (isset($settings['gf_image_choices_lightbox_size'])) ? $settings['gf_image_choices_lightbox_size'] : 'full';
		$form_choices_lightbox_size_field = array(
			'name' => 'gf_image_choices_lightbox_size',
			'tooltip' => esc_html__('The selected image size will be used in the lightbox, if enabled.', GFIC_TEXT_DOMAIN),
			'label' => esc_html__('Lightbox Image Size', GFIC_TEXT_DOMAIN),
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
			'tooltip' => esc_html__('These styles will be loaded for this form only.<br/>Find examples at <a href="https://jetsloth.com/support/gravity-forms-image-choices/">https://jetsloth.com/support/gravity-forms-image-choices/</a>', GFIC_TEXT_DOMAIN),
			'label' => esc_html__('Custom CSS', GFIC_TEXT_DOMAIN),
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
					'label' => esc_html__('Ignore Global Custom CSS for this form?', GFIC_TEXT_DOMAIN),
					'tooltip' => esc_html__('If checked, the custom css entered in the global settings won\'t be loaded for this form.', GFIC_TEXT_DOMAIN),
					'name' => 'gf_image_choices_ignore_global_css'
				)
			)
		);
		if (!empty($form_ignore_global_css_value)) {
			$form_ignore_global_css_field['choices'][0]['default_value'] = 1;
		}

		return array(
			array(
				'title' => esc_html__( 'Image Choices', GFIC_TEXT_DOMAIN ),
				'fields' => array(
					$form_choices_entry_field,
					$form_choices_lightbox_size_field,
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
			$image_entry_setting_values = array('src', 'image', 'image_text', 'image_value');

			foreach($product_summary_image_choices as $summary_item) {
				if (
				in_array($summary_item['product_field_entry_value_type'], $image_entry_setting_values)
				&& !empty($summary_item['product_field_selected_image'])) {

					$replacement_markup = '';

					if ($summary_item['product_field_entry_value_type'] == 'src') {
						$replacement_markup = $summary_item['product_field_selected_image'];
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image') {
						//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="background-image:url('.$summary_item['product_field_selected_image'].')"></div>';
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_text') {
						//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="background-image:url('.$summary_item['product_field_selected_image'].')"></div><div class="product_name">'.$summary_item['product_field_selected_label'].'</div>';
						$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$summary_item['product_field_selected_image'].'" '.$style.' /></div><div class="product_name">'.$summary_item['product_field_selected_label'].'</div>';
					}
					else if ($summary_item['product_field_entry_value_type'] == 'image_value') {
						//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="background-image:url('.$summary_item['product_field_selected_image'].')"></div><div class="product_name">'.$summary_item['product_summary_display_value'].'</div>';
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
							//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="background-image:url('.$option_item['option_field_selected_image'].')"></div>';
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div>';
						}
						else if ($option_item['option_field_entry_value_type'] == 'image_text') {
							//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="background-image:url('.$option_item['option_field_selected_image'].')"></div><div class="product_option_name">'.$option_item['option_field_label'] . ': ' . $option_item['option_field_selected_label'].'</div>';
							$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="display: inline-block;"><img src="'.$option_item['option_field_selected_image'].'" '.$style.' /></div><div class="product_option_name">'.$option_item['option_field_label'] . ': ' . $option_item['option_field_selected_label'].'</div>';
						}
						else if ($option_item['option_field_entry_value_type'] == 'image_value') {
							//$replacement_markup = '<div class="gf-image-choices-entry-choice-image" style="background-image:url(' . $option_item['option_field_selected_image'] . ')"></div><div class="product_option_name">' . $option_item['option_summary_display_value'] . '</div>';
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
			if (!empty($choice['imageChoices_image'])) {
				$img = $choice['imageChoices_image'];
			}
		}
		return $img;
	}

	public function get_choice_image_item_markup($src = '', $text_value = '', $modifier = '') {

		$size = (!empty($modifier) && strlen($modifier) > 5 && substr($modifier, 0, 6) == 'image_') ? substr($modifier, 6) : '80px';


		if (GFCommon::is_entry_detail()) {

			$markup = '<li class="gf-image-choices-entry-choice">';
			//$markup .= '<span class="gf-image-choices-entry-choice-image" style="background-image:url('.$src.')"></span>';
			$markup .= '<span class="gf-image-choices-entry-choice-image">';
			if (!empty($src)) {
				$markup .= '<img src="'.$src.'" style="width:'.$size.'; height:auto; max-width:100%;" />';
			}
			$markup .= '</span>';
			if (!empty($text_value)) {
				$markup .= '<span class="gf-image-choices-entry-choice-text">'.html_entity_decode($text_value).'</span>';
			}
			$markup .= '</li>';

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
			array_unshift($choice_images_markup, '<ul class="gf-image-choices-entry">');
			array_push($choice_images_markup, '</ul>');
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
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_value');
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
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_value');
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
					'image' => $choice['imageChoices_image'],
					'imageID' => (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : ''
				));
			}

			// Modifying the existing elements easier with DOMDocument
			$dom = new DOMDocument;
			//$dom->loadHTML($field_markup, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
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

					if ($field_entry_value_type == 'image' || $field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
						$img = $dom->createElement('span');
						$img->setAttribute('class', 'gquiz-image-choices-choice-image');
						$img->setAttribute('style', 'background-image:url('.$choice['image'].'); display: inline-block; width: 80px; height: 80px; background-size: cover; background-repeat: no-repeat; background-position: 50%;');// inline styles for notification email
						if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
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

			$new_field_markup = utf8_decode( $dom->saveHTML($dom->documentElement) );

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
		$image_entry_setting_values = array('src', 'image', 'image_text', 'image_value');
		// ---------

		$real_value = RGFormsModel::get_lead_field_value( $entry, $field );

		//if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages && $field->type != 'product' && $field->type != 'option') {
		if ( is_object( $field ) && property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages && $field_entry_value_type != 'value' ) {
			$type_property = ($field->type == 'survey' || $field->type == 'poll' || $field->type == 'quiz' || $field->type == 'post_custom_field' || $field->type == 'product' || $field->type == 'option') ? 'inputType' : 'type';

			// Product field doesn't have checkboxes, only radio
			// Option field has both

			if ($field[$type_property] == 'checkbox' && strpos($value, ', ') !== FALSE) {

				// multiple selections
				$ordered_values = (!empty($value)) ? explode(', ', $value) : '';
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
									if ($field_entry_value_type == 'image') {
										$image_item_markup = $this->get_choice_image_item_markup($image);
									}
									else if ($field_entry_value_type == 'image_text') {
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

							list($name, $price) = explode("|", $value);
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
				if ($field->type == 'checkbox') {

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
								if ($field_entry_value_type == 'image') {
									array_push($return_strings, $image);
								}
								else {
									$image_item_markup = '';
									if ($field_entry_value_type == 'image') {
										$image_item_markup = $this->get_choice_image_item_markup($image);
									}
									else if ($field_entry_value_type == 'image_text') {
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
							'image' => $choice['imageChoices_image'],
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
							else if ($field_entry_value_type == 'image') {
								$replacement_markup = '<span class="gf-image-choices-entry-choice-image" style="background-image:url(' . $choice['image'] . ')"></span>';
							}
							else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
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
							'image' => $choice['imageChoices_image'],
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
							else if ($field_entry_value_type == 'image') {
								$replacement_markup = '<div class="gf-image-choices-entry-choice"><span class="gf-image-choices-entry-choice-image" style="background-image:url(' . $choice['image'] . ')"></span></div>';
							}
							else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
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
										if ($field_entry_value_type == 'image') {
											$image_item_markup = $this->get_choice_image_item_markup($image);
										}
										else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
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
								if ($field_entry_value_type == 'image') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text' || $field_entry_value_type == 'image_value') {
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
							if ($field_entry_value_type == 'image') {
								$image_item_markup = $this->get_choice_image_item_markup($image);
							}
							else if ($field_entry_value_type == 'image_text' || ($force_text_instead_of_value && $field_entry_value_type == 'image_value')) {
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
								if ($field_entry_value_type == 'image') {
									$image_item_markup = $this->get_choice_image_item_markup($image);
								}
								else if ($field_entry_value_type == 'image_text') {
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
						$value_without_price = trim(substr($value, 0, strrpos($value, '(')));
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
		$tooltips['image_choices_use_images'] = '<h6>' . esc_html__( 'Use Images', GFIC_TEXT_DOMAIN ) . '</h6>' . esc_html__( 'Enable to use of images as choices.', GFIC_TEXT_DOMAIN );
		$tooltips['image_choices_show_labels'] = '<h6>' . esc_html__( 'Show Labels', GFIC_TEXT_DOMAIN ) . '</h6>' . esc_html__( 'Enable the display of the labels together with the image.', GFIC_TEXT_DOMAIN );
		return $tooltips;
	}

	/**
	 * Add the custom settings for the fields to the fields general tab.
	 *
	 * @param int $position The position the settings should be located at.
	 * @param int $form_id The ID of the form currently being edited.
	 */
	public function image_choice_field_settings( $position, $form_id ) {
		if ( $position == 1362 ) {
			wp_enqueue_media();// For Media Library
		}
	}


	public function add_image_options_markup( $choice_markup, $choice, $field, $value ) {
		if (  property_exists($field, 'imageChoices_enableImages') && $field->imageChoices_enableImages ) {
			$img = $choice['imageChoices_image'];
			$imgID = (isset($choice['imageChoices_imageID'])) ? $choice['imageChoices_imageID'] : '';

			$form = GFAPI::get_form( $field->formId );
			$form_settings = $this->get_form_settings($form);
			$form_lightbox_size = (isset($form_settings['gf_image_choices_lightbox_size'])) ? $form_settings['gf_image_choices_lightbox_size'] : 'full';

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

			if ($field->type == 'product' && !GFCommon::is_form_editor() && property_exists($field, 'imageChoices_showPrices') && $field->imageChoices_showPrices) {
				$choice_price = str_replace('$', '\$', $choice['price']);
				$choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
					'<label ${1}>',
						'<span class="image-choices-choice-image-wrap" style="background-image:url('.$img.')">',
							'<img src="'.$img.'" alt="" class="image-choices-choice-image" data-lightbox-src="'.$lightboxImg.'" />',
						'</span>',
						'<span class="image-choices-choice-text">${2}</span>',
						'<span class="image-choices-choice-price">',
							'<span class="ginput_price"> '.$choice_price.'</span>',
						'</span>',
					'</label>'
				)), $choice_markup);
			}
			else if ($field->type != 'option' || GFCommon::is_form_editor()) {
				$choice_markup = preg_replace('#<label\b([^>]*)>(.*?)</label\b[^>]*>#s', implode("", array(
					'<label ${1}>',
						'<span class="image-choices-choice-image-wrap" style="background-image:url('.$img.')">',
							'<img src="'.$img.'" alt="" class="image-choices-choice-image" data-lightbox-src="'.$lightboxImg.'" />',
						'</span>',
						'<span class="image-choices-choice-text">${2}</span>',
					'</label>'
				)), $choice_markup);
			}
			else {
				$choice_markup = str_replace('<label', '<label data-img="'.$img.'" data-lightbox-src="'.$lightboxImg.'"', $choice_markup);
			}
			return $choice_markup;
		}

		return $choice_markup;
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
