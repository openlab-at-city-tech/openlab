<?php

class MC4WP_Google_Recaptcha {

	private $form_ids = array();

	public function add_hooks() {
		add_filter( 'mc4wp_form_settings', array( $this, 'add_default_form_settings' ) );
		add_filter( 'mc4wp_settings', array( $this, 'add_default_settings' ) );
		add_action( 'mc4wp_output_form', array( $this, 'on_output_form' ), 20 );
		add_filter( 'mc4wp_form_errors', array( $this, 'verify_token' ), 10, 2 );
		add_action( 'mc4wp_admin_form_after_behaviour_settings_rows', array( $this, 'show_settings' ), 30, 2 );
		add_filter( 'mc4wp_form_sanitized_data', array( $this, 'sanitize_settings' ), 20, 2 );
		add_action( 'wp_footer', array( $this, 'load_script' ), 8000 );
	}


	public function add_default_settings( $settings ) {
		$defaults = array(
			'grecaptcha_site_key'   => '',
			'grecaptcha_secret_key' => '',
		);
		$settings = array_merge( $defaults, $settings );
		return $settings;
	}

	public function add_default_form_settings( $settings ) {
		$defaults = array(
			'grecaptcha_enabled' => 0,
		);
		$settings = array_merge( $defaults, $settings );
		return $settings;
	}

	public function sanitize_settings( $data, $raw_data ) {
		if ( ! isset( $data['settings']['grecaptcha_enabled'] ) || ! $data['settings']['grecaptcha_enabled'] ) {
			return $data;
		}

		// only enable grecaptcha if both site & secret key are set
		$global_settings                        = mc4wp_get_settings();
		$data['settings']['grecaptcha_enabled'] = isset( $global_settings['grecaptcha_site_key'] )
			&& isset( $global_settings['grecaptcha_secret_key'] )
			&& strlen( $global_settings['grecaptcha_site_key'] ) === 40
			&& strlen( $global_settings['grecaptcha_secret_key'] ) === 40 ? '1' : '0';
		return $data;
	}

	public function load_script() {
		$global_settings = mc4wp_get_settings();

		// do not load if no forms with Google reCAPTCHA enabled were outputted
		if ( empty( $this->form_ids ) || empty( $global_settings['grecaptcha_site_key'] ) || empty( $global_settings['grecaptcha_secret_key'] ) ) {
			return;
		}

		// load Google reCAPTCHA script
		echo sprintf( '<script src="https://www.google.com/recaptcha/api.js?render=%s"></script>', esc_attr( $global_settings['grecaptcha_site_key'] ) );

		// hook into form submit
		?><script>
			(function() {
				var formIds = <?php echo json_encode( $this->form_ids ); ?>;

				function addGoogleReCaptchaTokenToForm(form, event) {
					event.preventDefault();

					var submitForm = function() {
						if(form.element.className.indexOf('mc4wp-ajax') > -1) {
							mc4wp.forms.trigger('submit', [form, event]);
						} else {
							form.element.submit();
						}
					};
					var previousToken = form.element.querySelector('input[name=_mc4wp_grecaptcha_token]');
					if (previousToken) {
						previousToken.parentElement.removeChild(previousToken);
					}

					window.grecaptcha
						.execute('<?php echo esc_attr( $global_settings['grecaptcha_site_key'] ); ?>', {action: 'mc4wp_form_submit'})
						.then(function (token) {
							var tokenEl = document.createElement('input');
							tokenEl.type = 'hidden';
							tokenEl.value = token;
							tokenEl.name = '_mc4wp_grecaptcha_token';
							form.element.appendChild(tokenEl);
							submitForm();
						})
				}

				for(var i=0; i<formIds.length; i++) {
					mc4wp.forms.on(formIds[i]+'.submit', addGoogleReCaptchaTokenToForm)
				}
			})();
		</script>
		<?php
	}

	public function on_output_form( MC4WP_Form $form ) {
		// Check if form has Google ReCaptcha enabled
		if ( ! $form->settings['grecaptcha_enabled'] ) {
			return;
		}

		if ( ! in_array( $form->ID, $this->form_ids ) ) {
			$this->form_ids[] = $form->ID;
		}
	}

