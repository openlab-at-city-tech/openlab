<?php

if ( ! class_exists( 'anr_captcha_class' ) ) {
	class anr_captcha_class {

		private static $instance;

		private static $captcha_count = 0;

		public static function init() {
			if ( ! self::$instance instanceof self ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		function actions_filters() {
			if ( anr_is_form_enabled( 'fep_contact_form' ) ) {
				add_action( 'fepcf_message_form_after_content', array( $this, 'form_field' ), 99 );
				add_action( 'fepcf_action_message_before_send', array( $this, 'fepcf_verify' ) );
			}

			if ( anr_is_form_enabled( 'login' ) && ! defined( 'XMLRPC_REQUEST' ) ) {
				add_action( 'login_form', array( $this, 'login_form_field' ), 99 );
				add_filter( 'login_form_middle', array( $this, 'login_form_return' ), 99 );
				add_action( 'um_after_login_fields', array( $this, 'login_form_field' ), 99 );
				add_action( 'woocommerce_login_form', array( $this, 'login_form_field' ), 99 );
				add_filter( 'authenticate', array( $this, 'login_verify' ), 999, 3 );

				add_action( 'wp_login', array( $this, 'clear_data' ), 10, 2 );
			}

			if ( anr_is_form_enabled( 'wc_checkout' ) ) {
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'wc_checkout_verify' ), 10, 2 );
				add_action( 'woocommerce_checkout_after_order_review', array( $this, 'wc_form_field' ) );
			}

			if ( anr_is_form_enabled( 'registration' ) ) {
				add_action( 'register_form', array( $this, 'form_field' ), 99 );
				add_action( 'woocommerce_register_form', array( $this, 'form_field' ), 99 );
				add_filter( 'registration_errors', array( $this, 'registration_verify' ), 10, 3 );
				add_filter( 'woocommerce_registration_errors', array( $this, 'wc_registration_verify' ), 10, 3 );
				// add_action ('woocommerce_checkout_after_order_review', array($this, 'wc_form_field') );
			}

			if ( anr_is_form_enabled( 'bp_register' ) ) {
				add_action( 'bp_before_registration_submit_buttons', array( $this, 'bp_form_field' ), 99 );
				add_action( 'bp_signup_validate', array( $this, 'bp_registration_verify' ) );
			}

			if ( anr_is_form_enabled( 'ms_user_signup' ) && is_multisite() ) {
				add_action( 'signup_extra_fields', array( $this, 'ms_form_field' ), 99 );
				add_filter( 'wpmu_validate_user_signup', array( $this, 'ms_form_field_verify' ) );

				add_action( 'signup_blogform', array( $this, 'ms_form_field' ), 99 );
				add_filter( 'wpmu_validate_blog_signup', array( $this, 'ms_blog_verify' ) );

			}

			if ( anr_is_form_enabled( 'lost_password' ) ) {
				add_action( 'lostpassword_form', array( $this, 'form_field' ), 99 );
				add_action( 'woocommerce_lostpassword_form', array( $this, 'form_field' ), 99 );
				// add_action ('allow_password_reset', array($this, 'lostpassword_verify'), 10, 2); //lostpassword_post does not return wp_error( prior WP 4.4 )
				add_action( 'lostpassword_post', array( $this, 'lostpassword_verify_44' ) );
			}

			if ( anr_is_form_enabled( 'reset_password' ) ) {
				add_action( 'resetpass_form', array( $this, 'form_field' ), 99 );
				add_action( 'woocommerce_resetpassword_form', array( $this, 'form_field' ), 99 );
				add_filter( 'validate_password_reset', array( $this, 'reset_password_verify' ), 10, 2 );
			}

			if ( anr_is_form_enabled( 'comment' ) && ( ! is_admin() || ! current_user_can( 'moderate_comments' ) ) ) {
				if ( ! is_user_logged_in() ) {
					add_action( 'comment_form_after_fields', array( $this, 'form_field' ), 99 );
				} else {
					add_filter( 'comment_form_field_comment', array( $this, 'form_field_return' ), 99 );
				}
				if ( version_compare( get_bloginfo( 'version' ), '4.9.0', '>=' ) ) {
					add_filter( 'pre_comment_approved', array( $this, 'comment_verify_490' ), 99 );
				} else {
					add_filter( 'preprocess_comment', array( $this, 'comment_verify' ) );
				}
			}

			if ( function_exists( 'wpcf7_add_form_tag' ) ) {
				if( class_exists( 'WPCF7' ) && WPCF7::get_option( 'recaptcha' ) && apply_filters( 'anr_remove_cf7_recaptcha', true ) ) {
					//remove recaptcha keys from CF7 which will disable CF7 recaptcha
					WPCF7::update_option( 'recaptcha', null );
				}
				if( is_user_logged_in() && anr_get_option( 'loggedin_hide' ) ) {
					//enable verify nonce otherwie is_user_logged_in() return false when validate recaptcha
					add_filter( 'wpcf7_verify_nonce', '__return_true' );
				}
				wpcf7_add_form_tag( 'anr_nocaptcha', array( $this, 'wpcf7_form_field' ), array( 'name-attr' => true ) );
				add_filter( 'wpcf7_validate_anr_nocaptcha', array( $this, 'wpcf7_verify' ), 10, 2 );
			} elseif ( function_exists( 'wpcf7_add_shortcode' ) ) {
				wpcf7_add_shortcode( 'anr_nocaptcha', array( $this, 'wpcf7_form_field' ), true );
				add_filter( 'wpcf7_validate_anr_nocaptcha', array( $this, 'wpcf7_verify' ), 10, 2 );
			}

			if ( anr_is_form_enabled( 'bbp_new' ) ) {
				add_action( 'bbp_theme_before_topic_form_submit_wrapper', array( $this, 'form_field' ), 99 );
				add_action( 'bbp_new_topic_pre_extras', array( $this, 'bbp_new_verify' ) );
			}

			if ( anr_is_form_enabled( 'bbp_reply' ) ) {
				add_action( 'bbp_theme_before_reply_form_submit_wrapper', array( $this, 'form_field' ), 99 );
				add_action( 'bbp_new_reply_pre_extras', array( $this, 'bbp_reply_verify' ), 10, 2 );
			}
		}

		function add_error_to_mgs( $mgs = false ) {
			if ( false === $mgs ) {
				$mgs = anr_get_option( 'error_message', '' );
			}
			return '<strong>' . __( 'ERROR', 'advanced-nocaptcha-recaptcha' ) . '</strong>: ' . $mgs;
		}

		function total_captcha() {
			return self::$captcha_count;
		}

		function captcha_form_field() {
			self::$captcha_count++;
			$no_js    = anr_get_option( 'no_js' );
			$site_key = trim( anr_get_option( 'site_key' ) );
			$number   = $this->total_captcha();
			$version = anr_get_option( 'captcha_version', 'v2_checkbox' );

			$field = '<div class="anr_captcha_field"><div id="anr_captcha_field_' . $number . '" class="anr_captcha_field_div">';
			if ( 'v3' === $version ) {
				$field .= '<input type="hidden" name="g-recaptcha-response" value="" />';
			}
			$field .= '</div></div>';

			if ( 1 == $no_js && 'v2_checkbox' === $version ) {
				$field .= sprintf( '<noscript>
						  <div>
							<div style="width: 302px; height: 422px; position: relative;">
							  <div style="width: 302px; height: 422px; position: absolute;">
								<iframe src="https://www.%s/recaptcha/api/fallback?k=' . $site_key . '"
										frameborder="0" scrolling="no"
										style="width: 302px; height:422px; border-style: none;">
								</iframe>
							  </div>
							</div>
							<div style="width: 300px; height: 60px; border-style: none;
										   bottom: 12px; left: 25px; margin: 0px; padding: 0px; right: 25px;
										   background: #f9f9f9; border: 1px solid #c1c1c1; border-radius: 3px;">
							  <textarea id="g-recaptcha-response-' . $number . '" name="g-recaptcha-response"
										   class="g-recaptcha-response"
										   style="width: 250px; height: 40px; border: 1px solid #c1c1c1;
												  margin: 10px 25px; padding: 0px; resize: none;" ></textarea>
							</div>
						  </div>
						</noscript>', anr_recaptcha_domain() );
			}

			return $field;
		}

		function footer_script() {
			static $included = false;

			$number          = $this->total_captcha();
			$version = anr_get_option( 'captcha_version', 'v2_checkbox' );

			if ( ! $number && ( 'v3' !== $version || 'all_pages' !== anr_get_option( 'v3_script_load', 'all_pages' ) ) ) {
				return;
			}

			if ( $included ) {
				return;
			}

			$included = true;

			if ( 'v2_checkbox' === $version ) {
				$this->v2_checkbox_script();
			} elseif ( 'v2_invisible' === $version ) {
				$this->v2_invisible_script();
			} elseif ( 'v3' === $version ) {
				$this->v3_script();
			}
		}

		function v2_checkbox_script() {
			?>
			<script type="text/javascript">
				var anr_onloadCallback = function() {
					for ( var i = 0; i < document.forms.length; i++ ) {
						var form = document.forms[i];
						var captcha_div = form.querySelector( '.anr_captcha_field_div' );

						if ( null === captcha_div )
							continue;
						captcha_div.innerHTML = '';
						( function( form ) {
							var anr_captcha = grecaptcha.render( captcha_div,{
								'sitekey' : '<?php echo esc_js( trim( anr_get_option( 'site_key' ) ) ); ?>',
								'size'  : '<?php echo esc_js( anr_get_option( 'size', 'normal' ) ); ?>',
								'theme' : '<?php echo esc_js( anr_get_option( 'theme', 'light' ) ); ?>'
							});
							if ( typeof jQuery !== 'undefined' ) {
								jQuery( document.body ).on( 'checkout_error', function(){
									grecaptcha.reset(anr_captcha);
								});
							}
							if ( typeof wpcf7 !== 'undefined' ) {
								document.addEventListener( 'wpcf7submit', function() {
									grecaptcha.reset(anr_captcha);
								}, false );
							}
						})(form);
					}
				};
			</script>
			<?php
			$language = trim( anr_get_option( 'language' ) );

			$lang = '';
			if ( $language ) {
				$lang = '&hl=' . $language;
			}
			$google_url = apply_filters( 'anr_v2_checkbox_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?onload=anr_onloadCallback&render=explicit' . $lang, anr_recaptcha_domain() ), $lang );
			?>
			<script src="<?php echo esc_url( $google_url ); ?>"
				async defer>
			</script>
			<?php
		}

		function v2_invisible_script() {
			?>
			<script type="text/javascript">
				var anr_onloadCallback = function() {
					for ( var i = 0; i < document.forms.length; i++ ) {
						var form = document.forms[i];
						var captcha_div = form.querySelector( '.anr_captcha_field_div' );

						if ( null === captcha_div )
							continue;
						captcha_div.innerHTML = '';
						( function( form ) {
							var anr_captcha = grecaptcha.render( captcha_div,{
								'sitekey' : '<?php echo esc_js( trim( anr_get_option( 'site_key' ) ) ); ?>',
								'size'  : 'invisible',
								'theme' : '<?php echo esc_js( anr_get_option( 'theme', 'light' ) ); ?>',
								'badge' : '<?php echo esc_js( anr_get_option( 'badge', 'bottomright' ) ); ?>',
								'callback' : function ( token ) {
									if( typeof jQuery !== 'undefined' ){
										jQuery(form).submit();
										grecaptcha.reset( anr_captcha );
									} else {
										HTMLFormElement.prototype.submit.call( form );
									}
								},
								'expired-callback' : function(){
									grecaptcha.reset( anr_captcha );
								}
							});
							var cf7_submit = form.querySelector( '.wpcf7-submit' );

							if( null !== cf7_submit && ( typeof jQuery !== 'undefined' ) ){
								jQuery( cf7_submit ).off('click').on('click', function( e ){
									e.preventDefault();
									jQuery( '.ajax-loader', form ).addClass( 'is-active' );
									grecaptcha.execute( anr_captcha );
								});
							} else {
								form.onsubmit = function( e ){
									e.preventDefault();
									grecaptcha.execute( anr_captcha );
								};
							}
						})(form);
					}
				};
			</script>
			<?php
			$language = trim( anr_get_option( 'language' ) );

			$lang = '';
			if ( $language ) {
				$lang = '&hl=' . $language;
			}
			$google_url = apply_filters( 'anr_v2_invisible_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?onload=anr_onloadCallback&render=explicit' . $lang, anr_recaptcha_domain() ), $lang );
			?>
			<script src="<?php echo esc_url( $google_url ); ?>"
				async defer>
			</script>
			<?php
		}

		function v3_script() {
			// v3 support v2 script. So use it
			// $this->v2_invisible_script();

			$language = trim( anr_get_option( 'language' ) );
			$site_key = trim( anr_get_option( 'site_key' ) );

			$lang = '';
			if ( $language ) {
				$lang = '&hl=' . $language;
			}

			$google_url = apply_filters( 'anr_v3_script_api_src', sprintf( 'https://www.%s/recaptcha/api.js?render=' . $site_key . $lang, anr_recaptcha_domain() ), $site_key, $lang );
			?>
			<script src="<?php echo esc_url( $google_url ); ?>"></script>
			<script type="text/javascript">
				( function( grecaptcha ) {

					var anr_onloadCallback = function() {
						grecaptcha.execute(
							'<?php echo esc_js( $site_key ); ?>',
							{ action: 'advanced_nocaptcha_recaptcha' }
						).then( function( token ) {
							for ( var i = 0; i < document.forms.length; i++ ) {
								var form = document.forms[i];
								var captcha = form.querySelector( 'input[name="g-recaptcha-response"]' );
								if ( null === captcha )
									continue;

								captcha.value = token;
							}
						});
					};

					grecaptcha.ready( anr_onloadCallback );

					document.addEventListener( 'wpcf7submit', anr_onloadCallback, false );
					if ( typeof jQuery !== 'undefined' ) {
						//Woocommerce
						jQuery( document.body ).on( 'checkout_error', anr_onloadCallback );
					}
					//token is valid for 2 minutes, So get new token every after 1 minutes 50 seconds
					setInterval(anr_onloadCallback, 110000);

				} )( grecaptcha );
			</script>
			<?php
		}


		function form_field() {
			echo $this->form_field_return();
			}

		function form_field_return( $return = '' ) {
			if ( is_user_logged_in() && anr_get_option( 'loggedin_hide' ) ) {
				return $return;
			}
			$ip = $_SERVER['REMOTE_ADDR'];
			if ( in_array( $ip, array_filter( explode( '\n', anr_get_option( 'whitelisted_ips' ) ) ) ) ) {
				return $return;
			}
			return $return . $this->captcha_form_field();
		}

		function post_id() {
			global $wpdb;
			static $post_id;

			if ( ! absint( anr_get_option( 'failed_login_allow' ) ) ) {
				return 0;
			}
			if ( is_numeric( $post_id ) ) {
				return $post_id;
			}
			$post_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = 'anr-post' LIMIT 1" );

			if ( ! $post_id ) {
				$wpdb->insert( $wpdb->posts, array( 'post_type' => 'anr-post' ) );
				$post_id = $wpdb->insert_id;
			}
			$post_id = absint( $post_id );

			return $post_id;
		}

		function show_login_captcha() {
			global $wpdb;

			$show_captcha = true;
			$ip           = $_SERVER['REMOTE_ADDR'];
			if ( in_array( $ip, array_filter( explode( '\n', anr_get_option( 'whitelisted_ips' ) ) ) ) ) {
				return false;
			}
			// filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
			$count   = absint( anr_get_option( 'failed_login_allow' ) );
			$post_id = $this->post_id();

			if ( $count && $post_id && filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				$user_logins = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, md5( $ip ) ) );

				if ( count( $user_logins ) < $count && count( array_unique( $user_logins ) ) <= 1 ) {
					$show_captcha = false;
				}
			}

			return $show_captcha;
		}
		function login_form_field() {
			if ( $this->show_login_captcha() ) {
				$this->form_field();
			}
		}

		function login_form_return( $field = '' ) {
			if ( $this->show_login_captcha() ) {
				$field = $this->form_field_return( $field );
				}
			return $field;
		}

		function wc_form_field() {
			if ( ! is_user_logged_in() && 'yes' == get_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' ) && anr_is_form_enabled( 'registration' ) ) {
				$this->form_field();

			} elseif ( anr_is_form_enabled( 'wc_checkout' ) ) {
				$this->form_field();
			}

		}

		function ms_form_field( $errors ) {
			if ( $errmsg = $errors->get_error_message( 'anr_error' ) ) {
				echo '<p class="error">' . $errmsg . '</p>';
			}
			$this->form_field();
		}

		function verify( $response = false ) {
			static $last_verify = null;

			if ( is_user_logged_in() && anr_get_option( 'loggedin_hide' ) ) {
				return true;
			}
	
			$secre_key  = trim( anr_get_option( 'secret_key' ) );
			$remoteip = $_SERVER['REMOTE_ADDR'];
			$verify = false;

			if ( in_array( $remoteip, array_filter( explode( '\n', anr_get_option( 'whitelisted_ips' ) ) ) ) ) {
				return true;
			}
			
			if ( false === $response ) {
				$response = isset( $_POST['g-recaptcha-response'] ) ? $_POST['g-recaptcha-response'] : '';
			}
			
			$pre_check = apply_filters( 'anr_verify_captcha_pre', null, $response );
			
			if ( null !== $pre_check ) {
				return $pre_check;
			}

			if ( ! $secre_key ) { // if $secre_key is not set
				return true;
			}

			if ( ! $response || ! $remoteip ) {
				return $verify;
			}
			
			if ( null !== $last_verify ) {
				return $last_verify;
			}

			$url = apply_filters( 'anr_google_verify_url', sprintf( 'https://www.%s/recaptcha/api/siteverify', anr_recaptcha_domain() ) );

			// make a POST request to the Google reCAPTCHA Server
			$request = wp_remote_post(
				$url, array(
					'timeout' => 10,
					'body'    => array(
						'secret'   => $secre_key,
						'response' => $response,
						'remoteip' => $remoteip,
					),
				)
			);

			// get the request response body
			$request_body = wp_remote_retrieve_body( $request );
			if ( ! $request_body ) {
				return $verify;
			}

				$result = json_decode( $request_body, true );
			if ( isset( $result['success'] ) && true == $result['success'] ) {
				if ( 'v3' === anr_get_option( 'captcha_version' ) ) {
					$score = isset( $result['score'] ) ? $result['score'] : 0;
					$action = isset( $result['action'] ) ? $result['action'] : '';
					$verify = anr_get_option( 'score', '0.5' ) <= $score && 'advanced_nocaptcha_recaptcha' == $action;
				} else {
					$verify = true;
				}
			}
			$verify = apply_filters( 'anr_verify_captcha', $verify, $result, $response );
			$last_verify = $verify;

			return $verify;
		}

		function fepcf_verify( $errors ) {
			if ( ! $this->verify() ) {
				$errors->add( 'anr_error', anr_get_option( 'error_message' ) );
			}
		}

		function login_verify( $user, $username = '', $password = '' ) {
			global $wpdb;
			if ( ! $username ) {
				return $user;
			}

			$show_captcha = $this->show_login_captcha();

			if ( ! ( $user instanceof WP_User ) ) {
				if ( ! $show_captcha && ( $post_id = $this->post_id() ) ) {
					if ( is_email( $username ) ) {
						$user_data = get_user_by( 'email', $username );
						if ( $user_data ) {
							$username = $user_data->user_login;
						}
					}
					$wpdb->insert(
						$wpdb->postmeta, array(
						'post_id'    => $post_id,
						'meta_key'   => md5( $_SERVER['REMOTE_ADDR'] ),
						'meta_value' => $username,
					), array( '%d', '%s', '%s' )
					);
				}
				// return $user;
			}
			if ( $show_captcha && ! $this->verify() ) {
				return new WP_Error( 'anr_error', $this->add_error_to_mgs() );
			}

			return $user;
		}

		function clear_data( $user_login, $user ) {
			global $wpdb;

			if ( $post_id = $this->post_id() ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d AND ( meta_key = %s OR meta_value = %s )", $post_id, md5( $_SERVER['REMOTE_ADDR'] ), $user_login ) );
			}
		}

		function registration_verify( $errors, $sanitized_user_login, $user_email ) {
			if ( ! $this->verify() ) {
				$errors->add( 'anr_error', $this->add_error_to_mgs() );
			}

			return $errors;
		}

		function wc_registration_verify( $errors, $sanitized_user_login, $user_email ) {
			if ( defined( 'WOOCOMMERCE_CHECKOUT' ) && ! anr_is_form_enabled( 'wc_checkout' ) ) {
				return $errors;
			}
			if ( ! $this->verify() ) {
				$errors->add( 'anr_error', anr_get_option( 'error_message' ) );
			}

			return $errors;
		}

		function bp_form_field() {
			do_action( 'bp_anr_error_errors' );

			$this->form_field();
		}

		function bp_registration_verify() {
			if ( ! $this->verify() ) {
				buddypress()->signup->errors['anr_error'] = anr_get_option( 'error_message' );
			}
		}

		function ms_form_field_verify( $result ) {
			if ( isset( $_POST['stage'] ) && 'validate-user-signup' === $_POST['stage'] && ! $this->verify() ) {
				$result['errors']->add( 'anr_error', anr_get_option( 'error_message' ) );
			}

			return $result;
		}

		function ms_blog_verify( $result ) {
			if ( ! $this->verify() ) {
				$result['errors']->add( 'anr_error', anr_get_option( 'error_message' ) );
			}

			return $result;
		}

		function lostpassword_verify( $result, $user_id ) {
			if ( ! $this->verify() ) {
				return new WP_Error( 'anr_error', $this->add_error_to_mgs() );
			}

			return $result;
		}

		function lostpassword_verify_44( $errors ) {
			if ( ! $this->verify() ) {
				$errors->add( 'anr_error', $this->add_error_to_mgs() );
			}
		}


		function reset_password_verify( $errors, $user ) {
			if ( ! $this->verify() ) {
				$errors->add( 'anr_error', $this->add_error_to_mgs() );
			}
		}

		function comment_verify( $commentdata ) {
			if ( ! $this->verify() ) {
				wp_die(
					'<p>' . $this->add_error_to_mgs() . '</p>', __( 'Comment Submission Failure' ), array(
						'response'  => 403,
						'back_link' => true,
					)
				);
			}

			return $commentdata;
		}

		function comment_verify_490( $approved ) {
			if ( ! $this->verify() ) {
				return new WP_Error( 'anr_error', $this->add_error_to_mgs(), 403 );
			}
			return $approved;
		}

		function wpcf7_form_field( $tag ) {

			return $this->form_field_return() . sprintf( '<span class="wpcf7-form-control-wrap %s"></span>', $tag->name );
		}

		function wpcf7_verify( $result, $tag ) {
			if ( ! $this->verify() ) {
				$result->invalidate( $tag, anr_get_option( 'error_message' ) );
			}

			return $result;
		}

		function bbp_new_verify( $forum_id ) {
			if ( ! $this->verify() ) {
				bbp_add_error( 'anr_error', $this->add_error_to_mgs() );
			}
		}

		function bbp_reply_verify( $topic_id, $forum_id ) {
			if ( ! $this->verify() ) {
				bbp_add_error( 'anr_error', $this->add_error_to_mgs() );
			}
		}

		function wc_checkout_verify( $data, $errors ) {
			$is_reg_enable   = apply_filters( 'woocommerce_checkout_registration_enabled', 'yes' === get_option( 'woocommerce_enable_signup_and_login_from_checkout' ) );
			$is_reg_required = apply_filters( 'woocommerce_checkout_registration_required', 'yes' !== get_option( 'woocommerce_enable_guest_checkout' ) );

			if ( ! is_user_logged_in() && $is_reg_enable && anr_is_form_enabled( 'registration' ) && ( $is_reg_required || ! empty( $data['createaccount'] ) ) ) {
				// verification done during ragistration, So no need any more verification
			} elseif ( ! $this->verify() ) {
				$errors->add( 'anr_error', anr_get_option( 'error_message' ) );
			}
		}


	} //END CLASS
} //ENDIF

add_action( 'init', array( anr_captcha_class::init(), 'actions_filters' ), -9 );

