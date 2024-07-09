<style>
	.ekit-user-consent-for-banner{
		margin: 0 0 15px 0!important;
		max-width: 655px;
	}
	.ekit-success-notice {
		position: fixed;
		top: 50px;
		right: 20px;
		background-color: #f2295b;
		color: white;
		padding: 10px;
		border-radius: 5px;
		display: none;
	}
</style>
<script>
	jQuery(document).ready(function ($) {
		"use strict";
		$('#ekit-admin-switch__ekit-user-consent-for-banner').on('change', function(){
			let val = ($(this).prop("checked") ? $(this).val() : 'no');
			let data = {
				'settings' : {
					'ekit_user_consent_for_banner' : val, 
				}, 
				'nonce': "<?php echo esc_html(wp_create_nonce( 'ajax-nonce' )); ?>"
			};

			$.post( ajaxurl + '?action=ekit_admin_action', data, function( data ) {
				$('#success-notice').fadeIn().delay(1000).fadeOut(); 
			});
		});
	}); // end ready function
</script>

<div id="success-notice" class="ekit-success-notice"><?php esc_html_e( 'Success! Your action was completed', 'elementskit-lite' ); ?></div>
<div class="ekit-user-consent-for-banner notice notice-error">
	<p>
		<label for="ekit-admin-switch__ekit-user-consent-for-banner"><?php esc_html_e( 'Show update & fix related important messages, essential tutorials and promotional images on WP Dashboard', 'elementskit-lite' ); ?></label>
		<input type="checkbox" <?php echo ( $this->utils->get_settings( 'ekit_user_consent_for_banner', 'yes' ) == 'yes' ? 'checked' : '' ); ?> value="yes" class="ekit-admin-control-input" name="ekit-user-consent-for-banner" id="ekit-admin-switch__ekit-user-consent-for-banner">
	</p>
</div>
