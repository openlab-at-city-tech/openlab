<?php


if ( !defined('ABSPATH' ) )
    exit();

class TRP_Advanced_Tab {

    private $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

	/*
	 * Add new tab to TP settings
	 *
	 * Hooked to trp_settings_tabs
	 */
	public function add_advanced_tab_to_settings( $tab_array ){
		$tab_array[] =  array(
			'name'  => __( 'Advanced', 'translatepress-multilingual' ),
			'url'   => admin_url( 'admin.php?page=trp_advanced_page' ),
			'page'  => 'trp_advanced_page'
		);
		return $tab_array;
	}

	/*
	 * Add submenu for advanced page tab
	 *
	 * Hooked to admin_menu
	 */
	public function add_submenu_page_advanced() {
		add_submenu_page( 'TRPHidden', 'TranslatePress Advanced Settings', 'TRPHidden', apply_filters( 'trp_settings_capability', 'manage_options' ), 'trp_advanced_page', array(
			$this,
			'advanced_page_content'
		) );
	}

	/**
	 * Register setting
	 *
	 * Hooked to admin_init
	 */
	public function register_setting(){
		register_setting( 'trp_advanced_settings', 'trp_advanced_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Output admin notices after saving settings.
	 */
	public function admin_notices(){
		settings_errors( 'trp_advanced_settings' );
	}

	/**
	 * Sanitize settings
	 */
	public function sanitize_settings( $submitted_settings ){
        $array_possible_settings_for_tab = apply_filters('trp_possible_values_for_tab', array('ald_settings', 'troubleshooting', 'exclude_strings', 'debug', 'miscellaneous_options', 'custom_language'));
        if (isset($_REQUEST['tab']) && in_array($_REQUEST['tab'], $array_possible_settings_for_tab)){
            $_REQUEST['_wp_http_referer'] = add_query_arg( 'tab', $_REQUEST['tab'], $_REQUEST['_wp_http_referer'] );//phpcs:ignore
        }
		$registered_settings = $this->get_registered_advanced_settings();
		$prev_settings = get_option('trp_advanced_settings', array());

        $settings = array();
		foreach ( $registered_settings as $registered_setting ){

		    /* All advanced options are set to false and then maybe set to a default value below if a particular
		     * advanced option is not set in array $submitted_settings
             * Form submitted checkboxes are never set, so this is especially useful
		     */
            if( !isset( $submitted_settings[$registered_setting['name']] ) ){
                $submitted_settings[$registered_setting['name']] = false;
            }

			if ( isset( $submitted_settings[$registered_setting['name']] ) ){
				switch ($registered_setting['type'] ) {
					case 'checkbox': {
						$settings[ $registered_setting['name'] ] = ( $submitted_settings[ $registered_setting['name'] ] === 'yes' ) ? 'yes' : 'no';
						break;
					}
                    case 'select': {
                        if ( isset( $registered_setting['options'] ) && isset( $registered_setting['options'][ $submitted_settings[ $registered_setting['name'] ] ] ) ) {
                            $settings[ $registered_setting['name'] ] = $submitted_settings[ $registered_setting['name'] ];
                        } else {
                            $settings[ $registered_setting['name'] ] = ( empty( $registered_setting['default'] ) ) ? false : $registered_setting['default'];
                        }
                        break;
                    }
                    case 'input': {
                        $settings[ $registered_setting['name'] ] = sanitize_text_field($submitted_settings[ $registered_setting['name'] ]);
                        break;
                    }
                    case 'radio': {
                        if ( isset( $registered_setting['options'] ) && in_array( $submitted_settings[ $registered_setting['name'] ], $registered_setting['options'] ) ){
                            $settings[ $registered_setting['name'] ] = $submitted_settings[ $registered_setting['name'] ];
                        }else{
                            $settings[ $registered_setting['name'] ] = ( empty($registered_setting['default'] ) )? false : $registered_setting['default'];
                        }
                        break;
                    }
                    case 'custom': {
                        if ( isset( $registered_setting['rows'] ) ) {
                            foreach ( $registered_setting['rows'] as $row_label => $row_type ) {
                                if ( isset( $submitted_settings[ $registered_setting['name'] ][ $row_label ] ) ) {

                                    if ( $row_type != 'textarea' )
                                        $value = sanitize_text_field( $submitted_settings[ $registered_setting['name'] ][ $row_label ] );
                                    else
                                        $value = sanitize_textarea_field( $submitted_settings[ $registered_setting['name'] ][ $row_label ] );

                                    $settings[ $registered_setting['name'] ][ $row_label ] = $value;
                                }
                            }
                        }

                        if ( $registered_setting['name'] === 'enable_hreflang_xdefault' ){
                            $select_key   = $registered_setting['name'];
                            $checkbox_key = $registered_setting['name'] . '-checkbox';

                            $is_checkbox_disabled = $submitted_settings[$select_key] === false ;

                            $select_value   = $is_checkbox_disabled ? 'disabled' : $submitted_settings[$select_key];
                            $checkbox_value = $is_checkbox_disabled ? 'no' : $submitted_settings[$checkbox_key];

                            $settings[ $select_key ]   = sanitize_text_field( $select_value );
                            $settings[ $checkbox_key ] = sanitize_text_field( $checkbox_value );
                        }

						break;
					}
					case 'input_array': {
                        $formats_array_key = $registered_setting['name'];
                        $checkbox_key      = $registered_setting['name'] . '-checkbox';

						foreach ( $registered_setting['rows'] as $row_label => $row_name ) {
                            if (isset($submitted_settings[$formats_array_key][$row_label])) {
                                    $settings[$formats_array_key][$row_label] = sanitize_text_field( $submitted_settings[$formats_array_key][$row_label] );
                            }
                        }

                        $checkbox_value = isset( $submitted_settings[$checkbox_key] ) && $submitted_settings[$checkbox_key] !== false ? $submitted_settings[$checkbox_key] : 'no';

                        $settings[$checkbox_key] = sanitize_text_field( $checkbox_value );

						break;
					}
                    case 'number': {
                        $settings[ $registered_setting['name'] ] = sanitize_text_field(intval($submitted_settings[ $registered_setting['name'] ] ) );
                        break;
                    }
                    case 'list':
                    case 'list_input':
					case 'mixed':
						/*
						We use the same parsing and saving mechanism for list and mixed advanced types.
						*/
                    	{
						$settings[ $registered_setting['name'] ] = array();
                        $one_column = '';
						foreach ( $registered_setting['columns'] as $column => $column_name ) {
                            $one_column = ( empty ( $one_column ) && !(is_array($column_name) && $column_name ['type'] === 'checkbox') ) ? $column : $one_column;
							$settings[ $registered_setting['name'] ][ $column ] = array();
							if ( isset($submitted_settings[ $registered_setting['name'] ][ $column ] ) ) {
                                foreach ($submitted_settings[$registered_setting['name']][$column] as $key => $value) {
                                    $settings[$registered_setting['name']][$column][] = sanitize_text_field($value);
                                }
                            }
						}

						 /* If the setting is a type "checkbox" we remove one empty value from the sub-array if it comes after a 'yes' value
		                    In this case we properly save an empty value for an unchecked checkbox
		                    and also control the display checked/unchecked on the frontend
						 */
	                    foreach ( $registered_setting['columns'] as $column => $column_name ) {
	                        if (is_array($column_name) && $column_name ['type'] === 'checkbox'){
			                    foreach ($settings[ $registered_setting['name'] ] [$column] as $submitted_key => $submitted_value) {
					                    if ( $submitted_value === 'yes' ) {
						                    unset ( $settings[ $registered_setting['name'] ] [ $column ] [ $submitted_key + 1 ] );
					                    }
				                    // Check for illegal values at checkbox side
				                    if ( !$submitted_value === 'yes' || !$submitted_value === '' ) {
					                    $settings[ $registered_setting['name'] ] [ $column ] [$submitted_key] = '';
				                    }
			                    }
	                        }
	                    }

						// remove empty rows except checkboxes
						foreach ( $settings[ $registered_setting['name'] ][ $one_column ] as $key => $value ) {
							$is_empty = true;
							foreach ( $registered_setting['columns'] as $column => $column_name ) {
								if ( $settings[ $registered_setting['name'] ][$column][$key] != "" || ( is_array($column_name) && $column_name ['type'] === 'checkbox') ) {
									$is_empty = false;
									break;
								}
							}
							if ( $is_empty ){
								foreach ( $registered_setting['columns'] as $column => $column_name ) {

									unset( $settings[ $registered_setting['name'] ][$column][$key] );
								}
							}
						}

						foreach ( $settings[ $registered_setting['name'] ] as $column => $value ) {
							$settings[ $registered_setting['name'] ][ $column ] = array_values( $settings[ $registered_setting['name'] ][ $column ] );
						}
						break;
					}
				}
			} //endif

            // not all settings are updated by the user. Some are modified by the program and used as storage.
            // This is somewhat bad from a data model kind of way, but it's easy to pass the $settings variable around between classes.
            if( isset($registered_setting['data_model'])
                && $registered_setting['data_model'] == 'not_updatable_by_user'
                && isset($prev_settings[$registered_setting['name']])
            )
            {
                $settings[ $registered_setting['name'] ] = $prev_settings[$registered_setting['name']];
            }

		} //end foreach of parsing all the registered settings array

        if ( apply_filters( 'trp_saving_advanced_settings_is_successful', true, $settings, $submitted_settings ) ) {
            add_settings_error( 'trp_advanced_settings', 'settings_updated', esc_html__( 'Settings saved.', 'translatepress-multilingual' ), 'updated' );
        }

		return apply_filters( 'trp_extra_sanitize_advanced_settings', $settings, $submitted_settings, $prev_settings );
	}

	/*
	 * Advanced page content
	 */

	public function get_registered_advanced_settings(){
		return apply_filters( 'trp_register_advanced_settings', array() );
	}

	/*
	 * Require the custom codes from the specified folder
	 */

	public function advanced_page_content(){
		require_once TRP_PLUGIN_DIR . 'partials/advanced-settings-page.php';
	}

	/*
	 * Get array of registered options from custom code to display in Advanced Settings page
	 */

	public function include_custom_codes(){
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/disable-dynamic-translation.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/force-slash-at-end-of-links.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/enable-numerals-translation.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/custom-date-format.php');
		include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/custom-language.php');
		include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/exclude-dynamic-selectors.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/exclude-gettext-strings.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/exclude-selectors.php');
		include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/exclude-selectors-automatic-translation.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/fix-broken-html.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/show-dynamic-content-before-translation.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/enable-hreflang-xdefault.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/strip-gettext-post-content.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/strip-gettext-post-meta.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/exclude-words-from-auto-translate.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/disable-post-container-tags.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/separators.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/disable-languages-sitemap.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/remove-duplicates-from-db.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/do-not-translate-certain-paths.php');
        include_once (TRP_PLUGIN_DIR . 'includes/advanced-settings/opposite-flag-shortcode.php');
        include_once (TRP_PLUGIN_DIR . 'includes/advanced-settings/regular-tab-string-translation.php');
        include_once (TRP_PLUGIN_DIR . 'includes/advanced-settings/open-language-switcher-shortcode-on-click.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/hreflang-remove-locale.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/html-lang-remove-locale.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/serve-similar-translation.php');
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/disable-gettext-strings.php');
        //we can remove this at some point
        include_once(TRP_PLUGIN_DIR . 'includes/advanced-settings/load-legacy-seo-pack.php');

	}

	/*
	 * Hooked to trp_before_output_advanced_settings_options
	 */
    function trp_advanced_settings_content_table(){

        $advanced_settings_array = $this->get_registered_advanced_settings();

        $html                    = '<div class="trp_advanced_tab_content_table__wrapper"><div id="trp_advanced_tab_content_table">';
        $advanced_settings_array = apply_filters( 'trp_advanced_tab_add_element', $advanced_settings_array );
        $advanced_settings_array = apply_filters('trp_advanced_tab_add_element', $advanced_settings_array);

        $first_item = '';
        $other_items = '';

        foreach ($advanced_settings_array as $setting) {
            if ($setting['type'] === 'separator') {
                $tab_html = '<span class="trp_advanced_tab_content_table_item">
                                <a href="#' . esc_html($setting['id']) . '" class="' . esc_html($setting['id']) . '">
                                    ' . esc_html($setting['label']) . '
                                </a>
                            </span>';

                if ($setting['name'] === 'automatic_user_language_detection') {
                    $first_item = $tab_html; // Store this to add it first
                } else {
                    $other_items .= $tab_html; // Collect other separators
                }
            }
        }

        $html .= $first_item . $other_items;

        $html .= '</div></div>';

        echo $html;//phpcs:ignore
    }



    /*
     * Hooked to trp_settings_navigation_tabs
     */
    public function output_advanced_options() {
        echo "<input type='hidden' name='tab' id='trp_advanced_settings_referer'>"; // phpcs:ignore
        $advanced_settings_array = $this->get_registered_advanced_settings();

        $grouped_settings = [];

        // Step 1: Group settings by ID
        foreach ( $advanced_settings_array as $setting ) {
            if ( !isset( $setting['container'] ) )
                continue;

            $array_key = $setting['type'] === 'container_title' ? 'container_title' : 'container_elements';

            $grouped_settings[$setting['container']][$array_key][] = $setting;
        }

        // Step 2: Loop through each group and output settings within a container
        foreach ( $grouped_settings as $id => $settings ) {
            $container_id = $settings['container_elements'][0]['id'];

            echo "<div class='trp-settings-container trp-settings-container-" . esc_attr($container_id) . "'>";
            echo $this->container_title_setting( $settings['container_title'][0] ); //phpcs:ignore

            echo "<div class='trp-settings-options__wrapper'>";
                foreach ( $settings['container_elements'] as $setting ) {
                    switch ( $setting['type'] ) {
                        case 'checkbox':
                            echo $this->checkbox_setting($setting); // phpcs:ignore
                            break;
                        case 'radio':
                            echo $this->radio_setting($setting); // phpcs:ignore
                            break;
                        case 'input':
                            echo $this->input_setting($setting); // phpcs:ignore
                            break;
                        case 'number':
                            echo $this->input_setting($setting, 'number'); // phpcs:ignore
                            break;
                        case 'input_array':
                            echo $this->input_array_setting($setting); // phpcs:ignore
                            break;
                        case 'select':
                            echo $this->select_setting($setting); // phpcs:ignore
                            break;
                        case 'list':
                            echo $this->add_to_list_setting($setting); // phpcs:ignore
                            break;
                        case 'list_input':
                            echo $this->add_to_list_input_setting($setting); // phpcs:ignore
                            break;
                        case 'text':
                            echo $this->text_setting($setting); // phpcs:ignore
                            break;
                        case 'mixed':
                            echo $this->mixed_setting($setting); // phpcs:ignore
                            break;
                        case 'custom':
                            echo $this->custom_setting($setting); // phpcs:ignore
                            break;
                    }
                }
            echo "</div>"; // Close options wrapper

            echo "</div>"; // Close container for this group
        }
    }


	/**
	 * Return HTML of a checkbox type setting
	 *
	 * @param $setting
	 *
	 * @return 'string'
	 */
    public function checkbox_setting( $setting ) {
        $adv_option = $this->settings['trp_advanced_settings'];
        $checked = ( isset( $adv_option[ $setting['name'] ] ) && $adv_option[ $setting['name'] ] === 'yes' ) ? 'checked' : '';

        $html = "<div class='trp-settings-checkbox trp-settings-options-item'>
                <input type='checkbox' id='" . esc_attr( $setting['name'] ) . "' 
                       name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "]' 
                       value='yes' " . $checked . " />

                <label for='" . esc_attr( $setting['name'] ) . "' class='trp-checkbox-label'>
                    <div class='trp-checkbox-content'>
                        <span class='trp-primary-text-bold'>" . esc_html( $setting['label'] ) . "</span>
                        <span class='trp-description-text'>" . wp_kses_post( $setting['description'] ) . "</span>
                    </div>
                </label>
            </div>";

        return apply_filters( 'trp_advanced_setting_checkbox', $html );
    }


    /**
     * Return HTML of a radio button type setting
     *
     * @param $setting
     *
     * @return 'string'
     */
    public function radio_setting( $setting ){

        $adv_option = $this->settings['trp_advanced_settings'];
        $html = "<div class='trp-radio__wrapper trp-settings-options-item'>
                    <span class='trp-primary-text-bold'>" . esc_html($setting['label'] ) . "</span>
                    <div class='trp-adst-radio trp-radio__wrapper'>";

        foreach($setting[ 'options' ] as $key => $option ){
            if( isset( $adv_option[ $setting['name'] ] ) && !empty( $adv_option[ $setting['name'] ] ) ){
                if( $adv_option[ $setting['name'] ] === $option ){
                    $checked = 'checked="checked"';
                }
                else{
                    $checked = '';
                }
            }
            else{
                if( $setting['default'] === $option ){
                    $checked = 'checked="checked"';
                }
                else{
                    $checked = '';
                }
            }
            $setting_name  = $setting['name'];
            $label  = $setting[ 'labels' ][$key];
            $html .= "<label class='trp-primary-text'>
	                    <input type='radio' id='". esc_attr( $setting_name ) . "' name='trp_advanced_settings[". esc_attr( $setting_name ) ."]' value='". esc_attr( $option ) ."' $checked >
	                    ". esc_html( $label ) ."
			          </label>";
        }

        $html .=   "</div>
                    <span class='trp-description-text'>
                        " . wp_kses_post( $setting['description'] ). "
                    </span>
                </div>";
        return apply_filters('trp_advanced_setting_radio', $html );
    }

    /**
     * Return HTML of a input type setting
     *
     * @param array $setting
     * @param string $type
     *
     * @return 'string'
     */
    public function input_setting( $setting, $type = 'text'){

        $adv_option = $this->settings['trp_advanced_settings'];
        $default = ( isset( $setting['default'] )) ? $setting['default'] : '';
        $value = ( isset( $adv_option[ $setting['name'] ] ) ) ? $adv_option[ $setting['name'] ] : $default;
        $html = "
             <div class='trp_advanced_flex_box'>
                <div class='trp_advanced_option_name'>" . esc_html( $setting['label'] ). "</div>
                <div class='trp_advanced_settings_align'>
	                <label>
	                    <input type='" . esc_attr( $type ) ."' id='" . esc_attr( $setting['name'] ) ."' name='trp_advanced_settings[" .esc_attr( $setting['name'] )."]' value='" . esc_attr( $value ) ."'>
			        </label>
                    <p class='description'>
                        ". wp_kses_post( $setting['description'] ) . "
                    </p>
                </div>
            </div>";
        return apply_filters('trp_advanced_setting_input', $html );
    }

	/**
	 * Return HTML of an array type setting
	 *
	 * @param $setting
	 * @param string $type
	 *
	 * @return 'string'
	 */
    public function input_array_setting ($setting, $type = 'text'){
        $adv_option = $this->settings['trp_advanced_settings'];
        $default = ( isset( $setting['default'] )) ? $setting['default'] : '';

        $checked = ( isset( $adv_option[ $setting['name'] . '-checkbox' ] ) && $adv_option[ $setting['name'] . '-checkbox' ] === 'yes' )
            || !empty( $adv_option[ $setting['name'] ] )
            ? 'checked'
            : '';

        $input_rows = '<div class="trp-input-array-rows__wrapper">';

        foreach ($setting['rows'] as $row_label=>$row_name ){
            $value = ( isset( $adv_option[ $setting['name'] ][$row_label] ) ) ? $adv_option[ $setting['name'] ][$row_label]  : $default;

            $input_rows.= "<div class='trp-input-array-setting-row'>
                                <label class='trp-primary-text' for='". esc_attr( $setting['name'] ) ."-".esc_attr( $row_label ) ."'> ".esc_attr( $row_name )." </label>
                                <input type='text' id='". esc_attr( $setting['name'] ) ."-". esc_attr( $row_label ) ."' name='trp_advanced_settings[". esc_attr( $setting['name'] )."][". esc_attr( $row_label )."]' value='".esc_attr( $value )."'>
                           </div>";
        }

        $input_rows.= "</div>";

        $html = "<div class='trp-settings-custom-checkbox__wrapper'>
                    <div class='trp-settings-checkbox'>
                        <input type='checkbox' id='" . esc_attr( $setting['name'] ) . "' 
                               name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "-checkbox]' 
                               value='yes' " . $checked . " />
        
                        <label for='" . esc_attr( $setting['name'] ) . "' class='trp-checkbox-label'>
                            <div class='trp-checkbox-content'>
                                <span class='trp-primary-text-bold'>" . esc_html( $setting['label'] ) . "</span>
                                <span class='trp-description-text'>" . wp_kses_post( $setting['description'] ) . "</span>
                            </div>
                        </label>
                    </div>
                    $input_rows
                 </div>";

        return apply_filters('trp_advanced_setting_input_array', $html );
    }

    /**
     * Return HTML of an input type setting
     *
     * @param array $setting
     * @param string $type
     *
     * @return 'string'
     */
    public function select_setting( $setting ){

        $option = get_option('trp_advanced_settings', true );
        $default = ( isset( $setting['default'] )) ? $setting['default'] : '';
        $value = ( isset( $option[ $setting['name'] ] ) ) ? $option[ $setting['name'] ] : $default;

        $options = '';
        foreach ($setting['options'] as $lang => $label) {
            ($value == $lang) ? $selected = 'selected' : $selected = '' ;
            $options .= "<option value='". esc_attr( $lang ) ."' $selected>". esc_html( $label )."</option>";
        }

        $html = "
             <div class='trp_advanced_flex_box'>
                <div class='trp_advanced_option_name'>" . esc_html( $setting['label'] ) ."</div>
                <div class='trp_advanced_settings_align'>
	                <label>
	                    <select id='".esc_attr( $setting['name'] ) ."' name='trp_advanced_settings[". esc_attr( $setting['name'] ) ."]' style='width: 225px;'>
	                        ". $options ."
	                    </select>
			        </label>
                    <p class='description'>
                        ". wp_kses_post( $setting['description'] ) ."
                    </p>
                </div>
            </div>";
        return apply_filters('trp_advanced_setting_select', $html );
    }

    /**
     * Return HTML of a container title type setting
     *
     * @param $setting
     *
     * @return 'string'
     */
    public function container_title_setting( $setting ){
        $html = "<div class='trp-settings-container-title__wrapper'>
                    <h2 class='trp-settings-primary-heading'>" . esc_html( $setting['label'] ) . "</h2>
                    <div class='trp-settings-separator'></div>
                </div>";

        return apply_filters('trp_advanced_setting_separator', $html );
    }

	/**
	 * Return HTML of a checkbox type setting
	 *
	 * @param $setting
	 *
	 * @return 'string'
	 */
    public function add_to_list_setting( $setting ) {
        $adv_option = $this->settings['trp_advanced_settings'];

        $remove_element = "<div class='trp-remove-language__container trp-adst-remove-element'>
                            <span class='trp-adst-remove-element-text' data-confirm-message='" . esc_html__('Are you sure you want to remove this item?', 'translatepress-multilingual') . "'>" . esc_html__( 'Remove', 'translatepress-multilingual' ) . "</span>
                            <svg width='20' height='21' viewBox='0 0 20 21' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                <path fill-rule='evenodd' clip-rule='evenodd' d='M12 4.5H15C15.6 4.5 16 4.9 16 5.5V6.5H3V5.5C3 4.9 3.5 4.5 4 4.5H7C7.2 3.4 8.3 2.5 9.5 2.5C10.7 2.5 11.8 3.4 12 4.5ZM11 4.5C10.8 3.9 10.1 3.5 9.5 3.5C8.9 3.5 8.2 3.9 8 4.5H11ZM14.1 17.6L15 7.5H4L4.9 17.6C5 18.1 5.4 18.5 5.9 18.5H13.1C13.6 18.5 14.1 18.1 14.1 17.6Z' fill='#757575'/>
                            </svg>
                       </div>";

        $html = "
                <span class='trp-description-text'>" . wp_kses_post( $setting['description'] ) . "</span>
                <table class='trp-adst-list-option'>
                    <thead class='trp-add-to-input-setting-columns'>
                        <tr>";
        foreach( $setting['columns'] as $key => $value ){
            $html .= '<th><span class="trp-primary-text-bold">' . esc_html( $value ) . '</span></th>';
        }
        $html .=        "</tr>
                    </thead>";

        $first_column = key($setting['columns']);

        $html .= "<tbody>";

        // Existing Entries
        if ( isset( $adv_option[ $setting['name'] ] ) && is_array( $adv_option[ $setting['name'] ] ) ) {
            foreach ( $adv_option[ $setting['name'] ][ $first_column ] as $index => $value ) {
                $html .= "<tr class='trp-list-entry'>";
                foreach ( $setting['columns'] as $column => $column_name ) {
                    $column_value = isset($adv_option[ $setting['name'] ][ $column ][ $index ]) ? esc_attr($adv_option[ $setting['name'] ][ $column ][ $index ]) : '';
                    $html .= "<td><input type='text' name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $column ) . "][]' value='" . $column_value . "'></td>";
                }
                $html .= "<td>$remove_element</td>";
                $html .= "</tr>";
            }
        }

        // Add New Entry Row
        $html .= "<tr class='trp-add-list-entry trp-list-entry'>";
        foreach( $setting['columns'] as $column => $column_name ) {
            $html .= "<td class='trp-add-list-entry-input-col'><input type='text' id='new_entry_" . esc_attr( $setting['name'] ) . "_" . esc_attr( $column ) . "' data-name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $column ) . "][]' data-setting-name='" . esc_attr( $setting['name'] ) . "' data-column-name='" . esc_attr( $column ) . "'></td>";
        }

        $html .= "<td class='trp-add-list-entry-btn-col'>
                <input type='button' class='trp-button-secondary trp-adst-button-add-new-item' value='" . esc_html__( 'Add', 'translatepress-multilingual' ) . "'>
                <div style='display: none;'>$remove_element</div>
              </td>";

        $html .= "</tr></tbody></table>";

        return apply_filters( 'trp_advanced_setting_list', $html );
    }


