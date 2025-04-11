<?php
/**
 * File display the upgrade page.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $moppm_dir;
$back_button        = admin_url() . 'admin.php?page=moppm';
$moppm_allowed_html = array(
	'div'    => array( 'class' => array() ),
	'ul'     => array(),
	'li'     => array(),
	'strong' => array(),
	'b'      => array(),
);
echo '<a class="moppm_back_button" style="font-size: 16px; color: #000;" href="' . esc_url( $back_button ) . '"> <big> <big>&#8592; </big></big>' . esc_html__( 'Back To Plugin Configuration', 'password-policy-manager' ) . '</a>';
?>
<div class="moppm_upgrade_super_div" id="moppm_pass_plans">
<div class="moppm_upgrade_main_div">
	<div id="pricing_tabs_mo" class="moppm_pricing_tabs_mo moppm_pricing_tabs_mo_premium">
	<div id="pricing_head" class="moppm_pricing_head_supporter">
		<center>
		<h3 class="moppm_pricing_head_moppm">Premium </h3>
	</div>

	<br>
<div id="pricing_addons_site_based" class="moppm_pricing">
<div id="custom_my_plan_mo">
<div id="pricing_head" class="moppm_pricing_head_supporter_amount">
<div class="moppm_dollar">
			<center><span>$</span><span id="mo_ppm_pricing_adder_site_based"></span><span class="moppm_per_year"><?php esc_html_e( '/Year', 'password-policy-manager' ); ?></span></center>
			</div>
</div>
		<br><br>
		<center>
		<button class="moppm_upgrade_my_plan" onclick="moppm_upgradeform('wp_security_ppm_premium_plan','moppm_plan')"><?php esc_html_e( 'UPGRADE', 'password-policy-manager' ); ?></button>
		</center>
		</div>
		<div id="purchase_user_limit">
		<center>
			<h3> For Single Site</h3>
			<h3 class="moppm_purchase_user_limit_mo moppm_purchase_limit_mo"><?php esc_html_e( 'Choose No. of Sites', 'password-policy-manager' ); ?></h3>
			<select id="moppm_site_price" onchange="moppm_premium_update_site_limit()" onclick="moppm_premium_update_site_limit()" class="moppm_increase_my_limit">
			<option value="79"> 1 Sites</option>
			<option value="129">2 Sites </option>
			<option value="299">5 Sites</option>
			</select>
		</center>
		</div>
	</div>
	<div id="pricing_feature_collection_supporter" class="moppm_pricing_feature_collection_supporter">
	<div id="pricing_feature_collection" class="moppm_pricing_feature_collection">
		<ul class="moppm_ul">
			<p class="moppm_feature"><strong>Features</strong></p>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Role Based Password Policy Settings', 'password-policy-manager' ); ?><i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist"> Admin can set the different role and user based policies and enforce password changes.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Disallow Previously Used Passwords', 'password-policy-manager' ); ?><i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">Prevent users from re-using recently used passwords.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Role Based 1 Click Reset All Passwords', 'password-policy-manager' ); ?><i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">One-click password reset will help you to reset all user’s passwords in just a single click in case of attack or any suspicious activity.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Automatically Lock Inactive Users', 'password-policy-manager' ); ?><i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">It will lock the user automatically if the user is inactive for a specific time.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Role Based Enforce Reset Password', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_unavailable_feature"><i class="dashicons dashicons-no moppm_check"></i><?php esc_html_e( 'Logout Inactive Users', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Enforce Password Reset on First Login', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">Enforce the users to set the password according to password policy (like strong password, password expiration, one click reset password, enforce role based strong password, inactive user logout, password history management) set.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_unavailable_feature"><i class="dashicons dashicons-no moppm_check"></i><?php esc_html_e( 'Custom Redirect Url', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_unavailable_feature"><i class="dashicons dashicons-no moppm_check"></i><?php esc_html_e( 'Customize Reset Page/Form Template', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_unavailable_feature" id="addon_site_based"><i class="dashicons dashicons-no moppm_check"></i><?php esc_html_e( 'Multi-Site Support', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Generate Random Password', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist"> Generate random strong password containing all variations to make the password strong and secure from brute attacks.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Unlimited Users For Single-site', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Single-site Compatible', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Language Translation Support', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Priority Support (24/7 response time)', 'password-policy-manager' ); ?></li>
		</ul>
		</div>
	</div>
	</div>
</div>
<!-- Second one -->
<div class="moppm_upgrade_main_div">
	<div id="pricing_tabs_mo" class="moppm_pricing_tabs_mo moppm_pricing_tabs_mo_premium">
	<div id="pricing_head" class="moppm_pricing_head_supporter">
		<center>
		<h3 class="moppm_pricing_head_moppm">Enterprise</h3>
		</center>
	</div>
	<br>
	<div id="pricing_addons_site_based" class="moppm_pricing">
		<div id="custom_my_plan_mo">
		<div id="pricing_head" class="moppm_pricing_head_supporter_amount">
			<div class="moppm_dollar">
			<center><span>$</span><span id="moppm_pricing_adder_site_based"></span><span class="moppm_per_year"><?php esc_html_e( '/Year', 'password-policy-manager' ); ?></span></center>
			</div>
		</div>
		<br><br>
		<center>
		<button class="moppm_upgrade_my_plan" onclick="moppm_upgradeform('wp_security_ppm_enterprise_plan','moppm_plan')"><?php esc_html_e( 'UPGRADE', 'password-policy-manager' ); ?></button>
		</center>
		</div>
		<div id="purchase_user_limit">
		<center>
			<h3>For Multisite</h3>
			<h3 class="moppm_purchase_user_limit_mo moppm_purchase_limit_mo"><?php esc_html_e( 'Choose No. of Sites', 'password-policy-manager' ); ?></h3>
			<select id="moppm_multi_site_price" onchange="moppm_update_site_limit()" onclick="moppm_update_site_limit()" class="moppm_increase_my_limit">
			<option value="159"> 1 Site</option>
			<option value="279">2 Sites</option>
			<option value="499">5 Sites</option>
			</select>
		</center>
		</div>
	</div>
	<div id="pricing_feature_collection_supporter" class="moppm_pricing_feature_collection_supporter">
	<div id="pricing_feature_collection" class="moppm_pricing_feature_collection">
		<ul class="moppm_ul">
			<p class="moppm_feature"><strong>Features</strong></p>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Role Based Password Policy Settings', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist"> Admin can set the different role and user based policies and enforce password changes.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Disallow Previously Used Passwords', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">Prevent users from re-using recently used passwords.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Role Based 1 Click Reset All Passwords', 'password-policy-manager' ); ?><i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">One-click password reset will help you to reset all user’s passwords in just a single click in case of attack or any suspicious activity.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Automatically Lock Inactive Users', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">It will lock the user automatically if the user is inactive for a specific time.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Role Based Enforce Reset Password', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Logout Inactive Users', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Enforce Password Reset on First Login', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist">Enforce the users to set the password according to password policy (like strong password, password expiration, one click reset password, enforce role based strong password, inactive user logout, password history management) set.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Custom Redirect Url', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Customize Reset Page/Form Template', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature" id="addon_site_based"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Multi-Site Support', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><span class="moppm_15_tooltip_methodlist"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Generate Random Password', 'password-policy-manager' ); ?> <i class="dashicons dashicons-info moppm_info" aria-hidden="true"></i><span class="moppm_methodlist"> Generate random strong password containing all variations to make the password strong and secure from brute attacks.</a></span></span></i></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Unlimited Users For Single-site', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Single-site Compatible', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Language Translation Support', 'password-policy-manager' ); ?></li>
			<li class="moppm_feature_collect moppm_available_feature"><i class="dashicons dashicons-saved moppm_check"></i><?php esc_html_e( 'Priority Support (24/7 response time)', 'password-policy-manager' ); ?></li>
		</ul>
		</div>
</div>
	</div>
</div>
</div>
<div class="moppm_setting_layout moppm_setting_layout1">
<div>
	<h2><?php echo esc_html__( 'Steps to upgrade to the Premium Plan :', 'password-policy-manager' ); ?></h2>
	<ol class="moppm_licensing_plans_ol">
	<li><?php echo wp_kses( __( 'Click on <b>BUY</b> button of your preferred plan above.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'You will be redirected to the payment page.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>

	<li><?php echo wp_kses( __( 'Select the number of users/sites you wish to upgrade for and any add-ons if you wish to purchase and click on <b>Proceed to Payment</b>.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'You will be redirected to the miniOrange Console. Enter your miniOrange username and password, after which you will be redirected to the <b>Payment Details</b> page.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'Click on <b>Proceed to Payment</b> and make the payment.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'After making the payment, you can find the respective <b>plugins</b> to download from the <b>Downloads</b> tab in the left navigation bar of the miniOrange Console.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'Download the paid plugin from the <b>Releases and Downloads</b>.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'Deactivate and delete the free plugin from <b>WordPress dashboard</b> and install the paid plugin downloaded.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'Login to the paid plugin with the miniOrange account you used to make the payment, after this your users will be able to set up the password policy.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	</ol>
</div>
<hr>
<div>
	<h2><?php esc_html_e( 'Note :', 'password-policy-manager' ); ?></h2>
	<ol class="moppm_licensing_plans_ol">
	<li><?php echo wp_kses( __( 'The plugin works with many of the default custom login forms (like Woocommerce/Theme My Login/Login With Ajax/User Pro/Elementor), however if you face any issues with your custom login form, contact us and we will help you with it.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	<li><?php echo wp_kses( __( 'The <b>license key </b>is required to activate the premium versions of the plugin. You will have to login with the miniOrange Account you used to make the purchase then enter license key to activate plugin.', 'password-policy-manager' ), $moppm_allowed_html ); ?></li>
	</ol>
</div>
<hr><br>
<div>
	<?php
	echo '<b class="moppm_note">' . esc_html__( 'Refund Policy :', 'password-policy-manager' ) . '</b> <p style = "font-size:14px;">';
		printf(
			esc_html(
			/* Translators: %s: bold tags and links*/
				__( '%1$1sClick here%2$2s to read our refund policy.', 'password-policy-manager' )
			),
			'<a href="https://plugins.miniorange.com/end-user-license-agreement/#v5-software-warranty-refund-policy" target="blank">',
			'</a>'
		);
		echo ' </p>';
		?>
