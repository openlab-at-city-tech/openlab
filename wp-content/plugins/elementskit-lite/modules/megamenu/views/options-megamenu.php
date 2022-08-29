<script>
	var elementskit_options_megamenu_markup = `
	<fieldset class="menu-settings-group elementskit-options-megamenu" id="elementskit-options-megamenu">
		<legend class="menu-settings-group-name attr-text-bold"><?php esc_html_e( 'ElementsKit Megamenu', 'elementskit-lite' ); ?></legend>
		<div class="menu-settings-input checkbox-input">
		<input name="is_enabled" type="checkbox" <?php checked( ( isset( $data['is_enabled'] ) ? $data['is_enabled'] : '' ), '1' ); ?> id="elementskit-menu-metabox-input-is-enabled" class="elementskit-menu-is-enabled" value="1">
			<label for="elementskit-menu-metabox-input-is-enabled"><?php esc_html_e( 'Enable this menu for Megamenu content', 'elementskit-lite' ); ?></label><br>
			<p class="notice notice-warning" style="margin-top: 10px;padding-top: 10px;padding-bottom: 10px;background:#f3f3f3;">After enabling this, you need to use ElementsKit's header-footer module with <b>elementskit nav-menu</b> widget to show the mega menu. <a href="https://help.wpmet.com/docs/mega-menu-module/" target="_blank">See here how to do it.</a></p>
		</div>
	</fieldset>
	`;

	var elementskit_megamenu_nonce = `<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>`;

</script>
