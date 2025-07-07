<?php
/**
 * File to display reset password page.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Function to generate random string.
 *
 * @param integer $length Length of random string.
 * @return string
 */
function moppm_generate_random_string( $length = 10 ) {
	$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$characters_length = strlen( $characters );
	$random_string     = '';
	for ( $i = 0; $i < $length; $i++ ) {
		$random_string .= $characters[ random_int( 0, $characters_length - 1 ) ];
	}
	return $random_string;
}
/**
 * Function to generate random id based on session id or a random string.
 *
 * @return string
 */
function moppm_generate_id() {
	if ( ! function_exists( 'session_create_id' ) ) {
		return moppm_generate_random_string( 20 );
	} else {
		return session_create_id();
	}
}
/**
 * Function to reset password page
 *
 * @param object $user user object.
 * @return void
 */
function moppm_reset_pass_form( $user ) {
	$session_id = moppm_generate_id();
	$user_id    = $user->ID;
	set_transient( $session_id, array( 'moppm_user_id' => $user_id ), 90 );
	$miniorange_logo = plugins_url( 'password-policy-manager' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'shield.png' );
	?>
	<html>

	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php
		wp_enqueue_script( 'moppm_resest_pass_jquery_script', plugins_url( 'includes' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bootstrap.min.js', __FILE__ ), array( 'jquery' ), MOPPM_VERSION, true );
		wp_print_scripts( 'jquery-core' );
		wp_register_style( 'custom-login-css2', plugins_url( 'includes/css/bootstrap2.min.css', dirname( __FILE__ ) ), array(), MOPPM_VERSION );
		wp_print_styles( 'custom-login-css2' );
		wp_register_style( 'custom-login', plugins_url( 'includes/css/moppm_style_settings.min.css', dirname( __FILE__ ) ), array(), MOPPM_VERSION );
		wp_print_styles( 'custom-login' );
		?>
	</head>

	<body class="moppm_body">
		<div class="container">
			<div class="row w-100 d-flex justify-content-center align-items-center main_div">
				<div class="col-12 col-md-8 col-xxl-5 ">
					<div class="moppm_reset_body py-3 px-2">
						<center><?php echo '<img style="width:150px; height:90px;display: inline;"src="' . esc_url_raw( $miniorange_logo ) . '">'; ?></center>
						<h2 class="text-center my-3 text-capitalize" style="color:black; margin-left:15px; font-size:35px;"> <span><?php esc_html_e( 'Reset Password', 'password-policy-manager' ); ?></span> </h2>
						<div class="row mx-auto">
							<div class=" col-6 mx-auto">
								<form class="moppm_my_form">
									<div class="mb-3">
										<label for="Current Password" class="moppm_form_label"><?php esc_html_e( 'Current Password', 'password-policy-manager' ); ?></label>
										<input type="Password" class="moppm_input_password_field" id="moppm_old_pass" name="OldPass" placeholder="<?php esc_attr_e( 'Current Password', 'password-policy-manager' ); ?>">
									</div>
									<div class="mb-3">
										<label for="New Password" class="moppm_form_label moppm_form_value"><?php esc_html_e( 'New Password', 'password-policy-manager' ); ?></label>
										<input type="Password" class="moppm_input_password_field" id="moppm_new_pass1" name="Newpass" placeholder="<?php esc_attr_e( 'New Password', 'password-policy-manager' ); ?>">
									</div>
									<div class="mb-3">
										<label for="Confirm Password" class="moppm_form_label moppm_form_value"><?php esc_html_e( 'Confirm Password', 'password-policy-manager' ); ?></label>
										<input type="Password" class="moppm_input_password_field" id="moppm_new_pass2" name="Newpass2" placeholder="<?php esc_attr_e( 'Confirm Password', 'password-policy-manager' ); ?>">
										<input type="checkbox" onclick="moppm_myFunction()"><label style="margin-left:5%;"><?php esc_html_e( 'Show Password', 'password-policy-manager' ); ?></label>
									</div>

									<div class="my-3">
									<button class="btn btn-block btn-primary btn-md" type="button" value="SUBMIT" id="moppm_save_pass" >
									<span class="moppm_button_name"> <?php esc_html_e('Change Password','password-policy-manager'); ?></span> 
									<span class="moppm_loader"></span> 
										</button> 
									</div>
									<input type="hidden" name="NONCE" value="<?php echo esc_attr( wp_create_nonce( 'moppmresetformnonce' ) ); ?>">
									<input type="hidden" name="session_id" value="<?php echo esc_attr( $session_id ); ?>">
								</form>
							</div>
							<div class=" col-6 mx-auto">
								<span class="moppm_pass_require"> <?php esc_html_e( 'New Password Requirements', 'password-policy-manager' ); ?> </span><br>
								<div id="moppm_digit_entered" class="moppm_invalid fa fa-times">
									<?php
									$moppm_digit = get_site_option( 'moppm_digit' ) ? __( 'Minimum ', 'password-policy-manager' ) . get_site_option( 'moppm_digit' ) . __( ' characters', 'password-policy-manager' ) : '';
									echo esc_html( $moppm_digit );
									?>
								</div>
								<?php if ( get_site_option( 'moppm_Numeric_digit' ) ) { ?>
									<div id="moppm_number" class="moppm_invalid fa fa-times ">
										<?php
										$moppm_numeric_digit = get_site_option( 'moppm_Numeric_digit' ) ? __( ' Minimum one numeric digit (0,9)', 'password-policy-manager' ) : '';
										echo esc_html( $moppm_numeric_digit );
										?>
									</div><?php } ?>
								<?php if ( get_site_option( 'moppm_letter' ) ) { ?>
									<div id="moppm_lower_letter" class="moppm_invalid fa fa-times">
										<?php
										$moppm_letter = get_site_option( 'moppm_letter' ) ? __( ' Minimum one lower case letter', 'password-policy-manager' ) : '';
										echo esc_html( $moppm_letter );
										?>
									</div> <?php } ?>

								<?php if ( get_site_option( 'moppm_letter' ) ) { ?>
									<div id="moppm_upper_letter" class="moppm_invalid fa fa-times">
										<?php
										$moppm_letter = get_site_option( 'moppm_letter' ) ? __( ' Minimum one upper case letter', 'password-policy-manager' ) : '';
										echo esc_html( $moppm_letter );
										?>
									</div> <?php } ?>

								<?php if ( get_site_option( 'moppm_special_char' ) ) { ?>
									<div id="moppm_special_symbol" class="moppm_invalid fa fa-times">
										<?php
										$moppm_special_char = get_site_option( 'moppm_special_char' ) ? __( 'Minimum one special character (@ # $ %)', 'password-policy-manager' ) : '';
										echo esc_html( $moppm_special_char );
										?>
									</div> <?php } ?>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<form name="moppm-login-submit-form" id="moppm-login-form-submit" method="post" action="" hidden>
		    <input type="hidden" id="mopppm_userid" name="mopppm_userid" value=""/>
			<input type="text" name="option" id="mopppm_login" hidden/>
			<input type="hidden" name="moppm_login_nonce" value="<?php echo esc_attr( wp_create_nonce( 'moppm-login-nonce' ) ); ?>"/>
			<input type="hidden" name="moppm_session_id" value="<?php echo esc_attr( $session_id ); ?>"/>
		</form>   
		<div id="moppm_message"></div>
		<?php
		wp_register_script( 'moppm_ajax-login-script', plugins_url( 'includes/js/moppm_reset_pass.min.js', dirname( __FILE__ ) ), array(), MOPPM_VERSION, true );
		wp_localize_script(
			'moppm_ajax-login-script',
			'ajax_object',
			array(
				'ajaxurl'        => admin_url( 'admin-ajax.php' ),
				'redirecturl'    => admin_url(),
				'loginUrl'       => wp_login_url(),
				'min_length'     => get_site_option( 'moppm_digit' ),
				'loadingmessage' => __( 'Sending user info, please wait...', 'password-policy-manager' ),
			)
		);
		wp_print_scripts( 'moppm_ajax-login-script' );
		?>
	</body>

	</html>
<?php } ?>
