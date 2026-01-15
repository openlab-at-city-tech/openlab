<?php declare( strict_types=1 ); ?>
<p class="tooltip description">
	<?php esc_html_e( 'A valid license key is required for support and updates', '%TEXTDOMAIN%' ); ?>
</p>
<div class="license-test-results">
	<img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading-license" alt="Loading" style="display: none"/>
	<div class="key-validity"></div>
</div>
