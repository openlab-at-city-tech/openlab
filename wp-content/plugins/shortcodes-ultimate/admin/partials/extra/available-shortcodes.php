<?php defined( 'ABSPATH' ) || exit; ?>

<div class="su-admin-shortcodes-extra">
	<p class="su-admin-shortcodes-extra-message"><?php esc_html_e( 'This shortcode is available with the Extra Shortcodes add-on', 'shortcodes-ultimate' ); ?></p>
	<img src="<?php echo esc_attr( $this->get_image_url( 'icon-banner.png' ) ); ?>" class="su-admin-shortcodes-extra-icon">
	<h2 class="su-admin-shortcodes-extra-title"><?php esc_html_e( 'Extra Shortcodes', 'shortcodes-ultimate' ); ?></h2>
	<p class="su-admin-shortcodes-extra-description"><?php esc_html_e( 'This add-on extends Shortcodes Ultimate with 15 new shortcodes. Parallax sections, responsive content slider, pricing tables and more', 'shortcodes-ultimate' ); ?></p>
	<p class="su-admin-shortcodes-extra-action">
		<a href="<?php echo esc_attr( su_get_utm_link( 'https://getshortcodes.com/add-ons/extra-shortcodes/', array( 'available-shortcodes', 'extra-shortcode', 'wp-dashboard' ) ) ); ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Details & Pricing', 'shortcodes-ultimate' ); ?> &rarr;</a>
	</p>
	<div class="su-admin-shortcodes-extra-screenshot">
		<img src="<?php echo esc_attr( $this->get_image_url( 'screenshots/' . $data['id'] . '.png' ) ); ?>">
	</div>
</div>
