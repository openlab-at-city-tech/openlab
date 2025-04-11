<?php
/**
 * File to display miniorange registration form.
 *
 * @package    password-policy-manager/views/account
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
echo '<!--Register with miniOrange-->
	<form name="f" method="post" action="">
		<input type="hidden" name="option" value="moppm_register_user" />
		<input type="hidden" name="nonce" value=' . esc_attr( wp_create_nonce( 'moppm-account-nonce' ) ) . ' >
				<h3>Register with miniOrange</h3>
				<p>Just complete the short registration below to configure miniOrange Password Policy plugin. Please enter a valid email id that you have access to.</p>
				<table class="moppm_mo_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input class="moppm_table_input_text" type="email" name="email"
							required placeholder="person@example.com"
							value="' . esc_attr( $user->user_email ) . '" /></td>
					</tr>

					<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="moppm_table_input_text" required type="password"
							name="password" placeholder="Choose your password (Min. length 6)" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
						<td><input class="moppm_table_input_text" required type="password"
							name="confirmPassword" placeholder="Confirm your password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><br /><input type="submit" name="submit" value="Register" style="width:109px;"
							class="button button-primary" />
						<a class="button button-primary" href="#Moppm_account_exist">Existing User? Log In</a>
					</tr>
				</table>
	</form>
	 <form name="f" method="post" action="" class="moppm_verify_userform">
        <input type="hidden" name="option" value="moppm_goto_verifyuser">
         <input type="hidden" name="nonce" value=' . esc_attr( wp_create_nonce( 'moppm-account-nonce' ) ) . ' >
       </form>';
?>
	<script>
	jQuery('a[href=\"#Moppm_account_exist\"]').click(function (e) {
			jQuery('.moppm_verify_userform').submit();
		});
	</script>
