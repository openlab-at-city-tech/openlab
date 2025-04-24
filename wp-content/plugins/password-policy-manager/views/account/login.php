<?php
/**
 *  File to display miniorange login form
 *
 * @package    password-policy-manager/views/account
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
echo '	<form name="f" method="post" action="">
			<input type="hidden" name="option" value="moppm_verify_user" />
			<input type="hidden" name="nonce" value="' . esc_attr( wp_create_nonce( 'moppm-account-nonce' ) ) . '" />

			<table class="moppm_mo_settings_table">
					<h3>Login with miniOrange</h3>
					<p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password.</td><a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword"> Click here if you forgot your password?</a></b></p>
					
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="moppm_table_input_text" type="email" name="email"
								required placeholder="person@example.com"
								value="' . esc_attr( $admin_email ) . '" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="moppm_table_input_text" required type="password"
								name="password" placeholder="Enter your miniOrange password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" value="Sign In" class="button button-primary" />
								<a href="#cancel_link" class="button button-primary">New User? Register</a>
						</tr>
					</table>
			
		</form>
		<form id="cancel_form" method="post" action="">
			<input type="hidden" name="option" value="moppm_cancel" />
			<input type="hidden" name="nonce" value="' . esc_attr( wp_create_nonce( 'moppm-account-nonce' ) ) . '" >
		</form>
		<script>
			jQuery(document).ready(function(){
				jQuery(\'a[href="#cancel_link"]\').click(function(){
					jQuery("#cancel_form").submit();
				});		
			});
		</script>';
