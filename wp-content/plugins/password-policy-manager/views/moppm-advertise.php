<?php
/**
 *  File to display other miniorange plugins.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$moppm_allowed_html = array(
	'a'     => array(
		'href'  => array(),
		'class' => array(),
		'title' => array(),
	),
	'input' => array(
		'type'  => array(),
		'class' => array(),
		'value' => array(),
	),
);
?>
	<div class="moppm-2fa-ad">
		<div class="moppm-2fa-logo"></div>
		<div class="moppm-2fa-info">
			<div class="moppm-trynow-btn">
			<?php
			$plugin_name  = 'miniorange-2-factor-authentication';
			$install_link = '<a href="' . esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . esc_html( $plugin_name ) . '&TB_iframe=true&width=800&height=600' ) ) . '" class="thickbox" title="More info about miniOrange\'s Two-Factor Authentication Plugin"><input type="button" class="button button-primary" value="Try Now"/></a>';
			echo wp_kses( $install_link, $moppm_allowed_html );
			?>
		</div>
			<h1 class="moppm_h1_ad"><a href="https://wordpress.org/plugins/miniorange-2-factor-authentication/" target="_blank"><?php esc_html_e( 'miniOrange 2-factor Authentication (2FA with SMS, Email, Google Authenticator)', 'password-policy-manager' ); ?></a></h1>
			<div class="wporg-ratings" aria-label="4.5 out of 5 stars" data-title-template="%s out of 5 stars" data-rating="4.5" style="color:#ffb900;"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-half"></span>
			<span> <span><span><a href="https://wordpress.org/support/plugin/miniorange-2-factor-authentication/reviews" target="_blank">(350+ reviews)</a><span></div>
			<p> <?php esc_html_e( 'Ensures security of user applications and environments, so that right set of eyes have access to your sensitive information sitting on the cloud or on-premise. Provides multiple 2FA methods like Google Authenticator, Microsoft Authenticator, OTP over Email/SMS and more.', 'password-policy-manager' ); ?></b></p>
		</div>
	</div>