    /**
     * Return HTML of input type list
     *
     * @param $setting
     *
     * @return 'string'
     */
    public function add_to_list_input_setting( $setting ){
        $adv_option = $this->settings['trp_advanced_settings'];

        $remove_element = "<div class='trp-remove-language__container trp-adst-remove-element'>
                                <span class='trp-adst-remove-element-text' data-confirm-message='" . esc_html__('Are you sure you want to remove this item?', 'translatepress-multilingual') . "'>" . esc_html__( 'Remove', 'translatepress-multilingual' ) . "</span>
                                <svg width='20' height='21' viewBox='0 0 20 21' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                    <path fill-rule='evenodd' clip-rule='evenodd' d='M12 4.5H15C15.6 4.5 16 4.9 16 5.5V6.5H3V5.5C3 4.9 3.5 4.5 4 4.5H7C7.2 3.4 8.3 2.5 9.5 2.5C10.7 2.5 11.8 3.4 12 4.5ZM11 4.5C10.8 3.9 10.1 3.5 9.5 3.5C8.9 3.5 8.2 3.9 8 4.5H11ZM14.1 17.6L15 7.5H4L4.9 17.6C5 18.1 5.4 18.5 5.9 18.5H13.1C13.6 18.5 14.1 18.1 14.1 17.6Z' fill='#757575'/>
                                </svg>
                           </div>";

        $html = "
                    <span class='trp-description-text'>
                        " . wp_kses_post( $setting['description'] ) . "
                    </span>
	                <table class='trp-adst-list-option'>
						<thead class='trp-add-to-input-setting-columns'><tr>";
                            foreach( $setting['columns'] as $key => $value ){
                                $html .= '<th><span class="trp-primary-text-bold">' . esc_html( $value ) . '</span></th>';
                            }
        $html .=        "</tr></thead>";

        $first_column = '';
        foreach( $setting['columns'] as $column => $column_name ) {
            $first_column = $column;
            break;
        }

        $html .= '<tbody>';

        if ( isset( $adv_option[ $setting['name'] ] ) && is_array( $adv_option[ $setting['name'] ] ) ) {
            foreach ( $adv_option[ $setting['name'] ][ $first_column ] as $index => $value ) {
                $html .= "<tr class='trp-list-entry' id='trp-add-to-input-setting-div-entry'>";
                foreach ( $setting['columns'] as $column => $column_name ) {
                    $html .= "<td><input type='text' name='trp_advanced_settings[" . esc_attr( $setting['name'] ). "][" . esc_attr( $column ) . "][]' value='". htmlspecialchars($adv_option[ $setting['name'] ][ $column ][ $index ], ENT_QUOTES) ."'></td>";
                }

                $html .= "<td>$remove_element</td>";

                $html .= "</tr>";
            }
        }

        // add new entry to list
        $html .= "<tr class='trp-add-list-entry trp-list-entry'>";
        foreach( $setting['columns'] as $column => $column_name ) {
            $html .= "<td class='trp-add-list-entry-input-col'><input type='text' id='new_entry_" . esc_attr( $setting['name'] ) . "_" . esc_attr( $column ) . "' data-name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $column ) . "][]' data-setting-name='" . esc_attr( $setting['name'] ) . "' data-column-name='" . esc_attr( $column ) . "'></td>";

        }
        $html .= "<td class='trp-add-list-entry-btn-col'><input type='button' class='trp-button-secondary trp-adst-button-add-new-item' value='" . esc_html__( 'Add', 'translatepress-multilingual' ) . "'>
                    <div style='display: none;'>$remove_element</div>
                  </td>";

