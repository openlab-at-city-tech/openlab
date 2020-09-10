<?php

class ANR_Settings {

	private static $instance;

	public static function init() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	function actions_filters() {
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_init', array( $this, 'settings_save' ), 99 );
		add_filter( 'plugin_action_links_' . plugin_basename( ANR_PLUGIN_FILE ), array( $this, 'add_settings_link' ) );
		add_action('admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		if ( anr_same_settings_for_all_sites() ) {
			add_action( 'network_admin_menu', array( $this, 'network_menu_page' ) );
			add_filter( 'network_admin_plugin_action_links_' . plugin_basename( ANR_PLUGIN_FILE ), array( $this, 'add_settings_link' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'menu_page' ) );
		}

	}
	
	function admin_enqueue_scripts() {
		wp_register_script( 'anr-admin', ANR_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), ANR_PLUGIN_VERSION, true );
	}

	function admin_init() {
		register_setting( 'anr_admin_options', 'anr_admin_options', array( $this, 'options_sanitize' ) );
		foreach ( $this->get_sections() as $section_id => $section ) {
			add_settings_section( $section_id, $section['section_title'], ! empty( $section['section_callback'] ) ? $section['section_callback'] : null, 'anr_admin_options' );
		}
		foreach ( $this->get_fields() as $field_id => $field ) {
			$args = wp_parse_args(
				$field, array(
					'id'         => $field_id,
					'label'      => '',
					'cb_label'   => '',
					'type'       => 'text',
					'class'      => 'regular-text',
					'section_id' => '',
					'desc'       => '',
					'std'        => '',
				)
			);
			add_settings_field( $args['id'], $args['label'], ! empty( $args['callback'] ) ? $args['callback'] : array( $this, 'callback' ), 'anr_admin_options', $args['section_id'], $args );
		}
	}

	function get_sections() {
		$sections = array(
			'google_keys' => array(
				'section_title'    => __( 'Google Keys', 'advanced-nocaptcha-recaptcha' ),
				'section_callback' => function() {
					printf( __( 'Get reCaptcha keys from <a href="%s">Google</a>. Make sure to get keys for your selected captcha version.', 'advanced-nocaptcha-recaptcha' ), 'https://www.google.com/recaptcha/admin' );
				},
			),
			'forms'       => array(
				'section_title' => __( 'Forms', 'advanced-nocaptcha-recaptcha' ),
			),
			'other'       => array(
				'section_title' => __( 'Other Settings', 'advanced-nocaptcha-recaptcha' ),
			),
		);
		return apply_filters( 'anr_settings_sections', $sections );
	}

	function get_fields() {
		$score_values = [];
		for ( $i = 0.0; $i <= 1; $i += 0.1 ) {
			$score_values[ "$i" ] = number_format_i18n( $i, 1 );
		}
		$fields = array(
			'captcha_version'            => array(
				'label'      => __( 'Version', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'google_keys',
				'type'       => 'select',
				'class'      => 'regular',
				'std'        => 'v2_checkbox',
				'options'    => array(
					'v2_checkbox'  => __( 'V2 "I\'m not a robot"', 'advanced-nocaptcha-recaptcha' ),
					'v2_invisible' => __( 'V2 Invisible', 'advanced-nocaptcha-recaptcha' ),
					'v3'           => __( 'V3', 'advanced-nocaptcha-recaptcha' ),
				),
				'desc'       => __( 'Select your reCaptcha version. Make sure to use site key and secret key for your selected version.', 'advanced-nocaptcha-recaptcha' ),
			),
			'site_key'           => array(
				'label'      => __( 'Site Key', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'google_keys',
			),
			'secret_key'         => array(
				'label'      => __( 'Secret Key', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'google_keys',
			),
			'enabled_forms'      => array(
				'label'      => __( 'Enabled Forms', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'forms',
				'type'       => 'multicheck',
				'class'      => 'checkbox',
				'options'    => array(
					'login'          => __( 'Login Form', 'advanced-nocaptcha-recaptcha' ),
					'registration'   => __( 'Registration Form', 'advanced-nocaptcha-recaptcha' ),
					'ms_user_signup' => __( 'Multisite User Signup Form', 'advanced-nocaptcha-recaptcha' ),
					'lost_password'  => __( 'Lost Password Form', 'advanced-nocaptcha-recaptcha' ),
					'reset_password' => __( 'Reset Password Form', 'advanced-nocaptcha-recaptcha' ),
					'comment'        => __( 'Comment Form', 'advanced-nocaptcha-recaptcha' ),
					'bbp_new'         => __( 'bbPress New topic', 'advanced-nocaptcha-recaptcha' ),
					'bbp_reply'       => __( 'bbPress reply to topic', 'advanced-nocaptcha-recaptcha' ),
					'bp_register'       => __( 'BuddyPress register', 'advanced-nocaptcha-recaptcha' ),
					'wc_checkout'    => __( 'WooCommerce Checkout', 'advanced-nocaptcha-recaptcha' ),
				),
				'desc'       => sprintf( __( 'For other forms see <a href="%s">Instruction</a>', 'advanced-nocaptcha-recaptcha' ), esc_url( network_admin_url( 'admin.php?page=anr-instruction' ) ) ),
			),
			'error_message'      => array(
				'label'      => __( 'Error Message', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'std'        => __( 'Please solve Captcha correctly', 'advanced-nocaptcha-recaptcha' ),
			),
			'language'           => array(
				'label'      => __( 'Captcha Language', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular',
				'options'    => array(
					''       => __( 'Auto Detect', 'advanced-nocaptcha-recaptcha' ),
					'ar'     => __( 'Arabic', 'advanced-nocaptcha-recaptcha' ),
					'bg'     => __( 'Bulgarian', 'advanced-nocaptcha-recaptcha' ),
					'ca'     => __( 'Catalan', 'advanced-nocaptcha-recaptcha' ),
					'zh-CN'  => __( 'Chinese (Simplified)', 'advanced-nocaptcha-recaptcha' ),
					'zh-CN'  => __( 'Chinese (Traditional)', 'advanced-nocaptcha-recaptcha' ),
					'hr'     => __( 'Croatian', 'advanced-nocaptcha-recaptcha' ),
					'cs'     => __( 'Czech', 'advanced-nocaptcha-recaptcha' ),
					'da'     => __( 'Danish', 'advanced-nocaptcha-recaptcha' ),
					'nl'     => __( 'Dutch', 'advanced-nocaptcha-recaptcha' ),
					'en-GB'  => __( 'English (UK)', 'advanced-nocaptcha-recaptcha' ),
					'en'     => __( 'English (US)', 'advanced-nocaptcha-recaptcha' ),
					'fil'    => __( 'Filipino', 'advanced-nocaptcha-recaptcha' ),
					'fi'     => __( 'Finnish', 'advanced-nocaptcha-recaptcha' ),
					'fr'     => __( 'French', 'advanced-nocaptcha-recaptcha' ),
					'fr-CA'  => __( 'French (Canadian)', 'advanced-nocaptcha-recaptcha' ),
					'de'     => __( 'German', 'advanced-nocaptcha-recaptcha' ),
					'de-AT'  => __( 'German (Austria)', 'advanced-nocaptcha-recaptcha' ),
					'de-CH'  => __( 'German (Switzerland)', 'advanced-nocaptcha-recaptcha' ),
					'el'     => __( 'Greek', 'advanced-nocaptcha-recaptcha' ),
					'iw'     => __( 'Hebrew', 'advanced-nocaptcha-recaptcha' ),
					'hi'     => __( 'Hindi', 'advanced-nocaptcha-recaptcha' ),
					'hu'     => __( 'Hungarain', 'advanced-nocaptcha-recaptcha' ),
					'id'     => __( 'Indonesian', 'advanced-nocaptcha-recaptcha' ),
					'it'     => __( 'Italian', 'advanced-nocaptcha-recaptcha' ),
					'ja'     => __( 'Japanese', 'advanced-nocaptcha-recaptcha' ),
					'ko'     => __( 'Korean', 'advanced-nocaptcha-recaptcha' ),
					'lv'     => __( 'Latvian', 'advanced-nocaptcha-recaptcha' ),
					'lt'     => __( 'Lithuanian', 'advanced-nocaptcha-recaptcha' ),
					'no'     => __( 'Norwegian', 'advanced-nocaptcha-recaptcha' ),
					'fa'     => __( 'Persian', 'advanced-nocaptcha-recaptcha' ),
					'pl'     => __( 'Polish', 'advanced-nocaptcha-recaptcha' ),
					'pt'     => __( 'Portuguese', 'advanced-nocaptcha-recaptcha' ),
					'pt-BR'  => __( 'Portuguese (Brazil)', 'advanced-nocaptcha-recaptcha' ),
					'pt-PT'  => __( 'Portuguese (Portugal)', 'advanced-nocaptcha-recaptcha' ),
					'ro'     => __( 'Romanian', 'advanced-nocaptcha-recaptcha' ),
					'ru'     => __( 'Russian', 'advanced-nocaptcha-recaptcha' ),
					'sr'     => __( 'Serbian', 'advanced-nocaptcha-recaptcha' ),
					'sk'     => __( 'Slovak', 'advanced-nocaptcha-recaptcha' ),
					'sl'     => __( 'Slovenian', 'advanced-nocaptcha-recaptcha' ),
					'es'     => __( 'Spanish', 'advanced-nocaptcha-recaptcha' ),
					'es-419' => __( 'Spanish (Latin America)', 'advanced-nocaptcha-recaptcha' ),
					'sv'     => __( 'Swedish', 'advanced-nocaptcha-recaptcha' ),
					'th'     => __( 'Thai', 'advanced-nocaptcha-recaptcha' ),
					'tr'     => __( 'Turkish', 'advanced-nocaptcha-recaptcha' ),
					'uk'     => __( 'Ukrainian', 'advanced-nocaptcha-recaptcha' ),
					'vi'     => __( 'Vietnamese', 'advanced-nocaptcha-recaptcha' ),
				),
			),
			'theme'              => array(
				'label'      => __( 'Theme', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular hidden anr-show-field-for-v2_checkbox anr-show-field-for-v2_invisible',
				'std'        => 'light',
				'options'    => array(
					'light' => __( 'Light', 'advanced-nocaptcha-recaptcha' ),
					'dark'  => __( 'Dark', 'advanced-nocaptcha-recaptcha' ),
				),
			),
			'size'               => array(
				'label'      => __( 'Size', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular hidden anr-show-field-for-v2_checkbox',
				'std'        => 'normal',
				'options'    => array(
					'normal'    => __( 'Normal', 'advanced-nocaptcha-recaptcha' ),
					'compact'   => __( 'Compact', 'advanced-nocaptcha-recaptcha' ),
				),
			),
			'badge'              => array(
				'label'      => __( 'Badge', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular hidden anr-show-field-for-v2_invisible',
				'std'        => 'bottomright',
				'options'    => array(
					'bottomright' => __( 'Bottom Right', 'advanced-nocaptcha-recaptcha' ),
					'bottomleft'  => __( 'Bottom Left', 'advanced-nocaptcha-recaptcha' ),
					'inline'      => __( 'Inline', 'advanced-nocaptcha-recaptcha' ),
				),
				'desc'       => __( 'Badge shows for invisible captcha', 'advanced-nocaptcha-recaptcha' ),
			),
			'failed_login_allow' => array(
				'label'             => __( 'Failed login Captcha', 'advanced-nocaptcha-recaptcha' ),
				'section_id'        => 'other',
				'std'               => 0,
				'type'              => 'number',
				'class'             => 'regular-number',
				'sanitize_callback' => 'absint',
				'desc'              => __( 'Show login Captcha after how many failed attempts? 0 = show always', 'advanced-nocaptcha-recaptcha' ),
			),
			'v3_script_load'     => array(
				'label'      => __( 'v3 Script Load', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular hidden anr-show-field-for-v3',
				'std'        => 'all_pages',
				'options'    => array(
					'all_pages'  => __( 'All Pages', 'advanced-nocaptcha-recaptcha' ),
					'form_pages' => __( 'Form Pages', 'advanced-nocaptcha-recaptcha' ),
				),
				'desc'       => __( 'Loading in All Pages help google for analytics', 'advanced-nocaptcha-recaptcha' ),
			),
			'score'              => array(
				'label'      => __( 'Captcha Score', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular hidden anr-show-field-for-v3',
				'std'        => '0.5',
				'options'    => $score_values,
				'desc'       => __( 'Higher means more sensitive', 'advanced-nocaptcha-recaptcha' ),
			),
			'whitelisted_ips'              => array(
				'label'      => __( 'Whitelisted IPs', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'textarea',
				'class'      => 'regular',
				'desc'       => __( 'One per line', 'advanced-nocaptcha-recaptcha' ),
			),
			'recaptcha_domain'      => array(
				'label'      => __( 'Recaptcha Domain', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'select',
				'class'      => 'regular',
				'std'        => anr_recaptcha_domain(),
				'options'    => array(
					'google.com'   => 'google.com',
					'google.net'   => 'google.net',
				),
				'desc'        => __( 'If any one is blocked in your country select other one.', 'advanced-nocaptcha-recaptcha' ),
			),
			'loggedin_hide'      => array(
				'label'      => __( 'Logged in Hide', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'checkbox',
				'class'      => 'checkbox',
				'cb_label'   => __( 'Hide Captcha for logged in users?', 'advanced-nocaptcha-recaptcha' ),
			),
			'remove_css'         => array(
				'label'      => __( 'Remove CSS', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'checkbox',
				'class'      => 'checkbox hidden anr-show-field-for-v2_checkbox',
				'cb_label'   => __( "Remove this plugin's css from login page?", 'advanced-nocaptcha-recaptcha' ),
				'desc'       => __( 'This css increase login page width to adjust with Captcha width.', 'advanced-nocaptcha-recaptcha' ),
			),
			'no_js'              => array(
				'label'      => __( 'No JS Captcha', 'advanced-nocaptcha-recaptcha' ),
				'section_id' => 'other',
				'type'       => 'checkbox',
				'class'      => 'checkbox hidden anr-show-field-for-v2_checkbox',
				'cb_label'   => __( 'Show captcha if javascript disabled?', 'advanced-nocaptcha-recaptcha' ),
				'desc'       => __( 'If JavaScript is a requirement for your site, we advise that you do NOT check this.', 'advanced-nocaptcha-recaptcha' ),
			),
		);
		if ( ! class_exists( 'ANR_Pro' ) ) :
			$fields['pro_notice'] = array(
				'section_id' => 'forms',
				'type'       => 'html',
				'std'        => sprintf( '<div class="notice notice-success inline">
					<p>To support development of "Advanced noCaptcha & invisible Captcha" plugin please purchase PRO version. <a class="button button-secondary" href="%1$s">View Details</a></p>
				</div>', function_exists( 'anr_fs' ) ? anr_fs()->get_upgrade_url() : 'https://www.shamimsplugins.com/products/advanced-nocaptcha-and-invisible-captcha-pro/' ),
			);
		endif;
		
		return apply_filters( 'anr_settings_fields', $fields );
	}

	function callback( $field ) {
		$attrib = '';
		if ( ! empty( $field['required'] ) ) {
			$attrib .= ' required = "required"';
		}
		if ( ! empty( $field['readonly'] ) ) {
			$attrib .= ' readonly = "readonly"';
		}
		if ( ! empty( $field['disabled'] ) ) {
			$attrib .= ' disabled = "disabled"';
		}
		if ( ! empty( $field['minlength'] ) ) {
			$attrib .= ' minlength = "' . absint( $field['minlength'] ) . '"';
		}
		if ( ! empty( $field['maxlength'] ) ) {
			$attrib .= ' maxlength = "' . absint( $field['maxlength'] ) . '"';
		}

		$value = anr_get_option( $field['id'], $field['std'] );

		switch ( $field['type'] ) {
			case 'text':
			case 'email':
			case 'url':
			case 'number':
			case 'hidden':
			case 'submit':
				printf(
					'<input type="%1$s" id="anr_admin_options_%2$s" class="%3$s" name="anr_admin_options[%4$s]" placeholder="%5$s" value="%6$s"%7$s />',
					esc_attr( $field['type'] ),
					esc_attr( $field['id'] ),
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '',
					esc_attr( $value ),
					$attrib
				);
				break;
			case 'textarea':
					printf( '<textarea id="anr_admin_options_%1$s" class="%2$s" name="anr_admin_options[%3$s]" placeholder="%4$s" %5$s >%6$s</textarea>',
						esc_attr( $field['id'] ),
						esc_attr( $field['class'] ),
						esc_attr( $field['id'] ),
						isset( $field['placeholder'] ) ? esc_attr( $field['placeholder'] ) : '',
						$attrib,
						esc_textarea( $value )
					);
					break;
			case 'checkbox':
				printf( '<input type="hidden" name="anr_admin_options[%s]" value="" />', esc_attr( $field['id'] ) );
				printf(
					'<label><input type="%1$s" id="anr_admin_options_%2$s" class="%3$s" name="anr_admin_options[%4$s]" value="%5$s"%6$s /> %7$s</label>',
					'checkbox',
					esc_attr( $field['id'] ),
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] ),
					'1',
					checked( $value, '1', false ),
					esc_attr( $field['cb_label'] )
				);
				break;
			case 'multicheck':
				printf( '<input type="hidden" name="anr_admin_options[%s][]" value="" />', esc_attr( $field['id'] ) );
				foreach ( $field['options'] as $key => $label ) {
					printf(
						'<label><input type="%1$s" id="anr_admin_options_%2$s_%5$s" class="%3$s" name="anr_admin_options[%4$s][]" value="%5$s"%6$s /> %7$s</label><br>',
						'checkbox',
						esc_attr( $field['id'] ),
						esc_attr( $field['class'] ),
						esc_attr( $field['id'] ),
						esc_attr( $key ),
						checked( in_array( $key, (array) $value ), true, false ),
						esc_attr( $label )
					);
				}
				break;
			case 'select':
				printf(
					'<select id="anr_admin_options_%1$s" class="%2$s" name="anr_admin_options[%1$s]">',
					esc_attr( $field['id'] ),
					esc_attr( $field['class'] ),
					esc_attr( $field['id'] )
				);
				foreach ( $field['options'] as $key => $label ) {
					printf(
						'<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $key ),
						selected( $value, $key, false ),
						esc_attr( $label )
					);
				}
				printf( '</select>' );
				break;
			case 'html':
				echo $field['std'];
				break;

			default:
				printf( __( 'No hook defined for %s', 'advanced-nocaptcha-recaptcha' ), esc_html( $field['type'] ) );
				break;
		}
		if ( ! empty( $field['desc'] ) ) {
			printf( '<p class="description">%s</p>', $field['desc'] );
		}
	}

	function options_sanitize( $value ) {
		if ( ! $value || ! is_array( $value ) ) {
			return $value;
		}
		$fields = $this->get_fields();

		foreach ( $value as $option_slug => $option_value ) {
			if ( isset( $fields[ $option_slug ] ) && ! empty( $fields[ $option_slug ]['sanitize_callback'] ) ) {
				$value[ $option_slug ] = call_user_func( $fields[ $option_slug ]['sanitize_callback'], $option_value );
			} elseif ( isset( $fields[ $option_slug ] ) ) {
				$value[ $option_slug ] = $this->posted_value_sanitize( $option_value, $fields[ $option_slug ] );
			}
		}
		return $value;
	}

	function posted_value_sanitize( $value, $field ) {
		$sanitized = $value;
			switch ( $field['type'] ) {
				case 'text':
				case 'hidden':
				$sanitized = sanitize_text_field( trim( $value ) );
				break;
			case 'url':
				$sanitized = esc_url( $value );
				break;
			case 'number':
				$sanitized = absint( $value );
				break;
			case 'textarea':
			case 'wp_editor':
			case 'teeny':
				$sanitized = wp_kses_post( $value );
				break;
			case 'checkbox':
				$sanitized = absint( $value );
				break;
			case 'multicheck':
				$sanitized = is_array( $value ) ? array_filter( $value ) : array();
				foreach( $sanitized as $key => $p_value ) {
					if ( ! array_key_exists( $p_value, $field['options'] ) ) {
						unset( $sanitized[ $key ] );
					}
				}
				break;
			case 'select':
				if ( ! array_key_exists( $value, $field['options'] ) ) {
					$sanitized = isset($field['std'])? $field['std'] : '';
				}
				break;
			default:
				$sanitized = apply_filters( 'anr_settings_field_sanitize_filter_' . $field['type'], $value, $field );
				break;
		}
		return apply_filters( 'anr_settings_field_sanitize_filter', $sanitized, $field, $value );
	}

	function menu_page() {
		add_options_page( __( 'Advanced noCaptcha & invisible captcha Settings', 'advanced-nocaptcha-recaptcha' ), __( 'Advanced noCaptcha & invisible captcha', 'advanced-nocaptcha-recaptcha' ), 'manage_options', 'anr-admin-settings', array( $this, 'admin_settings' ) );
		add_submenu_page( 'anr-non-exist-menu', 'Advanced noCaptcha reCaptcha - ' . __( 'Instruction', 'advanced-nocaptcha-recaptcha' ), __( 'Instruction', 'advanced-nocaptcha-recaptcha' ), 'manage_options', 'anr-instruction', array( $this, 'instruction_page' ) );

	}

	function network_menu_page() {
		add_submenu_page( 'settings.php', __( 'Advanced noCaptcha & invisible captcha Settings', 'advanced-nocaptcha-recaptcha' ), __( 'Advanced noCaptcha & invisible captcha', 'advanced-nocaptcha-recaptcha' ), 'manage_network_options', 'anr-admin-settings', array( $this, 'admin_settings' ) );
		add_submenu_page( 'anr-non-exist-menu', 'Advanced noCaptcha reCaptcha - ' . __( 'Instruction', 'advanced-nocaptcha-recaptcha' ), __( 'Instruction', 'advanced-nocaptcha-recaptcha' ), 'manage_network_options', 'anr-instruction', array( $this, 'instruction_page' ) );

	}
	
	function settings_save() {
		if ( current_user_can( 'manage_options' ) && isset( $_POST['anr_admin_options'] ) && isset( $_POST['action'] ) && 'update' === $_POST['action'] && isset( $_GET['page'] ) && 'anr-admin-settings' === $_GET['page'] ) {
			check_admin_referer( 'anr_admin_options-options' );

			$value = wp_unslash( $_POST['anr_admin_options'] );
			if ( ! is_array( $value ) ) {
				$value = [];
			}
			anr_update_option( $value );
			
			wp_safe_redirect( add_query_arg( 'updated', true ) );
			exit;
		}
	}

	function admin_settings() {
		wp_enqueue_script( 'anr-admin' );
		?>
		<div class="wrap">
			<div id="poststuff">
				<h2><?php _e( 'Advanced noCaptcha & invisible captcha Settings', 'advanced-nocaptcha-recaptcha' ); ?></h2>
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div id="tab_container">
							<?php settings_errors( 'anr_admin_options' ); ?>
							<form method="post" action="">
								<?php
								settings_fields( 'anr_admin_options' );
								do_settings_sections( 'anr_admin_options' );
								do_action( 'anr_admin_setting_form' );
								submit_button();
								?>
							</form>
						</div><!-- #tab_container-->
					</div><!-- #post-body-content-->
					<div id="postbox-container-1" class="postbox-container">
						<?php echo $this->anr_admin_sidebar(); ?>
					</div><!-- #postbox-container-1 -->
				</div><!-- #post-body -->
				<br class="clear" />
			</div><!-- #poststuff -->
		</div><!-- .wrap -->
		<?php
	}

	function anr_admin_sidebar() {
			$return = '<div class="postbox">
					<h3 class="hndle" style="text-align: center;">
						<span>' . __( 'Plugin Author', 'advanced-nocaptcha-recaptcha' ) . '</span>
					</h3>

					<div class="inside">
						<div style="text-align: center; margin: auto">
						<strong>Shamim Hasan</strong><br />
						Know php, MySql, css, javascript, html. Expert in WordPress. <br /><br />
								
						You can hire for plugin customization, build custom plugin or any kind of WordPress job via <br> <a
								href="https://www.shamimsplugins.com/contact-us/"><strong>Contact Form</strong></a>
					</div>
				</div>
			</div>';
			if ( ! class_exists( 'ANR_Pro' ) ) :
			$return .= sprintf( '<div class="postbox">
					<h3 class="hndle" style="text-align: center;">
						<span>' . __( 'Support Development', 'advanced-nocaptcha-recaptcha' ) . '</span>
					</h3>

					<div class="inside">
						<div style="text-align: center; margin: auto">
						<a style="text-decoration:none;" href="%1$s">To support development of "Advanced noCaptcha & invisible Captcha" plugin please purchase
						<div style="font-size:24px;color:red;margin:10px;">PRO</div>
						version only for USD 10</a>
						<p><a class="button button-secondary" href="%1$s">View Details</a></p>
					</div>
				</div>
			</div>', function_exists( 'anr_fs' ) ? anr_fs()->get_upgrade_url() : 'https://www.shamimsplugins.com/products/advanced-nocaptcha-and-invisible-captcha-pro/' );
		endif;
		return $return;
	}

	function instruction_page() {
		?>
		<div class="wrap">
			<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
					<h2><?php _e( 'Advanced noCaptcha reCaptcha Setup Instruction', 'advanced-nocaptcha-recaptcha' ); ?></h2>
					<!-- main content -->
					<div id="post-body-content">
						<div class='postbox'>
							<div class='inside'>
								<div><?php printf( __( 'Get your site key and secret key from <a href="%s" target="_blank">GOOGLE</a> if you do not have already.', 'advanced-nocaptcha-recaptcha' ), esc_url( 'https://www.google.com/recaptcha/admin' ) ); ?></div>
								<div><?php printf( __( 'Goto %s page of this plugin and set up as you need. and ENJOY...', 'advanced-nocaptcha-recaptcha' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=anr-admin-settings' ) ) . '">' . esc_html__( 'Settings', 'advanced-nocaptcha-recaptcha' ) . '</a>' ); ?></div>
				
								<h3><?php _e( 'Implement noCaptcha in Contact Form 7', 'advanced-nocaptcha-recaptcha' ); ?></h3>
								<div><?php printf( __( 'To show noCaptcha use %s', 'advanced-nocaptcha-recaptcha' ), '<code>[anr_nocaptcha g-recaptcha-response]</code>' ); ?></div>
				
								<h3><?php _e( 'Implement noCaptcha in WooCommerce', 'advanced-nocaptcha-recaptcha' ); ?></h3>
								<div><?php _e( 'If Login Form, Registration Form, Lost Password Form, Reset Password Form is selected in SETTINGS page of this plugin they will show and verify Captcha in WooCommerce respective forms also.', 'advanced-nocaptcha-recaptcha' ); ?></div>
								
								<h3><?php _e( 'If you want to implement noCaptcha in any other custom form', 'advanced-nocaptcha-recaptcha' ); ?></h3>
								<div><?php printf( __( 'To show noCaptcha in a form use %1$s OR %2$s', 'advanced-nocaptcha-recaptcha' ), "<code>do_action( 'anr_captcha_form_field' )</code>", '<code>[anr-captcha]</code>' ); ?></div>
								<div><?php printf( __( 'To verify use %s. It will return true on success otherwise false.', 'advanced-nocaptcha-recaptcha' ), '<code>anr_verify_captcha()</code>' ); ?></div>
								<div><?php printf( __( 'For paid support pleasse visit <a href="%s" target="_blank">Advanced noCaptcha reCaptcha</a>', 'advanced-nocaptcha-recaptcha' ), esc_url( 'https://www.shamimsplugins.com/hire/' ) ); ?></div>
							</div>
						</div>
						<div><a class="button" href="<?php echo esc_url( admin_url( 'options-general.php?page=anr-admin-settings' ) ); ?>"><?php esc_html_e( 'Back to Settings', 'advanced-nocaptcha-recaptcha' ); ?></a></div>
					</div>
					<div id="postbox-container-1" class="postbox-container">
						<?php echo $this->anr_admin_sidebar(); ?>
					</div>
				</div>
				<br class="clear" />
			</div>
		</div>
		<?php
	}


	function add_settings_link( $links ) {
		// add settings link in plugins page
		$url = anr_same_settings_for_all_sites() ? network_admin_url( 'settings.php?page=anr-admin-settings' ) : admin_url( 'options-general.php?page=anr-admin-settings' );
		$settings_link = '<a href="' . $url . '">' . __( 'Settings', 'advanced-nocaptcha-recaptcha' ) . '</a>';
		array_unshift( $links, $settings_link );
		return $links;
	}


} //END CLASS

add_action( 'wp_loaded', array( ANR_Settings::init(), 'actions_filters' ) );
