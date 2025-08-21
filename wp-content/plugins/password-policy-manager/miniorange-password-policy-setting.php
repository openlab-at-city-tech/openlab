<?php //phpcs:ignore -- FileName
/**
 * Plugin Name: Password Policy Manager
 * Description: This plugin enables configurable password policies for the Stronger passwords. We Support Password expiration, Enforce strong password for all Users in the free version of the plugin.
 * Version: 2.0.5
 * Author: miniOrange
 * Author URI: https://miniorange.com
 * Text Domain: password-policy-manager
 * License: Expat
 * License URI: https://plugins.miniorange.com/mit-license
 * Domain Path: /lang
 *
 * @package password-policy-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
	define( 'MOPPM_HOST_NAME', 'https://login.xecurify.com' );
	define( 'MOPPM_VERSION', '2.0.5' );
	define( 'MOPPM_TEST_MODE', false );
	global $moppm_dir,$moppm_directory_url;
	$moppm_dir           = plugin_dir_url( __FILE__ );
	$moppm_directory_url = plugin_dir_path( __FILE__ );

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
if ( ! class_exists( 'MOPPM' ) ) {
	/**
	 * Class MOPPM
	 *
	 * This class is the main class for password policy manager
	 */
	class MOPPM {
		/**
		 * Constructor function
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'moppm_add_thickbox' ) );
			register_deactivation_hook( __FILE__, array( $this, 'moppm_deactivate' ) );
			register_activation_hook( __FILE__, array( $this, 'moppm_activate' ) );
			add_action( 'admin_menu', array( $this, 'moppm_widget_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'moppm_settings_style' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'moppm_settings_script' ) );
			add_action( 'moppm_show_message', array( $this, 'moppm_show_message' ), 1, 2 );
			add_action( 'admin_footer', array( $this, 'moppm_feedback_request' ) );
			add_action( 'validate_password_reset', array( $this, 'moppm_password_reset' ), 1, 2 );
			$this->moppm_includes();
			remove_filter( 'authenticate', 'wp_authenticate_username_password', 20 );
			remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
			add_filter( 'authenticate', array( $this, 'custom_authenticate' ), 1, 3 );
			add_action( 'user_profile_update_errors', array( $this, 'profile_authenticate' ), 0, 3 );
			add_action( 'user_register', array( $this, 'moppm_create_usermeta' ) );
			add_action( 'admin_init', array( $this, 'moppm_redirect_page' ) );
			add_action( 'elementor/init', array( $this, 'moppm_login_extra_note' ) );
			add_filter( 'manage_users_columns', array( $this, 'moppm_password_column' ) );
			add_action( 'manage_users_custom_column', array( $this, 'moppm_password_column_content' ), 10, 3 );
			add_action( 'plugins_loaded', array( $this, 'moppm_update_db_check' ) );
			add_action( 'wp_print_scripts', array( $this, 'webroom_wc_remove_password_strength' ), 100 );

			add_action( 'admin_notices', array( $this, 'moppm_notices' ) );
			if ( is_admin() ) {
				add_filter( 'plugin_action_links', array( $this, 'moppm_add_plugin_action_links' ), 10, 2 );
			}

		}
		/**
		 * Add plugin main file link using plugin_action_links filter.
		 *
		 * @param string $links add action links.
		 * @param string $file main plugin file.
		 * @return string
		 */
		public function moppm_add_plugin_action_links( $links, $file ) {
			if ( plugin_basename( dirname( __FILE__ ) . '/miniorange-password-policy-setting.php' ) === $file ) {
				$links[] = '<a href="admin.php?page=moppm">Settings</a>';
				$links[] = '<a href="admin.php?page=moppm_upgrade" style="color:orange;font-weight:bold">Upgrade</a>';
			}
			return $links;
		}

		/**
		 * Function for removing password strength script
		 *
		 * @return void
		 */
		public function webroom_wc_remove_password_strength() {
			if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
				wp_dequeue_script( 'wc-password-strength-meter' );
			}
		}

		/**
		 * Function to show offer banner.
		 *
		 * @return void
		 */
		public function moppm_notices() {
			$moppm_curr_date = new DateTime( 'today' );
			$moppm_end_date  = new DateTime( '2022-12-31' );
			if ( ! get_site_option( 'moppm_remove_offer_banner', false ) && ( $moppm_curr_date <= $moppm_end_date ) && false ) {
				?>
		<div class="notices moppm-black-friday" > 
			<div class="moppm-offer-logo    "></div>
			<div style="flex:7;display:flex;height:100%;align-items:center">
				<div class="moppm-bf-support-content">
					<strong>End of the year sale! Upto 50% off</strong>
					<div>On our <a href="admin.php?page=moppm_upgrade">Enterprise</a> and <a href="admin.php?page=moppm_upgrade">Premium</a> <b>Password Policy Manager</b> plugins</div> 
				</div>
				<div id="countdowns" class="moppm-countdown">
						<div class="moppm-bf-days">
							<span id="days" class="moppm-bf-time"></span> 
							Days
						</div> 
						<div class="moppm-bf-days">
							<span id="hours" class="moppm-bf-time"></span>
							Hours    
						</div>
						<div class="moppm-bf-days">
							<span id="minutes" class="moppm-bf-time"></span>
							minutes   
						</div>
						<div class="moppm-bf-days">
							<span id="seconds" class="moppm-bf-time"></span>
							seconds  
						</div>
					</div>
			</div>
			<div class="moppm-bf-support-btn">
				<a class="link-banner-btn" href="admin.php?page=moppm_upgrade" target="_blank" rel="nofollow">
						<button>
							Upgrade Now
						</button>
				</a>  	
			</div> 
			<div class="moppm_dismiss_bf"> 
				<div id="moppm-bf-dissmiss-permanent" class="moppm_dismiss_bf_bg dashicons dashicons-no-alt" title="do not show again"></div>
				<div id="moppm-bf-dissmiss" class="moppm_dismiss_bf_bg dashicons dashicons-minus" title="dismiss"></div>
			</div>
		</div>   
		<script>
		(function () {
		const second = 1000,
			minute = second * 60,
			hour = minute * 60,
			day = hour * 24;

			let today = new Date(),
			dd = String(today.getDate()).padStart(2, "0"),
			mm = String(today.getMonth() + 1).padStart(2, "0"),
			yyyy = today.getFullYear(),
			nextYear = yyyy + 1,
			dayMonth = "12/31/",
			offers = dayMonth + yyyy;

			today = mm + "/" + dd + "/" + yyyy;
			if (today > offers) {
				offers = dayMonth + nextYear;
			}

		const countDown = new Date(offers).getTime(),
			x = setInterval(function() {    
				const now = new Date().getTime(),
				distance = countDown - now;

				document.getElementById("days").innerText = Math.floor(distance / (day)),
				document.getElementById("hours").innerText = Math.floor((distance % (day)) / (hour));
				document.getElementById("minutes").innerText = Math.floor((distance % (hour)) / (minute)),
				document.getElementById("seconds").innerText = Math.floor((distance % (minute)) / second);


				if (distance < 0) {
					document.getElementById("countdown").style.display = "none";
					document.getElementById("content").style.display = "block";
					clearInterval(x);
				}
			}, 1000)
		}());


		jQuery("#moppm-bf-dissmiss").click(()=>{
				jQuery(".moppm-black-friday").slideToggle();
		})

		jQuery("#moppm-bf-dissmiss-permanent").click(()=>{
				var data = {
				'action'					: 'moppm_ajax',
				'option' 					: 'moppm_black_friday_remove',
				'nonce'						:  '<?php echo esc_js( wp_create_nonce( 'moppm-remove-offer-banner' ) ); ?>'
				};
				jQuery.post(ajaxurl, data, function(response) {
						var response = response.replace(/\s+/g,' ').trim();
						if(response !== 'ERROR')
						{
							jQuery(".moppm-black-friday").slideToggle();
						}
				});
		})

		</script> 
				<?php
			}
		}

		/**
		 * Function to add thickbox
		 *
		 * @return void
		 */
		public function moppm_add_thickbox() {
			add_thickbox();
		}

		/**
		 * Function to check if moppm_dbversion exist or not
		 *
		 * @return void
		 */
		public function moppm_update_db_check() {
			global $moppm_db_queries;
			if ( ! get_site_option( 'moppm_dbversion' ) ) {
				update_site_option( 'moppm_dbversion', MOPPM_Constants::DB_VERSION );
				$moppm_db_queries->generate_tables();
			} else {
				$current_db_version = get_site_option( 'moppm_dbversion' );
				if ( $current_db_version < MOPPM_Constants::DB_VERSION ) {
					if ( get_site_option( 'email' ) && ! get_site_option( 'moppm_email' ) ) {
						update_site_option( 'moppm_email', get_site_option( 'email' ) );
					}
					if ( get_site_option( 'customerKey' ) && ! get_site_option( 'moppm_customerKey' ) ) {
						update_site_option( 'moppm_customerKey', get_site_option( 'customerKey' ) );
					}
					if ( get_site_option( 'api_key' ) && ! get_site_option( 'moppm_api_key' ) ) {
						update_site_option( 'moppm_api_key', get_site_option( 'api_key' ) );
					}
					if ( get_site_option( 'customer_token' ) && ! get_site_option( 'moppm_customer_token' ) ) {
						update_site_option( 'moppm_customer_token', get_site_option( 'customer_token' ) );
					}
					if ( get_site_option( 'verify_customer' ) && ! get_site_option( 'moppm_verify_customer' ) ) {
						update_site_option( 'moppm_verify_customer', get_site_option( 'verify_customer' ) );
					}
					if ( get_site_option( 'registration_status' ) && ! get_site_option( 'moppm_registration_status' ) ) {
						update_site_option( 'moppm_registration_status', get_site_option( 'registration_status' ) );
					}

					update_site_option( 'moppm_dbversion', MOPPM_Constants::DB_VERSION );
				}
			}
			load_plugin_textdomain( 'password-policy-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		}

		/**
		 * Function to add one column 'Password Strength Score' in user table.
		 *
		 * @param array $columns column.
		 * @return array
		 */
		public function moppm_password_column( $columns ) {
			$columns['current_status'] = 'Password Strength Score';
			return $columns;
		}

		/**
		 * Function to add content to custom row in user table
		 *
		 * @param string $value data to add in user table.
		 * @param string $column_name column in which we add custom data.
		 * @param int    $user_id user_id for which we want to add data in custom column.
		 * @return string
		 */
		public function moppm_password_column_content( $value, $column_name, $user_id ) {
			$moppm_score = get_user_meta( $user_id, 'moppm_pass_score' );

			if ( 'current_status' === $column_name ) {
				if ( ! empty( $moppm_score[0] ) ) {
					$moppm_score = intval( $moppm_score[0] );
					return ( '<span style="margin-left:30%;">' . esc_html( $moppm_score ) . ' <span>' );
				} 
				return ( '<span style="margin-left:30%;">' . esc_html( '0' ) . ' <span>' );
			}
			return $value;
		}

		/**
		 * Function to enqueue script file when elementor plugin is initialised
		 *
		 * @return void
		 */
		public function moppm_login_extra_note() {
			if ( ! is_user_logged_in() ) {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'moppm_elementor_script', plugins_url( 'includes/js/moppm_elementor.min.js', __FILE__ ), array( 'jquery' ), MOPPM_VERSION, true );
				wp_localize_script(
					'moppm_elementor_script',
					'my_ajax_object',
					array( 'ajax_url' => get_site_url() . '/login/' )
				);

			}

		}

		/**
		 * Function to redirect user.
		 *
		 * @return void
		 */
		public function moppm_redirect_page() {
			if ( get_site_option( 'moppm_plugin_redirect' ) ) {
				delete_site_option( 'moppm_plugin_redirect' );
				wp_safe_redirect( admin_url() . 'admin.php?page=moppm' );
				exit();
			}
			if ( isset( $_POST['moppm_register_to_upgrade_nonce'] ) ) {
				$nonce = sanitize_key( $_POST['moppm_register_to_upgrade_nonce'] );
				if ( ! wp_verify_nonce( $nonce, 'miniorange-moppm-user-reg-to-upgrade-nonce' ) ) {
					update_site_option( 'mo_ppm_message', 'INVALID_REQ' );
				} else {
					if ( isset( $_POST['requestOrigin'] ) ) {
						$requestorigin = esc_url_raw( wp_unslash( $_POST['requestOrigin'] ) );
						update_site_option( 'mo_ppm_customer_selected_plan', $requestorigin );
						header( 'Location: admin.php?page=moppm_account' );
					}
				}
			}

		}
		/**
		 * Function to run after password reset form.
		 *
		 * @param object $error error object.
		 * @param object $user user object.
		 * @return void
		 */
		public function moppm_password_reset( $error, $user ) {
			if ( isset( $_POST['pass1'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing -- function is calling from validate_password_reset hook so nonce is not required here
				$new_pass = $_POST['pass1']; //phpcs:ignore WordPress.Security.NonceVerification.Missing , WordPress.Security.ValidatedSanitizedInput.MissingUnslash , WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize password and function is calling from validate_password_reset hook so nonce is not required here
				if ( get_site_option( 'Moppm_enable_disable_ppm' ) === 'on' && ! empty( $new_pass ) ) {
					$result = Moppm_Utility::validate_password( $new_pass );
					if ( 'VALID' !== $result ) {
						$error->add( 'weak_password', $result );
						return;
					}
					$moppm_count = Moppm_Utility::check_password_score( $new_pass );
					update_user_meta( $user->ID, 'moppm_pass_score', $moppm_count );
					global $moppm_db_queries;
					$log_out_time = gmdate( 'M j, Y, g:i:s a' );
					if ( get_site_option( 'moppm_enable_disable_report' ) === 'on' ) {
						$moppm_db_queries->update_report_list( $user->ID, $log_out_time );
					}
				}
				delete_user_meta( $user->ID, 'moppm_points' );
				add_user_meta( $user->ID, 'moppm_first_reset', '2' );
				update_user_meta( $user->ID, 'moppm_last_pass_timestmp', time() );
			}

		}

		/**
		 * Function to create meta of users
		 *
		 * @param int $user_id id of current user.
		 * @return void
		 */
		public function moppm_create_usermeta( $user_id ) {
			global $moppm_db_queries;
			$user        = get_user_by( 'ID', $user_id );
			$moppm_count = isset( $_POST['pass2'] ) ? Moppm_Utility::check_password_score( $_POST['pass2'] ) : 0;//phpcs:ignore  WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize password and function is calling from  hook so nonce is not required here.
			update_user_meta( $user_id, 'moppm_pass_score', $moppm_count );
			$meta_key = 'moppm_last_pass_timestmp';
			if ( get_site_option( 'moppm_expiration_time' ) ) {
				update_user_meta( $user_id, $meta_key, time() );
			}
			$log_time     = 'N/A';
			$log_out_time = gmdate( 'M j, Y, g:i:s a' );
			if ( get_site_option( 'moppm_enable_disable_report' ) === 'on' ) {
				$moppm_db_queries->insert_report_list( $user_id, $user->user_email, $log_time, $log_out_time );
			}

		}
		/**
		 * Function to do additional validation on password change from user profile section.
		 *
		 * @param object  $errors error object.
		 * @param boolean $update boolean variable update.
		 * @param object  $userdata user data object.
		 * @return void
		 */
		public function profile_authenticate( $errors, $update, $userdata ) {
			$password = ( isset( $_POST['pass1'] ) && trim( $_POST['pass1'] ) ) ? $_POST['pass1'] : false; //phpcs:ignore WordPress.Security.NonceVerification.Missing ,WordPress.Security.ValidatedSanitizedInput.MissingUnslash ,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize password and function is calling from user_profile_update_errors hook so nonce is not required here
			if ( get_site_option( 'Moppm_enable_disable_ppm' ) === 'on' && false !== $password ) {
				$result = Moppm_Utility::validate_password( $password );
				if ( 'VALID' !== $result ) {
					$errors->add( 'weak_password', $result );
					return;
				}
				global $moppm_db_queries;
				$log_out_time = gmdate( 'M j, Y, g:i:s a' );
				$moppm_count  = isset( $password ) ? Moppm_Utility::check_password_score( $password ) : 0;//phpcs:ignore  WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.MissingUnslash,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- do not sanitize password and function is calling from  hook so nonce is not required here.
				if ( isset( $userdata->ID ) ) {
					update_user_meta( $userdata->ID, 'moppm_pass_score', $moppm_count );
				}if ( get_site_option( 'moppm_enable_disable_report' ) === 'on' ) {
					if ( isset( $userdata->ID ) ) {
						$moppm_db_queries->update_report_list( $userdata->ID, $log_out_time );
					}
				}
			}
		}
		/**
		 * Function to add custom verification after user login
		 *
		 * @param string $user user object.
		 * @param string $username username of user.
		 * @param string $password password of user.
		 * @return object
		 */
		public function custom_authenticate( $user, $username, $password ) {
			$error = new WP_Error();
			if ( empty( $username ) ) {
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Username is empty', 'password-policy-manager' ) );
			}
			if ( empty( $password ) ) {
				$error->add( 'empty_password', __( '<strong>ERROR</strong>:Password is empty.', 'password-policy-manager' ) );
			}
			if ( is_email( $username ) ) {
				$user = wp_authenticate_email_password( $user, $username, $password );
			} else {
				$user = wp_authenticate_username_password( $user, $username, $password );
			}
			$currentuser = $user;
			if ( is_wp_error( $currentuser ) ) {
				$error->add( 'invalid_username_password', '<strong>' . __( 'ERROR', 'password-policy-manager' ) . '</strong>: ' . __( 'Invalid Username or password.', 'password-policy-manager' ) );
				return $currentuser;
			}
			if ( is_wp_error( $user ) ) {
				$error->add( 'empty_username', __( '<strong>ERROR</strong>: Invalid username or Password.', 'password-policy-manager' ) );
				return $user;
			}
			global $moppm_db_queries;
			$log_time     = gmdate( 'M j, Y, g:i:s a' );
			$log_out_time = gmdate( 'M j, Y, g:i:s a' );
			$user_id      = $currentuser->ID;
			if ( get_site_option( 'Moppm_enable_disable_ppm' ) === 'on' ) {
				if ( get_user_meta( $user->ID, 'moppm_points' ) ) {
					$this->moppm_send_reset_link( $currentuser->user_email, $user->ID, $user );
					$error->add( 'Reset Password', '<strong>' . __( 'ERROR', 'password-policy-manager' ) . '</strong>: ' . __( 'Reset password link has been sent in your email please check.', 'password-policy-manager' ) );
					return $error;
				}
				if ( get_site_option( 'moppm_enable_disable_expiry' ) ) {
					$user_time     = get_user_meta( $user->ID, 'moppm_last_pass_timestmp' );
					$tstamp        = isset( $user_time[0] ) ? $user_time[0] : 0;
					$current_time  = time();
					$start_time    = $current_time - $tstamp;
					$get_save_time = get_site_option( 'moppm_expiration_time' ) * 7 * 24 * 3600;
					if ( ! get_user_meta( $user->ID, 'moppm_last_pass_timestmp' ) || ( $start_time > $get_save_time && get_site_option( 'moppm_expiration_time' ) ) ) {
						moppm_reset_pass_form( $user );
						exit();
					}
				}
				if ( 'VALID' !== Moppm_Utility::validate_password( $password ) && get_site_option( 'moppm_first_reset' ) === '1' && ! get_user_meta( $user->ID, 'moppm_first_reset' ) ) {
					moppm_reset_pass_form( $user );
					exit();
				}
			}

			if ( get_site_option( 'moppm_enable_disable_report' ) === 'on' ) {
				$moppm_db_queries->insert_report_list( $user_id, $user->user_email, $log_time, $log_out_time );
			}

			return $currentuser;
		}

		/**
		 * Function to add widget of the plugin
		 *
		 * @return void
		 */
		public function moppm_widget_menu() {
			$menu_slug = 'moppm';
			add_menu_page( 'miniOrange Password policy', 'Password Policy Manager', 'administrator', $menu_slug, array( $this, 'moppm' ), plugin_dir_url( __FILE__ ) . 'includes/images/miniorange_icon.png' );
			add_submenu_page( $menu_slug, 'miniOrange Password policy', 'Addons', 'administrator', 'moppm_addons', array( $this, 'moppm' ), 1 );
			add_submenu_page( $menu_slug, 'miniOrange Password policy', 'Reports', 'administrator', 'moppm_reports', array( $this, 'moppm' ), 2 );
			add_submenu_page( $menu_slug, 'miniOrange Password policy', 'Upgrade', 'administrator', 'moppm_upgrade', array( $this, 'moppm' ), 3 );
			add_submenu_page( $menu_slug, 'miniOrange Password policy', 'Account', 'administrator', 'moppm_account', array( $this, 'moppm' ), 4 );
			add_submenu_page( $menu_slug, 'miniOrange Password policy', 'Integrations', 'administrator', 'moppm_registration_form', array( $this, 'moppm' ), 5 );
			add_submenu_page( $menu_slug, 'miniOrange Password policy', 'Other Products', 'administrator', 'moppm_advertise', array( $this, 'moppm' ), 6 );

		}
		/**
		 * Function to send password reset link to user email
		 *
		 * @param string $email email of user.
		 * @param int    $user_id id of the user.
		 * @param object $user user object.
		 * @return void
		 */
		public function moppm_send_reset_link( $email, $user_id, $user ) {

			$url         = is_multisite() ? get_blogaddress_by_id( (int) 1 ) : home_url( '', 'http' );
			$adt_rp_key  = get_password_reset_key( $user );
			$user_login  = $user->user_login;
			$url_new     = esc_url_raw( $url );
			$network_url = esc_url_raw( network_site_url( "wp-login.php?action=rp&key=$adt_rp_key&login=" . rawurlencode( sanitize_text_field( $user_login ) ), 'login' ) );

			$subject  = 'Reset password link';
			$messages = '<table cellpadding="25" style="margin:0px auto">
                            <tbody>
                            <tr>
                            <td>
                            <table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
                            <tbody>
                            <tr>
                            <td><img src="https://ci5.googleusercontent.com/proxy/10EQeM1udyBOkfD2dwxGhIaMXV4lOwCRtUecpsDkZISL0JIkOL2JhaYhVp54q6Sk656rW2rpAFJFEgGQiAOVcYIIKxXYMHHMNSNB=s0-d-e1-ft#https://login.xecurify.com/moas/images/xecurify-logo.png" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px" class="CToWUd"></td>
                            </tr>
                            </tbody>
                            </table>
                            <table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
                            <tbody>
                            <tr>
                            <td>
                            <p style="margin-top:0;margin-bottom:20px">Dear ' . sanitize_text_field( $user->user_nicename ) . ',</p>
                            <p style="margin-top:0;margin-bottom:10px">Your admin has requested to reset the password for security aspects</b>:</p>
                            <p style="margin-top:0;margin-bottom:10px">Site: ' . $url_new . '
                            <p style="margin-top:0;margin-bottom:10px">Please use the below link to reset the password and make secure your account
                            <p style="margin-top:0;margin-bottom:10px"><a href="' . $network_url . '">' . $network_url . ' </a> 
                            <p style="margin-top:0;margin-bottom:15px">Thank you,<br>miniOrange Team</p>
                            <p style="margin-top:0;margin-bottom:0px;font-size:11px">Disclaimer: This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
                            </div></div></td>
                            </tr>
                            </tbody>
                            </table>
                            </td>
                            </tr>
                            </tbody>
                            </table>';
			$headers  = array( 'Content-Type: text/html; charset=UTF-8' );
			wp_mail( $email, $subject, $messages, $headers );
		}

		/**
		 * Function to include main controller file
		 *
		 * @return void
		 */
		public function moppm() {
			include 'controllers' . DIRECTORY_SEPARATOR . 'main-controller.php';
		}

		/**
		 * Function to add default option on activation of the plugin.
		 *
		 * @return void
		 */
		public function moppm_activate() {
			global $moppm_db_queries;
			add_site_option( 'moppm_letter', 1 );
			add_site_option( 'moppm_Numeric_digit', 1 );
			add_site_option( 'moppm_special_char', 1 );
			add_site_option( 'moppm_digit', 8 );
			add_site_option( 'moppm_first_reset', 0 );
			add_site_option( 'moppm_expiration_time', 7 );
			update_site_option( 'moppm_plugin_redirect', true );
			$moppm_db_queries->plugin_activate();
			$expiry_time = get_site_option( 'moppm_expiration_time' );
		}

		/**
		 * Function to delete option on deactivation of the plugin.
		 *
		 * @return void
		 */
		public function moppm_deactivate() {
			delete_site_option( 'moppm_activated_time' );
		}

		/**
		 * Function to enqueue css styles
		 *
		 * @param string $hook current page.
		 * @return void
		 */
		public function moppm_settings_style( $hook ) {
			if ( strpos( $hook, 'page_moppm' ) ) {
				wp_enqueue_style( 'moppm_admin_settings_style', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'moppm_style_settings.min.css', __FILE__ ), array(), MOPPM_VERSION );
				wp_enqueue_style( 'moppm_admin_settings_datatable_style', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jquery.dataTables.min.css', __FILE__ ), array(), MOPPM_VERSION );
			}
			wp_enqueue_style( 'moppm_admin_offers', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'moppm_offers.min.css', __FILE__ ), array(), MOPPM_VERSION );
			wp_enqueue_style( 'moppm_upgrade_css', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'moppm_upgrade.min.css', __FILE__ ), array(), MOPPM_VERSION );
		}

		/**
		 * Function to enqueue js scripts
		 *
		 * @param [type] $hook current page.
		 * @return void
		 */
		public function moppm_settings_script( $hook ) {
			wp_enqueue_script( 'moppm_admin_settings_script', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'moppm_settings_page.min.js', __FILE__ ), array( 'jquery' ), MOPPM_VERSION, true );
			if ( strpos( $hook, 'page_moppm' ) ) {
				wp_enqueue_script( 'moppm_admin_datatable_script', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'jquery.dataTables.min.js', __FILE__ ), array( 'jquery' ), MOPPM_VERSION, true );
			}
		}

		/**
		 * Function to include php files
		 *
		 * @return void
		 */
		public function moppm_includes() {
			require_once 'helper' . DIRECTORY_SEPARATOR . 'class-moppm-utility.php';
			require_once 'controllers' . DIRECTORY_SEPARATOR . 'class-moppm-ajax.php';
			require_once 'database' . DIRECTORY_SEPARATOR . 'class-moppm-database.php';
			require_once 'api' . DIRECTORY_SEPARATOR . 'class-moppm-api.php';
			require_once 'helper' . DIRECTORY_SEPARATOR . 'class-moppm-constants.php';
			require_once 'helper' . DIRECTORY_SEPARATOR . 'class-moppm-messages.php';
			require_once 'handler' . DIRECTORY_SEPARATOR . 'class-moppmfeedbackhandler.php';
			require_once 'views' . DIRECTORY_SEPARATOR . 'reset-pass.php';
		}

		/**
		 * Function to show messages
		 *
		 * @param string $content content to show.
		 * @param string $type type of content.
		 * @return void
		 */
		public function moppm_show_message( $content, $type ) {
			if ( 'CUSTOM_MESSAGE' === $type ) {
				echo "<div class='moppm_overlay_not_JQ_success' id='pop_up_success'><p class='moppm_popup_text_not_JQ'>" . esc_html( $content ) . '</p> </div>';
				?>
				<script type="text/javascript">
				setTimeout(function () {
				var element = document.getElementById("pop_up_success");
				element.classList.toggle("moppm_overlay_not_JQ_success");
				element.innerHTML = "";
				}, 4000);			
			</script>
				<?php
			}
			if ( 'NOTICE' === $type ) {
				echo "<div class='moppm_overlay_not_JQ_error' id='pop_up_error'><p class='moppm_popup_text_not_JQ'>" . esc_html( $content ) . '</p> </div>';
				?>
				<script type="text/javascript">
				setTimeout(function () {
				var element = document.getElementById("pop_up_error");
				element.classList.toggle("moppm_overlay_not_JQ_error");
				element.innerHTML = "";
				}, 4000);		
				</script>
				<?php
			}
			if ( 'ERROR' === $type ) {
				echo "<div class='moppm_overlay_not_JQ_error' id='pop_up_error'><p class='moppm_popup_text_not_JQ'>" . esc_html( $content ) . '</p> </div>';
				?>
				<script type="text/javascript">
				setTimeout(function () {
				var element = document.getElementById("pop_up_error");
				element.classList.toggle("moppm_overlay_not_JQ_error");
				element.innerHTML = "";
				}, 4000);
				</script>
				<?php
			}
			if ( 'SUCCESS' === $type ) {
				echo "<div class='moppm_overlay_not_JQ_success' id='pop_up_success'><p class='moppm_popup_text_not_JQ'>" . esc_html( $content ) . '</p> </div>';
				?>
					<script type="text/javascript">
					setTimeout(function () {
					var element = document.getElementById("pop_up_success");
					element.classList.toggle("moppm_overlay_not_JQ_success");
					element.innerHTML = "";
					}, 4000);		
					</script>
				<?php
			}
		}

		/**
		 * Function to handle feedback of user
		 *
		 * @return void
		 */
		public function moppm_feedback_request() {
			if ( isset( $_SERVER['PHP_SELF'] ) ) {
				if ( 'plugins.php' !== basename( sanitize_text_field( wp_unslash( $_SERVER['PHP_SELF'] ) ) ) ) {
					return;
				}
			}
			global $moppm_dirname;

			$email = esc_html( get_site_option( 'moppm_email' ) );
			if ( empty( $email ) ) {
				$user  = wp_get_current_user();
				$email = $user->user_email;
			}
			$imagepath = plugins_url( '/includes/images/', __FILE__ );
			wp_enqueue_style( 'wp-pointer' );
			wp_enqueue_script( 'wp-pointer' );
			wp_enqueue_script( 'utils' );
			wp_enqueue_style( 'moppm_admin_plugins_page_style', plugins_url( '/includes/css/moppm_feedback_style.min.css?ver=' . MOPPM_VERSION, __FILE__ ), array(), MOPPM_VERSION );

			include $moppm_dirname . 'views' . DIRECTORY_SEPARATOR . 'feedback-form.php';
		}
	}}new MOPPM();