	public function verify_token( array $errors, MC4WP_Form $form ) {
		// Check if form has Google ReCaptcha enabled
		if ( ! $form->settings['grecaptcha_enabled'] ) {
			return $errors;
		}

		// Verify token
		if ( empty( $_POST['_mc4wp_grecaptcha_token'] ) ) {
			$errors[] = 'spam';
			return $errors;
		}

		$global_settings = mc4wp_get_settings();
		$token           = $_POST['_mc4wp_grecaptcha_token'];
		$response        = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'body' => array(
					'secret'   => $global_settings['grecaptcha_secret_key'],
					'response' => $token,
				),
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( $response_code >= 400 ) {
			// The request somehow failed... Allow the sign-up to go through to not break sign-up forms when Google reCaptcha is down (unlikely)
			return $errors;
		}

		$response_body  = wp_remote_retrieve_body( $response );
		$data           = json_decode( $response_body, true );
		$score_treshold = apply_filters( 'mc4wp_grecaptcha_score_treshold', 0.5 );

		if ( isset( $data['error-codes'] ) && in_array( 'invalid-input-secret', $data['error-codes'], true ) ) {
			$this->get_log()->warning( sprintf( 'Form %d > Invalid Google reCAPTCHA secret key', $form->ID ) );
			return $errors;
		}

		if ( $data['success'] === false || ! isset( $data['score'] ) || $data['score'] <= $score_treshold || $data['action'] !== 'mc4wp_form_submit' ) {
			$errors[] = 'spam';
			return $errors;
		}

		return $errors;
	}

	public function show_settings( array $settings, MC4WP_Form $form ) {
		// only show settings if Google reCAPTCHA is enabled, as we plan on removing this feature in a future version of the plugin
		if ( ! $settings['grecaptcha_enabled'] ) {
			return;
		}

		$global_settings = mc4wp_get_settings();
		?>
		<tr valign="top">
			<th scope="row"><?php _e( 'Enable Google reCaptcha', 'mailchimp-for-wp' ); ?></th>
			<td>
				<label><input type="radio" name="mc4wp_form[settings][grecaptcha_enabled]" value="1" <?php checked( $settings['grecaptcha_enabled'], 1 ); ?> /> <?php _e( 'Yes', 'mailchimp-for-wp' ); ?> &rlm;</label>
				 &nbsp;
				<label><input type="radio" name="mc4wp_form[settings][grecaptcha_enabled]" value="0" <?php checked( $settings['grecaptcha_enabled'], 0 ); ?> /> <?php _e( 'No', 'mailchimp-for-wp' ); ?> &rlm;</label>
				<p class="description">
					<?php _e( 'Select "yes" to enable Google reCAPTCHA spam protection for this form.', 'mailchimp-for-wp' ); ?>
				</p>
			</td>
		</tr>
		<?php
		$config = array(
			'element' => 'mc4wp_form[settings][grecaptcha_enabled]',
			'value'   => 1,
		);
		?>
		<tr valign="top" data-showif="<?php echo esc_attr( json_encode( $config ) ); ?>">
			<th scope="row"><label for="mc4wp_grecaptcha_site_key"><?php _e( 'Google reCAPTCHA Site Key', 'mailchimp-for-wp' ); ?></label></th>
			<td>
				<input type="text" class="widefat" name="mc4wp[grecaptcha_site_key]" id="mc4wp_grecaptcha_site_key" placeholder="<?php echo str_repeat( '●', 40 ); ?>" value="<?php echo esc_attr( $global_settings['grecaptcha_site_key'] ); ?>" />
				<p class="description">
					<?php printf( __( 'Enter your Google reCAPTCHA keys here. You can <a href="%1$s">retrieve your keys in the Google reCAPTCHA admin console</a> or read our help article on <a href="%2$s">how to configure Google reCAPTCHA</a>.', 'mailchimp-for-wp' ), 'https://g.co/recaptcha/v3', 'https://www.mc4wp.com/kb/google-recaptcha-forms/' ); ?>
				</p>
			</td>
		</tr>
		<?php
		$config = array(
			'element' => 'mc4wp_form[settings][grecaptcha_enabled]',
			'value'   => 1,
		);
		?>
		<tr valign="top" data-showif="<?php echo esc_attr( json_encode( $config ) ); ?>">
			<th scope="row"><label for="mc4wp_grecaptcha_secret_key"><?php _e( 'Google reCAPTCHA Secret Key', 'mailchimp-for-wp' ); ?></label></th>
			<td>
				<input type="text" class="widefat" name="mc4wp[grecaptcha_secret_key]" id="mc4wp_grecaptcha_secret_key" placeholder="<?php echo str_repeat( '●', 40 ); ?>" value="<?php echo esc_attr( $global_settings['grecaptcha_secret_key'] ); ?>" />
			</td>
		</tr>
		<?php
	}

	/**
	 * @return MC4WP_Debug_Log
	 */
	private function get_log() {
		return mc4wp( 'log' );
	}
}