</div><br>
<hr><br>
<div>
	<?php echo '<b class="moppm_note">' . esc_html__( 'Contact Us :', 'password-policy-manager' ) . ' </b> <p style = "font-size:14px;">' . esc_html__( 'If you have any doubts regarding the licensing plans, you can mail us at', 'password-policy-manager' ) . ' <a    href="mailto:info@xecurify.com"><i>info@xecurify.com</i></a>' . esc_html__( ' or submit a query using the support form.', 'password-policy-manager' ) . ' </p>'; ?>
</div>
<br><br>
</div>
<script type="text/javascript">
var base_price_site_based = 0;
var display_my_site_based_price = parseInt(base_price_site_based) + parseInt(0) + parseInt(0) + parseInt(0);
document.getElementById("mo_ppm_pricing_adder_site_based").innerHTML = +display_my_site_based_price;
jQuery('#moppm_site_price').click();

function moppm_premium_update_site_limit() {
	var users = document.getElementById("moppm_site_price").value;
	var users_addion = parseInt(base_price_site_based) + parseInt(users);
	document.getElementById("mo_ppm_pricing_adder_site_based").innerHTML = +users_addion;

}
var moppm_base_price_site_based = 0;
var moppm_display_my_site_based_price = parseInt(moppm_base_price_site_based) + parseInt(0) + parseInt(0) + parseInt(0);
document.getElementById("moppm_pricing_adder_site_based").innerHTML = +moppm_display_my_site_based_price;
jQuery('#moppm_multi_site_price').click();

function moppm_update_site_limit() {
	var users = document.getElementById("moppm_multi_site_price").value;
	var users_addion = parseInt(moppm_base_price_site_based) + parseInt(users);
	document.getElementById("moppm_pricing_adder_site_based").innerHTML = +users_addion;

}

function moppm_upgradeform(planType, planname) {
	var nonce = '<?php echo esc_js( wp_create_nonce( 'moppm_update_plan' ) ); ?>';
	const url = `https://portal.miniorange.com/initializepayment?requestOrigin=${planType}`;
	var data = {
	'action': 'moppm_ajax',
	'option': 'moppm_update_plan',
	'planname': planname,
	'nonce': nonce,
	'plantype': planType,
	}
	jQuery.post(ajaxurl, data, function(response) {});
	window.open(url, "_blank");
}
</script>