        $html .= "</tr></tbody></table>";

        return apply_filters( 'trp_advanced_setting_list', $html );
    }

    /**
     * Return HTML of a text type setting
     *
     * @param $setting
     *
     * @return 'string'
     */
    public function text_setting( $setting ){
        $html = "<div class='trp-settings-options-item trp-settings-options-item__column trp-settings-options-item__nocheckbox'>
                    <div class='trp-primary-text-bold'>" . esc_html( $setting['label'] ) . "</div>
                    <span class='trp-description-text'>
                        " . wp_kses_post( $setting['description'] ) . "
                    </span>
                 </div>";
        return apply_filters('trp_advanced_setting_text', $html );
    }

    public function mixed_setting($setting) {
        $adv_option = $this->settings['trp_advanced_settings'];

        $remove_element = "<div class='trp-remove-language__container trp-adst-remove-element'>
                            <span class='trp-adst-remove-element-text' data-confirm-message='" . esc_html__('Are you sure you want to remove this item?', 'translatepress-multilingual') . "'>" . esc_html__( 'Remove', 'translatepress-multilingual' ) . "</span>
                            <svg width='20' height='21' viewBox='0 0 20 21' fill='none' xmlns='http://www.w3.org/2000/svg'>
                                <path fill-rule='evenodd' clip-rule='evenodd' d='M12 4.5H15C15.6 4.5 16 4.9 16 5.5V6.5H3V5.5C3 4.9 3.5 4.5 4 4.5H7C7.2 3.4 8.3 2.5 9.5 2.5C10.7 2.5 11.8 3.4 12 4.5ZM11 4.5C10.8 3.9 10.1 3.5 9.5 3.5C8.9 3.5 8.2 3.9 8 4.5H11ZM14.1 17.6L15 7.5H4L4.9 17.6C5 18.1 5.4 18.5 5.9 18.5H13.1C13.6 18.5 14.1 18.1 14.1 17.6Z' fill='#757575'/>
                            </svg>
                       </div>";

        $html = "<span class='trp-description-text'>" . wp_kses_post($setting['first_description']) . "</span>";


        $html .= "<table id='trp-cuslang-table' class='trp-adst-list-option'>
                    <thead class='trp-add-to-input-setting-columns'>";

        // Column headers
        foreach ( $setting['columns'] as $option_name => $option_details ) {
            if ( !empty($option_details['required'] ) ) {
                $html .= "<th class='trp_lang_code'><span class='trp-primary-text-bold'>" . esc_html($option_details['label']) . " <span title='Required'>*</span></span></th>";
            }

            else {
                $html .= "<th><span class='trp-primary-text-bold'>" . esc_html($option_details['label']) . "</span></th>";
            }
        }
        $html .= "<th></th></thead>";

        $first_column = key($setting['columns']);

        // Existing entries
        if ( !empty( $adv_option[$setting['name']] ) && is_array( $adv_option[$setting['name']] ) ) {
            foreach ( $adv_option[$setting['name']][$first_column] as $index => $value ) {
                $html .= "<tr class='trp-list-entry'>";
                foreach ( $setting['columns'] as $option_name => $option_details ) {
                    $option_value = $adv_option[$setting['name']][$option_name][$index] ?? '';

                    switch ($option_details['type']) {
                        case 'text':
                            $html .= "<td class='trp-col-" . esc_attr($option_name) . "'>
                                    <input class='trp_narrow_input' type='text' 
                                    name='trp_advanced_settings[" . esc_attr($setting['name']) . "][" . esc_attr($option_name) . "][]' 
                                    value='" . esc_attr($option_value) . "'>
                                  </td>";
                            break;

                        case 'textarea':
                            $html .= "<td>
                                    <textarea class='trp_narrow_input' 
                                    name='trp_advanced_settings[" . esc_attr($setting['name']) . "][" . esc_attr($option_name) . "][]'>"
                                . esc_textarea($option_value) . "</textarea>
                                  </td>";
                            break;

                        case 'select':
                            $html .= "<td>
                                    <select class='trp-select-advanced' 
                                    name='trp_advanced_settings[" . esc_attr($setting['name']) . "][" . esc_attr($option_name) . "][]'>
                                      <option value=''>" . esc_html__('Select...', 'translatepress-multilingual') . "</option>";
                            foreach ($option_details["values"] as $select_value) {
                                $selected = ($option_value === $select_value) ? "selected='selected'" : '';
                                $html .= "<option value='" . esc_attr($select_value) . "' $selected>" . esc_html($select_value) . "</option>";
                            }
                            $html .= "</select></td>";
                            break;

                        case 'checkbox':
                            $checked = ($option_value === 'yes') ? "checked='checked'" : '';
                            $html .= "<td>
                                    <div class='trp-settings-checkbox trp-settings-options-item'>
                                        <input type='checkbox' id='" . esc_attr($setting['name']) . "_" . esc_attr($option_name) . "_$index' 
                                               name='trp_advanced_settings[" . esc_attr($setting['name']) . "][" . esc_attr($option_name) . "][]' 
                                               value='yes' $checked />
                                    </div>
                                  </td>";
                            break;
                    }
                }
                $html .= "<td>$remove_element</td>";
                $html .= "</tr>";
            }
        }

        // Add new entry to list; renders the last row which is initially empty.
        $html .= "<tr class='trp-add-list-entry trp-list-entry'>";
        foreach ( $setting['columns'] as $option_name => $option_details ) {
            switch ($option_details['type']) {
                case 'text':
                    $html .= "<td class='trp-col-" . esc_attr($option_name) . "'>
                            <input type='text' class='trp_narrow_input' 
                            id='new_entry_" . esc_attr($setting['name']) . "_" . esc_attr($option_name) . "' 
                            placeholder='" . esc_attr($option_details['placeholder'] ?? '') . "' data-name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $option_name ) . "][]' data-setting-name='" . esc_attr( $setting['name'] ) . "' data-column-name='" . esc_attr( $option_name ) . "'>
                          </td>";
                    break;

                case 'textarea':
                    $html .= "<td><textarea class='trp_narrow_input' id='new_entry_" . esc_attr($setting['name']) . "_" . esc_attr($option_name) . "'  data-name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $option_name ) . "][]' data-setting-name='" . esc_attr( $setting['name'] ) . "' data-column-name='" . esc_attr( $option_name ) . "'></textarea></td>";
                    break;

                case 'select':
                    $html .= "<td>
                            <select id='new_entry_" . esc_attr($setting['name']) . "_" . esc_attr($option_name) . "'  data-name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $option_name ) . "][]' data-setting-name='" . esc_attr( $setting['name'] ) . "' data-column-name='" . esc_attr( $option_name ) . "'>
                              <option value=''>" . esc_html__('Select...', 'translatepress-multilingual') . "</option>";
                    foreach ($option_details["values"] as $select_value) {
                        $html .= "<option value='" . esc_attr($select_value) . "'>" . esc_html($select_value) . "</option>";
                    }
                    $html .= "</select></td>";
                    break;

                case 'checkbox':
                    $html .= "<td>
                            <div class='trp-settings-checkbox trp-settings-options-item'>
                                <input type='checkbox' id='new_entry_" . esc_attr($setting['name']) . "_" . esc_attr($option_name) . "' value='yes'  data-name='trp_advanced_settings[" . esc_attr( $setting['name'] ) . "][" . esc_attr( $option_name ) . "][]' data-setting-name='" . esc_attr( $setting['name'] ) . "' data-column-name='" . esc_attr( $option_name ) . "'>
                            </div>
                          </td>";
                    break;
            }
        }
        $html .= "<td class='trp-col-add-new'>
                    <input type='button' class='trp-button-secondary trp-adst-button-add-new-item' value='" . esc_html__('Add', 'translatepress-multilingual') . "'>
                    <div style='display: none;'>$remove_element</div>
                  </td>";
        $html .= "</tr></table>";

        $html .= "<span class='trp-description-text'>" . wp_kses_post($setting['second_description']) . "</span>";

        return apply_filters('trp_advanced_setting_list', $html);
    }




    /**
     * Can be used to output content outside the very static methods from above
     * Hook to the provided filter
     *
     */
    public function custom_setting( $setting ){

        if( empty( $setting['name'] ) )
            return;

        return apply_filters( 'trp_advanced_setting_custom_' . $setting['name'], $setting );

    }

}
