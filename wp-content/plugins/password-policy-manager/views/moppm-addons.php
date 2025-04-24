<?php
/**
 * File to display premium plugin features.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_directory_url;

$setup_dir_name = $moppm_directory_url . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'account' . DIRECTORY_SEPARATOR . 'link-tracer.php';
require_once $setup_dir_name;
?>
<div id="main_class" style="display: flex;">
	<div id="main_first" style="width: 50%;">
		<h1 class="moppm_h1_ad"><b><?php esc_html_e( 'Premium Features', 'password-policy-manager' ); ?></b> <span class="moppm_advertise"> <?php echo '  <a href="' . esc_url( $upgrade_url ) . '" style="color: red; font-weight:bold;text-decoration: none !important;">'; ?>[ UPGRADE ]</a></span> </h1>
	</div>
	<div class="" style="width: 50%;margin-top: 1em;"></div>
</div>
<hr>
<div style="margin-top:12px">
	<table>
		<tbody>
			<tr>
				<td class="moppm_premium_feature_text"> <?php esc_html_e( 'Disallow Previously Used Passwords', 'password-policy-manager' ); ?> <span class="moppm_premium_instruction" id="error1"></span>
					<a href='<?php echo esc_url( $moppm_premium_doc['disallow_previously_used_passwords'] ); ?>' target="_blank" class="dashicons dashicons-text-page" title="More Information"></a>
					<a href='<?php echo esc_url( $password_policy_settings['password_policy_setting'] ); ?>' target="_blank" class="dashicons dashicons-video-alt3"></a>
				</td>
				<td class="moppm_premium_button"><label class="mo_wpns_switch">
						<input disabled type="checkbox" id="Moppm_previously_used" name="Moppm_previously_used"><span class="mo_wpns_slider mo_wpns_round mo_ppm_switch"></span>
					</label>
				</td>
			</tr>

		</tbody>
	</table>

	<p class="moppm_text"> <?php esc_html_e( 'Select the number of previous password you do not want to allow:', 'password-policy-manager' ); ?> <input disabled type="number" max=30 min=1 name="moppm_prev_password_lim" id="moppm_prev_password_lim" value="<?php echo esc_attr( get_site_option( 'moppm_prev_password_lim', 1 ) ); ?>" /> <input disabled name="moppm_prev_password_lim_save" id="moppm_prev_password_lim_save" value="<?php esc_attr_e( 'Save Settings', 'password-policy-manager' ); ?>" type="button" class="button button-primary">
	</p>

	<p style="margin-top:29px;" class="moppm_text"><?php esc_html_e( 'It will restrict users from using previously used passwords, which will make the password more secure and safe from attacks.', 'password-policy-manager' ); ?></p>
</div>
<hr>
<div style="margin-top:12px">
	<table>
		<tbody>
			<tr>
				<td class="moppm_premium_feature_text"><?php esc_html_e( 'Automatically Lock Inactive Users', 'password-policy-manager' ); ?> <span class="moppm_premium_instruction" id="error3"></span>
					<a href='<?php echo esc_url( $moppm_premium_doc['automatically_lock_inactive_users'] ); ?>' target="_blank">
						<span class="dashicons dashicons-text-page" title="More Information"></span></a>
					<a href='<?php echo esc_url( $password_policy_settings['automatically_lock_inactive_users'] ); ?>' target="_blank">
						<span class="dashicons dashicons-video-alt3" title="More Information"></span>
					</a>
				</td>
				<td class="moppm_premium_button"><label class="mo_wpns_switch">
						<input disabled type="checkbox" id="Moppm_automatically_Inactive" name="Moppm_automatically_Inactive"><span class="mo_wpns_slider mo_wpns_round mo_ppm_switch"></span>

					</label> </td>
			</tr>
		</tbody>
	</table>
	<form class="moppm_select_dropdown">
		<label for="cars" style=" font-size:15px;"><?php esc_html_e( 'Choose the time duration', 'password-policy-manager' ); ?></label>
		<input disabled type="number" max=30 min=1 name="moppm_prev_password_lim" id="moppm_prev_password_lim" value="<?php echo esc_attr( get_site_option( 'moppm_prev_password_lim', 3 ) ); ?>" />
		<select disabled id="moppm_select_days" name="cars">
			<option value="Days"><?php esc_html_e( 'Days', 'password-policy-manager' ); ?></option>
			<option value="Weeks"><?php esc_html_e( 'Weeks', 'password-policy-manager' ); ?></option>
			<option value="Month"><?php esc_html_e( 'Month', 'password-policy-manager' ); ?></option>
		</select>
		<input disabled name="moppm_inactive_user" id="moppm_inactive_user" value="<?php esc_attr_e( 'Save Settings', 'password-policy-manager' ); ?>" type="button" class="button button-primary">
	</form><br><br>
</div>
<hr>
<div>
	<table>
		<tbody>
			<tr>
				<td class="moppm_premium_feature_text"><?php esc_html_e( 'Custom Redirect Url', 'password-policy-manager' ); ?> <span class="moppm_premium_instruction" id="error5"></span>
					<a href='<?php echo esc_url( $moppm_premium_doc['custom_redirect_url'] ); ?>' target="_blank">
						<span class="dashicons dashicons-text-page" title="More Information"></span></a>
					<a href='<?php echo esc_url( $password_policy_settings['custom_redirect_url'] ); ?>' target="_blank">
						<span class="dashicons dashicons-video-alt3" title="More Information"></span>
					</a>
				</td>
				<td class="moppm_premium_button"><label class="mo_wpns_switch">
						<input disabled type="checkbox" id="Moppm_custom_redirect_url" name="Moppm_custom_redirect_url"><span class="mo_wpns_slider mo_wpns_round mo_ppm_switch"></span>

					</label> </td>
			</tr>
		</tbody>
	</table>
	<p style=" font-size: 15px;"> <?php esc_html_e( 'Set the redirect url, where user will redirect after the reset password', 'password-policy-manager' ); ?></p>
	<?php
	global $wp_roles;
	$wp_roles_1 = $wp_roles;
	if ( ! isset( $wp_roles ) ) {
		$wp_roles_1 = new WP_Roles();
	}
	echo '<div><span style="font-size:18px;">Roles<div style="float:right;">' . esc_html__( 'Custom Redirect Login Url', 'password-policy-manager' ) . '</div></span><br /><br />';
	foreach ( $wp_roles_1->role_names as $id_1 => $name ) {
		$setting  = get_site_option( 'moppm_' . $id_1 );
		$redirect = ( get_site_option( 'moppm_' . $id_1 . '_login_url', '' ) !== '' ) ? esc_html( get_site_option( 'moppm_' . $id_1 . '_login_url' ) ) : get_site_option( 'siteurl' );
		?>
		<div><input disabled type="checkbox" name="<?php echo 'moppm_' . esc_attr( $id_1 ); ?>" style="margin-left: 2%;" value="1" <?php checked( 1 === $setting ); ?> /><?php echo esc_html( $name ); ?>
			<input disabled type="text" class="moppm_table_textbox_redirect" name="<?php echo esc_attr( 'moppm_' . $id_1 ); ?>_login_url" value="<?php echo esc_attr( $redirect ); ?>" />
		</div>
		<br />
		<?php
	}
	$current_user_id = wp_get_current_user()->ID;

	if ( is_network_admin( $current_user_id ) && is_multisite() ) {
		?>
		<div><input disabled type="checkbox" name="moppm_superadmin" style="margin-left: 2%;" value="1" />Super Admin
			<input disabled type="text" class="moppm_table_textbox_redirect" name="moppm_superadmin_login_url" value="<?php echo esc_attr( get_option( 'moppm_superadmin_login_url' ) ); ?>" />
		</div> <br>
		<?php
	}
	?>

	<br><br>
	<input disabled type="submit" class="button button-primary" value="Submit">
	<?php echo '<input type="hidden" name="moppm_redirect_url" value="moppm_remove_account"/>'; ?>
</div> <br>
<hr>
<div style="margin-top:12px">
	<table>
		<tbody>
			<tr>
				<td class="moppm_premium_feature_text"> <?php esc_html_e( 'Generate Random Password', 'password-policy-manager' ); ?> <span class="moppm_premium_instruction" id="error4"></span>
					<a href='<?php echo esc_url( $moppm_premium_doc['generate_random_password'] ); ?>' target="_blank">
						<span class="dashicons dashicons-text-page" title="More Information"></span></a>
					<a href='<?php echo esc_url( $password_policy_settings['generate_random_password'] ); ?>' target="_blank">
						<span class="dashicons dashicons-video-alt3" title="More Information"></span>
					</a>
				</td>
				<td class="moppm_premium_button"><label class="mo_wpns_switch">
						<input disabled type="checkbox" id="Moppm_generate_password" name="Moppm_generate_password"><span class="mo_wpns_slider mo_wpns_round mo_ppm_switch"></span>
					</label> </td>
			</tr>
		</tbody>
	</table>
	<p style="margin-top:12px" class="moppm_text"><?php esc_html_e( 'This function will generate a random and strong password according to the set policy on password reset page window.', 'password-policy-manager' ); ?></p>
</div>
<hr>
<div>
	<table>
		<tbody>
			<tr>
				<td class="moppm_premium_feature_text"><?php esc_html_e( 'Hide Password Reset Link From WP-login ', 'password-policy-manager' ); ?><span class="moppm_premium_instruction" id="error6">
						<a href='<?php echo esc_url( $moppm_premium_doc['hide_password_reset_link_from_wp_login'] ); ?>' target="_blank">
							<span class="dashicons dashicons-text-page" title="More Information"></span></a>
						<a href='<?php echo esc_url( $password_policy_settings['hide_password_reset_link_from_wp_login'] ); ?>' target="_blank">
							<span class="dashicons dashicons-video-alt3" title="More Information"></span>
						</a>
				</td>
				<td class="moppm_premium_button"><label class="mo_wpns_switch">
						<input disabled type="checkbox" id="Moppm_reset_pass" name="Moppm_reset_pass"><span class="mo_wpns_slider mo_wpns_round mo_ppm_switch"></span>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<script type="text/javascript">
	jQuery(".mo_ppm_switch").click(function(e) {
		Moppm_error_msg("This feature is available in premium plugins.");
	});
</script>
