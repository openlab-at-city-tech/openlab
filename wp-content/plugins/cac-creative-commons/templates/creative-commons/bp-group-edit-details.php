
<div id="cac-cc-group-edit-details">
	<p>
		<label for="cac-cc-license"><?php esc_html_e( 'License', 'cac-creative-commons' ); ?></label>

		<?php esc_html_e( "Choose a suitable license for your group's created content.", 'cac-creative-commons' ); ?>
	</p>

	<?php cac_cc_license_logo(); ?>
	<p id="cac-cc-link"><?php cac_cc_license_link(); ?></p>

	<?php cac_cc_button_chooser(); ?>
</div>
