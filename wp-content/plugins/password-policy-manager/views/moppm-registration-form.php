<?php
/**
 * File to display different login forms supported by premium plugins.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_dir;
$woocommerce_logo       = $moppm_dir . '/includes/images/woocommerce.png';
$ultimate_member_logo   = $moppm_dir . '/includes/images/ultimate_member.png';
$user_registration_logo = $moppm_dir . '/includes/images/user_registration.png';
$buddy_press_logo       = $moppm_dir . '/includes/images/BuddyPress.png';
$memberpress_logo       = $moppm_dir . '/includes/images/memberpress.png';
$userpro_logo           = $moppm_dir . '/includes/images/userpro.png';
$gravity_forms_logo     = $moppm_dir . '/includes/images/gravity-forms.png';
$bbpress_logo           = $moppm_dir . '/includes/images/bbpress.png';
$ninja_logo             = $moppm_dir . '/includes/images/ninja.png';
$elementor_logo         = $moppm_dir . '/includes/images/elementor.png';
?>
<div class="moppm_table_layout">
<h1 class="moppm_h1_ad"><b><?php esc_html_e( 'Integrations', 'password-policy-manager' ); ?></b> </h1><br>
<span class="moppm_text"><?php esc_html_e( 'We support most of the login forms in our premium plugin present on WordPress. And our plugin is tested with almost all the forms like Woocommerce, Ultimate Member, Elementor Pro, and so on ', 'password-policy-manager' ); ?><?php echo '  <a href="' . esc_url_raw( $upgrade_url ) . '" style="color:red;font-weight:bold;text-decoration: none !important;">'; ?>[ UPGRADE ]</a></span><br><br>


<div class="">
	<div>
		<table class="moppm_customloginform" style="width: 95%">
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $woocommerce_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Woocommerce</h3>
				</td>
				<td style="align-items: right;">
					<label class="moppm_switch" >
					<input disabled type="checkbox"  id="moppm_woocommerce_form" name="moppm_woocommerce_form" style="opacity:0;">    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $ultimate_member_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Ultimate Member</h3>
				</td>
				<td style="text-align: center;">
					<label class="moppm_switch" >
					<input disabled type="checkbox"  id="moppm_ultimete_member_form" name="moppm_ultimete_member_form">    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $buddy_press_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Buddypress</h3>
				</td>
				<td style="text-align: center;">
					<label class="moppm_switch" >
					<input disabled type="checkbox"  id="moppm_Buddypress_form" name="moppm_Buddypress_form">    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td >
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $bbpress_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">BB press</h3>
				</td>
				<td style="text-align: center;">
				<label class="moppm_switch" >
					<input disabled type="checkbox"  id="moppm_BBpress_form" name="moppm_BBpress_form" >    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $user_registration_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">User Registration</h3>
				</td>
				<td style="text-align: center;">
				<label class="moppm_switch" >
				<input disabled type="checkbox"  id="moppm_User_registration_form" name="moppm_User_registration_form" >    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $memberpress_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">member press</h3>
				</td>
				<td style="text-align: center;">
				<label class="moppm_switch" >
				<input disabled type="checkbox"  id="moppm_Member_Press_form" name="moppm_Member_Press_form" >    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $userpro_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">User pro</h3>
				</td>
				<td style="text-align: center;">
				<label class="moppm_switch" >
				<input disabled type="checkbox"  id="moppm_USer_pro_form" name="moppm_USer_pro_form" >    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $gravity_forms_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Gravity Forms</h3>
				</td>
				<td style="text-align: center; ">
				<label class="moppm_switch" >
				<input disabled type="checkbox"  id="moppm_Gravity_form" name="moppm_Gravity_form" >    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label> 
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $ninja_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Ninja form</h3>
				</td>
				<td style="text-align: center; ">
				<label class="moppm_switch" >
				<input disabled type="checkbox"  id="moppm_Ninja_form" name="moppm_Ninja_form">    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label>
				</td>
				<td>
				</td>
			</tr>
			<tr>
				<td>
				<?php echo '<img style="height:30px;width:30px;display: inline;"src="' . esc_url_raw( $elementor_logo ) . '">'; ?><h3 style="margin-left: 15px; font-size: large; display: inline; float: inherit; padding-right: 50px;">Elementor Pro</h3>
				</td>
				<td style="text-align: center; ">
				<label class="moppm_switch" >
				<input disabled type="checkbox"  id="moppm_Elementor_pro_form" name="moppm_Elementor_pro_form">    
				<span class="moppm_switch_slider moppm_switch_round mo_ppm_form_switch"></span>
				</label> 
				</td>
				<td>
				</td>
			</tr>
		</table>
		<div style="text-align: center"> 
		</div>
	</div>
</div>
<script>
var elements = document.querySelectorAll(".moppm_form_column");
jQuery(".mo_ppm_form_switch").click(function(e){ 
	Moppm_error_msg("This feature is available in premium plugins.");
}); 
</script>
